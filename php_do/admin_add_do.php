<?php
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_admin_list.php');
    exit();
}

$last_name  = trim($_POST['last_name']  ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$login_id   = trim($_POST['login_id']   ?? '');
$password   = $_POST['password'] ?? '';

// 必須項目チェック
if ($last_name === '' || $first_name === '' || $login_id === '' || $password === '') {
    header('Location: ../admin_admin_add.php?status=error');
    exit();
}

// login_id 重複チェック
$db   = db_connect();
$stmt = $db->prepare('SELECT id FROM m_admins WHERE login_id = :login_id');
$stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->fetch()) {
    header('Location: ../admin_admin_add.php?status=login_id_duplicate');
    exit();
}

$data = [
    'last_name'  => $last_name,
    'first_name' => $first_name,
    'login_id'   => $login_id,
    'password'   => $password,
];

try {
    $result = add_admin($data);

    if ($result) {
        header('Location: ../admin_admin_list.php?status=success');
        exit();
    } else {
        header('Location: ../admin_admin_add.php?status=error');
        exit();
    }
} catch (PDOException $e) {
    check($e);
}
