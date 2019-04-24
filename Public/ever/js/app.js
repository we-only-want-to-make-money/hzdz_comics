$(function(){
	//分享遮罩层
	$('.goods-share').click(function () {
		document.getElementById('fade').style.display = 'block';
	})
	$('#fade').click(function () {
		document.getElementById('fade').style.display = 'none';
	})
	//商品规格选择
	$("#goods-model dd p span").each(function () {
		$(this).click(function () {
			$("#goods-model dd p span").removeClass("active");
			$(this).addClass("active");
			newPrice = parseFloat(oldPrice) + parseFloat($(this).data("price"));
			$('.goods-price span').html(newPrice.toFixed(2));
			$('#goods-model dt p span').html(newPrice.toFixed(2));
			$('#goods-model dt p q').html($(this).data("count"));
			$("input[name=opt]").val($(this).data("item"));
		})
	})

	//购物车添加数量
	$("#cart-op .icon-add").click(function () {
			oldNum = $(this).siblings("input").val();
			cartId = $(this).parents("dd").attr("data-item");
		newNum = parseInt(oldNum) + 1;
		$(this).siblings("input").val(newNum);
		updateCart(cartId, newNum);
	});
	//减少数量
	$("#cart-op .icon-move").click(function () {
			oldNum = $(this).siblings("input").val();
			cartId = $(this).parents("dd").attr("data-item");
		newNum = parseInt(oldNum) - 1;
		if (oldNum <= 1) {
			msgFade("不能小于最小数量");
		} else {
			updateCart(cartId, newNum);
			$(this).siblings("input").val(newNum);
		}
	});
	//删除商品
	$(".cart-price .icon-delete").click(function () {
		cartId = $(this).parents("dd").attr("data-item");
		if (confirm("确认删除吗？")) {
			updateCart(cartId, 0);
			$(this).parents("dd").remove();
			$(".cart-item dl").each(function () {
				if ($(this).find("dd").length <= 0) {
					$(this).remove();
				}
			});
		}
		return false;
	});

});
//显示商品购买遮罩层
function showGoodsModel(op) {
	if(op == "add"){
		$("#goods-model dl button").attr("onclick","addCart()");
	}else{
		$("#goods-model dl button").attr("onclick","buyNow()");
	}
	$("#goods-model").show();
}

//添加到购物车
function addCart() {
	if(attr.length != $(".attr-name").size()){
		// 还有属性没选择
		msgFade("还有属性没选择!");
		return false;
	}
	var goodsNum = $("#goods-op input").val();
	$.post(
		"./index.php?m=&c=Index&a=addCart",
		{goodsId: goodsId, goodsNum: goodsNum, attr: attr},
		function (data) {
			//console.log(data);
			if (data.status == "1") {
				msgFade(data.msg);
				$("#goods-model").hide();
			} else {
				if(data.url !=''){
					msgFade(data.msg,function(){
						window.location.href=data.url;
					});
				}else{
					msgFade(data.msg);
				}
			}
		},
		"json"
	)
}

//更新购车数量
function updateCart(cartId, num) {
	$.post(
		"./index.php?m=&c=Index&a=updateCart",
		{cartId: cartId, num: num},
		function (data) {
			console.log(data);
			if (data.status == "1") {
				$(".cart-count p span").text("￥" + data.msg);
				msgFade("购物车更新成功！");
			} else {
				msgFade(data.msg);
			}
		},
		"json"
	)
}
//初始化选择
function initCheck() {
	$(".cart-item dl").each(function () {
		if ($(this).children("dd").find(".icon-roundcheckfill").length !== $(this).children("dd").find(".checked").length) {
			$(this).children("dt").children("i").removeClass("checked");
		} else {
			$(this).children("dt").children("i").addClass("checked");
		}
	});
	if ($(".cart-item").find(".icon-roundcheckfill").length !== $(".cart-item").find(".checked").length) {
		$(".cart-count i").removeClass("checked");
	} else {
		$(".cart-count i").addClass("checked");
	}
	updateCheck();
}
//更新购物车选中状态
function updateCheck() {
	var cartIds = new Array;
	$(".cart-item dl dd .icon-roundcheckfill").each(function () {
		if ($(this).hasClass("checked")) {
			cartIds.push($(this).parents("dd").data("item"));
		}
	});
	$.post(
		"./index.php?m=&c=Index&a=cartChecked",
		{cartIds: cartIds.join(",")},
		function (data) {
			console.log(data);
			if (data.status == "1") {
				$(".cart-count p span").text("￥" + data.msg);
			} else {
				msgFade("更新失败，请重试！");
			}
		},
		"json"
	)
}
//立即购买
function buyNow() {
	if(attr.length != $(".attr-name").size()){
		// 还有属性没选择
		msgFade("还有属性没选择!");
		return false;
	}
	var goodsNum = $("#goods-op input").val();
	$.post(
		"./index.php?m=&c=Index&a=buyNow",
		{goodsId: goodsId, goodsNum: goodsNum, attr: attr},
		function (data) {
			if (data.status == "1") {
				window.location.href = data.url;
				$("#goods-model").hide();
			} else {
				if(data.url !=''){
					msgFade(data.msg,function(){
						window.location.href=data.url;
					});
				}else{
					msgFade(data.msg);
				}
			}
		},
		"json"
	)
}


