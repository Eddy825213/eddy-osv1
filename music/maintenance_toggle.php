<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../music/login/');
    exit;
}

define('MAINTENANCE_JSON_PATH', __DIR__ . '/./data/maintenance.json');
$maintenanceData = ['enabled' => false];

if (file_exists(MAINTENANCE_JSON_PATH)) {
    $content = file_get_contents(MAINTENANCE_JSON_PATH);
    if ($content !== false) {
        $maintenanceData = json_decode($content, true) ?: ['enabled' => false];
        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Lỗi phân tích JSON trong maintenance.json: ' . json_last_error_msg());
        }
    } else {
        die('Không thể đọc file maintenance.json!');
    }
} else {
    $initialMaintenance = json_encode(['enabled' => false], JSON_PRETTY_PRINT);
    if (file_put_contents(MAINTENANCE_JSON_PATH, $initialMaintenance) === false) {
        die('Không thể tạo file maintenance.json!');
    }
}

$newState = !($maintenanceData['enabled'] ?? false);
$maintenanceData['enabled'] = $newState;

if (file_put_contents(MAINTENANCE_JSON_PATH, json_encode($maintenanceData, JSON_PRETTY_PRINT)) === false) {
    die('Lỗi: Không thể ghi vào file maintenance.json! Vui lòng kiểm tra quyền truy cập.');
}

header('Location: ./');
exit;
?>