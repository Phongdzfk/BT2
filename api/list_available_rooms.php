<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

try {
    // Lấy danh sách phòng trống
    $stmt = $conn->query("SELECT p.*, k.ten_ktx 
                         FROM phong p 
                         JOIN ky_tuc_xa k ON p.ma_ktx = k.ma_ktx 
                         WHERE p.trang_thai = 'còn trống'
                         ORDER BY k.ten_ktx, p.so_phong");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $rooms
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy danh sách phòng: ' . $e->getMessage()
    ]);
} 