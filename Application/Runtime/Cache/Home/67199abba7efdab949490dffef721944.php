<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html style="overflow-x:hidden">
<head>
    <title><?php echo ($_CFG['site']['name']); ?> - 首页</title>
    <meta name="keywords" content="漫画,漫画大全,漫画图片,免费漫画,原创漫画,在线漫画,神漫画,漫画下载,看漫画,手机漫画,小说漫画源码" />
    <meta name="description" content="efucms小说漫画源码系统致力于打造正版小说漫画在线阅读平台搭建、网站开发,连载众多优秀原创国漫等海量精彩内容,小说漫画网站建设,就用易付漫画小说系统源码!" />
    <!-- 共用引入资源.开始 -->
    <meta charset="UTF-8">

    <meta name="viewport" content="designWidth=750,width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- 防止加载lib.flexible加载的时候文字由大变小的闪烁 -->
    <style>html,body{font-size:12px;}</style>
    <!-- lib.flexible 移动端相对适应比例 必须在浏览器head类 -->
    <script type="text/javascript">
        !function (a, b) { function c() { var b = f.getBoundingClientRect().width; b / i > 540 && (b = 540 * i); var c = b / 10; f.style.fontSize = c + "px", k.rem = a.rem = c } var d, e = a.document, f = e.documentElement, g = e.querySelector('meta[name="viewport"]'), h = e.querySelector('meta[name="flexible"]'), i = 0, j = 0, k = b.flexible || (b.flexible = {}); if (g) {  var l = g.getAttribute("content").match(/initial\-scale=([\d\.]+)/); l && (j = parseFloat(l[1]), i = parseInt(1 / j)) } else if (h) { var m = h.getAttribute("content"); if (m) { var n = m.match(/initial\-dpr=([\d\.]+)/), o = m.match(/maximum\-dpr=([\d\.]+)/); n && (i = parseFloat(n[1]), j = parseFloat((1 / i).toFixed(2))), o && (i = parseFloat(o[1]), j = parseFloat((1 / i).toFixed(2))) } } if (!i && !j) { var p = (a.navigator.appVersion.match(/android/gi), a.navigator.appVersion.match(/iphone/gi)), q = a.devicePixelRatio; i = p ? q >= 3 && (!i || i >= 3) ? 3 : q >= 2 && (!i || i >= 2) ? 2 : 1 : 1, j = 1 / i } if (f.setAttribute("data-dpr", i), !g) if (g = e.createElement("meta"), g.setAttribute("name", "viewport"), g.setAttribute("content", "initial-scale=" + 1 + ", maximum-scale=" + 1 + ", minimum-scale=" + 1 + ", user-scalable=no"), f.firstElementChild) f.firstElementChild.appendChild(g); else { var r = e.createElement("div"); r.appendChild(g), e.write(r.innerHTML) } a.addEventListener("resize", function () { clearTimeout(d), d = setTimeout(c, 300) }, !1), a.addEventListener("pageshow", function (a) { a.persisted && (clearTimeout(d), d = setTimeout(c, 300)) }, !1), "complete" === e.readyState ? e.body.style.fontSize = 12 * i + "px" : e.addEventListener("DOMContentLoaded", function () { e.body.style.fontSize = 12 * i + "px" }, !1), c(), k.dpr = a.dpr = i, k.refreshRem = c, k.rem2px = function (a) { var b = parseFloat(a) * this.rem; return "string" == typeof a && a.match(/rem$/) && (b += "px"), b }, k.px2rem = function (a) { var b = parseFloat(a) / this.rem; return "string" == typeof a && a.match(/px$/) && (b += "rem"), b } }(window, window.lib || (window.lib = {}));
    </script>
    <link rel="stylesheet" type="text/css" href="/Public/home/mhcss/style.min.css?v=201712191015" />
    <script type="text/javascript" src="/Public/home/mhjs/fundebug.0.1.7.min.js?v=1501078790472" apikey="ba3a0e0d938e92b44f279067dffb8d071ee87fc35eb75918b7a900e8581a955d"></script>
    <script type="text/javascript" src="/Public/home/mhjs/jquery.js?v=1501078790472"></script>
    <!-- 共用引入资源.结束 -->
    <script type="text/javascript" src="/Public/home/mhjs/jquery.lazyload.js?v=20171228"></script>

    <style type="text/css">
        #banner-container { display: block; position: relative; z-index: 1; width: 100%; overflow: hidden; }
        #banner-container .loading { display: block; width: 70px; text-align: center; font-size: 0; position: absolute; top: 50%; left: 50%; -webkit-transform: translate3d(-50%,-50%,0); transform: translate3d(-50%,-50%,0); }
        #banner-container .loading .item { width: 18px; height: 18px; margin: 0 2px; background-color: #333333; border-radius: 100%; display: inline-block; -webkit-animation: loading-delay 1.4s infinite ease-in-out; animation: loading-delay 1.4s infinite ease-in-out; -webkit-animation-fill-mode: both; animation-fill-mode: both; }
        #banner-container .loading .item:nth-child(2) { -webkit-animation-delay: -0.16s; animation-delay: -0.16s; }
        #banner-container .loading .item:nth-child(3) { -webkit-animation-delay: -0.32s; animation-delay: -0.32s; }
        @-webkit-keyframes loading-delay {
            0%,80%,100% {
                -webkit-transform: scale(0);
            }
            40% {
                -webkit-transform: scale(1);
            }
        }
        @keyframes loading-delay {
            0%,80%,100% {
                transform: scale(0);
                -webkit-transform: scale(0);
            }
            40% {
                transform: scale(1);
                -webkit-transform: scale(1);
            }
        }
		.navbar > .tab-box{
			width:5rem;
		}
    </style>
