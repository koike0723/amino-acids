<!-- http://localhost:8080/amino-acids/admin_cc_detail.php?cc_date=2026-04-25 -->
<!-- 必須キャリコンをドラック&ドロップで管理できる管理者画面 -->
<?php require_once __DIR__ . '/functions/functions.php'; ?>
<?php
/////////////////////////////////////////////////
/////////////////////GET通信処理/////////////////
/////////////////////////////////////////////////
if (isset($_GET['cc_date'])) {
    $cc_date = $_GET['cc_date'];
}
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
    $sql = 'SELECT id, last_name, first_name, CONCAT(last_name, first_name) AS name FROM m_consultants';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cc_teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('キャリアコンサルタント達の取得に失敗しました: ' . $e->getMessage());
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
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // SQL
    $sql = 'SELECT id, display_name AS name FROM m_times';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cc_times = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('ccの時間情報（m_times）の取得に失敗しました: ' . $e->getMessage());
}
try {
    $cc_slots = get_cc_slots(CC_SLOT_TYPE::Line->name, $cc_date);
} catch (PDOException $e) {
    exit('必須キャリコンのラインの取得に失敗しました: ' . $e->getMessage());
}
try {
    $cc_plus_slots = get_cc_slots(CC_SLOT_TYPE::CcPlus->name, $cc_date);
} catch (PDOException $e) {
    exit('任意キャリコンのラインの取得に失敗しました: ' . $e->getMessage());
}
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // SQL
    $sql = 'SELECT id, display_name AS name FROM m_times';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cc_all_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('キャリコンの予約の取得に失敗しました: ' . $e->getMessage());
}
check($cc_plus_slots);
?>



<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/kan.css">
    <title>-管理者- キャリコン管理</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>

        <div class="cc-mgmt">
            <p class="cc-head-text">キャリコン・ライン管理</p>
            <p class="cc-head-date"><?= format_japanese_date($cc_date); ?></p>
            <div class="kan_btn kan_back-btn"><a href="admin_index.php">一覧へ戻る</a></div>
        </div>
        <div class="kan_btn kan_open-btn" id="open_btn"><button type="button">任意キャリコンを開く ▶</button></div>

        <div class="content-wrap">
            <form action="php_do/cc_detail_edit_do.php">

                <div class="kan_btn kan_btn-confirm"><input type="submit" name="update" value="編集確定する"></div>


                <div class="cc-plus-table-area" id="drawer_area">
                    <div class="content-wrap">
                        <div class="cc-plus-list-flex">
                            <div class="kan_btn kan_close-btn"><button type="button" id="close_btn">◀ 閉じる</button></div>
                            <div>任意キャリコン枠</div>
                        </div>
                        <?php foreach ($cc_slots as $cc_slot): ?>
                            <table class="cc-plus-table" style="background-color: white;">
                                <thead class="cc-detail-thead">
                                    <tr class="cc-detail-headTr">
                                        <?php foreach ($cc_times as $cc_time): ?>
                                            <th class="cc-detail-th"><?= $cc_time["name"] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody class="cc-detail-tbody">
                                    <tr class="cc-detail-tr">
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cc-detail-table-area">
                    <?php foreach ($cc_slots as $cc_slot): ?>
                        <div class="cc_slot">
                            <label for="cc-detail-select-class">
                                教室
                                <select name="cc-detail-select-class" id="cc-detail-select-class" class="cc-detail_selectStyle">
                                    <option value="" selected disabled>未選択</option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value='<?= $room["id"] ?>'><?= $room["name"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label for="cc-detail-select-teacher">
                                CC講師
                                <select name="cc-detail-select-teacher" id="cc-detail-select-teacher" class="cc-detail_selectStyle">
                                    <option value="" selected disabled>未選択</option>
                                    <?php foreach ($cc_teachers as $cc_teacher): ?>
                                        <option value='<?= $cc_teacher["id"] ?>'><?= $cc_teacher["last_name"] ?><br><?= $cc_teacher["first_name"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <table class="cc-detail-table">
                                <thead class="cc-detail-thead">
                                    <tr class="cc-detail-headTr">
                                        <?php foreach ($cc_times as $cc_time): ?>
                                            <th class="cc-detail-th"><?= $cc_time["name"] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody class="cc-detail-tbody">

                                    <tr class="cc-detail-tr">

                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                        <td class="cc-detail-td">
                                            <div class="cc-detail-student-card">
                                                <p class="cc-detail-student">6c</p>
                                                <p class="cc-detail-student">リカレント太郎</p>
                                                <p class="cc-detail-student">対面</p>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    <div class="mt-3 text-center" id="display_parent">
                        <a href="#" id="add_btn"><img src="img/add_btn.png" alt=""></a>
                    </div>
                </div>

            </form>



        </div>
    </main>
    <script src="js/drag_and_drop.js"></script>
    <script src="js/drawer.js"></script>
    <script src="js/hamburger.js"></script>
    <script src="js/add_cc_slot.js"></script>
</body>

</html>