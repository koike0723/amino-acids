<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/cc_slots.php';

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


/**
 * 指定コース・回数の必須キャリコン予約を日付・時間でグループ化して返す
 * CC+から確定した通常予約（cc_plus_booking_id IS NOT NULL）は除外する
 *
 * 返却構造:
 * [
 *   '2026-01-01' => [
 *     '10:00' => [
 *       ['booking_id' => 1, 'student_id' => 3, 'student_name' => '山田太郎'],
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
                s.id                                 AS student_id,
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
            'student_id' => $row['student_id'],
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

