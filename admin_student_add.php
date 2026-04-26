<!-- http://localhost:8080/amino-acids/admin_student_add.php -->
<!------------------------------------------------
生徒追加画面  （「student_add_do.php」へformのpost通信でデータを送信）

〈送られるデータ内容〉
『course_id』  
=> 1,2,3,4,5…（生徒を追加するコースのid）

『last_name』
=> 梅崎（名前の上）

『first_name』
=> 竜之介（名前の下）

『student_number』
=> 出席番号（01）

※『last_name, first_name, student_number』は複数送るので配列で送る予定（試してないから動かないかも・・・）
------------------------------------------------->

<?php require_once __DIR__ . '/functions/functions.php'; ?>
<?php

/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
    $courses = get_courses(date("Y-m-d"), true, null, null);
} catch (PDOException $e) {
    exit('訓練コース一覧の取得に失敗しました: ' . $e->getMessage());
}
?>





<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>-管理者- 生徒追加</title>
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
            <?php if ($_GET["message"] === "no_data"): ?>
                <div class="alert alert-danger">
                    データがうまく送られませんでした。
                </div>
            <?php elseif ($_GET["message"] === "cant_db"): ?>
                <div class="alert alert-danger">
                    データベース時のエラー。
                </div>
            <?php elseif ($_GET["message"] === "same_student"): ?>
                <div class="alert alert-danger">
                    同じ生徒が登録されています！！
                </div>
            <?php elseif ($_GET["message"] === "not_int"): ?>
                <div class="alert alert-danger">
                    出席番号が整数ではありませんでした！！
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <!-- kan-to-do:コンテンツ幅の統一 -->
    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">生徒追加</h1>

        <form action="php_do/student_add_do.php" method="post" class="row align-items-start">


            <div class="select_course form-group col-12 mb-3">
                <label for="course_id" class="form-label">コース</label>
                <select name="course_id" id="course_id" required class="form-control">
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course["course_id"]; ?>"><?php echo $course["room_name"] ?>｜<?php echo $course["course_name"] ?>｜<?php echo $course["start_date"] ?> ~ <?php echo $course["end_date"] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input_field card col-12 px-3 py-3">
                <div class="input_student d-flex px-3 py-3 mb-2 mx-auto" style="background-color: #baeef7;" id="input_field">
                    <div class="input_name d-flex">
                        <div class="input_last_name mr-1">
                            <label for="last_name" class="form-label">苗字</label>
                            <input type="text" name="last_name[]" id="last_name" placeholder="リカレント" required class="last_name form-control form-control-sm">
                        </div>
                        <div class="input_first_name mr-5">
                            <label for="first_name" class="form-label">名前</label>
                            <input type="text" name="first_name[]" id="first_name" placeholder="太郎" required class="first_name form-control form-control-sm">
                        </div>
                    </div>
                    <div class="input_num">
                        <label for="student_number" class="form-label">出席番号</label>
                        <input type="text" name="student_number[]" id="student_number" value="1" required class="student_number form-control form-control-sm w-25">
                    </div>
                </div>

                <!-- 追加・削除ボタン -->
                <div id="display_parent" class="d-flex justify-content-center align-items-center mt-3 student-add-icon-buttons ">
                    <a href="#" id="add_btn" class="student-add-icon-btn">
                        <img src="img/add_btn.png" alt="入力欄を追加">
                    </a>
                    <a href="#" id="del_btn" class="student-add-icon-btn">
                        <img src="img/del_btn.png" alt="入力欄を削除">
                    </a>
                </div>

            </div>


            <div class="col-12 d-flex justify-content-left mt-4 mb-5" style="gap: 12px">
                <a href="admin_student_list.php" class="btn btn-secondary px-3 py-2">一覧へ戻る</a>
                <input type="submit" value="生徒追加" class="btn btn-primary px-3 py-2" style="margin-top: 10px;">
            </div>
        </form>
    </div>

    <script src="js/add_student_input_field.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>