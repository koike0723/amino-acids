<!-- http://localhost:8080/amino-acids/php_do/course_add_do.php -->
<!-- コース追加実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

// データの有無確認処理
if (
    !isset($_POST["course_name"], $_POST["room_id"], $_POST["category_id"], $_POST["course_start"], $_POST["course_finish"], $_POST["cc1_1"], $_POST["cc1_2"], $_POST["cc2_1"], $_POST["cc2_2"], $_POST["cc3_1"], $_POST["cc3_2"]) ||
    empty($_POST["course_name"]) ||
    empty($_POST["room_id"]) ||
    empty($_POST["category_id"]) ||
    empty($_POST["course_start"]) ||
    empty($_POST["course_finish"])
) {
    header("Location: ../admin_course_add.php?status=error&message=no_data");
    exit;
}



// 送られてきたデータの取得成形
$start_date = $_POST["course_start"];
$finish_date = $_POST["course_finish"];
$room_id = $_POST["room_id"];
$category_id = $_POST["category_id"];
$cc1_1 = $_POST["cc1_1"];
$cc1_2 = $_POST["cc1_2"];
$cc2_1 = $_POST["cc2_1"];
$cc2_2 = $_POST["cc2_2"];
$cc3_1 = $_POST["cc3_1"];
$cc3_2 = $_POST["cc3_2"];
$course = [
    'name' => $_POST["course_name"],
    'start_date' => $start_date,
    'end_date' => $finish_date,
    'room_id' => $room_id,
    'category_id' => $category_id,
    'cc' => [
        1 => [
            $cc1_1,
            $cc1_2,
        ],
        2 => [
            $cc2_1,
            $cc2_2,
        ],
        3 => [
            $cc3_1,
            $cc3_2,
        ],
    ]
];

// コース開始日と終了日の入力値チェック
if (strtotime($start_date) > strtotime($finish_date)) {
    header("Location: ../admin_course_add.php?status=error&message=error_date");
    exit;
}

//キャリコンの日がかぶっていないかチェック
$cc_days = [$cc1_1, $cc1_2, $cc2_1, $cc2_2, $cc3_1, $cc3_2];
$real_cc_days = array_filter($cc_days, fn($v) => !empty($v));
if (count($real_cc_days) != count(array_unique($real_cc_days))) {
    header("Location: ../admin_course_add.php?status=error&message=error_cc_date");
    exit;
}

// 訓練コース重複チェック
// kan-to-do:期間で重複チェックする
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // SQL
    $sql = 'SELECT
        COUNT(*)
        FROM m_courses
        WHERE room_id = :room_id
        AND (end_date > :start_day OR start_date < :end_day)';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":room_id", $room_id, PDO::PARAM_INT);
    $stmt->bindParam(":start_day", $start_date, PDO::PARAM_STR);
    $stmt->bindParam(":end_day", $finish_date, PDO::PARAM_STR);
    $stmt->execute();
    $same_check = $stmt->fetchColumn();
} catch (PDOException $e) {
    // exit('同じ生徒のチェック時にエラー発生: ' . $e->getMessage());
    header("Location: ../admin_course_add.php?status=error&message=cant_db");
    exit;
}
if ($same_check > 0) {
    header("Location: ../admin_course_add.php?status=error&message=same_course");
    exit;
}


// 訓練コースの登録
try {
    add_course($course);
} catch (PDOException $e) {
    // exit('訓練コースの登録に失敗しました: ' . $e->getMessage());
    header("Location: ../admin_course_add.php?status=error&message=cant_db");
    exit;
}
header("Location: ../admin_course_add.php?status=success");
exit;
?>
<pre>
    <?php var_dump($course) ?>
</pre>