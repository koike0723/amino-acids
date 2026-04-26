<!-- 生徒編集画面 -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();

$id = $_GET['id'] ?? '';

if (!empty($id)) {
    try {
        $student = get_student($id);
        $courses = get_courses(null, true);
    } catch (PDOException $e) {
        check($e);
    }
} else {
    header('Location: admin_student_list.php');
    exit();
}

$db = db_connect();
$sql = 'SELECT * FROM m_student_status ORDER BY id ASC, name ASC';
$stmt = $db->prepare($sql);
$stmt->execute();
$student_status = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 生徒編集</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">生徒編集</h1>

        <form action="./php_do/student_edit_do.php" method="post">
            <input type="hidden" name="student_id" value="<?= h($student['student_id']); ?>">

            <div class="col-6 mb-3">
                <label class="form-label">姓</label>
                <input type="text" name="last_name" class="form-control" value="<?= h($student['last_name'] ?? ''); ?>">
            </div>
            <div class="col-6 mb-3">
                <label class="form-label">名</label>
                <input type="text" name="first_name" class="form-control" value="<?= h($student['first_name'] ?? ''); ?>">
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">コース名</label>
                <select name="course_id" class="form-control">
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= h($course['course_id']); ?>" <?= ($student['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                            <?= h($course['room_name']) . ' / ' . h($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 mb-3">
                <label class="form-label">出席番号</label>
                <p class="form-control"><?= h($student["number"]); ?></p>
            </div>

            <div class="col-6 mb-3">
                <label class="form-label">状態</label>
                <select name="status_id" class="form-control">
                    <?php foreach ($student_status as $status): ?>
                        <option value="<?= h($status['id']); ?>" <?= ($student['status_id'] == $status['id']) ? 'selected' : ''; ?>>
                            <?= h($status['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 d-flex mt-4 mb-5" style="gap: 12px;">
                <a href="./admin_student_detail.php?id=<?= h($student['student_id']); ?>" class="btn btn-secondary px-3 py-2">詳細に戻る</a>
                <button type="submit" class="btn btn-primary px-3 py-2" style="margin-top: 10px;">編集完了</button>
            </div>

        </form>
    </div>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>
