<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$id = $_GET['ma_yeu_cau'] ?? 0;
$stmt = $conn->prepare("SELECT yc.*, sv.ma_so_sinh_vien, nd.ho_ten, p.so_phong FROM yeu_cau yc LEFT JOIN sinh_vien sv ON yc.ma_sinh_vien = sv.ma_sinh_vien LEFT JOIN nguoi_dung nd ON sv.ma_nguoi_dung = nd.ma_nguoi_dung LEFT JOIN phong p ON yc.ma_phong = p.ma_phong WHERE yc.ma_yeu_cau = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['success'=>!!$data, 'data'=>$data]); 