<?php
// DBへの接続情報
define('DB_HOST', 'localhost');
define('DB_USER', 'cc_user');
define('DB_PASS', 'password');
define('DB_NAME', 'career_consultant');

/**
 * データベース接続開始
 * @return PDO データベース操作用のオブジェクト
 */
function db_connect()
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // エラーモードを例外に設定
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // フェッチモードを連想配列形式に設定
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
}

//データベース接続終了

// デバックチェック関数
function check($str)
{
    echo "<pre>";
    var_dump($str);
    echo "</pre>";
}

// XSS対策
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 「2026-01-01」形式日付を「〇年〇月〇日」表記に変換
function format_japanese_date($date)
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return '';
    }

    return date('Y年n月j日', $timestamp);
}

/**
 * 生徒ログイン処理
 * 
 * ログイン成功時にセッションにIDと生徒名を設定する
 * @param string $login_id ログインID
 * @param string $password ログインパスワード
 * @return bool セッションにメッセージを設定して、ログイン成功時はtrue、失敗時はfalseを返す
 */
function student_login($login_id, $password)
{
    $db = db_connect();
    $sql = 'SELECT CONCAT(first_name,last_name) AS student_name, id FROM m_students WHERE login_id=:login_id ';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':login_id', $login_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if (password_verify($password, $result['password'])) {
            $_SESSION['id'] = $result['id'];
            $_SESSION['student_name'] = $result['student_name'];
            $_SESSION['res_message'] = ['status_code' => 1, 'msg' => 'ログイン成功'];
            return true;
        }
        $_SESSION['res_message'] = ['status_code' => 0, 'msg' => 'パスワードが間違っています'];
        return false;
    }
    $_SESSION['res_message'] = ['status_code' => 0, 'msg' => 'ログインIDが間違っています'];
    return false;
}

/**
 * 生徒一覧の取得
 * 
 * 生徒の構造
 * 
 * [
 * 'student_id',
 * 'student_name',
 * 'number',
 * 'status_name',
 * 'course_name',
 * 'room_name',
 * ]
 * 
 * @param 連想配列 $filters 連想配列で絞り込みたい項目を設定可能。
 * @param bool $is_display_end 訓練期間が終了している生徒を表示するか。デフォルトは表示しない(false)
 * @return 二次元配列 生徒一覧
 */
function get_students($filters = [], $is_display_end = false)
{
    $db = db_connect();

    // ベースとなるSQL
    $sql = 'SELECT
            s.id AS student_id, 
            CONCAT(s.last_name, s.first_name) AS student_name,
            s.number,
            ss.name AS status_name,
            c.name AS course_name,
            c.end_date,
            r.name AS room_name
            FROM m_students s
            JOIN m_student_status ss ON s.status_id = ss.id
            JOIN m_courses c ON s.course_id = c.id
            JOIN m_rooms r ON c.room_id = r.id
            JOIN m_courses_categories cc ON c.category_id = cc.id';

    // 1. 検索対象のカラム定義（フィルタのキー => SQL上のカラム名）
    $filter_definition = [
        'course_id' => 's.course_id',
        'status_id' => 's.status_id',
        'number'    => 's.number',
    ];

    $where_clauses = [];
    $params = [];

    // 2. ループで一括処理
    foreach ($filter_definition as $key => $column) {
        // フィルタに値が存在し、かつ空文字でない場合のみ追加
        if (isset($filters[$key]) && $filters[$key] !== '') {
            $where_clauses[] = "{$column} = :{$key}";
            $params[":{$key}"] = $filters[$key];
        }
    }

    // 訓練修了済みの生徒を取得しない
    $now_date = date('Y-m-d');
    if (!$is_display_end) {
        $where_clauses[] = 'c.end_date >= :now_date';
        $params[':now_date'] = $now_date;
    }

    // 3. WHERE句の組み立て
    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }
    $sql .= ' ORDER BY s.number ASC, r.id ASC';
    $stmt = $db->prepare($sql);

    // 4. まとめてバインドして実行
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 生徒詳細の取得
 * 
 * 生徒IDから生徒を取得する
 * 生徒の構造
 * [
 * 'student_id',
 * 'student_name',
 * 'number',
 * 'status_name',
 * 'course_name',
 * 'room_name',
 * 'bookings' => [
 *  'cc_slot_id',
 *  'is_cc_plus',
 *  'cc_consultant',
 *  'cc_room',
 *  'cc_date',
 *  'cc_time',
 *  'cc_style',
 *  ],
 * ]
 * @return 連想配列 生徒配列
 */
