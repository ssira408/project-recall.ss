<?php
require '../db.php';
session_start(); // เริ่ม session

// ตรวจสอบว่า login หรือยัง
if(!isset($_SESSION['user'])){
    die("คุณต้องเข้าสู่ระบบก่อนเข้าหน้านี้");
}

include '../inc/header.php';

$role = $_SESSION['user']['role'];
$user_email = $_SESSION['user']['email'];
$user_fullname = $_SESSION['user']['fullname'] ?? $user_email;
$user_department = $_SESSION['user']['department'] ?? '';

// รายการแผนกวิชา
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

// เพิ่มโครงการใหม่
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title']);
    $advisor_main = trim($_POST['advisor_main']);
    $advisor_co = trim($_POST['advisor_co']);
    $abstract = trim($_POST['abstract']);
    $department = trim($_POST['department']);
    $author = $user_fullname;

    if(isset($_FILES['file']) && $_FILES['file']['error'] === 0){
        $filename = time() . "_" . basename($_FILES['file']['name']);
        $filepath = '../projects/' . $filename;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $filepath)){
            // สำหรับ User ให้ status = pending, Employee/Admin สามารถตั้ง status = approved ได้ถ้าต้องการ
            $status = ($role === 'user') ? 'pending' : 'approved';

            $stmt = $pdo->prepare("
                INSERT INTO projects (title, author, advisor_main, advisor_co, abstract, file, department, status)
                VALUES (:title, :author, :advisor_main, :advisor_co, :abstract, :file, :dept, :status)
            ");
            $stmt->execute([
                'title'=>$title,
                'author'=>$author,
                'advisor_main'=>$advisor_main,
                'advisor_co'=>$advisor_co,
                'abstract'=>$abstract,
                'file'=>$filename,
                'dept'=>$department,
                'status'=>$status
            ]);

            $success = "เพิ่มโครงการสำเร็จ!";
        } else {
            $error = "อัปโหลดไฟล์ไม่สำเร็จ";
        }
    } else {
        $error = "กรุณาเลือกไฟล์โครงการ";
    }
}
?>

<h2>เพิ่มโครงการ</h2>

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

    <label>ครูที่ปรึกษาร่วม (ถ้ามี):</label><br>
    <input type="text" name="advisor_co"><br><br>

    <label>บทคัดย่อ:</label><br>
    <textarea name="abstract" rows="4" required></textarea><br><br>

    <label>แผนกวิชา:</label><br>
    <select name="department" required>
        <option value="">-- เลือกแผนกวิชา --</option>
        <?php foreach($departList as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>" <?= ($d === $user_department) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>ไฟล์โครงการ (PDF / DOCX):</label><br>
    <input type="file" name="file" accept=".pdf,.doc,.docx" required><br><br>

    <button type="submit">เพิ่มโครงการ</button>
</form>

<?php include '../inc/footer.php'; ?>
