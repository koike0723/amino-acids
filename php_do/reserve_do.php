<!-- 任意キャリコン新規予約実行処理 -->
<?php 
require_once __DIR__ . '/../functions/functions.php';

session_start();
$login_student = $_SESSION['student_id'];
$timeId = $_GET['timeid'];
$selected_date = $_GET['selected_date'];
$time = $_GET['time'];
$style_id = $_GET['style_id'];

if(!isset($login_student) || !isset($timeId) || !isset($selected_date) || !isset($time) || !isset($style_id)){
    header('location:../index.php');
    exit();
}else{
    book_cc_plus((int)$login_student,$selected_date,(int)$timeId,(int)$style_id);
}

header('location:../index.php');
exit();

?>

