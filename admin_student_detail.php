<!-- 生徒詳細画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 生徒詳細</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <p class="student-detail-h1">生徒詳細</p>
        <div class="student-detail-dl-area">
            <dl class="student-detail-dl">
                <div class="student-detail-dl-flex">
                    <dt class="student-detail-dt">名前：</dt>
                    <dd class="student-detail-dd">梅崎龍之介</dd>
                </div>
                <div class="student-detail-dl-flex">
                    <dt class="student-detail-dt">出席番号:</dt>
                    <dd class="student-detail-dd">C01</dd>
                </div>
                <div class="student-detail-dl-flex">
                    <dt class="student-detail-dt">訓練名:</dt>
                    <dd class="student-detail-dd">6C / Webプログラミング科</dd>
                </div>
                <div class="student-detail-dl-flex">
                    <dt class="student-detail-dt">状態：</dt>
                    <dd class="student-detail-dd">就職済</dd>
                </div>
                <div class="student-detail-dl-flex">
                    <dt class="student-detail-dt">キャリコン履歴：</dt>
                    <dd class="student-detail-dd">日数</dd>
                </div>
            </dl>
            <div class="student-detail-controle-area">
                <a href="admin_student_edit.php">
                    <button type="btn" class="student-detail-editBtn">編集画面へ</button>
                </a>
                <a href="#">
                    <button type="btn" class="prev-btn" style="border-radius: 5px;">戻る</button>
                </a>
            </div>
        </div>
    </main>
</body>

</html>