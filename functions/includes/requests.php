<?php
require_once __DIR__ . '/cc_bookings.php';

/**
 * 申請一覧の取得
 *
 * 返却データの構造
 * [
 *   [
 *     'request_id'       => 5,
 *     'type_id'          => 1,
 *     'type_name'        => 'cc+予約',
 *     'status_id'        => 1,
 *     'status_name'      => '新規',
 *     'status_label'     => '未解決',   // 新規・未対応:'未解決' / 承認・却下:'対応済み'
 *     'student_id'       => 11,
 *     'student_name'     => '山田太郎',
 *     'course_id'        => 2,
 *     'course_name'      => 'Webプログラミング科',
 *     'room_id'          => 3,
 *     'room_name'        => '6B',
 *     'course_full_name' => '6B/Webプログラミング科',
 *     'created_at'       => '2026-04-13 14:30:59',
 *   ],
 *   // ...
 * ]
 *
 * @param array $filters 絞り込み条件。各キーはスカラーまたは配列で指定可能。
 *   利用可能キー:
 *     status_id  (int|int[]) ステータスID。例: 1 または [1, 2]
 *     type_id    (int|int[]) 申請種別ID
 *     student_id (int|int[]) 生徒ID
 *     course_id  (int|int[]) コースID
 *
 * @return array 申請一覧（新着順）
 */
function get_cc_requests(array $filters = []): array
{
    $db = db_connect();

    $sql = 'SELECT
                req.id                            AS request_id,
                req.type_id                       AS type_id,
                rt.name                           AS type_name,
                req.status_id                     AS status_id,
                rs.name                           AS status_name,
                s.id                              AS student_id,
                CONCAT(s.last_name, s.first_name) AS student_name,
                c.id                              AS course_id,
                c.name                            AS course_name,
                r.id                              AS room_id,
                r.name                            AS room_name,
                CONCAT(r.name, "/", c.name)       AS course_full_name,
                req.created_at                    AS created_at
            FROM t_cc_requests req
            JOIN m_request_types  rt ON req.type_id    = rt.id
            JOIN m_request_status rs ON req.status_id  = rs.id
            JOIN m_students       s  ON req.student_id = s.id
            JOIN m_courses        c  ON s.course_id    = c.id
            JOIN m_rooms          r  ON c.room_id      = r.id';

    $filter_definition = [
        'status_id'  => 'req.status_id',
        'type_id'    => 'req.type_id',
        'student_id' => 'req.student_id',
        'course_id'  => 's.course_id',
    ];

    $where_clauses = [];
    $params        = [];

    foreach ($filter_definition as $key => $column) {
        if (!isset($filters[$key]) || $filters[$key] === '') {
            continue;
        }

        $value = $filters[$key];

        if (is_array($value)) {
            // 配列の場合はIN句を生成
            $placeholders = [];
            foreach ($value as $i => $v) {
                $ph             = ":{$key}_{$i}";
                $placeholders[] = $ph;
                $params[$ph]    = $v;
            }
            $where_clauses[] = "{$column} IN (" . implode(', ', $placeholders) . ")";
        } else {
            // スカラーの場合は通常の等値条件
            $where_clauses[]   = "{$column} = :{$key}";
            $params[":{$key}"] = $value;
        }
    }

    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }

    $sql .= ' ORDER BY req.created_at DESC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // status_labelをPHP側で付与（承認:3・却下:4 → 対応済み、それ以外 → 未解決）
    foreach ($rows as &$row) {
        $row['status_label'] = in_array((int) $row['status_id'], [3, 4])
            ? '対応済み'
            : '未解決';
    }
    unset($row);

    return $rows;
}

