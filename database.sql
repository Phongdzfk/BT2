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
