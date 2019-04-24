<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title><?php echo ($_site['name']); ?> - 提现</title>
<link  href="/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="/Public/css/style.css" />
<link type="text/css" rel="stylesheet"  href="/Public/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="/Public/css/ectouch.css">
<script type="text/javascript" src="/Public/js/jquery.min.js"></script>

<link href="/Public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="/Public/home/css/style.css">
</head>
<style>
.cz2 a.green {
    background: #f93;
    color: #ffffff;
    box-shadow: 0px 3px #aadc33;
}
.cz2 a {
    width: 80%;
    height: 38px;
    line-height: 40px;
    text-align: center;
    float: left;
    margin: 20px 10%;
    background: #f33;
    color: #ffffff;
    font-size: 1.0rem;
    border-radius: 3px;
    box-shadow: 0px 3px #aadc33;
}
.pages{
	width: 33%;
    float: left;
}
.order_list_style, .cat_style, .user_style {
    margin-top: 1.5rem;
}
.Paging a {
    padding: 3px;
}
.record_list li {
    padding:0;
}
</style>
<body>
<div class="Layout_style">
<div class="header-blank"></div>
<div class="header">
	<?php echo getUserName($user['id']);?> -- 客户信息
	<span class="left">
		<a href="javascript:;" onclick="window.history.go(-1)"><span class="glyphicon glyphicon-chevron-left"  aria-hidden="true"></span></a>
	</span>
</div>
<section class="user_style">
<div class="cz_form">
	<div class="tx_j">可提现金额：<span>￥<?php echo ($user['rmb']); ?></span></div>
	<div class="cz1"><span>金额(￥):</span><input class="txt " type="text" name="money" id="money" value="" autocomplete="off" placeholder="请输入金额1元起"></div>
	<div class="cz1"><span>支付宝名称:</span><input class="txt " type="text" name="truename" id="truename" value="" autocomplete="off" placeholder="请输入支付宝名称"></div>
	<div class="cz1"><span>支付宝账号:</span><input class="txt " type="text" name="cardno" id="cardno" value="" autocomplete="off" placeholder="请输入支付宝账号"></div>
</div>
<div class="cz2" id="show">
	<!-- <a href="javascript:;"  _id="1" class="green toggle">小额红包提现</a> -->
	<!-- <a href="javascript:;"  _id="2" class="toggle">大额平台提现</a> -->
	<a href="javascript:;"  _id="2" class="toggle">申请提现</a>
</div>
<div class="cz2" id="hide" style="display:none">
	<a href="javascript:;" style="width:80%;background:#f30;" _id="1" class="green toggle">正在提现,请耐心等待...</a>
</div>
<div class="record_list">
     <div class="title_name">提现记录</div>
     <ul id="recode">
     
     </ul>
    <div class="Paging">
     <div class="pages" id="up" style="text-align:right"><a href="javascript:;">上一页</a></div>
     <div class="pages" id="pages" style="text-align:center"></div>
     <div class="pages" id="down" style="text-align:left"><a href="javascript:;">下一页</a></div>
    </div>
</div>
</section>
</div>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
var links = window.location.href+'&parent='+"<?php echo ($user["id"]); ?>";
var img = "<?php echo ($share["pic"]); ?>";
var title = "<?php echo ($share["title"]); ?>";
var desc = "<?php echo ($share["desc"]); ?>";
wx.config({
	debug: false,
	appId: "<?php echo ($jssdk['appId']); ?>",
	timestamp:"<?php echo ($jssdk['timestamp']); ?>",
	nonceStr: "<?php echo ($jssdk['nonceStr']); ?>",
	signature: "<?php echo ($jssdk['signature']); ?>",
	jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage']
});
wx.ready(function () {
	wx.checkJsApi({
		jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
		success: function(res) {
			//alert(JSON.stringify(res));
		}
	});
	wx.error(function(res){
		console.log('err:'+JSON.stringify(res));
	});
	//分享给朋友
	wx.onMenuShareAppMessage({
		title:title, // 分享标题
		desc:desc, // 分享描述
		link:links, // 分享链接
		imgUrl:img, // 分享图标
		type: 'link', // 分享类型,music、video或link，不填默认为link
		dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		success: function () { 
		},
		cancel: function () { 
			
		}
	});
	//分享到朋友圈
	wx.onMenuShareTimeline({
		title:title, // 分享标题
		link: links, // 分享链接
		imgUrl:img, // 分享图标
		success: function () { 
			// 用户确认分享后执行的回调函数
		},
		cancel: function () { 
			// 用户取消分享后执行的回调函数
		}
	});
});
</script>
<?php echo ($_CFG["site"]["thirdcode"]); ?>
<script>
$('.toggle').click(function(data){
	$('#show').hide();
	$('#hide').show();
	var money = $('#money').val();
	var status = $(this).attr('_id');
	var truename = $("#truename").val();
	var cardno = $("#cardno").val();
	var truename = $("#truename").val();
	if(money=='' || truename=='' || cardno=='' || truename==''){
		$('#show').show();
		$('#hide').hide();
		alert('请输入金额和银行信息！');
		return false;
	}
	$.post("<?php echo U('Mh/withdraw');?>",{status:status,money:money,truename:truename,cardno:cardno,truename:truename},function(data){
		$('#show').show();
		$('#hide').hide();
		if(data){
			if(data.status){
				alert(data.info);
				location.reload();
			}else{
				alert(data.info);
			}
		}else{	
			alert('网络异常');
		}
	});
});

$('#up').click(function(){
	if(p==1){
		alert('已经是第一页了');
		return false;
	}
	p--;
	withdraw_recode();
});

$('#down').click(function(){
	if(p==count){
		alert('已经是最后一页了');
		return false;
	}
	p++;
	withdraw_recode();
});
withdraw_recode();
var p=1;
var count = 0;
function withdraw_recode(){
	$.post("<?php echo U('Mh/withdraw_recode');?>",{p:p},function(data){
		if(data){
			//console.log(data);
			if(data.status){
				$('#recode').html('');
				$('#pages').html(p+'/'+data.page_list);
				count = data.page_list;
				$('#recode').append(data.info);
			}else{
				alert(data.info);
			}
		}else{	
			alert('网络异常');
		}
	});
}
</script>