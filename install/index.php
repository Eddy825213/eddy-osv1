<?php
// File: install.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $dbname = $_POST['dbname'];
    
    try {
        $conn = new mysqli($host, $user, $pass);
        
        if ($conn->connect_error) {
            throw new Exception("Kết nối thất bại: " . $conn->connect_error);
        }
        
        // Tạo database nếu chưa tồn tại
        $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
        $conn->select_db($dbname);
        
        // Tạo bảng comments
        $sql = "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            content TEXT NOT NULL,
            timestamp BIGINT NOT NULL,
            likes INT DEFAULT 0,
            parent_id INT DEFAULT 0
        )";
        
        if (!$conn->query($sql)) {
            throw new Exception("Lỗi tạo bảng: " . $conn->error);
        }
        $sql = "CREATE TABLE IF NOT EXISTS comment_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    user_ip VARCHAR(45) NOT NULL,
    like_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (comment_id, user_ip)
";
$conn->query($sql);
        // Tạo file config
        $config = "<?php
        define('DB_HOST', '$host');
        define('DB_USER', '$user');
        define('DB_PASS', '$pass');
        define('DB_NAME', '$dbname');
        ";
        
        file_put_contents('config.php', $config);
        echo "Cài đặt thành công! Xóa file install.php sau khi hoàn tất";
        
    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cài đặt Database</title>
</head>
<body>
    <form method="post">
        <input type="text" name="host" placeholder="MySQL Host" required>
        <input type="text" name="user" placeholder="MySQL User" required>
        <input type="password" name="pass" placeholder="MySQL Password">
        <input type="text" name="dbname" placeholder="Database Name" required>
        <button type="submit">Cài đặt</button>
    </form>
</body>
</html>