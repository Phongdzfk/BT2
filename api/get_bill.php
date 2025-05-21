<?php
require_once(__DIR__ . '/../config.php');

$response = array('success' => false, 'data' => null, 'message' => '');

try {
    if (empty($_GET['ma_hoa_don'])) {
        throw new Exception("Mã hóa đơn không được để trống");
    }

    $sql = "SELECT h.*, nd.ho_ten as ten_sinh_vien, p.so_phong as ten_phong 
            FROM hoa_don h
            JOIN sinh_vien s ON h.ma_sinh_vien = s.ma_sinh_vien
            JOIN nguoi_dung nd ON s.ma_nguoi_dung = nd.ma_nguoi_dung
            JOIN phong p ON s.ma_phong = p.ma_phong
            WHERE h.ma_hoa_don = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(array($_GET['ma_hoa_don']));
    $bill = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bill) {
        $response['success'] = true;
        $response['data'] = $bill;
    } else {
        throw new Exception("Không tìm thấy hóa đơn");
    }

} catch(Exception $e) {
    $response['message'] = "Lỗi: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 