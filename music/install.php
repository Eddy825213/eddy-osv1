<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('USER_JSON_PATH', __DIR__ . '/data/user.json');

// Kiểm tra và tạo thư mục data nếu chưa tồn tại
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Kiểm tra file user.json
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
    // Tạo file user.json rỗng nếu chưa tồn tại
    if (file_put_contents(USER_JSON_PATH, json_encode([])) === false) {
        die('Không thể tạo file user.json!');
    }
}

// Kiểm tra xem tài khoản admin đã tồn tại chưa
$adminExists = false;
foreach ($userList as $user) {
    if ($user['email'] === 'admin@example.com') {
        $adminExists = true;
        break;
    }
}

// Nếu chưa có tài khoản admin, tạo mới
if (!$adminExists) {
    $adminUser = [
        'first_name' => 'Admin',
        'last_name' => 'User',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT), // Mật khẩu mặc định: admin123
        'registered_at' => date('Y-m-d H:i:s'),
        'role' => 'admin' // Thêm trường role để phân biệt admin
    ];
    $userList[] = $adminUser;
    if (file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT)) === false) {
        die('Lỗi: Không thể lưu tài khoản admin vào user.json!');
    }
    echo 'Tài khoản admin đã được tạo thành công! Email: admin@example.com, Mật khẩu: admin123';
} else {
    echo 'Tài khoản admin đã tồn tại! Email: admin@example.com';
}
?>