<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$id = $_POST['ma_yeu_cau'] ?? 0;
$stmt = $conn->prepare("DELETE FROM yeu_cau WHERE ma_yeu_cau = ?");
$stmt->execute([$id]);
echo json_encode(['success'=>true,'message'=>'Đã xóa yêu cầu!']); 