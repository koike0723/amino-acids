<!-- 生徒・管理者ログアウト実行処理 -->
<?php 
require_once __DIR__ . '/../functions/functions.php';

session_start();
if (isset($_SESSION['admin_id'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: ../inc/admin_login.php');
} else {
    $_SESSION = array();
    session_destroy();
    header('Location: ../inc/login.php');
}
exit();

?>