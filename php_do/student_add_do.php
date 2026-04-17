<!-- 生徒登録実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

// データの有無確認処理
if (
    !isset($_POST["course_id"], $_POST["first_name"], $_POST["last_name"], $_POST["student_number"]) ||
    empty($_POST["course_id"]) ||
    empty($_POST["first_name"]) ||
    empty($_POST["last_name"]) ||
    empty($_POST["student_number"])
) {
    header("Location: ../admin_student_add.php?status=error&message=no_data");
    exit;
}

// 送られてきたデータの取得成形 
$course_id = $_POST["course_id"];
$students = [];
foreach ($_POST["first_name"] as $key => $first_name) {
    $students[$key]["first_name"] = $first_name;
}
foreach ($_POST["last_name"] as $key => $last_name) {
    $students[$key]["last_name"] = $last_name;
}
foreach ($_POST["student_number"] as $key => $student_number) {
    $students[$key]["number"] = $student_number;
}


// 生徒重複チェック&出席番号が整数かどうかチェック
$same_check = 0;
foreach ($students as $student) {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $db = new PDO($dsn, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // SQL
        $sql = 'SELECT
    COUNT(*)
    FROM m_students
    WHERE course_id = :course_id
    AND number = :number';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":course_id", $course_id, PDO::PARAM_INT);
        $stmt->bindParam(":number", $student["number"], PDO::PARAM_INT);
        $stmt->execute();
        $same_check = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // exit('同じ生徒のチェック時にエラー発生: ' . $e->getMessage());
        header("Location: ../admin_student_add.php?status=error&message=cant_db");
        exit;
    }
    if(!is_int($student["number"])){
        header("Location: ../admin_student_add.php?status=error&message=not_int");
        exit;
    }
}
if($same_check > 0){
    header("Location: ../admin_student_add.php?status=error&message=same_student");
    exit;
}


// 生徒の登録
try {
    add_students($course_id, $students);
} catch (PDOException $e) {
    // exit('生徒の登録に失敗しました: ' . $e->getMessage());
    header("Location: ../admin_student_add.php?status=error&message=cant_db");
    exit;
}
header("Location: ../admin_student_add.php?status=success");
exit;
?>