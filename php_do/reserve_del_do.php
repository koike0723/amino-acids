<!-- 任意キャリコン予約削除実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';
?>
<?php
$student_id = $_GET['student_id'];
$booking_id = $_GET['booking_id'];

book_cc_plus_cancel($student_id, $booking_id);

?>