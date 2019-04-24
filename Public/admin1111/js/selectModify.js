(function($){
	var defaults = {
					isSearch:false,  //是否加入search框
					maxLength:5,  //最大显示高度
					onSelectChange:function(){},  //下拉框change事件
					onSearchpropertychange:function(){}  //搜索框内容改变时触发
				};
	
	$.fn.selectModify = function(method){
		
		if (methods[method]) {
            // 如果存在该方法就调用该方法
            // apply 是吧 obj.method(arg1, arg2, arg3) 转换成 method(obj, [arg1, arg2, arg3]) 的过程.
            // Array.prototype.slice.call(arguments, 1) 是把方法的参数转换成数组.
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            // 如果传进来的参数是"{...}", 就认为是初始化操作.
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.pluginName');
        }
        
	};
	
	var methods = {
        /**
         * 初始化方法
         * @param _options
         * @return {*}
         */
        init : function (_options) {
        	var settings = $.extend({},defaults,_options);
            return this.each(function () {
                var divstr_b='<div class="selectbox selectJs"><i></i><span class="arrowbt"></span><div class="selbox">',
					divstr_s='<ul class="selul">',
					divstr_sh='<label class="setsearch"><em class="fa fa-search"></em></label><input name="setsearch" type="text" /><ul class="selul">',
				    divstr_c='',
				    divstr_e='</ul></div></div>',
				    _this=$(this),
				    initIndex,
				    selected,
				    mLength = settings.maxLength,
				    listHeight,
				    dwidth;
				_this.hide();
				dwidth = _this.attr('data-w');
				
				if(_this.prev('.selectJs').length>0)_this.prev('.selectJs').remove();
				
				
					if(divstr_c == '' || divstr_c == null){
					selected = _this.find("option:selected").text();
					initIndex = _this.find("option:selected").index();
					_this.find('option').each(function(){
						if($(this).attr("value") == undefined){
							divstr_c+="<li lival=''>"+$(this).html()+"</li>";
						}else{
							divstr_c+="<li lival='"+$(this).attr("value")+"'>"+$(this).html()+"</li>";
						}
						
					});
				}
					
					if(!isNaN(mLength)){
					   mLength = parseInt(mLength);
					}else{
						mLength = 5;
					}
				if(settings.isSearch){
					_this.before(divstr_b+divstr_sh+divstr_c+divstr_e);  //若settings.isSearch为true则添加input搜索框
					
					if(_this.find("option").length <= 0){
							_this.prev('.selectJs').find('.selbox').css("min-height",30);
						}else{
							_this.prev('.selectJs').find('.selbox').css("max-height",_this.prev('.selectJs').find('input[type="text"]').height() + (_this.prev('.selectJs').find('li:eq(0)').height() * mLength) + 10);
						}
					
					_this.prev('.selectJs').find('input[type="text"]').keyup(function(){  //keyup事件每次按键完成进行搜索
						
						var TempArr = [],// 将select中的值存入数组
						    SelectObj = _this.find("option"),
						    inpTxt = $(this).val(),
						    SelectUl = _this.prev('.selectJs').find('ul'),
						    Arr = [];
						
						if($.trim(inpTxt) != '' || $.trim(inpTxt) != null){  //若input值不为空，则进行搜索操作
						
						    for (i = 0; i < SelectObj.length; i++){
						        TempArr[i] = [ SelectObj.eq(i).text(), SelectObj.eq(i).val() ];  //将当前select中的val和text放入数组
						    }
						
						for (i = 0; i < TempArr.length; i++){  //对数组中的所有值进行匹配，如果查询到匹配值，则进行拼接，并存储到新的数组中
							if (TempArr[i][0].indexOf(inpTxt) != -1){
								if(TempArr[i][1] == "" || TempArr[i][1] == null){
									continue;  //如果value为空，跳出当次循环(筛选提示)
								}else{
									Arr[Arr.length] = '<li lival="'+TempArr[i][1]+'">'+TempArr[i][0]+'</li>';
								}
							}
						}
						
						SelectUl.html(Arr.join().replace(/,/g, ""));  //将内容覆盖到ul中，并去掉逗号
						
						}
						
						if($.trim(inpTxt) == '' || $.trim(inpTxt) == null){  //若input的值为空，则将ul中的内容还原
							SelectUl.html(divstr_c);
						}
					
						_this.prev('.selectJs').find('ul li').unbind("click").click(function(e){  //为新添加的li绑定click事件
							selClick($(this),e);
						});
						
						_this.prev('.selectJs').find('li').removeClass('selcheck').eq(initIndex).addClass('selcheck');
						
						if($.isFunction(settings.onSearchpropertychange)){  //回调(搜索框内容改变时触发)
							settings.onSearchpropertychange.call(_this);
						}
						
					});
					
				}else{  //若settings.isSearch为false则只添加ul
					_this.before(divstr_b+divstr_s+divstr_c+divstr_e);
					if(_this.find("option").length <= 0){
							_this.prev('.selectJs').find('.selbox').css("min-height",30);
						}else{
							_this.prev('.selectJs').find('.selbox').css("max-height",(_this.prev('.selectJs').find('li:eq(0)').height() * mLength) + 10);
						}
				}
				
				_this.prev('.selectJs').css('width',dwidth);
				
				_this.prev('.selectJs').children('i').html(selected);
				_this.prev('.selectJs').find('li').eq(initIndex).addClass('selcheck');
				
				_this.prev('.selectJs').click(function(){
					$(this).find(".selbox").show();
					_this.prev('.selectJs').find('ul li').unbind("click").click(function(e){  //为新添加的li绑定click事件
							selClick($(this),e);
						});
						
						_this.prev('.selectJs').find('li').removeClass('selcheck').eq(initIndex).addClass('selcheck');
						
					if($.isFunction($().mCustomScrollbar)){
						_this.prev('.selectJs').find(".selbox").mCustomScrollbar("update");
					}
				});
				_this.prev('.selectJs').mouseleave(function(){
					
					$(this).find(".selbox").hide();
					_this.prev('.selectJs').find('ul').html(divstr_c);
					_this.prev('.selectJs').find('input[type="text"]').val("");
					_this.prev('.selectJs').find('li').removeClass('selcheck').eq(initIndex).addClass('selcheck');
				});
				
				function selClick($this,e){  //li的click事件
					e.stopPropagation();
					var p = $this.html(),value=$this.attr("lival");
					
					$this.parents('.selectJs').find('i').html(p);				
					_this.val(value);
					_this.click();
					var attrs={},
					    thisSel =_this.find("option[value='"+value+"']");

					if(thisSel.length != 0){

						for(i=0;i<thisSel.prop("attributes").length;i++){
						   	attrs[thisSel.prop("attributes")[i].name]=thisSel.prop("attributes")[i].value;
						}
					}
					
					$this.parents('.selbox').hide();
					
					initIndex = _this.find("option:selected").index();
					_this.prev('.selectJs').find('li').removeClass('selcheck').eq(initIndex).addClass('selcheck');
					
					if($.isFunction(settings.onSelectChange)){  //下拉框change回调
						settings.onSelectChange(_this,attrs);
					}
					
					_this.prev('.selectJs').find('input[type="text"]').val("");
				}
				
				if($.isFunction($().mCustomScrollbar)){
					_this.prev('.selectJs').find(".selbox").mCustomScrollbar({
								mouseWheel:"auto",
								autoDraggerLength:true,
						    scrollButtons:{
						        enable:true,
						    },
						    advanced:{
						    	updateOnBrowserResize:true,
								updateOnContentResize:true
						    },
						    theme:"dark"
						 });
				}
           });
        },
        Refresh : function(){
        	/*
        	return this.each(function () {
			var _this = $(this),
				selected = '',
				initIndex;
			initIndex = _this.find("option:selected").index();
			selected = _this.find("option:selected").text();
			
        	_this.prev('.selectJs').find(".selul").empty();
	        	_this.find('option').each(function(){
	        		if($(this).attr("value") == undefined){
							_this.prev('.selectJs').find(".selul").append("<li lival=''>"+$(this).html()+"</li>");
						}else{
							_this.prev('.selectJs').find(".selul").append("<li lival='"+$(this).attr("value")+"'>"+$(this).html()+"</li>");
						}
	        		
	        	});
	        	_this.prev('.selectJs').find('li').removeClass('selcheck').eq(initIndex).addClass('selcheck');
	        	_this.prev('.selectJs').children('i').html(selected);
        	});
        	*/
        }
    };
	
	
})(jQuery);
