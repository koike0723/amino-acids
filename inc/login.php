<?php 
require_once __DIR__ . '/../functions/functions.php';
session_start();
?>


<!doctype html>
<html lang="ja">

<head>
    <title>ログイン</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>

    <main role="main" class="container" style="padding:60px 15px 0">
        <div>
            <!-- ここから「本文」-->
            <h1>ログイン</h1>
            <form class="l-login_form" action="./../php_do/login_do.php" method="post">

                <div class="mb-3">
                    <label for="name" class="sr-only">名前</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="ユーザー名" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="sr-only">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="パスワード" required>
                </div>
                <a href="./pass_change.php">パスワードをお忘れですか？</a>
                <input type="submit" class="btn btn-primary btn-block" value="ログイン">

            </form>


            <!-- 本文ここまで -->
        </div>
    </main>

    <script src="../js/script.js"></script>
</body>

</html>