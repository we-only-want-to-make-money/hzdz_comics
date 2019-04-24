<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no, email=no">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="HandheldFriendly" content="true">
<meta name="MobileOptimized" content="640">
<meta name="screen-orientation" content="portrait">
<meta name="x5-orientation" content="portrait">
<meta name="full-screen" content="yes">
<meta name="x5-fullscreen" content="true">
<meta name="browsermode" content="application">
<meta name="x5-page-mode" content="app">
<meta name="msapplication-tap-highlight" content="no">
<meta name="viewport" content="width=640,target-densitydpi=device-dpi,maximum-scale=1.0, user-scalable=no ">
<meta name="keywords" content="<?php echo ($_site['name']); ?>">
<meta name="description" content="<?php echo ($_site['name']); ?>">
<title>温馨提示</title>
<script src="/Public/js/jquery.min.js" type="text/javascript"></script>
</head>
<style>
	*{margin:0;padding:0;}
	img{
	    width: 100px;
		display: block;
		margin: 10% auto 5% auto;
	}
	.msg{
		width: 50%;
		margin: 0 auto;
		display: block;
		text-align: center;
		font-size: 20px;
	}
	a{
		display: block;
		width: 30%;
		height: 50px;
		margin: 6% auto;
		text-align: center;
		font-size: 20px;
		text-decoration: none;
		color: #fff;
		background: #FF730A;
		border-radius: 10px;
		line-height: 50px;
	}
</style>
<body>
<div class="rmain">
	<img src="/Public/images/over.jpg" />
	<?php if($_GET['status'] == 1): ?><span class="msg">本书未完，作者还在努力创作中，敬请期待后续更新吧！</span>
	<?php else: ?>
	<span class="msg">本书已完结，谢谢</span><?php endif; ?>
	<?php if($_GET['type'] == 'xs'): ?><a href="<?php echo U('Book/index');?>">回首页看看</a>
	<?php else: ?>
	<a href="<?php echo U('Mh/index');?>">回首页看看</a><?php endif; ?>
	
</div>
</body>
</html>