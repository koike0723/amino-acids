<!-- 管理者ログイン実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';
session_start();

$login_id = $_POST['login_id'] ?? '';
$password = $_POST['password'] ?? '';

if (admin_login($login_id, $password)) {
    header('Location: ../admin_index.php');
    exit();
} else {
    header('Location: ../inc/admin_login.php');
    exit();
}
?>