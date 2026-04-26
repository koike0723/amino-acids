<?php require_once __DIR__ . '/functions/functions.php'; ?>
<?php
if (!isset($_GET['course_id']) || !ctype_digit((string) $_GET['course_id'])) {
    header('Location: admin_course_list.php');
    exit;
}
$course_id = (int) $_GET['course_id'];
?>

<?php
try {
    $course = get_course($course_id);
} catch (PDOException $e) {
    exit('コース情報の取得に失敗しました: ' . $e->getMessage());
}

if (empty($course) || $course['category_id'] != 1) {
    header('Location: admin_course_list.php');
    exit;
}

try {
    $students = get_students(['course_id' => $course_id], true);
} catch (PDOException $e) {
    exit('生徒一覧の取得に失敗しました: ' . $e->getMessage());
}
$student_count = count($students);
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
    <title>-管理者- 必須CC一括登録</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <div class="container-fluid px-4 py-4">
            <h1 class="h3 font-weight-bold text-center mb-4">必須CC一括登録</h1>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                <div class="alert alert-danger">一括登録に失敗しました。再度お試しください。</div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5 font-weight-bold mb-3"><?= h($course['room_name']) ?> / <?= h($course['course_name']) ?></h2>

                    <dl class="row mb-0">
                        <dt class="col-3">CCスケジュール</dt>
                        <dd class="col-9">
                            <?php if (empty($course['cc'])): ?>
                                <span class="text-muted">スケジュール未設定</span>
                            <?php else: ?>
                                <?php foreach ($course['cc'] as $cc_count => $dates): ?>
                                    <div>第<?= (int) $cc_count ?>回：
                                        <?php foreach ($dates as $i => $date): ?>
                                            <?= $i > 0 ? '、' : '' ?><?= h(format_japanese_date($date)) ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-3 mt-2">対象生徒数</dt>
                        <dd class="col-9 mt-2"><?= $student_count ?>名</dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 ad-index-table">
                            <thead>
                                <tr>
                                    <th class="col-1 ad-index-th">出席番号</th>
                                    <th class="col-4 ad-index-th">生徒名</th>
                                    <th class="col-2 ad-index-th">状態</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="3" class="ad-index-td">対象生徒がいません。</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td class="ad-index-td"><?= h($student['number']) ?></td>
                                            <td class="ad-index-td"><?= h($student['student_name']) ?></td>
                                            <td class="ad-index-td"><?= h($student['status_name']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-muted" style="font-size: 0.875rem;">
                ※ 既に予約済みの生徒はスキップされます。
            </p>

            <div class="d-flex align-items-center mt-3" style="gap: 12px;">
                <form method="POST" action="./php_do/cc_bulk_book_do.php" class="m-0">
                    <input type="hidden" name="course_id" value="<?= h($course_id) ?>">
                    <button type="submit" class="btn btn-primary px-4 py-2" style="margin-top: 10px;">一括登録</button>
                </form>
                <a href="admin_course_detail.php?course_id=<?= h($course_id) ?>"
                    class="btn btn-secondary px-4 py-2">
                    戻る
                </a>
            </div>
        </div>
    </main>
    <script src="./js/hamburger.js"></script>
</body>

</html>
