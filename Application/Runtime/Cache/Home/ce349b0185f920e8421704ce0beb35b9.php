<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0045)https://m.efucms.com/book/<?php echo U('Mh/book_shelf');?> -->
<html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo ($_CFG['site']['name']); ?> - 书架</title>
    <!-- 共用引入资源.开始 -->

    <script src="/Public/home/mhjs/stats.js" name="MTAH5" sid="500462993"></script>
    <meta name="viewport" content="designWidth=750,width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- 防止加载lib.flexible加载的时候文字由大变小的闪烁 -->
    <style>html,body{font-size:12px;}</style>
    <!-- lib.flexible 移动端相对适应比例 必须在浏览器head类 -->
    <script type="text/javascript">
        !function (a, b) { function c() { var b = f.getBoundingClientRect().width; b / i > 540 && (b = 540 * i); var c = b / 10; f.style.fontSize = c + "px", k.rem = a.rem = c } var d, e = a.document, f = e.documentElement, g = e.querySelector('meta[name="viewport"]'), h = e.querySelector('meta[name="flexible"]'), i = 0, j = 0, k = b.flexible || (b.flexible = {}); if (g) {  var l = g.getAttribute("content").match(/initial\-scale=([\d\.]+)/); l && (j = parseFloat(l[1]), i = parseInt(1 / j)) } else if (h) { var m = h.getAttribute("content"); if (m) { var n = m.match(/initial\-dpr=([\d\.]+)/), o = m.match(/maximum\-dpr=([\d\.]+)/); n && (i = parseFloat(n[1]), j = parseFloat((1 / i).toFixed(2))), o && (i = parseFloat(o[1]), j = parseFloat((1 / i).toFixed(2))) } } if (!i && !j) { var p = (a.navigator.appVersion.match(/android/gi), a.navigator.appVersion.match(/iphone/gi)), q = a.devicePixelRatio; i = p ? q >= 3 && (!i || i >= 3) ? 3 : q >= 2 && (!i || i >= 2) ? 2 : 1 : 1, j = 1 / i } if (f.setAttribute("data-dpr", i), !g) if (g = e.createElement("meta"), g.setAttribute("name", "viewport"), g.setAttribute("content", "initial-scale=" + 1 + ", maximum-scale=" + 1 + ", minimum-scale=" + 1 + ", user-scalable=no"), f.firstElementChild) f.firstElementChild.appendChild(g); else { var r = e.createElement("div"); r.appendChild(g), e.write(r.innerHTML) } a.addEventListener("resize", function () { clearTimeout(d), d = setTimeout(c, 300) }, !1), a.addEventListener("pageshow", function (a) { a.persisted && (clearTimeout(d), d = setTimeout(c, 300)) }, !1), "complete" === e.readyState ? e.body.style.fontSize = 12 * i + "px" : e.addEventListener("DOMContentLoaded", function () { e.body.style.fontSize = 12 * i + "px" }, !1), c(), k.dpr = a.dpr = i, k.refreshRem = c, k.rem2px = function (a) { var b = parseFloat(a) * this.rem; return "string" == typeof a && a.match(/rem$/) && (b += "px"), b }, k.px2rem = function (a) { var b = parseFloat(a) / this.rem; return "string" == typeof a && a.match(/px$/) && (b += "rem"), b } }(window, window.lib || (window.lib = {}));
    </script>
    <link rel="stylesheet" type="text/css" href="/Public/home/mhcss/style.min.css">
    <script type="text/javascript" src="/Public/home/mhjs/fundebug.0.1.7.min.js" apikey="ba3a0e0d938e92b44f279067dffb8d071ee87fc35eb75918b7a900e8581a955d"></script>
    <script type="text/javascript" src="/Public/home/mhjs/jquery.js"></script>
    <!-- 共用引入资源.结束 -->
    <script type="text/javascript" src="/Public/home/mhjs/saved_resource"></script>
    <script>(function(){new Image().src='//res.efucms.com/wap_v3/images/placeholder-unruly.png?v=20171208';})();</script>
</head>
<body style="font-size: 12px;">
<div class="navbar flt">
    <nav class="tab-box">
        <div class="item">
            <a href="<?php echo U('Mh/book_shelf');?>" class="active">收藏</a>
        </div>
        <div class="item">
            <a href="<?php echo U('Mh/book_recent_read');?>">历史</a>
        </div>
    </nav>
    <div class="action">
    </div>
</div>

