<!-- 管理者側 生徒一覧画面 -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();

/* -----------------------------
   絞り込み条件の受け取り
----------------------------- */
$date      = $_GET['date']   ?? date('Y-m-d');
$status_id = $_GET['status'] ?? '';
$course_id = $_GET['course'] ?? '';
$date_display = $date;

/* -----------------------------
   フィルタを組み立てて生徒一覧取得
----------------------------- */
$filters = [];
$filters['date'] = $date;
if ($status_id !== '') $filters['status_id'] = (int)$status_id;
if ($course_id !== '') $filters['course_id'] = (int)$course_id;

try {
    $students = get_students($filters);
} catch (PDOException $e) {
    check($e);
}

$db = db_connect();

/* ステータス一覧（ドロップダウン用） */
$statuses = $db->query('SELECT id, name FROM m_student_status ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

/* コース一覧（ドロップダウン用） */
$courses = get_courses();
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
  <title>-管理者- 生徒一覧</title>
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
  <main>
    <div class="container-fluid px-4 py-4">
      <h1 class="h3 font-weight-bold text-center mb-4">生徒一覧</h1>

      <div class="text-right mb-3">
        <a href="./admin_student_add.php" class="btn btn-primary mr-2">生徒追加</a>
        <a href="./admin_student_add_bulk.php" class="btn btn-primary">生徒一括追加</a>
      </div>

      <!-- 生徒絞り込み機能 -->
      <div class="card mb-4 ad-index-filter-card">
        <div class="card-body">
          <form action="admin_student_list.php" method="get">
            <div class="form-row">
              <div class="col-12 col-md-5 col-lg-4 mb-3">
                <label for="course" class="form-label">コース</label>
                <select name="course" id="course" class="custom-select">
                  <option value="">全表示</option>
                  <?php foreach ($courses as $course_item): ?>
                    <option value="<?= h($course_item["course_id"]); ?>" <?= ($course_id === (string)$course_item["course_id"]) ? 'selected' : ''; ?>>
                      <?= h($course_item["room_name"]) . ' / ' . h($course_item["course_name"]); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12 col-md-3 col-lg-2 mb-3">
                <label for="status" class="form-label">状態</label>
                <select name="status" id="status" class="custom-select">
                  <option value="">全表示</option>
                  <?php foreach ($statuses as $s): ?>
                    <option value="<?= h($s['id']); ?>" <?= ($status_id === (string)$s['id']) ? 'selected' : ''; ?>>
                      <?= h($s['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12 col-md-3 col-lg-3 mb-3">
                <label for="date" class="form-label">基準日</label>
                <input type="date" name="date" id="date" value="<?= h($date_display); ?>" class="form-control">
              </div>
            </div>

            <div class="d-flex">
              <button type="submit" class="btn btn-info mr-2">絞り込む</button>
              <a href="admin_student_list.php" class="btn btn-secondary">絞り込み解除</a>
            </div>
          </form>
        </div>
      </div>

      <!-- 生徒一覧テーブル -->
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered mb-0 ad-index-table">
              <thead>
                <tr>
                  <th class="col-1 ad-index-th">コース教室</th>
                  <th class="col-2 ad-index-th">コース名</th>
                  <th class="col-4 ad-index-th">生徒名</th>
                  <th class="col-1 ad-index-th">状態</th>
                  <th class="col-2 ad-index-th">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($students)): ?>
                  <tr>
                    <td colspan="5" class="ad-index-td">該当する生徒はいません。</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($students as $student): ?>
                    <tr>
                      <td class="ad-index-td"><?= h($student['room_name']); ?></td>
                      <td class="ad-index-td"><?= h($student['course_name']); ?></td>
                      <td class="ad-index-td"><?= h($student['student_name']); ?></td>
                      <td class="ad-index-td"><?= h($student['status_name']); ?></td>
                      <td class="ad-index-td">
                        <a class="btn btn-info mx-1 my-1" href="./admin_student_detail.php?id=<?= h($student['student_id']); ?>">詳細</a>
                        <a class="btn btn-success mx-1 my-1" href="./admin_student_edit.php?id=<?= h($student['student_id']); ?>">編集</a>
                        <button type="button" class="btn btn-danger mx-1 my-1"
                            data-toggle="modal" data-target="#deleteModal"
                            data-message="生徒「<?= h($student['student_name']) ?>」を削除してもよいですか？">削除</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <a href="./admin_index.php" class="btn btn-secondary px-3 py-2">トップへ戻る</a>
      </div>
    </div>
  </main>
  <?php require_once __DIR__ . '/inc/delete_modal.php'; ?>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script src="./js/script.js"></script>
  <script src="./js/hamburger.js"></script>
  <script>
    $('#deleteModal').on('show.bs.modal', function (event) {
      var message = $(event.relatedTarget).data('message');
      $(this).find('#deleteModalMessage').text(message);
    });
  </script>
</body>

</html>
