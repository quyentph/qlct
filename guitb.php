<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv;charset=utf8", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và kiểm tra
    $tieude = !empty($_POST['tieude']) ? $_POST['tieude'] : "";
    $thongbao = !empty($_POST['thongbao']) ? $_POST['thongbao'] : "";
    $tailieu = "";

    // Kiểm tra tiêu đề và nội dung thông báo
    if (empty($tieude) || empty($thongbao)) {
        echo "<div class='alert alert-danger text-center mt-5'>Lỗi: Tiêu đề và nội dung thông báo không được để trống!</div>";
        exit();
    }

    // Kiểm tra và xử lý tài liệu tải lên
    if (!empty($_FILES['tailieu']['name'])) {
        $target_dir = "document/";
        $target_file = $target_dir . basename($_FILES["tailieu"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["pdf", "docx", "txt"];
        if (!in_array($file_type, $allowed_types)) {
            die("Lỗi: Chỉ được phép tải lên file PDF, DOCX hoặc TXT!");
        }

        if (move_uploaded_file($_FILES["tailieu"]["tmp_name"], $target_file)) {
            $tailieu = $target_file;
        }
    }

    // Chèn thông báo vào bảng thongbao nếu tiêu đề và nội dung hợp lệ
    $sql = "INSERT INTO thongbao (tieude, thongbao, tailieu) VALUES (:tieude, :thongbao, :tailieu)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':tieude' => $tieude,
        ':thongbao' => $thongbao,
        ':tailieu' => $tailieu
    ]);

    // Lấy ID thông báo vừa chèn
    $thongbao_id = $conn->lastInsertId();

    // Gửi thông báo cho các nhân viên được chọn
    if (!empty($_POST['nhanvien'])) {
        $insert_nv_tb = $conn->prepare("INSERT INTO nv_tb (nhanvien_id, thongbao_id, is_read) VALUES (:nhanvien_id, :thongbao_id, 0)");
        foreach ($_POST['nhanvien'] as $nhanvien_id) {
            $insert_nv_tb->execute([
                ':nhanvien_id' => $nhanvien_id,
                ':thongbao_id' => $thongbao_id
            ]);
        }
    }

    echo "<div class='alert alert-success text-center mt-5'>Thông báo đã được gửi thành công!</div>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gửi Thông Báo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6E8EF1, #A777E3);
            color: white;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-outline-light {
            color: white;
            border-color: white;
        }
        .btn-outline-light:hover {
            background-color: white;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card col-md-8 mx-auto">
            <h2 class="text-center mb-4">📢 Gửi Thông Báo</h2>
            <form action="guitb.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="tieude" class="form-label">Tiêu đề:</label>
                    <input type="text" class="form-control" name="tieude" required>
                </div>

                <div class="mb-3">
                    <label for="thongbao" class="form-label">Nội dung thông báo:</label>
                    <textarea class="form-control" name="thongbao" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="tailieu" class="form-label">Tài liệu (nếu có):</label>
                    <input type="file" class="form-control" name="tailieu">
                </div>

                <div class="mb-3">
                    <label class="form-label">👥 Chọn nhân viên:</label>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-light" id="selectAllBtn">🔘 Chọn tất cả</button>
                    </div>
                    <select class="form-select selectpicker" name="nhanvien[]" multiple data-live-search="true" title="Chọn nhân viên...">
                        <?php
                        $stmt = $conn->query("SELECT id, tennv FROM nhanvien ORDER BY tennv");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['tennv']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">⬅️ Quay Lại</button>
                    <button type="submit" class="btn btn-primary">🚀 Gửi Thông Báo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.selectpicker').selectpicker();

            let allSelected = false;
            $("#selectAllBtn").click(function () {
                if (!allSelected) {
                    $('.selectpicker').selectpicker('selectAll');
                    $(this).text("❌ Bỏ chọn tất cả");
                } else {
                    $('.selectpicker').selectpicker('deselectAll');
                    $(this).text("🔘 Chọn tất cả");
                }
                allSelected = !allSelected;
            });
        });
    </script>
</body>
</html>
