<?php
// File ini di-include di setiap halaman yang butuh login
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Optional: Check role
function checkRole($required_role) {
    if($_SESSION['role'] != $required_role) {
        die("Access denied! You don't have permission.");
    }
}
?>