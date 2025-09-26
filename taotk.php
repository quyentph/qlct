<?php
// filepath: c:\xampp\htdocs\BTL_ LTWEB\taotk.php
session_start();
if (!isset($_SESSION['nhanvien_id'])) {
    header("Location: login.php");
    exit();
}
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy danh sách nhân viên chưa có tài khoản
$stmt_nv = $conn->query("SELECT id, tennv FROM nhanvien WHERE id NOT IN (SELECT nhanvien_id FROM users)");
$nhanviens = $stmt_nv->fetchAll(PDO::FETCH_ASSOC);

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nhanvien_id = $_POST['nhanvien_id'];
    $role = $_POST['role'];

    // Kiểm tra username đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $error = "Tên đăng nhập đã tồn tại!";
    } else {
        // Thêm tài khoản mới
        $sql = "INSERT INTO users (username, password, nhanvien_id, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $ok = $stmt->execute([$username, md5($password), $nhanvien_id, $role]);
        $success = $ok ? "Tạo tài khoản thành công!" : "Có lỗi khi tạo tài khoản!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo tài khoản người dùng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #e0f2fe 0%, #90cdf4 100%); }
        .container { max-width: 500px; margin-top: 48px; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px #0001; padding: 32px 24px;}
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Tạo tài khoản người dùng</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required >
        </div>
        <div class="mb-3">
            <label class="form-label">Nhân viên</label>
            <select name="nhanvien_id" class="form-select" required>
                <option value="">-- Chọn nhân viên --</option>
                <?php foreach($nhanviens as $nv): ?>
                    <option value="<?php echo $nv['id']; ?>"><?php echo htmlspecialchars($nv['tennv']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Quyền</label>
            <select name="role" class="form-select" required>
                <option value="nhanvien">Nhân viên</option>
                <option value="quanli">Quản lý</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Tạo tài khoản</button>
    </form>
    <a href="index.php" class="btn btn-secondary mt-3 w-100">Quay lại trang chủ</a>
</div>
</body>
</html>