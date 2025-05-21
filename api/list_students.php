<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$ma_phong = isset($_GET['ma_phong']) ? $_GET['ma_phong'] : '';
$ma_so_sinh_vien = isset($_GET['ma_so_sinh_vien']) ? $_GET['ma_so_sinh_vien'] : '';
$sql = "SELECT sv.*, nd.ho_ten, nd.email, nd.so_dien_thoai, p.so_phong FROM sinh_vien sv
        LEFT JOIN nguoi_dung nd ON sv.ma_nguoi_dung = nd.ma_nguoi_dung
        LEFT JOIN phong p ON sv.ma_phong = p.ma_phong WHERE 1";
$params = [];
if ($ma_phong !== '') {
    $sql .= " AND sv.ma_phong = :ma_phong";
    $params[':ma_phong'] = $ma_phong;
}
if ($ma_so_sinh_vien !== '') {
    $sql .= " AND sv.ma_so_sinh_vien LIKE :mssv";
    $params[':mssv'] = "%$ma_so_sinh_vien%";
}
$sql .= " ORDER BY sv.ma_sinh_vien DESC";
$stmt = $conn->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success' => true, 'data' => $data]); 