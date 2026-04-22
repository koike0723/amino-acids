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
