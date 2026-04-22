<!-- http://localhost:8080/amino-acids/admin_course_list.php -->
<!-- コース一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<?php
/////////////////////////////////////////////////
/////////////////////GET通信処理/////////////////
/////////////////////////////////////////////////
$date = date('Y-m-d');
if (isset($_GET['filter'])) {
    $date = $_GET['date'] !== '' ? $_GET['date'] : null;
    $room_id = $_GET['room_id'] !== '' ? $_GET['room_id'] : null;
    $category_id = $_GET['category_id'] !== '' ? $_GET['category_id'] : null;
} else {
    $room_id = null;
    $category_id = null;
}
?>
<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
    $courses = get_courses($date, false, $room_id, $category_id);
} catch (PDOException $e) {
    exit('情報の取得に失敗しました: ' . $e->getMessage());
}
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
            <div class="d-flex align-items-center">
                <h1 class="m-5">コース一覧</h1>
                <div class="ml-5"><a href="admin_course_add.php" style="background-color: #1828ff;" class="btn btn-primary btn-lg">コース追加</a></div>
            </div>
            <div class="course-search">
                <form action="admin_course_list.php" method="get" class="row">
                    <div class="date col-3">
                        <label for="date" class="form-label">訓練実施日</label>
                        <input type="date" name="date" id="date" value="<?= $date ?>" class="form-control">
                    </div>
                    <div class="room col-3">
                        <label for="room_id" class="form-label">教室名</label>
                        <select name="room_id" id="room_id" class="form-control">
                            <option value="" <?= ($room_id == "") ? "selected" : "" ?>>全表示</option>
                            <?php foreach ($rooms as $room): ?>
                                <?php if ($room["id"] != 13): ?>
                                    <option value="<?php echo $room["id"]; ?>" <?= ($room_id == $room["id"]) ? "selected" : "" ?>><?php echo $room["name"]; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="category col-4">
                        <label for="category_id" class="form-label">訓練カテゴリー</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="" selected <?= ($category_id == "") ? "selected" : "" ?>>全表示</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category["id"]; ?>" <?= ($category_id == $category["id"]) ? "selected" : "" ?>><?php echo $category["name"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <input type="submit" value="絞り込む" name="filter" class="btn btn-info">
                </form>
            </div>



            <table class="table table-striped mt-3">
                <thead>
                    <tr style="background-color: #a0a0a0;">
                        <th>教室名</th>
                        <th>訓練名</th>
                        <th>訓練日時</th>
                        <th>訓練タイプ</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo $course["room_name"] ?></td>
                            <td><?php echo $course["course_name"] ?></td>
                            <td><?php echo format_japanese_date($course["start_date"]) ?>～<?php echo format_japanese_date($course["end_date"]) ?></td>
                            <td><?php echo $course["category_name"] ?></td>
                            <td>
                                <a class="btn btn-info mx-1 my-1" href='admin_course_detail.php?course_id=<?= $course["course_id"] ?>'>詳細</a>
                                <a class="btn btn-success mx-1 my-1" href='admin_course_edit.php?course_id=<?= $course["course_id"] ?>'>編集</a>
                                <a class="btn btn-danger mx-1 my-1" href='php_do/course_del_do.php?course_id=<?= $course["course_id"] ?>'>削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="l-btn-area mb-5">
                <a class="btn btn-secondary" href="./admin_index.php">トップに戻る</a>
            </div>
        </main>
    </div>
</body>
<!-- kan-to-do:余力があればリアルタイムで変わるようにする -->
<script src="./js/admin_course_search.js"></script>
<script src="./js/hamburger.js"></script>
</html>