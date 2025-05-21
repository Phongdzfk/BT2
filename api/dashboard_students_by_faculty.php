<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

try {
    // Lấy 2 ký tự sau chữ B trong mã sinh viên, group by khóa
    $stmt = $conn->query("SELECT SUBSTRING(ma_so_sinh_vien, 2, 2) AS khoa, COUNT(*) as total FROM sinh_vien GROUP BY khoa ORDER BY khoa DESC");
    $labels = [];
    $values = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = 'Khóa ' . $row['khoa'];
        $values[] = (int)$row['total'];
    }
    $response = [
        'success' => true,
        'data' => [
            'labels' => $labels,
            'values' => $values
        ]
    ];
} catch(Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}
echo json_encode($response);