<?php  
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    // Kết nối CSDL
    try{
        $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        die("Kết nối thất bại: " . $e->getMessage());
    }
    // Lấy dữ liệu nhân viên theo ID
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT nv.id, nv.tennv, nv.gioitinh, nv.ngaysinh, nv.dienthoai, nv.email, nv.diachi, phongban.tenpb, vitri.tenvt, nv.ngay_vao_lam, nv.anh 
        FROM nhanvien nv INNER JOIN phongban ON nv.phongban_id = phongban.id 
        INNER JOIN vitri ON nv.vitri_id = vitri.id where nv.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $nhanvien = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: danhsachnv.php");
        exit();
    }

    function getEmployeeImage($imagePath,$gioitinh) {
        if (empty($imagePath)) {
            return ($gioitinh == 'Nữ') ? "image/avt_nu.jpg" : "image/avt_nam.png";
        }
        return $imagePath;
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Nhân Viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: rgb(200, 228, 255);
        }
        .profile-card {
            max-width: 600px;
            margin: auto;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Thông tin Nhân Viên</h2>
        <?php if ($nhanvien): ?>
            <div class="card profile-card shadow">
                <div class="card-body text-center">
                    <img src="<?php echo getEmployeeImage($nhanvien['anh'], $nhanvien['gioitinh']); ?>" 
                         class="rounded-circle profile-img img-thumbnail mb-3" 
                         alt="Ảnh nhân viên">
                    <h4 class="card-title"><?php echo htmlspecialchars($nhanvien['tennv']); ?></h4>
                    <div class="row text-start">
                        <div class="col-md-6">
                            <p><strong>Giới tính:</strong> <?php echo ($nhanvien['gioitinh'] == 'M') ? 'Nam' : 'Nữ'; ?></p>
                            <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($nhanvien['ngaysinh']); ?></p>
                            <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($nhanvien['dienthoai']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($nhanvien['email']); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($nhanvien['diachi']); ?></p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Phòng ban:</strong> <?php echo htmlspecialchars($nhanvien['tenpb']); ?></p>
                    <p><strong>Vị trí:</strong> <?php echo htmlspecialchars($nhanvien['tenvt']); ?></p>
                    <p><strong>Ngày vào làm:</strong> <?php echo htmlspecialchars($nhanvien['ngay_vao_lam']); ?></p>

                    <!-- Nút Xóa và Sửa -->
                    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Xóa Nhân Viên</button>
                    <a href="sua_nv.php?id=<?php echo $nhanvien['id']; ?>" class="btn btn-warning mt-3 ms-2">Sửa Nhân Viên</a>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-danger">Không tìm thấy thông tin nhân viên.</p>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="danhsachnv.php" class="btn btn-primary">Quay lại danh sách</a>
        </div>
    </div>

    <!-- Cửa sổ xác nhận xóa -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteLabel">Sa thải nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn muốn xóa thông tin nhân viên này trong công ty?</p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="xoa_nv.php">
                        <input type="hidden" name="id" value="<?php echo $nhanvien['id']; ?>">
                        <button type="submit" class="btn btn-danger">Xác nhận</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Quay lại</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
