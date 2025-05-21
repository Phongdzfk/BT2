<?php
require_once(__DIR__ . '/../config.php');

$response = array('success' => false, 'message' => '');

try {
    // Validate required fields
    $required_fields = array('ma_sinh_vien', 'so_tien', 'noi_dung', 'han_thanh_toan');
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Vui lòng điền đầy đủ thông tin");
        }
    }

    // Insert bill
    $sql = "INSERT INTO hoa_don (ma_sinh_vien, so_tien, noi_dung, han_thanh_toan, trang_thai) 
            VALUES (?, ?, ?, ?, 'chưa thanh toán')";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(array(
        $_POST['ma_sinh_vien'],
        $_POST['so_tien'],
        $_POST['noi_dung'],
        $_POST['han_thanh_toan']
    ));

    $response['success'] = true;
    $response['message'] = "Thêm hóa đơn thành công";

} catch(Exception $e) {
    $response['message'] = "Lỗi: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 