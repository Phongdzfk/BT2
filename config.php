<?php
$host = '';  // thêm cổng 3307
$dbname = '';  // tên database bạn đã tạo
$username = '';  // username mặc định của MySQL
$password = '';  // không có mật khẩu

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?> 
