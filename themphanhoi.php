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

$success = false;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirmed']) && $_POST['confirmed'] == "1") {
    $tieude = $_POST['tieude'];
    $loai = $_POST['loai'];
    $noidung = $_POST['noidung'];

    $stmt = $conn->prepare("INSERT INTO phanhoi (nhanvien_id, tieude, loai, noidung) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nhanvien_id, $tieude, $loai, $noidung]);
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gửi phản ánh tới sếp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .checkmark {
            font-size: 50px;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="mb-4 text-center text-primary">Gửi phản ánh đến quản lý</h3>
    <?php if ($success): ?>
        <div class="text-center">
            <div class="checkmark">✔</div>
            <p class="mt-3">Phản ánh đã được gửi thành công!</p>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = "index_nv.php";
            }, 2000);
        </script>
    <?php else: ?>
        <form method="POST" id="formPhanHoi">
            <input type="hidden" name="confirmed" value="1" id="confirmedField">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="tieude" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Loại phản ánh</label>
                <select name="loai" class="form-select" required>
                    <option value="Góp ý">Góp ý</option>
                    <option value="Khiếu nại">Khiếu nại</option>
                    <option value="Kỹ thuật">Kỹ thuật</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="noidung" class="form-control" rows="5" required></textarea>
            </div>
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#confirmModal">Gửi phản ánh</button>
        </form>
    <?php endif; ?>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmLabel">Xác nhận gửi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        Bạn có chắc chắn muốn gửi phản ánh này?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-success" id="btnXacNhan">Gửi</button>
      </div>
    </div>
  </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("btnXacNhan").onclick = function () {
        document.getElementById("confirmModal").classList.remove("show");
        const modalBackdrop = document.querySelector(".modal-backdrop");
        if (modalBackdrop) modalBackdrop.remove();
        document.getElementById("formPhanHoi").submit();
    };
</script>
</body>
</html>
