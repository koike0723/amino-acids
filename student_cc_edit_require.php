<!-- 生徒側必須キャリコン変更画面 -->
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- キャリコン管理</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/student_header.php'; ?>
    <?php
    $student = $_SESSION['student_id'];
    $selected_date = $_GET['selected_date'];
    $login_student = $_SESSION['student_id'];
    $login_booking_id = $_GET['login_booking_id'];
    $booking_id = $_GET['booking_id'];
    if (!isset($_SESSION['student_id'])) {
        header('location:./inc/login.php');
        exit();
    } else {
        $student = get_student($login_student);
    }
    $cc_change = get_cc_change_confirm($login_booking_id, $booking_id);

    $myself = $cc_change['my_self'];
    $target = $cc_change['target'];
    ?>
    <main>
        <form action="./php_do/cc_edit_require_do.php" method="GET">
            <p class="student-require-edit-h1">変更申請</p>
            <div class="student-require-edit-flex">
                <dl class="student-require-edit-dl">
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">クラス：</dt>
                        <dd class="student-require-edit-dd"><?= $myself['course_name'] ?></dd>
                    </div>
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">名前：</dt>
                        <dd class="student-require-edit-dd"><?= $myself['student_name'] ?></dd>
                    </div>
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">変更前日時：</dt>
                        <dd class="student-require-edit-dd"><?= $myself['from_datetime'] ?></dd>
                    </div>
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">変更後日時：</dt>
                        <dd class="student-require-edit-dd"><?= $myself['to_datetime'] ?></dd>
                    </div>
                </dl>
            </div>
            <p class="student-require-edit-change-h1">変更相手</p>
            <div class="student-require-edit-flex">
                <dl class="student-require-edit-dl">
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">クラス：</dt>
                        <dd class="student-require-edit-dd"><?= $target['course_name'] ?></dd>
                    </div>
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">名前：</dt>
                        <dd class="student-require-edit-dd"><?= $target['student_name'] ?></dd>
                    </div>
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">変更前日時：</dt>
                        <dd class="student-require-edit-dd"><?= $target['from_datetime'] ?></dd>
                    </div>
                    <div class="student-requier-edit-flex-con">
                        <dt class="student-require-edit-dt">変更後日時：</dt>
                        <dd class="student-require-edit-dd"><?= $target['to_datetime'] ?></dd>
                    </div>
                </dl>
            </div>
            <div class="student-require-edit-btn">
                <div class="student-require-edit-btn-flex">
                    <!-- 送信データ -->
                     <input type="hidden" name="login_booking_id" value="<?= h($login_booking_id); ?>">
                     <input type="hidden" name="booking_id" value="<?= h($booking_id); ?>">
                    <button type="btn" class="btn-require-edit">変更申請</button>
                    <a href="./index.php">
                        <button type="btn" class="btn-require-prev">戻る</button>
                    </a>
                </div>
            </div>
        </form>
    </main>
    <script src="/js/script.js"></script>
</body>

</html>