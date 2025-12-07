<?php
require '../db.php';
include '../inc/header.php';

// ตรวจสอบสิทธิ์ admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("<p style='color:red;'>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</p>");
}

// ดึงสรุปข้อมูล
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$project_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

// ดึงผู้ใช้ทั้งหมด
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// ดึงโครงการทั้งหมด (เพิ่มข้อมูล)
$projects = $pdo->query("
    SELECT *
    FROM projects
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Admin Dashboard</h2>

<p>สวัสดี, <?= htmlspecialchars($_SESSION['user']['email']) ?></p>

<div style="margin:15px 0; padding:10px; border:1px solid #ffc400; border-radius:8px;">
    <strong>จำนวนผู้ใช้ทั้งหมด:</strong> <?= $user_count ?><br>
    <strong>จำนวนโครงการทั้งหมด:</strong> <?= $project_count ?>
</div>

<!-- =========================== USERS TABLE =========================== -->
<h3>ผู้ใช้ทั้งหมด</h3>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%; margin-bottom:20px;">
    <tr style="background:#b40000; color:#fff;">
        <th>ID</th>
        <th>Email</th>
        <th>Fullname</th>
        <th>Role</th>
        <th>จัดการ</th>
    </tr>
    <?php foreach($users as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['fullname']) ?></td>
        <td><?= $u['role'] ?></td>
        <td>
            <a href="edit_user.php?id=<?= $u['id'] ?>"><button>แก้ไข</button></a>
            <a href="delete_user.php?id=<?= $u['id'] ?>" 
               onclick="return confirm('คุณแน่ใจหรือไม่?')">
               <button style="background:red; color:white;">ลบ</button>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- =========================== PROJECT TABLE =========================== -->
<h3>โครงการทั้งหมด</h3>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <tr style="background:#b40000; color:#fff;">
        <th>ID</th>
        <th>ชื่อโครงการ</th>
        <th>ผู้จัดทำ</th>
        <th>ที่ปรึกษา</th>
        <th>วันที่สร้าง</th>
        <th>สถานะ</th>
        <th>แผนก</th>
        <th>จัดการ</th>
    </tr>

    <?php foreach($projects as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['title']) ?></td>
        <td><?= htmlspecialchars($p['author']) ?></td>
        <td>
            <?= htmlspecialchars($p['advisor_main']) ?><br>
            <small>(Co: <?= htmlspecialchars($p['advisor_co']) ?>)</small>
        </td>
        <td><?= $p['created_at'] ?></td>
        <td><?= $p['status'] ?></td>
        <td><?= $p['department'] ?></td>
        <td>
            <a href="view_project.php?id=<?= $p['id'] ?>"><button>ดู</button></a>
            <a href="delete_project.php?id=<?= $p['id'] ?>" 
               onclick="return confirm('ลบโครงการนี้?')">
               <button style="background:red; color:white;">ลบ</button>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

