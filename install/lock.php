<?php
$file_path = '../install.lock';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    if (strpos($content, 'step3 = end') !== false) {
        $random_bytes = random_bytes(4);
        $random_string = bin2hex($random_bytes);
        $random_string = substr($random_string, 0, 8);
        $current_domain = $_SERVER['HTTP_HOST'];
        header('Location: https://' . $current_domain . '/' . $random_string . '');
        exit;
    }
}
?>
