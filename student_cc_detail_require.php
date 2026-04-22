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


</head>

<body>
    <?php include('./inc/student_header.php'); ?>
    <?php
    $student = $_SESSION['student_id'];
    if (!isset($_SESSION['student_id'])) {
        header('location:./inc/login.php');
        exit();
    }
    check($student);
    ?>

    <main class="container py-4">
        <section class="student-required-cc-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 text-center">

                    <h1 class="student-required-cc-title mb-4">4月 必須キャリコン一覧</h1>

                    <!-- 時間帯をクリックすると画面遷移するようにしてます -->
                    <!-- クリックしたときにどの日付かデータを送れるようにしたいなら以下ｐｈｐ -->
                    <!-- <a href="./student_cc_edit_require.php?date=2027-10-09&time=16:00" class="student-required-cc-time-link">１６：００〜</a> -->

                    <div class="student-required-cc-block mb-4">
                        <h2 class="student-required-cc-date mb-3">10月9日</h2>

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
                                        <td>岸本</td>
                                        <td>兵藤</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１１：００〜</a>
                                        </td>
                                        <td>岸本</td>
                                        <td>兵藤</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１２：００〜</a>
                                        </td>
                                        <td>岸本</td>
                                        <td>兵藤</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１４：００〜</a>
                                        </td>
                                        <td>岸本</td>
                                        <td>兵藤</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１５：００〜</a>
                                        </td>
                                        <td>岸本</td>
                                        <td>兵藤</td>
                                    </tr>
                                    <tr>
                                        <td class="student-required-cc-selected">
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１６：００〜</a>
                                        </td>
                                        <td class="student-required-cc-selected">岸本</td>
                                        <td>兵藤</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 10月16日 -->
                    <div class="student-required-cc-block mb-5">
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
                    </div>

                    <div class="mt-4">
                        <a href="./student_reserve_edit.php" class="btn btn-secondary">戻る</a>
                    </div>

                </div>
            </div>
        </section>
    </main>



    <script src="./js/script.js"></script>
</body>

</html>