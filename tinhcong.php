<?php
// Kết nối CSDL bằng PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy danh sách tháng/năm
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Lấy danh sách nhân viên, vị trí, lương cơ bản
$sql = "SELECT nv.id, nv.tennv, vt.tenvt, vt.luong_cb
        FROM nhanvien nv
        LEFT JOIN vitri vt ON nv.vitri_id = vt.id
        ORDER BY nv.tennv ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nhanviens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy dữ liệu chấm công theo tháng
$sql_cc = "SELECT nhanvien_id, ngay_cham_cong, check_in, check_out, tt
           FROM chamcong
           WHERE MONTH(ngay_cham_cong) = :m AND YEAR(ngay_cham_cong) = :y";
$stmt_cc = $conn->prepare($sql_cc);
$stmt_cc->execute([':m' => $month, ':y' => $year]);
$chamcong = [];
while ($row = $stmt_cc->fetch(PDO::FETCH_ASSOC)) {
    $chamcong[$row['nhanvien_id']][] = $row;
}
function tong_ngay_cong($list) {
    $count = 0;
    foreach ($list as $cc) {
        if ($cc['tt'] == 'Đi làm' || $cc['tt'] == 'Làm online') $count++;
    }
    return $count;
}
function tong_gio_cong($list) {
    $total = 0;
    foreach ($list as $cc) {
        if ($cc['check_in'] && $cc['check_out']) {
            $in = strtotime($cc['check_in']);
            $out = strtotime($cc['check_out']);
            if ($out > $in) $total += ($out - $in);
        }
    }
    $h = floor($total / 3600);
    $m = floor(($total % 3600) / 60);
    return [
        'str' => ($total > 0) ? sprintf('%02d:%02d', $h, $m) : '',
        'hours' => $h + $m/60
    ];
}
function tinh_luong($luong_cb, $tong_gio) {
    return $tong_gio > 0 ? round($luong_cb * $tong_gio) : '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng công & lương tháng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/tinhcong.js"></script>
    <style>
        body { background: linear-gradient(135deg, #e0f2fe 0%, #90cdf4 100%); }
        .table thead th, .table tfoot th { background: #38bdf8; color: #fff; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Bảng công & lương tháng</h2>
    <form class="row g-3 mb-4" method="get">
        <div class="col-auto">
            <select name="month" class="form-select">
                <?php for($m=1;$m<=12;$m++): ?>
                <option value="<?php echo $m; ?>" <?php if($m==$month) echo 'selected'; ?>>Tháng <?php echo $m; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <select name="year" class="form-select">
                <?php for($y=date('Y')-2;$y<=date('Y')+1;$y++): ?>
                <option value="<?php echo $y; ?>" <?php if($y==$year) echo 'selected'; ?>>Năm <?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Xem</button>
        </div>
    </form>
    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên nhân viên</th>
                <th>Vị trí</th>
                <th>Lương cơ bản (1 giờ)</th>
                <th>Số ngày công</th>
                <th>Tổng giờ công</th>
                <th>Lương thực nhận</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; foreach($nhanviens as $nv): 
            $list_cc = $chamcong[$nv['id']] ?? [];
            $ngay_cong = tong_ngay_cong($list_cc);
            $tong_gio_arr = tong_gio_cong($list_cc);
            $luong_cb = $nv['luong_cb'] ?? 0;
            $luong_nhan = tinh_luong($luong_cb, $tong_gio_arr['hours']);
            // Nếu không có ngày công, tổng giờ công và lương thì ẩn dòng này
            if ($ngay_cong == 0 && $tong_gio_arr['str'] === '' && $luong_nhan === '') continue;
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($nv['tennv']); ?></td>
                <td><?php echo htmlspecialchars($nv['tenvt']); ?></td>
                <td><?php echo $luong_cb ? number_format($luong_cb) . ' đ' : ''; ?></td>
                <td><?php echo $ngay_cong ?: ''; ?></td>
                <td><?php echo $tong_gio_arr['str']; ?></td>
                <td><?php echo $luong_nhan ? number_format($luong_nhan) . ' đ' : ''; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">Quay lại trang chủ</a>
</div>
</body>
</html>