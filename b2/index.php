<?php
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    setcookie('XDEBUG_SESSION', 1, time() + 86400);
}
//exit(phpinfo());
// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('PUBLIC_PATH', __DIR__ . '/public/');
define('UPLOAD_PATH', __DIR__ . '/public/uploads');
define('EXTEND_PATH',__DIR__ .'/extend/');

$the_file_path = $_SERVER['PHP_SELF'];
$findme = '/index.php';
$pos = strpos($the_file_path, $findme);
$target_path = substr($the_file_path, 0,$pos);
$site_url = "http://".$_SERVER['HTTP_HOST'].$target_path;
define('SITE_URL',$site_url);

define('Admin_Name','ogIAk=af');


// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';