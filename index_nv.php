<?php
session_start();
if (!isset($_SESSION['nhanvien_id'])) {
    header("Location: login.php");
    exit();
}
$nhanvien_id = $_SESSION['nhanvien_id'];

try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy thông tin nhân viên
$stmt = $conn->prepare("SELECT nv.*, vt.tenvt, pb.tenpb FROM nhanvien nv 
    LEFT JOIN vitri vt ON nv.vitri_id = vt.id
    LEFT JOIN phongban pb ON nv.phongban_id = pb.id
    WHERE nv.id=?");
$stmt->execute([$nhanvien_id]);
$nv = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra thông báo chưa đọc
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM nv_tb WHERE nhanvien_id = ? AND is_read = 0");
$stmt->execute([$nhanvien_id]);
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Hiển thị dấu đỏ nếu có thông báo chưa đọc

$notificationClass = $unreadCount > 0 ? 'text-danger' : ''; // Dấu đỏ nếu có thông báo chưa đọc
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang nhân viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/index_nv.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4 text-center text-white" style="text-shadow:0 2px 8px #0ea5e9;">Xin chào, <?php echo htmlspecialchars($nv['tennv']); ?>!</h2>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card p-3">
                <h5 class="mb-3 text-primary">Thông tin cá nhân</h5>
                <ul class="list-unstyled mb-0">
                    <li><b>Họ tên:</b> <?php echo htmlspecialchars($nv['tennv']); ?></li>
                    <li><b>Giới tính:</b> <?php echo htmlspecialchars($nv['gioitinh']); ?></li>
                    <li><b>Ngày sinh:</b> <?php echo htmlspecialchars($nv['ngaysinh']); ?></li>
                    <li><b>Email:</b> <?php echo htmlspecialchars($nv['email']); ?></li>
                    <li><b>Điện thoại:</b> <?php echo htmlspecialchars($nv['dienthoai']); ?></li>
                    <li><b>Địa chỉ:</b> <?php echo htmlspecialchars($nv['diachi']); ?></li>
                    <li><b>Phòng ban:</b> <?php echo htmlspecialchars($nv['tenpb']); ?></li>
                    <li><b>Vị trí:</b> <?php echo htmlspecialchars($nv['tenvt']); ?></li>
                    <li><b>Ngày vào làm:</b> <?php echo htmlspecialchars($nv['ngay_vao_lam']); ?></li>
                </ul>
                <a href="doimk.php" class="btn btn-outline-primary mt-3 w-100">Đổi mật khẩu</a>
            </div>
        </div>
        <div class="col-md-8">
            <ul class="nav nav-tabs mb-3" id="nvTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="chamcong-tab" data-bs-toggle="tab" data-bs-target="#chamcong" type="button" role="tab">Chấm công</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="nghiphep-tab" data-bs-toggle="tab" data-bs-target="#nghiphep" type="button" role="tab">Đăng ký nghỉ phép</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="luong-tab" data-bs-toggle="tab" data-bs-target="#luong" type="button" role="tab">Bảng lương cá nhân</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="phanhoi-tab" data-bs-toggle="tab" data-bs-target="#phanhoi" type="button" role="tab">Gửi phản hồi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="thongbao-tab" data-bs-toggle="tab" data-bs-target="#thongbao" type="button" role="tab">
                        Thông báo
                        <?php echo "<!-- Unread count: $unreadCount -->"; ?>

                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger rounded-circle" style="width:10px; height:10px;"></span>
                        <?php endif; ?>
                    </button>

                </li>
            </ul>
            <div class="tab-content" id="nvTabContent">
                <div class="tab-pane fade show active" id="chamcong" role="tabpanel">
                    <iframe src="chamcong.php" style="width:100%;height:550px;border:none;border-radius:8px;box-shadow:0 2px 8px #38bdf84d;"></iframe>
                </div>
                <div class="tab-pane fade" id="nghiphep" role="tabpanel">
                    <a href="xinnghi.php" class="btn btn-outline-info mt-3">Đăng ký nghỉ phép</a>
                </div>
                <div class="tab-pane fade" id="luong" role="tabpanel">
                    <h5 class="mb-3 text-dark">Bảng lương cá nhân</h5>
                    <?php
                    $sql_vt = "SELECT luong_cb FROM vitri WHERE id = :vitri_id";
                    $stmt_vt = $conn->prepare($sql_vt);
                    $stmt_vt->execute([':vitri_id' => $nv['vitri_id']]);
                    $luong_cb = $stmt_vt->fetch(PDO::FETCH_ASSOC)['luong_cb'];
                    $sql_cc = "SELECT MONTH(ngay_cham_cong) AS month, YEAR(ngay_cham_cong) AS year 
                            FROM chamcong 
                            WHERE nhanvien_id = :id
                            GROUP BY month, year 
                            ORDER BY year DESC, month DESC";
                    $stmt_cc = $conn->prepare($sql_cc);
                    $stmt_cc->execute([':id' => $nhanvien_id]);
                    if ($stmt_cc->rowCount() > 0):
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tháng</th>
                                <th>Tổng giờ làm</th>
                                <th>Lương tháng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $stmt_cc->fetch(PDO::FETCH_ASSOC)):
                                $month = $row['month'];
                                $year = $row['year'];
                                $sql_gio = "SELECT SUM(TIMESTAMPDIFF(HOUR, check_in, check_out)) AS total_hours 
                                            FROM chamcong 
                                            WHERE nhanvien_id = :id AND MONTH(ngay_cham_cong) = :month AND YEAR(ngay_cham_cong) = :year";
                                $stmt_gio = $conn->prepare($sql_gio);
                                $stmt_gio->execute([':id' => $nhanvien_id, ':month' => $month, ':year' => $year]);
                                $total_hours = $stmt_gio->fetch(PDO::FETCH_ASSOC)['total_hours'];
                                $monthly_salary = $total_hours * $luong_cb;
                            ?>
                            <tr>
                                <td><?= sprintf('%02d/%d', $month, $year) ?></td>
                                <td><?= number_format($total_hours, 0, ',', '.') ?> giờ</td>
                                <td><?= number_format($monthly_salary, 0, ',', '.') ?> VNĐ</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p>Chưa có dữ liệu chấm công cho nhân viên này.</p>
                    <?php endif; ?>
                </div>


            
                <div class="tab-pane fade" id="phanhoi" role="tabpanel">
                    <div class="mb-3">
                    </div>
                    <h5 class="text-dark">Danh sách phản hồi đã gửi</h5>
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Loại</th>
                                <th>Thời gian gửi</th>
                                <th>Trạng thái</th>
                                <th>Phản hồi từ admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT tieude, loai, ngay_gui, trang_thai, phanhoi_admin FROM phanhoi WHERE nhanvien_id = ? ORDER BY ngay_gui DESC");
                            $stmt->execute([$nhanvien_id]);
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tieude']) ?></td>
                                <td><?= htmlspecialchars($row['loai']) ?></td>
                                <td><?= htmlspecialchars($row['ngay_gui']) ?></td>
                                <td>
                                    <span class="badge <?= $row['trang_thai'] == 'Chưa phản hồi' ? 'bg-warning text-dark' : 'bg-success' ?>">
                                        <?= $row['trang_thai'] ?>
                                    </span>
                                </td>
                                <td><?= $row['phanhoi_admin'] ? htmlspecialchars($row['phanhoi_admin']) : '<em>Chưa có</em>' ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <a href="themphanhoi.php" class="btn btn-outline-info mt-3">Gửi phản hồi/phản ánh</a>
                </div>
                <div class="tab-pane fade" id="thongbao" role="tabpanel">
                    <h5 class="mb-3 text-dark">Thông báo</h5>
                    <div>
                        <?php
                        // Lấy thông báo của nhân viên
                        $stmt = $conn->prepare("SELECT thongbao.id, thongbao.tieude, nv_tb.is_read FROM thongbao
                                                JOIN nv_tb ON thongbao.id = nv_tb.thongbao_id 
                                                WHERE nv_tb.nhanvien_id = ? ORDER BY thongbao.thoigiantao DESC");
                        $stmt->execute([$nhanvien_id]);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $newLabel = $row['is_read'] == 0 ? '<span class="badge bg-danger ms-2">New</span>' : '';
                            $readClass = $row['is_read'] == 0 ? 'fw-bold text-danger' : '';
                            echo "<div class='notification-item'>
                                    <a href='chitiettb.php?id={$row['id']}' class='$readClass'>
                                        {$row['tieude']} $newLabel
                                    </a>
                                </div>";
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
