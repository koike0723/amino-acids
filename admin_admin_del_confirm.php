<!-- 管理者削除確認画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者削除確認</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">
        <h1 class="m-5">削除しますが、よろしいですか？</h1>
        <div class="admin_id">
            <label for="admin_id">ID</label>
            <p></p>
        </div>
        <div class="admin_password">
            <label for="admin_password">パスワード</label>
            <p></p>
        </div>
        <div class="admin_name">
            <label for="admin_name">名前</label>
            <p></p>
        </div>
        <div class="col-12 d-flex justify-content-center mt-4 mb-5">
            <a href="admin_course_list.php" class="btn btn-secondary px-3 mr-5">一覧へ戻る</a>
            <input type="submit" value="削除" class="btn btn-primary px-3 ml-5">
        </div>
    </div>
</body>

</html>