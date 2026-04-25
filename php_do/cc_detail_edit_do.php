<!-- 必須キャリコン編集実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_index.php');
    exit;
}

$cc_date        = $_POST['cc_date']        ?? '';
$room_ids       = $_POST['room_id']        ?? [];
$consultant_ids = $_POST['consultant_id']  ?? [];

// cc_dateバリデーション
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $cc_date)) {
    header('Location: ../admin_index.php');
    exit;
}

try {
    $db = db_connect();
    $stmt = $db->prepare(
        'UPDATE t_cc_slots
         SET room_id = :room_id, consultant_id = :consultant_id
         WHERE id = :slot_id AND is_cc_plus = 0'
    );

    foreach ($room_ids as $slot_id => $room_id) {
        $slot_id       = (int) $slot_id;
        $room_id       = ($room_id !== '') ? (int) $room_id : null;
        $consultant_id = isset($consultant_ids[$slot_id]) && $consultant_ids[$slot_id] !== ''
            ? (int) $consultant_ids[$slot_id]
            : null;

        $stmt->execute([
            ':room_id'       => $room_id,
            ':consultant_id' => $consultant_id,
            ':slot_id'       => $slot_id,
        ]);
    }
} catch (PDOException $e) {
    exit('編集の保存に失敗しました: ' . $e->getMessage());
}

header('Location: ../admin_cc_detail.php?cc_date=' . urlencode($cc_date));
exit;
