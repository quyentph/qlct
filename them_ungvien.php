<?php
// Kết nối CSDL bằng PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy danh sách vị trí từ bảng vitri
$vitri_list = [];
try {
    $stmt_vt = $conn->query("SELECT id, tenvt FROM vitri");
    $vitri_list = $stmt_vt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $vitri_list = [];
}
$ten = $gioitinh = $ngaysinh = $email = $dienthoai = $diachi = $vt_ungtuyen = $kynang = $ghichu = '';
// Danh sách phòng ban cố định
$phongban_list = [
    "Phòng Nhân sự",
    "Phòng Kế toán",
    "Phòng Kỹ thuật",
    "Phòng Marketing",
    "Phòng Hành chính"
];
// Xử lý thêm ứng viên khi submit form
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ungvien'])) {
    $ten = $_POST['ten'] ?? '';
    $gioitinh = $_POST['gioitinh'] ?? '';
    $ngaysinh = $_POST['ngaysinh'] ?? '';
    $email = $_POST['email'] ?? '';
    $dienthoai = $_POST['dienthoai'] ?? '';
    $diachi = $_POST['diachi'] ?? '';
    $vt_ungtuyen = $_POST['vt_ungtuyen'] ?? '';
    $phongban = $_POST['phongban'] ?? '';
    $kynang = $_POST['kynang'] ?? '';
    $tt = 'chờ xử lý'; // Mặc định trạng thái
    $ngay_ung_tuyen = $_POST['ngay_ung_tuyen'] ?? date('Y-m-d');
    $ghichu = $_POST['ghichu'] ?? '';
    $link_cv = '';

    // Xử lý upload file CV nếu có
    if (isset($_FILES['link_cv']) && $_FILES['link_cv']['error'] == 0) {
        $folder = "uploads/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $cv = basename($_FILES['link_cv']['name']);
        $tmp_name = $_FILES['link_cv']['tmp_name'];
        $path = $folder . time() . "_" . $cv;
        if (move_uploaded_file($tmp_name, $path)) {
            $link_cv = $path;
        } else {
            $link_cv = '';
        }
    }

    $sql = "INSERT INTO ungvien (ten, gioitinh, ngaysinh, email, dienthoai, diachi, tenpb, vt_ungtuyen, kynang, link_cv, tt, ngay_ung_tuyen, ghichu)
        VALUES (:ten, :gioitinh, :ngaysinh, :email, :dienthoai, :diachi, :phongban, :vt_ungtuyen, :kynang, :link_cv, :tt, :ngay_ung_tuyen, :ghichu)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':ten' => $ten,
        ':gioitinh' => $gioitinh,
        ':ngaysinh' => $ngaysinh,
        ':email' => $email,
        ':dienthoai' => $dienthoai,
        ':diachi' => $diachi,
        ':phongban' => $phongban,
        ':vt_ungtuyen' => $vt_ungtuyen,
        ':kynang' => $kynang,
        ':link_cv' => $link_cv,
        ':tt' => $tt,
        ':ngay_ung_tuyen' => $ngay_ung_tuyen,
        ':ghichu' => $ghichu
    ]);
    $success = "Thêm ứng viên thành công!";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm ứng viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style = "background: url('image/tuyendung.png') no-repeat center center fixed;
                background-size: cover;">
<div class="container py-4">
    <h2 class="mb-4">Thêm ứng viên mới</h2>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3" enctype="multipart/form-data">
        <div class="col-md-6">
            <label class="form-label">Tên ứng viên</label>
            <input type="text" name="ten" class="form-control" required >
        </div>
        <div class="col-md-3">
            <label class="form-label">Giới tính</label>
            <select name="gioitinh" class="form-select" required>
                <option value="">--Chọn--</option>
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
                <option value="Khác">Khác</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngaysinh" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Điện thoại</label>
            <input type="text" name="dienthoai" class="form-control">
        </div>
        <div class="col-md-12">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="diachi" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Vị trí ứng tuyển</label>
            <select name="vt_ungtuyen" class="form-select" required>
                <option value="">--Chọn vị trí--</option>
                <?php foreach($vitri_list as $vt): ?>
                    <option value="<?php echo htmlspecialchars($vt['tenvt']); ?>"><?php echo htmlspecialchars($vt['tenvt']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phòng ban</label>
            <select name="phongban" class="form-select" required>
                <option value="">-- Chọn phòng ban --</option>
                <?php foreach ($phongban_list as $pb): ?>
                    <option value="<?php echo htmlspecialchars($pb); ?>"><?php echo htmlspecialchars($pb); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Kỹ năng</label>
            <input type="text" name="kynang" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">CV (file)</label>
            <input type="file" name="link_cv" id="link_cv" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Ngày ứng tuyển</label>
            <input type="date" name="ngay_ung_tuyen" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="col-md-12">
            <label class="form-label">Ghi chú</label>
            <textarea name="ghichu" class="form-control"></textarea>
        </div>
        <div class="col-12 text-center mt-3">
            <button type="submit" name="submit_ungvien" class="btn btn-primary">Thêm ứng viên</button>
            <a href="index.php" class="btn btn-secondary">Quay về trang chủ</a>
        </div>
    </form>
</div>
</body>
</html>