<?php
require '../db.php';
include '../inc/auth.php';

if(!isEmployee()){
    die("คุณไม่มีสิทธิ์เข้าถึงหน้านี้");
}

include '../inc/header.php';

$user_email = $_SESSION['user']['email'];
$user_fullname = $_SESSION['user']['fullname'] ?? $user_email;
$user_department = $_SESSION['user']['department'] ?? '';

$departList = [
    "เทคโนโลยีสารสนเทศ",
    "การบัญชี",
    "คหกรรมศาสตร์",
    "ช่างยนต์",
    "ช่างไฟฟ้ากำลัง",
    "ช่างโยธา",
    "ช่างอิเล็กทรอนิกส์",
    "ช่างเชื่อมโลหะ",
    "ช่างกลโรงงาน",
    "สามัญสัมพันธ์",
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title']);
    $advisor_main = trim($_POST['advisor_main']);
    $advisor_co = trim($_POST['advisor_co']); // ครูที่ปรึกษาร่วม
    $abstract = trim($_POST['abstract']);
    $department = trim($_POST['department']);
    $author = $user_fullname;

    if(isset($_FILES['file']) && $_FILES['file']['error'] === 0){
        $filename = time() . "_" . basename($_FILES['file']['name']);
        $filepath = '../projects/' . $filename;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $filepath)){
            $stmt = $pdo->prepare("
                INSERT INTO projects (title, author, advisor_main, advisor_co, abstract, file, department)
                VALUES (:title, :author, :advisor_main, :advisor_co, :abstract, :file, :dept)
            ");

            $stmt->execute([
                'title' => $title,
                'author' => $author,
                'advisor_main' => $advisor_main,
                'advisor_co' => $advisor_co,
                'abstract' => $abstract,
                'file' => $filename,
                'dept' => $department
            ]);

            $success = "เพิ่มโครงการสำเร็จ!";
        } else {
            $error = "อัปโหลดไฟล์ไม่สำเร็จ";
        }
    } else {
        $error = "กรุณาเลือกไฟล์โครงการ";
    }
}

$stmt = $pdo->prepare("
    SELECT * FROM projects 
    WHERE department = :dept
    ORDER BY id DESC
");
$stmt->execute(['dept' => $user_department]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Dashboard Employee</h2>
<p>สวัสดี, <?= htmlspecialchars($user_fullname) ?> (แผนก: <?= htmlspecialchars($user_department) ?>)</p>

<h3>เพิ่มโครงการใหม่</h3>

<?php if(isset($error)): ?>
    <p style="color:red; font-weight:bold;"><?= $error ?></p>
<?php endif; ?>

<?php if(isset($success)): ?>
    <p style="color:green; font-weight:bold;"><?= $success ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>ชื่อโครงการ:</label><br>
    <input type="text" name="title" required><br><br>

    <label>ครูที่ปรึกษาโครงการหลัก:</label><br>
    <input type="text" name="advisor_main" required><br><br>

    <label>ครูที่ปรึกษาร่วม:</label><br>
    <input type="text" name="advisor_co"><br><br>

    <label>บทคัดย่อ:</label><br>
    <textarea name="abstract" rows="4" required></textarea><br><br>

    <label>แผนกวิชา:</label><br>
    <select name="department" required>
        <option value="">-- เลือกแผนกวิชา --</option>
        <?php foreach($departList as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>ไฟล์โครงการ (PDF / DOCX):</label><br>
    <input type="file" name="file" accept=".pdf,.doc,.docx" required><br><br>

    <button type="submit">เพิ่มโครงการ</button>
</form>

<hr>

<h3>โครงการของแผนก <?= htmlspecialchars($user_department) ?></h3>

<?php if(empty($projects)): ?>
    <p>ยังไม่มีโครงการในแผนกนี้</p>
<?php endif; ?>

<?php foreach($projects as $p): ?>
    <div style="border:1px solid #ffc400; padding:10px; margin:10px 0; border-radius:8px;">
        <h4><?= htmlspecialchars($p['title']) ?></h4>
        <p><strong>ผู้จัดทำ:</strong> <?= htmlspecialchars($p['author']) ?></p>
        <p><strong>ครูที่ปรึกษาโครงการหลัก:</strong> <?= htmlspecialchars($p['advisor_main']) ?></p>
        <?php if(!empty($p['advisor_co'])): ?>
            <p><strong>ครูที่ปรึกษาร่วม:</strong> <?= htmlspecialchars($p['advisor_co']) ?></p>
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars($p['abstract'])) ?></p>

        <?php if(file_exists('../projects/' . $p['file'])): ?>
            <a href="../projects/<?= htmlspecialchars($p['file']) ?>" download>
                <button>ดาวน์โหลดไฟล์</button>
            </a>
        <?php else: ?>
            <span style="color:red;">ไฟล์ไม่พบ</span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php include '../inc/footer.php'; ?>
