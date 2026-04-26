<!-- 管理者メッセージ可否画面（いらなければ削除） -->
 <?php 
 if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();
 ?>