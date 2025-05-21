<?php
session_start();
require_once(__DIR__ . '/../config.php');
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $vai_tro = 'sinh viên';
    $ma_so_sinh_vien = $_POST['ma_so_sinh_vien'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];

    // Xử lý upload ảnh
    $anh_sinh_vien = null;
    if (isset($_FILES['anh_sinh_vien']) && $_FILES['anh_sinh_vien']['error'] == 0) {
        $target_dir = '../images/';
        $file_ext = pathinfo($_FILES['anh_sinh_vien']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('sv_', true) . '.' . $file_ext;
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['anh_sinh_vien']['tmp_name'], $target_file)) {
            $anh_sinh_vien = $file_name;
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi upload ảnh sinh viên!']);
            exit();
        }
    }

    try {
        // Kiểm tra tên đăng nhập hoặc email đã tồn tại chưa
        $check_sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = :ten_dang_nhap OR email = :email";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':ten_dang_nhap', $ten_dang_nhap);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc email đã tồn tại!']);
            exit();
        }

        // Insert vào bảng nguoi_dung
        $sql = "INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, ho_ten, email, so_dien_thoai, vai_tro) 
                VALUES (:ten_dang_nhap, :mat_khau, :ho_ten, :email, :so_dien_thoai, :vai_tro)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ten_dang_nhap', $ten_dang_nhap);
        $stmt->bindParam(':mat_khau', $mat_khau);
        $stmt->bindParam(':ho_ten', $ho_ten);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':so_dien_thoai', $so_dien_thoai);
        $stmt->bindParam(':vai_tro', $vai_tro);
        $result = $stmt->execute();

        if ($result) {
            $ma_nguoi_dung = $conn->lastInsertId();
            // Insert vào bảng sinh_vien
            $sql_sv = "INSERT INTO sinh_vien (ma_nguoi_dung, ma_so_sinh_vien, ngay_sinh, gioi_tinh, anh_sinh_vien) 
                        VALUES (:ma_nguoi_dung, :ma_so_sinh_vien, :ngay_sinh, :gioi_tinh, :anh_sinh_vien)";
            $stmt_sv = $conn->prepare($sql_sv);
            $stmt_sv->bindParam(':ma_nguoi_dung', $ma_nguoi_dung);
            $stmt_sv->bindParam(':ma_so_sinh_vien', $ma_so_sinh_vien);
            $stmt_sv->bindParam(':ngay_sinh', $ngay_sinh);
            $stmt_sv->bindParam(':gioi_tinh', $gioi_tinh);
            $stmt_sv->bindParam(':anh_sinh_vien', $anh_sinh_vien);
            $stmt_sv->execute();

            echo json_encode(['success' => true, 'message' => 'Đăng ký thành công! Vui lòng đăng nhập.']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại! Vui lòng thử lại.']);
            exit();
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        exit();
    }
}
echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
?> 