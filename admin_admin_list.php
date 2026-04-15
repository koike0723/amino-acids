<!-- 管理者一覧 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.csss">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <title>管理者一覧</title>
</head>

<body>
    <header class="header">
        <div class="header-nav"></div>
    </header>
    <main class="admin-admin-wrapper">
        <h1>管理者一覧</h1>
        <a href="./admin_admin_add.php" class="">管理者追加</a>
        <div class="admin-admin-list">
            <table>
                <tr>
                    <th>名前</th>
                    <th>ＩＤ</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>小松 喜徳</td>
                    <td>komatsu1234</td>
                    <td>
                        <a class="list-btn-a" href="./admin_admin_edit.php">編集</a>
                        <a class="list-btn-c" href="./admin_admin_del_confirm.php">削除</a>
                    </td>
                </tr>
                <tr>
                    <th>名前</th>
                    <th>ＩＤ</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>佐藤 太郎</td>
                    <td>sugar0310</td>
                    <td>
                        <a class="list-btn-a" href="./admin_admin_edit.php">編集</a>
                        <a class="list-btn-c" href="./admin_admin_del_confirm.php">削除</a>
                    </td>
                </tr>
                <tr>
                    <th>名前</th>
                    <th>ＩＤ</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>中山 きんに君</td>
                    <td>matcho2929</td>
                    <td>
                        <a class="list-btn-a" href="./admin_admin_edit.php">編集</a>
                        <a class="list-btn-c" href="./admin_admin_del_confirm.php">削除</a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="l-btn-area">
            <a class="top-btn" href="./admin_index.php">トップに戻る</a>
        </div>
    </main>
</body>

</html>