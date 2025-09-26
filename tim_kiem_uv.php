<?php
// filepath: c:\xampp\htdocs\BTL_ LTWEB\tim_kiem_uv.php

// Kết nối CSDL bằng PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Xử lý tìm kiếm
$keyword = $_GET['keyword'] ?? '';
$vitri = $_GET['vitri'] ?? '';
$tt = $_GET['tt'] ?? '';

$sql = "SELECT * FROM ungvien WHERE 1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (ten LIKE :kw OR email LIKE :kw OR dienthoai LIKE :kw)";
    $params[':kw'] = '%' . $keyword . '%';
}
if ($vitri !== '') {
    $sql .= " AND vt_ungtuyen = :vitri";
    $params[':vitri'] = $vitri;
}
if ($tt !== '') {
    $sql .= " AND tt = :tt";
    $params[':tt'] = $tt;
}
$sql .= " ORDER BY FIELD(tt, 'Chờ xử lý', 'Đã phỏng vấn', 'Đã nhận', 'Từ chối'), id ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$ungviens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách vị trí ứng tuyển
$vitri_list = [];
try {
    $stmt_vt = $conn->query("SELECT DISTINCT vt_ungtuyen FROM ungvien");
    $vitri_list = $stmt_vt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $vitri_list = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm ứng viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/indexcss.css">
    <link rel="stylesheet" href="css/themungvien.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Tìm kiếm ứng viên</h2>
    <form class="row g-3 mb-4" method="get">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" placeholder="Tên, email hoặc điện thoại" value="<?php echo htmlspecialchars($keyword); ?>">
        </div>
        <div class="col-md-3">
            <select name="vitri" class="form-select">
                <option value="">-- Vị trí ứng tuyển --</option>
                <?php foreach($vitri_list as $vt): ?>
                    <option value="<?php echo htmlspecialchars($vt); ?>" <?php if($vitri==$vt) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($vt); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="tt" class="form-select">
                <option value="">-- Trạng thái --</option>
                <option value="Chờ xử lý" <?php if($tt=='Chờ xử lý') echo 'selected'; ?>>Chờ xử lý</option>
                <option value="Đã phỏng vấn" <?php if($tt=='Đã phỏng vấn') echo 'selected'; ?>>Đã phỏng vấn</option>
                <option value="Đã nhận" <?php if($tt=='Đã nhận') echo 'selected'; ?>>Đã nhận</option>
                <option value="Từ chối" <?php if($tt=='Từ chối') echo 'selected'; ?>>Từ chối</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
    </form>
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
                <th>Kỹ năng</th>
                <th>CV</th>
                <th>Trạng thái</th>
                <th>Ngày ứng tuyển</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($ungviens && count($ungviens) > 0): 
            foreach($ungviens as $row): 
                $rowClass = '';
                $tt_row = trim($row['tt']);
                if ($tt_row == 'Đã phỏng vấn') $rowClass = 'row-phongvan';
                elseif ($tt_row == 'Đã nhận') $rowClass = 'row-nhan';
                elseif ($tt_row == 'Từ chối') $rowClass = 'row-tuchoi';
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
                <td><?php echo htmlspecialchars($row['kynang']); ?></td>
                <td>
                  <?php if (!empty($row['link_cv'])): ?>
                    <a href="<?php echo htmlspecialchars($row['link_cv']); ?>" target="_blank">Xem CV</a>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['tt']); ?></td>
                <td><?php echo htmlspecialchars($row['ngay_ung_tuyen']); ?></td>
                <td><?php echo htmlspecialchars($row['ghichu']); ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="13" class="text-center">Không có dữ liệu</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">Quay lại trang chủ</a>
</div>
</body>
</html>