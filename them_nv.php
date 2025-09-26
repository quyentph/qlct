<?php
// Kết nối CSDL
$conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$showSuccessModal = false;
// Lấy danh sách phòng ban và vị trí
$phongbans = $conn->query("SELECT id, tenpb FROM phongban")->fetchAll(PDO::FETCH_ASSOC);
$vitris = $conn->query("SELECT id, tenvt FROM vitri")->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['from_ungvien']) && $_POST['from_ungvien'] == 'true') {
    $tennv = $_POST['tennv'];
    $gioitinh = $_POST['gioitinh'];
    $ngaysinh = $_POST['ngaysinh'];   
    $dienthoai = $_POST['dienthoai'];
    $email = $_POST['email'];
    $diachi = $_POST['diachi'];
    $tenpb = $_POST['tenpb'];
    $vitri = $_POST['vt_ungtuyen'];
    foreach ($phongbans as $pb) {
        if ($pb['tenpb'] == $tenpb) {
            $phongban_id = $pb['id'];
            break;
        }
    }
    foreach ($vitris as $vt) {
        if ($vt['tenvt'] == $vitri) {
            $vitri_id = $vt['id'];
            break;
        }
    }
}

    
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_nv'])) {
    $tennv = $_POST['tennv'];
    $gioitinh = $_POST['gioitinh'];
    $ngaysinh = $_POST['ngaysinh'];   
    $dienthoai = $_POST['dienthoai'];
    $email = $_POST['email'];
    $diachi = $_POST['diachi'];
    $phongban_id = $_POST['phongban_id'];
    $vitri_id = $_POST['vitri_id']; 
    $ngay_vao_lam = !empty($_POST['ngay_vao_lam']) ? $_POST['ngay_vao_lam'] : date('Y-m-d');
    
    // Xử lý ảnh đại diện
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
    $sql = "INSERT INTO nhanvien (tennv, gioitinh, ngaysinh, dienthoai, email, diachi, phongban_id, vitri_id, ngay_vao_lam, anh)
            VALUES (:tennv, :gioitinh, :ngaysinh, :dienthoai, :email, :diachi, :phongban_id, :vitri_id, :ngay_vao_lam, :avt)";
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
        ':avt' => $avt
    ]);
    $showSuccessModal = true;
}

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhân viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhân viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style="background: linear-gradient(135deg, #e0f7fa, #ffffff); font-family: 'Segoe UI', sans-serif;">
    <div class="container mt-5">
        <div class="card shadow-lg rounded-4 p-4 mx-auto" style="max-width: 650px;">
            <h2 class="mb-4 text-center text-primary fw-bold">Thêm nhân viên mới</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Tên nhân viên</label>
                    <input type="text" name="tennv" class="form-control" required value="<?php echo isset($tennv) ? htmlspecialchars($tennv) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Giới tính</label>
                    <select name="gioitinh" class="form-select" required>
                        <option value="Nam" <?php echo (isset($gioitinh) && $gioitinh === 'Nam') ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo (isset($gioitinh) && $gioitinh === 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="ngaysinh" class="form-control" required value="<?php echo isset($ngaysinh) ? htmlspecialchars($ngaysinh) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Điện thoại</label>
                    <input type="text" name="dienthoai" class="form-control" required value="<?php echo isset($dienthoai) ? htmlspecialchars($dienthoai) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="diachi" class="form-control" required value="<?php echo isset($diachi) ? htmlspecialchars($diachi) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phòng ban</label>
                    <select name="phongban_id" class="form-select" required>
                        <option value="">-- Chọn phòng ban --</option>
                        <?php foreach($phongbans as $pb): ?>
                            <option value="<?php echo $pb['id']; ?>" <?php echo (isset($phongban_id) && $phongban_id == $pb['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pb['tenpb']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vị trí</label>
                    <select name="vitri_id" class="form-select" required>
                        <option value="">-- Chọn vị trí --</option>
                        <?php foreach($vitris as $vt): ?>
                            <option value="<?php echo $vt['id']; ?>" <?php echo (isset($vitri_id) && $vitri_id == $vt['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($vt['tenvt']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày vào làm</label>
                    <input type="date" name="ngay_vao_lam" class="form-control" value="<?php echo $today; ?>">
                </div>
                <div class="mb-3">
                    <label for="profilePic" class="form-label">Chọn ảnh đại diện</label>
                    <input type="file" class="form-control" id="profilePic" name="profilePic" accept="image/jpeg,image/png,image/gif">
                </div>
                <div class="text-center mt-4">
                    <button type="submit" name="submit_nv" class="btn btn-primary px-4">Thêm nhân viên</button>
                    <a href="index.php" class="btn btn-outline-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-success">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="successModalLabel">Thành công</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body text-center">
            Nhân viên đã được thêm thành công!
          </div>
          <div class="modal-footer">
            <a href="danhsachnv.php" class="btn btn-success">Đi tới danh sách</a>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($showSuccessModal): ?>
            var myModal = new bootstrap.Modal(document.getElementById('successModal'));
            window.addEventListener('load', () => {
                myModal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>