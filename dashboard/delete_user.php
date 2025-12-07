<?php
require '../db.php';
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    die("คุณไม่มีสิทธิ์");
}

$id = intval($_GET['id']);

$del = $pdo->prepare("DELETE FROM users WHERE id = ?");
$del->execute([$id]);

echo "<script>alert('ลบผู้ใช้แล้ว'); location='admin_dashboard.php';</script>";
