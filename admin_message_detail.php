<!-- 管理者メッセージ詳細画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

/////////////////////////////////////////////////
// GET通信処理
/////////////////////////////////////////////////
$request_id = (int) ($_GET['request_id'] ?? 0);

if (!$request_id) {
  header('Location: ./admin_message.php');
  exit;
}

try {
  $detail = get_cc_request_detail($request_id);
} catch (PDOException $e) {
  $detail = [];
}
check($detail);

if (empty($detail)) {
  header('Location: ./admin_message.php');
  exit;
}

// 新規（1）・未対応（2）のみ操作可能
$is_unresolved = in_array($detail['status_id'], [1, 2]);
?>

<!doctype html>
<html lang="ja">

<head>
  <title>メッセージ詳細</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
  <?php require_once('./inc/admin_header.php'); ?>

  <main class="container py-5">
    <section class="message-section">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
          <h1 class="mb-5 text-center">メッセージ詳細</h1>

          <!-- 申請共通情報 -->
          <table class="table table-bordered mb-4">
            <tr>
              <th class="table-secondary w-25">申請者</th>
              <td><?= h($detail['student_name']) ?>（<?= h($detail['course_full_name']) ?>）</td>
            </tr>
            <tr>
              <th class="table-secondary">申請種別</th>
              <td><?= h($detail['type_name']) ?></td>
            </tr>
            <tr>
              <th class="table-secondary">対応状況</th>
              <td><?= h($detail['status_label']) ?></td>
            </tr>
            <tr>
              <th class="table-secondary">状態</th>
              <td><?= h($detail['status_name']) ?></td>
            </tr>
            <tr>
              <th class="table-secondary">申請日時</th>
              <td><?= h(date('Y/m/d H:i', strtotime($detail['created_at']))) ?></td>
            </tr>
            <?php if (!empty($detail['message'])): ?>
              <tr>
                <th class="table-secondary">申請メッセージ</th>
                <td><?= nl2br(h($detail['message'])) ?></td>
              </tr>
            <?php endif; ?>
          </table>

          <!-- タイプ別詳細情報 -->

          <?php if ((int)$detail['type_id'] === 1): // CC+予約申請 
          ?>
            <h5 class="mb-3">予約情報</h5>
            <table class="table table-bordered mb-4">
              <tr>
                <th class="table-secondary w-25">予約日</th>
                <td><?= h($detail['detail']['cc_date']) ?></td>
              </tr>
              <tr>
                <th class="table-secondary">時間</th>
                <td><?= h($detail['detail']['start_time']) ?></td>
              </tr>
              <tr>
                <th class="table-secondary">面談方法</th>
                <td><?= h($detail['detail']['style_name']) ?></td>
              </tr>
            </table>

          <?php elseif ((int)$detail['type_id'] === 2): // CC+変更申請 
          ?>
            <div class="row mb-4">
              <div class="col-md-6">
                <h5 class="mb-3">変更前</h5>
                <table class="table table-bordered">
                  <tr>
                    <th class="table-secondary">予約日</th>
                    <td><?= h($detail['detail']['before']['cc_date']) ?></td>
                  </tr>
                  <tr>
                    <th class="table-secondary">時間</th>
                    <td><?= h($detail['detail']['before']['start_time']) ?></td>
                  </tr>
                  <tr>
                    <th class="table-secondary">面談方法</th>
                    <td><?= h($detail['detail']['before']['style_name']) ?></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h5 class="mb-3">変更後</h5>
                <table class="table table-bordered">
                  <tr>
                    <th class="table-secondary">予約日</th>
                    <td><?= h($detail['detail']['after']['cc_date']) ?></td>
                  </tr>
                  <tr>
                    <th class="table-secondary">時間</th>
                    <td><?= h($detail['detail']['after']['start_time']) ?></td>
                  </tr>
                  <tr>
                    <th class="table-secondary">面談方法</th>
                    <td><?= h($detail['detail']['after']['style_name']) ?></td>
                  </tr>
                </table>
              </div>
            </div>

          <?php elseif ((int)$detail['type_id'] === 3): // CC+キャンセル申請 
          ?>
            <h5 class="mb-3">キャンセル対象予約</h5>
            <table class="table table-bordered mb-4">
              <tr>
                <th class="table-secondary w-25">予約日</th>
                <td><?= h($detail['detail']['cc_date']) ?></td>
              </tr>
              <tr>
                <th class="table-secondary">時間</th>
                <td><?= h($detail['detail']['start_time']) ?></td>
              </tr>
              <tr>
                <th class="table-secondary">面談方法</th>
                <td><?= h($detail['detail']['style_name']) ?></td>
              </tr>
            </table>

          <?php elseif ((int)$detail['type_id'] === 4): // 必須CC変更申請 
          ?>
            <div class="row mb-4">
              <div class="col-md-6">
                <h5 class="mb-3">申請者（<?= h($detail['detail']['my_self']['student_name']) ?>）</h5>
                <table class="table table-bordered">
                  <tr>
                    <th class="table-secondary">現在の日時</th>
                    <td><?= h($detail['detail']['my_self']['from_cc_date']) ?></td>
                  </tr>
                  <tr>
                    <th class="table-secondary">変更後の日時</th>
                    <td><?= h($detail['detail']['my_self']['to_cc_date']) ?></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h5 class="mb-3">相手（<?= h($detail['detail']['target']['student_name']) ?>）</h5>
                <table class="table table-bordered">
                  <tr>
                    <th class="table-secondary">現在の日時</th>
                    <td><?= h($detail['detail']['target']['from_cc_date']) ?></td>
                  </tr>
                  <tr>
                    <th class="table-secondary">変更後の日時</th>
                    <td><?= h($detail['detail']['target']['to_cc_date']) ?></td>
                  </tr>
                </table>
              </div>
            </div>

          <?php endif; ?>

          <!-- 操作ボタン（未解決のみ表示） -->
          <?php if ($is_unresolved): ?>
            <div class="d-flex justify-content-center gap-3 mb-4">
              <form action="./admin_message_do.php" method="post">
                <input type="hidden" name="request_id" value="<?= $request_id ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-primary px-4"
                  onclick="return confirm('承認してよろしいですか？')">承認</button>
              </form>
              <form action="./admin_message_do.php" method="post">
                <input type="hidden" name="request_id" value="<?= $request_id ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn btn-danger px-4"
                  onclick="return confirm('却下してよろしいですか？')">却下</button>
              </form>
            </div>
          <?php endif; ?>

          <div class="text-center mt-3">
            <a href="./admin_message.php" class="btn btn-secondary">一覧に戻る</a>
          </div>

        </div>
      </div>
    </section>
  </main>

  <script src="./js/script.js"></script>
</body>

</html>