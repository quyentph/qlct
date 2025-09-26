 <?php
// Kết nối CSDL
$conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lấy danh sách thông báo
$sql = "SELECT * FROM thongbao ORDER BY thoigiantao DESC";
$thongbaos = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách thông báo</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin-top: 50px;
        }

        h2 {
            color: #4e73df;
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }

        .table {
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
        }

        .table th {
            background-color: #4e73df;
            color: white;
            font-weight: bold;
            padding: 12px;
        }

        .table td {
            padding: 12px;
            color: #6c757d;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .btn-info, .btn-danger {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .text-center a {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .text-center a:hover {
            background-color: #5a6268;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            border-radius: 8px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Danh sách thông báo</h2>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Ngày gửi</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($thongbaos) > 0): ?>
                        <?php $stt = 1; foreach($thongbaos as $tb): ?>
                            <tr>
                                <td><?= $stt++ ?></td>
                                <td><?= htmlspecialchars($tb['tieude']) ?></td>
                                <td><?= htmlspecialchars($tb['thoigiantao']) ?></td>
                                <td>
                                    <a href="chitiettb.php?id=<?= $tb['id'] ?>" class="btn btn-info btn-sm">Xem</a>
                                    <form action="xoa_thongbao.php" method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này không?');" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($tb['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không có thông báo nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="index.php">Quay lại trang chủ</a>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
