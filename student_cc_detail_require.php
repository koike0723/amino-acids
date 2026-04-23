<!-- 生徒側必須キャリコン詳細画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<!doctype html>
<html lang="ja">

<head>
    <title>予約追加・変更</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />

</head>

<body>
    <?php include('./inc/student_header.php'); ?>
    <?php
    $login_student = $_SESSION['student_id'];
    $selected_date = $_GET['selected_date'];
    if (!isset($_SESSION['student_id'])) {
        header('location:./inc/login.php');
        exit();
    } else {
        $student = get_student($login_student);
    }

    $cc_require_student = get_course_cc_bookings_by_student((int)$login_student,  $selected_date);

    check($cc_require_student);

    ?>

    <?php //foreach($cc_require_student as $day){
    //check($day);
    //foreach($day as $times){
    // check($times);
    // foreach($times as $stu){
    //    check($stu);
    //    check($stu['student_id']);
    // }
    // }
    // } 

    ?>

    <main class="container py-4">
        <section class="student-required-cc-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 text-center">

                    <h1 class="student-required-cc-title mb-4">必須キャリコン一覧</h1>

                    <?php foreach ($cc_require_student as $key => $tbody): ?>
                        <div class="student-required-cc-block mb-4">
                            <h2 class="student-required-cc-date mb-3"><?= $key; ?></h2>
                            <div class="table-responsive d-flex justify-content-center">
                                <table class="table table-bordered student-required-cc-table w-auto align-middle text-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>時間</th>
                                            <th>必須キャリコン1</th>
                                            <th>必須キャリコン2</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($tbody as $time => $tr): ?>
                                            <tr>

                                                <td>
                                                    <?= $time; ?>
                                                </td>
                                                <?php foreach ($tr as $td): ?>
                                                    <td>
                                                        <a href="./student_cc_edit_require.php?<?= $td['booking_id'] ?>"><?= $td['student_name'] ?>
                                                        </a>
                                                    </td>

                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- 10月16日 -->
                    <!-- <div class="student-required-cc-block mb-5">
                        <h2 class="student-required-cc-date mb-3">10月16日</h2>
                        <div class="table-responsive d-flex justify-content-center">
                            <table class="table table-bordered student-required-cc-table w-auto align-middle text-center mb-0">
                                <thead>
                                    <tr>
                                        <th>時間</th>
                                        <th>必須キャリコン1</th>
                                        <th>必須キャリコン2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１０：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１１：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１２：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１４：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１５：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１６：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->

                    <div class="mt-4">
                        <a href="./index.php" class="btn btn-secondary">戻る</a>
                    </div>
                </div>
            </div>
        </section>
    </main>



    <script src="./js/script.js"></script>
</body>

</html>