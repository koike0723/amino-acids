<!-- コース一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.csss">
    <title>コース一覧</title>
</head>

<body>
    <header class="header">
        <div class="header-nav"></div>
    </header>
    <main class="course-wrapper">
        <h1>コース一覧</h1>
        <div class="course-search">
            <form action="search">
                <input type="text" name="教室名">
                <input type="text" name="訓練名">
                <input type="text" name="訓練期間">
                <input type="text" name="訓練タイプ">
                <a href="./admin_course_detail.php" class="">詳細設定</a>
                <input type="submit" value="検索">
            </form>
        </div>
        <div class="course-list">
            <table>
                <tr>
                    <th>教室名</th>
                    <th>訓練名</th>
                    <th>訓練日時</th>
                    <th>訓練タイプ</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>6c</td>
                    <td>Webプログラミング科</td>
                    <td>就職済み</td>
                    <td>利用可</td>
                    <td>
                        <a class="list-btn-a" href="./admin_course_edit.php">編集</a>
                        <a class="list-btn-b" href="./admin_course_detail.php">詳細</a>
                        <a class="list-btn-c" href="./admin_course_del_confirm.php">削除</a>
                    </td>
                </tr>
                <tr>
                    <td>6b</td>
                    <td>Webデザイナー科</td>
                    <td>在籍中</td>
                    <td>利用不可</td>
                    <td>
                        <a class="list-btn-a" href="./admin_course_edit.php">編集</a>
                        <a class="list-btn-b" href="./admin_course_detail.php">詳細</a>
                        <a class="list-btn-c" href="./admin_course_del_confirm.php">削除</a>
                    </td>
                </tr>
                <tr>
                    <td>6a</td>
                    <td>Webプログラミング科</td>
                    <td>卒業</td>
                    <td>利用可</td>
                    <td>
                        <a class="list-btn-a" href="./admin_course_edit.php">編集</a>
                        <a class="list-btn-b" href="./admin_course_detail.php">詳細</a>
                        <a class="list-btn-c" href="./admin_course_del_confirm.php">削除</a>
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