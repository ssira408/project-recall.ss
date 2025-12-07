<?php
require '../db.php';

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

// ===== ตรวจสอบสิทธิ์ =====
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','employee'])) {
    die("<p style='color:red;'>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</p>");
}

$role = $_SESSION['user']['role'];
$department = $_SESSION['user']['department'] ?? ''; 
$success = '';

include '../inc/header.php';

// ===== อัปเดตสถานะโครงการ =====
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if (in_array($action, ['approved','rejected'])) {
        // Employee สามารถแก้เฉพาะโครงการในแผนกตัวเอง
        if ($role === 'employee') {
            $stmt = $pdo->prepare("UPDATE projects SET status=:status WHERE id=:id AND department=:dept");
            $stmt->execute(['status'=>$action, 'id'=>$id, 'dept'=>$department]);
        } else {
            // Admin แก้ได้ทุกโครงการ
            $stmt = $pdo->prepare("UPDATE projects SET status=:status WHERE id=:id");
            $stmt->execute(['status'=>$action, 'id'=>$id]);
        }
        $success = "อัปเดตสถานะโครงการเรียบร้อยแล้ว";
    }
}

// ===== ดึงโครงการ =====
if ($role === 'admin') {
    $projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Employee: เห็นเฉพาะโครงการของแผนกตัวเอง
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE department=:dept ORDER BY id DESC");
    $stmt->execute(['dept'=>$department]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>อนุมัติโครงการ</h2>

<p>
สวัสดี, 
<?= htmlspecialchars($_SESSION['user']['fullname'] ?? $_SESSION['user']['email']) ?>
<?php if ($role === 'employee') echo " (แผนก: ".htmlspecialchars($department).")"; ?>
</p>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%;">
<tr style="background:#b40000; color:#fff;">
    <th>ID</th>
    <th>ชื่อโครงการ</th>
    <th>ผู้จัดทำ</th>
    <th>ครูที่ปรึกษาโครงการ</th>
    <th>บทคัดย่อ</th>
    <th>ไฟล์</th>
    <th>สถานะ</th>
    <th>จัดการ</th>
</tr>

<?php foreach ($projects as $p): ?>
<tr>
    <td><?= $p['id'] ?></td>
    <td><?= htmlspecialchars($p['title']) ?></td>
    <td><?= htmlspecialchars($p['author']) ?></td>
    <td><?= htmlspecialchars($p['advisor']) ?></td>
    <td><?= nl2br(htmlspecialchars($p['abstract'])) ?></td>
    <td>
        <?php if (!empty($p['file']) && file_exists('../projects/'.$p['file'])): ?>
            <a href="../projects/<?= htmlspecialchars($p['file']) ?>" download>
                <button>ดาวน์โหลด</button>
            </a>
        <?php else: ?>
            <span style="color:red;">ไฟล์ไม่พบ</span>
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($p['status'] ?? 'pending') ?></td>
    <td>
        <?php if (($p['status'] ?? 'pending') === 'pending'): ?>
            <a href="?action=approved&id=<?= $p['id'] ?>">
                <button style="background:green;">อนุมัติ</button>
            </a>
            <a href="?action=rejected&id=<?= $p['id'] ?>">
                <button style="background:red;">ปฏิเสธ</button>
            </a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php include '../inc/footer.php'; ?>
