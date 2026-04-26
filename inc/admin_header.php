<!-- 管理者側ヘッダー -->
<header class="site-header_admin">
    <div class="header-inner">
        <div class="inner-flex">
            <a href="./admin_index.php" style="text-decoration: none;">
                <p class="user-name"><?php echo h($_SESSION['admin_name']); ?> さん</p>
            </a>
            <a href="./php_do/logout.php" style="color: black;">ログアウト</a>
        </div>
        <a href="./admin_message.php" class="message-button bunner">
            <?php if (has_unresolved_cc_requests()) : ?>
                <div class="icon-circle">
                <?php endif; ?>
                <span class="material-symbols-outlined">
                    notifications
                </span>
                </div>
        </a>
    </div>
    <?php require_once('./inc/nav_bar.php'); ?>
</header>