//弹窗信息动画
var t;
function msgFade(msg, callback) {
	clearTimeout(t);
	$(".msg-fade").remove();
	tpl = '<div class="msg-fade"><span>' + msg + '</span></div>';
	$("body").append(tpl);
	$(".msg-fade").fadeIn("300");
	t = setTimeout(function () {
		$(".msg-fade").fadeOut("300", function () {
			$(".msg-fade").remove();
			if (typeof callback === "function") {
				callback();
			} else {
				return true;
			}
		});
	}, 1500);
}

//写cookies
function setCookie(name, value, setTime) {
	var Days = "";
	if (setTime === undefined) {
		Days = 30;
	} else {
		Days = setTime;
	}
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*60*1000);
	document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}
//读取cookies 
function getCookie(name) {
	var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
	if (arr = document.cookie.match(reg))
		return unescape(arr[2]);
	else
		return null;
}
//删除cookies 
function delCookie(name) {
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval = getCookie(name);
	if (cval != null) {
		document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
	}
}

/* function tomedia(src) {
	if (typeof src != 'string')
		return '';
	if (src.indexOf('http://') == 0 || src.indexOf('https://') == 0) {
		return src;
	} else if (src.indexOf('../addons') == 0 || src.indexOf('../attachment') == 0) {
		src = src.substr(3);
		return window.sysinfo.siteroot + src;
	} else if (src.indexOf('./resource') == 0) {
		src = src.substr(2);
		return window.sysinfo.siteroot + 'app/' + src;
	} else if (src.indexOf('images/') == 0) {
		return window.sysinfo.attachurl + src;
	}
} */
//清除html标签
/* function _removeHTMLTag(str) {
	if (typeof str == 'string') {
		str = str.replace(/<script[^>]*?>[\s\S]*?<\/script>/g, '');
		str = str.replace(/<style[^>]*?>[\s\S]*?<\/style>/g, '');
		str = str.replace(/<\/?[^>]*>/g, '');
		str = str.replace(/\s+/g, '');
		str = str.replace(/&nbsp;/ig, '');
	}
	return str;
} */
/*显示隐藏*/
 $("#showFloatMenu").click(function(){
    if($(".floatMenu").css("display")=='none'){
        $(".floatMenu").show();
    }else{
         $(".floatMenu").hide();
    }
})
/*全局搜索*/
$('#gsearch').bind('keypress', function (event) {
	var key_code = event.keyCode || event.charCode;
	if (key_code == "13") {
		var keyword = $('#gsearch').val();
		if (keyword.length <= 0) {
			msgFade("搜索关键词不能小于1位！");
		} else {
			window.location.href = './index.php?i=' + uniacid + 'c=home&a=list&keyword=' + keyword;
		}
	}
});
//点击按钮全局搜索
$('#searchall').bind('click', function () {
	       var keyword = $('#gsearch').val();
		if (keyword.length <= 0) {
			msgFade("搜索关键词不能小于1位！");
		} else {
			window.location.href = './index.php?i=' + uniacid + 'c=home&a=list&keyword=' + keyword;
		}
	
})
/*店铺商品搜索*/
$('#shop_search').bind('keypress', function (event) {
	var key_code = event.keyCode || event.charCode;
	if (key_code == "13") {
	        var spid=$("#spid").val();
		var keyword = $('#shop_search').val();
		if (keyword.length <= 0) {
			msgFade("搜索关键词不能小于1位！");
		} else {
			window.location.href = './index.php?i=' + uniacid + 'c=home&type=shops_search&a=list&spid='+spid+'&keyword=' + keyword;
		}
	}
})
//星级展示
function showStar(obj,level){
	level = parseInt(level);
	gray = '<i class="iconfont icon-favorfill"></i>';
	Shine = '<i class="iconfont icon-favorfill shine"></i>';
	var tpl = "";
	for(var i = 0; i<5; i++){
		if(i < level){
			tpl += Shine;
		}else{
			tpl += gray;
		}
	}
	tpl += '<i class="rate-text">'+level+'分</i>';
	obj.html(tpl);
}
//返回到老家
function commonBack(){
    backUrl = document.referrer;
    if(backUrl === ""){
        window.location.href="./index.php?i="+uniacid+"&c=home";
    }else{
        history.go(-1);
    }
}


//格式化时间
function formatDate(now) {
	if(now === undefined){
		now = new Date();
	}else{
		now = now * 1000;
		now = new Date(parseInt(now));
	}
	
	var year = now.getFullYear();
	var month = now.getMonth() + 1;
	var date = now.getDate();
	var hour = now.getHours();
	var minute = now.getMinutes();
	return   year + "-" + month + "-" + date + " " + hour + ":" + minute;
}
// function goBack(){
//        document.referrer === '' ?
//          window.location.href = 'http://www.baidu.com' :
//          window.history.go(-1);
//      }
