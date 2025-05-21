<?php
require_once(__DIR__ . '/../config.php');

$response = array('success' => false, 'message' => '');

try {
    if (empty($_POST['ma_hoa_don'])) {
        throw new Exception("Mã hóa đơn không được để trống");
    }

    $sql = "DELETE FROM hoa_don WHERE ma_hoa_don = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(array($_POST['ma_hoa_don']));

    $response['success'] = true;
    $response['message'] = "Xóa hóa đơn thành công";

} catch(Exception $e) {
    $response['message'] = "Lỗi: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 