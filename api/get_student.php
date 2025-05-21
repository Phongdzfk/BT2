<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$id = $_GET['ma_sinh_vien'] ?? 0;
$stmt = $conn->prepare("SELECT sv.*, nd.ho_ten, nd.email, nd.so_dien_thoai FROM sinh_vien sv LEFT JOIN nguoi_dung nd ON sv.ma_nguoi_dung = nd.ma_nguoi_dung WHERE sv.ma_sinh_vien = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['success'=>!!$data, 'data'=>$data]); 