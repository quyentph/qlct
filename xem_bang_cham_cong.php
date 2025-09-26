<?php

session_start();
if (!isset($_SESSION['username'])) {
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

// Lấy danh sách chấm công, join với tên nhân viên
$sql = "SELECT chamcong.*, nhanvien.tennv
        FROM chamcong 
        INNER JOIN nhanvien ON chamcong.nhanvien_id = nhanvien.id
        ORDER BY chamcong.ngay_cham_cong DESC, chamcong.id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem bảng chấm công</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/xemchamcong.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Bảng chấm công nhân viên</h2>
    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Tên nhân viên</th>
                <th>Ngày chấm công</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($rows && count($rows) > 0): 
            foreach($rows as $row): 
                $rowClass = '';
                $tt = $row['tt'];
                if ($tt == 'Đi làm') $rowClass = 'row-dilam';
                elseif ($tt == 'Nghỉ phép') $rowClass = 'row-nghiphep';
                elseif ($tt == 'Nghỉ không phép') $rowClass = 'row-nghikp'; 
                elseif ($tt == 'Làm online') $rowClass = 'row-lamonline';

            ?>
            
            <tr class = "<?php  echo $rowClass?>">
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['tennv']); ?></td>
                <td><?php echo htmlspecialchars($row['ngay_cham_cong']); ?></td>
                <td><?php echo htmlspecialchars($row['check_in']); ?></td>
                <td><?php echo htmlspecialchars($row['check_out']); ?></td>
                <td><?php echo htmlspecialchars($row['tt']); ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center">Không có dữ liệu</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    <?php
        if($_SESSION['role'] =='admin' || $_SESSION['role'] == 'quanli'){
            echo '<a href="index.php" class="btn btn-secondary mt-3">Quay lại trang chủ</a>';
        }
    ?>
</div>
</body>
</html>