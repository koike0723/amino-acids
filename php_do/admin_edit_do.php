<?php
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_admin_list.php');
    exit();
}

$admin_id   = $_POST['admin_id'] ?? '';
$last_name  = trim($_POST['last_name']  ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$login_id   = trim($_POST['login_id']   ?? '');
$password   = $_POST['password'] ?? '';

if ($admin_id === '' || !ctype_digit((string)$admin_id)) {
    header('Location: ../admin_admin_list.php?status=error');
    exit();
}

$admin_id = (int)$admin_id;

// 必須項目チェック
if ($last_name === '' || $first_name === '' || $login_id === '') {
    header('Location: ../admin_admin_edit.php?id=' . $admin_id . '&status=error');
    exit();
}

// login_id 重複チェック（自分以外）
$db   = db_connect();
$stmt = $db->prepare('SELECT id FROM m_admins WHERE login_id = :login_id AND id != :admin_id');
$stmt->bindValue(':login_id',  $login_id,  PDO::PARAM_STR);
$stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$stmt->execute();
if ($stmt->fetch()) {
    header('Location: ../admin_admin_edit.php?id=' . $admin_id . '&status=login_id_duplicate');
    exit();
}

$data = [
    'last_name'  => $last_name,
    'first_name' => $first_name,
    'login_id'   => $login_id,
];

// パスワードが入力された場合のみ更新対象に含める
if ($password !== '') {
    $data['password'] = $password;
}

try {
    $result = update_admin($admin_id, $data);

    if ($result) {
        header('Location: ../admin_admin_detail.php?id=' . $admin_id);
        exit();
    } else {
        header('Location: ../admin_admin_edit.php?id=' . $admin_id . '&status=error');
        exit();
    }
} catch (PDOException $e) {
    check($e);
}