/**
 * キャリコンプラスの申請情報を登録
 *
 * t_cc_requests に1件INSERTする
 * status_id = 1（新規）で固定
 *
 * @param  PDO         $db           DB接続（トランザクション管理用に外部から受け取る）
 * @param  int         $type_id      申請種別ID（1:cc+予約 / 2:cc+変更 / 3:cc+キャンセル / 4:cc変更）
 * @param  int         $student_id   申請する生徒のID
 * @param  int         $booking_id_a 変更元の予約ID（予約申請時は新規予約ID）
 * @param  int|null    $booking_id_b 変更先の予約ID（変更申請時のみ指定。デフォルトnull）
 * @param  string|null $message      申請メッセージ（任意）
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
 * キャリコンプラスの予約申請
 *
 * 空きスロットの特定・t_cc_bookings登録・t_cc_requests登録をトランザクション内で一括実行する
 *
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
 * キャリコンプラスの予約変更申請
 *
 * 変更先の空きスロット特定・t_cc_bookings登録・t_cc_requests登録をトランザクション内で一括実行する
 * ※ 変更元の予約削除は管理者の承認後（approve_cc_plus_change）に行うため、この関数では実施しない
 *
 * @param  int         $student_id      申請する生徒のID
 * @param  int         $from_booking_id 変更元の予約ID（booking_id_a に設定）
 * @param  string      $date            変更先の予約日（Y-m-d形式）
 * @param  int         $time_id         変更先の時間ID
 * @param  int         $style_id        面談スタイルID
 * @param  string|null $message         申請メッセージ（任意）
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

        if ($cc_slot_id === false) {
            throw new Exception('空きスロットが見つかりません');
        }

        // 2. 変更先を t_cc_bookings に登録
        $to_booking_id = add_cc_booking($db, $student_id, (int) $cc_slot_id, $time_id, $style_id);

        // 3. t_cc_requests に変更申請を登録（booking_id_a=変更元、booking_id_b=変更先）
        add_cc_request($db, 2, $student_id, $from_booking_id, $to_booking_id, $message);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * キャリコンプラスのキャンセル申請
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
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 必須キャリコンの変更申請
 *
 * t_cc_requests に変更申請を登録する
 * 実際の入れ替えは管理者が swap_cc_bookings() で行う
 *
 * @param  int         $student_id   申請する生徒のID
 * @param  int         $booking_id_a 入れ替え対象の予約ID①（自分の予約）
 * @param  int         $booking_id_b 入れ替え対象の予約ID②（変更先の予約）
 * @param  string|null $message      申請メッセージ（任意）
 * @return bool        登録成功時はtrue、失敗時はfalse
 */
