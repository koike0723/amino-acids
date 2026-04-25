<?php
require_once __DIR__ . '/../functions/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$slot_id       = (int) ($_POST['slot_id'] ?? 0);
$room_id       = ($_POST['room_id']       !== '') ? (int) $_POST['room_id']       : null;
$consultant_id = ($_POST['consultant_id'] !== '') ? (int) $_POST['consultant_id'] : null;

if (!$slot_id) {
    echo json_encode(['success' => false, 'message' => 'パラメータ不正']);
    exit;
}

try {
    $db = db_connect();
    $stmt = $db->prepare(
        'UPDATE t_cc_slots
         SET room_id = :room_id, consultant_id = :consultant_id
         WHERE id = :slot_id AND is_cc_plus = 0'
    );
    $stmt->execute([':room_id' => $room_id, ':consultant_id' => $consultant_id, ':slot_id' => $slot_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
