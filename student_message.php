 <?php 
 require_once __DIR__ . '/functions/functions.php';
 ?>

<!doctype html>
<html lang="ja">

<head>
    <title>予約一覧</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>
    <?php require_once('./inc/student_header.php'); ?>

    <main class="container py-5">
        <section class="message-section">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 text-center">
                    <h1 class="mb-5 fs-2">メッセージ一覧</h1>

                    <div class="table-responsive d-flex justify-content-center">
                        <table class="table table-bordered align-middle text-center w-auto">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col">既読</th>
                                    <th scope="col">時間</th>
                                    <th scope="col">送信者</th>
                                    <th scope="col">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>□</td>
                                    <td>2027/10/1 14:43</td>
                                    <td>事務</td>
                                    <td>
                                        <a href="student_message_detail.php?id=1" class="btn btn-success btn-sm">詳細</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>✓</td>
                                    <td>2027/10/1 14:43</td>
                                    <td>事務</td>
                                    <td>
                                        <a href="student_message_detail.php?id=2" class="btn btn-success btn-sm">詳細</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>□</td>
                                    <td>2027/10/1 14:43</td>
                                    <td>事務</td>
                                    <td>
                                        <a href="student_message_detail.php?id=3" class="btn btn-success btn-sm">詳細</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>□</td>
                                    <td>2027/10/1 14:43</td>
                                    <td>事務</td>
                                    <td>
                                        <a href="student_message_detail.php?id=4" class="btn btn-success btn-sm">詳細</a>
                                    </td>
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