<?php
require_once __DIR__ . '/../functions/functions.php';

$date          = $_POST['date'] ?? '';
$cc_plus_count = (int) ($_POST['cc_plus_count'] ?? 0);

if ($date && $cc_plus_count >= 0) {
    update_cc_plus_slot_count($date, $cc_plus_count);
}

$params = array_filter([
    'course_id'  => $_POST['course_id']  ?: null,
    'range'      => $_POST['range']      ?: null,
    'start_date' => $_POST['start_date'] ?: null,
]);
header('Location: ../admin_index.php' . ($params ? '?' . http_build_query($params) : ''));
exit;
