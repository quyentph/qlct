<?php
    session_start();
    if(!isset($_SESSION['nhanvien_id'])) {
        header("Location: login.php");
        exit();
    }
    try{
        $conn = new PDO("mysql:host=localhost;dbname= qlnv",'root','');
        $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e){
        echo "Kết nối thất bại: ".$e->getMessage();
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>đƠn xin nghỉ phép</title>
</head>
<body>
    <div>
        <div>
            
        </div>
    </div>
</body>
</html>