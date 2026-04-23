<!-- 生徒側ヘッダー -->
<?php session_start(); ?>
<header class="site-header">
    <div class="header-inner">
        <div class="student-nav-flex">
            <a href="./index.php" style="text-decoration: none;">
                <p class="user-name"><?= $_SESSION['student_name'] ?> さん</p>
            </a>
            <a href="./php_do/logout.php">
                <p class="student-logout">ログアウト</p>
            </a>
        </div>
        <a href="./student_message.php" class="message-button bunner">
                <i id="alert-icon" class="material-symbols-outlined">
                    notifications
                </i>
        </a>
    </div>
</header>