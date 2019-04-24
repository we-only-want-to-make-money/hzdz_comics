//公共ajax提交(func为js加载函数)
function Ajax(url,data,func){
	if(url){
		$.post(url,data,function(d){
			if(d){
				console.log(d);
				if(d.status){
					if(d.url){
						if(d.info!=''){
							msg(d.info);
						}
						window.location.href=d.url;
					}else if(isExitsFunction(func)){
						func();
						msg(d.info);
					}else{
						msg(d.info);
					}
				}else{
					msg(d.info);
				}
			}else{
				msg('网络异常');
			}
		});
	}
}

//公共ajax加载(func为js加载函数),
function AjaxLoad(url,data,model,html,func,load){
	layer.msg('加载中', {
	  icon: 16,
	  shade: 0.5
	});
	if(url){
		layer.closeAll();
		$.post(url,data,function(d){
			if(d){
				console.log(d);
				if(d.status){
					if(html){
						model.html('');
						model.append(d.info);
					}else{
						model.append(d.info);
						bindScroll(func);
					}
				}else{
					if(!load){
						if($('#common-load').html() == undefined){
								model.append(d.info);
							}
						}
					}
			}else{
				msg('网络异常');
			}
		});
	}
}

//下拉加载
function bindScroll(func){
	$(window).scroll(function(){
		// 当滚动到最底部以上50像素时， 加载新内容
		if ($(document).height() - $(this).scrollTop() - $(this).height() < 50){
			page++;
			$(window).unbind("scroll");
			$(".loading").show();
			func();
		}
	});
}


//判断是否存在指定函数   
function isExitsFunction(funcName) {  
    try {  
        if (typeof (eval(funcName)) == "function") {  
            return true;  
        }  
    } catch (e) {  
    }  
    return false;  
}  

//带按钮的系统提示
function maFade(id,func,btn1,btn2){
	layer.open({
		type: 1,
		title: false,
		closeBtn: false,
		area: '300px;',
		shade: 0.8,
		id: 'LAY_layuipro',
		resize: false,
		btn: [btn1, btn2],
		btnAlign: 'c',
		moveType: 1,
		content: id,
		success: func,
	});
}

//不带按钮的系统提示
function msg(msg){
	 layer.msg(msg, function(){
		setTimeout(function(){
			layer.closeAll();
		}, 2000);
	 });
}

//单条input修改或添加ajax提交
function prompt(title,url,func){
	layer.prompt({title: title, formType: 2}, function(text, index){
		Ajax(url,{content:text},func);
		layer.close(index);
	});
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
				msg("更新失败，请重试！");
			}
		},
		"json"
	)
}
   

