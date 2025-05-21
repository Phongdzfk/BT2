<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT trang_thai, COUNT(*) as total FROM phong GROUP BY trang_thai");
    $labels = [];
    $values = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['trang_thai'];
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