<?php 
require_once __DIR__ . '/../functions/functions.php';
?>

<!doctype html>
<html lang="ja">

<head>
    <title>パスワード変更申請</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>
    <main role="main" class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <!-- ここから本文 -->
                <h1 class="mb-4 text-center">パスワード再登録</h1>

                <form class="l-login_form text-center" action="login_do.php" method="post">
                    <p class="mb-4">名前：岸本恵美子</p>

                    <div class="mb-3 text-start">
                        <label for="new-password" class="form-label d-block text-center">新規パスワード</label>
                        <input
                            type="password"
                            name="password"
                            id="new-password"
                            class="form-control"
                            placeholder="パスワード"
                            required>
                    </div>

                    <div class="mb-4 text-start">
                        <label for="confirm-password" class="form-label d-block text-center">
                            確認のため再度パスワードを入力してください
                        </label>
                        <input
                            type="password"
                            name="password_confirm"
                            id="confirm-password"
                            class="form-control"
                            placeholder="確認パスワード"
                            required>
                    </div>

                    <div class="d-grid">
                        <input type="submit" class="btn btn-primary" value="登録">
                    </div>
                </form>
                <!-- 本文ここまで -->
            </div>
        </div>
    </main>

    <script src="../js/script.js"></script>
</body>

</html>