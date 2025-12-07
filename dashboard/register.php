<?php
require '../db.php';
include '../inc/header.php';

// ตรวจสอบสิทธิ์ admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("<p style='color:red;'>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</p>");
}

$departments = [
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

$roles = ['admin', 'employee', 'user'];

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = trim($_POST['fullname']);
    $student_id = trim($_POST['student_id']);
    $birthdate = trim($_POST['birthdate']);
    $department = trim($_POST['department']);
    $role = trim($_POST['role']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบฟิลด์ว่าง
    if(!$fullname || !$student_id || !$birthdate || !$department || !$role || !$email || !$password){
        $error = "กรุณากรอกทุกช่องให้ครบ";
    } else {
        // ตรวจสอบว่ามี email ซ้ำหรือไม่
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email=:email");
        $stmt->execute(['email'=>$email]);
        if($stmt->fetchColumn() > 0){
            $error = "Email นี้มีอยู่แล้วในระบบ";
        } else {
            // เข้ารหัสรหัสผ่าน
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // เพิ่มผู้ใช้ใหม่
            $stmt = $pdo->prepare("
                INSERT INTO users (fullname, student_id, birthdate, department, role, email, password)
                VALUES (:fullname, :student_id, :birthdate, :department, :role, :email, :password)
            ");
            $stmt->execute([
                'fullname'=>$fullname,
                'student_id'=>$student_id,
                'birthdate'=>$birthdate,
                'department'=>$department,
                'role'=>$role,
                'email'=>$email,
                'password'=>$password_hash
            ]);

            $success = "เพิ่มผู้ใช้ใหม่สำเร็จ!";
        }
    }
}
?>

<h2>เพิ่มผู้ใช้ใหม่ (Admin)</h2>

<?php if($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">
    <label>ชื่อ-นามสกุล:</label><br>
    <input type="text" name="fullname" required><br><br>

    <label>รหัสประจำตัวนักศึกษา:</label><br>
    <input type="text" name="student_id" required><br><br>

    <label>วันเดือนปีเกิด:</label><br>
    <input type="date" name="birthdate" required><br><br>

    <label>แผนกวิชา:</label><br>
    <select name="department" required>
        <option value="">-- เลือกแผนกวิชา --</option>
        <?php foreach($departments as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>ตำแหน่ง / Role:</label><br>
    <select name="role" required>
        <option value="">-- เลือกตำแหน่ง --</option>
        <?php foreach($roles as $r): ?>
            <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>รหัสผ่าน:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">เพิ่มผู้ใช้</button>
</form>

<?php include '../inc/footer.php'; ?>
