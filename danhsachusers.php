<?php
// filepath: c:\xampp\htdocs\BTL_ LTWEB\danhsachusers.php
session_start();
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

$success = '';
$error = '';

// Xử lý xóa user
if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
    $id = $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $ok = $stmt->execute([$id]);
    $success = $ok ? "Đã xóa tài khoản thành công!" : "Có lỗi khi xóa!";
}

// Xử lý cập nhật quyền user
if (isset($_POST['update_role']) && is_numeric($_POST['user_id'])) {
    $id = $_POST['user_id'];
    $role = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
    $ok = $stmt->execute([$role, $id]);
    $success = $ok ? "Cập nhật quyền thành công!" : "Có lỗi khi cập nhật!";
}

// Lấy danh sách user
$sql = "SELECT u.id, u.username, u.role, nv.tennv 
        FROM users u 
        LEFT JOIN nhanvien nv ON u.nhanvien_id = nv.id
        ORDER BY u.id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách tài khoản</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #e0f2fe 0%, #90cdf4 100%); }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Danh sách tài khoản người dùng</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Nhân viên</th>
                <th>Quyền</th>
                <th>Sửa quyền</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['tennv']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <form method="post" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <select name="role" class="form-select form-select-sm" style="width:120px;">
                            <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>Nhân viên</option>
                            <option value="quanli" <?php if($user['role']=='quanli') echo 'selected'; ?>>Quản lý</option>
                        </select>
                        <button type="submit" name="update_role" value="1" class="btn btn-sm btn-success">Lưu</button>
                    </form>
                </td>
                <td>
                    <form method="post" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản này?');">
                        <input type="hidden" name="delete" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (count($users) == 0): ?>
            <tr><td colspan="6" class="text-center">Không có tài khoản nào</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">Quay lại trang chủ</a>
</div>
</body>
</html>