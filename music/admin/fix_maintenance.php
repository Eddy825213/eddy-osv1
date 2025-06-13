<?php
define('MAINTENANCE_JSON_PATH', __DIR__ . '/../data/maintenance.json');
$maintenanceData = ['enabled' => false, 'message' => ''];
if (file_exists(MAINTENANCE_JSON_PATH)) {
    $maintenanceContent = file_get_contents(MAINTENANCE_JSON_PATH);
    $maintenanceData = json_decode($maintenanceContent, true) ?: [];
    $maintenanceData = array_merge(['enabled' => false, 'message' => ''], $maintenanceData);
    $fp = fopen(MAINTENANCE_JSON_PATH, 'w');
    if ($fp !== false && flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($maintenanceData, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}
echo 'maintenance.json đã được sửa.';
?>