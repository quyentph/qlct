<?php
session_start();
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id ) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $renew = $_POST['renew_password'];

    // Kiểm tra mật khẩu cũ
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || md5($old) != $row['password']) {
        $error = "Mật khẩu cũ không đúng!";
    } elseif ($new != $renew) {
        $error = "Mật khẩu mới không khớp!";
    } else {
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $ok = $stmt->execute([md5($new), $user_id]);
        $success = $ok ? "Đổi mật khẩu thành công!" : "Có lỗi khi đổi mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #e0f2fe 0%, #90cdf4 100%); }
        .container { max-width: 400px; margin-top: 48px; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px #0001; padding: 32px 24px;}
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Đổi mật khẩu</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label class="form-label">Mật khẩu cũ</label>
            <input type="password" name="old_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-control" required >
        </div>
        <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu mới</label>
            <input type="password" name="renew_password" class="form-control" required >
        </div>
        <button type="submit" class="btn btn-primary w-100">Đổi mật khẩu</button>
    </form>
    <?php
    
        if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'quanli'){
            
            echo "<a href='index.php' class='btn btn-secondary mt-3 w-100'>Quay lại trang chủ</a>";
        }
        else{
            echo "<a href='index_nv.php' class='btn btn-secondary mt-3 w-100'>Quay lại trang chủ</a>";
        }
    ?>
</div>
</body>
</html>