<!-- http://localhost:8080/amino-acids/admin_course_add.php -->
<!------------------------------------------------
コース追加画面 （「course_add_do.php」へformのpost通信でデータを送信）

〈送られるデータ内容〉
『course_name』
=> Webプログラミング科（コース名）

『room_id』
=> 1:6A～8:6H, 9:7A～12:7C（idを送る想定）

『category_id』
=> 1:求職者支援訓練, 2:公共職業訓練（idを送る想定）

『course_start』
=> 2025-11-05（訓練開始日）

『course_finish』
=> 2026-04-30（訓練終了日）

『cc1_1』
=> YYYY-MM-DD（キャリコン第１回目の１枠目）
    
『cc1_2』
=> YYYY-MM-DD（キャリコン第１回目の２枠目）

『cc2_1』
=> YYYY-MM-DD（キャリコン第２回目の１枠目）

『cc2_2』
=> YYYY-MM-DD（キャリコン第２回目の２枠目）

『cc3_1』
=> YYYY-MM-DD（キャリコン第３回目の１枠目）

『cc3_2』
=> YYYY-MM-DD（キャリコン第３回目の２枠目）
 ------------------------------------------------->

<?php require_once __DIR__ . '/functions/functions.php'; ?>
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
    <title>コース追加</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>

    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

    <?php if (isset($_GET["status"]) && $_GET["status"] === "success"): ?>
        <div class="alert alert-success">
            コースを追加しました！
        </div>
    <?php endif; ?>
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
            <?php elseif ($_GET["message"] === "same_course"): ?>
                <div class="alert alert-danger">
                    すでにその日付はほかのコースがその教室を使っています！
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

    <!-- kan-to-do:コンテンツ幅の統一 -->
    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">コース追加</h1>

        <form action="php_do/course_add_do.php" method="post" class="row align-items-start">
            <div class="course_name col-12 mb-2">
                <label for="course_name" class="form-label">訓練名</label>
                <input type="text" name="course_name" id="course_name" required class="form-control">
            </div>
            <div class="course_room form-group col-6 mb-3">
                <label for="room_id" class="form-label">教室名</label>
                <select name="room_id" id="room_id" required class="form-control">
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room["id"] != 13): ?>
                            <option value="<?php echo $room["id"]; ?>"><?php echo $room["name"]; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="course_category form-group col-6 mb-3">
                <label for="category_id" class="form-label">訓練カテゴリー</label>
                <select name="category_id" id="category_id" required class="form-control">
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
            <dl class="course_cc col-8" id="cc_box">
                <div class="card mb-2 px-1 py-1">
                    <dt class="mb-1">キャリコン１</dt>
                    <dd class="cc1 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc1_1" class="form-label">１枠目</label>
                            <input type="date" name="cc1_1" id="cc1_1" class="form-control form-control-sm">
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc1_2" class="form-label">２枠目</label>
                            <input type="date" name="cc1_2" id="cc1_2" class="form-control form-control-sm">
                        </div>
                    </dd>
                </div>
                <div class="card mb-2 px-1 py-1">
                    <dt class="mb-1">キャリコン２</dt>
                    <div class="cc2 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc2_1" class="form-label">１枠目</label>
                            <input type="date" name="cc2_1" id="cc2_1" class="form-control form-control-sm">
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc2_2" class="form-label">２枠目</label>
                            <input type="date" name="cc2_2" id="cc2_2" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="card mb-2 px-1 py-1">
                    <dt class="mb-1">キャリコン３</dt>
                    <div class="cc3 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc3_1" class="form-label">１枠目</label>
                            <input type="date" name="cc3_1" id="cc3_1" class="form-control form-control-sm">
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc3_2" class="form-label">２枠目</label>
                            <input type="date" name="cc3_2" id="cc3_2" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </dl>
            <div class="col-12 d-flex justify-content-center mt-4 mb-5">
                <a href="admin_course_list.php" class="btn btn-secondary px-3 mr-5">一覧へ戻る</a>
                <input type="submit" value="追加" class="btn btn-primary px-3 ml-5">
            </div>
        </form>
    </div>

</body>
<script src="js/cc_box_toggle.js"></script>

</html>