<?php
require_once __DIR__ . '/../functions/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$date = $_POST['cc_date'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'パラメータ不正']);
    exit;
}

$slot_id = add_cc_slot($date);
echo json_encode(['success' => (bool) $slot_id]);
