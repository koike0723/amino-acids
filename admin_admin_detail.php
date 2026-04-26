<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: admin_admin_list.php');
    exit();
}

try {
    $admin = get_admin($id);
} catch (PDOException $e) {
    check($e);
}

if (empty($admin)) {
    header('Location: admin_admin_list.php');
    exit();
}
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
  <title>-管理者- 管理者詳細</title>
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
  <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

    <h1 class="m-5">管理者詳細</h1>

    <div class="d-flex align-items-baseline mb-2">
      <label class="form-label mb-0 text-nowrap">姓：</label>
      <p class="form-control-plaintext py-0 mb-0"><?= h($admin['last_name']); ?></p>
    </div>
    <div class="d-flex align-items-baseline mb-2">
      <label class="form-label mb-0 text-nowrap">名：</label>
      <p class="form-control-plaintext py-0 mb-0"><?= h($admin['first_name']); ?></p>
    </div>
    <div class="d-flex align-items-baseline mb-3">
      <label class="form-label mb-0 text-nowrap">ログインID：</label>
      <p class="form-control-plaintext py-0 mb-0">&ensp;<?= h($admin['login_id']); ?></p>
    </div>

    <div class="col-12 d-flex mt-4 mb-5" style="gap: 12px;">
      <a href="./admin_admin_list.php" class="btn btn-secondary px-3 py-2">一覧へ戻る</a>
      <a href="./admin_admin_edit.php?id=<?= h($admin['admin_id']); ?>" class="btn btn-success px-3 py-2" style="margin-top: 10px;">編集</a>
    </div>

  </div>
  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>

</html>
