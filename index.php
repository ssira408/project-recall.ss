<?php
require 'db.php';
include 'inc/header.php';

// ตรวจสอบ login
$user_logged_in = isset($_SESSION['user']);

// ดึงโครงการจากฐานข้อมูล
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>โครงการตัวอย่าง</h2>

<?php foreach($projects as $p): ?>
    <div class="project-card">
        <h3><?= htmlspecialchars($p['title']) ?></h3>
        <p>ผู้จัดทำ: <?= htmlspecialchars($p['author']) ?></p>
        <p>ครูที่ปรึกษา: <?= htmlspecialchars($p['advisor']) ?></p>
        <p>บทคัดย่อ: <?= nl2br(htmlspecialchars($p['abstract'])) ?></p>

        <?php if($user_logged_in && file_exists('projects/'.$p['file'])): ?>
            <a href="projects/<?= htmlspecialchars($p['file']) ?>" download>
                <button>ดาวน์โหลด</button>
            </a>
        <?php else: ?>
            <button disabled>ล็อก ต้อง login ก่อน</button>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php include 'inc/footer.php'; ?>
