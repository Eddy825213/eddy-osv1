<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim(filter_input(INPUT_POST, 'first_name', FILTER_DEFAULT) ?? '');
    $lastName = trim(filter_input(INPUT_POST, 'last_name', FILTER_DEFAULT) ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_DEFAULT) ?? '');
    $otp = trim(filter_input(INPUT_POST, 'otp', FILTER_DEFAULT) ?? '');

    define('USER_JSON_PATH', __DIR__ . '/../data/user.json');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Email không hợp lệ!');
    }

    if (strlen($password) < 6) {
        die('Mật khẩu phải có ít nhất 6 ký tự!');
    }

    $userList = [];
    if (file_exists(USER_JSON_PATH)) {
        $jsonContent = file_get_contents(USER_JSON_PATH);
        if ($jsonContent === false) {
            die('Không thể đọc file user.json!');
        }
        $userList = json_decode($jsonContent, true) ?: [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Lỗi phân tích JSON: ' . json_last_error_msg());
        }
    } else {
        if (!is_dir(__DIR__ . '/../data')) {
            mkdir(__DIR__ . '/../data', 0755, true);
        }
        if (file_put_contents(USER_JSON_PATH, json_encode([])) === false) {
            die('Không thể tạo file user.json!');
        }
    }

    if (in_array($email, array_column($userList, 'email'))) {
        die('Email đã được đăng ký!');
    }

    if (!empty($otp) && isset($_SESSION['otp'])) {
        // Ép kiểu để so sánh
        if ((string)$_SESSION['otp'] === (string)$otp) {
            $user = [
                'first_name' => htmlspecialchars($firstName),
                'last_name' => htmlspecialchars($lastName),
                'name' => htmlspecialchars($firstName . ' ' . $lastName),
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'registered_at' => date('Y-m-d H:i:s')
            ];
            $userList[] = $user;
            if (file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT)) === false) {
                die('Lỗi: Không thể lưu dữ liệu người dùng!');
            }

            // Gửi thông báo cho admin
            $adminMail = new PHPMailer\PHPMailer\PHPMailer();
            $adminMail->isSMTP();
            $adminMail->Host = 'smtp.gmail.com';
            $adminMail->SMTPAuth = true;
            $adminMail->Username = 'eddybot8@gmail.com';
            $adminMail->Password = 'orof cdoo gctr lliy';
            $adminMail->SMTPSecure = 'tls';
            $adminMail->Port = 587;
            $adminMail->setFrom('eddybot8@gmail.com', 'Admin System');
            $adminMail->addAddress('ecaiconmemay@gmail.com');
            $adminMail->Subject = 'Người dùng mới đăng ký';
            $adminMail->Body = "Có 1 người dùng mới đăng ký:\nTên: $firstName $lastName\nEmail: $email\nThời gian: " . date('Y-m-d H:i:s');
            if (!$adminMail->send()) {
                // Không dừng nếu gửi email thất bại, chỉ ghi log
                error_log('Lỗi gửi email admin: ' . $adminMail->ErrorInfo);
            }

            echo 'Đăng ký thành công! Tên của bạn không thể thay đổi sau này. Đang chuyển hướng...';
            header('Refresh: 2; URL=../login/');
            unset($_SESSION['otp']);
            unset($_SESSION['otp_attempts']);
            exit;
        } else {
            die('Mã xác thực không đúng! Vui lòng thử lại.');
        }
    } else {
        if (!isset($_SESSION['otp_attempts'])) {
            $_SESSION['otp_attempts'] = 0;
        }
        if ($_SESSION['otp_attempts'] >= 3) {
            die('Đã vượt quá số lần gửi mã xác thực. Vui lòng thử lại sau!');
        }

        $otpCode = rand(100000, 999999);
        $_SESSION['otp'] = (string)$otpCode; // Lưu OTP dưới dạng chuỗi
        $_SESSION['otp_attempts']++;

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eddybot8@gmail.com';
        $mail->Password = 'orof cdoo gctr lliy';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('eddybot8@gmail.com', 'Xác thực đăng ký');
        $mail->addAddress($email);
        $mail->Subject = 'Mã xác thực đăng ký';
        $mail->Body = "Mã xác thực của bạn là: $otpCode. Vui lòng nhập mã này để hoàn tất đăng ký. Mã có hiệu lực trong 5 phút.";
        if (!$mail->send()) {
            die('Lỗi gửi mã xác thực: ' . $mail->ErrorInfo);
        }

        ?>
        <!DOCTYPE html>
        <html>
        <body>
            <form method="post">
                <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>">
                <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                <input type="text" name="otp" placeholder="Nhập mã xác thực" required>
                <button type="submit">Xác nhận</button>
                <p>Mã xác thực sẽ hết hạn sau 5 phút. Nếu không nhận được, hãy kiểm tra thư rác.</p>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <style>
        body {
            background: #2a1a3d;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.5);
        }
        .register-container input, .register-container button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            border-radius: 5px;
        }
        .register-container input {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .register-container button {
            background: hotpink;
            color: #fff;
            cursor: pointer;
        }
        .register-container button:hover {
            background: #ff1493;
        }
        .register-container p {
            font-size: 0.9em;
            color: #0ff;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng ký</h2>
        <form method="post">
            <input type="text" name="first_name" placeholder="Họ" required>
            <input type="text" name="last_name" placeholder="Tên" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu (ít nhất 6 ký tự)" required>
            <button type="submit">Đăng ký</button>
        </form>
    </div>
</body>
</html>