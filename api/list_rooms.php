<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

$floors = isset($_GET['floors']) ? $_GET['floors'] : '';
$status = isset($_GET['trang_thai']) ? $_GET['trang_thai'] : '';
$ma_ktx = isset($_GET['ma_ktx']) ? $_GET['ma_ktx'] : '';

$sql = "SELECT * FROM phong WHERE 1";
$params = [];
if ($floors !== '') {
    $sql .= " AND floors = :floors";
    $params[':floors'] = $floors;
}
if ($status !== '') {
    $sql .= " AND trang_thai = :trang_thai";
    $params[':trang_thai'] = $status;
}
if ($ma_ktx !== '') {
    $sql .= " AND ma_ktx = :ma_ktx";
    $params[':ma_ktx'] = $ma_ktx;
}
$sql .= " ORDER BY ma_phong DESC";
$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$rooms = $stmt->fetchAll();
echo json_encode(['success' => true, 'data' => $rooms]);