<?php
require 'db.php';
session_start();
$error = "";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // 6 ตัวท้ายบัตร
    $department = trim($_POST['department']); // แผนกที่เลือก

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        $error = "ไม่พบ Email นี้ในระบบ";
    } else {
        if(password_verify($password,$user['password'])){
            // เก็บ session พร้อมแผนก
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'department' => $department
            ];

            // เปลี่ยนหน้า dashboard ตาม role
            if($user['role'] === 'admin') header("Location: dashboard/admin.php");
            elseif($user['role'] === 'employee') header("Location: dashboard/employee.php");
            else header("Location: dashboard/user.php");
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง (6 ตัวท้ายบัตรประชาชน)";
        }
    }
}
?>

<?php include 'inc/header.php'; ?>
<h2>เข้าสู่ระบบ</h2>

<?php if($error) echo "<p style='color:red'>$error</p>"; ?>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>รหัสผ่าน (6 ตัวท้ายบัตร):</label><br>
    <input type="password" name="password" maxlength="6" required><br><br>

    <label>แผนกวิชา:</label><br>
    <select name="department" required>
        <option value="">-- เลือกแผนกวิชา --</option>
        <?php foreach($departList as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">เข้าสู่ระบบ</button>
</form>

<?php include 'inc/footer.php'; ?>
