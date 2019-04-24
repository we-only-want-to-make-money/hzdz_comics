if (!Array.prototype.forEach) {  
    Array.prototype.forEach = function(callback, thisArg) {  
        var T, k;  
        if (this == null) {  
            throw new TypeError(" this is null or not defined");  
        }  
        var O = Object(this);  
        var len = O.length >>> 0; // Hack to convert O.length to a UInt32  
        if ({}.toString.call(callback) != "[object Function]") {  
            throw new TypeError(callback + " is not a function");  
        }  
        if (thisArg) {  
            T = thisArg;  
        }  
        k = 0;  
        while (k < len) {  
            var kValue;  
            if (k in O) {  
                kValue = O[k];  
                callback.call(T, kValue, k, O);  
            }  
            k++;  
        }  
    };  
}

//字符串格式化
String.prototype.format= function(){
   var args = arguments;
   return this.replace(/\{(\d+)\}/g,function(s,i){
     return args[i];
   });
}
//字符串拼接
function StringBuilder(){
    this._strings_= new Array;
}
StringBuilder.prototype.append= function(str){
    this._strings_.push(str);
};
StringBuilder.prototype.toString= function(){
    return this ._strings_.join("" );
};

var clientHeight = $(window).height();
var maxIframeHeight = clientHeight-50;

var shop_car_goods_number = $.cookie("BYvraS_shopCarNumber");
if(shop_car_goods_number>0){
    $(".shop-car-tip").removeClass("hidden");
    $(".shop-car-tip").html(shop_car_goods_number);
}

//所有的ajax form提交,由于大多业务逻辑都是一样的，故统一处理
var ajaxForm_list = $('form.J_ajaxForm');
if (ajaxForm_list.length) {

    $('.J_ajax_submit_btn').on('click', function (e) {
        e.preventDefault();
        /*var btn = $(this).find('button.J_ajax_submit_btn'),
			form = $(this);*/
        var btn = $(this),
            form = btn.parents('form.J_ajaxForm');
        
        if(btn.hasClass("disabled")){
        	return;
        }
        form.ajaxSubmit({
            url: btn.data('action') ? btn.data('action') : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
            dataType: 'json',
            beforeSubmit: function (arr, $form, options) {
                var text = btn.text();

                //按钮文案、状态修改
                btn.text(text + '中...').prop('disabled', true).addClass('disabled').css("letter-spacing","0");
            },
            success: function (data, statusText, xhr, $form) {
                var text = btn.text();
                //按钮文案、状态修改
                btn.removeClass('disabled').prop('disabled',false).text(text.replace('中...', '')).css("letter-spacing","10px").parent().find('span').remove();
                if(data.status){
                	layer_success(data.info,data.url);
                }else{
                	layer_error(data.info,data.url);
                }
            }
        });
    });
}

/*日期初始化*/
var currYear = (new Date()).getFullYear();	
var opt={};
opt.date = {preset : 'date'};
opt.datetime = {preset : 'datetime'};
opt.time = {preset : 'time'};
opt.default = {
	theme: 'android-ics light', //皮肤样式
	display: 'modal', //显示方式 
	mode: 'scroller', //日期选择模式
	dateFormat: 'yyyymmdd',
	lang: 'zh',
	showNow: true,
	nowText: "今天",
	startYear: currYear - 50, //开始年份
	endYear: currYear + 10 //结束年份
};


function open_iframe_layer(url,style,title){
	//加载层
	layer.open({type: 2});
	if(title==null||title==''){
		$.get(url,function(data){
			layer.closeAll();
			layer.open({
			    type: 1,
			    shadeClose: true,
			    content: data,
			    style:style
			}); 
		});
	}else{
		$.get(url,function(data){
			layer.closeAll();
			layer.open({
			    type: 1,
			    title:[title],
			    shadeClose: true,
			    content: data,
			    style:style
			}); 
		});
	}
};

function ajax_alert(data){
	if(data.status){
		layer_success(data.info,data.url);
	}else{
		layer_error(data.info,data.url);
	}
}

function layer_success(msg,url){
	if(url){
		location.href=url;return;
	}else{
		layer.open({
			content:msg,
			btn:['确定'],
			shadeClose:false,
			yes: function(index){
				layer.close(index);
		    }
		});
	}
}

function layer_error(msg,url){
	layer.open({
		content:msg,
		btn:['确定'],
		style:"max-width:60%",
		yes: function(index){
			if(url){
				location.href=url;
			}
			layer.close(index);
	    }
	});
}

function setInput(name,obj){
	$("input[name="+name+"]").val(obj);
	layer.closeAll('iframe');
}

function layer_alert(msg,icon){
	layer.open({
		content:msg
	});
}

function redirect(url){
	location.href=url+"?v="+Math.random();
	return false;
}

function developing(){
	layer_alert("功能开发中...");
}

/**
 * 服务加入/减少到购物车
 */
