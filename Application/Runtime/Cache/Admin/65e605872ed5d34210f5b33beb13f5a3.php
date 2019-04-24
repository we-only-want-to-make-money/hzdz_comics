<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>管理后台</title>
<link rel="stylesheet" href="/Public/admin/css/style.default.css" type="text/css" />
<link rel="stylesheet" href="/Public/plugins/bootstrap/css/bootstrap.font.css" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
<script type="text/javascript" src="/Public/admin/js/plugins/jquery-1.7.min.js"></script>
<script type="text/javascript" src="/Public/admin/js/plugins/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/Public/admin/js/plugins/jquery.cookie.js"></script>
<script type="text/javascript" src="/Public/admin/js/custom/general.js"></script>

<!--[if IE 9]>
    <link rel="stylesheet" media="screen" href="css/style.ie9.css"/>
<![endif]-->
<!--[if IE 8]>
    <link rel="stylesheet" media="screen" href="css/style.ie8.css"/>
<![endif]-->
<!--[if lt IE 9]>
	<script src="js/plugins/css3-mediaqueries.js"></script>
<![endif]-->
</head>

<body class="withvernav">
<div class="bodywrapper">
    <div class="topheader" style="border-bottom: #009688 solid 2px;">
        <div class="left">
            <h1 class="logo"><?php echo ($_site['name']); ?><span></span></h1>
            <span class="slogan" style=" border-left-color:#396F08; color:#fff">后台管理系统</span>
                 
            <br clear="all" />
            
        </div><!--left-->
		<div class="right">
        	 <span style=" color:#fff;"><?php echo session('admin.nickname');?> <a href="<?php echo U('Index/logout');?>" style=" color:#ccc;">[退出]</a></span>
        </div><!--right-->

    </div><!--topheader-->
    
    <style>
	.vernav2 span.text{ padding-left:10px;}
	.menucoll2 span.text{ display:none;}
	.menucoll2>ul>li>a{ width:12px; padding:9px 10px; !important;}
	.dataTables_paginate a{ padding:0 10px;}
	</style>
    <div class="vernav2 iconmenu">
    	<ul>
		
        	<li>
				<a href="#formsub">
					<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
					<span class="text">系统设置</span>
				</a>
            	<span class="arrow"></span>
            	<ul id="formsub">
               		<li><a href="<?php echo U('Config/site');?>">站点设置</a></li>
                    <li><a href="<?php echo U('Config/dist');?>">分销设置</a></li>
					<li><a href="<?php echo U('Config/yyb');?>">优云宝设置</a></li>
                    <li><a href="<?php echo U('Config/charge');?>">充值赠送设置</a></li>
					<li><a href="<?php echo U('Config/send');?>">打赏赠送设置</a></li>
					<li><a href="<?php echo U('Config/ads');?>">广告设置</a></li>					
					<li><a href="<?php echo U('Config/user');?>">修改密码</a></li>
                </ul>
            </li>

			<li>
				<a href="#gzh">
					<span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
					<span class="text">公众号设置</span>
				</a>
            	<span class="arrow"></span>
            	<ul id="gzh">
					<li><a href="<?php echo U('Config/mp');?>">公众号配置</a></li>
					<li><a href="<?php echo U('Autoreply/index');?>">自动回复管理</a></li>
                    <li><a href="<?php echo U('Selfmenu/index');?>">公众号菜单管理</a></li>
					<li><a href="<?php echo U('Config/share');?>">微信分享设置</a></li>
                </ul>
            </li>
		
			
			<li>
				<a href="#finance" class="elements">
					<span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
					<span class="text">系统财务</span>
				</a>
            	<span class="arrow"></span>
            	<ul id="finance">
				    <li><a href="<?php echo U('Withdraw/index');?>">用户提现管理</a></li>
				    <li><a href="<?php echo U('Charge/index');?>">用户充值记录</a></li>
					<li><a href="<?php echo U('Finance/share');?>">用户分享获币记录</a></li>
					<!--li><a href="<?php echo U('Finance/pay');?>">一键转账</a></li-->
					<!--li><a href="<?php echo U('Finance/deposit_log');?>">转账记录</a></li-->
					<li><a href="<?php echo U('Finance/finance_log');?>">用户账户变动记录</a></li>
					<li><a href="<?php echo U('Finance/separate_log');?>">代理佣金分成记录</a></li>					
					<!--li><a href="<?php echo U('Finance/mch_pay_log');?>">转账记录</a></li--->
                </ul>
            </li>
			
					
			
			<li>
				<a href="#user" class="typo">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
					<span class="text">用户管理</span>
				</a>
				
				<span class="arrow"></span>
				<ul id="user">
					<li><a href="<?php echo U('User/index');?>">用户信息管理</a></li>
					<li><a href="<?php echo U('Report/index');?>">用户新增报表</a></li>
					<li><a href="<?php echo U('Tree/index');?>">用户树形关系</a></li>
                </ul>
				
            </li>
           
			<!-- <li>
				<a href="#gbal" class="support">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
					<span class="text">股东分红</span>
				</a>
            	<span class="arrow"></span>
            	<ul id="gbal">
					<li><a href="<?php echo U('Reward/index');?>">分红记录</a></li>
					<li><a href="<?php echo U('Reward/edit');?>">发放分红</a></li>
                </ul>
            </li> -->
			
			
			
			<li>
				<a href="<?php echo U('Member/index');?>">
					<span class="glyphicon glyphicon-eur" aria-hidden="true"></span>
					<span class="text">代理管理</span>
				</a>
            </li>
			
			
			<li>
				<a href="<?php echo U('Notice/index');?>" class="editor">
					<span class="glyphicon glyphicon-volume-down" aria-hidden="true"></span>
					<span class="text">公告管理</span>
				</a>
            </li>
			
			<li>
				<a href="<?php echo U('Custom/index');?>" class="typo">
					<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
					<span class="text">群发消息</span>
				</a>
            </li>
			
			<li>
				<a href="<?php echo U('Jub/index');?>" class="typo">
					<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
					<span class="text">举报管理</span>
				</a>
            </li>
			
			
			<li>
				<a href="#book" class="elements">
					<span class="glyphicon glyphicon-th" aria-hidden="true"></span>
					<span class="text">小说管理</span>
				</a>
				<span class="arrow"></span>
            	<ul id="book">
					<li><a href="<?php echo U('Config/xbanner');?>">轮播图设置</a></li>
					<li><a href="<?php echo U('Config/bookcate');?>">分类设置</a></li>
               		<li><a href="<?php echo U('Book/index');?>">小说管理</a></li>
                </ul>
            </li>
		
			<li>
				<a href="#mh" class="elements">
					<span class="glyphicon glyphicon-list" aria-hidden="true"></span>
					<span class="text">漫画管理</span>
				</a>
				<span class="arrow"></span>
            	<ul id="mh">
					<li><a href="<?php echo U('Config/banner');?>">轮播图设置</a></li>
					<li><a href="<?php echo U('Config/mhcate');?>">分类设置</a></li>
               		<li><a href="<?php echo U('Product/index');?>">漫画管理</a></li>
                </ul>
            </li>
			<li>
				<a href="#ysbook" class="elements">
					<span class="glyphicon glyphicon-equalizer" aria-hidden="true"></span>
					<span class="text">听书管理</span>
				</a>
				<span class="arrow"></span>
            	<ul id="ysbook">
					<li><a href="<?php echo U('Config/ybanner');?>">轮播图设置</a></li>
					<li><a href="<?php echo U('Config/yook');?>">分类设置</a></li>
               		<li><a href="<?php echo U('Yook/index');?>">听书管理</a></li>
                </ul>
            </li>
            <!--li>
				<a href="<?php echo U('Video/index');?>" class="typo">
					<span class="glyphicon glyphicon-list" aria-hidden="true"></span>
					<span class="text">动漫管理</span>
				</a>
            </li-->
			
			
			
			
			<li>
				<a href="#Chapter" class="elements">
					<span class="glyphicon glyphicon-import" aria-hidden="true"></span>
					<span class="text">文案制作</span>
				</a>
            	<span class="arrow"></span>
            	<ul id="Chapter">
					<li><a href="<?php echo U('Chapter/index');?>">漫画文案</a></li>
               		<li><a href="<?php echo U('Bhapter/index');?>">小说文案</a></li>
                </ul>
            </li> 
			<li>
				<a href="<?php echo U('Chapurl/index');?>" class="addons">
					<span class="glyphicon glyphicon-share" aria-hidden="true"></span>
					<span class="text">文案链接</span>
				</a>
            </li>
			
			
        </ul>
        <a class="togglemenu"></a>
        <br /><br />
    </div><!--leftmenu-->
        
    <div class="centercontent">
				<script src="/Public/plugins/My97DatePicker/WdatePicker.js"></script>
        <div class="pageheader notab">
            <h1 class="pagetitle">用户管理</h1>
            <span class="pagedesc">管理平台中的用户信息</span>
            
        </div><!--pageheader-->
        <style>
					.r4{
						display: block;
						width: 70px;
						line-height: 25px;
						background: #FF9800;
						float: right;
						text-align: center;
						font-size: 15px;
						color: #fff;
						border-radius: 5px;
						margin-right:5%;
					}
				</style>
        <div id="contentwrapper" class="contentwrapper lineheight21">
			<div class="tableoptions">        
				<form method="post" id="form">
					VIP:
					<select name="vip" default="<?php echo ($_GET['vip']); ?>" style="width:90px; min-width:0;">
						<option value="">全部</option>
						<option value="1">是</option>
						<option value="-1">否</option>
					</select>
					编号:
					<input type="text" name="id" value="<?php echo ($_GET['id']); ?>" class="smallinput" style="width:100px;" />
					昵称/姓名:
					<input type="text" name="name" value="<?php echo ($_GET['name']); ?>" class="smallinput" style="width:100px;" />
					<input type="submit" value="查找" />
					<input type="button" value="导出" onclick="exportExcel()" />
					<script>
						function exportExcel(){
							jQuery("#form").attr('action', "<?php echo U('export');?>");
							jQuery("#form").submit();
						}					
					</script>
				</form>
			</div><!--tableoptions-->
			<table cellpadding="0" cellspacing="0" border="0" id="table2" class="stdtable stdtablecb" style="background:#fff">
				<thead>
					<tr>
						<th class="head1">
							<a href="<?php if($_GET['order'] == 'id' && $_GET['type'] == 'desc'){ echo U('index?order=id&type=asc'); }else{ echo U('index?order=id&type=desc'); }?>">
							编号
							<?php if($_GET['order'] == 'id' && $_GET['type'] == 'desc')echo '↑';else echo '↓';?>
							</a>
						</th>
						<th class="head1">头像</th>
						<th class="head0">昵称</th>
						<th class="head1">VIP</th>
						<th class="head1">VIP到期时间</th>
						<th class="head1">
							<a href="<?php if($_GET['order'] == 'money' && $_GET['type'] == 'desc'){ echo U('index?order=money&type=asc'); }else{ echo U('index?order=money&type=desc'); }?>" style="color:red">
							账户书币
							<?php if($_GET['order'] == 'money' && $_GET['type'] == 'desc')echo '↑';else echo '↓';?>
							</a>
						</th>
						<th class="head1">账户余额</th>
						<th class="head0">
							<a href="<?php if($_GET['order'] == 'sub_time' && $_GET['type'] == 'desc'){ echo U('index?order=sub_time&type=asc'); }else{ echo U('index?order=sub_time&type=desc'); }?>" style="color:red">
							注册时间
							<?php if($_GET['order'] == 'sub_time' && $_GET['type'] == 'desc')echo '↑';else echo '↓';?>
							</a>
						</th>
						<th class="head0">
							<a href="<?php if($_GET['order'] == 'agent1' && $_GET['type'] == 'desc'){ echo U('index?order=agent1&type=asc'); }else{ echo U('index?order=agent1&type=desc'); }?>" style="color:red">
							一级下线数
							<?php if($_GET['order'] == 'agent1' && $_GET['type'] == 'desc')echo '↑';else echo '↓';?>
							</a>
						</th>
						<th class="head0">
							<a href="<?php if($_GET['order'] == 'agent2' && $_GET['type'] == 'desc'){ echo U('index?order=agent2&type=asc'); }else{ echo U('index?order=agent2&type=desc'); }?>" style="color:red">
							二级下线数
							<?php if($_GET['order'] == 'agent2' && $_GET['type'] == 'desc')echo '↑';else echo '↓';?>
							</a>
						</th>
						<th class="head0">
							<a href="<?php if($_GET['order'] == 'agent3' && $_GET['type'] == 'desc'){ echo U('index?order=agent3&type=asc'); }else{ echo U('index?order=agent3&type=desc'); }?>" style="color:red">
							三级下线数
							<?php if($_GET['order'] == 'agent3' && $_GET['type'] == 'desc')echo '↑';else echo '↓';?>
							</a>
						</th>
						<th class="head1">是否客服消息黑名单</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						
						<td><?php echo ($vo["id"]); ?></td>
						<td><img src="<?php echo ($vo["headimg"]); ?>" style="width:50px; height:50px; border-radius:25px;" /></td>
						<td><?php echo ($vo["nickname"]); ?></td>
						<td><?php if($vo['vip']): ?>VIP<?php else: ?>-<?php endif; ?></td>
						<td><?php if($vo['vip_e_time'] > 0): echo (date("Y-m-d H:i:s",$vo["vip_e_time"])); else: ?>-<?php endif; ?></td>
						<td><?php echo ($vo["money"]); ?><a href="javascript:;" class="r4" onclick="showP(<?php echo ($vo['id']); ?>);">充值书币</a></td></td>
						<td><?php echo ($vo["rmb"]); ?></td>
						<td><?php if($vo['sub_time'] > 0): echo (date("Y-m-d H:i:s",$vo["sub_time"])); else: ?>-<?php endif; ?></td>
						<td><?php echo ($vo["agent1"]); ?></td>
						<td><?php echo ($vo["agent2"]); ?></td>
						<td><?php echo ($vo["agent3"]); ?></td>
						<td>
							<?php if($vo['isrec'] == 1): ?><a style="color:blue" href="<?php echo U('Admin/setField',array('id'=>$vo['id'],'table'=>'user','field'=>'isrec'));?>">否</a>
							<?php else: ?>
								<a style="color:red" href="<?php echo U('Admin/setField',array('id'=>$vo['id'],'table'=>'user','field'=>'isrec'));?>">是</a><?php endif; ?>
						</td>
						<td>
							<a href="<?php echo U('detail?id='.$vo['id']);?>">详细信息</a> |
							<?php if($vo['vip'] == 1): ?><a href="<?php echo U('setVip?id='.$vo['id']);?>">取消VIP</a>
							<?php else: ?>
								<a href="<?php echo U('setVip?id='.$vo['id']);?>">设置VIP</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<style>
				.r3{
					float: left;
					border: none;
					color: #fff;
					background: #FF5722;
					margin-top: -2.5px;
					margin-left: 10px;
					border-radius: 4px;
					text-align: center;
				}
			</style>
			<div class="dataTables_paginate paging_full_numbers" id="dyntable2_paginate">
			<?php echo ((isset($page) && ($page !== ""))?($page):"<p style='text-align:center'>暂时没有数据</p>"); ?>
			</div>
        
        </div><!--contentwrapper-->
		<link href="/Public/layer_mobile/skin/layer.css" rel="stylesheet" type="text/css" />
		<script src="/Public/layer_mobile/layer.js" type="text/javascript"></script>
		<script>
				function showP(userid){
					layer.prompt({title:'输入充值金额，减少金额充值负数'},function(val, index){
						if(val == ''){
							layer.msg('请输入您要充值的金额');
							return;
						}

						$.post("<?php echo U('setMoney');?>",{money:val,user_id:userid},function(d){
							if(d){
								layer.msg(d.info, {icon: 6});
								setTimeout(function(){
									layer.close(index);
									location.reload();
								}, 2000);
							}else{
								layer.msg('网络异常');
							}
						});
						layer.close(index);
					});
				}
		</script>
	</div><!-- centercontent -->
    
    
</div><!--bodywrapper-->
<script>
	jQuery(document).ready(function(e){
		
		
		// 菜单添加提示 
		$ = jQuery;
		
		// 根据cookie打开对应的菜单
		if($.cookie('curIndex')){
			console.log($.cookie('curIndex'));
			$(".vernav2>ul>li").eq($.cookie('curIndex')).find('ul').show();
		}
		
		$(".vernav2 ul li").each(function(index, el){
			$(this).attr('title', $(this).find("a").find('span.text').text());
			
		});
		
		
		$(".vernav2>ul>li>a").each(function(index,el){
			$(el).on('click',function(e){
				$.cookie('curIndex',$(this).parent('li').index());
			});
		});
		
		
		// 调整默认选择内容
		$("select").each(function(index, element) {
			$(element).find("option[value='"+$(this).attr('default')+"']").attr('selected','selected');
		});
		// 调整提示内容
	});
</script>
</body>
</html>