<?php if($cnt <= 0): ?><div class="bs-box" style="margin-bottom: 0px;bottom: 1.333rem;">
	    <div class="common-ne">
	        <div class="subject">您还没有收藏记录</div>
	        <div class="action">
	            <a href="<?php echo U('Mh/index');?>" class="btn">去找几本看看</a>
	        </div>
	    </div>
</div>
<?php else: ?>
<div class="bs-box" style="margin-bottom: 0px;bottom: 1.333rem;">
    <div class="row-list" id="shelf-container" data-scroll="true">
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="item" data-looking="0">
			<?php if($vo['type'] == 'xs'): ?><a href="<?php echo U('Book/bookinfo');?>&bid=<?php echo ($vo["mhid"]); ?>">
			<?php else: ?>
				<a href="<?php echo U('Mh/bookinfo');?>&mhid=<?php echo ($vo["mhid"]); ?>"><?php endif; ?>
            
                <div class="cover">
					<img class="" src="<?php echo ($vo["cover_pic"]); ?>" data-original="<?php echo ($vo["cover_pic"]); ?>" style="background: rgb(238, 238, 238); display: block;width:125px;height:166px;" />
				</div>
				<div class="body">
					<div class="title"><?php echo ($vo["title"]); ?></div>
					<div class="text">共<?php echo ($vo["episodes"]); ?>话</div>
				</div>
            </a>
			<div class="cp-box" style="display:none;">
				<input type="checkbox" name="edit_history_books" class="selected-switch" value="0_10231">
				<div class="swtich"></div>
			</div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
      </div>
</div><?php endif; ?>

<div class="tabar flb" id="footer_nav">
    <nav class="nav hls1">
       <!--  <div class="item">
            <a href="<?php echo U('Mh/video');?>">
                <i class="icon-book"></i><div class="title">必看视频</div>
            </a>
        </div> -->
        <div class="item">
            <a href="<?php echo U('Mh/book_shelf');?>" class="active">
                <i class="icon-book"></i><div class="title">书架</div>
            </a>
        </div>
        <div class="item">
            <a href="<?php echo U('Book/index');?>">
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

<div class="editable-bar" id="d_layer" style="display:none;">
    <div class="row">
        <div class="cp-box">
            <input type="checkbox" name="select_all" class="select_all">
            <div class="text"><i></i>全选</div>
        </div>
        <div class="action">
            <a href="javascript:void(0));" class="btn delete-selected-btn">
                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M38.5 18v20a4.5 4.5 0 0 1-4.5 4.5H14A4.5 4.5 0 0 1 9.5 38V18h-3v20a7.51 7.51 0 0 0 7.5 7.5h20a7.51 7.51 0 0 0 7.5-7.5V18zM18.5 6.42a.87.87 0 0 1 .72-.92h9.46a.9.9 0 0 1 .81 1v4H40v-3h-7.5v-.92a3.9 3.9 0 0 0-3.72-4.08h-9.65a3.86 3.86 0 0 0-3.63 4v1H8v3h10.5z" fill="#ff730a"></path><path fill="#ff730a" d="M30.5 24h3v13h-3zM23.5 27h3v10h-3zM15.5 24h3v13h-3z"></path></svg>确定删除(<span id="delete_count">0</span>)
            </a>
        </div>
    </div>
</div>

