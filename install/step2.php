<?php require_once("lock.php");?>
<!doctype html>
<html lang="default">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Config Step 2 ThanhDieu.Com | Version V4.0.2</title>
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
                <a href="index.php?back" class="auth-go-back-a">
                    <i class="fa fa-long-arrow-left"></i>&ensp;Về lại step 1
                </a>
            </div>
              <div class="mb-16">
                <h2 class="form-box-title text-2xl mb-1"><i class="fa fa-gear fa-spin" style="font-size:24px"></i>&ensp;Trình cài đặt - profile thanhdieu.com</h2>
                <span display="inline" class="text-sm">Bước cấu hình step 2 ✌️</span>
              </div>
              <div class="login-include">
                <div class="social-links is-auth justify-center"></div>
                <!-- FORM -->
                <?php
$installLockFile = '../install.lock';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['config_profile2'])) {
        if (file_exists($installLockFile)) {
            $installLockContent = file_get_contents($installLockFile);
            $steps = explode(PHP_EOL, $installLockContent);
            $step1Completed = false;
            foreach ($steps as $key => $step) {
                if (strpos($step, 'step1') !== false) {
                    $steps[$key] = 'step1 = true';
                    $step1Completed = true;
                    break;
                }
            }
            if ($step1Completed) {
                $shortcutIcon = isset($_POST['shortcut_config']) ? $_POST['shortcut_config'] : '';
                $urlWebsite = isset($_POST['url_config']) ? $_POST['url_config'] : '';
                $imageReview = isset($_POST['image_review_config']) ? $_POST['image_review_config'] : '';
                $antiF12 = isset($_POST['anti_f12_config']) && $_POST['anti_f12_config'] === 'yes' ? './v4.0.2/js/sfs.js' : '';
                include('../config/config.php');
                $config['head']['shortcut_icon'] = $shortcutIcon;
                $config['head']['url'] = $urlWebsite;
                $config['head']['image_review'] = $imageReview;
                $config['head']['anti_f12'] = $antiF12;
                $configFileStepTwo = '../config/config.php';
                file_put_contents($configFileStepTwo, '<?php' . PHP_EOL . '$config = ' . var_export($config, true) . ';');
                $steps[] = 'step2 = true';
                $newInstallLockContent = implode(PHP_EOL, $steps);
                file_put_contents($installLockFile, $newInstallLockContent);
                echo '<script>Swal.fire("Success", "Cài đặt thành công, đang chuyển sang step cuối.", "success");</script>';
                echo '<script>setTimeout(function(){ window.location = "end.php?step=3"; }, 2000);</script>';
            } else {
                echo '<script>Swal.fire("Error", "Step 1 chưa hoàn thành.", "error");</script>';
                echo '<script>setTimeout(function(){ window.location = "index.php?step=1"; }, 2000);</script>';
            }
        } else {
            echo '<script>Swal.fire("Error", "Cài đặt thất bại mã lỗi 0x000001.", "error");</script>';
            echo '<script>setTimeout(function(){ window.location = "index.php?step=1"; }, 2000);</script>';
        }
    }
}
?>
                <form id="save_config" class="form" method="post">
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <input type="url" class="form-control" name="shortcut_config" value="" placeholder="Link shortcut Icon ( Vd: https://i.imgur.com/hJZ1QU8.jpeg )" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <input type="url" class="form-control" name="url_config" placeholder="Url Website ( Vd: https://thanhdieu.com )" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                        <input type="url" class="form-control" name="image_review_config" placeholder="Hình Thu Nhỏ ( Vd: https://i.imgur.com/hJZ1QU8.jpeg )" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <div class="form-input">
                      <input onclick="Swal.fire('Success', 'Trang website của bạn sẽ được bảo vệ bằng safeguard v4.', 'success');" type="checkbox" name="anti_f12_config" value="yes" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                     <b>Kích Hoạt Anti-F12 💝</b>
                    </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-item">
                      <button type="submit" name="config_profile2" class="shadow-none w-full button bg-gray-200 text-black toast-custom-close sandy-loader-flower">Cài Đặt ( Step 2 )</button>
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