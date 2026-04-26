<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();

try {
    $admins = get_admins();
} catch (PDOException $e) {
    check($e);
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
  <title>-管理者- 管理者一覧</title>
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
  <main>
    <div class="container-fluid px-4 py-4">
      <h1 class="h3 font-weight-bold text-center mb-4">管理者一覧</h1>

      <div class="text-right mb-3">
        <a href="./admin_admin_add.php" class="btn btn-primary">管理者追加</a>
      </div>

      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered mb-0 ad-index-table">
              <thead>
                <tr>
                  <th class="col-4 ad-index-th">名前</th>
                  <th class="col-4 ad-index-th">ログインID</th>
                  <th class="col-2 ad-index-th">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($admins)): ?>
                  <tr>
                    <td colspan="3" class="ad-index-td">管理者が登録されていません。</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($admins as $admin): ?>
                    <tr>
                      <td class="ad-index-td"><?= h($admin['last_name'] . ' ' . $admin['first_name']); ?></td>
                      <td class="ad-index-td"><?= h($admin['login_id']); ?></td>
                      <td class="ad-index-td">
                        <a class="btn btn-info mx-1 my-1" href="./admin_admin_detail.php?id=<?= h($admin['admin_id']); ?>">詳細</a>
                        <a class="btn btn-success mx-1 my-1" href="./admin_admin_edit.php?id=<?= h($admin['admin_id']); ?>">編集</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <a href="./admin_index.php" class="btn btn-secondary px-3 py-2">トップへ戻る</a>
      </div>
    </div>
  </main>
  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>

</html>
