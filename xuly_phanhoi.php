<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['phanhoi_id'], $_POST['phanhoi_admin'])) {
        try {
            $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("UPDATE phanhoi SET phanhoi_admin = :phanhoi_admin, trang_thai = 'Đã phản hồi' WHERE id = :id");
            $stmt->execute([
                ':phanhoi_admin' => $_POST['phanhoi_admin'],
                ':id' => $_POST['phanhoi_id']
            ]);

            header("Location: ds_phanhoi.php");
            exit();
        } catch(PDOException $e) {
            die("Lỗi cập nhật phản hồi: " . $e->getMessage());
        }
    }
}
?>
