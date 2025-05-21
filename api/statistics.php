<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

// Thống kê phòng
$stmt = $conn->query("SELECT 
    COUNT(*) as total_rooms,
    SUM(CASE WHEN trang_thai = 'còn trống' THEN 1 ELSE 0 END) as available_rooms,
    SUM(CASE WHEN trang_thai = 'đã đầy' THEN 1 ELSE 0 END) as occupied_rooms,
    SUM(CASE WHEN trang_thai = 'bảo trì' THEN 1 ELSE 0 END) as maintenance_rooms
FROM phong");
$room_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Thống kê sinh viên
$stmt = $conn->query("SELECT 
    COUNT(*) as total_students,
    SUM(CASE WHEN ngay_ra > CURDATE() THEN 1 ELSE 0 END) as active_students,
    SUM(CASE WHEN ngay_ra BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as expiring_soon
FROM sinh_vien");
$student_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Thống kê doanh thu
$stmt = $conn->query("SELECT 
    SUM(so_tien) as total_revenue,
    SUM(CASE WHEN trang_thai = 'đã thanh toán' THEN so_tien ELSE 0 END) as paid_revenue,
    SUM(CASE WHEN trang_thai = 'chưa thanh toán' THEN so_tien ELSE 0 END) as unpaid_revenue
FROM hoa_don");
$revenue_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Thống kê yêu cầu
$stmt = $conn->query("SELECT 
    COUNT(*) as total_requests,
    SUM(CASE WHEN trang_thai = 'chờ xử lý' THEN 1 ELSE 0 END) as pending_requests,
    SUM(CASE WHEN trang_thai = 'đang giải quyết' THEN 1 ELSE 0 END) as processing_requests,
    SUM(CASE WHEN trang_thai = 'đã giải quyết' THEN 1 ELSE 0 END) as resolved_requests
FROM yeu_cau");
$request_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Thống kê theo loại yêu cầu
$stmt = $conn->query("SELECT 
    loai_yeu_cau,
    COUNT(*) as count
FROM yeu_cau
GROUP BY loai_yeu_cau");
$request_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê doanh thu theo tháng (6 tháng gần nhất)
$stmt = $conn->query("SELECT 
    DATE_FORMAT(ngay_tao, '%Y-%m') as month,
    SUM(so_tien) as revenue,
    SUM(CASE WHEN trang_thai = 'chưa thanh toán' THEN so_tien ELSE 0 END) as unpaid_revenue
FROM hoa_don
WHERE ngay_tao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(ngay_tao, '%Y-%m')
ORDER BY month");
$monthly_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => [
        'rooms' => $room_stats,
        'students' => $student_stats,
        'revenue' => $revenue_stats,
        'requests' => $request_stats,
        'request_types' => $request_types,
        'monthly_revenue' => $monthly_revenue
    ]
]); 