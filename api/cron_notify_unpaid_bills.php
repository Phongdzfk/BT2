<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Lấy các hóa đơn chưa thanh toán, hạn thanh toán là hôm nay
$today = date('Y-m-d');
$sql = "SELECT h.*, nd.email, nd.ho_ten
        FROM hoa_don h
        JOIN sinh_vien s ON h.ma_sinh_vien = s.ma_sinh_vien
        JOIN nguoi_dung nd ON s.ma_nguoi_dung = nd.ma_nguoi_dung
        WHERE h.trang_thai = 'chưa thanh toán' AND h.han_thanh_toan = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$today]);
$bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($bills) {
    foreach ($bills as $bill) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // <-- Điền SMTP server của bạn
            $mail->SMTPAuth = true;
            $mail->Username = '24hphon@gmail.com'; 
            $mail->Password = 'dlxl joqc vfak fpih'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('24hphon@gmail.com', 'KTX PTIT'); // <-- Điền email gửi
            $mail->addAddress($bill['email'], $bill['ho_ten']);

            $mail->isHTML(true);
            $mail->Subject = 'Nhắc nhở thanh toán hóa đơn KTX';
            $mail->Body = "Chào {$bill['ho_ten']},<br>
                Hóa đơn <b>{$bill['noi_dung']}</b> với số tiền <b>" . number_format($bill['so_tien'], 0, ',', '.') . " VNĐ</b> đến hạn thanh toán hôm nay.<br>
                Vui lòng thanh toán đúng hạn để tránh bị xử lý.<br>
                <br>Trân trọng,<br>Ký túc xá PTIT";

            $mail->send();
        } catch (Exception $e) {
            echo "Lỗi gửi email: " . $e->getMessage();
        }
    }
} 