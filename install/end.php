<?php require_once("lock.php");?>
<!doctype html>
<html lang="default">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Config End ThanhDieu.Com | Version V4.0.2</title>
    <link href="https://i.pinimg.com/564x/67/9d/0f/679d0f1b128efefc189f1d9994e97d94.jpg" rel="shortcut icon" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="./assets/css/icons.bundle.css" data-no-instant="" rel="stylesheet">
    <!-- Sandy.Bundles -->
    <link href="./assets/css/sandy.bundles.css" data-no-instant="" rel="stylesheet">
    <!-- tailwind -->
    <link href="./assets/css/tailwind.min.css" data-no-instant="" rel="stylesheet">
    <!-- App -->
    <link href="./assets/css/app.css" data-no-instant="" rel="stylesheet">
  </head>
  <body id="app-sandy" app-sandy="wrapper">
    <div class="mobile">
      <div class="p-1 text-xs flex items-center justify-center bg-yellow-200 hidden">BETA</div>
      <div class="auth-row justify-center">
        <div class="auth-col hidden"></div>
        <div class="auth-col">
          <div class="landing-form items-center flex-col">
            <div class="form-box login-register-form-element p-0 lg:p-10">
            <div class="auth-go-back mb-10">
                <a href="step2.php?back" class="auth-go-back-a">
                    <i class="fa fa-long-arrow-left"></i>&ensp;Về lại step 2
                </a>
            </div>
              <div class="mb-16">
                <h2 class="form-box-title text-2xl mb-1"><i class="fa fa-gear fa-spin" style="font-size:24px"></i>&ensp;Trình cài đặt - profile thanhdieu.com</h2>
                <span display="inline" class="text-sm">Bước cấu hình cuối cùng ( step 3 )</span>
              </div>
              <div class="login-include">
                <div class="social-links is-auth justify-center"></div>
                <?php
$lockfile = '../install.lock';

