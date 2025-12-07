<?php
// เช็คก่อนว่า session ถูกเริ่มแล้วหรือยัง
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Project Recall</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">
<style>
body { font-family: 'Sarabun', sans-serif; background:#fff; margin:0; }
header { background:#b40000; color:#fff; padding:15px; text-align:center; }
nav a { margin:0 10px; color:#fff; text-decoration:none; font-weight:bold; }
nav a:hover { text-decoration:underline; }
.container { padding:20px; }
button { background:#b40000; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
button:hover { background:#e60000; }
</style>
</head>
<body>
<header>
    <h1>Project Recall</h1>
    <nav>
        <a href="/project_recall/index.php">หน้าแรก</a>

        <?php if(isset($_SESSION['user'])): 
            $role = $_SESSION['user']['role'];
        ?>

            <!-- ลิงก์เพิ่มโครงการ สำหรับทุก Role -->
            <a href="/project_recall/dashboard/user_add_project.php">เพิ่มโครงการ</a>

            <?php if(in_array($role, ['admin', 'employee'])): ?>
                <a href="/project_recall/dashboard/approve_projects.php">อนุมัติโครงการ</a>
            <?php endif; ?>

            <a href="/project_recall/logout.php">ออกจากระบบ</a>
        <?php else: ?>
            <a href="/project_recall/login.php">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
<?php
// แสดงโครงการตาม Role (Admin/Employee/User) ตามเดิม
if(isset($_SESSION['user'])){
    $role = $_SESSION['user']['role'];
    $user_email = $_SESSION['user']['email'];
    $user_department = $_SESSION['user']['department'] ?? '';

    if($role === 'admin'){
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
    } elseif($role === 'employee'){
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE department = :dept ORDER BY id DESC");
        $stmt->execute(['dept'=>$user_department]);
    } else { 
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE author = :author AND status = 'approved' ORDER BY id DESC");
        $stmt->execute(['author'=>$user_email]);
    }

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
