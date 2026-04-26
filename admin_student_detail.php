<!-- 生徒詳細画面 -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions/functions.php';
require_admin_login();
?>

<?php
$id = $_GET['id'];
if (!empty($_GET['id'])) {
    try {
        $student = get_student($id);
    } catch (PDOException $e) {
        check($e);
    }
} else {
    header('location:admin_student_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 生徒詳細</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <div class="content-wrap" style="width: 89.33333%; max-width: 1000px; margin-inline: auto;">

        <h1 class="m-5">生徒詳細</h1>

        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">名前：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($student['student_name']); ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">コース名：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($student['room_name']); ?> / <?= h($student['course_name']); ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-2">
            <label class="form-label mb-0 text-nowrap">出席番号：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($student['number']); ?></p>
        </div>
        <div class="d-flex align-items-baseline mb-3">
            <label class="form-label mb-0 text-nowrap">状態：</label>
            <p class="form-control-plaintext py-0 mb-0"><?= h($student['status_name']); ?></p>
        </div>

        <div class="col-12 mb-4">
            <label class="form-label">キャリコン履歴</label>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 ad-index-table">
                            <thead>
                                <tr>
                                    <th class="ad-index-th">種別</th>
                                    <th class="ad-index-th">日付</th>
                                    <th class="ad-index-th">時間</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($student['bookings'])): ?>
                                    <tr>
                                        <td colspan="3" class="ad-index-td">キャリコン履歴なし</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($student['bookings'] as $booking): ?>
                                        <tr>
                                            <td class="ad-index-td"><?= $booking['is_cc_plus'] ? 'cc+' : 'cc'; ?></td>
                                            <td class="ad-index-td"><?= h($booking['cc_date']); ?></td>
                                            <td class="ad-index-td"><?= h($booking['cc_time']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex mt-4 mb-5" style="gap: 12px;">
            <a href="./admin_student_list.php" class="btn btn-secondary px-3 py-2">一覧へ戻る</a>
            <a href="admin_student_edit.php?id=<?= h($student['student_id']); ?>" class="btn btn-success px-3 py-2" style="margin-top: 10px;">編集</a>
            <button type="button" class="btn btn-danger px-3 py-2" style="margin-top: 10px;"
                data-toggle="modal" data-target="#deleteModal"
                data-message="生徒「<?= h($student['student_name']) ?>」を削除してもよいですか？">
                削除
            </button>
        </div>

    </div>
    <?php require_once __DIR__ . '/inc/delete_modal.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
    <script>
        $('#deleteModal').on('show.bs.modal', function(event) {
            var message = $(event.relatedTarget).data('message');
            $(this).find('#deleteModalMessage').text(message);
        });
    </script>
</body>

</html>