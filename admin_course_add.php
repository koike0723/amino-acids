<!-- http://localhost:8080/amino-acids/admin_course_add.php -->
<!-- コース追加画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
define('DB_HOST', 'localhost');
define('DB_USER', 'cc_user');
define('DB_PASS', 'password');
define('DB_NAME', 'career_consultant');
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
    <title>コース追加</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>

    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">コース追加</h1>

        <form action="course_add_do.php" method="post" class="row align-items-start">
            <div class="course_name col-12 mb-2">
                <label for="course_name" class="form-label">訓練名</label>
                <input type="text" name="course_name" id="course_name" required class="form-control">
            </div>
            <div class="course_room form-group col-6 mb-3">
                <label for="room_name" class="form-label">教室名</label>
                <select name="room_name" id="room_name" required class="form-control">
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo $room["id"]; ?>"><?php echo $room["name"]; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="course_category form-group col-6 mb-3">
                <label for="category_name" class="form-label">訓練カテゴリー</label>
                <select name="category_name" id="category_name" required class="form-control">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="course_term col-4">
                <label for="course_start" class="form-label mt-1">訓練開始日</label>
                <input type="date" name="course_start" id="course_start" required class="form-control form-control-sm">
                <label for="course_finish" class="form-label mt-1">訓練終了日</label>
                <input type="date" name="course_finish" id="course_finish" required class="form-control form-control-sm">
            </div>
            <dl class="course_cc col-8">
                <div class="card mb-2 px-1 py-1">
                    <dt class="mb-1">キャリコン１</dt>
                    <dd class="cc1 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc1_1" class="form-label">１回目</label>
                            <input type="date" name="cc1_1" id="cc1_1" class="form-control form-control-sm">
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc1_2" class="form-label">２回目</label>
                            <input type="date" name="cc1_2" id="cc1_2" class="form-control form-control-sm">
                        </div>
                    </dd>
                </div>
                <div class="card mb-2 px-1 py-1">
                    <dt class="mb-1">キャリコン２</dt>
                    <div class="cc2 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc2_1" class="form-label">１回目</label>
                            <input type="date" name="cc2_1" id="cc2_1" class="form-control form-control-sm">
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc2_2" class="form-label">２回目</label>
                            <input type="date" name="cc2_2" id="cc2_2" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="card mb-2 px-1 py-1">
                    <dt class="mb-1">キャリコン３</dt>
                    <div class="cc3 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc3_1" class="form-label">１回目</label>
                            <input type="date" name="cc3_1" id="cc3_1" class="form-control form-control-sm">
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc3_2" class="form-label">２回目</label>
                            <input type="date" name="cc3_2" id="cc3_2" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </dl>
            <div class="col-12 d-flex justify-content-center mb-5">
                <a href="admin_course_list.php" class="btn btn-secondary px-3 mr-5">一覧へ戻る</a>
                <input type="submit" value="追加" class="btn btn-primary px-3 ml-5">
            </div>
        </form>
    </div>

</body>

</html>