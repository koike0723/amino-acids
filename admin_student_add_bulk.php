<!-- http://localhost:8080/amino-acids/admin_student_add_bulk.php -->
<!------------------------------------------------
生徒一括追加画面  （「student_add_do.php」へformのpost通信でデータを送信）

〈送られるデータ内容〉
『course_id』  
=> 1,2,3,4,5…（生徒を追加するコースのid）
------------------------------------------------->

<?php require_once __DIR__ . '/functions/functions.php'; ?>
<?php

/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
    $courses = get_courses(date("Y-m-d"), true, null, null);
} catch (PDOException $e) {
    exit('訓練タイプの取得に失敗しました: ' . $e->getMessage());
}
?>





<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>-管理者- 生徒一括追加</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
</head>

<body>

    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>

    <?php if (isset($_GET["status"]) && $_GET["status"] === "success"): ?>
        <div class="alert alert-success">
            生徒を追加しました！
        </div>
    <?php endif; ?>
    <?php if (isset($_GET["status"]) && $_GET["status"] === "error"): ?>
        <?php if (isset($_GET["message"])): ?>
        <?php elseif ($_GET["message"] === "file_error"): ?>
            <div class="alert alert-danger">
                ファイル通信がうまくいきませんできませんでした！
            </div>
        <?php endif; ?>
    <?php endif; ?>


    <!-- kan-to-do:コンテンツ幅の統一 -->
    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">生徒一括追加</h1>

        <form action="php_do/student_add_do.php" method="post" enctype="multipart/form-data" class="row align-items-start">

            <div class="select_course form-group col-12 mb-5">
                <label for="course_id" class="form-label">使用教室｜訓練名｜期間</label>
                <select name="course_id" id="course_id" required class="form-control">
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course["course_id"]; ?>"><?php echo $course["room_name"] ?>｜<?php echo $course["course_name"] ?>｜<?php echo $course["start_date"] ?> ~ <?php echo $course["end_date"] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="custom-file mb-2">
                <input type="file" name="csv_file" id="csv_file" class="custom-file-input">
                <label for="csv_file" class="custom-file-label" id="output_filename">CSVファイルを選択</label>
            </div>
            <div class="csv_drop_area col-12 px-3 py-3 d-flex align-items-center justify-content-center" style="height: 300px; border: 2px dashed #b8b8b8cd" id="csv_drop_area">
                <span style="font-size: xx-large;">CSVファイルをここにドロップ</span>
            </div>

            <div class="col-12 d-flex justify-content-center mt-4 mb-5">
                <a href="admin_student_list.php" class="btn btn-secondary px-3 mr-5">一覧へ戻る</a>
                <input type="submit" value="生徒一括追加" class="btn btn-primary px-3 ml-5" style="background-color: #020bff;">
            </div>

        </form>
    </div>

    <script src="./js/drop_csv.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>