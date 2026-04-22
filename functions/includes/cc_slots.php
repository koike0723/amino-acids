<?php
require_once __DIR__ . '/db.php';

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
 * キャリコン開催予定一覧を取得
 *
 * 基準日以降・指定範囲内のキャリコン開催予定を日付をキーとした連想配列で返す。
 * t_cc_slots の枠数集計と t_course_cc_schedules の必須CC開催コース一覧をマージして返す。
 *
 * 戻り値の構造:
 * [
 *   'Y' => [
 *     'm' => [
 *       'd' => [
 *         'cc_list'       => [ course_id(int) => room_name(string), ... ],
 *         'cc_plus_count' => int,  // is_cc_plus=1 の枠数
 *         'line_count'    => int,  // 全枠数（is_cc_plus 問わず）
 *       ],
 *     ],
 *   ],
 * ]
 *
 * @param string|null $base_date  基準日（デフォルト: 今日）。この日付以降が対象
 * @param int         $range      表示範囲（月数）。1, 2, 3, 6, 12 など。デフォルト: 2
 * @param array|null  $course_ids 絞り込むコースIDの配列。null の場合は全コース対象
 * @return array キャリコン開催予定一覧
 */
function get_cc_schedule_list(
    ?string $base_date = null,
    int $range = 2,
    ?array $course_ids = null
): array {
    $base_date ??= date('Y-m-d');

    // 終了日：基準日 + range ヶ月後（当日含む）
    $end_date = (new DateTime($base_date))
        ->modify("+{$range} months")
        ->format('Y-m-d');

    $db = db_connect();

    // --- 1. t_cc_slots から日付ごとの枠数を集計 ---
    $slot_sql = 'SELECT
                    date,
                    SUM(CASE WHEN is_cc_plus = 1 THEN 1 ELSE 0 END) AS cc_plus_count,
                    COUNT(*) AS line_count
                 FROM t_cc_slots
                 WHERE date >= :base_date
                   AND date <= :end_date
                 GROUP BY date
                 ORDER BY date ASC';

    $slot_stmt = $db->prepare($slot_sql);
    $slot_stmt->execute([
        ':base_date' => $base_date,
        ':end_date'  => $end_date,
    ]);
    $slot_rows = $slot_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 2. t_course_cc_schedules から必須CC開催コース一覧を取得 ---
    $schedule_params = [
        ':base_date' => $base_date,
        ':end_date'  => $end_date,
    ];

    $schedule_sql = 'SELECT
                        s.date,
                        s.course_id,
                        r.name AS room_name
                     FROM t_course_cc_schedules s
                     JOIN m_courses c ON s.course_id = c.id
                     LEFT JOIN m_rooms r ON c.room_id = r.id
                     WHERE s.date >= :base_date
                       AND s.date <= :end_date';

    // コースIDが指定されている場合は絞り込む
    if (!empty($course_ids)) {
        $id_placeholders = [];
        foreach ($course_ids as $i => $id) {
            $key = ':course_id_' . $i;
            $id_placeholders[] = $key;
            $schedule_params[$key] = $id;
        }
        $schedule_sql .= ' AND s.course_id IN (' . implode(', ', $id_placeholders) . ')';
    }

    $schedule_sql .= ' ORDER BY s.date ASC';

    $schedule_stmt = $db->prepare($schedule_sql);
    $schedule_stmt->execute($schedule_params);
    $schedule_rows = $schedule_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 3. 結果をマージして構造化 ---
    $result = [];

    // ヘルパー: 'Y-m-d' 形式の日付を [Y, m, d] に分解
    $split_date = fn(string $date): array => explode('-', $date);

    // t_cc_slots の集計をベースに初期化
    foreach ($slot_rows as $row) {
        [$y, $m, $d] = $split_date($row['date']);
        $result[$y][$m][$d] = [
            'cc_list'       => [],
            'cc_plus_count' => (int) $row['cc_plus_count'],
            'line_count'    => (int) $row['line_count'],
        ];
    }

    // t_course_cc_schedules の情報を cc_list に追加
    // ※ t_cc_slots に対応する日付がない場合も許容（カウントは 0 で初期化）
    foreach ($schedule_rows as $row) {
        [$y, $m, $d] = $split_date($row['date']);
        if (!isset($result[$y][$m][$d])) {
            $result[$y][$m][$d] = [
                'cc_list'       => [],
                'cc_plus_count' => 0,
                'line_count'    => 0,
            ];
        }
        $result[$y][$m][$d]['cc_list'][(int) $row['course_id']] = $row['room_name'];
    }

    // 年・月・日の順に並び替えて返す
    ksort($result);
    foreach ($result as &$months) {
        ksort($months);
        foreach ($months as &$days) {
            ksort($days);
        }
    }
    unset($months, $days);

    return $result;
}
