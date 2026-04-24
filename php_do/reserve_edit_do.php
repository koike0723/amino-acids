<!-- 任意キャリコン変更実行処理 -->
<?php 
require_once __DIR__ . '/../functions/functions.php';

session_start();
$login_student = $_SESSION['student_id'];
$timeId = $_GET['timeid'];
$selected_date = $_GET['selected_date'];
$booking_id = $_GET['booking_id'];
$style_id = $_GET['style_id'];

if(!isset($login_student) || !isset($timeId) || !isset($selected_date) || !isset($booking_id) || !isset($style_id)){
    header('location:../index.php');
    exit();
}else{
    book_cc_plus_change((int)$login_student,(int)$booking_id,$selected_date,(int)$timeId,(int)$style_id);
}

header('location:../index.php');
exit();

?>