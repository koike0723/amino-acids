<!-- ログイン実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

session_start();

//ログインしている場合


//login.phpからのデータを取得
if (!empty($_POST)) {
    $name = $_POST['name'];
    $passwords = $_POST['password'];

    try {
        $login_result = student_login($name, $passwords);
    } catch (PDOException $e) {
        check($e);
    }
    if ($login_result) {
        header('location:../index.php');
        exit();
    } else {
        header('location:../inc/login.php');
        exit();
    }
} else {
    header('location:../inc/login.php');
    exit();
}

?>