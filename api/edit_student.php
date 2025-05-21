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
$id = $_POST['ma_sinh_vien'] ?? 0;
$ngay_sinh = $_POST['ngay_sinh'] ?? '';
$gioi_tinh = $_POST['gioi_tinh'] ?? '';
$ngay_vao = $_POST['ngay_vao'] ?? '';
$ngay_ra = $_POST['ngay_ra'] ?? null;
$ma_phong = $_POST['ma_phong'] ?? '';
$anh_sinh_vien_old = $_POST['anh_sinh_vien_old'] ?? '';
$new_image = uploadImage($_FILES['anh_sinh_vien']);
$anh_sinh_vien = $new_image ? $new_image : $anh_sinh_vien_old;
if(!$id || !$ngay_sinh || !$gioi_tinh || !$ngay_vao /*|| !$ma_phong*/ ) {
    echo json_encode(['success'=>false,'message'=>'Vui lòng nhập đầy đủ thông tin!']);
    exit;
}
$old_room = null;
// Lấy phòng cũ
$stmt = $conn->prepare("SELECT ma_phong FROM sinh_vien WHERE ma_sinh_vien = ?");
$stmt->execute([$id]);
$old_room = $stmt->fetchColumn();
if (!$ma_phong) {
    $ma_phong = $old_room;
}
$stmt = $conn->prepare("UPDATE sinh_vien SET ma_phong=?, ngay_sinh=?, gioi_tinh=?, ngay_vao=?, ngay_ra=?, anh_sinh_vien=? WHERE ma_sinh_vien=?");
$stmt->execute([$ma_phong, $ngay_sinh, $gioi_tinh, $ngay_vao, $ngay_ra, $anh_sinh_vien, $id]);
// Nếu đổi phòng
if($old_room && $old_room != $ma_phong) {
    // Giảm số người phòng cũ
    $stmt = $conn->prepare("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai - 1 WHERE ma_phong = ?");
    $stmt->execute([$old_room]);
    // Nếu phòng cũ đang đầy mà giảm thì chuyển trạng thái về còn trống
    $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
    $stmt->execute([$old_room]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if($room && $room['so_nguoi_hien_tai'] < $room['suc_chua']) {
        $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'còn trống' WHERE ma_phong = ?");
        $stmt->execute([$old_room]);
    }
    // Tăng số người phòng mới
    $stmt = $conn->prepare("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai + 1 WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    // Nếu phòng mới đầy thì cập nhật trạng thái
    $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if($room && $room['so_nguoi_hien_tai'] >= $room['suc_chua']) {
        $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'đã đầy' WHERE ma_phong = ?");
        $stmt->execute([$ma_phong]);
    }
}
echo json_encode(['success'=>true,'message'=>'Cập nhật thông tin sinh viên thành công!']); 