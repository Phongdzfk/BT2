<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT COUNT(*) FROM phong");
    $total_rooms = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM sinh_vien");
    $total_students = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM hoa_don");
    $total_bills = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM yeu_cau");
    $total_requests = $stmt->fetchColumn();

    $response = [
        'success' => true,
        'data' => [
            'total_rooms' => (int)$total_rooms,
            'total_students' => (int)$total_students,
            'total_bills' => (int)$total_bills,
            'total_requests' => (int)$total_requests
        ]
    ];
} catch(Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}
echo json_encode($response);