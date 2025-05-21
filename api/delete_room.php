<?php
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_phong = $_POST['ma_phong'];
    try {
        $sql = "DELETE FROM phong WHERE ma_phong = :ma_phong";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ma_phong', $ma_phong);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Xóa phòng thành công!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
    exit();
}
echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']); 