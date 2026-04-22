<!-- 管理者側 生徒一覧画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

try {
    $students = get_students();
} catch (PDOException $e) {
    check($e);
}

$db = db_connect();

/* コース一覧取得（上の追加欄にも使う） */
$sql = 'SELECT * FROM m_rooms ORDER BY id ASC, name ASC';
$stmt = $db->prepare($sql);
$stmt->execute();
$course_name = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------------
   絞り込み条件の受け取り
----------------------------- */
$date = $_GET['date'] ?? '';
$status = $_GET['status'] ?? '';
$course = $_GET['course'] ?? '';

/* -----------------------------
   プルダウン用の選択肢を作成
----------------------------- */
$status_list = [];
$course_list = [];

foreach ($students as $student) {
    if (!in_array($student['status_name'], $status_list, true)) {
        $status_list[] = $student['status_name'];
    }

    if (!in_array($student['course_name'], $course_list, true)) {
        $course_list[] = $student['course_name'];
    }
}

/* 並び替えして見やすくする */
sort($status_list);
sort($course_list);

/* -----------------------------
   絞り込み実行
----------------------------- */
$filtered_students = array_filter($students, function ($student) use ($date, $status, $course) {
    if ($date !== '' && $student['end_date'] !== $date) {
        return false;
    }

    if ($status !== '' && $student['status_name'] !== $status) {
        return false;
    }

    if ($course !== '' && $student['course_name'] !== $course) {
        return false;
    }

    return true;
});
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
        <h1 class="h1">生徒一覧</h1>

        <!-- 生徒一括追加機能 選択欄 -->
        <div class="admin-student-select">
            <div class="admin-add-flex">
                <a href="./admin_student_add.php">
                    <button type="button" class="add-btn">生徒追加</button>
                </a>

                <a href="./admin_student_add_bulk.php">
                    <button type="button" class="add-btn">生徒一括追加</button>
                </a>
            </div>
        </div>

        <!-- 生徒絞り込み機能 項目選択欄 -->
        <div class="course-search container-fluid mb-4">
            <form action="admin_student_list.php" method="get">
                <!-- 入力欄 -->
                <div class="row justify-content-center">
                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                        <label for="date" class="form-label">日付</label>
                        <input type="date" name="date" id="date" value="<?= h($date); ?>" class="form-control">
                    </div>

                    <div class="col-12 col-md-4 col-lg-2 mb-3">
                        <label for="status" class="form-label">状態</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">全表示</option>
                            <?php foreach ($status_list as $status_item): ?>
                                <option value="<?= h($status_item); ?>" <?= ($status === $status_item) ? 'selected' : ''; ?>>
                                    <?= h($status_item); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 col-lg-4 mb-3">
                        <label for="course" class="form-label">コース</label>
                        <select name="course" id="course" class="form-control">
                            <option value="">全表示</option>
                            <?php foreach ($course_list as $course_item): ?>
                                <option value="<?= h($course_item); ?>" <?= ($course === $course_item) ? 'selected' : ''; ?>>
                                    <?= h($course_item); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- ボタン -->
                <div class="row justify-content-center">
                    <div class="col-auto mb-2">
                        <button type="submit" name="filter" class="btn btn-info px-5">
                            絞り込む
                        </button>
                    </div>

                    <div class="col-auto mb-2">
                        <a href="admin_student_list.php" class="btn btn-secondary px-4">
                            絞り込み解除
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- 生徒一覧表示テーブル -->
        <div class="table-area">
            <table class="ad-stu-list-table">
                <thead>
                    <tr class="ad-stu-headTr">
                        <th class="ad-stu-list-th">名前</th>
                        <th class="ad-stu-list-th">訓練名</th>
                        <th class="ad-stu-list-th">状態</th>
                        <th class="ad-stu-list-th">操作</th>
                    </tr>
                </thead>
                <tbody class="ad-stu-list-tbody">
                    <?php if (empty($filtered_students)): ?>
                        <tr>
                            <td colspan="4" class="ad-stu-list-td">該当する生徒はいません。</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filtered_students as $student): ?>
                            <tr class="ad-stu-list-tr">
                                <td class="ad-stu-list-td"><?= h($student['student_name']); ?></td>
                                <td class="ad-stu-list-td">
                                    <?= h($student['room_name']); ?>&nbsp;/&nbsp;<?= h($student['course_name']); ?>
                                </td>
                                <td class="ad-stu-list-td"><?= h($student['status_name']); ?></td>
                                <td class="ad-stu-list-td td-btn-flex">
                                    <a href="./admin_student_edit.php?id=<?= h($student['student_id']); ?>">
                                        <button type="button" class="controle-btn controle-edit">編集</button>
                                    </a>
                                    <a href="./admin_student_detail.php?id=<?= h($student['student_id']); ?>">
                                        <button type="button" class="controle-btn controle-detail">詳細</button>
                                    </a>
                                    <a href="./admin_student_del_confirm.php?id=<?= h($student['student_id']); ?>">
                                        <button type="button" class="controle-btn controle-delete">削除</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="previous-btn">
            <a href="#">
                <button type="button" class="prev-btn">戻る</button>
            </a>
        </div>
    </main>
</body>

<script src="./js/script.js"></script>
<script src="./js/hamburger.js"></script>
</html>