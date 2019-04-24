<?php

// 微信支付异步通知入口

$_GET['c'] = 'Notify';
$_GET['a'] = 'index';

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('PHP 版本必须大于等于5.3.0 !');

// 写入目录安全文件
define('DIR_SECURE_CONTENT', 'powered by http://www.efucms.com');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './#ThinkPHP/ThinkPHP.php';