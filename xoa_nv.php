<?php
$conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    if ($id > 0) {
        $tables = ['nv_tb', 'users', 'chamcong', 'phanhoi']; // cập nhật theo tên bảng bạn có
        foreach ($tables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE nhanvien_id = :id");
            $stmt->bindParam(':id', $id, PDO:       :PARAM_INT);
            $stmt->execute();
        }
        $stmt = $conn->prepare("DELETE FROM nhanvien WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header("Location: danhsachnv.php");
exit();
?>
