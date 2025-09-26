<?php
// Kết nối CSDL bằng PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Xử lý cập nhật trạng thái nếu có POST (và chỉ khi không phải các form khác)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['tt']) && !isset($_POST['ten'])) {
    $id = $_POST['id'];
    $tt = $_POST['tt'];
    $stmt = $conn->prepare("UPDATE ungvien SET tt = :tt WHERE id = :id");
    $stmt->execute([':tt' => $tt, ':id' => $id]);
}

// Lấy danh sách ứng viên
$sql = "SELECT id, ten, gioitinh, ngaysinh, email, dienthoai, diachi, vt_ungtuyen, kynang, link_cv, tt, ngay_ung_tuyen, ghichu ,tenpb
        FROM ungvien
        ORDER BY FIELD(tt, 'Chờ xử lý', 'Đã phỏng vấn', 'Đã nhận', 'Từ chối'), id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$ungviens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách ứng viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/indexcss.css">
    <link rel="stylesheet" href="css/themungvien.css">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Danh sách ứng viên</h2>
        <a href="index.php" class="btn btn-primary d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-door me-2" viewBox="0 0 16 16">
                <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 2 7.5V14a1 1 0 0 0 1 1h3.5a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.5a.5.5 0 0 0 .5.5H13a1 1 0 0 0 1-1V7.5a.5.5 0 0 0-.146-.354l-6-6z"/>
                <path d="M13 2.5V6l-5-5-5 5V2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5z"/>
            </svg>
            Trang chủ
        </a>
    </div>
    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giới tính</th>
                <th>Ngày sinh</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Địa chỉ</th>
                <th>Vị trí ứng tuyển</th>
                <th>Phòng ban</th>
                <th>Kỹ năng</th>
                <th>CV</th>
                <th>Trạng thái</th>
                <th>Ngày ứng tuyển</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($ungviens && count($ungviens) > 0): 
            foreach($ungviens as $row): 
                $rowClass = '';
                $tt = trim($row['tt']);
                if ($tt == 'Đã phỏng vấn') $rowClass = 'row-phongvan';
                elseif ($tt == 'Đã nhận') $rowClass = 'row-nhan';
                elseif ($tt == 'Từ chối') $rowClass = 'row-tuchoi';

        ?>
            <tr class="<?php echo $rowClass; ?>">
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['ten']); ?></td>
                <td><?php echo htmlspecialchars($row['gioitinh']); ?></td>
                <td><?php echo htmlspecialchars($row['ngaysinh']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['dienthoai']); ?></td>
                <td><?php echo htmlspecialchars($row['diachi']); ?></td>
                <td><?php echo htmlspecialchars($row['vt_ungtuyen']); ?></td>
                <td><?php echo htmlspecialchars($row['tenpb']); ?></td>
                <td><?php echo htmlspecialchars($row['kynang']); ?></td>
                <td>
                  <?php if (!empty($row['link_cv'])): ?>
                    <a href="<?php echo htmlspecialchars($row['link_cv']); ?>" target="_blank">Xem CV</a>
                  <?php endif; ?>
                </td>
                <td>
                  <form method="post" class="form-inline" style="display:flex; gap:4px;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <select name="tt" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="Chờ xử lý" <?php if($tt=='Chờ xử lý') echo 'selected'; ?>>Chờ xử lý</option>
                        <option value="Đã phỏng vấn" <?php if($tt=='Đã phỏng vấn') echo 'selected'; ?>>Đã phỏng vấn</option>
                        <option value="Đã nhận" <?php if($tt=='Đã nhận') echo 'selected'; ?>>Đã nhận</option>
                        <option value="Từ chối" <?php if($tt=='Từ chối') echo 'selected'; ?>>Từ chối</option>
                    </select>
                  </form>
                </td>
                <td><?php echo htmlspecialchars($row['ngay_ung_tuyen']); ?></td>
                <td><?php echo htmlspecialchars($row['ghichu']); ?></td>
                <td class="text-center">
                  <div class="action-btns">
                    <form method="post" action="them_nv.php">
                        <input type="hidden" name="from_ungvien" value="true">
                      <input type="hidden" name="tennv" value="<?php echo htmlspecialchars($row['ten']); ?>">
                      <input type="hidden" name="gioitinh" value="<?php echo htmlspecialchars($row['gioitinh']); ?>">
                      <input type="hidden" name="ngaysinh" value="<?php echo htmlspecialchars($row['ngaysinh']); ?>">
                      <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                      <input type="hidden" name="dienthoai" value="<?php echo htmlspecialchars($row['dienthoai']); ?>">
                      <input type="hidden" name="diachi" value="<?php echo htmlspecialchars($row['diachi']); ?>">
                      <input type="hidden" name="vt_ungtuyen" value="<?php echo htmlspecialchars($row['vt_ungtuyen']); ?>">
                      <input type="hidden" name="tenpb" value="<?php echo htmlspecialchars($row['tenpb']); ?>">
                      <input type="hidden" name="kynang" value="<?php echo htmlspecialchars($row['kynang']); ?>">
                      <button type="submit" class="btn btn-success btn-sm">V</button>
                  </form>
                    <form method="post" action="xoa_ungvien.php" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa ứng viên này?');">
                      <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                      <button type="submit" class="btn btn-danger btn-sm" title="Xóa ứng viên">X</button>
                    </form>
                  </div>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="14" class="text-center">Không có dữ liệu</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>