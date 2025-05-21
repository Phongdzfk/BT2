<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Hệ Thống Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .register-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }

        .panel {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .panel-heading {
            background-color: #c0392b !important;
            color: white !important;
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px 10px 0 0;
        }

        .form-group label {
            font-weight: 500;
        }

        .form-control {
            border-radius: 5px;
            padding: 0.75rem;
        }

        .btn-register {
            background: #c0392b;
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            width: 100%;
            font-weight: 500;
            margin-top: 1rem;
            border: 1px solid #c0392b;
        }

        .btn-register:hover {
            background: #a93226;
            color: white;
            border: 1px solid #a93226;
        }

        .form-control:focus {
            border-color: #c0392b;
            box-shadow: 0 0 5px rgba(192, 57, 43, 0.3);
        }

        .gender-group,
        .interests-group {
            margin-bottom: 1rem;
        }

        .gender-group .radio,
        .interests-group .checkbox {
            margin-right: 1.5rem;
            display: inline-block;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Đăng ký thông tin</h3>
                </div>
                <div class="panel-body">
                    <?php if(isset($_SESSION['register_error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['register_error']; unset($_SESSION['register_error']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['register_success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['register_success']; unset($_SESSION['register_success']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form id="registerForm" action="api/register_process.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="ten_dang_nhap">Tên đăng nhập</label>
                            <input type="text" name="ten_dang_nhap" class="form-control" placeholder="Nhập tên đăng nhập" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="form-group">
                            <label for="ho_ten">Họ và tên</label>
                            <input type="text" name="ho_ten" class="form-control" placeholder="Nhập họ và tên" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                        </div>
                        <div class="form-group">
                            <label for="so_dien_thoai">Số điện thoại</label>
                            <input type="text" name="so_dien_thoai" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="form-group">
                            <label for="ma_so_sinh_vien">Mã số sinh viên</label>
                            <input type="text" name="ma_so_sinh_vien" class="form-control" placeholder="Nhập MSSV" required>
                        </div>
                        <div class="form-group">
                            <label for="ngay_sinh">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="gioi_tinh">Giới tính</label>
                            <select name="gioi_tinh" class="form-control" required>
                                <option value="">Chọn giới tính</option>
                                <option value="nam">Nam</option>
                                <option value="nữ">Nữ</option>
                                <option value="khác">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="anh_sinh_vien">Ảnh sinh viên</label>
                            <input type="file" name="anh_sinh_vien" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-register">Đăng ký</button>
                    </form>
                    <div id="registerMsg"></div>
                    <div class="login-link">
                        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            $('#registerMsg').html('');
            var formData = new FormData(this);
            $.ajax({
                url: 'api/register_process.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        $('#registerMsg').html('<div class="alert alert-success">'+res.message+'</div>');
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 1500);
                    } else {
                        $('#registerMsg').html('<div class="alert alert-danger">'+res.message+'</div>');
                    }
                },
                error: function() {
                    $('#registerMsg').html('<div class="alert alert-danger">Lỗi hệ thống!</div>');
                }
            });
        });
    </script>
</body>
</html>
