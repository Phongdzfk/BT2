<?php
session_start();
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $password = $_POST['password'];
    try {
        $sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = :ten_dang_nhap";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ten_dang_nhap', $ten_dang_nhap);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['mat_khau'])) {
                $_SESSION['user_id'] = $user['ma_nguoi_dung'];
                $_SESSION['user_name'] = $user['ho_ten'];
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['ho_ten'];
                $_SESSION['role'] = $user['vai_tro'];
                $redirect = ($user['vai_tro'] === 'quản trị viên') ? 'dashboard.html' : 'index.html';
                echo json_encode([
                    'success' => true,
                    'message' => 'Đăng nhập thành công',
                    'role' => $user['vai_tro'],
                    'username' => $user['ho_ten'],
                    'redirect' => $redirect
                ]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Mật khẩu không chính xác!']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tên đăng nhập không tồn tại!']);
            exit();
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
    exit();
}
?> 