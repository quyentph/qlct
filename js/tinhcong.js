// Hàm tính tổng số ngày công từ mảng dữ liệu JS
function tongNgayCong(list) {
  let count = 0;
  list.forEach((cc) => {
    if (cc.tt === "Đi làm" || cc.tt === "Làm online") count++;
  });
  return count;
}

// Hàm tính tổng giờ công (trả về chuỗi và số giờ thập phân)
function tongGioCong(list) {
  let total = 0;
  list.forEach((cc) => {
    if (cc.check_in && cc.check_out) {
      let inTime = toSeconds(cc.check_in);
      let outTime = toSeconds(cc.check_out);
      if (outTime > inTime) total += outTime - inTime;
    }
  });
  let h = Math.floor(total / 3600);
  let m = Math.floor((total % 3600) / 60);
  return {
    str: (h < 10 ? "0" : "") + h + ":" + (m < 10 ? "0" : "") + m,
    hours: h + m / 60,
  };
}

// Chuyển "HH:MM:SS" hoặc "HH:MM" thành giây
function toSeconds(timeStr) {
  let parts = timeStr.split(":").map(Number);
  return parts[0] * 3600 + (parts[1] || 0) * 60 + (parts[2] || 0);
}

// Hàm tính lương
function tinhLuong(luong_cb, tong_gio) {
  return Math.round(luong_cb * tong_gio);
}
