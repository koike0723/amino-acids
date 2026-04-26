<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
  <link rel="stylesheet" href="./css/style.css">
  <title>-管理者- 管理者追加</title>
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
  <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

    <h1 class="m-5">管理者追加</h1>

    <form action="./php_do/admin_add_do.php" method="post">
      <div class="col-6 mb-3">
        <label class="form-label">姓</label>
        <input type="text" name="last_name" class="form-control" required placeholder="山田">
      </div>
      <div class="col-6 mb-3">
        <label class="form-label">名</label>
        <input type="text" name="first_name" class="form-control" required placeholder="太郎">
      </div>
      <div class="col-6 mb-3">
        <label class="form-label">ログインID</label>
        <input type="text" name="login_id" class="form-control" required placeholder="admin002">
      </div>
      <div class="col-6 mb-3">
        <label class="form-label">パスワード</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="col-12 d-flex mt-4 mb-5" style="gap: 12px;">
        <a href="./admin_admin_list.php" class="btn btn-secondary px-3 py-2">一覧へ戻る</a>
        <button type="submit" class="btn btn-primary px-3 py-2" style="margin-top: 10px;">追加完了</button>
      </div>
    </form>

  </div>
  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>

</html>
