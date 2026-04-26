<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/students.php';
require_once __DIR__ . '/includes/courses.php';
require_once __DIR__ . '/includes/cc_slots.php';
require_once __DIR__ . '/includes/cc_bookings.php';
require_once __DIR__ . '/includes/requests.php';
require_once __DIR__ . '/includes/admins.php';

function require_admin_login(): void
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ./inc/admin_login.php');
        exit();
    }
}