<!-- コース一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>
<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // SQL
    $sql = 'SELECT id,name FROM m_courses_categories';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('訓練タイプの取得に失敗しました: ' . $e->getMessage());
}
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // SQL
    $sql = 'SELECT id,name FROM m_rooms';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('教室データ（m_rooms）の取得に失敗しました: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コース一覧</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">
        <main class="course-wrapper">
            <h1 class="m-5">コース一覧</h1>
            <div class="course-search">
                <form action="search">
                    <input type="date" id="course-date">
                    </input>
                    <select type="text" id="course-room" placeholder="教室名">
                        <option value="教室名" hidden>教室を選択</option>
                        <option value="1">6a</option>
                        <option value="2">6b</option>
                        <option value="3">6c</option>
                    </select>
                    <input type="text" id="course-traning" placeholder="訓練タイプ">
                    <select name="course-status" id="course-status">
                        <option value="訓練タイプ" hidden>訓練タイプを選択</option>
                        <option value="1">求職者支援訓練</option>
                        <option value="2">公共職業訓練</option>
                    </select>
                    <div class="course_detail_btn">
                        <input type="image" value="検索" src="">
                        <a href="/admin_course_add.php">追加</a>
                    </div>
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
                        <td>１０月～４月</td>
                        <td>求職者支援訓練</td>
                        <td>
                            <a class="list-btn-a" href="./admin_course_edit.php">編集</a>
                            <a class="list-btn-b" href="./admin_course_detail.php">詳細</a>
                            <a class="list-btn-c" href="./admin_course_del_confirm.php">削除</a>
                        </td>
                    </tr>
                    <tr>
                        <td>6b</td>
                        <td>Webデザイナー科</td>
                        <td>１０月～４月</td>
                        <td>公共職業訓練</td>
                        <td>
                            <a class="list-btn-a" href="./admin_course_edit.php">編集</a>
                            <a class="list-btn-b" href="./admin_course_detail.php">詳細</a>
                            <a class="list-btn-c" href="./admin_course_del_confirm.php">削除</a>
                        </td>
                    </tr>
                    <tr>
                        <td>6a</td>
                        <td>Webプログラミング科</td>
                        <td>１１月～６月</td>
                        <td>求職者支援訓練</td>
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
        <script src="./js/admin_course_search.js"></script>
    </div>
</body>

</html>