CREATE TABLE ky_tuc_xa (
    ma_ktx INT AUTO_INCREMENT PRIMARY KEY,
    ten_ktx NVARCHAR(100) NOT NULL,
    dia_chi NVARCHAR(255),
    ten_quan_ly NVARCHAR(100),
    so_dien_thoai NVARCHAR(20)
);

CREATE TABLE phong (
    ma_phong INT AUTO_INCREMENT PRIMARY KEY,
    ma_ktx INT,
    so_phong VARCHAR(20) NOT NULL,
    floors INT, -- tầng
    suc_chua INT NOT NULL,
    so_nguoi_hien_tai INT DEFAULT 0,
    trang_thai ENUM('còn trống', 'đã đầy', 'bảo trì') DEFAULT 'còn trống',
    anh_phong VARCHAR(255),
    FOREIGN KEY (ma_ktx) REFERENCES ky_tuc_xa(ma_ktx)
);

CREATE TABLE nguoi_dung (
    ma_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap NVARCHAR(50) NOT NULL UNIQUE,
    mat_khau NVARCHAR(255) NOT NULL,
    ho_ten NVARCHAR(100),
    email NVARCHAR(100),
    so_dien_thoai NVARCHAR(20),
    vai_tro ENUM('quản trị viên', 'sinh viên') DEFAULT 'sinh viên',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sinh_vien (
    ma_sinh_vien INT AUTO_INCREMENT PRIMARY KEY,
    ma_nguoi_dung INT,
    ma_so_sinh_vien NVARCHAR(20) NOT NULL UNIQUE,
    ma_phong INT,
    ngay_sinh DATE,
    gioi_tinh ENUM('nam', 'nữ', 'khác'),
    ngay_vao DATE,
    ngay_ra DATE,
    anh_sinh_vien NVARCHAR(255),
    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung),
    FOREIGN KEY (ma_phong) REFERENCES phong(ma_phong)
);

CREATE TABLE yeu_cau (
    ma_yeu_cau INT AUTO_INCREMENT PRIMARY KEY,
    ma_sinh_vien INT,
    ma_phong INT,
    loai_yeu_cau NVARCHAR(100),
    mo_ta TEXT,
    trang_thai ENUM('chờ xử lý', 'đang xử lý', 'đã giải quyết', 'từ chối') DEFAULT 'chờ xử lý',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NULL,
    FOREIGN KEY (ma_sinh_vien) REFERENCES sinh_vien(ma_sinh_vien),
    FOREIGN KEY (ma_phong) REFERENCES phong(ma_phong)
);

CREATE TABLE hoa_don (
    ma_hoa_don INT AUTO_INCREMENT PRIMARY KEY,
    ma_sinh_vien INT,
    so_tien DECIMAL(10,2) NOT NULL,
    noi_dung NVARCHAR(255),
    han_thanh_toan DATE,
    ngay_thanh_toan DATE,
    trang_thai ENUM('chưa thanh toán', 'đã thanh toán', 'quá hạn') DEFAULT 'chưa thanh toán',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_sinh_vien) REFERENCES sinh_vien(ma_sinh_vien)
);

INSERT INTO ky_tuc_xa (ten_ktx, dia_chi, ten_quan_ly, so_dien_thoai) VALUES
('KTX A', '123 Đường A, Quận 1, TP.HCM', 'Nguyễn Văn Quản', '0901234567'),
('KTX B', '456 Đường B, Quận 2, TP.HCM', 'Trần Thị Quản', '0912345678');

INSERT INTO phong (ma_ktx, so_phong, suc_chua, so_nguoi_hien_tai, trang_thai, anh_phong, floors) VALUES
(1, '101', 4, 4, 'đã đầy', 'room101-1.jpeg',1),
(1, '102', 4, 2, 'còn trống', 'room101-2.jpeg',1),
(2, '201', 6, 6, 'đã đầy', 'room101-3.jpeg',2),
(2, '202', 6, 0, 'bảo trì', 'room-default1.jpeg',2);

INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, ho_ten, email, so_dien_thoai, vai_tro)
VALUES
('admin', '$10$t.JNUgWuKU7RFAKLn.ExVuAzd9zxfLbk9mtkjsPoiWVvnTvm8kIqm', 'Quản Trị Viên', 'admin@ktx.vn', '0901234567', 'quản trị viên'),
('sv01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'sinhvien1@ktx.vn', '0911111111', 'sinh viên'),
('sv02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 'sinhvien2@ktx.vn', '0922222222', 'sinh viên');

INSERT INTO sinh_vien (ma_nguoi_dung, ma_so_sinh_vien, ma_phong, ngay_sinh, gioi_tinh, ngay_vao, ngay_ra, anh_sinh_vien)
VALUES
(2, '20210001', 1, '2002-05-10', 'nam', '2023-09-01', NULL, 'student1.png'),
(3, '20210002', 2, '2001-09-15', 'nữ', '2023-09-01', NULL, 'student2.jpg');

INSERT INTO yeu_cau (ma_sinh_vien, ma_phong, loai_yeu_cau, mo_ta, trang_thai, ngay_tao, ngay_cap_nhat)
VALUES
(1, 1, 'Sửa chữa', 'Sửa bóng đèn phòng 101', 'chờ xử lý', NOW(), NULL),
(2, 2, 'Vệ sinh', 'Vệ sinh phòng 102', 'đang xử lý', NOW(), NOW());

INSERT INTO hoa_don (ma_sinh_vien, so_tien, noi_dung, han_thanh_toan, ngay_thanh_toan, trang_thai, ngay_tao)
VALUES
(1, 1200000, 'Tiền phòng tháng 5', '2024-05-10', NULL, 'chưa thanh toán', NOW()),
(2, 1200000, 'Tiền phòng tháng 5', '2024-05-10', '2024-05-05', 'đã thanh toán', NOW());

SET FOREIGN_KEY_CHECKS = 0;
truncate table ky_tuc_xa;
SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE phong
ADD floors INT;


UPDATE phong
SET ma_ktx = '2'
WHERE ma_phong = 4;