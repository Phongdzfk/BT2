<?php
require_once(__DIR__ . '/../config.php');
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$id = $_POST['ma_yeu_cau'] ?? 0;
$ma_sinh_vien = $_POST['ma_sinh_vien'] ?? '';
$ma_phong = $_POST['ma_phong'] ?? '';
$loai_yeu_cau = $_POST['loai_yeu_cau'] ?? '';
$mo_ta = $_POST['mo_ta'] ?? '';
$trang_thai = $_POST['trang_thai'] ?? '';
if(!$id || !$ma_sinh_vien || !$ma_phong || !$loai_yeu_cau || !$mo_ta || !$trang_thai) {
    echo json_encode(['success'=>false,'message'=>'Vui lòng nhập đầy đủ thông tin!']); exit;
}
$stmt = $conn->prepare("UPDATE yeu_cau SET ma_sinh_vien=?, ma_phong=?, loai_yeu_cau=?, mo_ta=?, trang_thai=?, ngay_cap_nhat=NOW() WHERE ma_yeu_cau=?");
$stmt->execute([$ma_sinh_vien, $ma_phong, $loai_yeu_cau, $mo_ta, $trang_thai, $id]);

$mail_status = null;
// Gửi mail khi trạng thái là 'đã giải quyết'
if (mb_strtolower(trim($trang_thai)) === 'đã giải quyết') {
    // Lấy email sinh viên
    $stmt = $conn->prepare("SELECT nd.email, nd.ho_ten FROM sinh_vien sv JOIN nguoi_dung nd ON sv.ma_nguoi_dung = nd.ma_nguoi_dung WHERE sv.ma_sinh_vien = ?");
    $stmt->execute([$ma_sinh_vien]);
    $sv = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($sv && $sv['email']) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '24hphon@gmail.com'; 
            $mail->Password = 'dlxl joqc vfak fpih';   
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('24hphon@gmail.com', 'KTX PTIT');
            $mail->addAddress($sv['email'], $sv['ho_ten']);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            // Lấy thông tin phòng
            $stmt_room = $conn->prepare("SELECT p.ten_phong, p.loai_phong, p.toa_nha FROM phong p WHERE p.ma_phong = ?");
            $stmt_room->execute([$ma_phong]);
            $phong = $stmt_room->fetch(PDO::FETCH_ASSOC);
            // Parse ngày ở/ngày đi từ mo_ta
            $ngay_vao = $ngay_ra = '';
            if (preg_match('/Ngày ở: ([0-9\-]+), Ngày đi: ([0-9\-]+)/u', $mo_ta, $matches)) {
                $ngay_vao = $matches[1];
                $ngay_ra = $matches[2];
            }
            $admin_phone = '0858262492';
            $mail->Subject = 'Yêu cầu đặt phòng đã được chấp nhận';
            $mail->Body    = '<b>Chúc mừng!</b> Yêu cầu đặt phòng của bạn đã được chấp nhận.<br><br>' .
                '<b>Thông tin đặt phòng:</b><br>' .
                'Họ tên: ' . htmlspecialchars($sv['ho_ten']) . '<br>' .
                'Mã sinh viên: ' . htmlspecialchars($ma_sinh_vien) . '<br>' .
                'Loại phòng: ' . htmlspecialchars($phong['loai_phong'] ?? '') . '<br>' .
                'Tòa: ' . htmlspecialchars($phong['toa_nha'] ?? '') . '<br>' .
                'Phòng: ' . htmlspecialchars($phong['ten_phong'] ?? '') . '<br>' .
                'Ngày ở: ' . htmlspecialchars($ngay_vao) . '<br>' .
                'Ngày đi: ' . htmlspecialchars($ngay_ra) . '<br>' .
                '--------------------------<br>' .
                'Nếu có thắc mắc, vui lòng liên hệ quản trị viên qua số điện thoại: <b>' . $admin_phone . '</b>';
            if ($mail->send()) {
                $mail_status = 'success';
            } else {
                $mail_status = 'fail';
            }
        } catch (Exception $e) {
            $mail_status = 'fail: ' . $mail->ErrorInfo;
        }
    }
}

