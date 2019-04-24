$(function(){
    //点击修改密码和退出按钮
    $('#quit').click(function(){
        $(".opereat_menu").toggle();
        return false;
    });

    //初始化顶部菜单高亮
    $('.header .menu ul li').each(function(i,v){
        var id = $(this).find('a').attr('_id');
        h_nav_id = $.cookie('h_nav_id');
        if(id==h_nav_id){
			if(h_nav_id==1 || !h_nav_id){
				$('.cotent_wrap .content .sidebar').css('border-right','none');	
			}else{
				$('.cotent_wrap .content .sidebar').css('border-right','1px solid #d4d4d4');	
			}
            $(this).find('a').addClass('now');
            $(this).siblings().find('a').removeClass('now');
        }
    });

    //默认radio选中
    $('input[type="radio"]').each(function(i,v){
        if($(this).val() == $(this).attr('default')){
            $(this).attr('checked',true);
        }
    })

    //默认select选中
    $("select").each(function(index, element) {
        $(element).find("option[value='"+$(this).attr('default')+"']").attr('selected','selected');
    });

    //登录菜单ajax提交
    $('.f_Ajax_btn').click(function(){
        var url = $('#f_Ajax_form').attr('action');
        data = $('#f_Ajax_form').serialize();
        $.post(url,data,function(d){
            //	console.log(d);
			//d = eval("("+d+")");
            if(d){
                if(d.status){
                    layer.alert(d.info,
                        function(){
                            if(d.url){
                                window.location.href=d.url;
                            }else{
                                window.location.reload();
                            }
                            layer.closeAll();
                        });

                }else{
                    layer.msg(d.info, {
                        time: 20000, //2s后自动关闭
                        btn: ['确定']
                    });
                }
            }else{
                layer.msg('网络错误', {
                    time: 20000, //2s后自动关闭
                    btn: ['确定']
                });
            }
        })
    });

    //公共ajax提交方法请定义class="b_Ajax_btn" form(id="b_Ajax_form")
    $('.b_Ajax_btn').click(function(){
		
		//测试阶段不能修改
		//layer.msg('演示程序不能修改', {icon : 2});
		//return false;
		
        var url = $('#b_Ajax_form').attr('action');
        data = $('#b_Ajax_form').serialize();
        $.post(url,data,function(d){
            console.log(d);
            if(d){
                if(d.status){
                    layer.msg(d.info, {icon : 1});
                    setTimeout(function(){
                        layer.closeAll();
                        if(d.url){
                            window.location.href=d.url;
                        }else{
							window.location.reload();
						}
                    }, 2000);
                }else{
                    layer.msg(d.info, {icon : 2});
                }
            }else{
                layer.msg('网络异常', {icon : 2});
            }
        })
    });
   
    // 表单回车事件
    $(document).keyup(function(e){
        var curKey = e.which;
        if(curKey==13){
            $('.b_Ajax_btn').click();
        }
    });

    //自定义中间内容高度
    var header_h = $('.header').height();
    footer_h = $('.footer').height();
    window_h = $(window).height();
    content_h = window_h - header_h - footer_h;
    $('.content').css('min-height',content_h);

    //更改角色的公共方法
    var changeRole = function(params){
        if(params.role_name == ''){
            layer.tips('角色名称不能为空！', '#role_name');
            return false;
        }
        if(params.role_name.length > 10){
            layer.tips('角色名称长度不能超过10个字符！', '#role_name');
            return false;
        }
        if(!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(params.role_name)){
            layer.tips('只能包含中文，英文，下划线以及数字', '#role_name');
            return false;
        }
        $.post('./admin.php?m=Admin&c=Setting&a=editGroup', params, function(resp){
            if(resp.status){
                layer.msg(resp.info, {icon : 1});
                setTimeout(function(){
                    layer.closeAll();
                    window.location.reload();
                }, 1000);
            }else{
                layer.msg(resp.info, {icon : 2});
            }
        }, 'json');
    }

    //角色界面添加角色按钮
    $("#add_role").click(function(){
        $("#role .title_text").find("span").html("添加角色");
        $("#role_name").val("");
        layer.open({
            type: 1,
            title: "添加角色",
            area: ['350px', '200px'],
            content: $("#role"),
            btn: ['确定', '取消'],
            yes:function(){
                var role_name = $.trim($("#role_name").val());
                var params = {};
                params.role_name = role_name;
                changeRole(params);
            },
            cancel:function(){
            }
        });
    });
    //角色界面修改角色按钮
    $(".editGroup").click(function(){
		
		//测试阶段不能修改
		layer.msg('演示程序不能修改', {icon : 2});
		return false;
		
        $("#role .title_text").find("span").html("修改角色");
        var ele = $("select[name='groupid'] option:selected");
        role_id = ele.attr('data-val');
        $("#role_name").val($.trim(ele.html()));
        layer.open({
            type: 1,
            title: "修改角色",
            area: ['350px', '200px'],
            content: $("#role"),
            btn: ['确定', '取消'],
            yes:function(){
                var role_name = $.trim($("#role_name").val());
                params = {};
                params.role_id = role_id;
                params.role_name = role_name;
                changeRole(params);
            },
            cancel:function(){
            }
        });
    });

    //删除角色
    $(".deleteGroup").click(function(){
		
		//测试阶段不能修改
		layer.msg('演示程序不能删除', {icon : 2});
		return false;
		
        var ele = $("select[name='groupid'] option:selected");
        role_id = ele.attr('data-val');
        layer.confirm('您确定要删除该角色吗？', {icon: 3, title:'提示'}, function(index){
            $.post('./admin.php?m=Admin&c=Setting&a=delGroup', {role_id: role_id}, function(resp){
                if(resp.status){
                    layer.msg(resp.info, {icon:1});
                    setTimeout(function(){
                        window.location.reload();
                    }, 800);
                }else{
                    layer.msg(resp.info, {icon:2});
                }
            },'json');
            layer.close(index);
        });
    });


    $(".saveGroup").click(function(){
        var groupid = $("select[name='groupid'] option:selected").attr('data-val');
        if(typeof(groupid) == "undefined"){
            layer.msg("请选择角色", {icon : 2});
            return false;
        }
        var rules = [];
        $("input[name='rule_ids[]']:checked").each(function(){
            rules.push($(this).val())
        });
        $("input[name='rule_ids[]']:indeterminate").each(function(){
            rules.push($(this).val())
        });
        var params = {};
        params.groupid = groupid;
        params.rules = rules.join(',');

        $.post('./admin.php?m=Admin&c=Setting&a=saveGroup', params, function(resp){
            if(resp.status){
                layer.msg(resp.info, {icon : 1});
                checkSelect();
            }else{
                layer.msg(resp.info, {icon : 2});
            }
        }, 'json');
    });


   


});