<div class="mask-box delete-selected-confirm" style="display: none;"></div>
<div class="modal confirm-modal delete-selected-confirm" style="display: none;">
    <div class="inner">
        <div class="confirm-box">
            <div class="body">
                <div class="title">共选中<span id="confirm-delete-count">0</span>个作品</div>
                <div class="text">是否删除已选中作品?</div>
            </div>
            <div class="action">
                <a href="javascript:void(0);" class="btn cancel">取消</a>
                <a href="javascript:void(0);" class="btn confirm">确定</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var $select_all = $('.select_all'),$delete_confirm = $('.delete-selected-confirm'),$delete_count = $("#delete_count"),all_selected = "#shelf-container .cp-box .selected-switch.selected",$all_selected_switch = $("#shelf-container .cp-box .selected-switch"), all_size = $all_selected_switch.size();
    function show_shelf_edit(){
        $('#footer_nav,#niubi').hide();
        $('#bibibi').text('取消');
        $('.editable-bar,#shelf-container .cp-box,#bibibi').show();
        $("#shelf-container").addClass('editable');
        $(this).off('click',show_shelf_edit).on('click',hide_shelf_edit);
        $("#shelf-container .item[data-looking='1']").on('click',looking_stop_tobook);
    }
    function hide_shelf_edit(){
        $('.editable-bar,#shelf-container .cp-box,#bibibi').hide();
        $('#footer_nav,#niubi').show();
        $("#shelf-container").removeClass('editable');
        $delete_count.text("0");
        $select_all.prop('checked',false);
        $all_selected_switch.removeClass('selected').prop('checked',false);
        $(this).off('click',hide_shelf_edit).on('click',show_shelf_edit);
        $("#shelf-container .item[data-looking='1']").off('click',looking_stop_tobook);
    }
    function looking_stop_tobook(event){
        if(document.all){
            window.event.returnValue = false;
        }else{
            event.preventDefault();
        }
    }
    $('#edit_shelf_btn').on('click',show_shelf_edit);
    $all_selected_switch.click(function(){
        $(this).toggleClass('selected');
        var selected_size = $(all_selected).size();
        $delete_count.text(selected_size);
        if(selected_size==all_size){
            $select_all.prop('checked',true);
        }else{
            $select_all.prop('checked',false);
        }
    });
    $select_all.click(function(){
        if($select_all.prop('checked')){
            $all_selected_switch.addClass('selected').prop('checked',true);
            $delete_count.text($(all_selected).size());
        }else{
            $all_selected_switch.removeClass('selected').prop('checked',false);
            $delete_count.text('0');
        }
    });
    $(".delete-selected-btn").click(function(){
        var all_selected_size = $(all_selected).size();
        if(all_selected_size<=0){
            bh_msg_tips('您没有选中任何记录');
            return false;
        }
        $delete_confirm.find('#confirm-delete-count').text(all_selected_size).end().show();
    });
    $(".delete-selected-confirm .action .cancel").click(function(){
        $delete_confirm.hide();
    });
    $(".delete-selected-confirm .action .confirm").click(function(){
        var reads = $("#shelf-container").find('.item').length;
        var cur_ids = $(all_selected).map(function(){return $(this).val()}).get().join(',');
        //得到选中的数据
        if(!cur_ids){
            bh_msg_tips('您没有选中任何记录');
            return false;
        }
        if($delete_confirm.hasClass('posting')){
            bh_msg_tips('请稍候，请求正在发送中');
            return false;
        }
        var url = "/book/ajax_del_book_shelf";
        var data = {book_ids:cur_ids};
        $delete_confirm.addClass('posting');
        AjaxJson(url,data,function(res){
            $delete_confirm.hide().removeClass('posting');
            if(res.status*1 == 1){
                $(all_selected).parents('.item').remove();
                $delete_count.text("0");
                $select_all.prop('checked',false);
                $('#bibibi').text('完成');
                if(reads > 19){
                    load_shelfs();
                }
                shelf_null_view();
            }else{
                bh_msg_tips(res.info);
            }
        })
    });

    function load_shelfs(){
        var url = "/book/ajax_load_shelf";
        var data = {};
        AjaxJson(url,data,function(res){
            if(res.length > 0){
                var loadHtml = '';
                $.each(res, function (index, obj) {
                    if(obj.btype==0){
                        var url="/book/"+obj.bid+"/"+obj.cid+"/";
                    }else{
                        var url="/novel/"+obj.bid+"/"+obj.cid+"/";
                    }
                    if(obj.is_looking==1){
                        var is_read = '<i class="icon-tj"><img style="width: 100%;" src="//res.efucms.com/wap_v3/images/zhengzaikan.png?v=20171201" alt=""></i>';
                    }else{
                        var is_read='';
                    }
                    loadHtml += '<div class="item" data-looking="'+obj.is_looking+'">'+
                        '<a href="'+url+'" class="link"><div class="cover">'+
                        '<img class="img-ajax-lazy" src="//res.efucms.com/wap_v3/images/placeholder-unruly.png?v=20171208" data-original="'+obj.bookInfo.book_unruly+'">'+
                        '</div><div class="body">'+
                        '<div class="title">'+obj.bookInfo.book_name+'</div>'+
                        '<div class="text">'+obj.readChapter+'话/'+obj.lastChapter+'话</div></div>"+is_read+"</a>'+
                        '<div class="cp-box">'+
                        '<input type="checkbox" name="edit_history_books" class="selected-switch" value="'+obj.btype+'_'+obj.bid+'">'+
                        '<div class="swtich"></div></div></div>';

                });
                $('#shelf-container').html(loadHtml);
                trigger_lazy_ajax();
                $('.selected-switch').on('click',function(){
                    $(this).toggleClass('selected');
                    var selected_size = $(all_selected).size();
                    $delete_count.text(selected_size);
                    if(selected_size==all_size){
                        $select_all.prop('checked',true);
                    }else{
                        $select_all.prop('checked',false);
                    }
                });
                $('.select_all').on('click',function(){
                    if($select_all.prop('checked')){
                        $('.selected-switch').addClass('selected').prop('checked',true);
                        $delete_count.text($(all_selected).size());
                    }else{
                        $('.selected-switch').removeClass('selected').prop('checked',false);
                        $delete_count.text('0');
                    }
                });
            }else{
                //删除完了，无数据提醒
                shelf_null_view();
            }
        })
    }

    function shelf_null_view(){
        var reads = $("#shelf-container").find('.item').length;
        if(reads==0 || reads <9){
            $('.bs-box').css('bottom','1.333rem');
            $('.bs-box').css('margin-bottom','0rem');
            $('.bs-box').css('padding-bottom','0rem');
        }
        if(reads == 0){
            $('.editable-bar,#shelf-container .cp-box,#bibibi,#niubi').hide();
            $('#footer_nav,#shelf-container .action').show();
            $('.bs-box').html('<div class="common-ne"><div class="subject">您还没有历史记录</div><div class="action"><a href="/" class="btn">去找几本漫画看看</a></div></div>');
        }
    }

    $(function(){
        trigger_lazy_ajax();
    })
