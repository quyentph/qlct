<?php
// Kết nối CSDL
$conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lấy danh sách phòng ban và vị trí cho select
$phongbans = $conn->query("SELECT id, tenpb FROM phongban")->fetchAll(PDO::FETCH_ASSOC);
$vitris = $conn->query("SELECT id, tenvt FROM vitri")->fetchAll(PDO::FETCH_ASSOC);

// Xử lý tìm kiếm
$where = [];
$params = [];

if (!empty($_GET['id'])) {
    $where[] = "nv.id = :id";
    $params[':id'] = $_GET['id'];
}
if (!empty($_GET['tennv'])) {
    $where[] = "nv.tennv LIKE :tennv";
    $params[':tennv'] = '%' . $_GET['tennv'] . '%';
}
if (!empty($_GET['phongban_id'])) {
    $where[] = "nv.phongban_id = :phongban_id";
    $params[':phongban_id'] = $_GET['phongban_id'];
}
if (!empty($_GET['vitri_id'])) {
    $where[] = "nv.vitri_id = :vitri_id";
    $params[':vitri_id'] = $_GET['vitri_id'];
}

$sql = "SELECT nv.id, nv.tennv, nv.gioitinh, nv.ngaysinh, nv.dienthoai, nv.email, nv.diachi, 
               pb.tenpb, vt.tenvt, nv.ngay_vao_lam
        FROM nhanvien nv
        INNER JOIN phongban pb ON nv.phongban_id = pb.id
        INNER JOIN vitri vt ON nv.vitri_id = vt.id";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$nhanviens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm định dạng ngày dd/mm/yyyy
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm nhân viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style="background: #f8f9fa;">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold mb-0">Tìm kiếm nhân viên</h2>
            <a href="index.php" class="btn btn-secondary d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-door me-2" viewBox="0 0 16 16">
                  <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 2 7.5V14a1 1 0 0 0 1 1h3.5a.5.5 0 0 0 .5-.5V11a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.5a.5.5 0 0 0 .5.5H13a1 1 0 0 0 1-1V7.5a.5.5 0 0 0-.146-.354l-6-6z"/>
                  <path d="M13 2.5V6l-5-5-5 5V2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5z"/>
                </svg>
                Trang chủ
            </a>
        </div>
        <form method="get" class="row g-3 mb-4 bg-white p-3 rounded-4 shadow-sm">
            <div class="col-md-2">
                <input type="text" name="id" class="form-control" placeholder="ID" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="tennv" class="form-control" placeholder="Tên nhân viên" value="<?php echo isset($_GET['tennv']) ? htmlspecialchars($_GET['tennv']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <select name="phongban_id" class="form-select">
                    <option value="">-- Phòng ban --</option>
                    <?php foreach($phongbans as $pb): ?>
                        <option value="<?php echo $pb['id']; ?>" <?php if(isset($_GET['phongban_id']) && $_GET['phongban_id']==$pb['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($pb['tenpb']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="vitri_id" class="form-select">
                    <option value="">-- Vị trí --</option>
                    <?php foreach($vitris as $vt): ?>
                        <option value="<?php echo $vt['id']; ?>" <?php if(isset($_GET['vitri_id']) && $_GET['vitri_id']==$vt['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($vt['tenvt']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-success">Tìm</button>
            </div>
        </form>
        <div class="table-responsive rounded-4 shadow-sm bg-white p-3">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Tên nhân viên</th>
                        <th>Giới tính</th>
                        <th>Ngày sinh</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th>Phòng ban</th>
                        <th>Vị trí</th>
                        <th>Ngày vào làm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($nhanviens) > 0): ?>
                        <?php foreach($nhanviens as $nv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($nv['id']); ?></td>
                            <td><?php echo htmlspecialchars($nv['tennv']); ?></td>
                            <td><?php echo htmlspecialchars($nv['gioitinh']); ?></td>
                            <td><?php echo formatDate($nv['ngaysinh']); ?></td>
                            <td><?php echo htmlspecialchars($nv['dienthoai']); ?></td>
                            <td><?php echo htmlspecialchars($nv['email']); ?></td>
                            <td><?php echo htmlspecialchars($nv['diachi']); ?></td>
                            <td><?php echo htmlspecialchars($nv['tenpb']); ?></td>
                            <td><?php echo htmlspecialchars($nv['tenvt']); ?></td>
                            <td><?php echo formatDate($nv['ngay_vao_lam']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Không tìm thấy nhân viên phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>