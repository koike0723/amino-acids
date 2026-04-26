<!-- 生徒編集実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_student_list.php');
    exit();
}

$student_id = $_POST['student_id'] ?? '';
$last_name  = trim($_POST['last_name'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$status_id  = $_POST['status_id'] ?? '';
$course_id  = $_POST['course_id'] ?? '';

if ($student_id === '' || !ctype_digit((string)$student_id)) {
    header('Location: ../admin_student_list.php?status=error');
    exit();
}

$data = [];

// 空文字でなければ更新対象に入れる
if ($last_name !== '') {
    $data['last_name'] = $last_name;
}

if ($first_name !== '') {
    $data['first_name'] = $first_name;
}

if ($status_id !== '' && ctype_digit((string)$status_id)) {
    $data['status_id'] = (int)$status_id;
}

if ($course_id !== '' && ctype_digit((string)$course_id)) {
    $data['course_id'] = (int)$course_id;
}

// 更新項目が1つもない場合
if (empty($data)) {
    header('Location: ../admin_student_edit.php?id=' . urlencode($student_id) . '&status=error');
    exit();
}

try {
    $result = update_student((int)$student_id, $data);

    if ($result) {
        header('Location: ../admin_student_detail.php?id='.$student_id);
        exit();
    } else {
        header('Location: ../admin_student_edit.php?id=' . urlencode($student_id) . '&status=error');
        exit();
    }
} catch (PDOException $e) {
    check($e);
}
?>