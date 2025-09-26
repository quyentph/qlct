-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 30, 2025 lúc 07:39 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: ` qlnv`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamcong`
--

CREATE TABLE `chamcong` (
  `id` int(11) NOT NULL,
  `nhanvien_id` int(11) DEFAULT NULL,
  `ngay_cham_cong` date DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `tt` enum('Đi làm','Nghỉ phép','Nghỉ không phép','Làm online') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chamcong`
--

INSERT INTO `chamcong` (`id`, `nhanvien_id`, `ngay_cham_cong`, `check_in`, `check_out`, `tt`) VALUES
(6, 6, '2025-04-28', '08:00:00', '17:00:00', 'Đi làm'),
(7, 7, '2025-04-28', '08:15:00', '17:05:00', 'Đi làm'),
(8, 8, '2025-04-28', NULL, NULL, 'Nghỉ phép'),
(9, 9, '2025-04-28', '09:00:00', '16:00:00', 'Làm online'),
(10, 10, '2025-04-28', NULL, NULL, 'Nghỉ không phép');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `id` int(11) NOT NULL,
  `tennv` varchar(100) DEFAULT NULL,
  `gioitinh` enum('Nam','Nữ') DEFAULT NULL,
  `ngaysinh` date DEFAULT NULL,
  `dienthoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `diachi` text DEFAULT NULL,
  `phongban_id` int(11) DEFAULT NULL,
  `vitri_id` int(11) DEFAULT NULL,
  `ngay_vao_lam` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`id`, `tennv`, `gioitinh`, `ngaysinh`, `dienthoai`, `email`, `diachi`, `phongban_id`, `vitri_id`, `ngay_vao_lam`) VALUES
(6, 'Nguyễn Văn A', 'Nam', '1990-01-15', '0901234567', 'a@example.com', 'Hà Nội', 6, 2, '2015-05-10'),
(7, 'Trần Thị B', 'Nữ', '1992-03-20', '0912345678', 'b@example.com', 'TP.HCM', 7, 3, '2017-06-12'),
(8, 'Lê Văn C', 'Nam', '1985-07-10', '0923456789', 'c@example.com', 'Đà Nẵng', 3, 5, '2018-09-01'),
(9, 'Phạm Thị D', 'Nữ', '1995-12-01', '0934567890', 'd@example.com', 'Cần Thơ', 4, 4, '2023-01-10'),
(10, 'Đỗ Mạnh E', 'Nam', '1988-11-25', '0945678901', 'e@example.com', 'Hải Phòng', 5, 1, '2010-03-15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phongban`
--

CREATE TABLE `phongban` (
  `id` int(11) NOT NULL,
  `tenpb` varchar(255) NOT NULL,
  `mota` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phongban`
--

INSERT INTO `phongban` (`id`, `tenpb`, `mota`) VALUES
(3, 'Phòng Nhân sự', 'Quản lý nhân sự, tuyển dụng và đào tạo'),
(4, 'Phòng Kế toán', 'Quản lý tài chính và kế toán'),
(5, 'Phòng Kỹ thuật', 'Phát triển và bảo trì hệ thống'),
(6, 'Phòng Marketing', 'Chiến lược và quảng bá sản phẩm'),
(7, 'Phòng Hành chính', 'Quản lý văn phòng và hành chính');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nhanvien_id` int(11) DEFAULT NULL,
  `role` enum('admin','quanli','nhanvien') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nhanvien_id`, `role`) VALUES
(1, 'admin01', '202cb962ac59075b964b07152d234b70', 6, 'admin'),
(2, 'manager01', '202cb962ac59075b964b07152d234b70', 7, 'quanli'),
(3, 'user01', '202cb962ac59075b964b07152d234b70', 8, 'nhanvien'),
(4, 'user02', '202cb962ac59075b964b07152d234b70', 9, 'nhanvien'),
(5, 'user03', '202cb962ac59075b964b07152d234b70', 10, 'nhanvien');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vitri`
--

CREATE TABLE `vitri` (
  `id` int(11) NOT NULL,
  `tenvt` varchar(100) DEFAULT NULL,
  `luong_cb` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vitri`
--

INSERT INTO `vitri` (`id`, `tenvt`, `luong_cb`) VALUES
(1, 'Giám đốc', 30000000.00),
(2, 'Trưởng phòng', 20000000.00),
(3, 'Nhân viên chính thức', 12000000.00),
(4, 'Thực tập sinh', 5000000.00),
(5, 'Kỹ sư phần mềm', 15000000.00);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phongban_id` (`phongban_id`),
  ADD KEY `vitri_id` (`vitri_id`);

--
-- Chỉ mục cho bảng `phongban`
--
ALTER TABLE `phongban`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `vitri`
--
ALTER TABLE `vitri`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `phongban`
--
ALTER TABLE `phongban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `vitri`
--
ALTER TABLE `vitri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD CONSTRAINT `chamcong_ibfk_1` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`);

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`phongban_id`) REFERENCES `phongban` (`id`),
  ADD CONSTRAINT `nhanvien_ibfk_2` FOREIGN KEY (`vitri_id`) REFERENCES `vitri` (`id`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
