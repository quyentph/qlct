<?php
// filepath: c:\xampp\htdocs\BTL_ LTWEB\forward.php
session_start();
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

$step = 1;
$error = '';
$user_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['xacnhan'])) {
        $ten = trim($_POST['ten']);
        $email = trim($_POST['email']);
        $dienthoai = trim($_POST['dienthoai']);

        // Tìm user theo thông tin xác nhận
        $stmt = $conn->prepare("SELECT users.id FROM nhanvien 
        INNER JOIN users ON nhanvien.id = users.nhanvien_id
            WHERE tennv=? AND email=? AND dienthoai=?");
        $stmt->execute([$ten, $email, $dienthoai]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['reset_user_id'] = $row['id'];
            $step = 2;
        } else {
            $error = "Thông tin xác nhận không đúng!";
        }
    } elseif (isset($_POST['doimk']) && isset($_SESSION['reset_user_id'])) {
        $user_id = $_SESSION['reset_user_id'];
        $new = $_POST['new_password'];
        $renew = $_POST['renew_password'];
        if ($new !== $renew) {
            $error = "Mật khẩu mới không khớp!";
            $step = 2;
        }else {
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $ok = $stmt->execute([md5($new), $user_id]);
            unset($_SESSION['reset_user_id']);
            $step = 3;
        }
    }
} elseif (isset($_SESSION['reset_user_id'])) {
    $step = 2;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #e0f2fe 0%, #90cdf4 100%); }
        .container { max-width: 400px; margin-top: 48px; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px #0001; padding: 32px 24px;}
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Quên mật khẩu</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($step == 1): ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label class="form-label">Tên nhân viên</label>
            <input type="text" name="ten" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="dienthoai" class="form-control" required>
        </div>
        <button type="submit" name="xacnhan" class="btn btn-primary w-100">Xác nhận</button>
    </form>
    <?php elseif ($step == 2): ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-control" required  >
        </div>
        <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu mới</label>
            <input type="password" name="renew_password" class="form-control" required >
        </div>
        <button type="submit" name="doimk" class="btn btn-success w-100">Đổi mật khẩu</button>
    </form>
    <?php elseif ($step == 3): ?>
        <div class="alert alert-success">Đổi mật khẩu thành công! <a href="login.php">Đăng nhập</a></div>
    <?php endif; ?>
    <a href="login.php" class="btn btn-secondary mt-3 w-100">Quay lại đăng nhập</a>
</div>
</body>
</html>