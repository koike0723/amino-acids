<!-- 生徒編集画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

$id = $_GET['id'] ?? '';

if (!empty($id)) {
    try {
        $student = get_student($id);
        $courses = get_courses();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 生徒編集</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

    <main>
        <p class="student-edit-h1">生徒編集</p>
        <?php check($student); ?>
        <form action="./php_do/student_edit_do.php" method="post">
            <input type="hidden" name="student_id" value="<?= h($student['student_id']); ?>">

            <div class="student-edit-area">
                <div class="student-edit-flex">
                    <div class="style-area" style="padding-block-end: 1.3rem;">
                        <label class="student-edit-title">
                            <span class="student-edit-span">姓</span>
                            <input
                                type="text"
                                name="last_name"
                                class="student-edit-name"
                                value="<?= h($student['last_name'] ?? ''); ?>">
                        </label>

                        <label class="student-edit-title">
                            <span class="student-edit-span">名</span>
                            <input
                                type="text"
                                name="first_name"
                                class="student-edit-name"
                                value="<?= h($student['first_name'] ?? ''); ?>">
                        </label>
                    </div>

                    <label class="student-edit-title">
                        <span class="student-edit-span">訓練名</span>
                        <select name="course_id" class="student-edit-option-select" style="width: fit-content;">
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= h($course['course_id']); ?>" <?= ($student['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                                    <?= h($course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </div>

            <div class="student-edit-option-area">
                <div class="student-edit-option-flex">
                    <div class="student-edit-option-con-flex">
                        <span class="student-edit-option-span">状態</span>
                        <label class="student-edit-option-label">
                            <select name="status_id" class="student-edit-option-select">
                                <?php foreach ($student_status as $status): ?>
                                    <option value="<?= h($status['id']); ?>" <?= ($student['status_id'] == $status['id']) ? 'selected' : ''; ?>>
                                        <?= h($status['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                </div>
            </div>

            <div class="student-edit-controle-area">
                <div class="student-edit-controle-flex">
                    <a href="./admin_student_detail.php?id=<?= h($student['student_id']); ?>">
                        <button type="button" class="seb-prev">キャンセル</button>
                    </a>

                    <button type="submit" class="seb-next">確定</button>
                </div>
            </div>
        </form>
    </main>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>