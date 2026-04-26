<!-- 必須キャリコン一覧 -->
<?php
require_once __DIR__ . '/functions/functions.php';

if (!isset($_GET['course_id']) || !ctype_digit((string) $_GET['course_id']) ||
    !isset($_GET['cc_count'])  || !ctype_digit((string) $_GET['cc_count'])) {
    header('Location: admin_course_list.php');
    exit;
}

$course_id = (int) $_GET['course_id'];
$cc_count  = (int) $_GET['cc_count'];

try {
    $course = get_course($course_id);
} catch (PDOException $e) {
    exit('コース情報の取得に失敗しました: ' . $e->getMessage());
}

if (empty($course)) {
    header('Location: admin_course_list.php');
    exit;
}

try {
    $cc_bookings = get_course_cc_bookings($course_id, $cc_count);
} catch (PDOException $e) {
    exit('キャリコン予約の取得に失敗しました: ' . $e->getMessage());
}

$db    = db_connect();
$times = $db->query('SELECT id, display_name FROM m_times ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
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
    <title>-管理者- 必須キャリコン一覧</title>
    <style>
        .cc-req-table {
            width: 100%;
            table-layout: fixed;
        }
        .cc-req-th-time,
        .cc-req-td-time {
            width: 80px;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <div class="container-fluid px-4 py-4" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">
            <h1 class="h3 font-weight-bold text-center mb-1">必須キャリコン一覧</h1>
            <p class="text-center text-muted mb-4">
                <?= h($course['room_name']) ?> / <?= h($course['course_name']) ?>&nbsp;第<?= $cc_count ?>回
            </p>

            <?php if (empty($cc_bookings)): ?>
                <p class="text-center text-muted">予約データがありません。</p>
            <?php else: ?>
                <?php foreach ($cc_bookings as $date => $time_rows): ?>
                    <?php
                    $max_lines = 1;
                    foreach ($time_rows as $bookings_in_time) {
                        $max_lines = max($max_lines, count($bookings_in_time));
                    }
                    ?>
                    <section class="cc-req-table-sec">
                        <p class="cc-req-day-text"><?= h(format_japanese_date($date)) ?></p>
                        <div class="cc-req-table-area">
                            <table class="cc-req-table">
                                <thead class="cc-req-list-thead">
                                    <tr class="cc-req-list-headTr">
                                        <th class="cc-req-list-th cc-req-th-time">時間</th>
                                        <?php for ($i = 1; $i <= $max_lines; $i++): ?>
                                            <th class="cc-req-list-th">ライン<?= $i ?></th>
                                        <?php endfor; ?>
                                    </tr>
                                </thead>
                                <tbody class="cc-req-list-tbody">
                                    <?php foreach ($times as $time): ?>
                                        <tr class="cc-req-list-tr">
                                            <td class="cc-req-list-td cc-req-td-time"><?= h($time['display_name']) ?></td>
                                            <?php $bookings = $time_rows[$time['display_name']] ?? []; ?>
                                            <?php for ($i = 0; $i < $max_lines; $i++): ?>
                                                <td class="cc-req-list-td">
                                                    <?= isset($bookings[$i]) ? h($bookings[$i]['student_name']) : '' ?>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="mt-4">
                <a href="admin_course_detail.php?course_id=<?= h($course_id) ?>" class="btn btn-secondary">詳細へ戻る</a>
            </div>
        </div>
    </main>
    <script src="./js/hamburger.js"></script>
</body>

</html>