function pushShopCar(post_url,userid,service_id,method,number){
	if(!arguments[3]) method = "setInc";
	if(!arguments[4]) number = 1;
	//购物车内容
	var jsonShopCar = $.cookie("BYvraS_shopCar");
	//购物车数据转格式
	var shopCar = new Object();
	if(jsonShopCar=='[object Object]'||typeof(jsonShopCar)=='undefined'){
		//shopCar = new Array();
	}else{
		shopCar = db_cookie=="1"?eval('('+ jsonShopCar+')'):JSON.parse(jsonShopCar);
	}
	//购物车增加订单
	if(typeof(shopCar[service_id]) == "undefined"||!IsNum(shopCar[service_id])||!shopCar[service_id]){
		if(method=="setInc"){
			shopCar[service_id] = 1;
		}
	}else{
		shopCar[service_id] = parseInt(shopCar[service_id]);
		if(method=="setInc"){
			shopCar[service_id] += number;
		}else if(method=="setDec"){
			var minusNumber = number;
			//当number=-1时，表示删除该服务项
			if(shopCar[service_id]<=number||number==-1){
				minusNumber =shopCar[service_id];
				delete shopCar[service_id];
			}else{
				shopCar[service_id] -= number;
			}
		}
		
	}
	jsonShopCar =  JSON.stringify(shopCar);
	$.cookie("BYvraS_shopCar",jsonShopCar, { expires: 7, path: '/' });
	//购物车订单数量
	var shopCarNumber = $.cookie("BYvraS_shopCarNumber");
	if(method=="setInc"){
		shopCarNumber = isNaN(shopCarNumber)?1:Number(shopCarNumber)+Number(number);
	}else if(method=="setDec"){
		shopCarNumber = isNaN(shopCarNumber)?0:Number(shopCarNumber)-Number(minusNumber);
	}
	
	$.cookie("BYvraS_shopCarNumber",shopCarNumber, { expires: 7, path: '/' });
	console.log(shopCarNumber);
	//通知服务器
	$.ajax({
		url: post_url,
		type: "POST",
		data:{"service_id":service_id,"userid":userid,"method":method,"number":number},
		async: false,
		success: function(data){}
	});
	
}

/*
 * 效宇的js
 * 快捷alert
 * message 提示信息
 * cancelTitle:取消的text
 * ensureTitle:确认的text
 * cancelFunc:取消时需要调用的方法
 * ensureFunc:确认时需要调用的方法
 * canBePause:是否点击黑影需要消失，默认为false
 * */
function showAlert(message , cancelTitle , ensureTitle ,cancelFunc , ensureFunc , canBePause){
	if(canBePause == null)
	{
		canBePause = false;
	}
	layer.open({
		content:message != null ? message : "需要输入提示信息！",
		btn: [ensureTitle != null ? ensureTitle : "确认", cancelFunc != null ?cancelTitle : "取消"],
		shadeClose: canBePause,
		yes: function(e){
			layer.close(e);    //关闭提示框
			if(ensureFunc != null){
			    ensureFunc();
			}
		}, no: function(e){
			layer.close(e);
			if(cancelFunc != null){
			    cancelFunc()
			}
		}
	});
}

function showEasyAlert(message , btnTitle , clickAction , canBePause){
	if(canBePause == null){
		canBePause = false;
	}
	layer.open({
		content:message != null ? message : "需要输入提示信息！",
		btn: [btnTitle != null ? btnTitle : "确认"],
		shadeClose: canBePause,
		yes: function(e){
			layer.close(e);    //关闭提示框
			if(clickAction != null)
			{
				clickAction();
			}
		}
	});

}

function my_open_iframe_layer(url,style,title,layerFinish){
	//加载层
	var indexNow = layer.open({type: 2});
	var index = 0;
	if(title==null||title==''){
		$.get(url,function(data){
			layer.close(indexNow);
			index =layer.open({
				type: 1,
				shadeClose: true,
				content: data,
				style:style
			});
			layerFinish(index)
		});
	}else{
		$.get(url,function(data){
			layer.close(indexNow);
			index =layer.open({
				type: 1,
				title:[title],
				shadeClose: true,
				content: data,
				style:style
			});
			layerFinish(index);
		});
	}
	//获取页面
}

function showWait()
{
	var c = layer.open({
		type : 2,
		shadeClose: false,
	});
	return c;
}

function hideWait(){
	layer.closeAll();
}
//文本域随文字增加高度增加
function MaxMe(o) {     
    if (window.navigator.userAgent.indexOf("Firefox") > -1) {
      o.style.height = o.scrollTop + o.scrollHeight + "px";
    }else {
      if (o.scrollTop > 0) o.style.height = o.scrollTop + o.scrollHeight + "px";
    }
  }

//判断是否是数字
function IsNum(s)
{
    if(s!=null){
    	var reg = new RegExp("^[0-9]*$");
    	if(!reg.test(s)){
	        return false;
	    }else{
	    	return true;
	    }
    }
    return false;
}

/**
 * 异步加载依赖的javascript文件
 * src：script的路径
 * callback：当外部的javascript文件被load的时候，执行的回调
 */
function loadAsyncScript(src, callback) {
	var head = document.getElementsByTagName("head")[0];
	var script = document.createElement("script");
	script.setAttribute("type", "text/javascript");
	script.setAttribute("src", src);
	script.setAttribute("async", true);
	script.setAttribute("defer", true);
	head.appendChild(script);
	
	//fuck ie! duck type
	if (document.all) {
		script.onreadystatechange = function() {
			var state = this.readyState;
			if (state === 'loaded' || state === 'complete') {
				callback();
			}
		}
	} else {
		//firefox, chrome
		script.onload = function() {
			callback();
		}
	}
}