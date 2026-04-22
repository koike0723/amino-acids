<!-- 生徒・管理者ログアウト実行処理 -->
<?php 
require_once __DIR__ . '/../functions/functions.php';

session_start();
if (isset($_SESSION["student_id"])) {
    $_SESSION = array();
    session_destroy();
}

header("location:../inc/login.php");
exit();

?>