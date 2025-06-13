<?php
session_start();

// Cài đặt PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../music/PHPMailer-master/src/PHPMailer.php';
require_once '../music/PHPMailer-master/src/SMTP.php';

// Tài khoản và mật khẩu mặc định
$default_username = 'admin';
$default_password = 'admin123';

// Tạo CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Hàm lấy thông tin địa chỉ từ IP
function getGeolocation($ip) {
    $url = "http://ip-api.com/json/{$ip}";
    $response = @file_get_contents($url);
    if ($response === false) {
        return ['city' => 'Không xác định', 'region' => 'Không xác định', 'country' => 'Không xác định'];
    }
    $data = json_decode($response, true);
    if ($data['status'] === 'success') {
        return [
            'city' => $data['city'] ?? 'Không xác định',
            'region' => $data['regionName'] ?? 'Không xác định',
            'country' => $data['country'] ?? 'Không xác định'
        ];
    }
    return ['city' => 'Không xác định', 'region' => 'Không xác định', 'country' => 'Không xác định'];
}

// Lấy IP thực (hỗ trợ proxy)
function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Lỗi bảo mật: CSRF token không hợp lệ!";
    } else {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($username === $default_username && $password === $default_password) {
            $_SESSION['logged_in'] = true;

            // Lấy IP và thông tin địa chỉ
            $ip = getRealIpAddr();
            $geo = getGeolocation($ip);
            $address = "{$geo['city']}, {$geo['region']}, {$geo['country']}";

            // Gửi email thông báo
            $mail = new PHPMailer(true);
            try {
                // Cấu hình SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'eddybot8@gmail.com';
                $mail->Password = 'orof cdoo gctr lliy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Người nhận
                $mail->setFrom('eddybot8@gmail.com', 'Control Panel');
                $mail->addAddress('ecaiconmemay@gmail.com'); // Email admin

                // Nội dung email
                $mail->isHTML(true);
                $mail->Subject = 'Login new adress to Control Panel';
                $mail->Body = 'Đăng nhập thành công vào Control Panel<br>'.
                              'Tài khoản: ' . htmlspecialchars($username) . '<br>'.
                              'Thời gian: ' . date('Y-m-d H:i:s') . '<br>'.
                              'IP: ' . htmlspecialchars($ip) . '<br>'.
                              'Địa chỉ: ' . htmlspecialchars($address);
                $mail->AltBody = 'Đăng nhập thành công vào Control Panel. '.
                                 'Tài khoản: ' . htmlspecialchars($username) . '. '.
                                 'Thời gian: ' . date('Y-m-d H:i:s') . '. '.
                                 'IP: ' . htmlspecialchars($ip) . '. '.
                                 'Địa chỉ: ' . htmlspecialchars($address);

                $mail->send();
            } catch (Exception $e) {
                $error = "Không thể gửi email thông báo: {$mail->ErrorInfo}";
            }

            // Chuyển hướng sau khi đăng nhập thành công
            header("Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ");
            exit();
        } else {
            $error = "Sai tài khoản hoặc mật khẩu!";
        }
    }
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel - Sakura</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f9e0e6, #fff0f5);
            overflow: hidden;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(255, 105, 180, 0.3);
            text-align: center;
            width: 400px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 1s ease-in-out;
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('https://www.transparenttextures.com/patterns/sakura.png') repeat;
            opacity: 0.15;
            transform: rotate(30deg);
        }
        .login-container img.logo {
            width: 80px;
            margin-bottom: 20px;
        }
        .login-container h2 {
            margin-bottom: 25px;
            color: #ff69b4;
            font-weight: 700;
            font-size: 28px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .login-container input {
            display: block;
            width: 100%;
            padding: 14px;
            margin: 15px 0;
            border: 2px solid #ffb6c1;
            border-radius: 12px;
            background: rgba(255, 245, 247, 0.9);
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .login-container input:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.4);
        }
        .login-container button {
            padding: 14px;
            width: 100%;
            background: linear-gradient(45deg, #ff69b4, #ff8c94);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.3s;
            position: relative;
        }
        .login-container button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(255, 105, 180, 0.5);
        }
        .login-container button.loading::after {
            content: '';
            border: 3px solid #fff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .error {
            color: #ff4040;
            margin-bottom: 15px;
            font-size: 14px;
            background: rgba(255, 240, 245, 0.9);
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ff9999;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="https://img.icons8.com/color/80/000000/cherry-blossom.png" alt="Sakura Logo" class="logo">
        <h2>Control Panel - Sakura</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" id="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="username" placeholder="Tài khoản" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" id="login-button">Đăng nhập</button>
        </form>
    </div>

    <script>
        // Anti Dev Tools
        (function() {
            let devtools = false;
            const threshold = 160;

            window.addEventListener('resize', () => {
                if (window.outerWidth - window.innerWidth > threshold || 
                    window.outerHeight - window.innerHeight > threshold) {
                    devtools = true;
                }
                if (devtools) {
                    document.body.innerHTML = '<h1>Đừng mở Dev Tools!</h1>';
                }
            });

            document.addEventListener('keydown', (e) => {
                if (
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                    (e.ctrlKey && e.shiftKey && e.key === 'J') ||
                    (e.ctrlKey && e.key === 'U')
                ) {
                    e.preventDefault();
                    alert('Dev Tools bị chặn!');
                }
            });

            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                alert('Chuột phải bị chặn!');
            });
        })();

        // Hiệu ứng loading khi gửi form
        document.getElementById('login-form').addEventListener('submit', function() {
            document.getElementById('login-button').classList.add('loading');
            document.getElementById('login-button').disabled = true;
        });
    </script>
</body>
</html>