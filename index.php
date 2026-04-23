<?php
require_once __DIR__ . '/functions/functions.php';

?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>予約一覧</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />

</head>

<body>
    <?php include('./inc/student_header.php'); ?>
    <?php
    // DBからデータ取得
    $login_student = $_SESSION['student_id'];
    if (!isset($_SESSION['student_id'])) {
        header('location:./inc/login.php');
        exit();
    } else {
        $student = get_student($login_student);
        $cc_plus_table = get_cc_plus_dates();
    }
    ?>
    <main role="main" class="student-index-main" style="padding:60px 15px 0">
        <section class="student-index-section">
            <div class="student-index-reserve-area">
                <div class="student-index-reserve-detail">
                    <p class="student-index-h1">予約一覧</p>

                    <div class="student-index-flex-area">
                        <?php if (!empty($student['bookings'])): ?>
                            <?php foreach ($student['bookings'] as $booking): ?>
                                <div class="student-index-flex">
                                    <div class="student-index-flex-text">
                                        <?php if ($booking['is_cc_plus'] === true) : ?>
                                            <span class="student-index-text-color1">任意：</span>
                                        <?php else: ?>
                                            <span class="student-index-text-color2">必須：</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="student-index-flex-date">
                                        <?php echo h($booking['cc_date']); ?>
                                    </div>
                                    <div class="student-index-flex-time">
                                        <?php echo h(substr($booking['cc_time'], 0, 5)); ?>
                                    </div>
                                    <div class="student-index-flex-btn-area">
                                        <?php if ($booking['is_cc_plus'] === true): ?>
                                            <button
                                                type="button"
                                                class="student-index-flex-btn-change"
                                                onclick="location.href='./student_reserve.php?selected_date=<?php echo ($booking['cc_date']); ?>&booking_id=<?= $booking['booking_id'] ?>'">
                                                変更
                                            <?php else: ?>
                                                <button
                                                    type="button"
                                                    class="student-index-flex-btn-change"
                                                    onclick="location.href='./student_cc_detail_require.php?selected_date=<?php echo ($booking['cc_date']); ?>'">
                                                    変更
                                                </button>
                                            <?php endif; ?>
                                    </div>
                                    <div class="student-index-flex-btn-area">
                                        <button
                                            type="button"
                                            class="student-index-flex-btn-cancel"
                                            onclick="location.href='./student_reserve_del_confirm.php?selected_date=<?php echo $booking['cc_date']; ?>&time=<?php echo $booking['cc_time']; ?>'">
                                            キャンセル
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="student-index-flex-notice">予約はありません</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <section class="student-index-section-ccplus">
            <p class="student-index-h1">キャリコンプラス実施日</p>
            <div class="cc-plus-list-area">
                <ul class="cc-plus-list-ul">
                    <?php foreach ($cc_plus_table as $cc_plus): ?>
                        <li class="cc-plus-list-li">
                            <a href="./student_reserve.php?selected_date=<?= $cc_plus['cc_date'] ?>"><?= $cc_plus['cc_date'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

    </main>

    <script src="./js/script.js"></script>
</body>

</html>