function get_student($student_id)
{
    $db = db_connect();
    $sql = 'SELECT
            s.id AS student_id,
            CONCAT(s.last_name, s.first_name) AS student_name,
            s.number,
            ss.id AS status_id,
            ss.name AS status_name,
            c.id AS course_id,
            c.name AS course_name,
            r.name AS room_name,
            b.cc_slot_id AS cc_slot_id,
            sl.is_cc_plus AS is_cc_plus,
            CONCAT(con.last_name,con.first_name) AS cc_consultant,
            br.name AS cc_room,
            sl.date AS cc_date,
            t.start_time AS cc_time,
            t.display_name AS cc_display_time,
            ms.name AS cc_style
            FROM m_students s
            JOIN m_student_status ss ON s.status_id = ss.id
            JOIN m_courses c ON s.course_id = c.id
            JOIN m_rooms r ON c.room_id = r.id
            LEFT JOIN t_cc_bookings b ON s.id = b.student_id
            LEFT JOIN t_cc_slots sl ON b.cc_slot_id = sl.id
            LEFT JOIN m_times t ON b.time_id = t.id
            LEFT JOIN m_consultants con ON sl.consultant_id = con.id
            LEFT JOIN m_rooms br ON sl.room_id = br.id
            LEFT JOIN m_meating_styles ms ON b.style_id = ms.id
            WHERE s.id = :student_id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $student = [];
    foreach ($result as $row) {
        if (empty($student)) {
            $student = [
                'student_id'   => $row['student_id'],
                'student_name' => $row['student_name'],
                'number'       => $row['number'],
                'status_id'    => $row['status_id'],
                'status_name'  => $row['status_name'],
                'course_id'    => $row['course_id'], 
                'course_name'  => $row['course_name'],
                'room_name'    => $row['room_name'],
                'bookings'     => []
            ];
        }
        // 予約レコードがある場合のみ追加
        if ($row['cc_slot_id']) {
            $student['bookings'][] = [
                'cc_slot_id'    => $row['cc_slot_id'],
                'is_cc_plus'    => $row['is_cc_plus'],
                'cc_consultant' => $row['cc_consultant'],
                'cc_room'       => $row['cc_room'],
                'cc_date'       => $row['cc_date'],
                'cc_time'       => $row['cc_time'],
                'cc_style'      => $row['cc_style'],
            ];
        }
    }

    return $student;
}

/**
 * 生徒の追加
 * 
 * $studentsに複数生徒を渡すことで一括登録可能
 * 
 * 生徒一人の構造
 * [
 *  'first_name',
 *  'last_name',
 *  'number',
 * ]
 */
