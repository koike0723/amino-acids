<?php
require_once __DIR__ . '/../functions/functions.php';

/////////////////////////////////////////////////
// POST通信処理
/////////////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_message.php');
  exit;
}

$request_id = (int) ($_POST['request_id'] ?? 0);
$action     = $_POST['action'] ?? '';  // 'approve' or 'reject'

if (!$request_id || !in_array($action, ['approve', 'reject'], true)) {
  header('Location: ../admin_message.php');
  exit;
}

// type_idをサーバーサイドで取得（フォームからは受け取らない）
$detail = get_cc_request_detail($request_id);

if (empty($detail)) {
  header('Location: ../admin_message.php');
  exit;
}

// 対応済み（承認=3 / 却下=4）なら何もしない
if (in_array((int)$detail['status_id'], [3, 4])) {
  header('Location: ../admin_message_detail.php?request_id=' . $request_id);
  exit;
}

$type_id = (int) $detail['type_id'];
$success = false;

if ($action === 'approve') {
  switch ($type_id) {
    case 1:
      $success = approve_cc_plus($request_id);
      break;
    case 2:
      $success = approve_cc_plus_change($request_id);
      break;
    case 3:
      $success = approve_cc_plus_cancel($request_id);
      break;
    case 4:
      $success = approve_cc_change($request_id);
      break;
  }
} else {
  switch ($type_id) {
    case 1:
      $success = reject_cc_plus($request_id);
      break;
    case 2:
      $success = reject_cc_plus_change($request_id);
      break;
    case 3:
      $success = reject_cc_plus_cancel($request_id);
      break;
    case 4:
      $success = reject_cc_change($request_id);
      break;
  }
}

header('Location: ../admin_message_detail.php?request_id=' . $request_id);
exit;
