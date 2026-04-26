<?php
require_once __DIR__ . '/../functions/functions.php';
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: ../admin_index.php');
    exit();
}
?>
<!doctype html>
<html lang="ja">

<head>
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>

    <main role="main" class="container" style="padding:60px 15px 0">
        <div>
            <h1>管理者ログイン</h1>
            <form class="l-login_form" action="../php_do/admin_login_do.php" method="post">

                <div class="mb-3">
                    <label for="login_id" class="sr-only">ログインID</label>
                    <input type="text" name="login_id" id="login_id" class="form-control" placeholder="ログインID" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="sr-only">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="パスワード" required>
                </div>
                <input type="submit" class="btn btn-primary btn-block" value="ログイン">

            </form>
        </div>
    </main>

    <script src="../js/script.js"></script>
</body>

</html>
