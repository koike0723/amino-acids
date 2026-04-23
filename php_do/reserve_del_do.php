<!-- 任意キャリコン予約削除実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: ../student_reserve_del.php');
    exit();
}

$student_id = $_GET['student_id'] ?? '';
$booking_id = $_GET['booking_id'] ?? '';

// 値が空、または数字でない場合は不正アクセスとして戻す
if ($student_id === '' || $booking_id === '' || !ctype_digit($student_id) || !ctype_digit($booking_id)) {
    header('Location: ../student_reserve_del.php');
    exit();
}

book_cc_plus_cancel((int)$student_id, (int)$booking_id);

// 処理後に一覧などへ戻す
header('Location: ../index.php');
exit();
