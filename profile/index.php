<?php
session_start();
define('PROFILE_JSON_PATH', __DIR__ . '/data/profiles.json');

$profileList = [];
if (is_readable(PROFILE_JSON_PATH)) {
    $jsonContent = file_get_contents(PROFILE_JSON_PATH);
    if ($jsonContent !== false) {
        $profileList = json_decode($jsonContent, true) ?: [];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Profile 🌸</title>
    <style>
        body {
            background: #2a1a3d;
            color: #fff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
        .profile-list {
            list-style: none;
            padding: 0;
        }
        .profile-list a {
            color: #0ff;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }
        .profile-list a:hover {
            background: rgba(0, 255, 255, 0.1);
            text-decoration: underline;
        }
        h1 {
            text-align: center;
            color: hotpink;
        }
        a.back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0ff;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Danh sách Profile 🎉</h1>
        <?php if (!empty($profileList)): ?>
            <ul class="profile-list">
                <?php foreach ($profileList as $profile): ?>
                    <li>
                        <a href="/profile/<?php echo htmlspecialchars(sanitizeFolderName($profile['name'])); ?>/"><?php echo htmlspecialchars($profile['name']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Chưa có profile nào được tạo.</p>
        <?php endif; ?>
        <a href="admin/" class="back-link">Đi tới quản trị</a>
    </div>
</body>
</html>
<?php
function sanitizeFolderName($name) {
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
    return strtolower($name);
}
?>