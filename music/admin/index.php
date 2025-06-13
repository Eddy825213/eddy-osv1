<?php
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    session_regenerate_id(true);
}

define('MAINTENANCE_JSON_PATH', __DIR__ . '/../data/maintenance.json');
$maintenanceData = ['enabled' => false];

if (!file_exists(MAINTENANCE_JSON_PATH)) {
    $initialMaintenance = json_encode(['enabled' => false], JSON_PRETTY_PRINT);
    if (file_put_contents(MAINTENANCE_JSON_PATH, $initialMaintenance) === false) {
        die('Không thể tạo file maintenance.json. Vui lòng kiểm tra quyền truy cập.');
    }
} else {
    $maintenanceContent = file_get_contents(MAINTENANCE_JSON_PATH);
    if ($maintenanceContent === false) {
        die('Không thể đọc file maintenance.json. Vui lòng kiểm tra quyền truy cập.');
    }
    $maintenanceData = json_decode($maintenanceContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON trong maintenance.json: ' . json_last_error_msg());
    }
    $maintenanceData = $maintenanceData ?: ['enabled' => false];
}

$isMaintenance = $maintenanceData['enabled'] ?? false;
$isAdmin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

define('USER_JSON_PATH', __DIR__ . '/../data/user.json');
$userList = [];
if (file_exists(USER_JSON_PATH)) {
    $jsonContent = file_get_contents(USER_JSON_PATH);
    if ($jsonContent === false) {
        die('Không thể đọc file user.json!');
    }
    $userList = json_decode($jsonContent, true) ?: [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON trong user.json: ' . json_last_error_msg());
    }
}

$isAdminRole = false;
if ($isAdmin && isset($_SESSION['email'])) {
    foreach ($userList as $user) {
        if ($user['email'] === $_SESSION['email'] && isset($user['role']) && $user['role'] === 'admin') {
            $isAdminRole = true;
            break;
        }
    }
}

