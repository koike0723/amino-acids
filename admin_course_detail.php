<!-- http://localhost:8080/amino-acids/admin_course_detail.php -->

<?php require_once __DIR__ . '/functions/functions.php'; ?>

<?php
/////////////////////////////////////////////////
/////////////////////GET通信処理/////////////////
/////////////////////////////////////////////////
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
}
?>

<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
///////////////////////////////////////////////////
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
    <title>コース追加</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <?php if (isset($_GET["status"]) && $_GET["status"] === "success"): ?>
        <div class="alert alert-success">
            コースを編集しました！
        </div>
    <?php endif; ?>

    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">コース詳細</h1>

        <div class="course_name col-12 mb-2">
            <label for="course_name" class="form-label">訓練名</label>
            <p id="course_name" class="form-control"><?= $course["course_name"] ?></p>
        </div>
        <div class="course_room form-group col-6 mb-3">
            <label for="room_id" class="form-label">教室名</label>
            <p name="room_id" id="room_id" class="form-control"><?= $course["room_name"] ?></p>
        </div>
        <div class="course_category form-group col-6 mb-3">
            <label for="category_id" class="form-label">訓練カテゴリー</label>
            <p id="category_id" class="form-control"><?= $course["category_name"] ?></p>
        </div>
        <div class="course_term col-4">
            <label for="course_start" class="form-label mt-1">訓練開始日</label>
            <p>
                <span id="course_start" required class="form-control form-control-sm"><?= $course["start_date"] ?></span>
            </p>
            <label for="course_finish" class="form-label mt-1">訓練終了日</label>
            <p>
                <span id="course_finish" required class="form-control form-control-sm"><?= $course["end_date"] ?></span>
            </p>
        </div>
        <?php if ($course["category_id"] == 1): ?>
            <div class="d-flex px-2 py-3" style="background-color: #bbd8ff; border-radius: 10px;">
                <dl class="course_cc col-6">
                    <label for="course_days" class="form-label">キャリコンの日時</label>
                    <dt class="mb-1">キャリコン１</dt>
                    <dd class="cc1 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc1_1" class="form-label">１枠目</label>
                            <p href="./admin_cc_course_list.php" id="cc1_1" class="form-control form-control-sm">
                                <?php
                                if (!empty($course["cc"][1][0]) && $course["cc"][1][0] != "0000-00-00") {
                                    echo format_japanese_date($course["cc"][1][0]);
                                } else {
                                    echo "未設定";
                                }
                                ?>
                            </p>
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc1_2" class="form-label">２枠目</label>
                            <p href="./admin_cc_course_list.php" id="cc1_2" class="form-control form-control-sm">
                                <?php
                                if (!empty($course["cc"][1][1]) && $course["cc"][1][1] != "0000-00-00") {
                                    echo format_japanese_date($course["cc"][1][1]);
                                } else {
                                    echo "未設定";
                                }
                                ?>
                            </p>
                        </div>
                    </dd>
                    <dt class="mb-1">キャリコン２</dt>
                    <div class="cc2 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc2_1" class="form-label">１枠目</label>
                            <p href="./admin_cc_course_list.php" id="cc2_1" class="form-control form-control-sm">
                                <?php
                                if (!empty($course["cc"][2][0]) && $course["cc"][2][0] != "0000-00-00") {
                                    echo format_japanese_date($course["cc"][2][0]);
                                } else {
                                    echo "未設定";
                                }
                                ?>
                            </p>
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc2_2" class="form-label">２枠目</label>
                            <p href="./admin_cc_course_list.php" id="cc2_2" class="form-control form-control-sm">
                                <?php
                                if (!empty($course["cc"][2][1]) && $course["cc"][2][1] != "0000-00-00") {
                                    echo format_japanese_date($course["cc"][2][1]);
                                } else {
                                    echo "未設定";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <dt class="mb-1">キャリコン３</dt>
                    <div class="cc3 mb-1 d-flex justify-content-around">
                        <div class="1st_cc_day mt-1">
                            <label for="cc3_1" class="form-label">１枠目</label>
                            <p href="./admin_cc_course_list.php" id="cc3_1" class="form-control form-control-sm">
                                <?php
                                if (!empty($course["cc"][3][0]) && $course["cc"][3][0] != "0000-00-00") {
                                    echo format_japanese_date($course["cc"][3][0]);
                                } else {
                                    echo "未設定";
                                }
                                ?>
                            </p>
                        </div>
                        <div class="2nd_cc_day mt-1">
                            <label for="cc3_2" class="form-label">２枠目</label>
                            <p href="./admin_cc_course_list.php" id="cc3_2" class="form-control form-control-sm">
                                <?php
                                if (!empty($course["cc"][3][1]) && $course["cc"][3][1] != "0000-00-00") {
                                    echo format_japanese_date($course["cc"][3][1]);
                                } else {
                                    echo "未設定";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </dl>
                <div class="d-flex col-6 flex-column mb-4">
                    <div class="mt-auto">
                        <a href="admin_cc_course_list.php?<?= $course["course_id"] ?>" class="btn btn-info">
                            キャリコン詳細へ
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-12 d-flex justify-content-center mt-4 mb-5">
            <a class="btn btn-success btn-lg mr-5 my-1" href='admin_course_edit.php?course_id=<?= $course["course_id"] ?>'>編集</a>
            <a class="btn btn-danger btn-lg ml-5 my-1" href='php_do/course_del_do.php?course_id=<?= $course["course_id"] ?>'>削除</a>
        </div>
        <div class="col-12 d-flex justify-content-center mt-4 mb-5">
            <a href="admin_course_list.php" class="btn btn-secondary btn-lg px-3 mr-5">一覧へ戻る</a>

        </div>
    </div>

</body>

</html>