if (file_exists($lockfile)) {
    $install_lock = file_get_contents($lockfile);
    if (strpos($install_lock, 'step1 = true') !== false && strpos($install_lock, 'step2 = true') !== false) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['config_profile3'])) {
                $hovaten = isset($_POST['hovaten_config']) ? $_POST['hovaten_config'] : '';
                $avatar = isset($_POST['avatar_config']) ? $_POST['avatar_config'] : '';
                $tieusu = isset($_POST['tieusu_config']) ? $_POST['tieusu_config'] : '';
                $tieusuArray = explode("\n", $tieusu);
                if (count($tieusuArray) !== 3) {
                  echo '<script>Swal.fire("Error", "Tiểu sử bắt buộc 3 dòng, vui lòng xem video hướng dẫn !", "error");</script>';
                  echo '<script>setTimeout(function(){ window.location = "end.php?error"; }, 3000);</script>';
                  exit();
              } else {
                $tieusuFormatted = 'tieusu: [' . "\n";
                foreach ($tieusuArray as $line) {
                    $tieusuFormatted .= '"' . trim($line) . '",'. "\n";
                }
              }
                $tieusuFormatted .= '],';
                $website = $_SERVER['HTTP_HOST'];
                $createdBy = isset($_POST['created_config']) ? $_POST['created_config'] : '';
                $noidung = isset($_POST['noidung_config']) ? $_POST['noidung_config'] : '';
                $antiDdos = isset($_POST['anti_ddos']) && $_POST['anti_ddos'] === 'yes' ? 'anti-ddos.php' : '';
                include('../config/config.php');
                $config['head']['anti_ddos'] = $antiDdos;
                $configFileStepThree = '../config/config.php';
file_put_contents($configFileStepThree, '<?php' . PHP_EOL . '$config = ' . var_export($config, true) . ';');
                $configJsContent = "var config = {
    name: {
        hovaten: \"$hovaten\",
        avatar: \"$avatar\",
        $tieusuFormatted
        website: \"$website\",
        tichxanh: \"./v4.0.2/img/tich-xanh.png\",
        created_by: \"$createdBy\",
        all_rights_reserved_year: \"2023\"
    },
    // top nav, home, download, about, bạn hãy chỉnh theo ý mình
    nav: {
        nav1: [\"HOME\", \"#home\"],
        nav2: [\"DOWNLOAD\", \"download.html\"],
        nav3: [\"ABOUT\", \"about.html\"]
    },
    thongbao: {
        title: \"WELCOME BACK\",
        noidung: \"$noidung\",
      //  facebook: [\"https://facebook.com/\"],
      //  zalo: [\"https://zalo.me/\"] 
    },
    // Link mạng xã hội, `K` tượng chưng cho con số theo dõi là ngàn, `+` tượng chưng cho con số theo dõi là trăm.
    social: {
        facebook: [\"https://facebook.com/\", \"507K\"],
        youtube: [\"https://youtube.com/\", \"3K\"],
        discord: [\"https://discord.com/\", \"800+\"],
        instagram: [\"https://instagram.com/\", \"7+\"] 
    },
    skill: {
        html_css: \"76%\",
        php: \"37%\",
        javascript: \"65%\",
        python: \"22%\" 
    },
    // Link nhạc .mp3 có thể sử dụng https://catbox.moe/ để upload nhạc, có thể thêm nhiều nhạc bằng cách nhân bản mảng.
    music: {
        linkbaihat: [
        \"//thanhdieu.com/files/Dont-Côi-Remake.mp3\",
        \"//thanhdieu.com/files/Anh-Đã-Quen-Với-Cô-Đơn.mp3\",
        \"//thanhdieu.com/files/Về-Bên-Anh.mp3\",
        \"//thanhdieu.com/files/Save-Me.mp3\",
        \"//thanhdieu.com/files/td1.mp3\",
        ] 
    } 
};";
        $installLockFile = '../install.lock';
        if (file_exists($installLockFile)) {
        $installLockContent = file_get_contents($installLockFile);
        $installLockContent .= PHP_EOL . 'step3 = end';
        file_put_contents($installLockFile, $installLockContent);
        } else { }
                file_put_contents('../config/config.js', $configJsContent);
                echo '<script>Swal.fire({';
                    echo '    icon: "success",';
                    echo '    title: "Tuyệt Vời !",';
                    echo '    text: "Setup đã xong, hệ thống sẽ tự vô hiệu hóa trang install nhầm tăng tính bảo mật, không sao, bạn đã hoàn thành install 3 step vì vậy bạn có thể edit lại bất cứ lúc nào ở đường dẫn config/config.js",';
                    echo '    showCancelButton: false,';
                    echo '    confirmButtonText: "Đã Hiểu",';
                    echo '    allowOutsideClick: false';
                    echo '}).then((result) => {';
                    echo '    if (result.isConfirmed) {';
                    echo '        window.location.href = "../";';
                    echo '    }';
                    echo '});';
                    echo '</script>';
                    
            }
        }
    } else {
        echo '<script>Swal.fire("Error", "Bạn cần hoàn thành Step 1 và Step 2 trước khi tiến hành bước tiếp theo.", "error");</script>';
        echo '<script>setTimeout(function(){ window.location = "index.php"; }, 3000);</script>';
    }
} else {
    echo '<script>Swal.fire("Error", "File install.lock không tồn tại.", "error");</script>';
    echo '<script>setTimeout(function(){ window.location = "index.php"; }, 3000);</script>';
}
?>


                <form id="save_config" class="form" method="post">
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <input type="text" class="form-control" name="hovaten_config" value="" placeholder="Họ Và Tên ( Vd: Vương Thanh Diệu )" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <input type="url" class="form-control" name="avatar_config" placeholder="Link Avatar ( Vd: https://i.imgur.com/hJZ1QU8.jpeg )" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <textarea style="height: 110px;" type="text" class="form-control" placeholder="1 tiểu sử thì xuống hàng 1 dòng, bắt buộc 3 hàng tiểu sử, như cái mẫu sẵn có." name="tieusu_config" required>Hi my name is ThanhDieu 😍
Hey brother, I'm a Developer.
What about you :)?</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                      <textarea style="background: #1f1538;height: 65px;" type="url" class="form-control" name="website_config" placeholder="Config Site: `<?php echo $_SERVER['HTTP_HOST']; ?>`" disabled></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <textarea type="text" class="form-control" name="created_config" placeholder="Tên Người Triển Khai ( Ví dụ: ThanhDieu viết liền không dấu )" required></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <textarea style="height: 140px;" type="text" class="form-control" name="noidung_config" placeholder="Nội Dung Alert Bật Lên ( Ví dụ: Bạn đang dùng phiên bản source code V4.0.2<br>Bấm vào nút bên dưới để liên hệ! 💘 )" required></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                      <input style="margin: -5px auto;" onclick="Swal.fire('Success', 'Trang website của bạn sẽ có biểu mẫu bảo vệ bởi anti-ddos.', 'success');" type="checkbox" name="anti_ddos" value="yes" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                      <b>Kích Hoạt Anti-DDoS 💣</b>
                    </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <button type="submit" name="config_profile3" class="shadow-none w-full button bg-gray-200 text-black toast-custom-close sandy-loader-flower">Cài Đặt ( End )</button>
                    </div>
                  </div>
                  <div class="flex justify-center mt-8">
                    <a class="auth-link" href="https://youtu.be/57gjfp6GK6k" target="_blank">
                      <b class="icon icon-link">
                      </b><u>Hướng dẫn cài đặt ( tutorial config )</u></a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div></div>
    <div></div>
    <script>
window.onload = function() {
  const form = document.getElementById('save_config');
  const elements = form.elements;
  if (document.cookie) {
    const cookies = document.cookie.split(';');
    cookies.forEach(cookie => {
      const parts = cookie.split('=');
      const name = parts[0].trim();
      const value = parts[1];
      for (let i = 0; i < elements.length; i++) {
        if (elements[i].name === name) {
          elements[i].value = value;
          break;
        }
      }
    });
  }
  form.addEventListener('input', function(e) {
    const input = e.target;
    document.cookie = `${input.name}=${input.value}; expires=Thu, 18 Dec 2023 12:00:00 UTC; path=/`;
  });
};
</script>
  </body>
</html>