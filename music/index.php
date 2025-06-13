<?php
session_start();

// Định nghĩa đường dẫn tuyệt đối để debug
$musicFile = realpath('../music/data/music.json');
$maintenanceFile = realpath('../music/data/maintenance.json');

if ($musicFile === false) {
    die('Đường dẫn music.json không hợp lệ. Vui lòng kiểm tra thư mục ../music/data/');
}

if ($maintenanceFile === false) {
    // Tạo file maintenance.json nếu chưa tồn tại
    $initialMaintenance = json_encode(['enabled' => false], JSON_PRETTY_PRINT);
    if (file_put_contents('../music/data/maintenance.json', $initialMaintenance) === false) {
        die('Không thể tạo file maintenance.json. Vui lòng kiểm tra quyền truy cập.');
    }
    $maintenanceData = ['enabled' => false];
} else {
    $maintenanceContent = file_get_contents($maintenanceFile);
    if ($maintenanceContent === false) {
        die('Không thể đọc file maintenance.json. Vui lòng kiểm tra quyền truy cập.');
    }
    $maintenanceData = json_decode($maintenanceContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON trong maintenance.json: ' . json_last_error_msg());
    }
    $maintenanceData = $maintenanceData ?: ['enabled' => false];
}

// Kiểm tra chế độ bảo trì
$isMaintenance = $maintenanceData['enabled'] ?? false;
$isAdmin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Nếu chế độ bảo trì bật và người dùng không phải admin, hiển thị thông báo bảo trì
if ($isMaintenance && !$isAdmin) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đang bảo trì</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background: #2a1a3d;
                color: #fff;
                font-family: Arial, sans-serif;
                text-align: center;
            }
            .maintenance-message {
                background: rgba(0, 0, 0, 0.8);
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 0 20px rgba(255, 105, 180, 0.5);
            }
            h1 {
                color: hotpink;
                text-shadow: 0 0 10px hotpink;
                margin-bottom: 20px;
            }
            p {
                color: #0ff;
                text-shadow: 0 0 8px #0ff;
            }
            a {
                color: hotpink;
                text-decoration: none;
                font-weight: bold;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="maintenance-message">
            <h1>Trang web đang bảo trì ⚙️</h1>
            <p>Chúng tôi đang nâng cấp hệ thống. Vui lòng quay lại sau!</p>
            <p><a href="../music/login/">Đăng nhập</a> nếu bạn là quản trị viên.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Kiểm tra và tạo file music.json nếu chưa tồn tại
if (!file_exists($musicFile)) {
    $initialData = json_encode([]);
    if (file_put_contents($musicFile, $initialData) === false) {
        die('Không thể tạo file music.json. Vui lòng kiểm tra quyền truy cập.');
    }
    $musicList = [];
} else {
    // Đọc và kiểm tra nội dung file
    $jsonContent = file_get_contents($musicFile);
    if ($jsonContent === false) {
        die('Không thể đọc file music.json. Vui lòng kiểm tra quyền truy cập.');
    }
    $musicList = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON trong music.json: ' . json_last_error_msg());
    }
    $musicList = $musicList ?: [];
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Eddy's musicList 🌸</title>
    <link rel="shortcut icon" href="https://i.imgur.com/885aRAi.jpeg" type="image/x-icon">
    <meta name="description" content="Eddy's - A teen dev passionate about HTML, CSS, JavaScript, and Python. Explore my projects and stats!">
    <meta name="keywords" content="Eddy, portfolio, web developer, anime, coding, HTML, CSS, JavaScript, Python, Node.js, full-stack, sakura, Minecraft">
    <meta name="author" content="Eddy Sakura">
    <meta property="og:title" content="Eddy's Profile 🌸">
    <meta property="og:description" content="Yo! I'm Eddy, a teen dev who loves coding and anime. Check out my projects, skills, and sakura-themed portfolio!">
    <meta property="og:image" content="https://i.imgur.com/BW7ZUkq.webp">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;700&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Pixelify Sans', 'M PLUS Rounded 1c', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: #2a1a3d url(https://i.imgur.com/v6jgeDg.png) center/cover fixed, #2a1a3d;
            background-size: 50%;
            background-repeat: repeat;
            background-blend-mode: overlay;
            color: #fff;
            overflow-x: hidden;
        }

        .playlist-panel {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 900;
            transition: transform 0.5s ease, opacity 0.5s ease;
            transform: translateY(0);
            opacity: 1;
        }

        .playlist-panel.hidden {
            transform: translateY(-100%);
            opacity: 0;
        }

        .playlist-content {
            background: linear-gradient(145deg, #1a1a2e, #2a2a4e);
            padding: 20px;
            border-radius: 15px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .player-section {
            width: 100%;
            padding: 15px;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow-y: auto;
        }

        .playlist-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-shrink: 0;
        }

        .playlist-header h2 {
            font-size: 1.6rem;
            color: hotpink;
            text-shadow: 0 0 12px hotpink;
        }

        .close-btn {
            background: hotpink;
            border: none;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.3s;
        }

        .close-btn:hover {
            transform: rotate(90deg);
        }

        .track-count {
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #0ff;
            text-shadow: 0 0 8px #0ff;
            text-align: center;
            flex-shrink: 0;
        }

        .show-player-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: hotpink;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1rem;
            z-index: 1000;
            display: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .search-container {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        #search-input, #genre-filter, .clear-filters {
            padding: 10px;
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s;
        }

        #search-input {
            flex-grow: 1;
        }

        #genre-filter {
            width: 120px;
        }

        .clear-filters {
            background: #0ff;
            cursor: pointer;
            color: #1a1a2e;
            font-weight: bold;
        }

        #search-input:focus, #genre-filter:focus, .clear-filters:hover {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 10px hotpink;
        }

        .now-playing {
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            flex-shrink: 0;
        }

        .now-playing.loading .title::after {
            content: ' (Đang tải...)';
            opacity: 0.7;
        }

        .now-playing-info .title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .now-playing-info .artist, .now-playing-info .genre {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .player-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
            padding: 10px 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            flex-shrink: 0;
        }

        .player-controls button {
            background: transparent;
            border: none;
            color: #0ff;
            font-size: 1.4rem;
            cursor: pointer;
            transition: all 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .player-controls button:hover {
            background: rgba(255, 105, 180, 0.3);
            transform: scale(1.1);
        }

        .player-controls button.active {
            color: hotpink;
            text-shadow: 0 0 10px hotpink;
        }

        .volume-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            flex-shrink: 0;
        }

        .volume-container button {
            background: transparent;
            border: none;
            color: #0ff;
            font-size: 1.2rem;
            cursor: pointer;
        }

        #volume-bar {
            flex-grow: 1;
            height: 5px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            cursor: pointer;
        }

        #volume-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: hotpink;
            border-radius: 50%;
        }

        #volume-bar::-moz-range-thumb {
            width: 14px;
            height: 14px;
            background: hotpink;
            border-radius: 50%;
            border: none;
        }

        .progress-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            flex-shrink: 0;
        }

        .time-display {
            font-size: 0.8rem;
            color: #ddd;
            min-width: 40px;
            text-align: center;
        }

        #progress-bar {
            flex-grow: 1;
            height: 5px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            cursor: pointer;
        }

        #progress-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: hotpink;
            border-radius: 50%;
        }

        #progress-bar::-moz-range-thumb {
            width: 14px;
            height: 14px;
            background: hotpink;
            border-radius: 50%;
            border: none;
        }

        .playlist-grid {
            flex-grow: 1;
            height: 100%;
            min-height: 200px;
            max-height: calc(100% - 300px); /* Bù cho header, now-playing, search, controls, margins */
            overflow-y: scroll;
            margin-top: 10px;
            z-index: 1;
            display: block;
            scrollbar-width: thin;
            scrollbar-color: #0ff rgba(255, 255, 255, 0.2);
            overscroll-behavior: contain;
            scroll-behavior: smooth;
        }

        .playlist-item {
            padding: 10px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
        }

        .playlist-item:hover {
            background: rgba(0, 255, 255, 0.2);
        }

        .playlist-item.active {
            background: hotpink;
            color: #1a1a2e;
            box-shadow: 0 0 10px hotpink;
        }

        .track-info .title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .track-info .artist, .track-info .genre {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        audio {
            display: none;
        }

        .notification {
            position: fixed;
            bottom: 30px;
            right: -300px; /* Ẩn ban đầu */
            background: rgba(0, 255, 255, 0.9);
            color: #1a1a2e;
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.5);
            border: 1px solid rgba(0, 255, 255, 0.3);
            z-index: 1000;
            transition: right 0.5s ease;
        }

        .devtools-warning {
            position: fixed;
            bottom: 80px;
            right: -300px; /* Ẩn ban đầu */
            background: rgba(200, 0, 0, 0.95);
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: bold;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.7), 0 0 25px rgba(255, 0, 0, 0.5);
            border: 2px solid;
            border-image: linear-gradient(to right, hotpink, #0ff) 1;
            z-index: 1000;
            transition: right 0.5s ease;
            animation: shake 0.3s ease-in-out;
        }

        .notification.show, .devtools-warning.show {
            right: 30px; /* Hiển thị khi có lớp .show */
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #0ff;
            border-radius: 5px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #00cccc;
        }

        @media (max-width: 600px) {
            .playlist-content {
                width: 95%;
                padding: 15px;
            }

            .player-section {
                width: 100%;
                max-height: 100%;
                overflow-y: auto;
            }

            .playlist-grid {
                height: 100%;
                max-height: calc(100% - 300px);
                min-height: 200px;
                overflow-y: scroll;
                scrollbar-width: thin;
                scrollbar-color: #0ff rgba(255, 255, 255, 0.2);
                overscroll-behavior: contain;
                scroll-behavior: smooth;
            }

            .playlist-header h2 {
                font-size: 1.4rem;
            }

            .close-btn {
                width: 25px;
                height: 25px;
            }

            .track-count {
                font-size: 0.8rem;
            }

            .now-playing-info .title {
                font-size: 1rem;
            }

            .now-playing-info .artist, .now-playing-info .genre {
                font-size: 0.7rem;
            }

            .player-controls {
                gap: 8px;
            }

            .player-controls button {
                font-size: 1.2rem;
                width: 35px;
                height: 35px;
            }

            .volume-container button {
                font-size: 1rem;
            }

            .time-display {
                font-size: 0.7rem;
                min-width: 35px;
            }
        }
    </style>
