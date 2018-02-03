<?php
/**
*入口文件
*/
define('IN_EBH', TRUE);
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('IS_DEBUG', TRUE);  //是否开启调试状态
date_default_timezone_set('Asia/Shanghai');
$config = S_ROOT.'config/config.php';
require S_ROOT.'system/core/runtime.php';
Ebh::createWebApplication($config)->run();