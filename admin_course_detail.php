<!-- http://localhost:8080/amino-acids/admin_course_detail.php -->

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
    <title>コース詳細</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <?php if (isset($_GET["status"]) && $_GET["status"] === "success"): ?>
        <div class="alert alert-success">
            コースを編集しました！
        </div>
    <?php endif; ?>
    <?php if (isset($_GET["status"]) && $_GET["status"] === "bulk_book_success"): ?>
        <div class="alert alert-success">
            必須CCを一括登録しました！
        </div>
    <?php endif; ?>

    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">コース詳細</h1>

        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">訓練名：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($course["course_name"]) ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">教室名：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($course["room_name"]) ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">訓練カテゴリー：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($course["category_name"]) ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">訓練開始日：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($course["start_date"]) ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-3">
            <label class="form-label mb-0 text-nowrap">訓練終了日：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($course["end_date"]) ?></p>
        </div>
        <?php if ($course["category_id"] == 1): ?>
            <div class="col-8" id="cc_box">
                <div class="d-flex align-items-center mb-2">
                    <label class="form-label mb-0">キャリコンの日時</label>
                    <a class="btn btn-primary course_cc_bulk ml-3" href="admin_cc_bulk_book.php?course_id=<?= h($course['course_id']) ?>">一括登録</a>
                </div>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 ad-index-table">
                                <thead>
                                    <tr>
                                        <th class="ad-index-th">開催数</th>
                                        <th class="ad-index-th">1枠目</th>
                                        <th class="ad-index-th">2枠目</th>
                                        <th class="ad-index-th">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($course["cc"])): ?>
                                        <tr>
                                            <td colspan="4" class="ad-index-td">スケジュール未設定</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($course["cc"] as $cc_count => $dates): ?>
                                            <tr>
                                                <td class="ad-index-td">第<?= (int) $cc_count ?>回</td>
                                                <td class="ad-index-td">
                                                    <?= (!empty($dates[0]) && $dates[0] !== '0000-00-00') ? h(format_japanese_date($dates[0])) : '未設定' ?>
                                                </td>
                                                <td class="ad-index-td">
                                                    <?= (!empty($dates[1]) && $dates[1] !== '0000-00-00') ? h(format_japanese_date($dates[1])) : '未設定' ?>
                                                </td>
                                                <td class="ad-index-td">
                                                    <a href="admin_cc_course_list.php?course_id=<?= h($course['course_id']) ?>&cc_count=<?= (int) $cc_count ?>" class="btn btn-info btn-sm">
                                                        詳細
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-12 d-flex mt-4 mb-5" style="gap: 12px;">
            <a href="admin_course_list.php" class="btn btn-secondary px-3 py-2">一覧へ戻る</a>
            <a href="admin_course_edit.php?course_id=<?= $course["course_id"] ?>" class="btn btn-success px-3 py-2" style="margin-top: 10px;">編集</a>
            <a href="php_do/course_del_do.php?course_id=<?= $course["course_id"] ?>" class="btn btn-danger px-3 py-2" style="margin-top: 10px;">削除</a>
        </div>
    </div>

    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>