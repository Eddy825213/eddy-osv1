<?php
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
    if ($profile["user_email"] === "eddy@profile.com") {
        $currentProfile = $profile;
        break;
    }
}

$currentUser = null;
foreach ($userList as $user) {
    if ($user["email"] === "eddy@profile.com") {
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
            background: url("<?php echo htmlspecialchars($currentProfile['profile_image']); ?>") no-repeat center center/cover;
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
            background: url("<?php echo htmlspecialchars($currentProfile['logo']); ?>") no-repeat center center/cover;
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
</html>