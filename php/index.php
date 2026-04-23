<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check role
if ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'MANAGER') {
    header('Location: dashboard.php');
    exit;
} else {
    // This shouldn't happen with our new login logic, but for safety:
    header('Location: logout.php');
    exit;
}
?>
