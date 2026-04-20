<!-- http://localhost:8080/amino-acids/admin_course_list.php -->
<!-- コース一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>
<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
    $courses = get_courses(null, false, null, null);
} catch (PDOException $e) {
    exit('情報の取得に失敗しました: ' . $e->getMessage());
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
                <form action="search" class="mx-auto">
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



            <table class="table table-striped">
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
                                <a class="btn btn-success mx-1 my-1" href="./admin_course_edit.php">編集</a>
                                <a class="btn btn-info mx-1 my-1" href="./admin_course_detail.php">詳細</a>
                                <a class="btn btn-danger mx-1 my-1" href="./admin_course_del_confirm.php">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <div class="l-btn-area">
                <a class="btn btn-secondary" href="./admin_index.php">トップに戻る</a>
            </div>
        </main>
        <script src="./js/admin_course_search.js"></script>
    </div>
</body>

</html>