if ($isMaintenance && !$isAdminRole) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đang bảo trì</title>
        <style>
            body { margin: 0; padding: 0; height: 100vh; display: flex; justify-content: center; align-items: center; background: #2a1a3d; color: #fff; font-family: Arial, sans-serif; text-align: center; }
            .maintenance-message { background: rgba(0, 0, 0, 0.8); padding: 30px; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 105, 180, 0.5); }
            h1 { color: hotpink; text-shadow: 0 0 10px hotpink; margin-bottom: 20px; }
            p { color: #0ff; text-shadow: 0 0 8px #0ff; }
            a { color: hotpink; text-decoration: none; font-weight: bold; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="maintenance-message">
            <h1>Trang web đang bảo trì ⚙️</h1>
            <p>Chúng tôi đang nâng cấp hệ thống. Vui lòng quay lại sau!</p>
            <p><a href="../../music/login/">Đăng nhập</a> nếu bạn là quản trị viên.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (!$isAdmin || !$isAdminRole) {
    header('Location: ../../music/login/');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

define('MUSIC_JSON_PATH', __DIR__ . '/../data/music.json');
define('BACKUP_DIR', __DIR__ . '/../data/backups/');
$musicList = [];
$notification = '';

if (!file_exists(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0755, true);
}

if (is_readable(MUSIC_JSON_PATH)) {
    $musicList = json_decode(file_get_contents(MUSIC_JSON_PATH), true) ?: [];
    if ($musicList === null) {
        $notification = 'Lỗi: Tệp music.json không hợp lệ!';
        $musicList = [];
        file_put_contents(MUSIC_JSON_PATH, json_encode($musicList, JSON_PRETTY_PRINT));
    }
} else {
    $notification = 'Lỗi: Không thể đọc tệp music.json!';
    file_put_contents(MUSIC_JSON_PATH, json_encode($musicList, JSON_PRETTY_PRINT));
}

if (is_readable(USER_JSON_PATH)) {
    $jsonContent = file_get_contents(USER_JSON_PATH);
    if ($jsonContent === false) {
        $notification = 'Lỗi: Không thể đọc file user.json!';
    } else {
        $userList = json_decode($jsonContent, true) ?: [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            $notification = 'Lỗi: Tệp user.json không hợp lệ: ' . json_last_error_msg();
            $userList = [];
            file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT));
        }
    }
} else {
    $notification = 'Tạo file user.json mới...';
    if (!is_dir(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0755, true);
    }
    if (file_put_contents(USER_JSON_PATH, json_encode([])) === false) {
        $notification = 'Lỗi: Không thể tạo file user.json!';
    } else {
        $userList = [];
    }
}

if (empty($userList)) {
    $notification = 'Không tìm thấy dữ liệu người dùng! Vui lòng đăng ký tài khoản trước.';
}

// Hàm sao lưu dữ liệu
function createBackup($sourcePath, $backupDir) {
    $backupFile = $backupDir . 'music_backup_' . date('Ymd_His') . '.json';
    if (file_exists($sourcePath) && is_readable($sourcePath)) {
        $content = file_get_contents($sourcePath);
        if ($content !== false) {
            return file_put_contents($backupFile, $content) !== false;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (isset($_POST['action'])) {
        // Sao lưu trước khi thực hiện hành động
        if (!createBackup(MUSIC_JSON_PATH, BACKUP_DIR)) {
            $notification = 'Lỗi: Không thể tạo bản sao lưu!';
        }

        if ($_POST['action'] === 'add') {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $artist = filter_input(INPUT_POST, 'artist', FILTER_SANITIZE_SPECIAL_CHARS);
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

            if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/\.(mp3|wav|ogg)$/i', $url)) {
                $notification = 'Lỗi: URL không hợp lệ hoặc không phải định dạng âm thanh!';
            } elseif (empty($title) || empty($artist)) {
                $notification = 'Lỗi: Vui lòng điền đầy đủ thông tin!';
            } else {
                $newTrack = [
                    'title' => $title,
                    'artist' => $artist,
                    'url' => $url
                ];
                if (in_array($url, array_column($musicList, 'url'))) {
                    $notification = 'Lỗi: URL đã tồn tại trong danh sách!';
                } else {
                    $musicList[] = $newTrack;
                    if (file_put_contents(MUSIC_JSON_PATH, json_encode($musicList, JSON_PRETTY_PRINT)) === false) {
                        $notification = 'Lỗi: Không thể ghi vào tệp music.json!';
                    } else {
                        $notification = 'Thêm bài hát thành công!';
                    }
                }
            }
        } elseif ($_POST['action'] === 'edit') {
            $index = filter_input(INPUT_POST, 'index', FILTER_SANITIZE_NUMBER_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $artist = filter_input(INPUT_POST, 'artist', FILTER_SANITIZE_SPECIAL_CHARS);
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

            if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/\.(mp3|wav|ogg)$/i', $url)) {
                $notification = 'Lỗi: URL không hợp lệ hoặc không phải định dạng âm thanh!';
            } elseif (empty($title) || empty($artist)) {
                $notification = 'Lỗi: Vui lòng điền đầy đủ thông tin!';
            } elseif (isset($musicList[$index])) {
                $newTrack = [
                    'title' => $title,
                    'artist' => $artist,
                    'url' => $url
                ];
                $existingUrls = array_column($musicList, 'url');
                unset($existingUrls[$index]);
                if (in_array($url, $existingUrls)) {
                    $notification = 'Lỗi: URL đã tồn tại trong danh sách!';
                } else {
                    $musicList[$index] = $newTrack;
                    if (file_put_contents(MUSIC_JSON_PATH, json_encode($musicList, JSON_PRETTY_PRINT)) === false) {
                        $notification = 'Lỗi: Không thể ghi vào tệp music.json!';
                    } else {
                        $notification = 'Chỉnh sửa bài hát thành công!';
                    }
                }
            } else {
                $notification = 'Lỗi: Bài hát không tồn tại!';
            }
        } elseif ($_POST['action'] === 'delete') {
            $index = filter_input(INPUT_POST, 'index', FILTER_SANITIZE_NUMBER_INT);
            if (isset($musicList[$index])) {
                array_splice($musicList, $index, 1);
                if (file_put_contents(MUSIC_JSON_PATH, json_encode($musicList, JSON_PRETTY_PRINT)) === false) {
                    $notification = 'Lỗi: Không thể ghi vào tệp music.json!';
                } else {
                    $notification = 'Xóa bài hát thành công!';
                }
            } else {
                $notification = 'Lỗi: Bài hát không tồn tại!';
            }
        } elseif ($_POST['action'] === 'edit_password') {
            $userIndex = filter_input(INPUT_POST, 'user_index', FILTER_SANITIZE_NUMBER_INT);
            $newPassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            if (isset($userList[$userIndex])) {
                $userList[$userIndex]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                if (file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT)) === false) {
                    $notification = 'Lỗi: Không thể ghi vào tệp user.json!';
                } else {
                    $notification = 'Đổi mật khẩu thành công!';
                }
            } else {
                $notification = 'Lỗi: Người dùng không tồn tại!';
            }
        } elseif ($_POST['action'] === 'delete_user') {
            $userIndex = filter_input(INPUT_POST, 'user_index', FILTER_SANITIZE_NUMBER_INT);
            if (isset($userList[$userIndex])) {
                array_splice($userList, $userIndex, 1);
                if (file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT)) === false) {
                    $notification = 'Lỗi: Không thể ghi vào tệp user.json!';
                } else {
                    $notification = 'Xóa người dùng thành công!';
                }
            } else {
                $notification = 'Lỗi: Người dùng không tồn tại!';
            }
        } elseif ($_POST['action'] === 'set_role') {
            $userIndex = filter_input(INPUT_POST, 'user_index', FILTER_SANITIZE_NUMBER_INT);
            $newRole = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
            if (isset($userList[$userIndex])) {
                if ($newRole === 'admin' || $newRole === 'user') {
                    $userList[$userIndex]['role'] = $newRole;
                    if (file_put_contents(USER_JSON_PATH, json_encode($userList, JSON_PRETTY_PRINT)) === false) {
                        $notification = 'Lỗi: Không thể ghi vào tệp user.json!';
                    } else {
                        $notification = 'Cập nhật vai trò thành công!';
                    }
                } else {
                    $notification = 'Lỗi: Vai trò không hợp lệ!';
                }
            } else {
                $notification = 'Lỗi: Người dùng không tồn tại!';
            }
        }
    } else {
        $notification = 'Lỗi: Hành động không hợp lệ!';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notification = 'Lỗi: Token CSRF không hợp lệ!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị danh sách nhạc 🌸</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;700&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #2a1a3d;
            color: #fff;
            font-family: 'Pixelify Sans', 'M PLUS Rounded 1c', sans-serif;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        .admin-container {
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
        .form-container {
            margin: 20px 0;
        }
        .form-container input, .form-container button {
            padding: 10px;
            margin: 5px;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
        }
        .form-container input {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            width: calc(100% - 20px);
        }
        .form-container button {
            background: hotpink;
            color: #fff;
            cursor: pointer;
        }
        .form-container button:hover {
            background: #ff1493;
        }
        .track-list, .user-list {
            margin-top: 20px;
        }
        .track-item, .user-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .track-item button, .user-item button {
            background: #0ff;
            color: #1a1a2e;
            padding: 8px 15px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }
        .track-item button:hover, .user-item button:hover {
            background: #00cccc;
        }
        .notification {
            position: fixed;
            bottom: 30px;
            right: -300px;
            background: rgba(0, 255, 255, 0.9);
            color: #1a1a2e;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.5);
            transition: right 0.5s ease;
            z-index: 1000;
        }
        .notification.show {
            right: 30px;
        }
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: #2a2a4e;
            padding: 20px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
        }
        .sakura {
            position: absolute;
            width: 10px;
            height: 10px;
            background: hotpink;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: sakura-fall 10s linear infinite;
        }
        @keyframes sakura-fall {
            0% { transform: translateY(-100vh) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
        a.back-link, a.logout-link, a.maintenance-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0ff;
            text-decoration: none;
        }
        a.back-link:hover, a.logout-link:hover, a.maintenance-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .form-container input, .form-container button {
                font-size: 0.9rem;
                padding: 8px;
            }
            .track-item, .user-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .track-item button, .user-item button {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sakura" style="left: 10%; animation-delay: 0s;"></div>
    <div class="sakura" style="left: 30%; animation-delay: 1s;"></div>
    <div class="sakura" style="left: 50%; animation-delay: 2s;"></div>
    <div class="sakura" style="left: 70%; animation-delay: 3s;"></div>
    <div class="sakura" style="left: 90%; animation-delay: 4s;"></div>

    <div class="admin-container">
        <h1>Quản trị danh sách nhạc 🎶</h1>
        <a href="../" class="back-link">Quay lại trình phát</a>
        <a href="../../logout/" class="logout-link">Đăng xuất</a>
        <a href="../maintenance_toggle.php" class="maintenance-link">
            <?php echo $isMaintenance ? 'Tắt bảo trì' : 'Bật bảo trì'; ?>
        </a>

        <div class="form-container">
            <h2>Thêm bài hát mới</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="title" placeholder="Tiêu đề bài hát" required>
                <input type="text" name="artist" placeholder="Nghệ sĩ" required>
                <input type="url" name="url" placeholder="URL bài hát" required>
                <button type="submit">Thêm</button>
            </form>
        </div>

        <div class="form-container">
            <input type="text" id="search" placeholder="Tìm kiếm bài hát..." oninput="filterTracks()">
        </div>

        <div class="track-list">
            <h2>Danh sách bài hát</h2>
            <?php foreach ($musicList as $index => $track): ?>
                <div class="track-item">
                    <div>
                        <strong><?php echo htmlspecialchars($track['title']); ?></strong> - 
                        <?php echo htmlspecialchars($track['artist']); ?>
                    </div>
                    <div>
                        <button onclick="editTrack(<?php echo $index; ?>, '<?php echo htmlspecialchars($track['title']); ?>', '<?php echo htmlspecialchars($track['artist']); ?>', '<?php echo htmlspecialchars($track['url']); ?>')">Chỉnh sửa</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit">Xóa</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="user-list">
            <h2>Danh sách người dùng</h2>
            <?php foreach ($userList as $index => $user): ?>
                <div class="user-item">
                    <div>
                        <strong><?php echo htmlspecialchars($user['name']); ?></strong> - 
                        <?php echo htmlspecialchars($user['email']); ?> (Vai trò: <?php echo htmlspecialchars($user['role'] ?? 'user'); ?>)
                    </div>
                    <div>
                        <button onclick="editPassword(<?php echo $index; ?>, '<?php echo htmlspecialchars($user['email']); ?>')">Đổi mật khẩu</button>
                        <button onclick="setRole(<?php echo $index; ?>, '<?php echo htmlspecialchars($user['email']); ?>')">Set Role</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_index" value="<?php echo $index; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit">Xóa</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="edit-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <h2>Chỉnh sửa bài hát</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="index" id="edit-index">
                    <input type="text" name="title" id="edit-title" placeholder="Tiêu đề bài hát" required>
                    <input type="text" name="artist" id="edit-artist" placeholder="Nghệ sĩ" required>
                    <input type="url" name="url" id="edit-url" placeholder="URL bài hát" required>
                    <button type="submit">Lưu</button>
                    <button type="button" onclick="closeModal()">Hủy</button>
                </form>
            </div>
        </div>

        <div id="password-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <h2>Chỉnh sửa mật khẩu</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_password">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="user_index" id="edit-user-index">
                    <input type="password" name="password" id="edit-password" placeholder="Mật khẩu mới" required>
                    <button type="submit">Lưu</button>
                    <button type="button" onclick="closeModal()">Hủy</button>
                </form>
            </div>
        </div>

        <div id="role-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <h2>Cập nhật vai trò</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="set_role">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="user_index" id="role-user-index">
                    <label for="role">Chọn vai trò:</label>
                    <select name="role" id="role">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <button type="submit">Lưu</button>
                    <button type="button" onclick="closeModal()">Hủy</button>
                </form>
            </div>
        </div>
    </div>

    <?php if ($notification): ?>
        <div class="notification show"><?php echo htmlspecialchars($notification); ?></div>
    <?php endif; ?>

    <script>
        function editTrack(index, title, artist, url) {
            document.getElementById('edit-modal').style.display = 'flex';
            document.getElementById('edit-index').value = index;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-artist').value = artist;
            document.getElementById('edit-url').value = url;
        }

        function editPassword(index, email) {
            document.getElementById('password-modal').style.display = 'flex';
            document.getElementById('edit-user-index').value = index;
            document.getElementById('edit-password').value = '';
        }

        function setRole(index, email) {
            document.getElementById('role-modal').style.display = 'flex';
            document.getElementById('role-user-index').value = index;
        }

        function closeModal() {
            document.getElementById('edit-modal').style.display = 'none';
            document.getElementById('password-modal').style.display = 'none';
            document.getElementById('role-modal').style.display = 'none';
        }

        function filterTracks() {
            const search = document.getElementById('search').value.toLowerCase();
            const tracks = document.querySelectorAll('.track-item');
            tracks.forEach(track => {
                const title = track.querySelector('strong').textContent.toLowerCase();
                const artist = track.textContent.toLowerCase().split('-')[1]?.trim() || '';
                track.style.display = (title.includes(search) || artist.includes(search)) ? 'flex' : 'none';
            });
        }

        const notification = document.querySelector('.notification');
        if (notification) {
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        const messages = [
            'Cảm ơn đã sử dụng trình quản lý nhạc!',
            'Hãy thêm bài hát yêu thích của bạn!',
            'Website được thiết kế bởi Eddy!'
        ];
        setInterval(() => {
            const notification = document.createElement('div');
            notification.className = 'notification show';
            notification.textContent = messages[Math.floor(Math.random() * messages.length)];
            document.body.appendChild(notification);
            setTimeout(() => notification.classList.remove('show'), 3000);
            setTimeout(() => notification.remove(), 3500);
        }, Math.random() * (120000 - 5000) + 5000);
    </script>
</body>
</html>