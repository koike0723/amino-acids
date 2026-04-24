<!-- 管理者メッセージ一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

/////////////////////////////////////////////////
// GET通信処理
/////////////////////////////////////////////////
$status_filter       = $_GET['status_filter'] ?? '';
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
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
</head>

<body>
  <?php require_once('./inc/admin_header.php'); ?>

  <main class="container py-5">
    <section class="message-section">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10 text-center">
          <h1 class="mb-5 fs-2">メッセージ一覧</h1>

          <!-- 絞り込みフォーム -->
          <form action="admin_message.php" method="get" class="row mb-4 justify-content-center">
            <div class="col-auto">
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

            <div class="col-auto">
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

            <div class="col-auto d-flex align-items-end">
              <input type="submit" value="絞り込む" class="btn btn-info">
            </div>
          </form>
          <!-- /絞り込みフォーム -->

          <div class="table-responsive d-flex justify-content-center">
            <table class="table table-bordered align-middle text-center w-auto">
              <thead class="table-secondary">
                <tr>
                  <th scope="col">対応状況</th>
                  <th scope="col">状態</th>
                  <th scope="col">種類</th>
                  <th scope="col">コース</th>
                  <th scope="col">申請者</th>
                  <th scope="col">申請日時</th>
                  <th scope="col">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($requests as $request): ?>
                  <tr>
                    <td><?php echo $request['status_label']; ?></td>
                    <td><?php echo $request['status_name']; ?></td>
                    <td><?php echo $request['type_name']; ?></td>
                    <td><?php echo $request['room_name']; ?></td>
                    <td><?php echo $request['student_name']; ?></td>
                    <?php $datetime = date('Y/m/d H:i', strtotime($request['created_at'])); ?>
                    <td><?php echo $datetime; ?></td>
                    <td>
                      <a href="./admin_message_detail.php?request_id=<?= $request['request_id'] ?>" class="btn btn-success btn-sm">詳細</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="mt-5">
            <a href="./admin_index.php" class="btn btn-secondary">戻る</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
</body>

</html>