<!-- 生徒登録実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

$course_id = $_POST["course_id"];
$student = [];
foreach ($_POST["first_name"] as $key => $first_name) {
    $student[$key]["first_name"] = $first_name;
}
foreach ($_POST["last_name"] as $key => $last_name) {
    $student[$key]["last_name"] = $last_name;
}
foreach ($_POST["student_number"] as $key => $student_number) {
    $student[$key]["number"] = $student_number;
}
add_students($course_id, $student);
header("Location: ../admin_student_add.php");
exit;
?>