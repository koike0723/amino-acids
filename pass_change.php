<?php include('./function/function.php'); ?>

<!doctype html>
<html lang="ja">

<head>
    <title>ログイン</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>

    <main role="main" class="container" style="padding:60px 15px 0">
        <div>
            <!-- ここから「本文」-->
            <h1>パスワード再登録</h1>

            <form class="l-login_form" action="login_do.php" method="post">
                <p>名前：岸本恵美子</p>
                <div class="mb-3">
                    <p>新規パスワード</p>
                    <label for="password" class="sr-only">名前</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="パスワード" required>
                </div>
                <div class="mb-3">
                    <p>確認のため再度パスワードを入力してください</p>
                    <label for="password" class="sr-only">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="確認パスワード" required>
                </div>
                <input type="submit" class="btn btn-primary btn-block" value="ログイン">

            </form>


            <!-- 本文ここまで -->
        </div>
    </main>

    <script src="./js/script.js"></script>
</body>

</html>