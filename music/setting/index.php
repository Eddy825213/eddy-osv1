<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: ../../music/login');
    exit;
}

// Đọc danh sách nhạc từ music.json
$musicList = json_decode(file_get_contents('.././data/music.json'), true) ?: [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt danh sách nhạc 🌸</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;700&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #2a1a3d;
            color: #fff;
            font-family: 'Pixelify Sans', 'M PLUS Rounded 1c', sans-serif;
            padding: 20px;
        }

        .settings-container {
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1a1a2e, #2a2a4e);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }

        h1 {
            text-align: center;
            color: hotpink;
            text-shadow: 0 0 12px hotpink;
        }

        textarea {
            width: 100%;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        button {
            background: hotpink;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        button:hover {
            background: #ff1493;
        }

        a.back-link, a.logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0ff;
            text-decoration: none;
        }

        a.back-link:hover, a.logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h1>Cài đặt danh sách nhạc 🎶</h1>
        <a href="/../music/" class="back-link">Quay lại trình phát</a>
        <a href="/../music/logout" class="logout-link">Đăng xuất</a>
        <form method="POST" action="/../music/save/">
            <textarea name="music_list" placeholder="Nhập danh sách nhạc (định dạng JSON)"><?php echo htmlspecialchars(json_encode($musicList, JSON_PRETTY_PRINT)); ?></textarea>
            <button type="submit">Lưu</button>
        </form>
    </div>
    <script src="../anti/antidev.js"></script>
    <script src="../assets/main.js"></script>
</body>
</html>