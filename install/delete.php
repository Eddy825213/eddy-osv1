<?php
$install_lockfile = '../install.lock';
$config_js_file = '../config/config.js';

if (file_exists($install_lockfile)) {
    unlink($install_lockfile);

    if (file_exists($config_js_file)) {
        unlink($config_js_file);
        echo 'Xóa tệp thành công';
    } else {
        echo 'Tệp config.js không tồn tại';
    }
} else {
    echo 'Tệp không tồn tại';
}
?>
