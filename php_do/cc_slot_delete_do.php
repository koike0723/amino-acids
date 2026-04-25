<?php
require_once __DIR__ . '/../functions/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$slot_id = (int) ($_POST['slot_id'] ?? 0);
if (!$slot_id) {
    echo json_encode(['success' => false, 'message' => 'パラメータ不正']);
    exit;
}

echo json_encode(['success' => delete_cc_slot($slot_id)]);
