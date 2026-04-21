<!-- 生徒側任意キャリコン予約変更画面 -->
<?php
session_start();
require_once __DIR__ . '/functions/functions.php';
?>

<?php
$timeId = $_GET['timeid'];
$selected_date = $_GET['selected_date'];
$time = $_GET['time'];
$booking_id = 11; //予約変更時GETから取得
$_SESSION['selected_date'] = $_GET['selected_date'];

//if (isset($timeId) || isset($date)) {
try {
    $db = db_connect();
    $sql = 'SELECT * FROM m_meating_styles';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cc_style = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $student_id = 1; //後々セッションから取得
    $reserve = get_student(1);
    $filters = [
        'booking_id' => $booking_id,
    ];
    $result = get_cc_bookings($filters);
    $booking = reset($result);
} catch (PDOException $e) {
    check($e);
}
//}else{
// header('location:student_reserve.php');
//exit();
//}
?>

<!doctype html>
<html lang="ja">

<head>
    <title>予約追加・変更</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>
    <?php include('./inc/student_header.php'); ?>

    <main class="container py-5">
        <section class="student-reservation-apply-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 text-center">

                    <h1 class="student-reservation-apply-title mb-4">予約申請</h1>

                    <div class="student-reservation-apply-detail mx-auto">
                        <div class="row mb-3">
                            <div class="col-4 text-end fw-bold">クラス：</div>
                            <div class="col-8 text-start" style="text-align: left;"><?= $reserve['course_name'] ?></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 text-end fw-bold">名前：</div>
                            <div class="col-8 text-start" style="text-align: left;"><?= $reserve['student_name'] ?></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 text-end fw-bold">日時：</div>
                            <div class="col-8 text-start" style="text-align: left;"><?= $reserve['bookings'][0]['cc_date'] ?>&nbsp;<?= $time ?></div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-4 text-end fw-bold">方法：</div>
                            <div class="col-8 text-start">
                                <div class="student-reservation-apply-methods">
                                    <div class="form-check form-check-inline me-4" style="display: flex;">
                                        <?php foreach ($cc_style as $style): ?>
                                            <div style="display: flex; gap: 5px; padding-inline-end: 20px;">
                                                <input
                                                    class="form-check-input student-reservation-apply-radio"
                                                    type="radio"
                                                    name="reservation_method"
                                                    id="student-method-face" style="padding-inline: 30px;"
                                                    value="<?= $reserve['bookings'][0]['cc_style_id'] === $style['id'] ? 'checked' : '' ?>"?>

                                                <label class="form-check-label" for="student-method-face"><?= $style['name'] ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="student-reservation-apply-buttons d-flex justify-content-center gap-3 mt-4">
                        <a href="">
                            <button type="submit" class="btn btn-success student-reservation-apply-submit">
                                追加・変更申請
                            </button>
                        </a>
                        <a href="student_reserve.php?selected_date=<?= $_SESSION['selected_date'] ?>" class="btn btn-secondary">戻る</a>
                    </div>

                </div>
            </div>
        </section>
    </main>



    <script src="./js/script.js"></script>
</body>

</html>