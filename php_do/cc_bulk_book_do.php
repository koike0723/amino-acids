<?php
require_once __DIR__ . '/../functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['course_id']) || !ctype_digit((string) $_POST['course_id'])) {
    header('Location: ../admin_course_list.php');
    exit;
}

$course_id = (int) $_POST['course_id'];

$result = bulk_book_cc($course_id);

if ($result) {
    header('Location: ../admin_course_detail.php?course_id=' . $course_id . '&status=bulk_book_success');
} else {
    header('Location: ../admin_cc_bulk_book.php?course_id=' . $course_id . '&status=error');
}
exit;
