<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$ma_sinh_vien = $_POST['ma_sinh_vien'] ?? '';
$ma_phong = $_POST['ma_phong'] ?? '';
$loai_yeu_cau = $_POST['loai_yeu_cau'] ?? '';
$mo_ta = $_POST['mo_ta'] ?? '';
if(!$ma_sinh_vien || !$ma_phong || !$loai_yeu_cau || !$mo_ta) {
    echo json_encode(['success'=>false,'message'=>'Vui lòng nhập đầy đủ thông tin!']); exit;
}
$stmt = $conn->prepare("INSERT INTO yeu_cau (ma_sinh_vien, ma_phong, loai_yeu_cau, mo_ta) VALUES (?, ?, ?, ?)");
$stmt->execute([$ma_sinh_vien, $ma_phong, $loai_yeu_cau, $mo_ta]);
echo json_encode(['success'=>true,'message'=>'Thêm yêu cầu thành công!']); 