function request_cc_change(int $student_id, int $booking_id_a, int $booking_id_b, ?string $message = null): bool
{
    $db = db_connect();

    try {
        add_cc_request($db, 4, $student_id, $booking_id_a, $booking_id_b, $message);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * CC+変更申請の承認処理
 *
 * 変更元CC+仮予約（booking_id_a）と、それに紐づく確定済み通常予約を削除し、
 * 申請ステータスを承認済みに更新する
 * ※ 変更先CC+仮予約（booking_id_b）はそのまま残り、新しい仮予約として有効になる
 *
 * @param  int  $request_id 承認する t_cc_requests の ID
 * @return bool 成功時 true、失敗時 false
 */
function approve_cc_plus_change(int $request_id): bool
{
    $db = db_connect();

    try {
        $db->beginTransaction();

        // 申請情報を取得（type_id=2:CC+変更 かつ 未処理のみ承認可能）
        $req_stmt = $db->prepare(
            'SELECT booking_id_a, booking_id_b
             FROM t_cc_requests
             WHERE id = :request_id
               AND type_id   = 2
               AND status_id = 1'
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
            'DELETE FROM t_cc_bookings WHERE cc_plus_booking_id = :booking_id_a'
        )->execute([':booking_id_a' => $booking_id_a]);

        // 2. 変更元CC+仮予約を削除
        $db->prepare(
            'DELETE FROM t_cc_bookings
             WHERE id = :booking_id_a
               AND cc_plus_booking_id IS NULL'
        )->execute([':booking_id_a' => $booking_id_a]);

        // 3. 申請ステータスを承認済みに更新
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
 * CC+変更申請の却下処理
 *
 * 変更先CC+仮予約（booking_id_b）を削除し、変更元は現状維持のまま
 * 申請ステータスを却下に更新する
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
               AND type_id   = 2
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
            'UPDATE t_cc_requests SET status_id = 4 WHERE id = :request_id'
        )->execute([':request_id' => $request_id]);

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * 必須CC変更申請の確認画面用データ取得
 *
 * 2つの予約のそれぞれの生徒情報と入れ替え後の日時を返す
 *
 * 返却データの構造
 * [
 *   'my_self' => [
 *     'course_name'   => 'Webプログラミング科',
 *     'student_name'  => '山田太郎',
 *     'from_datetime' => '2026-05-10 10:00',  // 現在の日時
 *     'to_datetime'   => '2026-06-07 11:00',  // 変更後の日時
 *   ],
 *   'target' => [
 *     'course_name'   => 'Webプログラミング科',
 *     'student_name'  => '鈴木花子',
 *     'from_datetime' => '2026-06-07 11:00',
 *     'to_datetime'   => '2026-05-10 10:00',
 *   ],
 * ]
 *
 * @param int $booking_id_a 申請者側の予約ID
 * @param int $booking_id_b 相手側の予約ID
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

    if (count($rows) !== 2) {
        return [];
    }

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

// ============================================================
// 申請詳細取得
// ============================================================

/**
 * 申請詳細の取得
 *
 * request_idから申請の詳細情報を取得する。
 * 共通情報（申請者・コース・申請日時等）に加え、申請タイプに応じた
 * 予約詳細を 'detail' キー以下に格納して返す。
 *
 * 返却データの構造（共通部分）
 * [
 *   'request_id'       => 5,
 *   'type_id'          => 1,
 *   'type_name'        => 'cc+予約',
 *   'status_id'        => 1,
 *   'status_name'      => '新規',
 *   'status_label'     => '未解決',   // 新規・未対応:'未解決' / 承認・却下:'対応済み'
 *   'student_id'       => 11,
 *   'student_name'     => '山田太郎',
 *   'course_id'        => 2,
 *   'course_name'      => 'Webプログラミング科',
 *   'room_id'          => 3,
 *   'room_name'        => '6B',
 *   'course_full_name' => '6B/Webプログラミング科',
 *   'created_at'       => '2026-04-13 14:30:59',
 *   'message'          => '申請メッセージ',  // 任意。未入力時はnull
 *   'booking_id_a'     => 10,   // 承認・却下処理用。常に含まれる
 *   'booking_id_b'     => 15,   // type 2・4のみ値あり。それ以外はnull
 *   'detail'           => [...], // タイプ別の予約詳細（下記参照）
 * ]
 *
 * --- detail の構造（type 1: CC+新規予約） ---
 * 'detail' => [
 *   'booking_id' => 10,
 *   'cc_date'    => '2026-05-10',
 *   'cc_time'    => '10時～',
 *   'style_id'   => 1,
 *   'style_name' => 'ZOOM',
 * ]
 *
 * --- detail の構造（type 2: CC+変更申請） ---
 * 'detail' => [
 *   'before' => ['booking_id'=>10, 'cc_date'=>'2026-05-10', 'cc_time'=>'10時～', 'style_id'=>1, 'style_name'=>'ZOOM'],
 *   'after'  => ['booking_id'=>15, 'cc_date'=>'2026-06-07', 'cc_time'=>'14時～', 'style_id'=>2, 'style_name'=>'対面'],
 * ]
 *
 * --- detail の構造（type 3: CC+キャンセル申請） ---
 * type 1 と同一構造
 *
 * --- detail の構造（type 4: 必須CC変更申請） ---
 * 'detail' => [
 *   'my_self' => [
 *     'booking_id'   => 10,
 *     'from_cc_date' => '2026-05-10',  // 現在の日付
 *     'from_cc_time' => '10時～',      // 現在の時間
 *     'to_cc_date'   => '2026-06-07',  // 入れ替え後（=相手の現在）
 *     'to_cc_time'   => '14時～',
 *   ],
 *   'target' => [
 *     'booking_id'       => 15,
 *     'student_name'     => '鈴木花子',
 *     'course_full_name' => '6B/Webプログラミング科',
 *     'from_cc_date'     => '2026-06-07',
 *     'from_cc_time'     => '14時～',
 *     'to_cc_date'       => '2026-05-10',  // 入れ替え後（=申請者の現在）
 *     'to_cc_time'       => '10時～',
 *   ],
 * ]
 *
 * @param int $request_id 取得する申請のID
 * @return array 申請詳細。該当なしの場合は空配列
 */
function get_cc_request_detail(int $request_id): array
{
    $db = db_connect();

    // 1. 共通情報を取得
    $sql = 'SELECT
                req.id                            AS request_id,
                req.type_id                       AS type_id,
                rt.name                           AS type_name,
                req.status_id                     AS status_id,
                rs.name                           AS status_name,
                s.id                              AS student_id,
                CONCAT(s.last_name, s.first_name) AS student_name,
                c.id                              AS course_id,
                c.name                            AS course_name,
                r.id                              AS room_id,
                r.name                            AS room_name,
                CONCAT(r.name, "/", c.name)       AS course_full_name,
                req.created_at                    AS created_at,
                req.message                       AS message,
                req.booking_id_a                  AS booking_id_a,
                req.booking_id_b                  AS booking_id_b
            FROM t_cc_requests req
            JOIN m_request_types  rt ON req.type_id    = rt.id
            JOIN m_request_status rs ON req.status_id  = rs.id
            JOIN m_students       s  ON req.student_id = s.id
            JOIN m_courses        c  ON s.course_id    = c.id
            JOIN m_rooms          r  ON c.room_id      = r.id
            WHERE req.id = :request_id';

    $stmt = $db->prepare($sql);
    $stmt->execute([':request_id' => $request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        return [];
    }

    // status_label を付与
    $request['status_label'] = in_array((int) $request['status_id'], [3, 4])
        ? '対応済み'
        : '未解決';

    // 2. タイプ別詳細を取得して 'detail' キーに格納
    $type_id = (int) $request['type_id'];

    $request['detail'] = match ($type_id) {
        1, 3    => _fetch_cc_plus_single_detail($db, $request),
        2       => _fetch_cc_plus_change_detail($db, $request),
        4       => _fetch_cc_change_detail($db, $request),
        default => [],
    };

    return $request;
}

/**
 * CC+新規予約・キャンセル申請の予約詳細を取得（type 1・3 共用）
 *
 * booking_id_a に紐づく予約の日付・時間・面談方法を返す
 *
 * @param PDO   $db      DB接続
 * @param array $request 共通情報（booking_id_a を含む）
 * @return array 予約詳細。予約が取得できない場合は空配列
 */
function _fetch_cc_plus_single_detail(PDO $db, array $request): array
{
    if (!$request['booking_id_a']) {
        return [];
    }

    $sql = 'SELECT
                b.id           AS booking_id,
                sl.date        AS cc_date,
                t.display_name AS cc_time,
                ms.id          AS style_id,
                ms.name        AS style_name
            FROM t_cc_bookings b
            JOIN t_cc_slots       sl ON b.cc_slot_id = sl.id
            JOIN m_times          t  ON b.time_id    = t.id
            JOIN m_meating_styles ms ON b.style_id   = ms.id
            WHERE b.id = :booking_id';

    $stmt = $db->prepare($sql);
    $stmt->execute([':booking_id' => $request['booking_id_a']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: [];
}

/**
 * CC+変更申請の予約詳細を取得（type 2）
 *
 * booking_id_a（変更前）と booking_id_b（変更後）の2件を取得して
 * 'before' / 'after' のキーで返す
 *
 * @param PDO   $db      DB接続
 * @param array $request 共通情報（booking_id_a・booking_id_b を含む）
 * @return array ['before' => [...], 'after' => [...]]。取得できない場合は空配列
 */
function _fetch_cc_plus_change_detail(PDO $db, array $request): array
{
    if (!$request['booking_id_a'] || !$request['booking_id_b']) {
        return [];
    }

    $sql = 'SELECT
                b.id           AS booking_id,
                sl.date        AS cc_date,
                t.display_name AS cc_time,
                ms.id          AS style_id,
                ms.name        AS style_name
            FROM t_cc_bookings b
            JOIN t_cc_slots       sl ON b.cc_slot_id = sl.id
            JOIN m_times          t  ON b.time_id    = t.id
            JOIN m_meating_styles ms ON b.style_id   = ms.id
            WHERE b.id IN (:booking_id_a, :booking_id_b)';

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':booking_id_a' => $request['booking_id_a'],
        ':booking_id_b' => $request['booking_id_b'],
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) !== 2) {
        return [];
    }

    $bookings = array_column($rows, null, 'booking_id');

    return [
        'before' => $bookings[$request['booking_id_a']],
        'after'  => $bookings[$request['booking_id_b']],
    ];
}

/**
 * 必須CC変更申請の予約詳細を取得（type 4）
 *
 * booking_id_a（申請者）と booking_id_b（相手）の2件を取得し、
 * 入れ替え後の日時を計算した上で 'my_self' / 'target' のキーで返す
 * 相手の生徒名・コース情報も含む
 *
 * @param PDO   $db      DB接続
 * @param array $request 共通情報（booking_id_a・booking_id_b を含む）
 * @return array ['my_self' => [...], 'target' => [...]]。取得できない場合は空配列
 */
function _fetch_cc_change_detail(PDO $db, array $request): array
{
    if (!$request['booking_id_a'] || !$request['booking_id_b']) {
        return [];
    }

    $sql = 'SELECT
                b.id                              AS booking_id,
                s.id                              AS student_id,
                CONCAT(s.last_name, s.first_name) AS student_name,
                CONCAT(r.name, "/", c.name)       AS course_full_name,
                sl.date                           AS cc_date,
                t.display_name                    AS cc_time
            FROM t_cc_bookings b
            JOIN m_students       s  ON b.student_id = s.id
            JOIN m_courses        c  ON s.course_id  = c.id
            JOIN m_rooms          r  ON c.room_id    = r.id
            JOIN t_cc_slots       sl ON b.cc_slot_id = sl.id
            JOIN m_times          t  ON b.time_id    = t.id
            WHERE b.id IN (:booking_id_a, :booking_id_b)';

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':booking_id_a' => $request['booking_id_a'],
        ':booking_id_b' => $request['booking_id_b'],
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) !== 2) {
        return [];
    }

    $bookings = array_column($rows, null, 'booking_id');
    $a = $bookings[$request['booking_id_a']]; // 申請者
    $b = $bookings[$request['booking_id_b']]; // 相手

    return [
        'my_self' => [
            'booking_id'   => $a['booking_id'],
            'from_cc_date' => $a['cc_date'],
            'from_cc_time' => $a['cc_time'],
            'to_cc_date'   => $b['cc_date'],
            'to_cc_time'   => $b['cc_time'],
        ],
        'target' => [
            'booking_id'       => $b['booking_id'],
            'student_name'     => $b['student_name'],
            'course_full_name' => $b['course_full_name'],
            'from_cc_date'     => $b['cc_date'],
            'from_cc_time'     => $b['cc_time'],
            'to_cc_date'       => $a['cc_date'],
            'to_cc_time'       => $a['cc_time'],
        ],
    ];
}

/**
 * 未解決申請の存在確認
 *
 * status_id が 新規（1）または未対応（2）の申請が1件以上存在すれば true を返す
 * 通知バッジ等の表示制御用途を想定
 *
 * @return bool 未解決申請が存在する場合 true、すべて解決済みまたは申請がない場合 false
 */
function has_unresolved_cc_requests(): bool
{
    $db = db_connect();

    $stmt = $db->query(
        'SELECT EXISTS (
            SELECT 1 FROM t_cc_requests
            WHERE status_id IN (1, 2)
        )'
    );

    return (bool) $stmt->fetchColumn();
}

// -------------------------------------------------------
// type 1: CC+予約申請
// -------------------------------------------------------

/**
 * CC+予約申請の承認処理
 * ステータスを承認（3）に更新する。予約はそのまま残す。
 */
function approve_cc_plus(int $request_id): bool
{
    $db = db_connect();
    try {
        $stmt = $db->prepare(
            'UPDATE t_cc_requests
             SET status_id = 3
             WHERE id = :id AND type_id = 1 AND status_id IN (1, 2)'
        );
        $stmt->execute([':id' => $request_id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * CC+予約申請の却下処理
 * 予約を削除してステータスを却下（4）に更新する。
 */
function reject_cc_plus(int $request_id): bool
{
    $db = db_connect();
    try {
        $db->beginTransaction();

        $stmt = $db->prepare(
            'SELECT booking_id_a FROM t_cc_requests
             WHERE id = :id AND type_id = 1 AND status_id IN (1, 2)'
        );
        $stmt->execute([':id' => $request_id]);
        $request = $stmt->fetch();

        if (!$request || !$request['booking_id_a']) {
            throw new Exception('対象の申請が見つかりません');
        }

        $db->prepare('DELETE FROM t_cc_bookings WHERE id = :booking_id')
           ->execute([':booking_id' => $request['booking_id_a']]);

        $db->prepare('UPDATE t_cc_requests SET status_id = 4 WHERE id = :id')
           ->execute([':id' => $request_id]);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

// -------------------------------------------------------
// type 3: CC+キャンセル申請
// -------------------------------------------------------

/**
 * CC+キャンセル申請の承認処理
 * 予約を削除してステータスを承認（3）に更新する。
 */
function approve_cc_plus_cancel(int $request_id): bool
{
    $db = db_connect();
    try {
        $db->beginTransaction();

        $stmt = $db->prepare(
            'SELECT booking_id_a FROM t_cc_requests
             WHERE id = :id AND type_id = 3 AND status_id IN (1, 2)'
        );
        $stmt->execute([':id' => $request_id]);
        $request = $stmt->fetch();

        if (!$request || !$request['booking_id_a']) {
            throw new Exception('対象の申請が見つかりません');
        }

        $db->prepare('DELETE FROM t_cc_bookings WHERE id = :booking_id')
           ->execute([':booking_id' => $request['booking_id_a']]);

        $db->prepare('UPDATE t_cc_requests SET status_id = 3 WHERE id = :id')
           ->execute([':id' => $request_id]);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * CC+キャンセル申請の却下処理
 * ステータスを却下（4）に更新する。予約はそのまま残す。
 */
function reject_cc_plus_cancel(int $request_id): bool
{
    $db = db_connect();
    try {
        $stmt = $db->prepare(
            'UPDATE t_cc_requests
             SET status_id = 4
             WHERE id = :id AND type_id = 3 AND status_id IN (1, 2)'
        );
        $stmt->execute([':id' => $request_id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// -------------------------------------------------------
// type 4: 必須CC変更申請
// -------------------------------------------------------

/**
 * 必須CC変更申請の承認処理
 * swap_cc_bookings() で2件の予約を入れ替え、ステータスを承認（3）に更新する。
 * ※ swap_cc_bookings() は内部でトランザクションを持つため、ここでは分離して呼び出す
 */
function approve_cc_change(int $request_id): bool
{
    $db = db_connect();
    try {
        $stmt = $db->prepare(
            'SELECT booking_id_a, booking_id_b FROM t_cc_requests
             WHERE id = :id AND type_id = 4 AND status_id IN (1, 2)'
        );
        $stmt->execute([':id' => $request_id]);
        $request = $stmt->fetch();

        if (!$request || !$request['booking_id_a'] || !$request['booking_id_b']) {
            return false;
        }

        if (!swap_cc_bookings($request['booking_id_a'], $request['booking_id_b'])) {
            return false;
        }

        $db->prepare('UPDATE t_cc_requests SET status_id = 3 WHERE id = :id')
           ->execute([':id' => $request_id]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 必須CC変更申請の却下処理
 * ステータスを却下（4）に更新する。予約はそのまま残す。
 */
function reject_cc_change(int $request_id): bool
{
    $db = db_connect();
    try {
        $stmt = $db->prepare(
            'UPDATE t_cc_requests
             SET status_id = 4
             WHERE id = :id AND type_id = 4 AND status_id IN (1, 2)'
        );
        $stmt->execute([':id' => $request_id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}