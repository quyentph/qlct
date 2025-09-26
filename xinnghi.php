<?php
session_start();
if (!isset($_SESSION['nhanvien_id'])) {
    header("Location: login.php");
    exit();
}
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nhanvien_id = $_SESSION['nhanvien_id'];
    $tieude = 'Đơn xin nghỉ'; 
    $loai = 'Xin nghỉ phép'; 
    $noidung = $_POST['noidung']; 

    try {
        $stmt = $conn->prepare("INSERT INTO phanhoi (nhanvien_id, tieude, loai, noidung, trang_thai, ngay_gui) 
                                VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nhanvien_id, $tieude, $loai, $noidung, 'Chưa phản hồi']);
        if($_SESSION['role'] == 'admin'|| $_SESSION['role'] == 'manager') {
            header("Location: ds_phanhoi.php");
            exit();
        }
        else{
            header("Location: index_nv.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Lỗi: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đơn xin nghỉ phép</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color:rgb(182, 237, 255);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-outline-info:hover {
            background-color: #38bdf8;
            color: white;
            border-color: #38bdf8;
        }
        textarea:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 5px rgba(56, 189, 248, 0.5);
        }
        .container {
            padding-top: 50px;
        }
        h5 {
            color:rgb(1, 4, 5);
            font-weight: bold;
            text-shadow: 0 2px 8px #0ea5e9;
        }

    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm rounded p-4">
        <h5 class="mb-4 text-center">Đơn xin nghỉ phép</h5>
        <form action="xinnghi.php" method="POST">
            <input type="hidden" name="nhanvien_id" value="<?= $nhanvien_id ?>">
            <input type="hidden" name="tieude" value="Đơn xin nghỉ">
            <input type="hidden" name="loai" value="Xin nghỉ phép">
            
            <div class="mb-3">
                <label for="noidung" class="form-label">Lý do xin nghỉ</label>
                <textarea class="form-control" id="noidung" name="noidung" rows="5" placeholder="Nhập lý do xin nghỉ phép..." required></textarea>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-outline-info">Gửi đơn xin nghỉ</button>
            </div>
        </form>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
