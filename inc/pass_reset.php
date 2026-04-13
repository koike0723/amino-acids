<?php 
require_once __DIR__ . '/../functions/functions.php';
?>

<!doctype html>
<html lang="ja">

<head>
    <title>パスワードリセット申請</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>
    <main role="main" class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <!-- ここから本文 -->
                <h1 class="mb-4 text-center">パスワードリセット申請</h1>

                <form class="l-login_form text-center" action="login_do.php" method="post">
                    <p class="mb-4">名前：岸本恵美子</p>

                    <div class="mb-3 text-start">
                        <label for="name" class="form-label d-block text-center">名前</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            placeholder="氏名"
                            required>
                    </div>



                    <div class="d-grid">
                        <input type="submit" class="btn btn-primary" value="申請">
                    </div>
                </form>
                <!-- 本文ここまで -->
            </div>
        </div>
    </main>

    <script src="../js/script.js"></script>
</body>

</html>