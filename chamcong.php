<?php
// filepath: c:\xampp\htdocs\BTL_ LTWEB\chamcong.php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Lấy id nhân viên từ session
$nhanvien_id = $_SESSION['nhanvien_id'] ?? null;
if (!$nhanvien_id) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy ngày hôm nay
$today = date('Y-m-d');

// Kiểm tra trạng thái chấm công hiện tại
$stmt = $conn->prepare("SELECT * FROM chamcong WHERE nhanvien_id=? AND ngay_cham_cong=?");
$stmt->execute([$nhanvien_id, $today]);
$cc = $stmt->fetch(PDO::FETCH_ASSOC);

$trang_thai = 'checkin'; // Mặc định là check in
if ($cc) {
    if ($cc['check_in'] && !$cc['check_out']) {
        $trang_thai = 'checkout';
    } elseif ($cc['check_in'] && $cc['check_out']) {
        $trang_thai = 'done';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'checkin') {
        if (!$cc) {
            $stmt = $conn->prepare("INSERT INTO chamcong (nhanvien_id, ngay_cham_cong, check_in, tt) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nhanvien_id, $today, date('H:i:s'), 'Đi làm']);
        } else {
            // Nếu đã có, cập nhật lại giờ check_in
            $stmt = $conn->prepare("UPDATE chamcong SET check_in=?, check_out=NULL, tt='Đi làm' WHERE id=?");
            $stmt->execute([date('H:i:s'), $cc['id']]);
        }
        $trang_thai = 'checkout';
    } elseif ($_POST['action'] === 'checkout' && $cc && $cc['check_in'] && !$cc['check_out']) {
        $stmt = $conn->prepare("UPDATE chamcong SET check_out=? WHERE id=?");
        $stmt->execute([date('H:i:s'), $cc['id']]);
        $trang_thai = 'done';
    }
    header("Location: chamcong.php");
    exit();
}

// Lọc lịch sử theo tháng/năm
$filter_month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$filter_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$stmt = $conn->prepare("SELECT * FROM chamcong WHERE nhanvien_id=? AND MONTH(ngay_cham_cong)=? AND YEAR(ngay_cham_cong)=? ORDER BY ngay_cham_cong DESC");
$stmt->execute([$nhanvien_id, $filter_month, $filter_year]);
$lichsu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm tính tổng thời gian làm trong tháng
function tong_gio_thang($lichsu) {
    $total = 0;
    foreach ($lichsu as $cc) {
        if ($cc['check_in'] && $cc['check_out']) {
            $in = strtotime($cc['check_in']);
            $out = strtotime($cc['check_out']);
            if ($out > $in) $total += ($out - $in);
        }
    }
    $h = floor($total / 3600);
    $m = floor(($total % 3600) / 60);
    return ($total > 0) ? sprintf('%02d:%02d', $h, $m) : '00:00';
}
$tong_gio = tong_gio_thang($lichsu);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chấm công</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .btn-chamcong {
            font-size: 1.3rem;
            padding: 16px 40px;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .btn-checkin {
            background: #38bdf8;
            color: #fff;
        }
        .btn-checkin:hover { background: #0ea5e9; }
        .btn-checkout {
            background: #facc15;
            color: #222;
        }
        .btn-checkout:hover { background: #f59e42; }
        .btn-done {
            background: #22c55e;
            color: #fff;
            cursor: default;
        }
    </style>
</head>
<body>
<div class="container py-5 text-center">
    <h2 class="mb-4">Chấm công ngày <?php echo date('d/m/Y'); ?></h2>
    <form method="post">
        <?php if ($trang_thai === 'checkin'): ?>
            <button type="submit" name="action" value="checkin" class="btn btn-chamcong btn-checkin">Check In</button>
        <?php elseif ($trang_thai === 'checkout'): ?>
            <button type="submit" name="action" value="checkout" class="btn btn-chamcong btn-checkout">Check Out</button>
        <?php else: ?>
            <button type="button" class="btn btn-chamcong btn-done" disabled>Đã chấm công xong</button>
        <?php endif; ?>
    </form>
    <div class="mt-4">
        <h5>Trạng thái hôm nay:</h5>
        <ul class="list-unstyled">
            <li>Check In: <b><?php echo $cc['check_in'] ?? '-'; ?></b></li>
            <li>Check Out: <b><?php echo $cc['check_out'] ?? '-'; ?></b></li>
        </ul>
    </div>
    <hr class="my-4">
    <h4>Lịch sử làm việc</h4>
    <form class="row g-2 justify-content-center mb-3" method="get">
        <div class="col-auto">
            <select name="month" class="form-select">
                <?php for($m=1;$m<=12;$m++): ?>
                <option value="<?php echo $m; ?>" <?php if($m==$filter_month) echo 'selected'; ?>>Tháng <?php echo $m; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <select name="year" class="form-select">
                <?php for($y=date('Y')-2;$y<=date('Y')+1;$y++): ?>
                <option value="<?php echo $y; ?>" <?php if($y==$filter_year) echo 'selected'; ?>>Năm <?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>
    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>Ngày</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Trạng thái</th>
                <th>Tổng thời gian</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($lichsu as $row): 
            $tg = '';
            if ($row['check_in'] && $row['check_out']) {
                $in = strtotime($row['check_in']);
                $out = strtotime($row['check_out']);
                if ($out > $in) {
                    $h = floor(($out-$in)/3600);
                    $m = floor((($out-$in)%3600)/60);
                    $tg = sprintf('%02d:%02d', $h, $m);
                }
            }
        ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($row['ngay_cham_cong'])); ?></td>
                <td><?php echo $row['check_in'] ?: '-'; ?></td>
                <td><?php echo $row['check_out'] ?: '-'; ?></td>
                <td><?php echo $row['tt'] ?: '-'; ?></td>
                <td><?php echo $tg ?: '-'; ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (count($lichsu) == 0): ?>
            <tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Tổng thời gian đã làm trong tháng:</th>
                <th><?php echo $tong_gio; ?></th>
            </tr>
        </tfoot>
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