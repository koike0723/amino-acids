<!-- 任意キャリコン予約削除実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //     header('Location: ../index.php');
    //     exit();
}

$student_id = $_SESSION['student_id'];
$booking_id = $_GET['booking_id'];
$selected_date = $_GET['selected_date'];
$time = $_GET['time'];

check($student_id);
check($booking_id);
check($selected_date);
check($time);

// 値が空、または数字でない場合は不正アクセスとして戻す
if (empty($student_id) || empty($booking_id) || !is_numeric($student_id) || !is_numeric($booking_id)) {
    header('location:../student_reserve_del_comfirm.php?selected_date=' . $selected_date . '&time=' . $time);
    exit();
} else {
    book_cc_plus_cancel((int)$student_id, (int)$booking_id);
}



// 処理後に一覧などへ戻す
header('Location: ../index.php');
exit();
