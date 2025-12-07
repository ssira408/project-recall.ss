<?php
require '../db.php';
include '../inc/header.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    die("คุณไม่มีสิทธิ์");
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if(!$user) die("ไม่พบผู้ใช้");

if($_POST){
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];

    $update = $pdo->prepare("UPDATE users SET fullname=?, role=? WHERE id=?");
    $update->execute([$fullname, $role, $id]);

    echo "<script>alert('แก้ไขสำเร็จ'); location='admin_dashboard.php';</script>";
}
?>

<h2>แก้ไขผู้ใช้</h2>

<form method="post">
    ชื่อ-นามสกุล:
    <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>"><br><br>

    บทบาท:
    <select name="role">
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
        <option value="employee" <?= $user['role']=='employee'?'selected':'' ?>>Employee</option>
        <option value="teacher" <?= $user['role']=='teacher'?'selected':'' ?>>Teacher</option>
    </select><br><br>

    <button type="submit">บันทึก</button>
</form>
