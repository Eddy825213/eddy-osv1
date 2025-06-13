<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('USER_JSON_PATH', __DIR__ . '/data/users.json');
define('PROFILE_JSON_PATH', __DIR__ . '/data/profiles.json');

// Kiểm tra và tạo thư mục data nếu chưa tồn tại
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Khởi tạo users.json với tài khoản admin mặc định
$userList = [];
if (file_exists(USER_JSON_PATH)) {
    $jsonContent = file_get_contents(USER_JSON_PATH);
    if ($jsonContent === false) {
        die('Không thể đọc file users.json!');
    }
    $userList = json_decode($jsonContent, true) ?: [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON: ' . json_last_error_msg());
    }
} else {
    $adminUser = [
        'name' => 'Admin User',
        'email' => 'admin@profile.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin'
    ];
    $userList[] = $adminUser;
    if (file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT)) === false) {
        die('Không thể tạo file users.json!');
    }
    echo 'Tài khoản admin đã được tạo: Email: admin@profile.com, Mật khẩu: admin123<br>';
}

// Khởi tạo profiles.json
$profileList = [];
if (file_exists(PROFILE_JSON_PATH)) {
    $jsonContent = file_get_contents(PROFILE_JSON_PATH);
    if ($jsonContent === false) {
        die('Không thể đọc file profiles.json!');
    }
    $profileList = json_decode($jsonContent, true) ?: [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON trong profiles.json: ' . json_last_error_msg());
    }
} else {
    if (file_put_contents(PROFILE_JSON_PATH, json_encode([])) === false) {
        die('Không thể tạo file profiles.json!');
    }
    echo 'File profiles.json đã được tạo thành công!<br>';
}

echo 'Cài đặt hệ thống profile hoàn tất!';
?>