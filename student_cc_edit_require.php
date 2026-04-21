<!-- 生徒側必須キャリコン変更画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<?php
//$student_before = $_GET[''];
//$student_after = $_GET[''];

//if(!empty($student_before) || !empty($student_after)){
$student_1 = get_student(1);
$student_2 = get_student(2);

check($student_1);
//}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- キャリコン管理</title>
</head>

<body>
    <?php
    $myself = [
        'course_name' => 'web',
        'student_name' => '梅崎',
        'from_date' => '2026-4-20',
        'to_date' => '2026-4-25',
    ];
    $target = [
        'course_name' => 'webpuro',
        'student_name' => '江原',
        'from_date' => '2026-4-25',
        'to_date' => '2026-5-3',
    ];
    ?>
    <?php require_once __DIR__ . '/inc/student_header.php'; ?>
    <main>
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
                    <dd class="student-require-edit-dd"><?= $myself['from_date'] ?></dd>
                </div>
                <div class="student-requier-edit-flex-con">
                    <dt class="student-require-edit-dt">変更後日時：</dt>
                    <dd class="student-require-edit-dd"><?= $myself['to_date'] ?></dd>
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
                    <dd class="student-require-edit-dd"><?= $target['from_date'] ?></dd>
                </div>
                <div class="student-requier-edit-flex-con">
                    <dt class="student-require-edit-dt">変更後日時：</dt>
                    <dd class="student-require-edit-dd"><?= $target['to_date'] ?></dd>
                </div>
            </dl>
        </div>
        <div class="student-require-edit-btn">
            <div class="student-require-edit-btn-flex">
                <a href="#">
                    <button type="btn" class="btn-require-edit">変更申請</button>
                </a>
                <a href="#">
                    <button type="btn" class="btn-require-prev">戻る</button>
                </a>
            </div>
        </div>
    </main>
    <script src="/js/script.js"></script>
</body>

</html>