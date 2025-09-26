<?php
session_start();
if (!isset($_SESSION['nhanvien_id'])) {
    header("Location: login.php");
    exit();
}

$nhanvien_id = $_SESSION['nhanvien_id'];
$thongbao_id = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Cập nhật trạng thái thông báo sang đã đọc
$stmt = $conn->prepare("UPDATE nv_tb SET is_read = 1 WHERE thongbao_id = ? AND nhanvien_id = ?");
$stmt->execute([$thongbao_id, $nhanvien_id]);

// Lấy thông tin chi tiết thông báo
$stmt = $conn->prepare("SELECT tieude, thongbao, tailieu FROM thongbao WHERE id = ?");
$stmt->execute([$thongbao_id]);
$thongbao = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Thông báo</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color:rgb(193, 224, 255);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
        }
        .notification-card {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 30px;
            margin-top: 20px;
            min-height: 300px;
        }
        .notification-card h2 {
            color: #0d6efd;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .notification-card p {
            color: #495057;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .btn-primary-custom {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            text-align: center;
            display: inline-block;
            margin-top: 10px;
        }
        .btn-primary-custom:hover {
            background-color: #0b5ed7;
        }
        .btn-outline-custom {
            border-color: #0d6efd;
            color: #0d6efd;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
        }
        .btn-outline-custom:hover {
            background-color: #0d6efd;
            color: white;
        }
        .notification-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .notification-footer a {
            text-decoration: none;
            color: #0d6efd;
        }
        .notification-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="notification-card">
        <h2 class="text-center"><?php echo htmlspecialchars($thongbao['tieude']); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($thongbao['thongbao'])); ?></p>
        <?php if ($thongbao['tailieu']): ?>
            <div class="text-center">
                <a href="<?php echo htmlspecialchars($thongbao['tailieu']); ?>" class="btn btn-primary-custom" target="_blank">
                    <i class="bi bi-file-earmark-arrow-down"></i> Tải tài liệu
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Footer with back link -->
        <div class="notification-footer">
            <?php 
            if(isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'quanli')) {
                echo '<a href="ds_thongbao.php" class="btn btn-outline-custom">Trở về</a>';
            } else {
                echo '<a href="index_nv.php" class="btn btn-outline-custom">Trở về</a>';
            }
            ?>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
