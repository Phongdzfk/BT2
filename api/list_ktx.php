<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

$sql = "SELECT ma_ktx, ten_ktx FROM ky_tuc_xa ORDER BY ten_ktx ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$ktx = $stmt->fetchAll();
echo json_encode(['success' => true, 'data' => $ktx]); 