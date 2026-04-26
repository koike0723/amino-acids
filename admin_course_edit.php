<!-- http://localhost:8080/amino-acids/admin_course_edit.php -->
<!-- コース編集画面 -->
<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login(); ?>


<?php
/////////////////////////////////////////////////
/////////////////////GET通信処理/////////////////
/////////////////////////////////////////////////
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
}
?>

<?php
//////////////////////////////////////////////////
//////////////////データベース処理/////////////////
//////////////////////////////////////////////////
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
    $sql = 'SELECT id, name FROM m_rooms';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('教室データ（m_rooms）の取得に失敗しました: ' . $e->getMessage());
}
try {
    $course = get_course($course_id);
} catch (PDOException $e) {
    exit('コース詳細の取得に失敗しました: ' . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コース編集</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
</head>

<body>

    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

    <?php if (isset($_GET["status"]) && $_GET["status"] === "error"): ?>
        <?php if (isset($_GET["message"])): ?>
            <?php if ($_GET["message"] === "no_data"): ?>
                <div class="alert alert-danger">
                    データがうまく送られませんでした。
                </div>
            <?php elseif ($_GET["message"] === "cant_db"): ?>
                <div class="alert alert-danger">
                    データベース時のエラー。
                </div>
            <?php elseif ($_GET["message"] === "error_date"): ?>
                <div class="alert alert-danger">
                    コース終了日はコース開始日よりあとに設定してください！！
                </div>
            <?php elseif ($_GET["message"] === "error_cc_date"): ?>
                <div class="alert alert-danger">
                    キャリコンの日付が同じ日があります！！
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">コース編集</h1>

        <form action="php_do/course_edit_do.php" method="post" class="hum-row align-items-start">

            <input type="hidden" name="course_id" id="course_id" value="<?= $course_id ?>">

            <div class="course_name col-12 mb-2">
                <label for="course_name" class="form-label">訓練名</label>
                <input type="text" name="course_name" id="course_name" required class="form-control" value="<?= $course["course_name"] ?>">
            </div>
            <div class="course_room form-group col-6 mb-3">
                <label for="room_id" class="form-label">教室名</label>
                <select name="room_id" id="room_id" required class="form-control">
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room["id"] != 13): ?>
                            <option value="<?php echo $room["id"]; ?>" <?= ($course["room_id"] == $room["id"]) ? "selected" : "" ?>><?php echo $room["name"]; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="course_category form-group col-6 mb-3">
                <label for="category_id" class="form-label">訓練カテゴリー</label>
                <select name="category_id" id="category_id" required class="form-control">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category["id"]; ?>" <?= ($course["category_id"] == $category["id"]) ? "selected" : "" ?>><?php echo $category["name"]; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="course_term col-4">
                <label for="course_start" class="form-label mt-1">訓練開始日</label>
                <input type="date" name="course_start" id="course_start" value="<?= $course["start_date"] ?>" required class="form-control form-control-sm">
                <label for="course_finish" class="form-label mt-1">訓練終了日</label>
                <input type="date" name="course_finish" id="course_finish" value="<?= $course["end_date"] ?>" required class="form-control form-control-sm">
            </div>


            <dl class="course_cc col-8" id="cc_box" style="<?= ($course['category_id'] == 1) ? '' : 'display:none;' ?>">
                <label for="course_cc" class="form-label">キャリコンの日時設定</label>
                <?php if (!empty($course["cc"])): ?>
                    <?php foreach ($course["cc"] as $cc_count => $dates): ?>
                        <div class="card mb-2 px-1 py-1">
                            <dt class="mb-1">キャリコン<?= (int)$cc_count ?></dt>
                            <div class="cc<?= (int)$cc_count ?> mb-1 d-flex justify-content-around">
                                <div class="1st_cc_day mt-1">
                                    <label for="cc<?= (int)$cc_count ?>_1" class="form-label">１枠目</label>
                                    <input type="date" name="cc<?= (int)$cc_count ?>_1" id="cc<?= (int)$cc_count ?>_1" value="<?= h($dates[0] ?? '') ?>" class="form-control form-control-sm">
                                </div>
                                <div class="2nd_cc_day mt-1">
                                    <label for="cc<?= (int)$cc_count ?>_2" class="form-label">２枠目</label>
                                    <input type="date" name="cc<?= (int)$cc_count ?>_2" id="cc<?= (int)$cc_count ?>_2" value="<?= h($dates[1] ?? '') ?>" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card mb-2 px-1 py-1">
                        <dt class="mb-1">キャリコン1</dt>
                        <div class="cc1 mb-1 d-flex justify-content-around">
                            <div class="1st_cc_day mt-1">
                                <label for="cc1_1" class="form-label">１枠目</label>
                                <input type="date" name="cc1_1" id="cc1_1" value="" class="form-control form-control-sm">
                            </div>
                            <div class="2nd_cc_day mt-1">
                                <label for="cc1_2" class="form-label">２枠目</label>
                                <input type="date" name="cc1_2" id="cc1_2" value="" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- 追加・削除ボタン -->
                <div id="display_parent" class="d-flex justify-content-center align-items-center mt-3 student-add-icon-buttons ">
                    <a href="#" id="add_btn" class="student-add-icon-btn">
                        <img src="img/add_btn.png" alt="入力欄を追加">
                    </a>
                    <a href="#" id="del_btn" class="student-add-icon-btn">
                        <img src="img/del_btn.png" alt="入力欄を削除">
                    </a>
                </div>
            </dl>
            <div class="col-12 d-flex justify-content-left mt-4 mb-5" style="gap: 12px">
                <a href="admin_course_detail.php?course_id=<?= $course_id; ?>" class="btn btn-secondary px-3 py-2">詳細へ戻る</a>
                <input type="submit" value="編集完了" class="btn btn-primary px-3 py-2" style="margin-top: 10px">
            </div>
        </form>
    </div>
    <script src="./js/add_couese_input_field.js"></script>
    <script src="js/cc_box_toggle.js"></script>
    <script src="js/script.js"></script>
    <script src="js/hamburger.js"></script>
</body>

</html>