<!-- 管理者側ヘッダー -->
<header class="site-header_admin">
    <?php include('./inc/nav_bar.php'); ?>
    <div class="header-inner">
        <div class="inner-flex">
            <a href="./index.php" style="text-decoration: none;">
                <p class="user-name"><?php echo '事務'; //役職 
                                        ?>&nbsp;岸本 さん</p>
            </a>
            <a href="../php_do/logout.php" style="color: black;">ログアウト</a>
        </div>
        <a href="./student_message.php" class="message-button">メッセージがあります</a>
    </div>
</header>