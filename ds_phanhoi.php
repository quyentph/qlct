<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

$stmt = $conn->query("SELECT * FROM phanhoi ORDER BY ngay_gui DESC");
$phanhois = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách phản hồi</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body{
            background-color:rgb(149, 198, 247);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .modal-dialog {
            max-width: 600px;
        }
    </style>
</head>
<body class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Danh sách phản hồi đã gửi</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Nội dung</th>
                        <th>Thời gian gửi</th>
                        <th>Trạng thái</th>
                        <th>Phản hồi từ admin</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($phanhois as $index => $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tieude']) ?></td>
                        <td><?= htmlspecialchars($row['loai']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['noidung'])) ?></td>
                        <td><?= htmlspecialchars($row['ngay_gui']) ?></td>
                        <td>
                            <span class="badge <?= $row['trang_thai'] == 'Chưa phản hồi' ? 'bg-warning text-dark' : 'bg-success' ?>">
                                <?= $row['trang_thai'] ?>
                            </span>
                        </td>
                        <td><?= $row['phanhoi_admin'] ? htmlspecialchars($row['phanhoi_admin']) : '<em>Chưa có</em>' ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#phanhoiModal<?= $index ?>">
                                <?= $row['phanhoi_admin'] ? 'Chỉnh sửa' : 'Ghi nhận' ?>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal phản hồi -->
                    <div class="modal fade" id="phanhoiModal<?= $index ?>" tabindex="-1" aria-labelledby="phanhoiModalLabel<?= $index ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post" action="xuly_phanhoi.php" onsubmit="return confirm('Bạn chắc chắn muốn gửi phản hồi này?');">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="phanhoiModalLabel<?= $index ?>">Phản hồi phản ánh: <?= htmlspecialchars($row['tieude']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="phanhoi_id" value="<?= $row['id'] ?>">
                                        <p><strong>Nội dung phản ánh:</strong></p>
                                        <div class="mb-3 border rounded p-2 bg-light">
                                            <?= nl2br(htmlspecialchars($row['noidung'])) ?>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phanhoi_admin<?= $index ?>" class="form-label"><strong>Nội dung phản hồi từ admin</strong></label>
                                            <textarea class="form-control" id="phanhoi_admin<?= $index ?>" name="phanhoi_admin" rows="5" placeholder="Nhập phản hồi tại đây..." required><?= htmlspecialchars($row['phanhoi_admin']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-success">Gửi phản hồi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Nút quay lại trang chủ -->
    <div class = "container mt-4 ">
        <div class="mb-4">
            <a href="index.php" class="btn btn-secondary">Quay lại trang chủ</a>
        </div>
    </div>


    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
