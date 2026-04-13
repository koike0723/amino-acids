<!-- 生徒側任意キャリコン予約画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<!doctype html>
<html lang="ja">

<head>
    <title>予約可能枠一覧</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>
    <?php include('./inc/student_header.php'); ?>

    <main class="container py-5">
        <section class="s-career-frame-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 text-center">

                    <h1 class="s-career-frame-title mb-4">キャリコンプラス一覧</h1>
                    <p class="s-career-frame-date mb-4">10月16日</p>

                    <!-- 項目内容が〇なら背景ホワイト、×なら背景グレーのｐｈｐ ※いったんＨＴＭＬだけにした方が変更しやすいかと思ったのでメモとして以下に残します-->
                    <!-- <td class="<?php echo $is_available ? 's-frame-available' : 's-frame-unavailable'; ?>">
  <?php echo $is_available ? '○' : '×'; ?>
</td> -->

                    <div class="table-responsive d-flex justify-content-center">
                        <table class="table table-bordered s-career-frame-table w-auto align-middle text-center">
                            <thead>
                                <tr>
                                    <th>時間</th>
                                    <th>任意キャリコン枠</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>１０：００〜</td>
                                    <td class="s-frame-available">○</td>
                                </tr>
                                <tr>
                                    <td>１１：００〜</td>
                                    <td class="s-frame-unavailable">×</td>
                                </tr>
                                <tr>
                                    <td>１２：００〜</td>
                                    <td class="s-frame-available">○</td>
                                </tr>
                                <tr>
                                    <td>１４：００〜</td>
                                    <td class="s-frame-unavailable">×</td>
                                </tr>
                                <tr>
                                    <td>１５：００〜</td>
                                    <td class="s-frame-available">○</td>
                                </tr>
                                <tr>
                                    <td>１６：００〜</td>
                                    <td class="s-frame-available">○</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        <a href="./index.php" class="btn btn-secondary">戻る</a>
                    </div>

                </div>
            </div>
        </section>
    </main>



    <script src="./js/script.js"></script>
</body>

</html>