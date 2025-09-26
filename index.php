<?php
  session_start();
  if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
  }
  $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
  $ten = isset($_SESSION['ten']) ? $_SESSION['ten'] : $_SESSION['username'];
  $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>TRANG CHỦ</title>
    <link rel="stylesheet" href="css/indexcss.css" />
  </head>
  <body>
    <div class="container">
    <div class="container_menu mb-4"> 
        <div class="container_item">
          <a href="#">Quản lý thông tin nhân viên</a>
          <div class="list_items">
            <a class="item" href="danhsachnv.php">Xem danh sách nhân viên</a>
            <a class="item" href="them_nv.php">Thêm nhân viên mới</a>
            <a class="item" href="timkiem_nv.php">Tìm kiếm, lọc nhân viên theo phòng ban</a>
          </div>
        </div>
        <div class="container_item">
          <a href="#">Quản lý tuyển dụng</a>
          <div class="list_items">
            <a class="item" href="ds_ungvien.php">Xem danh sách ứng viên</a>
            <a class="item" href="them_ungvien.php">Thêm hồ sơ ứng tuyển</a>
            <a class="item lock" href="#">Lên lịch phỏng vấn</a>
            <a class="item" href="tim_kiem_uv.php">Tìm kiếm ứng viên ứng tuyển</a>
          </div>
        </div>
        <div class="container_item">
          <a href="#">Chấm công và quản lý thời gian làm việc</a>
          <div class="list_items">
            <a class="item" href="xem_bang_cham_cong.php">Xem bảng chấm công</a>
            <a class="item" href="them_sua_chamcong.php">Thêm/sửa dữ liệu chấm công</a>
            <a class="item" href="chamcong.php">Chấm công</a>
            <a class="item" href="tinhcong.php">Tính công</a>
          </div>
        </div>
        <div class="container_item">
          <a href="#" class = "">Quản lý tài liệu và báo cáo</a>
          <div class="list_items">
            <a class="item " href="guitb.php">Gửi thông báo - tài liệu</a>
            <a class="item " href="ds_thongbao.php">Danh sách thông báo -tài liệu</a>
            <a class="item " href="ds_phanhoi.php">Danh sách  phản hồi</a>
          </div>
        </div>
        <div class="container_item">
          <a href="#">Phân quyền và bảo mật</a>
          <div class="list_items">
            <a class="item" href="taotk.php">Tạo tài khoản người dùng</a>
            <a class="item" href="danhsachusers.php">Cấp quyền, vai trò</a>
            <a class="item" href="doimk.php">Đổi mật khẩu</a>
            <a class="item" href="logout.php">Đăng xuất</a>
            <a class="item" href="#">Lịch sử đăng nhập hệ thống</a>
          </div>
        </div>
        <div class="container_item dropdown-left">
          <a href="#" class  = "lock">Đào tạo và phát triển</a>
          <div class="list_items ">
            <a class="item lock" href="#">Quản lí khóa đào tạo</a>
            <a class="item lock" href="#">Đăng kí tham gia đào tạo</a>
            <a class="item lock" href="#">Ghi nhận kết quả sau đào tạo</a>
            <a class="item lock" href="#">Lịch sử đào tạo nội bộ</a>
            <a class="item lock" href="#">Xem tiến độ phát triển kĩ năng của nhân viên</a>
          </div>
        </div>
      </div>
      <!-- Banner sinh động -->
      <div class="header-banner">
        <img src="image/th.jpg" alt="Ảnh công ty" id="main-banner-img">
        <div class="welcome">
          <h1 class="fw-bold text-primary mb-2">Chào mừng đến với hệ thống quản lý nhân sự</h1>
          <p class="mb-0 text-secondary">Quản lý nhân viên, tuyển dụng, lương thưởng, chấm công và nhiều hơn nữa.<br>Hãy chọn chức năng bạn muốn sử dụng bên dưới!</p>
        </div>
      </div>
      <div class="quick-links">
        <a class="quick-link-item" href="danhsachnv.php" title="Xem danh sách nhân viên">
          <svg fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
          Danh sách nhân viên
        </a>
        <a class="quick-link-item" href="them_nv.php" title="Thêm nhân viên mới">
          <svg fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5V7h2.5a.5.5 0 0 1 0 1H8.5v2.5a.5.5 0 0 1-1 0V8H5a.5.5 0 0 1 0-1h2.5V4.5A.5.5 0 0 1 8 4z"/></svg>
          Thêm nhân viên
        </a>
        <a class="quick-link-item" href="timkiem_nv.php" title="Tìm kiếm nhân viên">
          <svg fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a5 5 0 1 1-2-4 5 5 0 0 1 2 4zm-1 0a4 4 0 1 0-2 3.464A4 4 0 0 0 10 6z"/><path d="M15.854 15.146a.5.5 0 0 1-.708 0l-3.182-3.182A6.978 6.978 0 0 1 8 14a7 7 0 1 1 7-7 6.978 6.978 0 0 1-1.146 3.964l3.182 3.182a.5.5 0 0 1 0 .708z"/></svg>
          Tìm kiếm nhân viên
        </a>
      </div>
      
      <!-- Slide trình chiếu và thông tin tài khoản đặt dưới taskbar -->
      <div class="custom-slider-row">
        <div class="custom-slider-col position-relative">
          <button class="slider-arrow slider-arrow-left" onclick="prevSlide()" aria-label="Trước">
            &#10094;
          </button>
          <a id="slider-link-0" href="#" target="_blank" style="display:block;">
            <img id="slider-img-0" class="custom-slider-img" src="image/slide1.jpg" alt="Ảnh 1">
          </a>
          <a id="slider-link-1" href="#" target="_blank" style="display:none;">
            <img id="slider-img-1" class="custom-slider-img" src="image/slide2.jpg" alt="Ảnh 2">
          </a>
          <a id="slider-link-2" href="#" target="_blank" style="display:none;">
            <img id="slider-img-2" class="custom-slider-img" src="image/slide3.jpg" alt="Ảnh 3">
          </a>
          <button class="slider-arrow slider-arrow-right" onclick="nextSlide()" aria-label="Sau">
            &#10095;
          </button>
        </div>
        <div class="custom-info-col">
          <h2>Xin chào <?php echo htmlspecialchars($role); ?>!</h2>
          <div class="info-label">Tên đăng nhập:</div>
          <div class="info-value"><?php echo htmlspecialchars($ten); ?></div>
          <div class="info-label">Email:</div>
          <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
          <div class="info-label">Quyền truy cập:</div>
          <div class="info-value"><?php echo htmlspecialchars($role); ?></div>
        </div>
      </div>
    </div>
    <script>
      const totalSlides = 3;
      let currentSlide = 0;
      let slideInterval = setInterval(nextSlide, 5000);

      function showSlide(idx) {
        for (let i = 0; i < totalSlides; i++) {
          document.getElementById('slider-link-' + i).style.display = (i === idx) ? 'block' : 'none';
        }
        currentSlide = idx;
        resetInterval();
      }

      function nextSlide() {
        let next = (currentSlide + 1) % totalSlides;
        showSlide(next);
      }

      function prevSlide() {
        let prev = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prev);
      }

      function resetInterval() {
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
      }
      const slideData = [
        {src: "image/pt1.jpg", href: "https://www.wikihow.vn/Tr%E1%BB%9F-th%C3%A0nh-m%E1%BB%99t-qu%E1%BA%A3n-l%C3%BD-gi%E1%BB%8Fi", alt: "Ảnh 1"},
        {src: "image/pt2.jpg", href: "them_ungvien.php", alt: "Ảnh 2"},
        {src: "image/pt3.jpg", href: "https://www.facebook.com/profile.php?id=100053400101515", alt: "Ảnh 3"}
      ];
      for (let i = 0; i < totalSlides; i++) {
        document.getElementById('slider-img-' + i).src = slideData[i].src;
        document.getElementById('slider-img-' + i).alt = slideData[i].alt;
        document.getElementById('slider-link-' + i).href = slideData[i].href;
      }
    </script>
  </body>
</html>