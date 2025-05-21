<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');
$trang_thai = $_GET['trang_thai'] ?? '';
$loai_yeu_cau = $_GET['loai_yeu_cau'] ?? '';
$search = $_GET['search'] ?? '';
$sql = "SELECT yc.*, sv.ma_so_sinh_vien, nd.ho_ten, p.so_phong FROM yeu_cau yc
        LEFT JOIN sinh_vien sv ON yc.ma_sinh_vien = sv.ma_sinh_vien
        LEFT JOIN nguoi_dung nd ON sv.ma_nguoi_dung = nd.ma_nguoi_dung
        LEFT JOIN phong p ON yc.ma_phong = p.ma_phong WHERE 1";
$params = [];
if ($trang_thai !== '') {
    $sql .= " AND yc.trang_thai = :trang_thai";
    $params[':trang_thai'] = $trang_thai;
}
if ($loai_yeu_cau !== '') {
    $sql .= " AND yc.loai_yeu_cau LIKE :loai_yeu_cau";
    $params[':loai_yeu_cau'] = "%$loai_yeu_cau%";
}
if ($search !== '') {
    $sql .= " AND (sv.ma_so_sinh_vien LIKE :search OR nd.ho_ten LIKE :search)";
    $params[':search'] = "%$search%";
}
$sql .= " ORDER BY yc.ma_yeu_cau DESC";
$stmt = $conn->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success' => true, 'data' => $data]); 