<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('USER_JSON_PATH', __DIR__ . '/../data/user.json'); // Đường dẫn tới user.json

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_DEFAULT) ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Email không hợp lệ!');
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
        die('File user.json không tồn tại!');
    }

    $userFound = false;
    foreach ($userList as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            $userFound = true;
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email; // Lưu email vào session
            header('Location: ../admin/');
            exit;
        }
    }

    if (!$userFound) {
        die('Email hoặc mật khẩu không đúng!');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
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
        .login-container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.5);
        }
        .login-container input, .login-container button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            border-radius: 5px;
        }
        .login-container input {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .login-container button {
            background: hotpink;
            color: #fff;
            cursor: pointer;
        }
        .login-container button:hover {
            background: #ff1493;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>