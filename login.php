<?php
    session_start();
    try {
        $conn = new PDO("mysql:host=localhost;dbname=qlnv", 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Kết nối thất bại: " . $e->getMessage();
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $stmt = $conn->prepare("SELECT users.*, nhanvien.email FROM users 
        INNER JOIN nhanvien ON users.nhanvien_id = nhanvien.id 
        WHERE users.username = :username AND users.password = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $query = $stmt->fetch(PDO::FETCH_ASSOC);
            if($query['role'] == 'admin' || $query['role'] == 'quanli') {
                $_SESSION['role'] = 'quanli';
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $query['email'];
                $_SESSION['nhanvien_id'] = $query['nhanvien_id'];
                $_SESSION['user_id'] = $query['id'];
                header("Location: index.php");
                exit();
            }
            else{
                $_SESSION['role'] = 'nhanvien';
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $query['email'];
                $_SESSION['nhanvien_id'] = $query['nhanvien_id'];
                $_SESSION['user_id'] = $query['id'];
                header("Location: index_nv.php");
                exit();
            }
        } else {
            echo "<script>alert('Tên đăng nhập hoặc mật khẩu không đúng!');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Đăng nhập</title>
</head>
<body>
    <div class="container">
        <div class="login-wrapper">
            <div class="card">
                <div class="card-header" style="text-align: center;">
                <div class="logo-title">
                    <img src="image/logo.png" alt="Logo">
                    <span class="system-title">Hệ thống quản lý nhân sự ver 1.0</span>
                </div>
                    <h3>Đăng nhập</h3>
                </div>
                <div class="card-body mr-3">
                    <form action="login.php" method="POST">
                        <div class="form-group mb-3 ">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <a href="forward.php">Quên mật khẩu</a>
                        </div>
                        <button type="submit" class="btn-login">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
