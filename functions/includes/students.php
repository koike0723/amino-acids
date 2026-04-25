<?php
require_once __DIR__ . '/db.php';

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
    $sql = 'SELECT CONCAT(first_name,last_name) AS student_name,password ,id FROM m_students WHERE login_id=:login_id ';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':login_id', $login_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if (password_verify($password, $result['password'])) {
            $_SESSION['student_id'] = $result['id'];
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
 * 'status_id',
 * 'status_name',
 * 'course_id',
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
            ss.id AS status_id,
            ss.name AS status_name,
            c.id AS course_id,
            c.name AS course_name,
            c.start_date,
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

    // 日付指定がある場合はその日付で範囲チェック、なければ今日以降のみ
    if (isset($filters['date']) && $filters['date'] !== '') {
        $where_clauses[] = 'c.start_date <= :date_start AND c.end_date >= :date_end';
        $params[':date_start'] = $filters['date'];
        $params[':date_end']   = $filters['date'];
    } elseif (!$is_display_end) {
        $where_clauses[] = 'c.end_date >= :now_date';
        $params[':now_date'] = date('Y-m-d');
    }

    // 3. WHERE句の組み立て
    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }
    $sql .= ' ORDER BY  r.id ASC, s.number ASC';
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
 * 'first_name',
 * 'last_name',
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
            s.first_name,
            s.last_name,
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

    // 1パス目: 同一日付に複数の異なる時間帯を持つCC+予約の日付を特定する
    $cc_plus_times_by_date = [];
    foreach ($result as $row) {
        if ($row['booking_id'] && $row['is_cc_plus']) {
            $cc_plus_times_by_date[$row['cc_date']][$row['cc_time']] = true;
        }
    }
    // 異なる時間帯が2つ以上ある日付のみ残す（除外対象）
    $conflict_cc_plus_dates = array_filter(
        $cc_plus_times_by_date,
        fn($times) => count($times) > 1
    );
    
    $student = [];
    foreach ($result as $row) {
        if (empty($student)) {
            $student = [
                'student_id'   => $row['student_id'],
                'first_name'   => $row['first_name'],
                'last_name'    => $row['last_name'],
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
            // CC+予約かつ同日に異なる時間帯が複数存在する場合はスキップ
            if ($row['is_cc_plus'] && isset($conflict_cc_plus_dates[$row['cc_date']])) {
                continue;
            }
            $student['bookings'][] = [
                'booking_id'      => $row['booking_id'],
                'cc_slot_id'      => $row['cc_slot_id'],
                'is_cc_plus'      => (bool) $row['is_cc_plus'],
                'cc_consultant'   => $row['cc_consultant'],
                'cc_room'         => $row['cc_room'],
                'cc_date'         => $row['cc_date'],
                'cc_time'         => $row['cc_time'],
                'cc_display_time' => $row['cc_display_time'],
                'cc_style_id'     => $row['cc_style_id'],
                'cc_style_name'   => $row['cc_style_name'],
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
