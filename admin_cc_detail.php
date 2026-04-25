<!-- http://localhost:8080/amino-acids/admin_cc_detail.php?cc_date=2026-04-25 -->
<!-- 必須キャリコンをドラック&ドロップで管理できる管理者画面 -->
<?php require_once __DIR__ . '/functions/functions.php'; ?>
<?php
// GETパラメータのバリデーション
if (!isset($_GET['cc_date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['cc_date'])) {
  header('Location: admin_index.php');
  exit;
}
$cc_date = $_GET['cc_date'];
?>

<?php
// データベース処理
try {
  $db = db_connect();
  $stmt = $db->prepare('SELECT id, last_name, first_name, CONCAT(last_name, first_name) AS name FROM m_consultants');
  $stmt->execute();
  $cc_teachers = $stmt->fetchAll();
} catch (PDOException $e) {
  exit('キャリアコンサルタント達の取得に失敗しました: ' . $e->getMessage());
}
try {
  $db = db_connect();
  $stmt = $db->prepare('SELECT id, name FROM m_rooms');
  $stmt->execute();
  $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
  exit('教室データ（m_rooms）の取得に失敗しました: ' . $e->getMessage());
}
try {
  $db = db_connect();
  $stmt = $db->prepare('SELECT id, start_time, display_name AS name FROM m_times');
  $stmt->execute();
  $cc_times = $stmt->fetchAll();
  $time_id_map = array_column($cc_times, 'id', 'start_time');
} catch (PDOException $e) {
  exit('ccの時間情報（m_times）の取得に失敗しました: ' . $e->getMessage());
}
try {
  $cc_slots = get_cc_slots(CC_SLOT_TYPE::Line->name, $cc_date);
} catch (PDOException $e) {
  exit('必須キャリコンのラインの取得に失敗しました: ' . $e->getMessage());
}
try {
  $cc_plus_slots = get_cc_slots(CC_SLOT_TYPE::CcPlus->name, $cc_date);
} catch (PDOException $e) {
  exit('任意キャリコンのラインの取得に失敗しました: ' . $e->getMessage());
}
try {
  $cc_all_bookings = get_cc_bookings(["slot_date" => $cc_date]);
} catch (PDOException $e) {
  exit('キャリコンの予約の取得に失敗しました: ' . $e->getMessage());
}
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
  <link rel="stylesheet" href="./css/kan.css">
  <title>-管理者- キャリコン管理</title>
</head>

<body>
  <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
  <main>

    <div class="cc-mgmt">
      <p class="cc-head-text">キャリコン・ライン管理</p>
      <p class="cc-head-date"><?= format_japanese_date($cc_date); ?></p>
      <div class="kan_btn kan_back-btn"><a href="admin_index.php">一覧へ戻る</a></div>
    </div>
    <div class="kan_btn kan_open-btn" id="open_btn"><button type="button">任意キャリコンを開く ▶</button></div>

    <div class="content-wrap" id="drag_drop_area">
      <form method="POST" action="php_do/cc_detail_edit_do.php">
        <input type="hidden" name="cc_date" value="<?= h($cc_date) ?>">

        <div class="kan_btn kan_btn-confirm"><input type="submit" name="update" value="編集確定する"></div>


        <div class="cc-plus-table-area" id="drawer_area">
          <div class="content-wrap">
            <div class="cc-plus-list-flex">
              <div class="kan_btn kan_close-btn"><button type="button" id="close_btn">◀ 閉じる</button></div>
              <div>任意キャリコン枠</div>
            </div>
            <?php foreach ($cc_plus_slots as $key => $cc_plus_slot): ?>
              <table class="cc-plus-table" style="background-color: white;">
                <thead class="cc-detail-thead">
                  <tr class="cc-detail-headTr">
                    <?php foreach ($cc_times as $cc_time): ?>
                      <th class="cc-detail-th"><?= $cc_time["name"] ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody class="cc-detail-tbody">
                  <tr class="cc-detail-tr">
                    <!-- 10:00:00 -->
                    <?php $time = "10:00:00"; ?>
                    <td class="cc-detail-td">
                      <?php if (empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 11:00:00 -->
                    <?php $time = "11:00:00"; ?>
                    <td class="cc-detail-td">
                      <?php if (empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 12:00:00 -->
                    <?php $time = "12:00:00"; ?>
                    <td class="cc-detail-td">
                      <?php if (empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 14:00:00 -->
                    <?php $time = "14:00:00"; ?>
                    <td class="cc-detail-td">
                      <?php if (empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 15:00:00 -->
                    <?php $time = "15:00:00"; ?>
                    <td class="cc-detail-td">
                      <?php if (empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 16:00:00 -->
                    <?php $time = "16:00:00"; ?>
                    <td class="cc-detail-td">
                      <?php if (empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_plus_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_plus_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="cc-detail-table-area">
          <?php foreach ($cc_slots as $cc_slot): ?>
            <?php $slot_id = $cc_slot["slot_id"]; ?>
            <div class="cc_slot">
              <div class="select_box_area">
                <label for="cc-detail-select-class-<?= $slot_id ?>">
                  教室
                  <select name="room_id[<?= $slot_id ?>]" id="cc-detail-select-class-<?= $slot_id ?>" class="cc-detail_selectStyle">
                    <option value="">未選択</option>
                    <?php foreach ($rooms as $room): ?>
                      <option value="<?= h($room["id"]) ?>" <?= ($cc_slot["room_id"] == $room["id"]) ? ' selected' : '' ?>><?= h($room["name"]) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label for="cc-detail-select-teacher-<?= $slot_id ?>">
                  CC講師
                  <select name="consultant_id[<?= $slot_id ?>]" id="cc-detail-select-teacher-<?= $slot_id ?>" class="cc-detail_selectStyle">
                    <option value="">未選択</option>
                    <?php foreach ($cc_teachers as $cc_teacher): ?>
                      <option value="<?= h($cc_teacher["id"]) ?>" <?= ($cc_slot["consultant_id"] == $cc_teacher["id"]) ? ' selected' : '' ?>><?= h($cc_teacher["last_name"]) ?> <?= h($cc_teacher["first_name"]) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
              </div>
              <table class="cc-detail-table">
                <thead class="cc-detail-thead">
                  <tr class="cc-detail-headTr">
                    <?php foreach ($cc_times as $cc_time): ?>
                      <th class="cc-detail-th"><?= $cc_time["name"] ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody class="cc-detail-tbody">
                  <tr class="cc-detail-tr">
                    <!-- 10:00:00 -->
                    <?php $time = "10:00:00"; ?>
                    <td class="cc-detail-td" data-slot-id="<?= $slot_id ?>" data-time-id="<?= $time_id_map[$time] ?>">
                      <?php if (empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 11:00:00 -->
                    <?php $time = "11:00:00"; ?>
                    <td class="cc-detail-td" data-slot-id="<?= $slot_id ?>" data-time-id="<?= $time_id_map[$time] ?>">
                      <?php if (empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 12:00:00 -->
                    <?php $time = "12:00:00"; ?>
                    <td class="cc-detail-td" data-slot-id="<?= $slot_id ?>" data-time-id="<?= $time_id_map[$time] ?>">
                      <?php if (empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 14:00:00 -->
                    <?php $time = "14:00:00"; ?>
                    <td class="cc-detail-td" data-slot-id="<?= $slot_id ?>" data-time-id="<?= $time_id_map[$time] ?>">
                      <?php if (empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 15:00:00 -->
                    <?php $time = "15:00:00"; ?>
                    <td class="cc-detail-td" data-slot-id="<?= $slot_id ?>" data-time-id="<?= $time_id_map[$time] ?>">
                      <?php if (empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                    <!-- 16:00:00 -->
                    <?php $time = "16:00:00"; ?>
                    <td class="cc-detail-td" data-slot-id="<?= $slot_id ?>" data-time-id="<?= $time_id_map[$time] ?>">
                      <?php if (empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                          <div class="cc-detail-student-card" data-booking-id="empty">
                            <p class="cc-detail-student">空き</p>
                          </div>
                      <?php endif; ?>
                      <?php if (!empty($cc_all_bookings[$cc_slot["slot_id"]][$time])): ?>
                        <div class="cc-detail-student-card" draggable="true" data-booking-id="<?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["booking_id"] ?? "empty" ?>">
                          <p class="cc-detail-student">
                            <?= explode("/", $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["course_data"])[0]; ?>
                          </p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["student_name"]; ?></p>
                          <p class="cc-detail-student"><?= $cc_all_bookings[$cc_slot["slot_id"]][$time]["bookings"][0]["style_name"]; ?></p>
                        </div>
                      <?php endif; ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          <?php endforeach; ?>
          <div class="mt-3 text-center" id="display_parent">
            <a href="#" id="add_btn"><img src="img/add_btn.png" alt=""></a>
          </div>
        </div>

      </form>



    </div>
  </main>
  <script src="js/drag_and_drop.js"></script>
  <script src="js/drawer.js"></script>
  <script src="js/hamburger.js"></script>
  <script src="js/add_cc_slot.js"></script>
</body>

</html>