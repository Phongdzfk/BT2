<?php
require_once(__DIR__ . '/../config.php');

$response = array('success' => false, 'data' => array(), 'message' => '');

try {
    // Build query with filters
    $sql = "SELECT h.*, nd.ho_ten as ten_sinh_vien, p.so_phong as ten_phong 
            FROM hoa_don h
            JOIN sinh_vien s ON h.ma_sinh_vien = s.ma_sinh_vien
            JOIN nguoi_dung nd ON s.ma_nguoi_dung = nd.ma_nguoi_dung
            JOIN phong p ON s.ma_phong = p.ma_phong
            WHERE 1=1";
    $params = array();

    // Filter by status
    if (!empty($_GET['trang_thai'])) {
        $sql .= " AND h.trang_thai = ?";
        $params[] = $_GET['trang_thai'];
    }

    // Filter by mã số sinh viên
    if (!empty($_GET['ma_so_sinh_vien'])) {
        $sql .= " AND s.ma_so_sinh_vien LIKE ?";
        $params[] = '%' . $_GET['ma_so_sinh_vien'] . '%';
    }

    // Filter by room
    if (!empty($_GET['ma_phong'])) {
        $sql .= " AND s.ma_phong = ?";
        $params[] = $_GET['ma_phong'];
    }

    // Filter by date range
    if (!empty($_GET['tu_ngay'])) {
        $sql .= " AND h.ngay_tao >= ?";
        $params[] = $_GET['tu_ngay'];
    }
    if (!empty($_GET['den_ngay'])) {
        $sql .= " AND h.ngay_tao <= ?";
        $params[] = $_GET['den_ngay'];
    }

    // Order by creation date
    $sql .= " ORDER BY h.ngay_tao DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $bills;

} catch(Exception $e) {
    $response['message'] = "Lỗi: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 