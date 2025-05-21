<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

try {
    $data = [];

    // 3 sinh viên mới nhất
    $stmt = $conn->query("SELECT s.ma_sinh_vien, nd.ho_ten, s.ngay_vao, p.so_phong FROM sinh_vien s JOIN nguoi_dung nd ON s.ma_nguoi_dung = nd.ma_nguoi_dung JOIN phong p ON s.ma_phong = p.ma_phong ORDER BY s.ngay_vao DESC LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'icon' => 'fas fa-user-plus',
            'icon_color' => 'text-success',
            'content' => "Sinh viên mới: {$row['ho_ten']} - Phòng {$row['so_phong']}",
            'time' => date('d/m/Y', strtotime($row['ngay_vao']))
        ];
    }

    // 3 yêu cầu mới nhất
    $stmt = $conn->query("SELECT loai_yeu_cau, ngay_tao, ma_phong FROM yeu_cau ORDER BY ngay_tao DESC LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'icon' => 'fas fa-tools',
            'icon_color' => 'text-warning',
            'content' => "Yêu cầu: {$row['loai_yeu_cau']} - Phòng {$row['ma_phong']}",
            'time' => date('d/m/Y', strtotime($row['ngay_tao']))
        ];
    }

    $response = [
        'success' => true,
        'data' => $data
    ];
} catch(Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}
echo json_encode($response);