<?php
require_once __DIR__ . '/../functions/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$booking_id_a = (int) ($_POST['booking_id_a'] ?? 0);
$booking_id_b = (int) ($_POST['booking_id_b'] ?? 0);

if (!$booking_id_a || !$booking_id_b) {
    echo json_encode(['success' => false, 'message' => 'パラメータ不正']);
    exit;
}

echo json_encode(['success' => swap_cc_bookings($booking_id_a, $booking_id_b)]);
