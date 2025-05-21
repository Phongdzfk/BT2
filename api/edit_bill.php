<?php
require_once(__DIR__ . '/../config.php');

$response = array('success' => false, 'message' => '');

try {
    // Validate required fields
    $required_fields = array('ma_hoa_don', 'ma_sinh_vien', 'so_tien', 'noi_dung', 'han_thanh_toan', 'trang_thai');
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Vui lòng điền đầy đủ thông tin");
        }
    }

    // Update bill
    $sql = "UPDATE hoa_don 
            SET ma_sinh_vien = ?, 
                so_tien = ?, 
                noi_dung = ?, 
                han_thanh_toan = ?,
                trang_thai = ?,
                ngay_thanh_toan = CASE 
                    WHEN ? = 'đã thanh toán' THEN CURRENT_DATE()
                    ELSE ngay_thanh_toan 
                END
            WHERE ma_hoa_don = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(array(
        $_POST['ma_sinh_vien'],
        $_POST['so_tien'],
        $_POST['noi_dung'],
        $_POST['han_thanh_toan'],
        $_POST['trang_thai'],
        $_POST['trang_thai'],
        $_POST['ma_hoa_don']
    ));

    $response['success'] = true;
    $response['message'] = "Cập nhật hóa đơn thành công";

} catch(Exception $e) {
    $response['message'] = "Lỗi: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 