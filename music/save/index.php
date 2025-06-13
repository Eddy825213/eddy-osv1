<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: ../../music/login');
    exit;
}

$notification = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['music_list'])) {
    // Vệ sinh dữ liệu JSON
    $musicListInput = trim($_POST['music_list']);
    $musicList = json_decode($musicListInput, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        // Kiểm tra định dạng từng bài hát
        $valid = true;
        foreach ($musicList as $track) {
            if (!isset($track['title'], $track['artist'], $track['genre'], $track['url']) ||
                !filter_var($track['url'], FILTER_VALIDATE_URL) ||
                !preg_match('/\.(mp3|wav|ogg)$/i', $track['url'])) {
                $valid = false;
                break;
            }
        }

        if ($valid) {
            file_put_contents('../../music/data/music.json', json_encode($musicList, JSON_PRETTY_PRINT));
            $notification = 'Lưu danh sách nhạc thành công!';
        } else {
            $notification = 'Lỗi: Một hoặc nhiều bài hát có định dạng không hợp lệ!';
        }
    } else {
        $notification = 'Lỗi: Định dạng JSON không hợp lệ!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lưu danh sách nhạc 🌸</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;700&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #2a1a3d;
            color: #fff;
            font-family: 'Pixelify Sans', 'M PLUS Rounded 1c', sans-serif;
            padding: 20px;
            text-align: center;
        }

        .notification {
            background: rgba(0, 255, 255, 0.9);
            color: #1a1a2e;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.5);
            margin: 20px auto;
            max-width: 600px;
        }

        a.back-link {
            color: #0ff;
            text-decoration: none;
        }

        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="notification"><?php echo htmlspecialchars($notification); ?></div>
    <a href="../../music" class="back-link">Quay lại trình phát</a>
    <script>
        setTimeout(() => {
            window.location.href = '../../music';
        }, 3000);
    </script>
    <script src="../anti/antidev.js"></script>
    <script src="../assets/main.js"></script>
</body>
</html>