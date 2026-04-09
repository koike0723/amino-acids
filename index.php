<?php include('./function/function.php'); ?>


<?php
// 表示したい年月を取得
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// 不正な月が来たときの補正
if ($month < 1 || $month > 12) {
    $month = date('n');
}

// その月の1日のタイムスタンプ
$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);

// 月名
$monthName = date('F', $firstDayTimestamp);

// その月の日数
$daysInMonth = date('t', $firstDayTimestamp);

// その月の1日が何曜日か（0:日曜 ～ 6:土曜）
$startWeekday = date('w', $firstDayTimestamp);

// 前月情報
$prevMonthTimestamp = mktime(0, 0, 0, $month - 1, 1, $year);
$prevYear = date('Y', $prevMonthTimestamp);
$prevMonth = date('n', $prevMonthTimestamp);
$daysInPrevMonth = date('t', $prevMonthTimestamp);

// 次月情報
$nextMonthTimestamp = mktime(0, 0, 0, $month + 1, 1, $year);
$nextYear = date('Y', $nextMonthTimestamp);
$nextMonth = date('n', $nextMonthTimestamp);

// サンプル予定データ
// 実際はDBから取得したデータをここに入れる想定
$events = [
    "{$year}-" . sprintf('%02d', $month) . "-02" => [
        ['text' => '日商簿記試験日', 'class' => 'text-dark']
    ],
    "{$year}-" . sprintf('%02d', $month) . "-09" => [
        ['text' => 'キャッチリンク', 'class' => 'text-danger']
    ],
    "{$year}-" . sprintf('%02d', $month) . "-16" => [
        ['text' => '応用基本レベル', 'class' => 'text-danger'],
        ['text' => 'IT見本サイトリンク', 'class' => 'text-primary']
    ],
];
?>

<!doctype html>
<html lang="ja">

<head>
    <title>予約一覧</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">


</head>

<body>
    <?php include('./common/header.php'); ?>

    <main role="main" class="container py-4" style="padding:60px 15px 0">
        <section class="reserve-list container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <h2 class="text-center mb-4 fs-6">予約一覧</h2>

                    <div class="d-flex flex-column gap-3">

                        <!-- 1件分 -->
                        <div class="row align-items-center gx-2">
                            <div class="col-1 text-danger text-center">•</div>
                            <div class="col-4 text-danger">2027/10/9</div>
                            <div class="col-3 text-danger">16:00~</div>
                            <div class="col-2">
                                <button type="button" class="btn btn-primary btn-sm w-100">変更</button>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-warning btn-sm w-200">キャンセル</button>
                            </div>
                        </div>

                        <!-- 2件分 -->
                        <div class="row align-items-center gx-2">
                            <div class="col-1 text-primary text-center">•</div>
                            <div class="col-4 text-primary">2027/10/16</div>
                            <div class="col-3 text-primary">10:00~</div>
                            <div class="col-2">
                                <button type="button" class="btn btn-primary btn-sm w-100">変更</button>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-warning btn-sm w-200">キャンセル</button>
                            </div>
                        </div>

                        <!-- 3件分 -->
                        <div class="row align-items-center gx-2">
                            <div class="col-1 text-danger text-center">•</div>
                            <div class="col-4 text-danger">2027/11/13</div>
                            <div class="col-3 text-danger">10:00~</div>
                            <div class="col-2">
                                <button type="button" class="btn btn-primary btn-sm w-100">変更</button>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-warning btn-sm w-200">キャンセル</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <section class="calendar-section">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">

                    <!-- 前月・次月移動 -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>" class="btn btn-outline-secondary btn-sm">
                            前の月
                        </a>
                        <a href="?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>" class="btn btn-outline-secondary btn-sm">
                            次の月
                        </a>
                    </div>

                    <!-- カレンダータイトル -->
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div class="d-flex align-items-end gap-2">
                            <span class="calendar-heading-number"><?php echo $month; ?></span>
                            <span class="calendar-heading-month"><?php echo htmlspecialchars($monthName, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="calendar-heading-year"><?php echo $year; ?></div>
                    </div>

                    <!-- カレンダー -->
                    <div class="table-responsive">
                        <table class="table table-bordered calendar-table mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-danger small">SUN</th>
                                    <th class="small">MON</th>
                                    <th class="small">TUE</th>
                                    <th class="small">WED</th>
                                    <th class="small">THU</th>
                                    <th class="small">FRI</th>
                                    <th class="text-primary small">SAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $day = 1;
                                $nextMonthDay = 1;

                                // 最大6週分表示
                                for ($week = 0; $week < 6; $week++) {
                                    echo '<tr>';

                                    for ($weekday = 0; $weekday < 7; $weekday++) {
                                        $cellIndex = $week * 7 + $weekday;

                                        // 日曜赤、土曜青
                                        $dateClass = '';
                                        if ($weekday === 0) {
                                            $dateClass = 'text-danger';
                                        } elseif ($weekday === 6) {
                                            $dateClass = 'text-primary';
                                        }

                                        // 月初前の前月日付
                                        if ($cellIndex < $startWeekday) {
                                            $prevMonthDay = $daysInPrevMonth - $startWeekday + $cellIndex + 1;

                                            echo '<td class="other-month">';
                                            echo '<span class="date-number">' . $prevMonthDay . '</span>';
                                            echo '</td>';
                                        }
                                        // 当月の日付
                                        elseif ($day <= $daysInMonth) {
                                            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);

                                            echo '<td>';
                                            echo '<span class="date-number ' . $dateClass . '">' . $day . '</span>';

                                            if (isset($events[$dateKey])) {
                                                foreach ($events[$dateKey] as $event) {
                                                    echo '<div class="event-item ' . htmlspecialchars($event['class'], ENT_QUOTES, 'UTF-8') . '">';
                                                    echo '・' . htmlspecialchars($event['text'], ENT_QUOTES, 'UTF-8');
                                                    echo '</div>';
                                                }
                                            }

                                            echo '</td>';
                                            $day++;
                                        }
                                        // 月末後の次月日付
                                        else {
                                            echo '<td class="other-month">';
                                            echo '<span class="date-number">' . $nextMonthDay . '</span>';
                                            echo '</td>';
                                            $nextMonthDay++;
                                        }
                                    }

                                    echo '</tr>';

                                    // 当月の日付を出し切って、次月も1週分以上埋めたら終了
                                    if ($day > $daysInMonth && $nextMonthDay > 7) {
                                        break;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <script src="./js/script.js"></script>
</body>

</html>