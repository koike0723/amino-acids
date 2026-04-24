<!-- 必須キャリコン編集実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

session_start();
$login_student = $_SESSION['student_id'];
$login_booking_id = $_GET['login_booking_id'];
$booking_id = $_GET['booking_id'];

if (!isset($login_student) || !isset($login_booking_id) || !isset($booking_id)) {
    header('location:../index.php');
    exit();
} else {
    request_cc_change((int)$login_student, (int)$login_booking_id, (int)$booking_id);
}

header('location:../index.php');
exit();
?>
