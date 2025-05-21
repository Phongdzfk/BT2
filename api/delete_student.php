<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$id = $_POST['ma_sinh_vien'] ?? 0;
// Lấy mã người dùng
$stmt = $conn->prepare("SELECT ma_nguoi_dung FROM sinh_vien WHERE ma_sinh_vien = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row) {
    echo json_encode(['success'=>false,'message'=>'Không tìm thấy sinh viên!']); exit;
}
$ma_nguoi_dung = $row['ma_nguoi_dung'];
// Xóa sinh viên
$stmt = $conn->prepare("DELETE FROM sinh_vien WHERE ma_sinh_vien = ?");
$stmt->execute([$id]);
// Xóa tài khoản
$stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE ma_nguoi_dung = ?");
$stmt->execute([$ma_nguoi_dung]);
echo json_encode(['success'=>true,'message'=>'Đã xóa sinh viên và tài khoản liên quan!']); 