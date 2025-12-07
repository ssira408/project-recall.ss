<?php
require '../db.php';
include '../inc/auth.php';
if(!isUser()){ die("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"); }
include '../inc/header.php';

$user_email = $_SESSION['user']['email'];

// ดึงโครงการของผู้ใช้ที่ **อนุมัติแล้วเท่านั้น**
$stmt = $pdo->prepare("SELECT * FROM projects WHERE author = :author AND status = 'approved' ORDER BY id DESC");
$stmt->execute(['author' => $user_email]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Dashboard User</h2>
<p>สวัสดี, <?= htmlspecialchars($user_email) ?></p>

<h3>โครงการที่ได้รับอนุมัติ</h3>

<?php if(empty($projects)): ?>
    <p>ยังไม่มีโครงการที่ได้รับการอนุมัติ</p>
<?php endif; ?>

<?php foreach($projects as $p): ?>
    <div style="border:1px solid #ffc400; padding:10px; margin:10px 0; border-radius:8px;">
        <h4><?= htmlspecialchars($p['title']) ?></h4>
        <p>ครูที่ปรึกษาโครงการหลัก: <?= htmlspecialchars($p['advisor_main']) ?></p>
        <?php if(!empty($p['advisor_co'])): ?>
            <p>ครูที่ปรึกษาร่วม: <?= htmlspecialchars($p['advisor_co']) ?></p>
        <?php endif; ?>
        <p>บทคัดย่อ: <?= nl2br(htmlspecialchars($p['abstract'])) ?></p>
        <?php if(file_exists('../projects/' . $p['file'])): ?>
            <a href="../projects/<?= htmlspecialchars($p['file']) ?>" download>
                <button>ดาวน์โหลด</button>
            </a>
        <?php else: ?>
            <span style="color:red;">ไฟล์ไม่พบ</span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php include '../inc/footer.php'; ?>
