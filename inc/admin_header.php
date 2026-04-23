<!-- 管理者側ヘッダー -->
<header class="site-header_admin">
    <div class="header-inner">
        <div class="inner-flex">
            <a href="./admin_index.php" style="text-decoration: none;">
                <p class="user-name"><?php echo '事務'; //役職 
                                        ?>&nbsp;岸本 さん</p>
            </a>
            <a href="../php_do/logout.php" style="color: black;">ログアウト</a>
        </div>
        <a href="./student_message.php" class="message-button bunner">
            <?php if (has_unresolved_cc_requests()) : ?>
                <span class="icon-circle">
                <?php endif; ?>
                <i id="alert-icon" class="material-symbols-outlined">
                    notifications
                </i>
                </span>
        </a>
    </div>
    <?php require_once('./inc/nav_bar.php'); ?>
</header>