</script>
<!-- 统计 -->
<script type="text/javascript" src="/Public/home/mhjs/gcoupon.min.js"></script>
<script type="text/javascript">
    function addLoadEvent(func){
        if (typeof window.addEventListener != "undefined") {
            window.addEventListener("load",func,false);
        } else {
            window.attachEvent("onload",func) ;
        }
    }
    function tj_getcookie(name){
        var nameValue = "";
        var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
        if (arr = document.cookie.match(reg)) {
            nameValue = decodeURI(arr[2]);
        }
        return nameValue;
    }
    function getQueryString(name){
        var reg = new RegExp("(^|&)"+name+"=([^&]*)(&|$)","i");
        var r = window.location.search.substr(1).match(reg);
        if (r!=null) return unescape(r[2]); return null;
    }
    addLoadEvent(function(){
        var error_img = new Image(),channel=tj_getcookie('qrmh_channel'),channel_type=tj_getcookie('qrmh_channel_type');
        error_img.onerror=null;
        error_img.src="//www.efucms.com/stats/?c="+channel+"&ct="+channel_type+"&rnd="+(+new Date);
        error_img=null;
        //某些地方页面缓存-检测
        var p_img =new Image(), p_userid = parseInt("5414066"),c_auth=tj_getcookie('qrmh_auth'),p_reload = getQueryString('p_reload');
        if(p_userid>0&&c_auth==''){
            if(p_reload==null){
                var url = window.location.href;
                //刷新一次页面
                window.location.href=url.indexOf("?")>0?(url+'&p_reload=1&reload_time='+(+new Date)):(url+'?p_reload=1&reload_time='+(+new Date));
            }else{
                //还是出现这个问题的话，就记录下来
                p_img.onerror=null;
                p_img.src="//www.efucms.com/page_stats/?p_userid="+p_userid+"&rnd="+(+new Date);
            }
        }
        p_img=p_userid=c_auth=p_reload=null;
    });
    //update byefucms 20170906 某些手机系统下，旋转屏幕出现页面混乱问题，通过延时500ms滚动页面1个单位得以恢复正常
    var evt = "onorientationchange" in window ? "orientationchange" : "resize";
    window.addEventListener(evt, function() {
        setTimeout(function(){
            window.scrollTo(0, window.pageYOffset+1);
        },500);
    }, false);
</script>
<!-- 统计 -->
<!-- 第三方qq统计 -->
<!-- <script type="text/javascript">
    var _mtac = {};
    (function() {
        setTimeout(function(){
            var mta = document.createElement("script");
            mta.src = (("https:" == document.location.protocol) ? "https://" : "http://")+"pingjs.qq.com/h5/stats.js?v2.0.4";
            mta.setAttribute("name", "MTAH5");
            mta.setAttribute("sid", "500462993");
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(mta, s);
        },888);
    })();
</script> -->
<!-- 第三方qq统计 -->
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
</body></html>