<!-- 管理者トップページ -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();

// GETパラメータ取得・バリデーション
$course_id_filter = isset($_GET['course_id']) && is_numeric($_GET['course_id'])
  ? (int) $_GET['course_id'] : null;
$range      = in_array((int) ($_GET['range'] ?? 0), [2, 3, 4, 6, 12]) ? (int) $_GET['range'] : 2;
$start_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['start_date'] ?? '')
  ? $_GET['start_date'] : date('Y-m-d');

// コース一覧（ドロップダウン用・未開始コースも含む）
$courses    = get_courses($start_date, true);
$course_map = array_column($courses, null, 'course_id');

// スケジュール一覧
$course_ids    = $course_id_filter ? [$course_id_filter] : null;
$schedule_list = get_cc_schedule_list($start_date, $range, $course_ids);

// rowspan 事前計算（月ごとの日数・年ごとの合計日数）
$rowspan_map = [];
$year_rowspan_map = [];
foreach ($schedule_list as $year => $months) {
  $year_total = 0;
  foreach ($months as $month => $days) {
    $rowspan_map[$year][$month] = count($days);
    $year_total += count($days);
  }
  $year_rowspan_map[$year] = $year_total;
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
    <div class="container-fluid px-4 py-4">
      <h1 class="h3 font-weight-bold text-center mb-4">キャリコン管理</h1>

      <!-- 絞り込みカード -->
      <div class="card mb-4 ad-index-filter-card">
        <div class="card-body">
          <form method="get" action="">
            <div class="form-row">
              <div class="form-group col-md-4 mb-3">
                <label class="mb-1">開催コース</label>
                <select name="course_id" class="form-control">
                  <option value="">すべて</option>
                  <?php foreach ($courses as $course): ?>
                    <option value="<?= h($course['course_id']) ?>"
                      <?= $course_id_filter === (int) $course['course_id'] ? 'selected' : '' ?>>
                      <?= h($course['room_name']) . ' / ' . h($course['course_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-2 mb-3">
                <label class="mb-1">表示期間</label>
                <select name="range" class="form-control">
                  <option value="2" <?= $range === 2 ? 'selected' : '' ?>>2カ月</option>
                  <option value="3" <?= $range === 3 ? 'selected' : '' ?>>3カ月</option>
                  <option value="4" <?= $range === 4 ? 'selected' : '' ?>>4カ月</option>
                  <option value="6" <?= $range === 6 ? 'selected' : '' ?>>6カ月</option>
                  <option value="12" <?= $range === 12 ? 'selected' : '' ?>>1年</option>
                </select>
              </div>
              <div class="form-group col-md-3 mb-3">
                <label class="mb-1">表示開始日</label>
                <input type="date" name="start_date" value="<?= h($start_date) ?>" class="form-control">
              </div>
            </div>
            <div class="d-flex">
              <button type="submit" class="btn btn-info mr-2">絞り込み</button>
              <a href="admin_index.php" class="btn btn-secondary">絞り込み解除</a>
            </div>
          </form>
        </div>
      </div>

      <!-- スケジュールテーブル -->
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered mb-0 ad-index-table">
              <thead>
                <tr>
                  <th class="ad-index-th">年</th>
                  <th class="ad-index-th">月</th>
                  <th class="ad-index-th">日</th>
                  <th class="ad-index-th">必須キャリコン開催コース</th>
                  <th class="ad-index-th">キャリコン+</th>
                  <th class="ad-index-th">使用教室</th>
                  <th class="ad-index-th">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($schedule_list)): ?>
                  <tr>
                    <td colspan="7" class="text-center py-3">該当するデータがありません</td>
                  </tr>
                <?php endif; ?>
                <?php foreach ($schedule_list as $year => $months): ?>
                  <?php $is_first_in_year = true; ?>
                  <?php foreach ($months as $month => $days): ?>
                    <?php $is_first_in_month = true; ?>
                    <?php foreach ($days as $day => $data): ?>
                      <?php
                      $date_str   = sprintf('%04d-%02d-%02d', $year, $month, $day);
                      $room_names = array_values($data['cc_list']);
                      ?>
                      <tr>
                        <?php if ($is_first_in_year): ?>
                          <td rowspan="<?= $year_rowspan_map[$year] ?>" class="ad-index-td line-bold td-year"><?= $year ?></td>
                          <?php $is_first_in_year = false; ?>
                        <?php endif; ?>
                        <?php if ($is_first_in_month): ?>
                          <td rowspan="<?= $rowspan_map[$year][$month] ?>" class="ad-index-td line-bold td-month"><?= (int) $month ?></td>
                          <?php $is_first_in_month = false; ?>
                        <?php endif; ?>
                        <td class="ad-index-td va-middle"><?= (int) $day ?></td>
                        <td class="ad-index-td va-middle"><?= implode(' / ', $room_names) ?: '—' ?></td>
                        <td class="ad-index-td cc-plus-fz va-middle">
                          <form method="post" action="php_do/cc_plus_count_do.php" style="display:inline">
                            <input type="hidden" name="date" value="<?= h($date_str) ?>">
                            <input type="hidden" name="course_id" value="<?= h($course_id_filter ?? '') ?>">
                            <input type="hidden" name="range" value="<?= h($range) ?>">
                            <input type="hidden" name="start_date" value="<?= h($start_date) ?>">
                            <select name="cc_plus_count" class="form-control cc-plus_select d-inline-block w-auto" onchange="this.form.submit()">
                              <?php for ($i = 0; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= $data['cc_plus_count'] === $i ? 'selected' : '' ?>><?= $i ?></option>
                              <?php endfor; ?>
                            </select>
                          </form>
                        </td>
                        <td class="ad-index-td va-middle"><?= $data['line_count'] ?></td>
                        <td class="ad-index-td">
                          <a class="btn btn-info mx-1 my-1" href="admin_cc_detail.php?cc_date=<?= h($date_str) ?>">詳細</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </main>
  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>


</html>
