<!-- 生徒側任意キャリコン予約削除確認画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

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
        <section class="student-reservation-detail-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">

                    <h1 class="student-reservation-detail-title text-center mb-5">予約詳細</h1>

                    <div class="student-reservation-detail-body mx-auto">
                        <div class="row mb-4">
                            <div class="col-12 col-sm-4 text-sm-end fw-bold">クラス：</div>
                            <div class="col-12 col-sm-8">６C？ Webプログラミング？</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12 col-sm-4 text-sm-end fw-bold">名前：</div>
                            <div class="col-12 col-sm-8">岸本恵美子</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12 col-sm-4 text-sm-end fw-bold">日時：</div>
                            <div class="col-12 col-sm-8">2027年 10月 16日 16:00〜</div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-12 col-sm-4 text-sm-end fw-bold">方法：</div>
                            <div class="col-12 col-sm-8">対面</div>
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

                </div>
            </div>
        </section>
    </main>


    <script src="./js/script.js"></script>
</body>

</html>