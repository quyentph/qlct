<?php
// filepath: c:\xampp\htdocs\BTL_ LTWEB\them_sua_chamcong.php
session_start();
if (!isset($_SESSION['nhanvien_id'])) {
    header("Location: login.php");
    exit();
}
// Kết nối CSDL bằng PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy danh sách nhân viên
$stmt_nv = $conn->query("SELECT id, tennv FROM nhanvien");
$nhanviens = $stmt_nv->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm/xóa/chỉnh giờ trực tiếp
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thêm mới chấm công
    if (isset($_POST['add'])) {
        $nhanvien_id = $_POST['nhanvien_id'];
        $ngay = $_POST['ngay_cham_cong'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $tt = $_POST['tt'];
        $check_in = $check_in !== '' ? $check_in : null;
        $check_out = $check_out !== '' ? $check_out : null;
        $sql = "INSERT INTO chamcong (nhanvien_id, ngay_cham_cong, check_in, check_out, tt) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $ok = $stmt->execute([$nhanvien_id, $ngay, $check_in, $check_out, $tt]);
        $success = $ok ? "Thêm mới thành công!" : "Có lỗi khi thêm mới!";
    }
    // Xóa chấm công
    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $stmt = $conn->prepare("DELETE FROM chamcong WHERE id=?");
        $ok = $stmt->execute([$id]);
        $success = $ok ? "Đã xóa thành công!" : "Có lỗi khi xóa!";
    }
    // Cập nhật giờ check in/check out trực tiếp
    if (isset($_POST['update_time'])) {
        $id = $_POST['update_time'];
        $check_in = $_POST['check_in_'.$id];
        $check_out = $_POST['check_out_'.$id];
        $check_in = $check_in !== '' ? $check_in : null;
        $check_out = $check_out !== '' ? $check_out : null;
        $sql = "UPDATE chamcong SET check_in=?, check_out=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $ok = $stmt->execute([$check_in, $check_out, $id]);
        $success = $ok ? "Cập nhật giờ thành công!" : "Có lỗi khi cập nhật!";
    }
}

// Lấy danh sách chấm công
$sql = "SELECT chamcong.*, nhanvien.tennv FROM chamcong 
        INNER JOIN nhanvien ON chamcong.nhanvien_id = nhanvien.id
        ORDER BY chamcong.ngay_cham_cong DESC, chamcong.id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm tính tổng thời gian làm việc
function tinh_tong_gio($check_in, $check_out) {
    if (!$check_in || !$check_out) return '';
    $in = strtotime($check_in);
    $out = strtotime($check_out);
    if ($out > $in) {
        $diff = $out - $in;
        $h = floor($diff / 3600);
        $m = floor(($diff % 3600) / 60);
        return sprintf('%02d:%02d', $h, $m);
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chấm công linh hoạt</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Chấm công nhân viên</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Nhân viên</label>
            <select name="nhanvien_id" class="form-select" required>
                <option value="">-- Chọn nhân viên --</option>
                <?php foreach($nhanviens as $nv): ?>
                    <option value="<?php echo $nv['id']; ?>">
                        <?php echo htmlspecialchars($nv['tennv']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Ngày chấm công</label>
            <input type="date" name="ngay_cham_cong" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Check In</label>
            <input type="time" name="check_in" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Check Out</label>
            <input type="time" name="check_out" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Trạng thái</label>
            <select name="tt" class="form-select" required>
                <option value="Đi làm">Đi làm</option>
                <option value="Nghỉ phép">Nghỉ phép</option>
                <option value="Nghỉ không phép">Nghỉ không phép</option>
                <option value="Làm online">Làm online</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" name="add" class="btn btn-primary w-100">Thêm</button>
        </div>
    </form>
    <div class="table-responsive">
    <form method="post">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Tên nhân viên</th>
                <th>Ngày chấm công</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Trạng thái</th>
                <th>Tổng thời gian</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($rows && count($rows) > 0): 
            foreach($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['tennv']); ?></td>
                <td><?php echo htmlspecialchars($row['ngay_cham_cong']); ?></td>
                <td>
                    <input type="time" name="check_in_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['check_in']); ?>" class="form-control" style="min-width:100px;">
                </td>
                <td>
                    <input type="time" name="check_out_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['check_out']); ?>" class="form-control" style="min-width:100px;">
                </td>
                <td><?php echo htmlspecialchars($row['tt']); ?></td>
                <td>
                    <?php echo tinh_tong_gio($row['check_in'], $row['check_out']); ?>
                </td>
                <td>
                    <button type="submit" name="delete" value="<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa dòng này?')">Xóa</button>
                    <button type="submit" name="update_time" value="<?php echo $row['id']; ?>" class="btn btn-sm btn-success ms-1">Lưu giờ</button>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </form>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">Quay lại trang chủ</a>
</div>
</body>
</html>