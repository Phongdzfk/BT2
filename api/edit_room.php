<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_phong = $_POST['ma_phong'];
    $so_phong = $_POST['so_phong'];
    $floors = $_POST['floors'];
    $suc_chua = $_POST['suc_chua'];
    $trang_thai = $_POST['trang_thai'];
    $so_nguoi_hien_tai = isset($_POST['so_nguoi_hien_tai']) ? $_POST['so_nguoi_hien_tai'] : 0;
    $ma_ktx = $_POST['ma_ktx'];
    $anh_phong = isset($_POST['anh_phong_old']) ? $_POST['anh_phong_old'] : null;

    // Xử lý upload ảnh mới nếu có
    if (isset($_FILES['anh_phong']) && $_FILES['anh_phong']['error'] == 0) {
        $target_dir = '../images/';
        $file_ext = pathinfo($_FILES['anh_phong']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('room_', true) . '.' . $file_ext;
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['anh_phong']['tmp_name'], $target_file)) {
            $anh_phong = $file_name;
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi upload ảnh phòng!']);
            exit();
        }
    }

    try {
        $sql = "UPDATE phong SET ma_ktx = :ma_ktx, so_phong = :so_phong, floors = :floors, suc_chua = :suc_chua, so_nguoi_hien_tai = :so_nguoi_hien_tai, trang_thai = :trang_thai, anh_phong = :anh_phong WHERE ma_phong = :ma_phong";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ma_ktx', $ma_ktx);
        $stmt->bindParam(':so_phong', $so_phong);
        $stmt->bindParam(':floors', $floors);
        $stmt->bindParam(':suc_chua', $suc_chua);
        $stmt->bindParam(':so_nguoi_hien_tai', $so_nguoi_hien_tai);
        $stmt->bindParam(':trang_thai', $trang_thai);
        $stmt->bindParam(':anh_phong', $anh_phong);
        $stmt->bindParam(':ma_phong', $ma_phong);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Cập nhật phòng thành công!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
    exit();
}
echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']); 