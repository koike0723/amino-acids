# functions.php リファレンス

`functions.php` は `require_once` のみを記述したエントリーポイントです。  
実装は `includes/` 配下の5ファイルに分割されています。

```
includes/
├── db.php          # DB接続・ユーティリティ
├── students.php    # 生徒管理
├── courses.php     # コース管理
├── cc_slots.php    # CC枠管理
└── cc_bookings.php # CC予約・申請管理
```

---

## 目次

**DB接続・ユーティリティ（db.php）**
| 関数名 | 説明 |
|---|---|
| [`db_connect()`](#db_connect) | PDO接続を生成して返す |
| [`h($str)`](#hstr) | XSS対策のHTMLエスケープ |
| [`format_japanese_date($date)`](#format_japanese_datedate) | `Y-m-d` 形式を `〇年〇月〇日` に変換 |
| [`check($str)`](#checkstr) | デバッグ用 `var_dump` ラッパー |

**生徒管理（students.php）**
| 関数名 | 説明 |
|---|---|
| [`student_login($login_id, $password)`](#student_loginlogin_id-password) | ログイン処理。成功時にセッションを設定 |
| [`get_students($filters, $is_display_end)`](#get_studentsfilters--is_display_end) | 生徒一覧を取得。フィルタ・終了済み表示に対応 |
| [`get_student($student_id)`](#get_studentstudent_id) | 生徒IDから詳細情報と予約一覧を取得 |
| [`add_students($course_id, $students)`](#add_studentscourse_id-students) | 生徒を一括登録。ログインIDを自動生成 |
| [`update_student($student_id, $data)`](#update_studentint-student_id-array-data-bool) | 生徒情報を部分更新 |

**コース管理（courses.php）**
| 関数名 | 説明 |
|---|---|
| [`get_courses($target_date, ...)`](#get_coursestarget_date-is_display_not_start-room_id-category_id) | コース一覧を取得。日付・教室・カテゴリで絞り込み可能 |
| [`get_course($course_id)`](#get_coursecourse_id) | コースIDから詳細情報と必須CCスケジュールを取得 |
| [`add_course($course)`](#add_coursecourse) | コースを登録。必須CCスケジュールも同時登録可能 |
| [`update_course($course_id, $data)`](#update_courseint-course_id-array-data-bool) | コース情報を部分更新。CCスケジュール再設定にも対応 |
| [`get_course_cc_schedules($course_id)`](#get_course_cc_schedulescourse_id) | コースの必須CCスケジュール（回数と日付の対応）を取得 |
| [`add_course_cc_schedules($course_id, $cc_schedules)`](#add_course_cc_schedulescourse_id-cc_schedules) | 必須CCスケジュールを登録 |

**CC枠管理（cc_slots.php）**
| 関数名 | 説明 |
|---|---|
| [`CC_SLOT_TYPE`](#cc_slot_type-enum) | CC枠の種別を表すenum（`All` / `Line` / `CcPlus`） |
| [`get_cc_slots($cc_type, $target_date)`](#get_cc_slotscc_type-target_date) | CC枠一覧を取得。種別・日付で絞り込み可能 |
| [`add_cc_slot($date, $is_cc_plus)`](#add_cc_slotdate-is_cc_plus--false-int) | CC枠を1件登録してスロットIDを返す |
| [`get_cc_plus_dates($base_date)`](#get_cc_plus_datesstring-base_date--null-array) | CC+開催日の一覧を重複なしで取得 |

**CC予約・申請管理（cc_bookings.php）**
| 関数名 | 説明 |
|---|---|
| [`get_cc_bookings($filters)`](#get_cc_bookingsarray-filters---array) | CC予約一覧を `[slot_id][start_time]` の階層構造で取得 |
| [`get_cc_booking($booking_id)`](#get_cc_bookingint-booking_id-array) | 予約IDから予約を1件取得 |
| [`swap_cc_bookings($booking_id_a, $booking_id_b)`](#swap_cc_bookingsbooking_id_a-booking_id_b) | 2件の予約の日時を入れ替える（管理者による穴埋め調整用） |
| [`get_cc_plus_time_table($date)`](#get_cc_plus_time_tablestring-date-array) | 指定日のCC+枠の時間ごとの空き状況を取得 |
| [`add_cc_booking($db, ...)`](#add_cc_bookingpdo-db---int-⚠️-内部関数) | ⚠️ 内部関数。予約を1件INSERT |
| [`add_cc_request($db, ...)`](#add_cc_requestpdo-db---void-⚠️-内部関数) | ⚠️ 内部関数。申請を1件INSERT |
| [`book_cc_plus($student_id, ...)`](#book_cc_plusint-student_id-string-date-int-time_id-int-style_id-string-message-bool) | CC+枠の新規予約申請（空き確認・予約・申請を一括処理） |
| [`book_cc_plus_change($student_id, ...)`](#book_cc_plus_changeint-student_id-int-from_booking_id-string-date-int-time_id-int-style_id-string-message-bool) | CC+予約の変更申請（変更先の仮予約を作成） |
| [`book_cc_plus_cancel($student_id, ...)`](#book_cc_plus_cancelint-student_id-int-booking_id-string-message-bool) | CC+予約のキャンセル申請を登録 |
| [`request_cc_change($student_id, ...)`](#request_cc_changeint-student_id-int-booking_id_a-int-booking_id_b-string-message-bool) | 必須CC予約の変更申請（生徒間の入れ替え申請）を登録 |
| [`approve_cc_plus_change($request_id)`](#approve_cc_plus_changeint-request_id-bool) | CC+変更申請を承認し、変更元の予約を削除 |
| [`reject_cc_plus_change($request_id)`](#reject_cc_plus_changeint-request_id-bool) | CC+変更申請を却下し、変更先の仮予約を削除 |
| [`bulk_book_cc($course_id)`](#bulk_book_ccint-course_id-bool) | 指定コースの全生徒に必須CC予約を一括登録 |
| [`get_course_cc_bookings($course_id, $cc_count)`](#get_course_cc_bookingsint-course_id-int-cc_count-array) | 必須CC予約を日付・時間・予約の階層構造で取得 |
| [`get_course_cc_bookings_by_student($student_id, $date)`](#get_course_cc_bookings_by_studentint-student_id-string-date-array) | 生徒IDと日付から必須CC予約一覧を取得するラッパー |
| [`get_cc_change_confirm($booking_id_a, $booking_id_b)`](#get_cc_change_confirmint-booking_id_a-int-booking_id_b-array) | CC変更申請の確認画面用データ（双方の生徒情報・入れ替え日時）を取得 |

---

## DB接続・ユーティリティ（db.php）

### `db_connect()`
PDOオブジェクトを生成して返す。各関数内で呼び出して使用する。

```php
$db = db_connect();
```

| | |
|---|---|
| 戻り値 | `PDO` |

---

### `h($str)`
XSS対策用のHTMLエスケープ。出力時に必ず使用する。

```php
echo h($user_input);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$str` | `string` | エスケープする文字列 |

| | |
|---|---|
| 戻り値 | `string` エスケープ済み文字列 |

---

### `format_japanese_date($date)`
`2026-01-01` 形式の日付を `2026年1月1日` に変換する。

```php
echo format_japanese_date('2026-04-15'); // → "2026年4月15日"
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$date` | `string` | `Y-m-d` 形式の日付文字列 |

| | |
|---|---|
| 戻り値 | `string` 日本語形式の日付。空・不正な値の場合は空文字 |

---

### `check($str)`
デバッグ用の `var_dump` ラッパー。**本番環境では使用しないこと。**

```php
check($some_array);
```

---

## 生徒管理（students.php）

### `student_login($login_id, $password)`
ログイン処理。成功時はセッションに `id`・`student_name`・`res_message` を設定する。

```php
$result = student_login('202604_6A01', 'password');
// $_SESSION['id'], $_SESSION['student_name'] にセットされる
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$login_id` | `string` | ログインID |
| `$password` | `string` | パスワード（平文） |

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true`、失敗時 `false` |

**セッションに設定される値**

| キー | 内容 |
|---|---|
| `$_SESSION['id']` | 生徒ID |
| `$_SESSION['student_name']` | 姓名（結合済み） |
| `$_SESSION['res_message']` | `['status_code' => 1/0, 'msg' => '...']` |

---

### `get_students($filters = [], $is_display_end = false)`
生徒一覧を取得する。フィルタなしで呼ぶと訓練期間中の全生徒を返す。

```php
// コースで絞り込み
$students = get_students(['course_id' => 3]);

// 訓練終了済みも含めて全取得
$all = get_students([], true);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$filters` | `array` | 絞り込み条件（後述） |
| `$is_display_end` | `bool` | 訓練終了済みを含むか（デフォルト: `false`） |

**`$filters` に指定できるキー**

| キー | 説明 |
|---|---|
| `course_id` | コースIDで絞り込み |
| `status_id` | ステータスIDで絞り込み |
| `number` | 出席番号で絞り込み |

| | |
|---|---|
| 戻り値 | `array[]` 生徒情報の配列（下記構造） |

**返却データ構造**
```php
[
    'student_id'   => 1,
    'student_name' => '山田太郎',
    'number'       => 1,
    'status_name'  => '在校中',
    'course_name'  => 'Webプログラミング科',
    'end_date'     => '2026-09-30',
    'room_name'    => '6B',
]
```

---

### `get_student($student_id)`
生徒IDから1名の詳細情報と予約一覧を取得する。

```php
$student = get_student(1);
foreach ($student['bookings'] as $booking) {
    echo $booking['cc_date'];
}
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$student_id` | `int` | 取得する生徒のID |

| | |
|---|---|
| 戻り値 | `array` 生徒情報（下記構造）。該当なしの場合は空配列 |

**返却データ構造**
```php
[
    'student_id'   => 1,
    'student_name' => '山田太郎',
    'number'       => 1,
    'status_id'    => 1,
    'status_name'  => '在校中',
    'course_id'    => 2,
    'course_name'  => 'Webプログラミング科',
    'room_name'    => '6B',
    'bookings'     => [
        [
            'booking_id'      => 10,
            'cc_slot_id'      => 5,
            'is_cc_plus'      => false,   // true = CC+枠
            'cc_consultant'   => '田中花子',
            'cc_room'         => '相談室A',
            'cc_date'         => '2026-05-10',
            'cc_time'         => '10:00:00',
            'cc_display_time' => '10時～',
            'cc_style_id'     => 1,
            'cc_style_name'   => 'ZOOM',
        ],
        // ...
    ],
]
```

> **注意:** `bookings` には必須CC枠とCC+枠のみ含まれる（CC+から確定した通常予約は除外）。

---

### `add_students($course_id, $students)`
生徒を一括登録する。パスワードは `password` で固定、ログインIDはコース開始年月・教室名・出席番号から自動生成される。

```php
add_students(3, [
    ['first_name' => '太郎', 'last_name' => '山田', 'number' => 1],
    ['first_name' => '花子', 'last_name' => '鈴木', 'number' => 2],
]);
// ログインID例: "202604_6B01", "202604_6B02"
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course_id` | `int` | 登録先コースのID |
| `$students` | `array[]` | 生徒データの配列（`first_name`, `last_name`, `number` を含む連想配列） |

---

### `update_student(int $student_id, array $data): bool`
生徒情報を部分更新する。渡したキーのみ更新される。

```php
// ステータスだけ変更
update_student(1, ['status_id' => 2]);

// 複数カラムを同時変更
update_student(1, ['first_name' => '二郎', 'course_id' => 4]);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$student_id` | `int` | 更新対象の生徒ID |
| `$data` | `array` | 更新するカラムと値（更新可能: `first_name`, `last_name`, `number`, `status_id`, `course_id`） |

| | |
|---|---|
| 戻り値 | `bool` 1件以上更新された場合 `true` |

> **注意:** ステータス変更時に予約の自動削除は行わない。表示側で `status_id` を参照してアラート等を表示すること。

---

## コース管理（courses.php）

### `get_courses($target_date, $is_display_not_start, $room_id, $category_id)`
コース一覧を取得する。デフォルトは本日時点で開講中のコースのみ。

```php
// 本日開講中の全コース
$courses = get_courses();

// 求職者訓練コース（category_id=1）のみ
$courses = get_courses(null, false, null, 1);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$target_date` | `string\|null` | 基準日（デフォルト: 今日） |
| `$is_display_not_start` | `bool` | 未開始コースを含むか（デフォルト: `false`） |
| `$room_id` | `int\|null` | 教室IDで絞り込み |
| `$category_id` | `int\|null` | カテゴリIDで絞り込み |

| | |
|---|---|
| 戻り値 | `array[]` コース一覧（`course_id`, `course_name`, `start_date`, `end_date`, `room_name`, `category_name`） |

---

### `get_course($course_id)`
コースIDから詳細情報と必須CC日程を取得する。

```php
$course = get_course(2);
// 第1回の日程一覧
foreach ($course['cc'][1] as $date) {
    echo $date; // "2026-05-10"
}
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course_id` | `int` | 取得するコースのID |

| | |
|---|---|
| 戻り値 | `array` コース情報（下記構造）。 |

**返却データ構造**
```php
[
    'course_id'     => 2,
    'course_name'   => 'Webプログラミング科',
    'start_date'    => '2026-04-01',
    'end_date'      => '2026-09-30',
    'room_id'       => 1,
    'room_name'     => '6B',
    'category_id'   => 1,
    'category_name' => '求職者訓練',
    'cc'            => [
        1 => ['2026-05-10', '2026-05-17'],  // 第1回の実施日
        2 => ['2026-07-12'],                // 第2回の実施日
    ],
]
```

---

### `add_course($course)`
コースを1件登録する。`cc` キーがある場合は必須CCスケジュールも同時登録される。

```php
add_course([
    'name'        => 'Webプログラミング科',
    'start_date'  => '2026-04-01',
    'end_date'    => '2026-09-30',
    'room_id'     => 1,
    'category_id' => 1,
    'cc'          => [
        1 => ['2026-05-10', '2026-05-17'],
        2 => ['2026-07-12'],
    ],
]);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course` | `array` | コースデータ（`name`, `start_date`, `end_date`, `room_id`, `category_id`, `cc`(任意)） |

---

### `update_course(int $course_id, array $data): bool`
コース情報を部分更新する。`cc` キーが含まれる場合はスケジュールを全削除して再登録する。  
削除された日付に対応する予約も自動削除される。

```php
// コース名だけ変更
update_course(2, ['name' => '新コース名']);

// スケジュールを再設定（削除された日付の予約は自動削除）
update_course(2, [
    'cc' => [
        1 => ['2026-05-10', '2026-05-17'],
        2 => ['2026-07-12'],
    ],
]);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course_id` | `int` | 更新対象のコースID |
| `$data` | `array` | 更新データ（更新可能: `name`, `start_date`, `end_date`, `room_id`, `category_id`, `cc`） |

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true` |

---

### `get_course_cc_schedules($course_id)`
コースの必須CCスケジュール（回数と日付の対応）を取得する。

```php
$schedules = get_course_cc_schedules(2);
// [1 => ['2026-05-10', '2026-05-17'], 2 => ['2026-07-12']]
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course_id` | `int` | コースID |

| | |
|---|---|
| 戻り値 | `array` `[cc_count => [date, ...], ...]` の形式 |

---

### `add_course_cc_schedules($course_id, $cc_schedules)`
必須CCスケジュールを登録する。`add_course` や `update_course` から内部的に呼ばれる。

```php
add_course_cc_schedules(2, [
    1 => ['2026-05-10', '2026-05-17'],
    2 => ['2026-07-12'],
]);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course_id` | `int` | コースID |
| `$cc_schedules` | `array` | `[cc_count => [date, ...], ...]` 形式のスケジュール |

---

## CC枠管理（cc_slots.php）

### `CC_SLOT_TYPE` (enum)
`get_cc_slots()` に渡す枠種別の列挙型。

| 値 | 説明 |
|---|---|
| `CC_SLOT_TYPE::All` | 全ての枠 |
| `CC_SLOT_TYPE::Line` | 必須CC枠（デフォルト） |
| `CC_SLOT_TYPE::CcPlus` | CC+枠 |

---

### `get_cc_slots($cc_type, $target_date)`
CC枠一覧を取得する。

```php
// CC+枠のみ取得
$slots = get_cc_slots(CC_SLOT_TYPE::CcPlus->name);

// 特定日の全枠を取得
$slots = get_cc_slots(CC_SLOT_TYPE::All->name, '2026-05-10');
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$cc_type` | `string` | `CC_SLOT_TYPE` の `name`（デフォルト: `Line`） |
| `$target_date` | `string\|null` | 絞り込む日付（デフォルト: 全日程） |

| | |
|---|---|
| 戻り値 | `array[]` 枠情報（`cc_date`, `is_cc_plus`, `consultant_name`, `room_name`） |

> **注意:** `consultant_name` と `room_name` は `null` の可能性があるため、使用時はnullチェックを行うこと。

---

### `add_cc_slot($date, $is_cc_plus = false): int`
CC枠を1件登録し、採番されたスロットIDを返す。

```php
$slot_id = add_cc_slot('2026-05-10');           // 必須CC枠
$slot_id = add_cc_slot('2026-05-10', true);     // CC+枠
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$date` | `string` | 開催日（`Y-m-d` 形式） |
| `$is_cc_plus` | `bool` | CC+枠かどうか（デフォルト: `false`） |

| | |
|---|---|
| 戻り値 | `int` 採番されたスロットID |

---

### `get_cc_plus_dates(?string $base_date = null): array`
CC+枠の開催日一覧を重複なしで取得する。

```php
// 今日以降の開催日
$dates = get_cc_plus_dates();

// 特定日以降
$dates = get_cc_plus_dates('2026-06-01');
// [['cc_date' => '2026-07-05'], ['cc_date' => '2026-08-02'], ...]
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$base_date` | `string\|null` | この日より後の日程を取得（デフォルト: 今日） |

| | |
|---|---|
| 戻り値 | `array[]` `[['cc_date' => 'Y-m-d'], ...]` の配列 |

---

## CC予約・申請管理（cc_bookings.php）

### 申請種別 (`type_id`) 一覧

| `type_id` | 説明 |
|---|---|
| 1 | CC+予約申請 |
| 2 | CC+変更申請 |
| 3 | CC+キャンセル申請 |
| 4 | 必須CC変更申請 |

---

### `get_cc_bookings(array $filters = []): array`
CC予約一覧を `[slot_id][start_time]` の階層構造で取得する。

```php
// 特定日の予約一覧
$bookings = get_cc_bookings(['slot_date' => '2026-05-10']);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$filters` | `array` | 絞り込み条件（`booking_id`, `student_id`, `slot_date`, `course_id`） |

**返却データ構造**
```php
[
    5 /* slot_id */ => [
        'cc_date' => '2026-05-10',
        '10:00:00' /* start_time */ => [
            'display_name' => '10時～',
            'bookings' => [
                [
                    'booking_id'   => 10,
                    'student_id'   => 1,
                    'student_name' => '山田太郎',
                    'course_id'    => 2,
                    'course_data'  => '6B/Webプログラミング科',
                    'style_id'     => 1,
                    'style_name'   => 'ZOOM',
                ],
            ],
        ],
    ],
]
```

---

### `get_cc_booking(int $booking_id): array`
予約IDから予約を1件取得する。

```php
$booking = get_cc_booking(10);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$booking_id` | `int` | 予約ID |

| | |
|---|---|
| 戻り値 | `array` 予約情報（`booking_id`, `student_id`, `student_name`, `course_id`, `course_data`, `cc_date`, `start_time`, `style_id`, `style_name`）。該当なしは空配列 |

---

### `swap_cc_bookings($booking_id_a, $booking_id_b)`
2件の予約の `cc_slot_id` と `time_id` を入れ替える（管理者による穴埋め調整用）。  
トランザクション処理で安全に実行される。

```php
$result = swap_cc_bookings(10, 15);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$booking_id_a` | `int` | 入れ替え対象の予約ID① |
| `$booking_id_b` | `int` | 入れ替え対象の予約ID② |

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true`、失敗時 `false` |

---

### `get_cc_plus_time_table(string $date): array`
指定日のCC+枠について、時間ごとの空き状況を返す。スロットが複数ある場合、1つでも空きがあれば `true`。

```php
$timetable = get_cc_plus_time_table('2026-05-10');
// [1 => true, 2 => false, 3 => true, ...]  (time_id => 空きあり/なし)
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$date` | `string` | 対象日付（`Y-m-d` 形式） |

| | |
|---|---|
| 戻り値 | `array<int, bool>` `[time_id => true/false, ...]` |

---

### `add_cc_booking(PDO $db, ...) : int` ⚠️ 内部関数

トランザクション内で予約を1件INSERTする。**直接呼び出さず `book_cc_plus()` 等のラッパー関数を使うこと。**

| 引数 | 型 | 説明 |
|---|---|---|
| `$db` | `PDO` | トランザクション管理中のDB接続 |
| `$student_id` | `int` | 生徒ID |
| `$cc_slot_id` | `int` | スロットID |
| `$time_id` | `int` | 時間ID |
| `$style_id` | `int` | 面談スタイルID |
| `$cc_plus_booking_id` | `int\|null` | CC+仮予約から確定する場合の元予約ID |

| | |
|---|---|
| 戻り値 | `int` 採番された予約ID |

---

### `add_cc_request(PDO $db, ...) : void` ⚠️ 内部関数

トランザクション内で申請を1件INSERTする。**直接呼び出さず各ラッパー関数を使うこと。**

| 引数 | 型 | 説明 |
|---|---|---|
| `$db` | `PDO` | トランザクション管理中のDB接続 |
| `$type_id` | `int` | 申請種別（上記「申請種別一覧」参照） |
| `$student_id` | `int` | 生徒ID |
| `$booking_id_a` | `int` | 変更元（または新規）予約ID |
| `$booking_id_b` | `int\|null` | 変更先予約ID（変更申請時のみ） |
| `$message` | `string\|null` | 申請メッセージ（任意） |

---

### `book_cc_plus(int $student_id, string $date, int $time_id, int $style_id, ?string $message): bool`
CC+枠の新規予約申請を行う。空きスロット特定・予約登録・申請登録をトランザクションで一括実行する。

```php
$result = book_cc_plus(
    student_id: 1,
    date: '2026-05-10',
    time_id: 2,
    style_id: 1,
    message: '都合がいいので申し込みます'
);
```

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true`（空きなし・DB失敗時は `false`） |

---

### `book_cc_plus_change(int $student_id, int $from_booking_id, string $date, int $time_id, int $style_id, ?string $message): bool`
CC+予約の変更申請を行う。変更先の仮予約を作成し申請を登録する。  
変更元の予約削除は管理者承認後（`approve_cc_plus_change`）に行われる。

```php
$result = book_cc_plus_change(
    student_id: 1,
    from_booking_id: 10,
    date: '2026-06-07',
    time_id: 3,
    style_id: 1,
);
```

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true` |

---

### `book_cc_plus_cancel(int $student_id, int $booking_id, ?string $message): bool`
CC+予約のキャンセル申請を登録する。予約の削除は管理者承認後に行われる。

```php
$result = book_cc_plus_cancel(student_id: 1, booking_id: 10);
```

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true` |

---

### `request_cc_change(int $student_id, int $booking_id_a, int $booking_id_b, ?string $message): bool`
必須CC予約の変更申請（生徒間の入れ替え申請）を登録する。  
実際の入れ替えは管理者が `swap_cc_bookings()` で行う。

```php
$result = request_cc_change(
    student_id: 1,
    booking_id_a: 10,   // 自分の予約
    booking_id_b: 15,   // 変更先の予約
);
```

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true` |

---

### `approve_cc_plus_change(int $request_id): bool`
CC+変更申請を承認する。変更元の仮予約（とそれに紐づく確定済み通常予約）を削除し、ステータスを承認済みに更新する。

```php
$result = approve_cc_plus_change(request_id: 5);
```

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true` |

---

### `reject_cc_plus_change(int $request_id): bool`
CC+変更申請を却下する。変更先の仮予約を削除し、変更元は現状維持のままステータスを却下に更新する。

```php
$result = reject_cc_plus_change(request_id: 5);
```

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true` |

---

### `bulk_book_cc(int $course_id): bool`
指定コースの全生徒に対して、全回数分の必須CC予約を一括登録する。

**処理方針**
- `cc_count` ごとに生徒を日付数で均等分割（端数は前の日付グループへ）
- 各グループ内の生徒を `m_times` の件数ずつチャンクに分割し、チャンクごとにスロットを生成
- 既に同 `cc_count` の予約がある生徒はスキップして続行
- `style_id` はデフォルト値（1）で登録

```php
$result = bulk_book_cc(course_id: 2);
```

| 引数 | 型 | 説明 |
|---|---|---|
| `$course_id` | `int` | 対象コースID |

| | |
|---|---|
| 戻り値 | `bool` 成功時 `true`、DBエラー時 `false` |

---

### `get_course_cc_bookings(int $course_id, int $cc_count): array`
指定コース・回数の必須CC予約を、日付 > 時間 > 予約一覧 の三次元構造で返す。  
CC+から確定した通常予約（`cc_plus_booking_id IS NOT NULL`）は除外される。

```php
$bookings = get_course_cc_bookings(course_id: 2, cc_count: 1);
foreach ($bookings['2026-05-10']['10:00'] as $b) {
    echo $b['student_id'];    // 生徒ID
    echo $b['student_name'];  // 生徒氏名
}
```

**返却データ構造**
```php
[
    '2026-05-10' => [
        '10:00' => [
            ['booking_id' => 10, 'student_id' => 3, 'student_name' => '山田太郎'],
        ],
        '11:00' => [...],
    ],
]
```

---

### `get_course_cc_bookings_by_student(int $student_id, string $date): array`
生徒IDと日付から `get_course_cc_bookings()` を呼び出すラッパー。  
`student_id → course_id → cc_count` の順に解決してから委譲する。

```php
$bookings = get_course_cc_bookings_by_student(student_id: 1, date: '2026-05-10');
```

| | |
|---|---|
| 戻り値 | `array` `get_course_cc_bookings()` と同じ構造。解決できない場合は空配列 |

---

### `get_cc_change_confirm(int $booking_id_a, int $booking_id_b): array`
必須CC変更申請の確認画面用データを取得する。  
2つの予約のそれぞれの生徒情報と入れ替え後の日時を返す。

```php
$data = get_cc_change_confirm(booking_id_a: 10, booking_id_b: 15);
echo $data['my_self']['student_name'];  // 自分
echo $data['target']['student_name'];   // 相手
```

**返却データ構造**
```php
[
    'my_self' => [
        'course_name'   => 'Webプログラミング科',
        'student_name'  => '山田太郎',
        'from_datetime' => '2026-05-10 10:00',  // 現在の日時
        'to_datetime'   => '2026-06-07 11:00',  // 変更後の日時
    ],
    'target' => [
        'course_name'   => 'Webプログラミング科',
        'student_name'  => '鈴木花子',
        'from_datetime' => '2026-06-07 11:00',
        'to_datetime'   => '2026-05-10 10:00',
    ],
]
```

| | |
|---|---|
| 戻り値 | `array` 確認画面用データ。いずれかの予約が取得できない場合は空配列 |
