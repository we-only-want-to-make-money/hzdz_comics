<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>

<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta content="telephone=no" name="format-detection">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no">

<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; text-align:center; }
.system-message{ padding: 24px 48px; }
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.3em; font-size: 20px;  padding-top:20px;}
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
.icon img{ max-width:50%;}
</style>
</head>
<body>
<div class="system-message">
<?php if(isset($message)) {?>
<p class="icon"><img src="__PUBLIC__/images/face_ok.png" /></p>
<p class="success"><?php echo($message); ?></p>
<?php }else{?>
<p class="icon"><img src="__PUBLIC__/images/face_error.png" /></p>
<p class="error"><?php echo($error); ?></p>
<?php }?>
<p class="detail"></p>
<p class="jump">
<b id="wait"><?php echo($waitSecond); ?></b> 后页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 
</p>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
