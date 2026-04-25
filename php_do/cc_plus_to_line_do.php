<?php
require_once __DIR__ . '/../functions/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$cc_plus_booking_id = (int) ($_POST['cc_plus_booking_id'] ?? 0);
$to_slot_id         = (int) ($_POST['to_slot_id']         ?? 0);
$to_time_id         = (int) ($_POST['to_time_id']         ?? 0);

if (!$cc_plus_booking_id || !$to_slot_id || !$to_time_id) {
    echo json_encode(['success' => false, 'message' => 'パラメータ不正']);
    exit;
}

$result = register_cc_plus_to_line($cc_plus_booking_id, $to_slot_id, $to_time_id);
echo json_encode(['success' => $result]);
