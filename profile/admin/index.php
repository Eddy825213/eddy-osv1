<?php
session_set_cookie_params([
    'httponly' => true,
    'secure' => false,
    'samesite' => 'Strict'
]);

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    session_regenerate_id(true);
}

define('USER_JSON_PATH', __DIR__ . '/../data/users.json');
define('PROFILE_JSON_PATH', __DIR__ . '/../data/profiles.json');
define('PROFILE_BASE_PATH', __DIR__ . '/../../profile/');

$userList = [];
if (file_exists(USER_JSON_PATH)) {
    $jsonContent = file_get_contents(USER_JSON_PATH);
    if ($jsonContent === false) {
        die('Không thể đọc file users.json!');
    }
    $userList = json_decode($jsonContent, true) ?: [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Lỗi phân tích JSON trong users.json: ' . json_last_error_msg());
    }
}

$isAdmin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$isAdminRole = false;
if ($isAdmin && isset($_SESSION['email'])) {
    foreach ($userList as $user) {
        if ($user['email'] === $_SESSION['email'] && isset($user['role']) && $user['role'] === 'admin') {
            $isAdminRole = true;
            break;
        }
    }
}

if (!$isAdmin || !$isAdminRole) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        foreach ($userList as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
        $notification = 'Email hoặc mật khẩu không đúng!';
    }
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đăng nhập</title>
        <style>
            body { background: #2a1a3d; color: #fff; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .login-container { background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 10px; text-align: center; }
            input { padding: 10px; margin: 5px; border-radius: 5px; border: none; }
            button { padding: 10px 20px; background: hotpink; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
            button:hover { background: #ff1493; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Đăng nhập</h2>
            <?php if (isset($notification)) echo '<p style="color: red;">' . htmlspecialchars($notification) . '</p>'; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <input type="hidden" name="login" value="1">
                <button type="submit">Đăng nhập</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$profileList = [];
if (is_readable(PROFILE_JSON_PATH)) {
    $jsonContent = file_get_contents(PROFILE_JSON_PATH);
    if ($jsonContent === false) {
        $notification = 'Lỗi: Không thể đọc file profiles.json!';
    } else {
        $profileList = json_decode($jsonContent, true) ?: [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            $notification = 'Lỗi: Tệp profiles.json không hợp lệ: ' . json_last_error_msg();
            $profileList = [];
            file_put_contents(PROFILE_JSON_PATH, json_encode($profileList, JSON_PRETTY_PRINT));
        }
    }
} else {
    $notification = 'Tạo file profiles.json mới...';
    if (!is_dir(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0755, true);
    }
    if (file_put_contents(PROFILE_JSON_PATH, json_encode([])) === false) {
        $notification = 'Lỗi: Không thể tạo file profiles.json!';
    } else {
        $profileList = [];
    }
}

function sanitizeFolderName($name) {
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
    return strtolower($name);
}

$notification = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_profile') {
            $userEmail = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
            $hometown = filter_input(INPUT_POST, 'hometown', FILTER_SANITIZE_SPECIAL_CHARS);
            $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_SPECIAL_CHARS);
            $hobbies = filter_input(INPUT_POST, 'hobbies', FILTER_SANITIZE_SPECIAL_CHARS);
            $profileImage = filter_input(INPUT_POST, 'profile_image', FILTER_SANITIZE_URL);
            $logo = filter_input(INPUT_POST, 'logo', FILTER_SANITIZE_URL);
            $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $aboutMe = filter_input(INPUT_POST, 'about_me', FILTER_SANITIZE_SPECIAL_CHARS);
            $posts = filter_input(INPUT_POST, 'posts', FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                $notification = 'Lỗi: Email không hợp lệ!';
            } elseif (!filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $notification = 'Lỗi: URL ảnh bìa không hợp lệ!';
            } elseif (!filter_var($logo, FILTER_VALIDATE_URL)) {
                $notification = 'Lỗi: URL logo không hợp lệ!';
            } elseif (empty($name) || empty($age) || empty($hometown) || empty($occupation) || empty($hobbies)) {
                $notification = 'Lỗi: Vui lòng điền đầy đủ thông tin bắt buộc!';
            } else {
                $profileExists = false;
                foreach ($profileList as $profile) {
                    if ($profile['user_email'] === $userEmail) {
                        $profileExists = true;
                        break;
                    }
                }
                if ($profileExists) {
                    $notification = 'Lỗi: Profile cho người dùng này đã tồn tại!';
                } else {
                    $newProfile = [
                        'user_email' => $userEmail,
                        'name' => $name,
                        'age' => $age,
                        'hometown' => $hometown,
                        'occupation' => $occupation,
                        'hobbies' => $hobbies,
                        'profile_image' => $profileImage,
                        'logo' => $logo,
                        'bio' => $bio,
                        'description' => $description,
                        'about_me' => $aboutMe,
                        'posts' => $posts ? explode("\n", $posts) : [],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $profileList[] = $newProfile;

                    if (file_put_contents(PROFILE_JSON_PATH, json_encode($profileList, JSON_PRETTY_PRINT)) === false) {
                        $notification = 'Lỗi: Không thể ghi vào tệp profiles.json!';
                    } else {
                        $folderName = sanitizeFolderName($name);
                        $newFolderPath = PROFILE_BASE_PATH . $folderName;
                        if (!is_dir($newFolderPath)) {
                            mkdir($newFolderPath, 0755, true);
                        }

                        $profileIndexFile = $newFolderPath . '/index.php';
                        $profileContent = '<?php
session_start();
define("PROFILE_JSON_PATH", __DIR__ . "/../../profile/data/profiles.json");
define("USER_JSON_PATH", __DIR__ . "/../../profile/data/users.json");

$userList = [];
if (is_readable(USER_JSON_PATH)) {
    $jsonContent = file_get_contents(USER_JSON_PATH);
    if ($jsonContent !== false) {
        $userList = json_decode($jsonContent, true) ?: [];
    }
}

$profileList = [];
if (is_readable(PROFILE_JSON_PATH)) {
    $jsonContent = file_get_contents(PROFILE_JSON_PATH);
    if ($jsonContent !== false) {
        $profileList = json_decode($jsonContent, true) ?: [];
    }
}

$currentProfile = null;
foreach ($profileList as $profile) {
    if ($profile["user_email"] === "' . $userEmail . '") {
        $currentProfile = $profile;
        break;
    }
}

$currentUser = null;
foreach ($userList as $user) {
    if ($user["email"] === "' . $userEmail . '") {
        $currentUser = $user;
        break;
    }
}

if (!$currentProfile) {
    die("Profile không tồn tại!");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile của <?php echo htmlspecialchars($currentProfile["name"]); ?> 🌸</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #2a2a4e);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1920&auto=format&fit=crop") no-repeat center center/cover;
            opacity: 0.2;
            z-index: -1;
        }
        .profile-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .profile-cover {
            position: relative;
            width: 100%;
            height: 200px;
            background: url("<?php echo htmlspecialchars($currentProfile[\'profile_image\']); ?>") no-repeat center center/cover;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            z-index: 1;
        }
        .profile-cover::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .profile-logo {
            display: block;
            margin: 0 auto;
            margin-top: -30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ff6f61;
            background: url("<?php echo htmlspecialchars($currentProfile[\'logo\']); ?>") no-repeat center center/cover;
            box-shadow: 0 0 10px rgba(255, 111, 97, 0.5);
            position: relative;
            z-index: 2;
        }
        .profile-header {
            text-align: center;
            margin-top: 20px;
            padding: 0 40px;
        }
        .profile-header h1 {
            font-family: "Montserrat", sans-serif;
            font-size: 2rem;
            color: #ff6f61;
            text-shadow: 0 0 10px rgba(255, 111, 97, 0.5);
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
        }
        .profile-info {
            padding: 20px 40px;
            background: rgba(255, 255, 255, 0.05);
            margin: 20px 40px;
            border-radius: 15px;
        }
        .profile-info p {
            font-size: 1.1rem;
            margin: 10px 0;
            line-height: 1.6;
        }
        .profile-info p strong {
            color: #0ff;
            font-weight: 600;
        }
        .posts-list {
            margin: 20px 40px;
            padding-bottom: 20px;
        }
        .posts-list h3 {
            font-family: "Montserrat", sans-serif;
            font-size: 1.5rem;
            color: #ff6f61;
            margin-bottom: 15px;
        }
        .posts-list ul {
            list-style: none;
        }
        .posts-list li {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .posts-list li:hover {
            background: rgba(0, 255, 255, 0.1);
        }
        a.back-link {
            display: inline-block;
            margin: 0 40px 20px;
            padding: 10px 20px;
            background: #ff6f61;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        a.back-link:hover {
            background: #ff4f41;
        }
        .sakura {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ff6f61;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: sakura-fall 10s linear infinite;
        }
        @keyframes sakura-fall {
            0% { transform: translateY(-100vh) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
        @media (max-width: 600px) {
            .profile-container {
                margin: 0;
                border-radius: 10px;
            }
            .profile-cover {
                height: 150px;
            }
            .profile-logo {
                margin-top: -20px;
                width: 80px;
                height: 80px;
            }
            .profile-header {
                margin-top: 15px;
                padding: 0 20px;
            }
            .profile-header h1 {
                font-size: 1.5rem;
            }
            .profile-info {
                margin: 20px;
                padding: 15px;
            }
            .profile-info p {
                font-size: 1rem;
            }
            .posts-list {
                margin: 20px;
                padding-bottom: 15px;
            }
            .posts-list h3 {
                font-size: 1.2rem;
            }
            a.back-link {
                margin: 0 20px 15px;
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

    <div class="profile-container">
        <div class="profile-cover"></div>
        <div class="profile-logo"></div>
        <div class="profile-header">
            <h1>Profile của <?php echo htmlspecialchars($currentProfile["name"]); ?> 🌟</h1>
        </div>
        <div class="profile-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($currentProfile["user_email"]); ?></p>
            <p><strong>Tên:</strong> <?php echo htmlspecialchars($currentProfile["name"]); ?></p>
            <p><strong>Tuổi:</strong> <?php echo htmlspecialchars($currentProfile["age"]); ?></p>
            <p><strong>Quê quán:</strong> <?php echo htmlspecialchars($currentProfile["hometown"]); ?></p>
            <p><strong>Nghề nghiệp:</strong> <?php echo htmlspecialchars($currentProfile["occupation"]); ?></p>
            <p><strong>Sở thích:</strong> <?php echo htmlspecialchars($currentProfile["hobbies"]); ?></p>
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($currentProfile["bio"]); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($currentProfile["description"]); ?></p>
            <p><strong>About Me:</strong> <?php echo htmlspecialchars($currentProfile["about_me"]); ?></p>
        </div>
        <div class="posts-list">
            <h3>Bài viết 📝</h3>
            <?php if (!empty($currentProfile["posts"])): ?>
                <ul>
                    <?php foreach ($currentProfile["posts"] as $post): ?>
                        <li><?php echo htmlspecialchars($post); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Chưa có bài viết.</p>
            <?php endif; ?>
        </div>
        <a href="../../profile/" class="back-link">Quay lại danh sách Profile</a>
    </div>
</body>
</html>';

                        if (file_put_contents($profileIndexFile, $profileContent) === false) {
                            $notification = 'Lỗi: Không thể tạo file index.php trong thư mục profile!';
                        } else {
                            $notification = 'Thêm profile và tạo trang thành công! Truy cập tại /profile/' . $folderName . '/';
                        }
                    }
                }
            }
        } elseif ($_POST['action'] === 'edit_profile') {
            $profileIndex = filter_input(INPUT_POST, 'profile_index', FILTER_SANITIZE_NUMBER_INT);
            $userEmail = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
            $hometown = filter_input(INPUT_POST, 'hometown', FILTER_SANITIZE_SPECIAL_CHARS);
            $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_SPECIAL_CHARS);
            $hobbies = filter_input(INPUT_POST, 'hobbies', FILTER_SANITIZE_SPECIAL_CHARS);
            $profileImage = filter_input(INPUT_POST, 'profile_image', FILTER_SANITIZE_URL);
            $logo = filter_input(INPUT_POST, 'logo', FILTER_SANITIZE_URL);
            $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $aboutMe = filter_input(INPUT_POST, 'about_me', FILTER_SANITIZE_SPECIAL_CHARS);
            $posts = filter_input(INPUT_POST, 'posts', FILTER_SANITIZE_SPECIAL_CHARS);

            if (isset($profileList[$profileIndex])) {
                $oldFolderName = sanitizeFolderName($profileList[$profileIndex]['name']);
                $newFolderName = sanitizeFolderName($name);

                $profileList[$profileIndex] = [
                    'user_email' => $userEmail,
                    'name' => $name,
                    'age' => $age,
                    'hometown' => $hometown,
                    'occupation' => $occupation,
                    'hobbies' => $hobbies,
                    'profile_image' => $profileImage,
                    'logo' => $logo,
                    'bio' => $bio,
                    'description' => $description,
                    'about_me' => $aboutMe,
                    'posts' => $posts ? explode("\n", $posts) : $profileList[$profileIndex]['posts'],
                    'created_at' => $profileList[$profileIndex]['created_at'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (file_put_contents(PROFILE_JSON_PATH, json_encode($profileList, JSON_PRETTY_PRINT)) === false) {
                    $notification = 'Lỗi: Không thể ghi vào tệp profiles.json!';
                } else {
                    if ($oldFolderName !== $newFolderName) {
                        $oldFolderPath = PROFILE_BASE_PATH . $oldFolderName;
                        $newFolderPath = PROFILE_BASE_PATH . $newFolderName;
                        if (is_dir($oldFolderPath)) {
                            rename($oldFolderPath, $newFolderPath);
                        }
                    }

                    $profileIndexFile = PROFILE_BASE_PATH . $newFolderName . '/index.php';
                    $profileContent = '<?php
session_start();
define("PROFILE_JSON_PATH", __DIR__ . "/../../profile/data/profiles.json");
define("USER_JSON_PATH", __DIR__ . "/../../profile/data/users.json");

$userList = [];
if (is_readable(USER_JSON_PATH)) {
    $jsonContent = file_get_contents(USER_JSON_PATH);
    if ($jsonContent !== false) {
        $userList = json_decode($jsonContent, true) ?: [];
    }
}

$profileList = [];
if (is_readable(PROFILE_JSON_PATH)) {
    $jsonContent = file_get_contents(PROFILE_JSON_PATH);
    if ($jsonContent !== false) {
        $profileList = json_decode($jsonContent, true) ?: [];
    }
}

$currentProfile = null;
foreach ($profileList as $profile) {
    if ($profile["user_email"] === "' . $userEmail . '") {
        $currentProfile = $profile;
        break;
    }
}

$currentUser = null;
foreach ($userList as $user) {
    if ($user["email"] === "' . $userEmail . '") {
        $currentUser = $user;
        break;
    }
}

if (!$currentProfile) {
    die("Profile không tồn tại!");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile của <?php echo htmlspecialchars($currentProfile["name"]); ?> 🌸</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;4 00;600&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #2a2a4e);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1920&auto=format&fit=crop") no-repeat center center/cover;
            opacity: 0.2;
            z-index: -1;
        }
        .profile-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .profile-cover {
            position: relative;
            width: 100%;
            height: 200px;
            background: url("<?php echo htmlspecialchars($currentProfile[\'profile_image\']); ?>") no-repeat center center/cover;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            z-index: 1;
        }
        .profile-cover::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .profile-logo {
            display: block;
            margin: 0 auto;
            margin-top: -30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ff6f61;
            background: url("<?php echo htmlspecialchars($currentProfile[\'logo\']); ?>") no-repeat center center/cover;
            box-shadow: 0 0 10px rgba(255, 111, 97, 0.5);
            position: relative;
            z-index: 2;
        }
        .profile-header {
            text-align: center;
            margin-top: 20px;
            padding: 0 40px;
        }
        .profile-header h1 {
            font-family: "Montserrat", sans-serif;
            font-size: 2rem;
            color: #ff6f61;
            text-shadow: 0 0 10px rgba(255, 111, 97, 0.5);
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
        }
        .profile-info {
            padding: 20px 40px;
            background: rgba(255, 255, 255, 0.05);
            margin: 20px 40px;
            border-radius: 15px;
        }
        .profile-info p {
            font-size: 1.1rem;
            margin: 10px 0;
            line-height: 1.6;
        }
        .profile-info p strong {
            color: #0ff;
            font-weight: 600;
        }
        .posts-list {
            margin: 20px 40px;
            padding-bottom: 20px;
        }
        .posts-list h3 {
            font-family: "Montserrat", sans-serif;
            font-size: 1.5rem;
            color: #ff6f61;
            margin-bottom: 15px;
        }
        .posts-list ul {
            list-style: none;
        }
        .posts-list li {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .posts-list li:hover {
            background: rgba(0, 255, 255, 0.1);
        }
        a.back-link {
            display: inline-block;
            margin: 0 40px 20px;
            padding: 10px 20px;
            background: #ff6f61;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        a.back-link:hover {
            background: #ff4f41;
        }
        .sakura {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ff6f61;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: sakura-fall 10s linear infinite;
        }
        @keyframes sakura-fall {
            0% { transform: translateY(-100vh) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
        @media (max-width: 600px) {
            .profile-container {
                margin: 0;
                border-radius: 10px;
            }
            .profile-cover {
                height: 150px;
            }
            .profile-logo {
                margin-top: -20px;
                width: 80px;
                height: 80px;
            }
            .profile-header {
                margin-top: 15px;
                padding: 0 20px;
            }
            .profile-header h1 {
                font-size: 1.5rem;
            }
            .profile-info {
                margin: 20px;
                padding: 15px;
            }
            .profile-info p {
                font-size: 1rem;
            }
            .posts-list {
                margin: 20px;
                padding-bottom: 15px;
            }
            .posts-list h3 {
                font-size: 1.2rem;
            }
            a.back-link {
                margin: 0 20px 15px;
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

    <div class="profile-container">
        <div class="profile-cover"></div>
        <div class="profile-logo"></div>
        <div class="profile-header">
            <h1>Profile của <?php echo htmlspecialchars($currentProfile["name"]); ?> 🌟</h1>
        </div>
        <div class="profile-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($currentProfile["user_email"]); ?></p>
            <p><strong>Tên:</strong> <?php echo htmlspecialchars($currentProfile["name"]); ?></p>
            <p><strong>Tuổi:</strong> <?php echo htmlspecialchars($currentProfile["age"]); ?></p>
            <p><strong>Quê quán:</strong> <?php echo htmlspecialchars($currentProfile["hometown"]); ?></p>
            <p><strong>Nghề nghiệp:</strong> <?php echo htmlspecialchars($currentProfile["occupation"]); ?></p>
            <p><strong>Sở thích:</strong> <?php echo htmlspecialchars($currentProfile["hobbies"]); ?></p>
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($currentProfile["bio"]); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($currentProfile["description"]); ?></p>
            <p><strong>About Me:</strong> <?php echo htmlspecialchars($currentProfile["about_me"]); ?></p>
        </div>
        <div class="posts-list">
            <h3>Bài viết 📝</h3>
            <?php if (!empty($currentProfile["posts"])): ?>
                <ul>
                    <?php foreach ($currentProfile["posts"] as $post): ?>
                        <li><?php echo htmlspecialchars($post); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Chưa có bài viết.</p>
            <?php endif; ?>
        </div>
        <a href="../../profile/" class="back-link">Quay lại danh sách Profile</a>
    </div>
</body>
</html>';

                    if (file_put_contents($profileIndexFile, $profileContent) === false) {
                        $notification = 'Lỗi: Không thể cập nhật file index.php trong thư mục profile!';
                    } else {
                        $notification = 'Chỉnh sửa profile và cập nhật trang thành công! Truy cập tại /profile/' . $newFolderName . '/';
                    }
                }
            } else {
                $notification = 'Lỗi: Profile không tồn tại!';
            }
        } elseif ($_POST['action'] === 'delete_profile') {
            $profileIndex = filter_input(INPUT_POST, 'profile_index', FILTER_SANITIZE_NUMBER_INT);
            if (isset($profileList[$profileIndex])) {
                $folderName = sanitizeFolderName($profileList[$profileIndex]['name']);
                $folderPath = PROFILE_BASE_PATH . $folderName;

                if (is_dir($folderPath)) {
                    unlink($folderPath . '/index.php');
                    rmdir($folderPath);
                }

                array_splice($profileList, $profileIndex, 1);
                if (file_put_contents(PROFILE_JSON_PATH, json_encode($profileList, JSON_PRETTY_PRINT)) === false) {
                    $notification = 'Lỗi: Không thể ghi vào tệp profiles.json!';
                } else {
                    $notification = 'Xóa profile và thư mục thành công!';
                }
            } else {
                $notification = 'Lỗi: Profile không tồn tại!';
            }
        }
    } else {
        $notification = 'Lỗi: Token CSRF không hợp lệ!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị Profile 🌸</title>
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
        .form-container input, .form-container button, .form-container select, .form-container textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
            width: calc(100% - 20px);
        }
        .form-container input, .form-container select, .form-container textarea {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .form-container button {
            background: hotpink;
            color: #fff;
            cursor: pointer;
        }
        .form-container button:hover {
            background: #ff1493;
        }
        .profile-list {
            margin-top: 20px;
        }
        .profile-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .profile-item button {
            background: #0ff;
            color: #1a1a2e;
            padding: 8px 15px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }
        .profile-item button:hover {
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
            max-height: 80vh;
            overflow-y: auto;
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
        @media (max-width: 600px) {
            .form-container input, .form-container button, .form-container select, .form-container textarea {
                font-size: 0.9rem;
                padding: 8px;
            }
            .profile-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .profile-item button {
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
        <h1>Quản trị Profile 🎉</h1>
        <a href="../" class="back-link">Quay lại</a>
        <a href="../logout/" class="logout-link">Đăng xuất</a>

        <div class="form-container">
            <h2>Thêm profile mới</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_profile">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="email" name="user_email" placeholder="Email người dùng" required>
                <input type="text" name="name" placeholder="Tên" required>
                <input type="number" name="age" placeholder="Tuổi" required>
                <input type="text" name="hometown" placeholder="Quê quán" required>
                <input type="text" name="occupation" placeholder="Nghề nghiệp" required>
                <input type="text" name="hobbies" placeholder="Sở thích" required>
                <input type="url" name="profile_image" placeholder="URL ảnh bìa" required>
                <input type="url" name="logo" placeholder="URL logo" required>
                <input type="text" name="bio" placeholder="Bio" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <textarea name="about_me" placeholder="About Me" required></textarea>
                <textarea name="posts" placeholder="Bài viết (mỗi dòng một bài viết)"></textarea>
                <button type="submit">Thêm</button>
            </form>
        </div>

        <div class="profile-list">
            <h2>Danh sách profile</h2>
            <?php foreach ($profileList as $index => $profile): ?>
                <div class="profile-item">
                    <div>
                        <strong>Email:</strong> <?php echo htmlspecialchars($profile['user_email']); ?><br>
                        <strong>Tên:</strong> <?php echo htmlspecialchars($profile['name']); ?>
                    </div>
                    <div>
                        <button onclick="editProfile(<?php echo $index; ?>, '<?php echo htmlspecialchars($profile['user_email']); ?>', '<?php echo htmlspecialchars($profile['name']); ?>', '<?php echo htmlspecialchars($profile['age']); ?>', '<?php echo htmlspecialchars($profile['hometown']); ?>', '<?php echo htmlspecialchars($profile['occupation']); ?>', '<?php echo htmlspecialchars($profile['hobbies']); ?>', '<?php echo htmlspecialchars($profile['profile_image']); ?>', '<?php echo htmlspecialchars($profile['logo']); ?>', '<?php echo htmlspecialchars($profile['bio']); ?>', '<?php echo htmlspecialchars($profile['description']); ?>', '<?php echo htmlspecialchars($profile['about_me']); ?>', '<?php echo htmlspecialchars(implode('\n', $profile['posts'])); ?>')">Chỉnh sửa</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_profile">
                            <input type="hidden" name="profile_index" value="<?php echo $index; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit">Xóa</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="profile-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <h2>Chỉnh sửa profile</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_profile">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="profile_index" id="profile-index">
                    <input type="email" name="user_email" id="profile-user-email" placeholder="Email người dùng">
                    <input type="text" name="name" id="profile-name" placeholder="Tên" required>
                    <input type="number" name="age" id="profile-age" placeholder="Tuổi" required>
                    <input type="text" name="hometown" id="profile-hometown" placeholder="Quê quán" required>
                    <input type="text" name="occupation" id="profile-occupation" placeholder="Nghề nghiệp" required>
                    <input type="text" name="hobbies" id="profile-hobbies" placeholder="Sở thích" required>
                    <input type="url" name="profile_image" id="profile-image" placeholder="URL ảnh bìa" required>
                    <input type="url" name="logo" id="profile-logo" placeholder="URL logo" required>
                    <input type="text" name="bio" id="profile-bio" placeholder="Bio" required>
                    <textarea name="description" id="profile-description" placeholder="Description" required></textarea>
                    <textarea name="about_me" id="profile-about_me" placeholder="About Me" required></textarea>
                    <textarea name="posts" id="profile-posts" placeholder="Bài viết (mỗi dòng một bài viết)"></textarea>
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
        function editProfile(index, userEmail, name, age, hometown, occupation, hobbies, profileImage, logo, bio, description, aboutMe, posts) {
            document.getElementById('profile-modal').style.display = 'flex';
            document.getElementById('profile-index').value = index;
            document.getElementById('profile-user-email').value = userEmail;
            document.getElementById('profile-name').value = name;
            document.getElementById('profile-age').value = age;
            document.getElementById('profile-hometown').value = hometown;
            document.getElementById('profile-occupation').value = occupation;
            document.getElementById('profile-hobbies').value = hobbies;
            document.getElementById('profile-image').value = profileImage;
            document.getElementById('profile-logo').value = logo;
            document.getElementById('profile-bio').value = bio;
            document.getElementById('profile-description').value = description;
            document.getElementById('profile-about_me').value = aboutMe;
            document.getElementById('profile-posts').value = posts;
        }

        function closeModal() {
            document.getElementById('profile-modal').style.display = 'none';
        }

        const notification = document.querySelector('.notification');
        if (notification) {
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        const messages = [
            'Cảm ơn đã sử dụng trình quản lý profile!',
            'Hãy thêm profile cho bạn bè của bạn!',
            'Chúc bạn quản lý profile vui vẻ!'
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