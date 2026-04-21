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
 *  'booking_id'
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
            b.id AS booking_id,
            b.cc_slot_id AS cc_slot_id,
            sl.is_cc_plus AS is_cc_plus,
            CONCAT(con.last_name,con.first_name) AS cc_consultant,
            br.name AS cc_room,
            sl.date AS cc_date,
            t.start_time AS cc_time,
            t.display_name AS cc_display_time,
            ms.id AS cc_style_id,
            ms.name AS cc_style_name
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
            WHERE s.id = :student_id
            AND (
                sl.is_cc_plus = 1
                OR b.cc_plus_booking_id IS NULL  -- 予約なし(b全体がNULL)の場合もここでtrueになる
            )';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
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
                'bookings'     => [],
            ];
        }
        if ($row['booking_id']) {
            $student['bookings'][] = [
                'booking_id'    => $row['booking_id'],
                'cc_slot_id'    => $row['cc_slot_id'],
                'is_cc_plus'    => (bool) $row['is_cc_plus'],
                'cc_consultant' => $row['cc_consultant'],
                'cc_room'       => $row['cc_room'],
                'cc_date'       => $row['cc_date'],
                'cc_time'       => $row['cc_time'],
                'cc_display_time' => $row['cc_display_time'],
                'cc_style_id'   => $row['cc_style_id'],
                'cc_style_name' => $row['cc_style_name'],
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
 * 生徒情報の更新
 *
 * 渡されたキーのみ動的にUPDATEする。
 * 更新可能なカラム: first_name, last_name, number, status_id, course_id
 *
 * 使用例:
 * update_student(1, ['status_id' => 2]);
 * update_student(1, ['first_name' => '太郎', 'last_name' => '山田', 'course_id' => 3]);
 *
 * ※ステータス変更時に予約の自動削除は行わない。
 *   予約表示側で status_id を参照してアラート等を表示すること。
 *
 * @param  int   $student_id 更新対象の生徒ID
 * @param  array $data       更新するカラムと値の連想配列
 * @return bool  成功時 true、失敗時（対象なし・不正カラム含む）false
 */
function update_student(int $student_id, array $data): bool
{
    // 更新を許可するカラムのホワイトリスト
    $allowed_columns = ['first_name', 'last_name', 'number', 'status_id', 'course_id'];

    $set_clauses = [];
    $params      = [':student_id' => $student_id];

    foreach ($data as $column => $value) {
        // ホワイトリスト外のキーは無視
        if (!in_array($column, $allowed_columns, true)) {
            continue;
        }
        $set_clauses[]          = "{$column} = :{$column}";
        $params[":{$column}"]   = $value;
    }

    // 更新対象カラムが1つもなければ何もしない
    if (empty($set_clauses)) {
        return false;
    }

    $db  = db_connect();
    $sql = 'UPDATE m_students SET ' . implode(', ', $set_clauses) . ' WHERE id = :student_id';

    $stmt         = $db->prepare($sql);
    $stmt->execute($params);

    // 実際に1件以上更新されたかで成否を返す
    return $stmt->rowCount() > 0;
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
 * キャリコンプラスの開催日一覧を取得
 * 
 * キャリコンプラス枠の開催日を重複なしで取得する
 * 
 * @param string|null $base_date 基準日（この日より後の開催日を取得）。デフォルトは今日の日付
 * @return array 開催日の配列 例: [['cc_date' => '2026-05-01'], ...]
 */
function get_cc_plus_dates(?string $base_date = null): array
{
    $base_date ??= date('Y-m-d');

    $db = db_connect();

    $sql = 'SELECT DISTINCT s.date AS cc_date
            FROM t_cc_slots s
            WHERE s.is_cc_plus = true
              AND s.date > :base_date
            ORDER BY s.date ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute([':base_date' => $base_date]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
 *   'category_id' => 1,
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
        add_course_cc_schedules($last_id, $course['cc']);
    }
}

/**
 * コース情報の更新
 *
 * 渡されたキーのみ動的にUPDATEする。
 * 更新可能なカラム: name, start_date, end_date, room_id, category_id
 *
 * 'cc' キーが含まれる場合は、削除された日付に対応するこのコースの生徒の予約を削除したうえで、
 * 既存の t_course_cc_schedules を全削除してから新しいスケジュールを再登録する。
 * 'cc' キーがない場合は t_course_cc_schedules・t_cc_bookings には触れない。
 *
 * 使用例:
 * // コース名だけ変更
 * update_course(1, ['name' => '新コース名']);
 *
 * // スケジュールも再設定（削除された日付の予約は自動削除）
 * update_course(1, [
 *     'name' => '新コース名',
 *     'cc'   => [
 *         1 => ['2026-05-10', '2026-05-17'],
 *         2 => ['2026-06-14'],
 *     ],
 * ]);
 *
 * @param  int   $course_id 更新対象のコースID
 * @param  array $data      更新するカラムと値の連想配列
 * @return bool  成功時 true、失敗時 false
 */
function update_course(int $course_id, array $data): bool
{
    $allowed_columns = ['name', 'start_date', 'end_date', 'room_id', 'category_id'];

    $set_clauses = [];
    $params      = [':course_id' => $course_id];

    foreach ($data as $column => $value) {
        if (!in_array($column, $allowed_columns, true)) {
            continue;
        }
        $set_clauses[]        = "{$column} = :{$column}";
        $params[":{$column}"] = $value;
    }

    $has_cc_update     = isset($data['cc']) && is_array($data['cc']);
    $has_column_update = !empty($set_clauses);

    if (!$has_column_update && !$has_cc_update) {
        return false;
    }

    $db = db_connect();

    try {
        $db->beginTransaction();

        // m_courses のカラム更新（対象がある場合のみ）
        if ($has_column_update) {
            $sql  = 'UPDATE m_courses SET ' . implode(', ', $set_clauses) . ' WHERE id = :course_id';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
        }

        // t_course_cc_schedules の再登録（'cc' キーがある場合のみ）
        if ($has_cc_update) {
            // 旧スケジュールの日付を取得
            $old_stmt = $db->prepare(
                'SELECT DISTINCT date FROM t_course_cc_schedules WHERE course_id = :course_id'
            );
            $old_stmt->execute([':course_id' => $course_id]);
            $old_dates = $old_stmt->fetchAll(PDO::FETCH_COLUMN);

            // 新スケジュールの日付をフラットな配列に
            $new_dates = [];
            foreach ($data['cc'] as $dates) {
                foreach ($dates as $date) {
                    $new_dates[] = $date;
                }
            }

            // 削除された日付（旧日付 - 新日付）
            $removed_dates = array_values(array_diff($old_dates, $new_dates));

            // 削除された日付に対応するこのコースの生徒の予約を削除
            if (!empty($removed_dates)) {
                $placeholders = implode(', ', array_fill(0, count($removed_dates), '?'));
                $del_stmt = $db->prepare(
                    "DELETE b FROM t_cc_bookings b
                     JOIN t_cc_slots sl ON b.cc_slot_id = sl.id
                     WHERE b.student_id IN (
                         SELECT id FROM m_students WHERE course_id = ?
                     )
                     AND sl.date IN ({$placeholders})
                     AND sl.is_cc_plus = 0
                     AND b.cc_plus_booking_id IS NULL"
                );
                $del_stmt->execute(array_merge([$course_id], $removed_dates));
            }

            // 既存スケジュールを全削除して再登録
            $db->prepare('DELETE FROM t_course_cc_schedules WHERE course_id = :course_id')
               ->execute([':course_id' => $course_id]);

            if (!empty($data['cc'])) {
                add_course_cc_schedules($course_id, $data['cc']);
            }
        }

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * 訓練コース毎の必須キャリコンスケジュールの取得
 * @param int $course_id 訓練コースのID
 * @return 連想配列 array["第何回目(int)"]["実際の日付(string)"]
 */
function get_course_cc_schedules($course_id)
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
 * $cc_schedules の構造例:
 * [
 *   1 => ['2026-04-15', '2026-04-22'],
 *   2 => ['2026-05-14', '2026-05-21'],
 * ]
 * キーが cc_count（第何回目か）、値が実施日付の配列
 */
function add_course_cc_schedules($course_id, $cc_schedules)
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
    foreach ($cc_schedules as $cc_count => $dates) {
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
    /** 登録枠 */
    case Line = 'line';
    /** キャリコンプラス枠 */
    case CcPlus = 'cc_plus';
}

/**
 * キャリコン枠を取得
 * 
 * キャリコン枠情報を配列として取得する
 * [
 *  'cc_date', 
 *  'is_cc_plus',
 *  'counsultant_name',
 *  'room_name',
 * ]
 * 
 * コンサルタント名と部屋名はnullの可能性があるので使用する際はnullチェックを行う
 * @param CC_SLOT_TYPE $cc_type 取得するキャリコンの種類。デフォルトは登録枠のみ取得
 * @param string $target_date 取得したい開催日デフォルトはすべて
 * @return array キャリコン枠情報配列
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
    if ($cc_type !== CC_SLOT_TYPE::All->name) {
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
 * @param string $date キャリコンを開催する日付
 * @param bool $is_cc_plus キャリコンプラスかどうか。デフォルトは登録枠(false)
 * @return int 採番されたスロットID
 */
function add_cc_slot($date, $is_cc_plus = false): int
{
    $db = db_connect();
    $sql = 'INSERT INTO t_cc_slots (date, is_cc_plus) VALUES (:date, :is_cc_plus)';
    $stmt = $db->prepare($sql);
    $stmt->execute([':date' => $date, ':is_cc_plus' => $is_cc_plus]);
    return (int) $db->lastInsertId();
}

/**
 * キャリコン予約一覧の取得
 *
 * 返却データの構造
 * [
 *  'slot_id' => [
 *    'cc_date',
 *    'start_time' => [
 *      'display_name' => '10時～',
 *      'bookings' => [ 
 *        [
 *          'student_id'   => 1,
 *          'student_name' => '山田太郎',
 *          'course_data'  => '6B/Webプログラミング科',
 *          'style_name'   => 'ZOOM',
 *        ],
 *        // ...
 *      ],
 *    ],
 *  ],
 * ]
 *
 * @param array $filters 絞り込み条件
 *   利用可能キー: slot_date（開催日）, course_id（コースID）
 * @return array 二次元配列 予約一覧
 */
function get_cc_bookings(array $filters = []): array
{
    $db = db_connect();

    $sql = 'SELECT
                b.id                              AS booking_id,
                s.id                              AS student_id,
                CONCAT(s.last_name, s.first_name) AS student_name,
                c.id AS course_id,
                CONCAT(r.name, "/", c.name)       AS course_data,
                t.start_time                      AS start_time,
                t.display_name                    AS display_name,
                ms.id                             AS style_id,
                ms.name                           AS style_name,
                slot.id                           AS slot_id,
                slot.date                         AS cc_date
            FROM t_cc_bookings b
            JOIN m_students        s    ON b.student_id = s.id
            JOIN m_courses         c    ON s.course_id  = c.id
            JOIN m_rooms           r    ON c.room_id    = r.id
            JOIN m_times           t    ON b.time_id    = t.id
            JOIN m_meating_styles  ms   ON b.style_id   = ms.id
            JOIN t_cc_slots        slot ON b.cc_slot_id = slot.id';

    $filter_definition = [
        'booking_id' => 'b.id',
        'student_id' => 's.id',
        'slot_date' => 'slot.date',
        'course_id' => 's.course_id',
    ];

    $where_clauses = [];
    $params        = [];

    foreach ($filter_definition as $key => $column) {
        if (isset($filters[$key]) && $filters[$key] !== '') {
            $where_clauses[] = "{$column} = :{$key}";
            $params[":{$key}"] = $filters[$key];
        }
    }

    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }

    $sql .= ' ORDER BY t.start_time ASC, slot.date ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // [slot_id][start_time] に整形
    $result = [];
    foreach ($rows as $row) {
        $slot_id    = $row['slot_id'];
        $start_time = $row['start_time'];

        // slot_idの初回のみcc_dateをセット
        if (!isset($result[$slot_id])) {
            $result[$slot_id] = [
                'cc_date' => $row['cc_date'],
            ];
        }

        // start_timeの初回のみdisplay_nameをセット
        if (!isset($result[$slot_id][$start_time])) {
            $result[$slot_id][$start_time] = [
                'display_name' => $row['display_name'],
                'bookings'     => [],
            ];
        }

        $result[$slot_id][$start_time]['bookings'][] = [
            'booking_id' => $row['booking_id'],
            'student_id'   => $row['student_id'],
            'student_name' => $row['student_name'],
            'course_id' => $row['course_id'],
            'course_data'  => $row['course_data'],
            'style_id'   => $row['style_id'],
            'style_name'   => $row['style_name'],
        ];
    }

    return $result;
}

/**
 * 予約IDから予約を1件取得
 *
 * 返却構造:
 * [
 *   'booking_id'   => 1,
 *   'student_id'   => 1,
 *   'student_name' => '山田太郎',
 *   'course_id'    => 1,
 *   'course_data'  => '6B/Webプログラミング科',
 *   'cc_date'      => '2026-01-01',
 *   'start_time'   => '10:00',
 *   'style_id'     => 1,
 *   'style_name'   => 'ZOOM',
 * ]
 *
 * @param int $booking_id 取得する予約のID
 * @return array 予約情報。該当なしの場合は空配列
 */
function get_cc_booking(int $booking_id): array
{
    $db = db_connect();

    $sql = 'SELECT
                b.id                                    AS booking_id,
                s.id                                    AS student_id,
                CONCAT(s.last_name, s.first_name)       AS student_name,
                c.id                                    AS course_id,
                CONCAT(r.name, "/", c.name)             AS course_data,
                slot.date                               AS cc_date,
                DATE_FORMAT(t.start_time, \'%H:%i\')   AS start_time,
                ms.id                                   AS style_id,
                ms.name                                 AS style_name
            FROM t_cc_bookings b
            JOIN m_students       s    ON b.student_id  = s.id
            JOIN m_courses        c    ON s.course_id   = c.id
            JOIN m_rooms          r    ON c.room_id     = r.id
            JOIN t_cc_slots       slot ON b.cc_slot_id  = slot.id
            JOIN m_times          t    ON b.time_id     = t.id
            JOIN m_meating_styles ms   ON b.style_id    = ms.id
            WHERE b.id = :booking_id';

    $stmt = $db->prepare($sql);
    $stmt->execute([':booking_id' => $booking_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: [];
}

/**
 * 予約の入れ替え処理
 * @param int $booking_id_a 入れ替え予定の予約ID1
 * @param int $booking_id_b 入れ替え予定の予約ID2
 * @return bool 入れ替えに成功したかどうか
 */
function swap_cc_bookings($booking_id_a, $booking_id_b)
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        // 2件の現在のデータを取得
        $stmt = $db->prepare('SELECT * FROM t_cc_bookings WHERE id IN (:id_a, :id_b)');
        $stmt->execute([':id_a' => $booking_id_a, ':id_b' => $booking_id_b]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) !== 2) {
            throw new Exception('対象の予約が見つかりません');
        }

        [$a, $b] = $rows;

        // 一旦2件削除（制約から解放）
        $stmt = $db->prepare('DELETE FROM t_cc_bookings WHERE id IN (:id_a, :id_b)');
        $stmt->execute([':id_a' => $booking_id_a, ':id_b' => $booking_id_b]);

        // time_idを入れ替えて再INSERT
        $stmt = $db->prepare(
            'INSERT INTO t_cc_bookings (id, student_id, cc_slot_id, time_id, style_id)
             VALUES (:id, :student_id, :cc_slot_id, :time_id, :style_id)'
        );

        $stmt->execute([
            ':id'         => $a['id'],
            ':student_id' => $a['student_id'],
            ':cc_slot_id' => $b['cc_slot_id'], // ← 入れ替え
            ':time_id'    => $b['time_id'],  // ← 入れ替え
            ':style_id'   => $a['style_id'],
        ]);

        $stmt->execute([
            ':id'         => $b['id'],
            ':student_id' => $b['student_id'],
            ':cc_slot_id' => $a['cc_slot_id'], // ← 入れ替え
            ':time_id'    => $a['time_id'],  // ← 入れ替え
            ':style_id'   => $b['style_id'],
        ]);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * キャリコンプラスの空き時間の取得
 *
 * 返却データの構造
 * [
 *   time_id => true,   // 空きあり
 *   time_id => false,  // 空きなし（全スロットが予約済み）
 *   // ...
 * ]
 *
 * スロットが複数ある場合、1つでも空きがあれば true とする。
 * 例）スロット2枠・予約1件 → true、スロット2枠・予約2件 → false
 *
 * @param  string $date 対象日付 (Y-m-d形式)
 * @return array<int, bool> [time_id => 空きあり(true) / 空きなし(false), ...]
 */
function get_cc_plus_time_table(string $date): array
{
    $db = db_connect();

    // ----------------------------------------------------------------
    // 1. 引数の日付から、t_cc_slots で is_cc_plus が true のID一覧を作成
    // ----------------------------------------------------------------
    $slot_sql  = 'SELECT id FROM t_cc_slots WHERE date = :date AND is_cc_plus = 1';
    $slot_stmt = $db->prepare($slot_sql);
    $slot_stmt->execute([':date' => $date]);
    $slot_ids  = $slot_stmt->fetchAll(PDO::FETCH_COLUMN); // [1, 2, ...]

    // m_times の全レコードを取得（returnする構造のベースになる）
    $times_stmt = $db->query('SELECT id FROM m_times ORDER BY id ASC');
    $all_time_ids = $times_stmt->fetchAll(PDO::FETCH_COLUMN); // [1, 2, 3, ...]

    // キャリコンプラス枠が存在しない日付の場合は全時間 false を返す
    if (empty($slot_ids)) {
        return array_fill_keys($all_time_ids, false);
    }

    // ----------------------------------------------------------------
    // 2. 1.のスロットID一覧から、t_cc_bookings の予約を time_id ごとに集計
    // ----------------------------------------------------------------
    // IN句のプレースホルダーを動的に生成 (:id0, :id1, ...)
    $placeholders = implode(', ', array_map(fn($i) => ":id{$i}", array_keys($slot_ids)));

    $booking_sql = "SELECT time_id, COUNT(*) AS booked_count
                    FROM t_cc_bookings
                    WHERE cc_slot_id IN ({$placeholders})
                    GROUP BY time_id";

    $booking_stmt = $db->prepare($booking_sql);
    foreach ($slot_ids as $i => $slot_id) {
        $booking_stmt->bindValue(":id{$i}", $slot_id, PDO::PARAM_INT);
    }
    $booking_stmt->execute();
    $booking_rows = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);

    // [time_id => booked_count] に整形
    $booked_count_by_time_id = [];
    foreach ($booking_rows as $row) {
        $booked_count_by_time_id[$row['time_id']] = (int) $row['booked_count'];
    }

    // ----------------------------------------------------------------
    // 3 & 4. 全時間について「予約数 < スロット数」であれば空きあり(true) として配列を構築
    // ----------------------------------------------------------------
    $slot_count = count($slot_ids);

    $result = [];
    foreach ($all_time_ids as $time_id) {
        $booked_count    = $booked_count_by_time_id[$time_id] ?? 0;
        $result[$time_id] = $booked_count < $slot_count;
    }

    return $result;
}

/**
 * キャリコン予約を登録
 *
 * t_cc_bookings に1件INSERT し、採番されたIDを返す
 *
 * @param  PDO      $db                 DB接続（トランザクション管理用に外部から受け取る）
 * @param  int      $student_id         予約する生徒のID
 * @param  int      $cc_slot_id         予約するスロットのID
 * @param  int      $time_id            予約する時間のID
 * @param  int      $style_id           面談スタイルのID
 * @param  int|null $cc_plus_booking_id CC+仮予約から確定する場合、元CC+予約のID。通常予約の場合はnull
 * @return int      採番された予約ID
 */
function add_cc_booking(PDO $db, $student_id, $cc_slot_id, $time_id, $style_id, ?int $cc_plus_booking_id = null): int
{
    $sql = 'INSERT INTO t_cc_bookings
                (student_id, cc_slot_id, time_id, style_id, cc_plus_booking_id)
            VALUES
                (:student_id, :cc_slot_id, :time_id, :style_id, :cc_plus_booking_id)';
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':student_id'         => $student_id,
        ':cc_slot_id'         => $cc_slot_id,
        ':time_id'            => $time_id,
        ':style_id'           => $style_id,
        ':cc_plus_booking_id' => $cc_plus_booking_id,  // ← 追加
    ]);
    return (int) $db->lastInsertId();
}

/**
 * キャリコンプラスの申請情報を登録
 *
 * t_cc_requests に1件INSERTする
 * status_id = 1（新規）で固定
 * @param  PDO        $db           DB接続（トランザクション管理用に外部から受け取る）
 * @param  int        $type_id      申請種別ID（1:cc+予約 / 2:cc+変更 / 3:cc+キャンセル / 4:cc変更）
 * @param  int        $student_id   申請する生徒のID
 * @param  int        $booking_id_a 変更元の予約ID（予約申請時は新規予約ID）
 * @param  int|null   $booking_id_b 変更先の予約ID（変更申請時のみ指定。デフォルトnull）
 * @param  string|null $message     申請メッセージ（任意）
 * @return void
 */
function add_cc_request(PDO $db, int $type_id, int $student_id, int $booking_id_a, ?int $booking_id_b = null, ?string $message = null): void
{
    $sql  = 'INSERT INTO t_cc_requests (type_id, student_id, status_id, booking_id_a, booking_id_b, message)
             VALUES (:type_id, :student_id, :status_id, :booking_id_a, :booking_id_b, :message)';
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':type_id'      => $type_id,
        ':student_id'   => $student_id,
        ':status_id'    => 1, // 新規
        ':booking_id_a' => $booking_id_a,
        ':booking_id_b' => $booking_id_b,
        ':message'      => $message,
    ]);
}

/**
 * キャリコンプラスの予約登録
 *
 * 空きスロットの特定・t_cc_bookings登録・t_cc_requests登録をトランザクション内で一括実行する
 * @param  int         $student_id 予約する生徒のID
 * @param  string      $date       予約日（Y-m-d形式）
 * @param  int         $time_id    予約する時間のID
 * @param  int         $style_id   面談スタイルのID
 * @param  string|null $message    申請メッセージ（任意）
 * @return bool        登録成功時はtrue、失敗時はfalse
 */
function book_cc_plus(int $student_id, string $date, int $time_id, int $style_id, ?string $message = null): bool
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        // 1. 指定日・時間で空きのあるスロットIDを取得
        $slot_sql  = 'SELECT s.id
                      FROM t_cc_slots s
                      WHERE s.date        = :date
                        AND s.is_cc_plus  = 1
                        AND NOT EXISTS (
                            SELECT 1 FROM t_cc_bookings b
                            WHERE b.cc_slot_id = s.id
                              AND b.time_id    = :time_id
                        )
                      LIMIT 1';
        $slot_stmt = $db->prepare($slot_sql);
        $slot_stmt->execute([
            ':date'    => $date,
            ':time_id' => $time_id,
        ]);
        $cc_slot_id = $slot_stmt->fetchColumn();

        // 空きスロットが見つからない場合は登録せず終了
        if ($cc_slot_id === false) {
            throw new Exception('空きスロットが見つかりません');
        }

        // 2. t_cc_bookings に登録
        $booking_id = add_cc_booking($db, $student_id, (int) $cc_slot_id, $time_id, $style_id);

        // 3. t_cc_requests に登録
        add_cc_request($db, 1, $student_id, $booking_id, null, $message);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * キャリコンプラスの予約変更申請（ラッパー）
 *
 * 変更先の空きスロット特定・t_cc_bookings登録・t_cc_requests登録を
 * トランザクション内で一括実行する
 * ※ 変更元の予約削除は管理者の承認後に行うため、この関数では実施しない
 * @param  int         $student_id          申請する生徒のID
 * @param  int         $from_booking_id     変更元の予約ID（booking_id_a に設定）
 * @param  string      $date                変更先の予約日（Y-m-d形式）
 * @param  int         $time_id             変更先の時間ID
 * @param  int         $style_id            面談スタイルID
 * @param  string|null $message             申請メッセージ（任意）
 * @return bool        登録成功時はtrue、失敗時はfalse
 */
function book_cc_plus_change(int $student_id, int $from_booking_id, string $date, int $time_id, int $style_id, ?string $message = null): bool
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        // 1. 変更先の空きスロットIDを取得
        $slot_sql  = 'SELECT s.id
                      FROM t_cc_slots s
                      WHERE s.date       = :date
                        AND s.is_cc_plus = 1
                        AND NOT EXISTS (
                            SELECT 1 FROM t_cc_bookings b
                            WHERE b.cc_slot_id = s.id
                              AND b.time_id    = :time_id
                        )
                      LIMIT 1';
        $slot_stmt = $db->prepare($slot_sql);
        $slot_stmt->execute([
            ':date'    => $date,
            ':time_id' => $time_id,
        ]);
        $cc_slot_id = $slot_stmt->fetchColumn();

        // 空きスロットが見つからない場合は登録せず終了
        if ($cc_slot_id === false) {
            throw new Exception('空きスロットが見つかりません');
        }

        // 2. 変更先を t_cc_bookings に登録
        $to_booking_id = add_cc_booking($db, $student_id, (int) $cc_slot_id, $time_id, $style_id);

        // 3. t_cc_requests に変更申請を登録
        //    booking_id_a = 変更元、booking_id_b = 変更先
        add_cc_request($db, 2, $student_id, $from_booking_id, $to_booking_id, $message);
        //                  ↑ type_id=2（cc+変更）

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * キャリコンプラスのキャンセル申請（ラッパー）
 *
 * t_cc_requests にキャンセル申請を登録する
 * キャンセル対象の予約削除は管理者の承認後に行うため、この関数では実施しない
 *
 * @param  int         $student_id 申請する生徒のID
 * @param  int         $booking_id キャンセル対象の予約ID（booking_id_a に設定）
 * @param  string|null $message    申請メッセージ（任意）
 * @return bool        登録成功時はtrue、失敗時はfalse
 */
function book_cc_plus_cancel(int $student_id, int $booking_id, ?string $message = null): bool
{
    $db = db_connect();

    try {
        add_cc_request($db, 3, $student_id, $booking_id, null, $message);
        //                  ↑ type_id=3（cc+キャンセル）
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 必須キャリコンの変更申請
 *
 * t_cc_requests に変更申請を登録する
 * @param  int         $student_id   申請する生徒のID
 * @param  int         $booking_id_a 入れ替え対象の予約ID①（booking_id_a に設定）
 * @param  int         $booking_id_b 入れ替え対象の予約ID②（booking_id_b に設定）
 * @param  string|null $message      申請メッセージ（任意）
 * @return bool        登録成功時はtrue、失敗時はfalse
 */
function request_cc_change(int $student_id, int $booking_id_a, int $booking_id_b, ?string $message = null): bool
{
    $db = db_connect();

    try {
        add_cc_request($db, 4, $student_id, $booking_id_a, $booking_id_b, $message);
        //                  ↑ type_id=4（cc変更）
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * CC+変更申請の承認処理
 * 
 * ※ 変更先CC+仮予約（booking_id_b）はそのまま残り、新しい仮予約として有効になる
 * @param  int  $request_id 承認する t_cc_requests の ID
 * @return bool 成功時 true、失敗時 false
 */
function approve_cc_plus_change(int $request_id): bool
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        // 申請情報を取得（type_id=2:CC+変更 であることも確認）
        $req_stmt = $db->prepare(
            'SELECT booking_id_a, booking_id_b
             FROM t_cc_requests
             WHERE id = :request_id
               AND type_id = 2
               AND status_id = 1'  // 未処理のみ承認可能
        );
        $req_stmt->execute([':request_id' => $request_id]);
        $request = $req_stmt->fetch();

        if (!$request) {
            throw new Exception('対象の申請が見つかりません');
        }

        $booking_id_a = $request['booking_id_a']; // 変更元CC+仮予約ID
        $booking_id_b = $request['booking_id_b']; // 変更先CC+仮予約ID

        if (!$booking_id_a || !$booking_id_b) {
            throw new Exception('予約IDが不正です');
        }

        // 1. 変更元CC+に紐づく確定済み通常予約を削除（存在しない場合は何もしない）
        $db->prepare(
            'DELETE FROM t_cc_bookings
             WHERE cc_plus_booking_id = :booking_id_a'
        )->execute([':booking_id_a' => $booking_id_a]);

        // 2. 変更元CC+仮予約を削除
        $db->prepare(
            'DELETE FROM t_cc_bookings
             WHERE id = :booking_id_a
               AND cc_plus_booking_id IS NULL'  // 念のため仮予約であることを確認
        )->execute([':booking_id_a' => $booking_id_a]);

        // 3. 申請ステータスを承認済みに更新
        $db->prepare(
            'UPDATE t_cc_requests
             SET status_id = 2
             WHERE id = :request_id'
        )->execute([':request_id' => $request_id]);

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * CC+変更申請の却下処理
 *
 * 変更先CC+仮予約（booking_id_b）を削除し、変更元は現状維持
 *
 * @param  int  $request_id 却下する t_cc_requests の ID
 * @return bool 成功時 true、失敗時 false
 */
function reject_cc_plus_change(int $request_id): bool
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        $req_stmt = $db->prepare(
            'SELECT booking_id_b
             FROM t_cc_requests
             WHERE id = :request_id
               AND type_id = 2
               AND status_id = 1'
        );
        $req_stmt->execute([':request_id' => $request_id]);
        $request = $req_stmt->fetch();

        if (!$request || !$request['booking_id_b']) {
            throw new Exception('対象の申請が見つかりません');
        }

        // 変更先CC+仮予約を削除
        $db->prepare(
            'DELETE FROM t_cc_bookings WHERE id = :booking_id_b'
        )->execute([':booking_id_b' => $request['booking_id_b']]);

        // 申請ステータスを却下に更新
        $db->prepare(
            'UPDATE t_cc_requests SET status_id = 3 WHERE id = :request_id'
        )->execute([':request_id' => $request_id]);

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * 必須キャリコン一括予約登録
 *
 * 指定コースの全生徒に対して、全回数分の必須キャリコン予約をまとめて登録する。
 *
 * 処理方針:
 * - cc_count ごとに、生徒を日付数で均等分割（端数は前の日付グループに寄せる）
 * - 各日付グループ内の生徒を m_times の件数ずつチャンクに分割し、チャンクごとにスロットを1件生成
 * - time_id はチャンク内の出席番号順に m_times.id を先頭から割り当てる
 * - 既に同 cc_count の予約が存在する生徒はスキップ（その他の生徒の登録は続行）
 * - style_id はデフォルト値(1)で登録する
 *
 * @param  int  $course_id 対象コースのID
 * @return bool 成功時 true、DBエラー時 false
 */
function bulk_book_cc(int $course_id): bool
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        // 1. コースの全生徒を出席番号昇順で取得
        $students_stmt = $db->prepare(
            'SELECT id, number FROM m_students WHERE course_id = :course_id ORDER BY number ASC'
        );
        $students_stmt->execute([':course_id' => $course_id]);
        $students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

        // 生徒が0人の場合は正常終了
        if (empty($students)) {
            $db->commit();
            return true;
        }

        // 2. cc_count ごとの日付一覧を取得
        $sched_stmt = $db->prepare(
            'SELECT cc_count, date
             FROM t_course_cc_schedules
             WHERE course_id = :course_id
             ORDER BY cc_count ASC, date ASC'
        );
        $sched_stmt->execute([':course_id' => $course_id]);

        // [cc_count => [date, ...]] に整形
        $schedules = [];
        foreach ($sched_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $schedules[$row['cc_count']][] = $row['date'];
        }

        // 3. m_times の全IDを昇順で取得
        $time_ids   = $db->query('SELECT id FROM m_times ORDER BY id ASC')->fetchAll(PDO::FETCH_COLUMN);
        $time_count = count($time_ids);

        $student_ids = array_column($students, 'id');

        // 4. cc_count ごとにループ
        foreach ($schedules as $cc_count => $dates) {
            $date_count = count($dates);

            // 既存予約がある student_id を取得してスキップリストを作成
            $id_placeholders = implode(', ', array_fill(0, count($student_ids), '?'));
            $skip_stmt       = $db->prepare(
                "SELECT DISTINCT b.student_id
                 FROM t_cc_bookings b
                 JOIN t_cc_slots sl             ON b.cc_slot_id  = sl.id
                 JOIN t_course_cc_schedules sch ON sl.date       = sch.date
                 WHERE b.student_id           IN ({$id_placeholders})
                   AND sch.course_id           = ?
                   AND sch.cc_count            = ?
                   AND sl.is_cc_plus           = 0
                   AND b.cc_plus_booking_id    IS NULL"
            );
            $skip_stmt->execute(array_merge($student_ids, [$course_id, $cc_count]));
            // isset() で高速検索できるよう id をキーに反転
            $skip_ids = array_flip($skip_stmt->fetchAll(PDO::FETCH_COLUMN));

            // 生徒を日付数で均等分割（端数は前の日付グループに寄せる）
            $chunk_size     = (int) ceil(count($students) / $date_count);
            $student_groups = array_chunk($students, $chunk_size);

            foreach ($dates as $date_index => $date) {
                $group = $student_groups[$date_index] ?? [];
                if (empty($group)) {
                    continue;
                }

                // スキップ対象を除外
                $targets = array_values(
                    array_filter($group, fn($s) => !isset($skip_ids[$s['id']]))
                );

                if (empty($targets)) {
                    continue;
                }

                // m_times の件数ずつチャンクに分割し、チャンクごとにスロットを生成
                foreach (array_chunk($targets, $time_count) as $chunk) {
                    // スロットを1件INSERT し、採番されたIDを取得
                    $slot_stmt = $db->prepare(
                        'INSERT INTO t_cc_slots (date, is_cc_plus) VALUES (:date, 0)'
                    );
                    $slot_stmt->execute([':date' => $date]);
                    $slot_id = (int) $db->lastInsertId();

                    // チャンク内の生徒を出席番号順に time_id を割り当てて一括INSERT
                    $values_sql = [];
                    $params     = [];
                    foreach ($chunk as $i => $student) {
                        $values_sql[]                    = "(:student_id_{$i}, :slot_id_{$i}, :time_id_{$i}, :style_id_{$i})";
                        $params[":student_id_{$i}"]      = $student['id'];
                        $params[":slot_id_{$i}"]         = $slot_id;
                        $params[":time_id_{$i}"]         = $time_ids[$i];
                        $params[":style_id_{$i}"]        = 1; // デフォルトスタイル
                    }

                    $booking_sql = 'INSERT INTO t_cc_bookings (student_id, cc_slot_id, time_id, style_id) VALUES '
                        . implode(', ', $values_sql);
                    $db->prepare($booking_sql)->execute($params);
                }
            }
        }

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}


/*
 * 指定コース・回数の必須キャリコン予約を日付・時間でグループ化して返す
 * CC+から確定した通常予約（cc_plus_booking_id IS NOT NULL）は除外する
 *
 * 返却構造:
 * [
 *   '2026-01-01' => [
 *     '10:00' => [
 *       ['booking_id' => 1, 'student_name' => '山田太郎'],
 *       ...
 *     ],
 *     '11:00' => [...],
 *   ],
 *   ...
 * ]
 *
 * @param int $course_id 対象コースのID
 * @param int $cc_count  対象の回数（第何回目か）
 * @return array 日付 > 時間 > 予約一覧 の三次元配列
 */
function get_course_cc_bookings(int $course_id, int $cc_count): array
{
    $db = db_connect();

    $sql = 'SELECT
                b.id                                 AS booking_id,
                CONCAT(s.last_name, s.first_name)    AS student_name,
                sl.date                              AS cc_date,
                DATE_FORMAT(t.start_time, \'%H:%i\') AS start_time
            FROM t_course_cc_schedules sched
            JOIN t_cc_slots sl
                ON  sl.date       = sched.date
                AND sl.is_cc_plus = 0
            JOIN t_cc_bookings b
                ON  b.cc_slot_id         = sl.id
                AND b.cc_plus_booking_id IS NULL
            JOIN m_students s
                ON  b.student_id = s.id
                AND s.course_id  = :course_id_student
            JOIN m_times t
                ON  b.time_id = t.id
            WHERE sched.course_id = :course_id_sched
              AND sched.cc_count  = :cc_count
            ORDER BY sl.date ASC, t.start_time ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':course_id_sched'   => $course_id,
        ':course_id_student' => $course_id,
        ':cc_count'          => $cc_count,
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($rows as $row) {
        $result[$row['cc_date']][$row['start_time']][] = [
            'booking_id'   => $row['booking_id'],
            'student_name' => $row['student_name'],
        ];
    }

    return $result;
}

/**
 * 日付と生徒IDから必須キャリコン予約一覧を取得
 *
 * student_id → course_id → cc_count の順に解決し、
 * get_course_cc_bookings() に委譲して結果を返す
 *
 * @param int    $student_id 生徒のID
 * @param string $date       対象日付（Y-m-d形式）
 * @return array get_course_cc_bookings() と同じ構造。
 *               course_idまたはcc_countが取得できない場合は空配列を返す
 */
function get_course_cc_bookings_by_student(int $student_id, string $date): array
{
    $db = db_connect();

    // 1. student_id → course_id
    $stmt = $db->prepare('SELECT course_id FROM m_students WHERE id = :student_id');
    $stmt->execute([':student_id' => $student_id]);
    $course_id = $stmt->fetchColumn();

    if ($course_id === false) {
        return [];
    }

    // 2. course_id + date → cc_count
    $stmt = $db->prepare(
        'SELECT cc_count
         FROM t_course_cc_schedules
         WHERE course_id = :course_id
           AND date      = :date'
    );
    $stmt->execute([
        ':course_id' => $course_id,
        ':date'      => $date,
    ]);
    $cc_count = $stmt->fetchColumn();

    if ($cc_count === false) {
        return [];
    }

    // 3. get_course_cc_bookings() に委譲
    return get_course_cc_bookings((int) $course_id, (int) $cc_count);
}

[
    'my_self' => [
        'course_name',
        'student_name',
        'from_datetime',
        'to_datetime',
    ],
    'target' => [
        'course_name',
        'student_name',
        'from_datetime',
        'to_datetime',
    ]
];

/**
 * キャリコン変更申請確認画面用データの取得
 *
 * 2つの予約IDからそれぞれの生徒情報と入れ替え後の日時を返す
 *
 * 返却構造:
 * [
 *   'my_self' => [
 *     'course_name'   => 'Webプログラミング科',
 *     'student_name'  => '山田太郎',
 *     'from_datetime' => '2026-01-01 10:00',  // booking_id_aの日時
 *     'to_datetime'   => '2026-02-01 11:00',  // booking_id_bの日時
 *   ],
 *   'target' => [
 *     'course_name'   => 'Webプログラミング科',
 *     'student_name'  => '鈴木花子',
 *     'from_datetime' => '2026-02-01 11:00',  // booking_id_bの日時
 *     'to_datetime'   => '2026-01-01 10:00',  // booking_id_aの日時
 *   ],
 * ]
 *
 * @param int $booking_id_a 自分の予約ID
 * @param int $booking_id_b 変更先の予約ID
 * @return array 確認画面用データ。いずれかの予約が取得できない場合は空配列
 */
function get_cc_change_confirm(int $booking_id_a, int $booking_id_b): array
{
    $db = db_connect();

    $sql = 'SELECT
                b.id                                    AS booking_id,
                CONCAT(s.last_name, s.first_name)       AS student_name,
                c.name                                  AS course_name,
                CONCAT(sl.date, \' \',
                    DATE_FORMAT(t.start_time, \'%H:%i\')) AS datetime
            FROM t_cc_bookings b
            JOIN m_students s  ON b.student_id  = s.id
            JOIN m_courses  c  ON s.course_id   = c.id
            JOIN t_cc_slots sl ON b.cc_slot_id  = sl.id
            JOIN m_times    t  ON b.time_id     = t.id
            WHERE b.id IN (:booking_id_a, :booking_id_b)';

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':booking_id_a' => $booking_id_a,
        ':booking_id_b' => $booking_id_b,
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2件取得できない場合は異常とみなす
    if (count($rows) !== 2) {
        return [];
    }

    // booking_idをキーにして引きやすくする
    $bookings = array_column($rows, null, 'booking_id');
    $a = $bookings[$booking_id_a];
    $b = $bookings[$booking_id_b];

    return [
        'my_self' => [
            'course_name'   => $a['course_name'],
            'student_name'  => $a['student_name'],
            'from_datetime' => $a['datetime'],
            'to_datetime'   => $b['datetime'],
        ],
        'target' => [
            'course_name'   => $b['course_name'],
            'student_name'  => $b['student_name'],
            'from_datetime' => $b['datetime'],
            'to_datetime'   => $a['datetime'],
        ],
    ];
}