</head>
<body>
<div class="navbar flt">
    <nav class="tab-box">
		<div class="item">
            <a href="<?php echo U('Mh/index');?>" class="active">漫画</a>
        </div>
        <div class="item">
            <a href="<?php echo U('Book/index');?>">小说</a>
        </div>
		 <div class="item">
            <a href="<?php echo U('Yook/index');?>">听书</a>
        </div>
    </nav>
    <div class="action">
        <a href="<?php echo U('Mh/load_search');?>" class="btn">
            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
            	<path d="M35.94 35.94l7.71 7.71" fill="none" stroke="#fff" stroke-width="3"/>
            	<circle cx="23.3" cy="23.3" r="18.5" fill="none" stroke="#fff" stroke-width="3"/>
            	<path d="M11.72 23.15A12 12 0 0 1 24.5 12" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="3"/>
            </svg>
        </a>
    </div>
</div>

<div id="banner-container">
    <div class="loading">
        <div class="item"></div><div class="item"></div><div class="item"></div>
    </div>
    <div class="portal-slick mt-navbar">
        <?php if(is_array($_banner['config'])): $i = 0; $__LIST__ = $_banner['config'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="item">
				<a href="<?php echo ($vo["url"]); ?>">
				<img class="slide_loading" src="<?php echo ($vo["pic"]); ?>" data-original="<?php echo ($vo["pic"]); ?>" alt="漫画轮播图" />
				</a>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<style>
.item_cate{
	width: 1.333rem;
    height: 1.333rem;
}	
</style>
<nav class="portal-nav">
	<?php if(is_array($_mhcate)): foreach($_mhcate as $k=>$v): if($v['show'] == 1 and $v['isshow']): ?><div class="item">
			<a href="<?php echo ($v["url"]); ?>">
				<img src="<?php echo ($v["pic"]); ?>" class="item_cate"/>
				<div class="title"><?php echo ($v["name"]); ?></div>
			</a>
		</div><?php endif; endforeach; endif; ?>
</nav>


<?php if(is_array($mhcate)): foreach($mhcate as $k=>$v): if($v['list']): ?><div class="bm-box mt-10">
		<div class="head">
			<div class="title dot"><?php echo ($v["name"]); ?></div>
			<div class="pull-right">
				<a href="<?php echo U('Mh/mhlist',array('cate'=>$k));?>">
					更多 <svg width="7" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 24">
					<title>right-arrow</title>
					<path d="M1.91 1.93L12.06 12 1.91 22" fill="none" stroke="#ff5420" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
				</a>
			</div>
		</div>
		<div class="books-row">
			<?php if(is_array($v['list'])): $i = 0; $__LIST__ = $v['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="item">
				<a href="<?php echo U('Mh/bookinfo');?>&mhid=<?php echo ($vo["id"]); ?>">
					<img class="lazy" src="<?php echo ($vo["cover_pic"]); ?>" data-original="<?php echo ($vo["cover_pic"]); ?>" style="width:110px;height:146px;" />
					<div class="title"><?php echo ($vo["title"]); ?></div>
					<div class="text">更新至<?php echo ($vo["episodes"]); ?>话</div>
				</a>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div><?php endif; endforeach; endif; ?>

<style>

</style>
<?php if($mf): ?><div class="bm-box mt-10">
	<div class="head">
		<div class="title dot">免费专区</div>
		<div class="pull-right">
			<a href="<?php echo U('Mh/book_free');?>">
				更多 <svg width="7" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 24">
				<title>right-arrow</title>
				<path d="M1.91 1.93L12.06 12 1.91 22" fill="none" stroke="#ff5420" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
			</a>
		</div>
	</div>
	<div class="books-row">
		<?php if(is_array($mf)): $i = 0; $__LIST__ = $mf;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="width:100%;padding-bottom: 20px;border-bottom: 1px solid #ddd;position:relative;">
				<img src="<?php echo ($vo["cover_pic"]); ?>" data-original="<?php echo ($vo["cover_pic"]); ?>" style="width:88px;height:110px;float:left;margin:10px;" />
				<div class="title" style="margin-top: 15px;font-size: 17px;color: #000;"><?php echo ($vo["title"]); ?></div>
				<div class="text" style="margin-top: 5px;font-size: 14px;color: #666666;"><?php echo ($vo["author"]); ?></div>
				<div class="text" style="margin-top: 5px;font-size: 14px;color: #666666;height:40px;text-overflow: ellipsis;overflow: hidden; word-wrap: break-word;word-break: break-all;"><?php echo ($vo["summary"]); ?></div>
				<div onclick="location.href=&#39;<?php echo U('Mh/bookinfo');?>&mhid=<?php echo ($vo["id"]); ?>&#39;" style="position: absolute;top: 10px;right: 10px;padding: 8px;border: 1px solid #FF9800;border-radius: 5px;color: #FF9800;">立即阅读</div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
</div>
<?php else: ?>
	<div style="margin:10px 0;width:100%;border:1px solid #eeeeee"></div><?php endif; ?>
<div style="width:200px;height:200px;margin:20px auto 60px auto">
	<?php if($member['gqrcode']): ?><img src="<?php echo ($member['gqrcode']); ?>" style="width:100%;height:100%;" />
	<?php else: ?>
	<img src="<?php echo ($_site['qrcode']); ?>" style="width:100%;height:100%;" /><?php endif; ?>
	<span style="display: block;width: 100%;text-align: center;font-size: 14px;color: #666666;"></br>客服微信号：<?php echo ($_site['weixin']); ?></span>
</div>
<div style="clear:both;height:1.4rem;"></div>
<div class="backtop" id="icon-top" style="display:none">
    <a href="javascript:void(0);" class="top">顶部</a>
</div>
<div class="tabar flb">
    <nav class="nav hls1">
        <!-- <div class="item">
            <a href="<?php echo U('Mh/video');?>">
                <i class="icon-book"></i><div class="title">必看视频</div>
            </a>
        </div> -->
        <div class="item">
            <a href="<?php echo U('Mh/book_shelf');?>">
                <i class="icon-book"></i><div class="title">书架</div>
            </a>
        </div>
        <div class="item">
            <a href="<?php echo U('Mh/index');?>" class="active">
                <i class="icon-home"></i><div class="title">首页</div>
            </a>
        </div>
        <div class="item">
            <a href="<?php echo U('Mh/my');?>">
                <i class="icon-user"></i><div class="title">我的</div>
            </a>
        </div>
    </nav>
</div>
<script type="text/javascript">
    $(function(){
        //距离100px自动加载下一张图片
        $(".item img.lazy").lazyload({ threshold:100,effect:'fadeIn' });
        $("#single-media div").show();
        $('.portal-slick').slick({
            arrows: false,
            dots: true,
            autoplay: true,
            autoplaySpeed: 3000,
            adaptiveHeight: true
        });  
    });
</script>
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
</body>
</html>