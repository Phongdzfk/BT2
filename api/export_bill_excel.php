<?php
ob_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

// Tạo file Excel mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề
$sheet->setCellValue('A1', 'HÓA ĐƠN THANH TOÁN');
$sheet->mergeCells('A1:C1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Ký túc xá: ' . $bill['ten_ktx']);
$sheet->mergeCells('A2:C2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Thông tin hóa đơn
$sheet->setCellValue('A4', 'Mã sinh viên:');
$sheet->setCellValue('B4', $bill['ma_so_sinh_vien']);
$sheet->setCellValue('A5', 'Họ tên sinh viên:');
$sheet->setCellValue('B5', $bill['student_name']);
$sheet->setCellValue('A6', 'Phòng:');
$sheet->setCellValue('B6', $bill['so_phong']);
$sheet->setCellValue('A7', 'Ngày tạo:');
$sheet->setCellValue('B7', date('d/m/Y', strtotime($bill['ngay_tao'])));
$sheet->setCellValue('A8', 'Hạn thanh toán:');
$sheet->setCellValue('B8', date('d/m/Y', strtotime($bill['han_thanh_toan'])));
$sheet->setCellValue('A9', 'Trạng thái:');
$sheet->setCellValue('B9', $bill['trang_thai']);

// Định dạng cột A
$sheet->getStyle('A4:A9')->getFont()->setBold(true);
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(30);

// Bảng chi tiết
$sheet->setCellValue('A11', 'STT');
$sheet->setCellValue('B11', 'Nội dung');
$sheet->setCellValue('C11', 'Số tiền');
$sheet->getStyle('A11:C11')->getFont()->setBold(true);
$sheet->getStyle('A11:C11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A12', '1');
$sheet->setCellValue('B12', $bill['noi_dung']);
$sheet->setCellValue('C12', number_format($bill['so_tien'], 0, ',', '.') . ' VNĐ');

// Tổng cộng
$sheet->setCellValue('A14', 'Tổng cộng:');
$sheet->setCellValue('C14', number_format($bill['so_tien'], 0, ',', '.') . ' VNĐ');
$sheet->getStyle('A14:C14')->getFont()->setBold(true);
$sheet->getStyle('C14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Footer
$sheet->setCellValue('A16', 'Ngày xuất: ' . date('d/m/Y'));
$sheet->mergeCells('A16:C16');
$sheet->getStyle('A16')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A17', 'Chữ ký người thu tiền');
$sheet->mergeCells('A17:C17');
$sheet->getStyle('A17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Thêm viền cho bảng
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A11:C12')->applyFromArray($styleArray);

// Xuất file
$filename = 'HoaDon_' . $bill['ma_so_sinh_vien'] . '_' . date('dmY', strtotime($bill['ngay_tao'])) . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
ob_end_clean();
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 