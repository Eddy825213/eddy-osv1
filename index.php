<?php $created_config='./config.php';if(!file_exists($created_config)){if(touch($created_config)){}else{}}else{}?>
<?php require_once('./config.php'); ?>
<?php if(isset($config['head']['anti_ddos']) && $config['head']['anti_ddos'] !== '') {require($config['head']['anti_ddos']);}?>
<?php $lockfile=__DIR__.'/install.lock';$configFile=__DIR__.'/config/config.js';if(!file_exists($lockfile)){echo '<script>alert("Bạn chưa cấu hình thông tin website!")</script>';echo '<script>window.location="./install/"</script>';exit;}else{$install_lock=file_get_contents($lockfile);if(strpos($install_lock,'step1 = true')!==false&&strpos($install_lock,'step2 = true')!==false){if(!file_exists($configFile)){echo '<script>alert("File config chưa được tạo!")</script>';echo '<script>window.location="./install/"</script>';exit;}}else{echo '<script>alert("Bạn chưa cấu hình thông tin website!")</script>';echo '<script>window.location="./install/"</script>';exit;}} ?>
<?php
// Đọc và hiển thị nội dung từ main.html
if (file_exists('index.html')) {
    echo file_get_contents('index.html');
} else {
    echo '<h1>Lỗi: Không tìm thấy file index.html</h1>';
}
?>
<?php
if (!file_exists('config.php')) {
    header('Location: install.php');
    exit;
}
require_once 'config.php';
?>
