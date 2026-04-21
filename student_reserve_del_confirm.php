<?php
require_once __DIR__ . '/functions/functions.php';

// DBからデータ取得
$student = get_student(1);

// index.php から受け取る
$selected_date = $_GET['cc_date'] ?? '';
$selected_time = $_GET['cc_time'] ?? '';

// 一致する予約を探す
$target_booking = null;

if (!empty($student['bookings'])) {
    foreach ($student['bookings'] as $booking) {
        if ($booking['cc_date'] === $selected_date && $booking['cc_time'] === $selected_time) {
            $target_booking = $booking;
            break;
        }
    }
}
?>
<!doctype html>
<html lang="ja">

<head>
    <?php check($student) ?>
    <title>予約追加・変更</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <?php include('./inc/student_header.php'); ?>

    <main class="container py-5">
        <section class="student-reservation-detail-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">

                    <h1 class="student-reservation-detail-title text-center mb-5">予約詳細</h1>

                    <?php if ($target_booking): ?>
                        <div class="student-reservation-detail-body mx-auto">
                            <div class="row mb-4">
                                <div class="col-12 col-sm-4 text-sm-end fw-bold">クラス：</div>
                                <div class="col-12 col-sm-8">
                                    <?php echo htmlspecialchars($student['course_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12 col-sm-4 text-sm-end fw-bold">名前：</div>
                                <div class="col-12 col-sm-8">
                                    <?php echo htmlspecialchars($student['student_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12 col-sm-4 text-sm-end fw-bold">日時：</div>
                                <div class="col-12 col-sm-8">
                                    <?php echo htmlspecialchars($target_booking['cc_date'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php echo htmlspecialchars(substr($target_booking['cc_time'], 0, 5), ENT_QUOTES, 'UTF-8'); ?>〜
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col-12 col-sm-4 text-sm-end fw-bold">方法：</div>
                                <div class="col-12 col-sm-8">
                                    <?php echo htmlspecialchars($target_booking['how_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="student-reservation-detail-buttons d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-warning student-reservation-detail-cancel-btn">
                                キャンセル申請
                            </button>
                            <a href="./index.php" class="btn btn-secondary">
                                戻る
                            </a>
                        </div>

                    <?php else: ?>
                        <p class="text-center text-danger">該当する予約が見つかりませんでした。</p>
                        <div class="text-center">
                            <a href="./index.php" class="btn btn-secondary">戻る</a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>
    </main>

    <script src="./js/script.js"></script>
</body>

</html>