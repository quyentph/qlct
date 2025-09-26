<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'quanli'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname= qlnv", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Xoá thông báo khỏi bảng liên kết nv_tb trước
        $stmt = $conn->prepare("DELETE FROM nv_tb WHERE thongbao_id = ?");
        $stmt->execute([$id]);

        // Xoá thông báo chính
        $stmt = $conn->prepare("DELETE FROM thongbao WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ds_thongbao.php");
        exit();
    } catch(PDOException $e) {
        die("Lỗi: " . $e->getMessage());
    }
} else {
    header("Location: ds_thongbao.php");
    exit();
}
?>
