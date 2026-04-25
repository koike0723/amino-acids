<!-- 管理者メッセージ一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

/////////////////////////////////////////////////
// GET通信処理
/////////////////////////////////////////////////
$status_filter = $_GET['status_filter'] ?? 'label_unsolved';
$status_label_filter = '';
$status_id_filter    = '';
$type_id_filter      = $_GET['type_id']      ?? '';

if (str_starts_with($status_filter, 'label_')) {
  $status_label_filter = str_replace('label_', '', $status_filter); // 'unsolved' or 'solved'
} elseif (str_starts_with($status_filter, 'status_')) {
  $status_id_filter = str_replace('status_', '', $status_filter);   // '1'～'4'
}

// フィルタ条件を組み立て
$filters = [];

// 状態（status_id）が指定されていれば優先、なければ対応状況（status_label）で絞り込む
if ($status_id_filter !== '') {
  $filters['status_id'] = (int) $status_id_filter;
} elseif ($status_label_filter !== '') {
  $filters['status_id'] = $status_label_filter === 'unsolved' ? [1, 2] : [3, 4];
}

if ($type_id_filter !== '') {
  $filters['type_id'] = (int) $type_id_filter;
}

try {
  $requests = get_cc_requests($filters);
  $db = db_connect();
  $statuses = $db->query('SELECT id, name FROM m_request_status ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
  $types    = $db->query('SELECT id, name FROM m_request_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $requests = [];
}

// 絞り込み用マスターデータ（ハードコード）
$status_labels = [
  ['value' => 'unsolved', 'label' => '未解決'],
  ['value' => 'solved',   'label' => '対応済み'],
];
?>

<!doctype html>
<html lang="ja">

<head>
  <title>予約一覧</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <?php require_once('./inc/admin_header.php'); ?>

  <main>
    <div class="container-fluid px-4 py-4">
      <h1 class="h3 font-weight-bold text-center mb-4">メッセージ一覧</h1>

      <!-- 絞り込みフォーム -->
      <div class="card mb-4 ad-index-filter-card">
        <div class="card-body">
          <form action="admin_message.php" method="get">
            <div class="form-row">
              <div class="col-12 col-md-5 mb-3">
                <label for="status_filter" class="form-label">対応状況 / 状態</label>
                <select name="status_filter" id="status_filter" class="form-control">
                  <option value="">全表示</option>

                  <optgroup label="── 対応状況 ──">
                    <?php foreach ($status_labels as $sl): ?>
                      <option value="label_<?= $sl['value'] ?>"
                        <?= ($status_label_filter === $sl['value'] && $status_id_filter === '') ? 'selected' : '' ?>>
                        <?= $sl['label'] ?>
                      </option>
                    <?php endforeach; ?>
                  </optgroup>

                  <optgroup label="── 状態 ──">
                    <?php foreach ($statuses as $s): ?>
                      <option value="status_<?= $s['id'] ?>"
                        <?= ($status_id_filter === (string)$s['id']) ? 'selected' : '' ?>>
                        <?= $s['name'] ?>
                      </option>
                    <?php endforeach; ?>
                  </optgroup>
                </select>
              </div>

              <div class="col-12 col-md-3 mb-3">
                <label for="type_id" class="form-label">種類</label>
                <select name="type_id" id="type_id" class="form-control">
                  <option value="">全表示</option>
                  <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>"
                      <?= ((string)$type_id_filter === (string)$t['id']) ? 'selected' : '' ?>>
                      <?= $t['name'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="d-flex">
              <input type="submit" value="絞り込む" class="btn btn-info mr-2">
              <a href="admin_message.php" class="btn btn-secondary">絞り込み解除</a>
            </div>
          </form>
        </div>
      </div>

      <!-- テーブル -->
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered mb-0 ad-index-table">
              <thead>
                <tr>
                  <th class="ad-index-th">対応状況</th>
                  <th class="ad-index-th">状態</th>
                  <th class="ad-index-th">種類</th>
                  <th class="ad-index-th">コース</th>
                  <th class="ad-index-th">申請者</th>
                  <th class="ad-index-th">申請日時</th>
                  <th class="ad-index-th">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($requests as $request): ?>
                  <tr>
                    <td class="ad-index-td"><?php echo $request['status_label']; ?></td>
                    <td class="ad-index-td"><?php echo $request['status_name']; ?></td>
                    <td class="ad-index-td"><?php echo $request['type_name']; ?></td>
                    <td class="ad-index-td"><?php echo $request['room_name']; ?></td>
                    <td class="ad-index-td"><?php echo $request['student_name']; ?></td>
                    <?php $datetime = date('Y/m/d H:i', strtotime($request['created_at'])); ?>
                    <td class="ad-index-td"><?php echo $datetime; ?></td>
                    <td class="ad-index-td">
                      <a class="btn btn-info mx-1 my-1" href="./admin_message_detail.php?request_id=<?= $request['request_id'] ?>&<?= h($_SERVER['QUERY_STRING']) ?>">詳細</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <a href="./admin_index.php" class="btn btn-secondary">トップへ戻る</a>
      </div>
    </div>
  </main>

  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>

</html>
