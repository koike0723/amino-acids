<?php
require_once __DIR__ . '/db.php';

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

    $sql .= ' ORDER BY r.id ASC, c.start_date ASC';

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

    try {
        $db->beginTransaction();
        // 3. 結合して実行
        if (!empty($row_values)) {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $last_id = $db->lastInsertId();
        }

        //必須キャリコンのスケジュール登録
        if (isset($course['cc']) && $course['cc'] != '') {
            add_course_cc_schedules($db, $last_id, $course['cc']);
        }
        $db->commit();
        return true;
    } catch (PDOException $e) {
        $db->rollback();
        return false;
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
function update_course(int $course_id, array $data)
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
                add_course_cc_schedules($db, $course_id, $data['cc']);
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
function add_course_cc_schedules($db, $course_id, $cc_schedules)
{

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
