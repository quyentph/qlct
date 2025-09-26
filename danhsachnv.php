<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Kết nối CSDL
$conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lấy dữ liệu nhân viên, join với phòng ban
$sql = "SELECT nv.id, nv.tennv, nv.phongban_id, phongban.tenpb,anh,gioitinh
        FROM nhanvien nv
        INNER JOIN phongban ON nv.phongban_id = phongban.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nhanviens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm định dạng đường dẫn ảnh nhân viên
function getEmployeeImage($imagePath,$gioitinh) {
    
    if (empty($imagePath)) {
        if($gioitinh == 'Nữ'){
        return "image/avt_nu.jpg"; // Đường dẫn ảnh mặc định nếu không có ảnh
        }
        else{
             return "image/avt_nam.png"; // Đường dẫn ảnh mặc định nếu không có ảnh
        }
       
    }
    
    return $imagePath;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách nhân viên</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/danhsachnv.css">
    <style>
        .card img {
            height: 180px;
            object-fit: cover;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const cards = document.querySelectorAll(".card");

            cards.forEach(card => {
                card.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");
                    window.location.href = "chitietnv.php?id=" + id;
                });
            });
        });
        </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("search");
            const cards = document.querySelectorAll(".card");

            searchInput.addEventListener("input", function () {
                const query = searchInput.value.toLowerCase();

                cards.forEach(card => {
                    const name = card.querySelector(".card-title").textContent.toLowerCase();
                    const id = card.querySelector(".card-text strong").textContent.toLowerCase();
                    const department = card.querySelectorAll(".card-text")[1].textContent.toLowerCase();

                    if (name.includes(query) || id.includes(query) || department.includes(query)) {
                        card.parentElement.style.display = "block";
                    } else {
                        card.parentElement.style.display = "none";
                    }
                });
            });
        });
    </script>
</head>
<body style="background:rgb(183, 219, 255);">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger fw-bold mb-0">Danh sách nhân viên</h2>
            <input type="text" id="search" class="form-control w-25" placeholder="Tìm kiếm nhân viên...">
            <a href="index.php" class="btn btn-primary">Trang chủ</a>
        </div>
        <div class="row">
            <?php foreach ($nhanviens as $nv): ?>
            <div class="col-md-5 col-lg-4 col-xl-2 mb-3">
                <div class="card shadow-sm" data-id="<?php echo htmlspecialchars($nv['id']); ?>">
                    <img src="<?php echo getEmployeeImage($nv['anh'],$nv['gioitinh']); ?>" class="card-img-top" alt="Ảnh nhân viên">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($nv['tennv']); ?></h5>
                        <p class="card-text"><strong>ID:</strong> <?php echo htmlspecialchars($nv['id']); ?></p>
                        <p class="card-text"><strong>Phòng ban:</strong> <?php echo htmlspecialchars($nv['tenpb']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?> 
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>