function add_students($course_id, $students)
{
    $db = db_connect();

    // コース情報からlogin_id用のプレフィックスを取得
    $sql = 'SELECT c.start_date, r.name as room_name 
            FROM m_courses c
            JOIN m_rooms r ON c.room_id = r.id
            WHERE c.id = :course_id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    $date = new DateTime($course['start_date']);
    $login_id_prefix = $date->format('Ym') . '_' . $course['room_name'];

    // カラム定義
    $student_definition = [
        'first_name',
        'last_name',
        'number',
        'login_id',
        'password',
        'status_id',
        'course_id',
    ];

    $sql = 'INSERT INTO m_students (' . implode(', ', $student_definition) . ') VALUES ';

    $values_queries = [];
    $params = [];
    $password_hash = password_hash('password', PASSWORD_DEFAULT);

    // 2. ループで生徒ごとのプレースホルダとパラメータを生成
    foreach ($students as $i => $student) {
        $row_placeholders = [];

        // login_idを生成 (例: 202604_6A01)
        // numberが1桁の場合に備え str_pad で2桁に揃える
        $formatted_number = str_pad($student['number'], 2, '0', STR_PAD_LEFT);
        $login_id = $login_id_prefix . $formatted_number;

        // 各カラムの値をセット
        $row_values = [
            'first_name' => $student['first_name'],
            'last_name'  => $student['last_name'],
            'number'     => $student['number'],
            'login_id'   => $login_id,
            'password'   => $password_hash,
            'status_id'  => 1, // 在校中
            'course_id'  => $course_id,
        ];

        foreach ($student_definition as $column) {
            $placeholder = ":" . $column . "_" . $i; // 例: :first_name_0
            $row_placeholders[] = $placeholder;
            $params[$placeholder] = $row_values[$column];
        }

        $values_queries[] = '(' . implode(', ', $row_placeholders) . ')';
    }

    // 3. 結合して実行
    if (!empty($values_queries)) {
        $sql .= implode(', ', $values_queries);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
}

/**
 * コース一覧を取得
 * @param string $target_date 実施状況を確認したい基準日
 * @param int $room_id 表示したい教室のID
 * @param int $category_id 表示したいカテゴリーのID
 * @param bool $is_display_not_start 基準日より後に開始するコースを表示するか。デフォルトは表示しない(false)
 * @return 連想配列 開催中のコース一覧
 */
function get_courses($target_date = null, $is_display_not_start = false, $room_id = null, $category_id = null)
{
    $db = db_connect();

    // 1. デフォルト値の設定（target_dateが空なら今日の日付を入れる）
    if ($target_date === null) {
        $target_date = date('Y-m-d');
    }

    // 2. 基本となるSQL
    $sql = 'SELECT
            c.id AS course_id,
            c.name AS course_name,
            c.start_date,
            c.end_date,
            r.name AS room_name,
            cc.name AS category_name
            FROM m_courses c
            JOIN m_rooms r ON c.room_id = r.id
            JOIN m_courses_categories cc ON c.category_id = cc.id';

    // 3. WHERE句の動的組み立て
    $where_clauses = [];
    $params = [];

    // 日付条件：指定日が開始日と終了日の間にあるか

    $where_clauses[] = $is_display_not_start ?
        ':target_date <= c.end_date' :
        ':target_date BETWEEN c.start_date AND c.end_date';
    $params[':target_date'] = $target_date;

    // 教室ID条件：指定がある場合のみ追加
    if ($room_id !== null) {
        $where_clauses[] = 'c.room_id = :room_id';
        $params[':room_id'] = $room_id;
    }

    if ($category_id !== null) {
        $where_clauses[] = 'c.category_id = :category_id';
        $params[':category_id'] = $category_id;
    }

    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }

    $sql .= ' ORDER BY c.start_date ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * コース詳細の取得
 * @param int $course_id 取得したいコースのID
 * @return 連想配列 コースの情報配列。
 * 必須キャリコンがある場合は array['cc'][1(第何回目)]['2026-01-01','2026-01-08'(開催する日付)]
 */
function get_course($course_id)
{
    $db = db_connect();
    $sql = 'SELECT 
            c.id AS course_id, 
            c.name AS course_name, 
            c.start_date, 
            c.end_date, 
            c.room_id, 
            r.name AS room_name, 
            c.category_id, 
            cc.name AS category_name, 
            s.cc_count, 
            s.date AS cc_date 
            FROM m_courses c 
            JOIN m_rooms r ON c.room_id = r.id 
            JOIN m_courses_categories cc ON c.category_id = cc.id 
            LEFT JOIN t_course_cc_schedules s ON c.id = s.course_id 
            WHERE c.id = :course_id 
            ORDER BY s.cc_count ASC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $course_detail = [];

    foreach ($result as $row) {
        // 1. コースの基本情報をセット（最初の1回だけ実行）
        if (empty($course_detail)) {
            $course_detail = [
                'course_id'     => $row['course_id'],
                'course_name'   => $row['course_name'],
                'start_date'    => $row['start_date'],
                'end_date'      => $row['end_date'],
                'room_id'       => $row['room_id'],
                'room_name'     => $row['room_name'],
                'category_id'   => $row['category_id'],
                'category_name' => $row['category_name'],
                'cc'            => [] // ここに cc_count をキーとした配列を格納
            ];
        }

        // 2. スケジュール（cc_date）を cc_count をキーにして格納
        if ($row['cc_date'] !== null) {
            $count_val = $row['cc_count']; // 「1」や「2」など第何回目かの値

            // 指定された構造に合わせて、cc_count をキーとした多次元配列を作成
            $course_detail['cc'][$count_val][] = $row['cc_date'];
        }
    }
    return $course_detail;
}

/**
 * コースに情報を追加
 * 
 * @param パラメーターに渡す配列
 * [
 *   'name' => 'webプログラミング',
 *   'start_date' => '2026-01-01',
 *   'end_date' => '2026-01-30',
 *   'room_id' => 1,
 *   'category_id' = 1,
 *   'cc' => [
 *      1 => [
 *          '2026-04-15',
 *          '2026-04-22',
 *      ],
 *      2 => [
 *          '2026-05-14',
 *          '2026-05-21',
 *      ],
 *   ]
 * ]
 */
function add_course($course)
{
    $db = db_connect();

    // カラム定義
    $course_definition = [
        'name',
        'start_date',
        'end_date',
        'room_id',
        'category_id',
    ];

    $sql = 'INSERT INTO m_courses (' . implode(', ', $course_definition) . ') VALUES ';

    $params = [];

    // 各カラムの値をセット
    $row_values = [
        'name' => $course['name'],
        'start_date'  => $course['start_date'],
        'end_date'     => $course['end_date'],
        'room_id'   => $course['room_id'],
        'category_id'   => $course['category_id'],
    ];

    foreach ($course_definition as $column) {
        $placeholder = ":" . $column;
        $row_placeholders[] = $placeholder;
        $params[$placeholder] = $row_values[$column];
    }

    $sql .= '(' . implode(', ', $row_placeholders) . ')';

    // 3. 結合して実行
    if (!empty($row_values)) {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $last_id = $db->lastInsertId();
    }

    //必須キャリコンのスケジュール登録
    if (isset($course['cc']) && $course['cc'] != '') {
        add_course_cc_schadules($last_id, $course['cc']);
    }
}

/**
 * 訓練コース毎の必須キャリコンスケジュールの取得
 * @param int $course_id 訓練コースのID
 * @return 連想配列 array["第何回目(int)"]["実際の日付(string)"]
 */
function get_course_cc_schadules($course_id)
{
    $db = db_connect();
    $sql = 'SELECT 
            cc_count, 
            date AS cc_date 
            FROM t_course_cc_schedules s
            WHERE s.course_id = :course_id 
            ORDER BY s.cc_count ASC';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        // スケジュール（cc_date）を cc_count をキーにして格納
        if ($row['cc_date'] !== null) {
            $count_val = $row['cc_count']; // 「1」や「2」など第何回目かの値

            // 指定された構造に合わせて、cc_count をキーとした多次元配列を作成
            $course_detail[$count_val][] = $row['cc_date'];
        }
    }
    return $course_detail;
}

