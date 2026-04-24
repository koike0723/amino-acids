<!-- 必須キャリコン編集実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

session_start();

$student_id = $_SESSION['student_id'];
$selected_date = $_GET['selected_date'];
$booking_id = $_GET['booking_id'];

$student = get_student($student_id);
foreach($student['bookings'] as $booking){
if($booking['booking_id'] === $booking_id){
    $
}
}



$result = book_cc_plus_change(
    // ログインしてればOK
    student_id: 1,

    from_booking_id: 10,
    date: '2026-06-07',
    time_id: 3,
    style_id: 1,
);

?>

// <a href="./student_cc_detail_require.php?selected_date=<?= $selected_date
                                                            ?>&booking_id=<?= $login_booking_id ?>">

                                                            selected_date=<?php echo ($booking['cc_date']); ?>