<?php
// Kết nối CSDL
$conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lấy ID nhân viên từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin nhân viên
$stmt = $conn->prepare("SELECT * FROM nhanvien WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$nv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nv) {
    echo "Không tìm thấy nhân viên!";
    exit();
}

// Lấy danh sách phòng ban và vị trí
$phongbans = $conn->query("SELECT id, tenpb FROM phongban")->fetchAll(PDO::FETCH_ASSOC);
$vitris = $conn->query("SELECT id, tenvt FROM vitri")->fetchAll(PDO::FETCH_ASSOC);

// Xử lý cập nhật khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tennv = $_POST['tennv'];
    $gioitinh = $_POST['gioitinh'];
    $ngaysinh = $_POST['ngaysinh'];
    $dienthoai = $_POST['dienthoai'];
    $email = $_POST['email'];
    $diachi = $_POST['diachi'];
    $phongban_id = $_POST['phongban_id'];
    $vitri_id = $_POST['vitri_id'];
    $ngay_vao_lam = $_POST['ngay_vao_lam'];
    $dir = "image/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $avt = null;
    // Kiểm tra file upload
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
        $file_infor = $_FILES['profilePic']['name'];
        $file_tmp = $_FILES['profilePic']['tmp_name'];
        $file_type = pathinfo($file_infor, PATHINFO_EXTENSION);
        $file_size = $_FILES['profilePic']['size'];

        // Kiểm tra định dạng file
        $allowed_types = ['jpeg', 'png', 'gif','jpg'];
        if (in_array($file_type, $allowed_types) && $file_size <= 2 * 1024 * 1024) { 
            $file_name = $dir . pathinfo($file_infor, PATHINFO_FILENAME) . time() . '.' . $file_type;
            move_uploaded_file($file_tmp, $file_name);
            $avt = $file_name;
        } else {
            echo "<script>alert('Định dạng file không hợp lệ hoặc kích thước quá lớn!');</script>";
        }
    }
    $sql = "UPDATE nhanvien SET tennv=:tennv, gioitinh=:gioitinh, ngaysinh=:ngaysinh, dienthoai=:dienthoai, email=:email, diachi=:diachi, phongban_id=:phongban_id, vitri_id=:vitri_id, ngay_vao_lam=:ngay_vao_lam, anh=    :anh WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':tennv' => $tennv,
        ':gioitinh' => $gioitinh,
        ':ngaysinh' => $ngaysinh,
        ':dienthoai' => $dienthoai,
        ':email' => $email,
        ':diachi' => $diachi,
        ':phongban_id' => $phongban_id,
        ':vitri_id' => $vitri_id,
        ':ngay_vao_lam' => $ngay_vao_lam,
        ':anh' => $avt,
        ':id' => $id
    ]);
    header("Location: danhsachnv.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa nhân viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style="background: #f8f9fa;">
    <div class="container mt-5">
        <h2 class="mb-4 text-center text-primary">Sửa thông tin nhân viên</h2>
        <form method="post" class="bg-white p-4 rounded-4 shadow-sm" style="max-width:600px;margin:auto;" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Tên nhân viên</label>
                <input type="text" name="tennv" class="form-control" value="<?php echo htmlspecialchars($nv['tennv']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Giới tính</label>
                <select name="gioitinh" class="form-select" required>
                    <option value="Nam" <?php if($nv['gioitinh']=='Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nữ" <?php if($nv['gioitinh']=='Nữ') echo 'selected'; ?>>Nữ</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Ngày sinh</label>
                <input type="date" name="ngaysinh" class="form-control" value="<?php echo htmlspecialchars($nv['ngaysinh']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Điện thoại</label>
                <input type="text" name="dienthoai" class="form-control" value="<?php echo htmlspecialchars($nv['dienthoai']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($nv['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="diachi" class="form-control" value="<?php echo htmlspecialchars($nv['diachi']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phòng ban</label>
                <select name="phongban_id" class="form-select" required>
                    <?php foreach($phongbans as $pb): ?>
                        <option value="<?php echo $pb['id']; ?>" <?php if($nv['phongban_id']==$pb['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($pb['tenpb']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Vị trí</label>
                <select name="vitri_id" class="form-select" required>
                    <?php foreach($vitris as $vt): ?>
                        <option value="<?php echo $vt['id']; ?>" <?php if($nv['vitri_id']==$vt['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($vt['tenvt']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Ngày vào làm</label>
                <input type="date" name="ngay_vao_lam" class="form-control" value="<?php echo htmlspecialchars($nv['ngay_vao_lam']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="profilePic" class="form-label">Chọn ảnh đại diện</label>
                <input type="file" class="form-control" id="profilePic" name="profilePic" >
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <a href="danhsachnv.php" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
        </form>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>