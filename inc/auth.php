<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

function isAdmin() {
    return $_SESSION['user']['role'] === 'admin';
}
function isEmployee() {
    return $_SESSION['user']['role'] === 'employee';
}
function isUser() {
    return $_SESSION['user']['role'] === 'user';
}
