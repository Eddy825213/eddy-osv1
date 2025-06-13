<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $dbname = $_POST['dbname'];
    
    try {
        // Kết nối database
        $conn = new mysqli($host, $user, $pass);
        if ($conn->connect_error) {
            throw new Exception("Kết nối thất bại: " . $conn->connect_error);
        }

        // Tạo database và tables
        $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
        $conn->select_db($dbname);

        // Tạo bảng comments và likes
        $tables = [
            "CREATE TABLE IF NOT EXISTS comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL DEFAULT 'Khách',
                content TEXT NOT NULL,
                timestamp BIGINT NOT NULL,
                likes INT DEFAULT 0
            )",
            "CREATE TABLE IF NOT EXISTS comment_likes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                comment_id INT NOT NULL,
                user_ip VARCHAR(45) NOT NULL,
                like_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (comment_id, user_ip)
            )"
        ];

        foreach ($tables as $sql) {
            if (!$conn->query($sql)) {
                throw new Exception("Lỗi tạo bảng: " . $conn->error);
            }
        }

        // Tạo file config
        $config = "<?php\n";
        $config .= "define('DB_HOST', '$host');\n";
        $config .= "define('DB_USER', '$user');\n";
        $config .= "define('DB_PASS', '$pass');\n";
        $config .= "define('DB_NAME', '$dbname');\n";
        $config .= "define('RECAPTCHA_SITEKEY', '6Leo2SorAAAAAKfl13Hk-KrxT56tnIlGb0K_8qCi');\n"; // Thay bằng key của bạn
        $config .= "define('RECAPTCHA_SECRET', '6Leo2SorAAAAAO-TZq6HHyxLCJ5XgBVKB_gZ6nvw');\n"; // Thay bằng key của bạn
        $config .= "define('LIKE_LIMIT', 3);\n";
        file_put_contents('config.php', $config);

        echo "Cài đặt thành công! Hãy xóa file install.php ngay!";
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
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; }
        input { display: block; width: 100%; margin: 10px 0; padding: 8px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Cài đặt Database</h2>
    <form method="post">
        <input type="text" name="host" placeholder="MySQL Host (ví dụ: localhost)" required>
        <input type="text" name="user" placeholder="MySQL User (ví dụ: root)" required>
        <input type="password" name="pass" placeholder="MySQL Password">
        <input type="text" name="dbname" placeholder="Tên Database" required>
        <button type="submit">Cài đặt</button>
    </form>
</body>
</html>