</head>
<body>
    <?php if ($isAdmin): ?>
        <a href="../music/admin/" class="admin-link">Quản trị</a>
        <a href="../music/setting/" class="settings-link">Cài đặt</a>
        <a href="../music/logout/" class="logout-link">Đăng xuất</a>
        <!-- Thêm nút bật/tắt bảo trì cho admin -->
        <a href="../music/maintenance_toggle.php" class="maintenance-link" style="color: #0ff; position: fixed; top: 10px; right: 10px;">
            <?php echo $isMaintenance ? 'Tắt bảo trì' : 'Bật bảo trì'; ?>
        </a>
    <?php else: ?>
        <a href="../music/login/" class="admin-link">Đăng nhập</a>
    <?php endif; ?>
    <button class="show-player-btn" aria-label="Hiển thị trình phát nhạc">🎵 Mở trình phát</button>
    <div class="playlist-panel">
        <div class="playlist-content">
            <div class="player-section">
                <div class="playlist-header">
                    <h2>Trình phát của Eddy</h2>
                    <button class="close-btn" aria-label="Đóng trình phát">X</button>
                </div>
                <div class="now-playing" id="now-playing">
                    <div class="now-playing-info">
                        <div class="title">Chưa chọn bài hát</div>
                        <div class="artist">Không có nghệ sĩ</div>
                        <div class="genre">Không có thể loại</div>
                    </div>
                </div>
                <div class="search-container">
                    <input type="text" id="search-input" placeholder="Tìm kiếm bài hát, nghệ sĩ..." aria-label="Tìm kiếm bài hát hoặc nghệ sĩ">
                    <select id="genre-filter" aria-label="Lọc theo thể loại">
                        <option value="">Tất cả thể loại</option>
                    </select>
                    <button class="clear-filters" aria-label="Xóa bộ lọc">Xóa</button>
                </div>
                <div class="player-controls">
                    <button title="Xáo trộn" id="shuffle-btn" aria-label="Bật/tắt xáo trộn">🔀</button>
                    <button title="Lặp lại" id="repeat-btn" aria-label="Bật/tắt lặp lại">🔁</button>
                    <button title="Lùi 10s" id="prev-btn" aria-label="Quay lại bài trước hoặc đầu bài">⏪</button>
                    <button title="Phát/Tạm dừng" id="play-btn" aria-label="Phát hoặc tạm dừng">▶️</button>
                    <button title="Bài tiếp theo" id="next-btn" aria-label="Chuyển sang bài tiếp theo">⏩</button>
                </div>
                <div class="volume-container">
                    <button id="volume-icon" aria-label="Tắt/bật âm thanh">🔊</button>
                    <input type="range" id="volume-bar" min="0" max="1" step="0.01" value="0.7" aria-label="Thanh điều chỉnh âm lượng">
                </div>
                <div class="progress-container">
                    <span class="time-display" id="current-time">0:00</span>
                    <input type="range" id="progress-bar" value="0" min="0" step="0.1" aria-label="Thanh tiến trình bài hát">
                    <span class="time-display" id="duration">0:00</span>
                </div>
                <div class="playlist-header">
                    <h2>Danh sách nhạc 🎶</h2>
                </div>
                <div class="track-count" id="track-count">Tổng cộng: 0 bài hát</div>
                <div class="playlist-grid" id="playlist-grid"></div>
            </div>
        </div>
    </div>

    <div class="notification" id="notification" role="alert" aria-live="polite"></div>
    <div class="devtools-warning" id="devtools-warning" role="alert" aria-live="assertive"></div>

    <script>
        // Chống Dev Tools và các hành động không mong muốn
        (function antiDevTools() {
            let devToolsOpen = false;
            const threshold = 160;
            const warningEl = document.getElementById('devtools-warning');
            let isWarning = false;
            let warningQueue = [];

            function showWarning(message) {
                warningQueue.push(`⚠️ ${message}`);
                if (!isWarning) displayNextWarning();
            }

            function displayNextWarning() {
                if (warningQueue.length === 0) return;
                isWarning = true;
                const message = warningQueue.shift();
                warningEl.textContent = message.length > 30 ? message.substring(0, 27) + '...' : message;
                warningEl.classList.add('show');
                setTimeout(() => {
                    warningEl.classList.remove('show');
                    isWarning = false;
                    displayNextWarning();
                }, 3000);
            }

            function checkDevTools() {
                const widthDiff = window.outerWidth - window.innerWidth > threshold;
                const heightDiff = window.outerHeight - window.innerHeight > threshold;
                const isDevTools = widthDiff || heightDiff;

                if (isDevTools && !devToolsOpen) {
                    devToolsOpen = true;
                    showWarning('Vui lòng không mở Dev Tools! Điều này có thể ảnh hưởng đến trải nghiệm.');
                } else if (!isDevTools && devToolsOpen) {
                    devToolsOpen = false;
                }
            }

            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                showWarning('Chuột phải đã bị vô hiệu hóa để bảo vệ nội dung!');
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'F12') {
                    e.preventDefault();
                    showWarning('Phím F12 đã bị vô hiệu hóa!');
                }
                if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.key === 'K')) {
                    e.preventDefault();
                    showWarning('Không được mở Dev Tools bằng phím tắt!');
                }
                if (e.ctrlKey && e.altKey && (e.key === 'I' || e.key === 'J')) {
                    e.preventDefault();
                    showWarning('Không được mở Dev Tools bằng phím tắt!');
                }
                if (e.ctrlKey && e.key === 'u') {
                    e.preventDefault();
                    showWarning('Xem mã nguồn đã bị vô hiệu hóa!');
                }
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    showWarning('Lưu trang đã bị vô hiệu hóa!');
                }
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    showWarning('In trang đã bị vô hiệu hóa!');
                }
            });

            setInterval(checkDevTools, 1000);
        })();

        const musicList = <?php echo json_encode($musicList); ?>;
        if (!musicList || musicList.length === 0) {
            console.warn('Danh sách nhạc rỗng hoặc không hợp lệ. Vui lòng kiểm tra music.json.');
        }

        const audio = new Audio();
        const playBtn = document.getElementById('play-btn');
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const repeatBtn = document.getElementById('repeat-btn');
        const shuffleBtn = document.getElementById('shuffle-btn');
        const progressBar = document.getElementById('progress-bar');
        const currentTimeEl = document.getElementById('current-time');
        const durationEl = document.getElementById('duration');
        const playlistGrid = document.getElementById('playlist-grid');
        const closeBtn = document.querySelector('.close-btn');
        const showPlayerBtn = document.querySelector('.show-player-btn');
        const searchInput = document.getElementById('search-input');
        const genreFilter = document.getElementById('genre-filter');
        const clearFiltersBtn = document.querySelector('.clear-filters');
        const volumeBar = document.getElementById('volume-bar');
        const volumeIcon = document.getElementById('volume-icon');
        const notification = document.getElementById('notification');
        const nowPlaying = document.getElementById('now-playing');
        const trackCountEl = document.getElementById('track-count');

        let currentTrackIndex = 0;
        let isRepeat = localStorage.getItem('isRepeat') === 'true';
        let isMuted = false;
        let isShuffled = localStorage.getItem('isShuffled') === 'true';
        let shuffledList = isShuffled ? JSON.parse(localStorage.getItem('shuffledList') || JSON.stringify([...musicList])) : [...musicList];
        let isNotifying = false;
        let notificationQueue = [];

        async function validateAudioUrl(url) {
            try {
                const response = await fetch(url, { method: 'HEAD' });
                return response.ok;
            } catch {
                return false;
            }
        }

        function populateGenreFilter() {
            const genres = [...new Set(musicList.map(track => track.genre || ''))];
            genreFilter.innerHTML = '<option value="">Tất cả thể loại</option>';
            genres.forEach(genre => {
                if (genre) {
                    const option = document.createElement('option');
                    option.value = genre;
                    option.textContent = genre;
                    genreFilter.appendChild(option);
                }
            });
        }

        function renderPlaylist(filter = '', genre = '') {
            playlistGrid.innerHTML = '';
            let filteredList = isShuffled ? shuffledList : musicList;
            console.log('Danh sách gốc:', musicList);
            console.log('Số bài hát gốc:', musicList.length);
            console.log('Danh sách xáo trộn:', shuffledList);
            console.log('Bộ lọc:', { filter, genre });
            console.log('Search input:', searchInput.value);
            console.log('Genre filter:', genreFilter.value);
            console.log('localStorage:', {
                searchFilter: localStorage.getItem('searchFilter'),
                genreFilter: localStorage.getItem('genreFilter')
            });
            console.log('Số bài hát trước lọc:', filteredList.length);

            if (filter) {
                filteredList = filteredList.filter(track =>
                    (track.title || '').toLowerCase().includes(filter.toLowerCase()) ||
                    (track.artist || '').toLowerCase().includes(filter.toLowerCase()));
            }
            if (genre) {
                filteredList = filteredList.filter(track => (track.genre || '') === genre);
            }

            console.log('Số bài hát sau lọc:', filteredList.length);
            console.log('Danh sách sau lọc:', filteredList);

            // Kiểm tra nếu danh sách bị lọc bất ngờ
            if (filteredList.length < musicList.length && !filter && !genre) {
                showNotification('Danh sách bị lọc bất ngờ. Vui lòng nhấn "Xóa" để hiển thị tất cả.');
            }

            trackCountEl.textContent = filter || genre
                ? `Hiển thị: ${filteredList.length}/${musicList.length} bài hát`
                : `Tổng cộng: ${musicList.length} bài hát`;

            if (filteredList.length === 0) {
                playlistGrid.innerHTML = '<div style="text-align: center; padding: 20px; color: #0ff;">Không tìm thấy bài hát phù hợp</div>';
                return;
            }

            // Kiểm tra trùng lặp
            const seen = new Set();
            filteredList.forEach((track, index) => {
                const key = `${track.title}|${track.artist}`;
                if (seen.has(key)) {
                    console.warn(`Bài hát trùng lặp tại index ${index}: ${key}`);
                }
                seen.add(key);

                const originalIndex = musicList.findIndex(
                    t => t.title === track.title && t.artist === track.artist
                );
                const item = document.createElement('div');
                item.className = 'playlist-item';
                item.setAttribute('aria-current', track.url === audio.src ? 'true' : 'false');
                if (track.url === audio.src) {
                    item.classList.add('active');
                }

                item.innerHTML = `
                    <div class="track-info">
                        <div class="title">${track.title || 'Không có tiêu đề'}</div>
                        <div class="artist">${track.artist || 'Không có nghệ sĩ'}</div>
                        <div class="genre">${track.genre || 'Không có thể loại'}</div>
                    </div>
                `;

                item.addEventListener('click', async () => {
                    if (!(await validateAudioUrl(track.url))) {
                        showNotification('Không thể tải bài hát. Chuyển sang bài tiếp theo.');
                        nextBtn.click();
                        return;
                    }
                    currentTrackIndex = originalIndex;
                    loadTrack();
                    audio.play().catch(() => {
                        showNotification('Không thể phát bài hát. Vui lòng thử lại.');
                    });
                    playBtn.textContent = '⏸️';
                    renderPlaylist(searchInput.value, genreFilter.value);
                });

                playlistGrid.appendChild(item);
                console.log(`Rendered item ${index + 1}: ${track.title}`);
            });

            // Debug chiều cao
            console.log('Chiều cao playlist-grid:', playlistGrid.offsetHeight);
            console.log('Chiều cao nội dung:', playlistGrid.scrollHeight);

            // Buộc cập nhật thanh cuộn
            playlistGrid.scrollTop = 0;
        }

        function updateNowPlaying() {
            const track = (isShuffled ? shuffledList : musicList)[currentTrackIndex] || {};
            nowPlaying.innerHTML = `
                <div class="now-playing-info">
                    <div class="title">${track.title || 'Chưa chọn bài hát'}</div>
                    <div class="artist">${track.artist || 'Không có nghệ sĩ'}</div>
                    <div class="genre">${track.genre || 'Không có thể loại'}</div>
                </div>
            `;
        }

        async function loadTrack() {
            const trackList = isShuffled ? shuffledList : musicList;
            if (!trackList[currentTrackIndex]) {
                showNotification('Không có bài hát để phát. Vui lòng kiểm tra danh sách.');
                return;
            }
            if (!(await validateAudioUrl(trackList[currentTrackIndex].url))) {
                showNotification('Không thể tải bài hát. Chuyển sang bài tiếp theo.');
                nextBtn.click();
                return;
            }
            audio.src = trackList[currentTrackIndex].url;
            audio.preload = 'metadata';
            updateNowPlaying();
            renderPlaylist(searchInput.value, genreFilter.value);
        }

        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
        }

        function updateProgress() {
            if (!isNaN(audio.duration)) {
                progressBar.value = audio.currentTime;
                currentTimeEl.textContent = formatTime(audio.currentTime);
                durationEl.textContent = formatTime(audio.duration);
            }
        }

        const notificationMessages = [
            () => `Bạn đang nghe bài: ${(isShuffled ? shuffledList : musicList)[currentTrackIndex]?.title || 'Không xác định'} - ${(isShuffled ? shuffledList : musicList)[currentTrackIndex]?.artist || 'Không xác định'}`,
            () => 'Cảm ơn đã truy cập web!',
            () => 'Khám phá thêm những bài hát mới trong danh sách!',
            () => 'Chúc bạn có những phút giây thư giãn với âm nhạc!'
        ];

        function showNotification(message) {
            notificationQueue.push(message);
            if (!isNotifying) displayNextNotification();
        }

        function displayNextNotification() {
            if (notificationQueue.length === 0) return;
            isNotifying = true;
            const message = notificationQueue.shift();
            const displayMessage = typeof message === 'string'
                ? (message.length > 30 ? message.substring(0, 27) + '...' : message)
                : notificationMessages[Math.floor(Math.random() * notificationMessages.length)]();
            notification.textContent = displayMessage;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
                isNotifying = false;
                displayNextNotification();
            }, 3000);
        }

        function scheduleNotifications() {
            const intervals = [10000, 30000, 60000, 120000];
            setInterval(() => {
                if (!isNotifying) {
                    showNotification();
                }
            }, intervals[Math.floor(Math.random() * intervals.length)]);
        }

        playBtn.addEventListener('click', async () => {
            if (audio.paused) {
                if (!(await validateAudioUrl(audio.src))) {
                    showNotification('Không thể tải bài hát. Chuyển sang bài tiếp theo.');
                    nextBtn.click();
                    return;
                }
                audio.play().catch(() => {
                    showNotification('Không thể phát bài hát. Vui lòng thử lại.');
                });
                playBtn.textContent = '⏸️';
            } else {
                audio.pause();
                playBtn.textContent = '▶️';
            }
        });

        nextBtn.addEventListener('click', async () => {
            const trackList = isShuffled ? shuffledList : musicList;
            currentTrackIndex = (currentTrackIndex + 1) % trackList.length;
            await loadTrack();
            audio.play().catch(() => {
                showNotification('Không thể phát bài hát. Vui lòng thử lại.');
            });
            playBtn.textContent = '⏸️';
            showNotification(`Chuyển sang: ${trackList[currentTrackIndex]?.title || 'Không xác định'}`);
        });

        prevBtn.addEventListener('click', async () => {
            const trackList = isShuffled ? shuffledList : musicList;
            if (audio.currentTime > 3) {
                audio.currentTime = 0;
            } else {
                currentTrackIndex = (currentTrackIndex - 1 + trackList.length) % trackList.length;
                await loadTrack();
                audio.play().catch(() => {
                    showNotification('Không thể phát bài hát. Vui lòng thử lại.');
                });
                playBtn.textContent = '⏸️';
                showNotification(`Chuyển sang: ${trackList[currentTrackIndex]?.title || 'Không xác định'}`);
            }
        });

        repeatBtn.addEventListener('click', () => {
            isRepeat = !isRepeat;
            audio.loop = isRepeat;
            repeatBtn.classList.toggle('active', isRepeat);
            localStorage.setItem('isRepeat', isRepeat);
        });

        shuffleBtn.addEventListener('click', () => {
            isShuffled = !isShuffled;
            shuffleBtn.classList.toggle('active', isShuffled);
            if (isShuffled) {
                shuffledList = [...musicList].sort(() => Math.random() - 0.5);
                currentTrackIndex = 0;
                localStorage.setItem('shuffledList', JSON.stringify(shuffledList));
            } else {
                currentTrackIndex = musicList.findIndex(
                    track => track.url === audio.src
                );
                if (currentTrackIndex === -1) currentTrackIndex = 0;
            }
            localStorage.setItem('isShuffled', isShuffled);
            loadTrack();
            renderPlaylist(searchInput.value, genreFilter.value);
        });

        progressBar.addEventListener('input', () => {
            audio.currentTime = progressBar.value;
        });

        volumeBar.addEventListener('input', () => {
            audio.volume = volumeBar.value;
            isMuted = audio.volume === 0;
            updateVolumeIcon();
            localStorage.setItem('volume', audio.volume);
        });

        volumeIcon.addEventListener('click', () => {
            isMuted = !isMuted;
            if (isMuted) {
                audio.volume = 0;
                volumeBar.value = 0;
            } else {
                audio.volume = volumeBar.value || parseFloat(localStorage.getItem('volume') || 0.7);
                volumeBar.value = audio.volume;
            }
            updateVolumeIcon();
            localStorage.setItem('volume', audio.volume);
        });

        function updateVolumeIcon() {
            if (audio.volume === 0 || isMuted) {
                volumeIcon.textContent = '🔇';
            } else if (audio.volume < 0.5) {
                volumeIcon.textContent = '🔈';
            } else {
                volumeIcon.textContent = '🔊';
            }
        }

        let searchTimeout;
        function updatePlaylist() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                localStorage.setItem('searchFilter', searchInput.value);
                localStorage.setItem('genreFilter', genreFilter.value);
                renderPlaylist(searchInput.value, genreFilter.value);
            }, 300);
        }

        searchInput.addEventListener('input', updatePlaylist);
        genreFilter.addEventListener('change', updatePlaylist);

        clearFiltersBtn.addEventListener('click', () => {
            searchInput.value = '';
            genreFilter.value = '';
            localStorage.removeItem('searchFilter');
            localStorage.removeItem('genreFilter');
            renderPlaylist();
        });

        audio.addEventListener('error', () => {
            showNotification('Không thể tải bài hát. Chuyển sang bài tiếp theo.');
            nextBtn.click();
        });

        audio.addEventListener('loadedmetadata', () => {
            progressBar.max = audio.duration;
            durationEl.textContent = formatTime(audio.duration);
        });

        audio.addEventListener('timeupdate', updateProgress);

        audio.addEventListener('waiting', () => nowPlaying.classList.add('loading'));
        audio.addEventListener('playing', () => nowPlaying.classList.remove('loading'));

        audio.addEventListener('ended', async () => {
            const trackList = isShuffled ? shuffledList : musicList;
            if (!isRepeat) {
                currentTrackIndex = (currentTrackIndex + 1) % trackList.length;
                await loadTrack();
                audio.play().catch(() => {
                    showNotification('Không thể phát bài hát. Vui lòng thử lại.');
                });
                showNotification(`Chuyển sang: ${trackList[currentTrackIndex]?.title || 'Không xác định'}`);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                playBtn.click();
                e.preventDefault();
            } else if (e.code === 'ArrowRight') {
                nextBtn.click();
            } else if (e.code === 'ArrowLeft') {
                prevBtn.click();
            } else if (e.code === 'KeyM') {
                volumeIcon.click();
            } else if (e.code === 'KeyS') {
                shuffleBtn.click();
            } else if (e.code === 'Escape') {
                closeBtn.click();
            }
        });

        closeBtn.addEventListener('click', () => {
            document.querySelector('.playlist-panel').classList.add('hidden');
            showPlayerBtn.style.display = 'block';
        });

        showPlayerBtn.addEventListener('click', () => {
            document.querySelector('.playlist-panel').classList.remove('hidden');
            showPlayerBtn.style.display = 'none';
        });

        // Khởi tạo
        audio.volume = parseFloat(localStorage.getItem('volume') || 0.7);
        volumeBar.value = audio.volume;
        audio.preload = 'none';
        repeatBtn.classList.toggle('active', isRepeat);
        audio.loop = isRepeat;
        shuffleBtn.classList.toggle('active', isShuffled);
        populateGenreFilter();

        // Xóa bộ lọc và render toàn bộ danh sách
        localStorage.removeItem('searchFilter');
        localStorage.removeItem('genreFilter');
        searchInput.value = '';
        genreFilter.value = '';
        renderPlaylist();
        if (musicList.length > 0) {
            loadTrack();
        }
        updateVolumeIcon();
        scheduleNotifications();
    </script>
    <noscript>
        <p style="color: red; text-align: center;">
            ⚠️ Trình duyệt của bạn không hỗ trợ JavaScript hoặc JavaScript đã bị tắt.<br>
            Vui lòng bật JavaScript để tiếp tục!
        </p>
    </noscript>
</body>
</html>