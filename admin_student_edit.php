<!-- 生徒編集画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

//$id = $_GET['id'];

//if (!empty($id)) {
try {
    $student = get_student(1); //GETで取得した生徒IDを使う
    $courses = get_courses();
} catch (PDOException $e) {
    check($e);
}
//} 
// else {
//     header('location:admin_student_list.php');
//     exit();
// }
?>
<?php
$db = db_connect();
$sql = 'SELECT * FROM m_student_status ORDER BY id ASC,name ASC';
$stmt = $db->prepare($sql);
$stmt->execute();
$student_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

// $db2 = db_connect();
// $sql2 = 'SELECT * FROM m_courses ORDER BY id ASC,name ASC';
// $stmt2 = $db->prepare($sql2);
// $stmt2->execute();
// $courses = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 生徒編集</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <p class="student-edit-h1">生徒編集</p>
        <div class="student-edit-area">
            <div class="student-edit-flex">
                <div class="style-area" style="padding-block-end: 1.3rem;">
                    <label class="student-edit-title">
                        <span class="student-edit-span">名前</span>
                        <input type="text" name="student-edit-name" class="student-edit-name" value="<?= $student['student_name']; ?>">
                    </label>
                </div>
                <label class="student-edit-title">
                    <span class="student-edit-span">訓練名</span>
                    <label for="student-edit-option" class="student-edit-option-label">
                        <select name="student-edit-option-select" id="student-edit-option-select" class="student-edit-option-select" style="width: fit-content;">
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= h($course['course_id']); ?>" <?php echo $student['course_id'] === $course['course_id'] ? "selected" : ''; ?>><?= h($course['course_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
            </div>
        </div>
        <div class="student-edit-option-area">
            <div class="student-edit-option-flex">
                <div class="student-edit-option-con-flex">
                    <span class="student-edit-option-span">状態</span>
                    <label for="student-edit-option" class="student-edit-option-label">
                        <select name="student-edit-option-select" id="student-edit-option-select" class="student-edit-option-select">
                            <?php foreach ($student_status as $status): ?>
                                <option value="<?= h($status['id']); ?>" <?php echo $student['status_id'] === $status['id'] ? "selected" : ''; ?>><?= $status['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </div>
        </div>
        <div class="student-edit-controle-area">
            <div class="student-edit-controle-flex">
                <a href="#">
                    <button type="btn" class="seb-prev">キャンセル</button>
                </a>
                <a href="#">
                    <button type="btn" class="seb-next">確定</button>
                </a>
            </div>
        </div>
    </main>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>