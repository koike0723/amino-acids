<!-- 管理者トップページ -->
<?php
require_once __DIR__ . '/functions/functions.php';

// GETパラメータ取得・バリデーション
$course_id_filter = isset($_GET['course_id']) && is_numeric($_GET['course_id'])
  ? (int) $_GET['course_id'] : null;
$range      = in_array((int) ($_GET['range'] ?? 0), [2, 3]) ? (int) $_GET['range'] : 2;
$start_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['start_date'] ?? '')
  ? $_GET['start_date'] : date('Y-m-d');

// コース一覧（ドロップダウン用・未開始コースも含む）
$courses    = get_courses($start_date, true);
$course_map = array_column($courses, null, 'course_id');

// スケジュール一覧
$course_ids    = $course_id_filter ? [$course_id_filter] : null;
$schedule_list = get_cc_schedule_list($start_date, $range, $course_ids);

// rowspan 事前計算（月ごとの日数）
$rowspan_map = [];
foreach ($schedule_list as $year => $months) {
  foreach ($months as $month => $days) {
    $rowspan_map[$year][$month] = count($days);
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=house,notifications" />
  <link rel="stylesheet" href="./css/style.css">
  <title>-管理者- キャリコン管理</title>
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
  <main>
    <div class="wrapper">
      <p class="h1"><b>キャリコン管理</b></p>
      <form method="get" action="">
        <div class="cc-area">
          <div class="cc-content_area">
            <p class="cc-text">開催クラス</p>
            <div class="cc-select">
              <select name="course_id" class="cc-select_style">
                <option value="">すべて</option>
                <?php foreach ($courses as $course): ?>
                  <option value="<?= h($course['course_id']) ?>"
                    <?= $course_id_filter === (int) $course['course_id'] ? 'selected' : '' ?>>
                    <?= h($course['room_name']) . ' / ' . h($course['course_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="cc-content_area">
            <p class="cc-text">表示期間</p>
            <div class="cc-select">
              <select name="range" class="cc-select_style">
                <option value="2" <?= $range === 2 ? 'selected' : '' ?>>2カ月</option>
                <option value="3" <?= $range === 3 ? 'selected' : '' ?>>3カ月</option>
              </select>
            </div>
          </div>
          <div class="cc-content_area">
            <p class="cc-text">表示開始日</p>
            <div class="cc-select">
              <input type="date" name="start_date" value="<?= h($start_date) ?>" class="cc-select_style">
            </div>
          </div>
          <div class="cc-content_area">
            <p class="cc-text">&nbsp;</p>
            <button type="submit" class="ad-index-detailBtn">絞り込み</button>
          </div>
        </div>
      </form>
    </div>
    <div class="wrapper">
      <table class="ad-index-table">
        <thead class="ad-index-thead">
          <tr class="ad-index-headTr">
            <th class="ad-index-th">年</th>
            <th class="ad-index-th">月</th>
            <th class="ad-index-th">日</th>
            <th class="ad-index-th">必須キャリコン開催コース</th>
            <th class="ad-index-th">キャリコン+</th>
            <th class="ad-index-th">使用教室</th>
            <th class="ad-index-th">操作</th>
          </tr>
        </thead>
        <tbody class="ad-index-tbody">
          <?php if (empty($schedule_list)): ?>
            <tr>
              <td colspan="7" class="ad-index-td" style="text-align:center;">該当するデータがありません</td>
            </tr>
          <?php endif; ?>
          <?php foreach ($schedule_list as $year => $months): ?>
            <?php foreach ($months as $month => $days): ?>
              <?php $is_first_in_month = true; ?>
              <?php foreach ($days as $day => $data): ?>
                <?php
                $rowspan    = $rowspan_map[$year][$month];
                $date_str   = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $room_names = array_values($data['cc_list']);
                ?>
                <tr class="ad-index-tr">
                  <?php if ($is_first_in_month): ?>
                    <td rowspan="<?= $rowspan ?>" class="ad-index-td line-bold td-year"><?= $year ?></td>
                    <td rowspan="<?= $rowspan ?>" class="ad-index-td line-bold td-month"><?= (int) $month ?></td>
                  <?php $is_first_in_month = false;
                  endif; ?>
                  <td class="ad-index-td va-middle"><?= (int) $day ?></td>
                  <td class="ad-index-td va-middle"><?= implode(' / ', $room_names) ?: '—' ?></td>
                  <td class="ad-index-td cc-plus-fz va-middle">
                    <form method="post" action="php_do/cc_plus_count_do.php" style="display:inline">
                      <input type="hidden" name="date" value="<?= h($date_str) ?>">
                      <input type="hidden" name="course_id" value="<?= h($course_id_filter ?? '') ?>">
                      <input type="hidden" name="range" value="<?= h($range) ?>">
                      <input type="hidden" name="start_date" value="<?= h($start_date) ?>">
                      <select name="cc_plus_count" class="cc-plus_select" onchange="this.form.submit()">
                        <?php for ($i = 0; $i <= 5; $i++): ?>
                          <option value="<?= $i ?>" <?= $data['cc_plus_count'] === $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                      </select>
                    </form>
                  </td>
                  <td class="ad-index-td va-middle"><?= $data['line_count'] ?></td>
                  <td class="ad-index-td">
                    <a href="admin_cc_detail.php?cc_date=<?= h($date_str) ?>">
                      <button type="button" class="ad-index-detailBtn">詳細</button>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>


</html>