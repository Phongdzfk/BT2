<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Lấy ID hóa đơn từ request
$bill_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bill_id <= 0) {
    die('Invalid bill ID');
}

// Lấy thông tin hóa đơn
$sql = "SELECT h.*, s.ma_so_sinh_vien, s.ma_sinh_vien, nd.ho_ten as student_name, p.so_phong, k.ten_ktx 
FROM hoa_don h
JOIN sinh_vien s ON h.ma_sinh_vien = s.ma_sinh_vien
        JOIN nguoi_dung nd ON s.ma_nguoi_dung = nd.ma_nguoi_dung
        JOIN phong p ON s.ma_phong = p.ma_phong 
        JOIN ky_tuc_xa k ON p.ma_ktx = k.ma_ktx 
        WHERE h.ma_hoa_don = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$bill_id]);
$bill = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bill) {
    die('Bill not found');
}

// Tạo nội dung HTML cho PDF
$html = '<style>
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ddd; }
    .header { text-align: center; margin-bottom: 20px; }
    .title { font-size: 20px; font-weight: bold; }
    .subtitle { font-size: 16px; margin-bottom: 10px; }
    .info-table td { border: none; padding: 4px 8px; }
    .total { text-align: right; font-weight: bold; margin-top: 20px; }
    .footer { text-align: center; margin-top: 50px; font-style: italic; }
</style>';
$html .= '<div class="header">'
    .'<div class="title">HÓA ĐƠN THANH TOÁN</div>'
    .'<div class="subtitle">Ký túc xá: '.htmlspecialchars($bill['ten_ktx']).'</div>'
    .'</div>';
$html .= '<table class="info-table">'
    .'<tr><td><b>Mã sinh viên:</b></td><td>'.htmlspecialchars($bill['ma_so_sinh_vien']).'</td></tr>'
    .'<tr><td><b>Họ tên sinh viên:</b></td><td>'.htmlspecialchars($bill['student_name']).'</td></tr>'
    .'<tr><td><b>Phòng:</b></td><td>'.htmlspecialchars($bill['so_phong']).'</td></tr>'
    .'<tr><td><b>Ngày tạo:</b></td><td>'.date('d/m/Y', strtotime($bill['ngay_tao'])).'</td></tr>'
    .'<tr><td><b>Hạn thanh toán:</b></td><td>'.date('d/m/Y', strtotime($bill['han_thanh_toan'])).'</td></tr>'
    .'<tr><td><b>Trạng thái:</b></td><td>'.htmlspecialchars($bill['trang_thai']).'</td></tr>'
    .'</table><br>';
$html .= '<table>'
    .'<tr><th>STT</th><th>Nội dung</th><th>Số tiền</th></tr>'
    .'<tr><td>1</td><td>'.htmlspecialchars($bill['noi_dung']).'</td><td>'.number_format($bill['so_tien'], 0, ',', '.').' VNĐ</td></tr>'
    .'</table>';
$html .= '<div class="total">Tổng cộng: '.number_format($bill['so_tien'], 0, ',', '.').' VNĐ</div>';
$html .= '<div class="footer">Ngày xuất: '.date('d/m/Y').'<br>Chữ ký người thu tiền<br>........................</div>';

// Tạo PDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 16,
    'margin_bottom' => 16,
    'margin_header' => 9,
    'margin_footer' => 9
]);
$mpdf->SetFont('dejavusans', '', 10);
$mpdf->WriteHTML($html);
$filename = 'HoaDon_' . $bill['ma_so_sinh_vien'] . '_' . date('dmY', strtotime($bill['ngay_tao'])) . '.pdf';
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD); 