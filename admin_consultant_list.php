<!-- コンサルタント一覧 -->
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
    <title>コンサルタント一覧</title>
</head>

<body>
    <header class="header">
        <div class="header-nav"></div>
    </header>
    <main class="admin-consultant-wrapper">
        <h1>コンサルタント一覧</h1>
        <a href="./admin_consultant_add.php" class="">コンサルタント追加</a>
        <div class="admin-consultant-list">
            <table>
                <tr>
                    <th>名前</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>小松 喜徳</td>
                    <td>
                        <a class="list-btn-a" href="./admin_consultant_edit.php">編集</a>
                        <a class="list-btn-c" href="./admin_consultant_del_confirm.php">削除</a>
                    </td>
                </tr>
                <tr>
                    <th>名前</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>梅崎 竜之介</td>
                    <td>
                        <a class="list-btn-a" href="./admin_consultant_edit.php">編集</a>
                        <a class="list-btn-c" href="./admin_consultant_del_confirm.php">削除</a>
                    </td>
                </tr>
                <tr>
                    <th>名前</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>江原 実里</td>
                    <td>
                        <a class="list-btn-a" href="./admin_consultant_edit.php">編集</a>
                        <a class="list-btn-c" href="./admin_consultant_del_confirm.php">削除</a>
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