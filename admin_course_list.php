<!-- http://localhost:8080/amino-acids/admin_course_list.php -->
<!-- コース一覧画面 -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();
?>

<?php
/////////////////////////////////////////////////
/////////////////////GET通信処理/////////////////
/////////////////////////////////////////////////
$date        = isset($_GET['date'])        && $_GET['date']        !== '' ? $_GET['date']        : date('Y-m-d');
$room_id     = isset($_GET['room_id'])     && $_GET['room_id']     !== '' ? $_GET['room_id']     : null;
$category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? $_GET['category_id'] : null;
?>
<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
  $courses = get_courses($date, false, $room_id, $category_id);
} catch (PDOException $e) {
  exit('情報の取得に失敗しました: ' . $e->getMessage());
}
try {
  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
  $db = new PDO($dsn, DB_USER, DB_PASS);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // SQL
  $sql = 'SELECT id,name FROM m_courses_categories';
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  exit('訓練タイプの取得に失敗しました: ' . $e->getMessage());
}
try {
  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
  $db = new PDO($dsn, DB_USER, DB_PASS);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // SQL
  $sql = 'SELECT id,name FROM m_rooms';
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  exit('教室データ（m_rooms）の取得に失敗しました: ' . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>コース一覧</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

  <main>
    <div class="container-fluid px-4 py-4">
      <h1 class="h3 font-weight-bold text-center mb-4">コース一覧</h1>

      <div class="text-right mb-3">
        <a href="admin_course_add.php" class="btn btn-primary">コース追加</a>
      </div>

      <div class="card mb-4 ad-index-filter-card">
        <div class="card-body">
          <form action="admin_course_list.php" method="get">
            <div class="form-row">
              <div class="col-12 col-md-3 mb-3">
                <label for="room_id" class="form-label">教室名</label>
                <select name="room_id" id="room_id" class="form-control">
                  <option value="" <?= ($room_id == "") ? "selected" : "" ?>>全表示</option>
                  <?php foreach ($rooms as $room): ?>
                    <?php if ($room["id"] != 13): ?>
                      <option value="<?php echo $room["id"]; ?>" <?= ($room_id == $room["id"]) ? "selected" : "" ?>><?php echo $room["name"]; ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12 col-md-4 mb-3">
                <label for="category_id" class="form-label">訓練カテゴリー</label>
                <select name="category_id" id="category_id" class="form-control">
                  <option value="" selected <?= ($category_id == "") ? "selected" : "" ?>>全表示</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category["id"]; ?>" <?= ($category_id == $category["id"]) ? "selected" : "" ?>><?php echo $category["name"]; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12 col-md-3 mb-3">
                <label for="date" class="form-label">基準日</label>
                <input type="date" name="date" id="date" value="<?= $date ?>" class="form-control">
              </div>
            </div>
            <div class="d-flex">
              <button type="submit" class="btn btn-info mr-2">絞り込む</button>
              <a href="admin_course_list.php" class="btn btn-secondary">絞り込み解除</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered mb-0 ad-index-table">
              <thead>
                <tr>
                  <th class="ad-index-th">教室名</th>
                  <th class="ad-index-th">訓練名</th>
                  <th class="ad-index-th">訓練日時</th>
                  <th class="ad-index-th">訓練タイプ</th>
                  <th class="ad-index-th">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($courses as $course): ?>
                  <tr>
                    <td class="ad-index-td"><?php echo $course["room_name"] ?></td>
                    <td class="ad-index-td"><?php echo $course["course_name"] ?></td>
                    <td class="ad-index-td"><?php echo format_japanese_date($course["start_date"]) ?>～<?php echo format_japanese_date($course["end_date"]) ?></td>
                    <td class="ad-index-td"><?php echo $course["category_name"] ?></td>
                    <td class="ad-index-td">
                      <a class="btn btn-info mx-1 my-1" href='admin_course_detail.php?course_id=<?= $course["course_id"] ?>'>詳細</a>
                      <a class="btn btn-success mx-1 my-1" href='admin_course_edit.php?course_id=<?= $course["course_id"] ?>'>編集</a>
                      <button type="button" class="btn btn-danger mx-1 my-1"
                          data-toggle="modal" data-target="#deleteModal"
                          data-message="コース「<?= h($course['course_name']) ?>」を削除してもよいですか？">削除</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <a class="btn btn-secondary px-3 py-2" href="./admin_index.php">トップへ戻る</a>
      </div>
    </div>
  </main>
  <!-- kan-to-do:余力があればリアルタイムで変わるようにする -->
  <?php require_once __DIR__ . '/inc/delete_modal.php'; ?>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script src="./js/script.js"></script>
  <script src="./js/admin_course_search.js"></script>
  <script src="./js/hamburger.js"></script>
  <script>
    $('#deleteModal').on('show.bs.modal', function (event) {
      var message = $(event.relatedTarget).data('message');
      $(this).find('#deleteModalMessage').text(message);
    });
  </script>
</body>

</html>