/**
 * 必須キャリコンスケジュールの登録
 *
 * $cc_schadules の構造例:
 * [
 *   1 => ['2026-04-15', '2026-04-22'],
 *   2 => ['2026-05-14', '2026-05-21'],
 * ]
 * キーが cc_count（第何回目か）、値が実施日付の配列
 */
function add_course_cc_schadules($course_id, $cc_schadules)
{
    $db = db_connect();

    // カラム定義
    $definition = [
        'course_id',
        'cc_count',
        'date',
    ];

    $sql = 'INSERT INTO t_course_cc_schedules (' . implode(', ', $definition) . ') VALUES ';

    $values_queries = [];
    $params = [];
    $i = 0;

    // cc_count（回数）ごとにループ
    foreach ($cc_schadules as $cc_count => $dates) {
        // 同じ回数に複数の日付がある場合もループ
        foreach ($dates as $date) {
            $row_placeholders = [];

            $row_values = [
                'course_id' => $course_id,
                'cc_count'  => $cc_count,
                'date'      => $date,
            ];

            foreach ($definition as $column) {
                $placeholder = ':' . $column . '_' . $i; // 例: :course_id_0
                $row_placeholders[] = $placeholder;
                $params[$placeholder] = $row_values[$column];
            }
            $i++;

            $values_queries[] = '(' . implode(', ', $row_placeholders) . ')';
        }
    }

    // 登録対象がある場合のみ実行
    if (!empty($values_queries)) {
        $sql .= implode(', ', $values_queries);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
}

/**
 * キャリコンの種類
 * 
 * 実際の予約枠かキャリコンプラス用の枠かの種類
 */
enum CC_SLOT_TYPE: string
{
    /** 全て */
    case All = 'all';
    /** 予約枠 */
    case Line = 'line';
    /** キャリコンプラス枠 */
    case CcPlus = 'cc_plus';
}

/**
 * キャリコン枠を取得
 * @param CC_SLOT_TYPE $cc_type 取得するキャリコンの種類。デフォルトは予約枠のみ取得
 * @param string $target_date 取得したい開催日デフォルトはすべて
 */
function get_cc_slots($cc_type = CC_SLOT_TYPE::Line->name, $target_date = null)
{
    $db = db_connect();

    $sql = 'SELECT 
            s.date AS cc_date, 
            s.is_cc_plus, 
            CONCAT(c.last_name, c.first_name) AS consultant_name,
            r.name AS room_name
            FROM t_cc_slots s
            LEFT JOIN m_consultants c ON s.consultant_id = c.id
            LEFT JOIN m_rooms r ON s.room_id = r.id';

    $where_clauses = [];
    $params = [];

    // 取得したい枠の条件
    if ($cc_type != CC_SLOT_TYPE::All->name) {
        $where_slot_type = [
            CC_SLOT_TYPE::Line->name => 's.is_cc_plus = false',
            CC_SLOT_TYPE::CcPlus->name => 's.is_cc_plus = true',
        ];
        $where_clauses[] = $where_slot_type[$cc_type];
    }

    // 対象の日付のキャリコン枠を取得
    if ($target_date !== null) { 
        $where_clauses[] = 's.date = :target_date';
        $params[':taget_date'] = $target_date;
    }

    // Where句の組み立て
    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }

    $sql .= ' ORDER BY s.date ASC';
    $stmt = $db->prepare($sql);

    // まとめてバインドして実行
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * キャリコン枠を登録
 */
function add_cc_slot($date, $is_cc_plus = false){
    $db = db_connect();
    $sql = 'INSERT INTO t_cc_slots (date, is_cc_plus) VALUES (:date, :is_cc_plus)';
    $stmt = $db->prepare($sql);
    $stmt->execute([':date' => $date, 'is_cc_plus' => $is_cc_plus]);
}