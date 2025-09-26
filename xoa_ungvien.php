<?php
// Kết nối CSDL bằng PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy ID từ URL
$id = $_POST['id'] ?? 0;

if ($id > 0) {
    // Lấy đường dẫn CV nếu có để xóa file
    $stmt = $conn->prepare("SELECT link_cv FROM ungvien WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $ungvien = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ungvien) {
        // Xóa file CV nếu tồn tại
        if (!empty($ungvien['link_cv']) && file_exists($ungvien['link_cv'])) {
            unlink($ungvien['link_cv']);
        }

        // Xóa ứng viên khỏi CSDL
        $stmt = $conn->prepare("DELETE FROM ungvien WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Chuyển hướng hoặc hiển thị thông báo
        header("Location: ds_ungvien.php");
        exit();
    } else {
        echo "Không tìm thấy ứng viên.";
    }
} else {
    echo "ID không hợp lệ.";
}
?>
