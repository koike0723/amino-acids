<!-- 生徒側任意キャリコン予約画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

// 選択された日付データの取得
$selected_date = $_GET['selected_date'] ?? '';
//$_SESSION['selected_date'] = $_GET['selected_date'];
$booking_id = $_GET['booking_id'] ?? '';
?>

<?php
try {
    $db = db_connect();
    $sql = 'SELECT * FROM m_times';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $date = get_cc_plus_time_table($selected_date);
} catch (PDOException $e) {
    check($e);
}
?>

<!doctype html>
<html lang="ja">

<head>
    <title>予約可能枠一覧</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />

</head>

<body>
    <?php include('./inc/student_header.php'); ?>
    <?php
    $login_student = $_SESSION['student_id'];
    if(!isset($_SESSION['student_id'])){
        header('location:./inc/login.php');
        exit();
    }else{
        $stundent = get_student($login_student);
    }
    ?>

    <main class="container py-5">
        <section class="s-career-frame-section">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 text-center">

                    <h1 class="s-career-frame-title mb-4">キャリコンプラス一覧</h1>
                    <p class="s-career-frame-date mb-4"><?php echo h(format_japanese_date($selected_date)); ?></p>

                    <div class="table-responsive d-flex justify-content-center">
                        <table class="table table-bordered s-career-frame-table w-auto align-middle text-center">
                            <thead>
                                <tr>
                                    <th>時間</th>
                                    <th>任意キャリコン枠</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($timetables as $key => $time): ?>
                                    <tr>
                                        <td><?= h($time['display_name']); ?></td>
                                        <?php if ($date[$key + 1]): ?>
                                            <td class="s-frame-available js-click" onclick="location.href='student_reserve_edit.php?timeid=<?= h($time['id']) ?>&selected_date=<?= $selected_date ?>&time=<?= h($time['start_time']) ?><?= !empty($booking_id) ? '&booking_id='. $booking_id : '' ?>'" style="cursor: pointer;">
                                                〇
                                            </td>
                                        <?php else: ?>
                                            <td class="s-frame-unavailable js-click">✕</td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        <a href="./index.php" class="btn btn-secondary">戻る</a>
                    </div>

                </div>
            </div>
        </section>
    </main>



    <script src="./js/script.js"></script>
</body>

</html>