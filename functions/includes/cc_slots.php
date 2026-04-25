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
            s.id AS slot_id,
            s.date AS cc_date,
            s.is_cc_plus,
            s.consultant_id,
            s.room_id,
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
        $params[':target_date'] = $target_date;
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

    // コースIDが指定されている場合のプレースホルダー事前生成
    $course_id_placeholders = [];
    $course_id_params       = [];
    if (!empty($course_ids)) {
        foreach ($course_ids as $i => $id) {
            $key = ':course_id_' . $i;
            $course_id_placeholders[] = $key;
            $course_id_params[$key]   = $id;
        }
    }
    $in_clause = implode(', ', $course_id_placeholders);

    // --- 1. t_cc_slots から日付ごとの枠数を集計 ---
    // course_ids 指定時：対象コースのCCがある日付のみに絞り込む
    $slot_sql = 'SELECT
                    date,
                    SUM(CASE WHEN is_cc_plus = 1 THEN 1 ELSE 0 END) AS cc_plus_count,
                    SUM(CASE WHEN is_cc_plus = 0 THEN 1 ELSE 0 END) AS line_count
                 FROM t_cc_slots
                 WHERE date >= :base_date
                   AND date <= :end_date';

    if (!empty($course_ids)) {
        $slot_sql .= " AND date IN (
            SELECT date FROM t_course_cc_schedules
            WHERE course_id IN ({$in_clause})
        )";
    }

    $slot_sql .= ' GROUP BY date ORDER BY date ASC';

    $slot_params = array_merge(
        [':base_date' => $base_date, ':end_date' => $end_date],
        $course_id_params
    );
    $slot_stmt = $db->prepare($slot_sql);
    $slot_stmt->execute($slot_params);
    $slot_rows = $slot_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 2. t_course_cc_schedules から必須CC開催コース一覧を取得 ---
    // course_ids 指定時でも全コースを返す（同日開催の他コースも表示するため）
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
                       AND s.date <= :end_date
                     ORDER BY s.date ASC';

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
    // ※ course_ids 未指定時のみ、t_cc_slots に対応する日付がない場合も許容（カウントは 0 で初期化）
    // ※ course_ids 指定時は slot_rows で絞り込み済みの日付のみ対象とする（余分な日付の混入を防ぐ）
    foreach ($schedule_rows as $row) {
        [$y, $m, $d] = $split_date($row['date']);
        if (!isset($result[$y][$m][$d])) {
            if (!empty($course_ids)) continue;
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

/**
 * キャリコンプラス枠数を調整する
 *
 * 指定日のキャリコンプラス枠数をパラメータに合わせて増減する。
 * - パラメータ > 現在数: 不足分を追加
 * - パラメータ < 現在数: 超過分を削除（予約なし枠を優先して削除）
 * - 削除時に予約がある場合は、紐づく予約（CC+仮予約・確定通常予約）も削除する
 *   ※ t_cc_requests の booking_id_a/b は FK の ON DELETE SET NULL により自動でNULLになる
 *
 * @param  string $date          対象日付（Y-m-d 形式）
 * @param  int    $cc_plus_count 目標のキャリコンプラス枠数（0以上）
 * @return bool                  成功時 true、失敗時 false
 */
function update_cc_plus_slot_count(string $date, int $cc_plus_count): bool
{
    if ($cc_plus_count < 0) {
        return false;
    }

    $db = db_connect();

    try {
        $db->beginTransaction();

        // 1. 現在のCC+スロット一覧を取得（予約数も集計し、削除優先順に並べる）
        //    ORDER: 予約なし → 予約あり → ID昇順（古い順）
        $stmt = $db->prepare(
            'SELECT s.id, COUNT(b.id) AS booking_count
             FROM t_cc_slots s
             LEFT JOIN t_cc_bookings b ON b.cc_slot_id = s.id
             WHERE s.date = :date
               AND s.is_cc_plus = 1
             GROUP BY s.id
             ORDER BY booking_count ASC, s.id ASC'
        );
        $stmt->execute([':date' => $date]);
        $current_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $current_count = count($current_slots);

        $diff = $cc_plus_count - $current_count;

        // 変化なし
        if ($diff === 0) {
            $db->commit();
            return true;
        }

        // ── 枠を追加 ──────────────────────────────────────────
        if ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                add_cc_slot($date, true);
            }
            $db->commit();
            return true;
        }

        // ── 枠を削除 ──────────────────────────────────────────
        // 予約なし枠が先頭に来ているので先頭から $delete_count 件を対象にする
        $delete_count  = abs($diff);
        $slots_to_delete = array_slice($current_slots, 0, $delete_count);

        foreach ($slots_to_delete as $slot) {
            $slot_id = (int) $slot['id'];

            if ((int) $slot['booking_count'] > 0) {
                // このスロットに紐づくCC+予約IDを取得
                $id_stmt = $db->prepare(
                    'SELECT id FROM t_cc_bookings WHERE cc_slot_id = :slot_id'
                );
                $id_stmt->execute([':slot_id' => $slot_id]);
                $cc_plus_booking_ids = $id_stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($cc_plus_booking_ids)) {
                    $placeholders = implode(
                        ', ',
                        array_map(fn($i) => ":bid{$i}", array_keys($cc_plus_booking_ids))
                    );

                    // ① CC+予約から確定した通常予約を先に削除
                    $del_derived = $db->prepare(
                        "DELETE FROM t_cc_bookings
                         WHERE cc_plus_booking_id IN ({$placeholders})"
                    );
                    foreach ($cc_plus_booking_ids as $i => $bid) {
                        $del_derived->bindValue(":bid{$i}", $bid, PDO::PARAM_INT);
                    }
                    $del_derived->execute();

                    // ② CC+仮予約を削除
                    //    t_cc_requests の booking_id_a/b は FK ON DELETE SET NULL で自動NULL化
                    $db->prepare(
                        'DELETE FROM t_cc_bookings WHERE cc_slot_id = :slot_id'
                    )->execute([':slot_id' => $slot_id]);
                }
            }

            // ③ スロットを削除
            $db->prepare(
                'DELETE FROM t_cc_slots WHERE id = :slot_id'
            )->execute([':slot_id' => $slot_id]);
        }

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * 必須CCスロットを削除
 *
 * 指定した必須CCスロット（is_cc_plus=0）を削除する。
 * 紐づく予約（t_cc_bookings）も合わせて削除する。
 * t_cc_requests の booking_id_a/b は FK ON DELETE SET NULL により自動でNULLになる。
 *
 * @param  int  $slot_id 削除対象のスロットID
 * @return bool          成功時 true、失敗時 false
 */
function delete_cc_slot(int $slot_id): bool
{
    $db = db_connect();
    try {
        $db->beginTransaction();

        $check = $db->prepare('SELECT id FROM t_cc_slots WHERE id = :id AND is_cc_plus = 0');
        $check->execute([':id' => $slot_id]);
        if (!$check->fetch()) {
            $db->rollBack();
            return false;
        }

        $db->prepare('DELETE FROM t_cc_bookings WHERE cc_slot_id = :slot_id')
           ->execute([':slot_id' => $slot_id]);

        $db->prepare('DELETE FROM t_cc_slots WHERE id = :slot_id')
           ->execute([':slot_id' => $slot_id]);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}