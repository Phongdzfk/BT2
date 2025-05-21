<?php 
session_start();

// Kiểm tra nếu đã đăng nhập thì chuyển hướng về trang chủ
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Ký Túc Xá PTIT</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #fff5f5, #ffe3e3);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .panel {
            border: 1px solid rgba(192, 57, 43, 0.1);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .panel-heading {
            background-color: #c0392b !important;
            color: white !important;
            text-align: center;
            padding: 15px;
        }
        .btn-primary {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        .btn-primary:hover {
            background-color: #a93226;
            border-color: #a93226;
        }
        .form-control:focus {
            border-color: #c0392b;
            box-shadow: 0 0 5px rgba(192, 57, 43, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fas fa-user-circle"></i> Đăng nhập</h3>
            </div>
            <div class="panel-body">
                <?php
                if(isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                    unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                    unset($_SESSION['success']);
                }
                ?>
                <form id="loginForm">
                    <div class="form-group">
                        <label for="ten_dang_nhap"><i class="fas fa-user"></i> Tên đăng nhập</label>
                        <input type="text" class="form-control" id="ten_dang_nhap" name="ten_dang_nhap" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                </form>
                <div id="loginMsg"></div>
                <div class="text-center" style="margin-top: 15px;">
                    <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        $('#loginMsg').html('');
        $.ajax({
            url: 'api/login_process.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    $('#loginMsg').html('<div class="alert alert-success">'+res.message+'</div>');
                    setTimeout(function() {
                        window.location.href = res.redirect;
                    }, 1000);
                } else {
                    $('#loginMsg').html('<div class="alert alert-danger">'+res.message+'</div>');
                }
            },
            error: function() {
                $('#loginMsg').html('<div class="alert alert-danger">Lỗi hệ thống!</div>');
            }
        });
    });
    </script>
</body>
</html> 