// Xử lý logic đặc biệt cho yêu cầu đặt phòng khi đã giải quyết
if (mb_strtolower(trim($trang_thai)) === 'đã giải quyết' && mb_strtolower(trim($loai_yeu_cau)) === 'đặt phòng') {
    // Parse ngày ở/ngày đi từ mo_ta
    $ngay_vao = null;
    $ngay_ra = null;
    if (preg_match('/Ngày ở: ([0-9\-]+), Ngày đi: ([0-9\-]+)/u', $mo_ta, $matches)) {
        $ngay_vao = $matches[1];
        $ngay_ra = $matches[2];
    }
    // Cập nhật sinh viên
    if ($ngay_vao && $ngay_ra) {
        $stmt = $conn->prepare("UPDATE sinh_vien SET ma_phong=?, ngay_vao=?, ngay_ra=? WHERE ma_sinh_vien=?");
        $stmt->execute([$ma_phong, $ngay_vao, $ngay_ra, $ma_sinh_vien]);
    } else {
        $stmt = $conn->prepare("UPDATE sinh_vien SET ma_phong=? WHERE ma_sinh_vien=?");
        $stmt->execute([$ma_phong, $ma_sinh_vien]);
    }
    // Tăng số người hiện tại của phòng
    $stmt = $conn->prepare("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai + 1 WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    // Kiểm tra nếu phòng đã đầy thì cập nhật trạng thái
    $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if($room && $room['so_nguoi_hien_tai'] >= $room['suc_chua']) {
        $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'đã đầy' WHERE ma_phong = ?");
        $stmt->execute([$ma_phong]);
    }
}

// Xử lý logic đặc biệt cho yêu cầu đổi phòng khi đã giải quyết
if (mb_strtolower(trim($trang_thai)) === 'đã giải quyết' && mb_strtolower(trim($loai_yeu_cau)) === 'đổi phòng') {
    // Lấy thông tin phòng mới
    $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    $phong_moi = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$phong_moi || $phong_moi['so_nguoi_hien_tai'] >= $phong_moi['suc_chua']) {
        echo json_encode(['success'=>false,'message'=>'Phòng muốn chuyển đến đã đầy, không thể đổi phòng!']);
        exit;
    }
    // Lấy phòng cũ của sinh viên
    $stmt = $conn->prepare("SELECT ma_phong FROM sinh_vien WHERE ma_sinh_vien = ?");
    $stmt->execute([$ma_sinh_vien]);
    $phong_cu = $stmt->fetchColumn();
    // Cập nhật phòng mới cho sinh viên
    $stmt = $conn->prepare("UPDATE sinh_vien SET ma_phong=? WHERE ma_sinh_vien=?");
    $stmt->execute([$ma_phong, $ma_sinh_vien]);
    // Tăng số người phòng mới
    $stmt = $conn->prepare("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai + 1 WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    // Giảm số người phòng cũ
    if($phong_cu && $phong_cu != $ma_phong) {
        $stmt = $conn->prepare("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai - 1 WHERE ma_phong = ?");
        $stmt->execute([$phong_cu]);
        // Nếu phòng cũ không còn đầy thì cập nhật trạng thái còn trống
        $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
        $stmt->execute([$phong_cu]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        if($room && $room['so_nguoi_hien_tai'] < $room['suc_chua']) {
            $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'còn trống' WHERE ma_phong = ?");
            $stmt->execute([$phong_cu]);
        }
    }
    // Nếu phòng mới đầy thì cập nhật trạng thái đã đầy
    $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
    $stmt->execute([$ma_phong]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if($room && $room['so_nguoi_hien_tai'] >= $room['suc_chua']) {
        $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'đã đầy' WHERE ma_phong = ?");
        $stmt->execute([$ma_phong]);
    }
}

// Xử lý logic cho yêu cầu bảo trì
if (mb_strtolower(trim($loai_yeu_cau)) === 'bảo trì') {
    if (mb_strtolower(trim($trang_thai)) === 'đang giải quyết') {
        // Chuyển phòng sang bảo trì
        $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'bảo trì' WHERE ma_phong = ?");
        $stmt->execute([$ma_phong]);
    } elseif (mb_strtolower(trim($trang_thai)) === 'đã giải quyết') {
        // Nếu phòng chưa đầy thì chuyển về còn trống, nếu đầy thì vẫn là đã đầy
        $stmt = $conn->prepare("SELECT so_nguoi_hien_tai, suc_chua FROM phong WHERE ma_phong = ?");
        $stmt->execute([$ma_phong]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        if($room && $room['so_nguoi_hien_tai'] < $room['suc_chua']) {
            $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'còn trống' WHERE ma_phong = ?");
            $stmt->execute([$ma_phong]);
        } else if($room && $room['so_nguoi_hien_tai'] >= $room['suc_chua']) {
            $stmt = $conn->prepare("UPDATE phong SET trang_thai = 'đã đầy' WHERE ma_phong = ?");
            $stmt->execute([$ma_phong]);
        }
    }
}

echo json_encode(['success'=>true,'message'=>'Cập nhật yêu cầu thành công!','mail_status'=>$mail_status]); 