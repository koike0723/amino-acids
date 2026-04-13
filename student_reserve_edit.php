<!-- 生徒側任意キャリコン予約変更画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
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
                            <div class="col-8 text-start">Webプログラミング科</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 text-end fw-bold">名前：</div>
                            <div class="col-8 text-start">岸本恵美子</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4 text-end fw-bold">日時：</div>
                            <div class="col-8 text-start">2026年 4月 4日 10：00</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-4 text-end fw-bold">方法：</div>
                            <div class="col-8 text-start">
                                <div class="student-reservation-apply-methods">
                                    <div class="form-check form-check-inline me-4">
                                        <input
                                            class="form-check-input student-reservation-apply-radio"
                                            type="radio"
                                            name="reservation_method"
                                            id="student-method-face"
                                            value="対面"
                                            checked>
                                        <label class="form-check-label" for="student-method-face">対面</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input
                                            class="form-check-input student-reservation-apply-radio"
                                            type="radio"
                                            name="reservation_method"
                                            id="student-method-zoom"
                                            value="ZOOM">
                                        <label class="form-check-label" for="student-method-zoom">ZOOM</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="student-reservation-apply-buttons d-flex justify-content-center gap-3 mt-4">
                        <button type="submit" class="btn btn-success student-reservation-apply-submit">
                            追加・変更申請
                        </button>
                        <a href="student_reserve.php" class="btn btn-secondary">戻る</a>
                    </div>

                </div>
            </div>
        </section>
    </main>



    <script src="./js/script.js"></script>
</body>

</html>