<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
function uploadImage($file, $dir = '../images/') {
    if(isset($file) && $file['error'] == 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('sv_').'.'.$ext;
        move_uploaded_file($file['tmp_name'], $dir.$filename);
        return $filename;
    }
    return null;
}
$ten_dang_nhap = $_POST['ten_dang_nhap'] ?? '';
$mat_khau = $_POST['mat_khau'] ?? '';
$ho_ten = $_POST['ho_ten'] ?? '';
$email = $_POST['email'] ?? '';
$so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
$ma_so_sinh_vien = $_POST['ma_so_sinh_vien'] ?? '';
$ngay_sinh = $_POST['ngay_sinh'] ?? '';
$gioi_tinh = $_POST['gioi_tinh'] ?? '';
$ngay_vao = $_POST['ngay_vao'] ?? '';
$ngay_ra = $_POST['ngay_ra'] ?? null;
$ma_phong = $_POST['ma_phong'] ?? '';
$anh_sinh_vien = isset($_FILES['anh_sinh_vien']) ? uploadImage($_FILES['anh_sinh_vien']) : null;
if(!$ten_dang_nhap || !$mat_khau || !$ho_ten || !$email || !$ma_so_sinh_vien || !$ngay_sinh || !$gioi_tinh || !$ngay_vao || !$ma_phong) {
    echo json_encode(['success'=>false,'message'=>'Vui lòng nhập đầy đủ thông tin!']);
    exit;
}
// Check username or mssv exists
$stmt = $conn->prepare("SELECT 1 FROM nguoi_dung WHERE ten_dang_nhap = ?");
$stmt->execute([$ten_dang_nhap]);
if($stmt->fetch()) {
    echo json_encode(['success'=>false,'message'=>'Tên đăng nhập đã tồn tại!']); exit;
}
$stmt = $conn->prepare("SELECT 1 FROM sinh_vien WHERE ma_so_sinh_vien = ?");
$stmt->execute([$ma_so_sinh_vien]);
if($stmt->fetch()) {
    echo json_encode(['success'=>false,'message'=>'Mã số sinh viên đã tồn tại!']); exit;
}
// Insert user
$hash = password_hash($mat_khau, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, ho_ten, email, so_dien_thoai, vai_tro) VALUES (?, ?, ?, ?, ?, 'sinh viên')");
$stmt->execute([$ten_dang_nhap, $hash, $ho_ten, $email, $so_dien_thoai]);
$ma_nguoi_dung = $conn->lastInsertId();
// Insert sinh vien
$stmt = $conn->prepare("INSERT INTO sinh_vien (ma_nguoi_dung, ma_so_sinh_vien, ma_phong, ngay_sinh, gioi_tinh, ngay_vao, ngay_ra, anh_sinh_vien) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$ma_nguoi_dung, $ma_so_sinh_vien, $ma_phong, $ngay_sinh, $gioi_tinh, $ngay_vao, $ngay_ra, $anh_sinh_vien]);
// Tăng số người hiện tại của phòng
$stmt = $conn->prepare("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai + 1 WHERE ma_phong = ?");
$stmt->execute([$ma_phong]);
// Nếu phòng đã đầy thì cập nhật trạng thái
$stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
$stmt->execute([$ma_phong]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);
if($room && $room['so_nguoi_hien_tai'] >= $room['suc_chua']) {
    $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'đã đầy' WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
}
echo json_encode(['success'=>true,'message'=>'Thêm sinh viên thành công!']); 