<?php
session_start();

function current_user_role() {
    return $_SESSION['role'] ?? null;
}

function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: login.php");
        exit;
    }
}
?>