//公共删除
function del(id,table){
    layer.confirm('您确定要删除吗？', {icon: 3, title:'提示'}, function(index){
        $.post('./admin.php?m=Admin&c=Admin&a=del', {id: id,table:table}, function(resp){
            if(resp.status){
                layer.msg(resp.info, {icon:1});
                setTimeout(function(){
                    window.location.reload();
                }, 800);
            }else{
                layer.msg(resp.info, {icon:2});
            }
        },'json');
        layer.close(index);
    });
}

//group表单专用函数
function checkAll(obj){
    $(obj).parents('.b-group').eq(0).find("input[type='checkbox']").prop('checked', $(obj).prop('checked'));
}

function checkSelect() {
    var groupid = $("#groupid").find(" option:selected").attr("data-val");
    $.post("./admin.php?m=Admin&c=Setting&a=groupList", {groupid: groupid}, function (data) {
        if (data) {
            $('#cShape').html('');
            $('#cShape').append(data.info);
        } else {
            layer_error('網絡忙，請稍後再試');
        }
    });
}

//设置cookie
function setCookie(obj){
    var name= $(obj).attr('_name');
    url = $(obj).attr('_url');
    $.cookie(name,url,{path:'/'});
    return true;
}

//启用和禁用
function swtich(obj) {
    var $this = $(obj);
		$font = $this.find('span');
		$circle = $this.find('.circle');
    if ($this.hasClass('on')) {
        $font.html('');
        $circle.stop().animate({
            'right': '39px'
        }, 300, function () {
            $this.removeClass('on');
            $font.html('启用');
        });
        $this.attr('data-status', '0');
    }else {
        $font.html('');
        $circle.stop().animate({
            'right': '3px'
        }, 300, function () {
            $this.addClass('on');
            $font.html('禁用');
        });
        $this.attr('data-status', '1');
    }
}

//更改启用/禁用状态
var enable_lock = 1;
function do_swtich(obj){
	var status = $(obj).attr('data-status');
		id = $(obj).attr('data-id');
		switch_obj = $(obj);
		url = $(obj).attr('_url');
	if (status == 1) {
		var warning = layer.confirm(
			"是否禁用？",
			{ btn : ["确认", "取消"]},
			function() {
				if (enable_lock) {
					enable_lock = 0;
					$.post(url, {id: id}, function(res) {
						console.log(res);
						layer.close(warning);
						if (res.status) {
							swtich(switch_obj);
							layer.msg(res.info, {icon:1, time:1000});
						}else{
							layer.msg(res.info, {icon:2});
						}
						enable_lock = 1;
					});
				}
			},
			function() {}
		)
	}else{
		if (enable_lock) {
			enable_lock = 0;
			$.post(url, {id: id}, function(res) {
				if (res.status) {
					swtich(switch_obj);
					layer.msg(res.info, {icon:1, time:1000});
				}else{
					layer.msg(res.info, {icon:2});
				}
				enable_lock = 1;
			});
		}
	}
}

//layer显示iframe
function showModel(url,title){
	layer.open({
		 type: 2,
		 title: title,
		 shadeClose: true,
		 shade: false,
		 maxmin: true, //开启最大化最小化按钮
		 area: ['893px', '500px'],
		 content: url
	});
	$('.layui-layer-content').css('margin','0px');
}


//窗体重置基本方法
function resetFrom(){
	$("input").val(""); 
}

//判断数值是否在一维数组中
function contains(arr, obj) {
    var i = arr.length;
    while (i--) {
        if (arr[i] === obj) {
            return true;
        }
    }
    return false;
}  


//公共ajax加载(func为js加载函数),
function AjaxLoad(url,data,model){
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
					model.html('');
					model.append(d.info);
				}else{
					model.msg(d.info);
				}
			}else{
				msg('网络异常');
			}
		});
	}
}

