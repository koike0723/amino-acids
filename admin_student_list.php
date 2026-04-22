<!-- 生徒画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';
try {
    $students = get_students();
} catch (PDOException $e) {
    check($e);
}
?>
<?php
$db = db_connect();
$sql = 'SELECT * FROM m_rooms ORDER BY id ASC,name ASC';
$stmt = $db->prepare($sql);
$stmt->execute();
$course_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 生徒一覧</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <p class="h1">生徒一覧</p>
        <div class="admin-student-list">
            <div class="admin-stuent-search-area">
                <div class="admin-student-flex">
                    <p class="ad-stu_search">コース</p>
                    <p class="ad-stu_search">名前</p>
                    <p class="ad-stu_search">状態</p>
                    <div class="admin-student-flex-btn">
                        <div class="search-btn">
                            <p class="ad-stu_search">詳細設定</p>
                        </div>
                        <div class="search-btn">
                            <p class="ad-stu_search">○</p>
                        </div>
                        <!-- 検索欄横の追加ボタン -->
                    </div>
                </div>
            </div>
        </div>
        <div class="admin-student-select">
            <div class="admin-add-flex">
                <label for="ad-stu_select">
                    <select name="ad-stu_select" id="ad-stu_select" class="cc-select_style">
                        <?php foreach ($course_name as $course): ?>
                            <option value="<?= h($course['id']); ?>" style="background-color: white;"><?= h($course['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <a href="#">
                    <button type="btn" class="add-btn">生徒追加</button>
                </a>
                <a href="#">
                    <button type="btn" class="add-btn">生徒一括追加</button>
                </a>
            </div>
        </div>
        <div class="table-area">
            <table class="ad-stu-list-table">
                <thead>
                    <tr class="ad-stu-headTr">
                        <th class="ad-stu-list-th">名前</th>
                        <th class="ad-stu-list-th">訓練名</th>
                        <th class="ad-stu-list-th">状態</th>
                        <th class="ad-stu-list-th">利用可</th>
                        <th class="ad-stu-list-th">操作</th>
                    </tr>
                </thead>
                <tbody class="ad-stu-list-tbody">
                    <?php foreach ($students as $student): ?>
                        <tr class="ad-stu-list-tr">
                            <td class="ad-stu-list-td"><?= h($student['student_name']); ?></td>
                            <td class="ad-stu-list-td"><?= h($student['room_name']); ?>&nbsp;/&nbsp;<?= h($student['course_name']); ?></td>
                            <td class="ad-stu-list-td"><?= h($student['status_name']); ?></td>
                            <td class="ad-stu-list-td">利用可</td>
                            <td class="ad-stu-list-td td-btn-flex">
                                <a href="#">
                                    <button type="btn" class="controle-btn controle-edit">編集</button>
                                </a>
                                <a href="#">
                                    <button type="btn" class="controle-btn controle-detail">詳細</button>
                                </a>
                                <a href="#">
                                    <button type="btn" class="controle-btn controle-delete">削除</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="previous-btn">
            <a href="#">
                <button type="btn" class="prev-btn">戻る</button>
            </a>
        </div>
    </main>
</body>
<script src="./js/script.js"></script>
<script src="./js/hamburger.js"></script>
</html>