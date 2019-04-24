<?php
namespace Home\Controller;
use Think\Controller;
class MhPublicController extends MhHomeController {
	
	//登录
	public function binding(){
		if(IS_POST){
			$username = trim($_POST['mobile']);
			$password = trim($_POST['pass']);
			$fr = trim($_POST['fr']);
			$user = M('user')->where("username='{$username}'")->find();
			if(!$user){
				//$this->sendAjax(0,'不存在该用户');
				$uu = U('MhPublic/login');
				//echo "<script>alert('不存在该用户！');window.location.href='{$uu}'</script>";
				$arrret = array(
						'status' 	=> 0,
						'info'		=> '不存在该用户！',
						'url'		=> $fr,
				);
				echo json_encode($arrret);exit;
			}else{
				if($password != $user['userpwd']){
					//$this->sendAjax(0,'用户密码错误');
					$uu = U('MhPublic/login');
					//echo "<script>alert('密码错误！');window.location.href='{$uu}'</script>";
					$arrret = array(
							'status' 	=> 0,
							'info'		=> '密码错误！',
							'url'		=> $fr,
					);
					echo json_encode($arrret);exit;
				}else{
					session('vip_user',$user);
					session('user',$user);
					session('user_id',$user['id']);
					//session('login_time',time());
					//$this->sendAjax(1,'登录成功',U('Mh/index'));
					$uu = U('Mh/my');
					//echo "<script>window.location.href='{$uu}'</script>";
					$arrret = array(
							'status' => 1,
							'info'		=> '登陆成功！',
							'url'		=> $fr,
					);
					echo json_encode($arrret);exit;
				}
			}
			exit;
		}
		$this->display('MhPublic/login');
	}
	
	//注册
	public function register() {
		$fuid = I('fuid', 0, 'intval');
		
		if(IS_POST){
			$username = trim($_POST['username']);
			//$mobile = trim($_POST['mobile']);
			$userpwd = trim($_POST['userpwd']);
			$userpwdck = trim($_POST['userpwdck']);
			if($userpwd != $userpwdck){
				//$this->sendAjax(0,'两次输入的密码不一致！');exit;
				$uu = U('MhPublic/register');
				//echo "<script>alert('两次输入的密码不一致！');window.location.href='{$uu}'</script>";
				$arrret = array(
						'status' => 0,
						'info'		=> '两次输入的密码不一致！',
						'url'		=> $uu,
				);
				echo json_encode($arrret);exit;
			}
			$user = M('user')->where(array('username'=>"{$username}"))->find();
			if($user) {
				//$this->sendAjax(0,'用户名重复，请重新输入！');exit;
				$uu = U('MhPublic/register');
				//echo "<script>alert('用户名重复，请重新输入！');window.location.href='{$uu}'</script>";
				$arrret = array(
						'status' => 0,
						'info'		=> '用户名重复，请重新输入！',
						'url'		=> $uu,
				);
				echo json_encode($arrret);exit;
			}
			/* $user2 = M('user')->where(array('mobile'=>"%{$mobile}%"))->find();
			if($user2) {
				//$this->sendAjax(0,'手机号重复，请重新输入！');exit;
				$uu = U('MhPublic/register');
				echo "<script>alert('手机号重复，请重新输入！');window.location.href='{$uu}'</script>";
			} */
			$ins = array(
					'nickname'	=> $username,
					'headimg'	=> '/Public/home/mhimages/100.jpeg',
					//'mobile'	=> $mobile,
					'vip_level'	=> 0,
					'username'	=> $username,
					'userpwd'	=> $userpwd,
					'fid'		=> 0,
					'sub_time'	=> NOW_TIME,
					'create_time'=>NOW_TIME,
					'update_time'=>NOW_TIME,
			);
			if($fuid > 0) {
				$ins['parent1'] = $fuid;
				
				$p2 = M('user')->where("id={$fuid}")->find();
				if ($p2['parent1'] > 0) {
					$ins['parent2'] = $p2['parent1'];
				}
				if($p2['parent2'] > 0) {
					$ins['parent3'] = $p2['parent2'];
				}
			}
			$user_id = M("user")->add($ins);
			if($fuid>0 && $user_id>0) {
				$agent1 = M('user')->where("parent1={$fuid}")->count();
				M('user')->where("id={$fuid}")->setField('agent1', $agent1);
				
				$p2 = M('user')->where("id={$fuid}")->find();
				if ($p2['parent1'] > 0) {
					$agent2 = M('user')->where("parent2={$p2['parent1']}")->count();
					M('user')->where("id={$p2['parent1']}")->setField('agent2', $agent2);
				}
				if($p2['parent2'] > 0) {
					$agent3 = M('user')->where("parent3={$p2['parent2']}")->count();
					M('user')->where("id={$p2['parent2']}")->setField('agent3', $agent3);
				}
			}
			$userinfo = M('user')->where("id={$user_id}")->find();
			
			session('vip_user',$userinfo);
			session('user',$userinfo);
			session('user_id',$user_id);
			//session('login_time',time());
			//$this->sendAjax(1,'注册成功',U('Mh/index'));
			//$this->success('注册成功', U('Mh/index'));
			$uu = U('MhPublic/login');
			//echo "<script>alert('注册成功！');window.location.href='{$uu}'</script>";
			$arrret = array(
					'status' => 1,
					'info'		=> '注册成功！',
					'url'		=> $uu,
			);
			echo json_encode($arrret);exit;
			exit;
		}
		
		$this->assign('fuid', $fuid);
		$this->display();
	}
	
	//个人收益后台登陆
	public function loging(){
		if(IS_POST){
			$username = trim($_POST['username']);
			$password = xmd5(trim($_POST['password']));
			$user = M('user')->where(array('username'=>$username))->find();
			if(!$user){
				$this->sendAjax(0,'不存在该用户');
			}else{
				//$this->sendAjax(0,$username);
				if($password != $user['pass']){
					$this->sendAjax(0,'用户密码错误');
				}else{
					session('adminuser',$user);
					$this->sendAjax(1,'登录成功',U('Agent/index'));
				}
			}
			exit;
		}
		$this->display();
	}
	
	public function gproduct(){
		$this->display();
	}
	
	//检查条形码
	public function get_barcode(){
		$barcode = $_POST['barcode'];
		$normal = M('product_normal')->where(array('barcode'=>$barcode))->find();
		if(!$normal){
			$this->sendAjax(0,'输入的条形码编码不存在');
		}else{
			
			$this->sendAjax(1,$normal,U('Public/prcode',array('id'=>$normal['id'])));
		}
	}
	
	public function cultrue(){
		$this->display();
	}
	
	
	//获取员工之家的最新信息
	public function getCultrue(){
		$page = $_POST['p']?$_POST['p']:1;
		$pagesize = 15;
		$start = ($page-1)*$pagesize;
		$list = M('cultrue')->order('create_time desc')->limit($start,$pagesize)->select();
		$count =M('cultrue')->count();
		$page_list = ceil($count/$pagesize);
		$html='';
		if($list){
			foreach($list as $k=>$v){
				
				$html.='<a href="'.U('Public/cultrue_info',array('id'=>$v['id'])).'">';
				$html.='<li>'.$v['title'];
				$html.='</li>';
				$html.='</a>';

			}
			$this->ajaxReturn(array('status'=>1,'info'=>$html,'page_list'=>$page_list));			
		}else{
			if($page == 1){
				$html.='<li>无记录</li>';
				$this->ajaxReturn(array('status'=>1,'info'=>$html,'page_list'=>$page_list));	
			}else{
				$html.='<li>没有更多记录</li>';
				$this->ajaxReturn(array('status'=>0,'info'=>'已经没有更多数据了'));	
			}
			
		}
	}
	
	public function cultrue_info(){
		$id = $_GET['id'];
		$this->assign('cultrue',M('cultrue')->where(array('id'=>$id))->find());
		$this->display();
	}
	
	
	//合成图片
	public function prcode(){
		$id = $_GET['id'];
		if(!M('product_normal')->where(array('id'=>$id))->find()){
			$this->error('不存在该正品溯源');
			exit;
		}
		$this->assign('id',$id);
		$this->display();
	}
	
	// 显示/获取推广二维码图片
	public function get_prcode($id){
		header("Content-type: image/jpeg");
		// 忽略用户取消，限制执行时间为90s
		ignore_user_abort();
		set_time_limit(90);
		$path_info = get_prcode_path($id);
		
		// 已生成则直接返回
		if(is_file($path_info['new'])){
			echo file_get_contents($path_info['new']);
			exit;
		}
		
		// 目录不存在则创建
		if(!is_dir($path_info['path'])){
			mkdir($path_info['path'], 0777,1);
		}
		$normal = M('product_normal')->where(array('id'=>$id))->find();
		// 合成
		$im_dst = imagecreatefromjpeg("./Public/wine/images/sb.jpg");

		$color = ImageColorAllocate($im_dst, 0,0,0);
		// 合成品名
		$title = '品名：'.$normal['name'];
		$rs1 = imagettftext($im_dst, '25', 0, 100, 550, $color, './Public/font/simhei.ttf',  $title);
		//装柜价
		$price = '装柜价：'.$normal['price'].'元';
		$rs2 = imagettftext($im_dst, '25', 0, 100, 590, $color, './Public/font/simhei.ttf',  $price);
		//酒精度
		$alc = '酒精度：'.$normal['alc'];
		$rs3 = imagettftext($im_dst, '25', 0, 100, 630, $color, './Public/font/simhei.ttf',  $alc);
		//净含量
		$weight = '净含量：'.$normal['weight'];
		$rs4 = imagettftext($im_dst, '25', 0, 330, 630, $color, './Public/font/simhei.ttf',  $weight);
		//生产许可证号：
		$cardno = '生产许可证号：'.$normal['cardno'];
		$rs5 = imagettftext($im_dst, '25', 0, 100, 670, $color, './Public/font/simhei.ttf',  $cardno);
		//原产地：
		$address = '原产地：'.$normal['address'];
		$rs6 = imagettftext($im_dst, '25', 0, 100, 710, $color, './Public/font/simhei.ttf',  $address);
		//基酒储存日期：
		$ymonth = '基酒储存日期：'.date('Y-m-d',$normal['ymonth']);
		$rs7 = imagettftext($im_dst, '25', 0, 100, 750, $color, './Public/font/simhei.ttf',  $ymonth);
		//包装日期：
		$packdate = '包装日期：'.date('Y-m-d',$normal['packdate']);
		$rs8 = imagettftext($im_dst, '25', 0, 100, 790, $color, './Public/font/simhei.ttf',  $packdate);
		//出厂日期：
		$outdate = '出厂日期：'.date('Y-m-d',$normal['outdate']);
		$rs9 = imagettftext($im_dst, '25', 0, 100, 830, $color, './Public/font/simhei.ttf',  $outdate);
		
		//合成酒图片
		$im_src0 = imagecreatefromjpeg($normal['pic']);
		imagecopyresized ($im_dst, $im_src0,650, 560, 0, 0, 300, 300, 300, 300);
		//合成条形码1
		$im_src1 = imagecreatefromjpeg($normal['bar1']);
		imagecopyresized ($im_dst, $im_src1,45, 1150, 0, 0, 430, 300, 430, 300);
		//合成条形码2
		$im_src2 = imagecreatefromjpeg($normal['bar2']);
		imagecopyresized ($im_dst, $im_src2,522, 1150, 0, 0, 430, 300, 430, 300);
		
		//检酒员姓名
		$wine_name = '姓名：'.$normal['wine_name'];
		$rs10 = imagettftext($im_dst, '25', 0, 50,1950, $color, './Public/font/simhei.ttf',  $wine_name);
		//包装员姓名
		$pack_name = '姓名：'.$normal['pack_name'];
		$rs11 = imagettftext($im_dst, '25', 0, 530,1950, $color, './Public/font/simhei.ttf',  $pack_name);
		//检酒员身份证
		$wine_card = '身份证：'.$normal['wine_card'];
		$rs12 = imagettftext($im_dst, '25', 0, 50,1990, $color, './Public/font/simhei.ttf',  $wine_card);
		//包装员身份证
		$pack_card = '身份证：'.$normal['pack_card'];
		$rs13 = imagettftext($im_dst, '25', 0, 530,1990, $color, './Public/font/simhei.ttf',  $pack_card);
		//存储坛以及图片
		$arr1 = explode('</p>',$normal['storage']);
		$height = 2200;
		foreach($arr1 as $k=>$v){
			if($k==0){
				$width = 460;
			}else{
				$width = 400;
			}
			$v = str_replace('<br/>','',$v);
			$v = str_replace('<p>','',$v);
			$v = str_replace('&nbsp;','',$v);
			$kk = imagettftext($im_dst, '25', 0, $width,$height, $color, './Public/font/simhei.ttf',$v);
			$height = $height+40;
		}
		
		//合成酒坛
		$im_src3 = imagecreatefromjpeg($normal['storage_pic']);
		imagecopyresized ($im_dst, $im_src3,60, 2130, 0, 0, 300, 350, 300, 350);
		
		//简介
		$arr2 = explode('</p>',$normal['remark']);
		$height = 2680;
		foreach($arr2 as $k=>$v){
			if($k==0){
				$width = 160;
			}else{
				$width = 100;
			}
			$v = str_replace('<br/>','',$v);
			$v = str_replace('<p>','',$v);
			$v = str_replace('&nbsp;','',$v);
			$kk = imagettftext($im_dst, '25', 0, $width,$height, $color, './Public/font/simhei.ttf',  $v);
			$height = $height+40;
		}
		
		// 保存
		imagejpeg($im_dst, $path_info['new']);
		
		// 输出
		imagejpeg($im_dst);
		
		// 销毁
		imagedestroy($im_src0);
		imagedestroy($im_src1);
		imagedestroy($im_src2);
		imagedestroy($im_src3);
		imagedestroy($im_dst);
	}

	//绑定微信账户
    public function binding1(){
        if(IS_POST) {
            $wxtel = trim($_POST['wxtel']);
            $wxpassword= trim($_POST['wxpassword']);
            $fr = trim($_POST['fr']);
            $user = M('user')->where(array(['wxtel'=>$wxtel,'wxpassword'=>$wxpassword]))->find();

            if(!$user){
                $arrret = array(
                    'status' 	=> 0,
                    'info'		=> '不存在该用户！',
                    'url'		=> $fr,
                );
                echo json_encode($arrret);exit;
            }else{

                    session('vip_user',$user);
                    session('user',$user);
                    session('user_id',$user['id']);
                    //session('login_time',time());
                    //$this->sendAjax(1,'登录成功',U('Mh/index'));
                    $uu = U('Mh/my');
                    //echo "<script>window.location.href='{$uu}'</script>";
                    $arrret = array(
                        'status' => 1,
                        'info'		=> '登陆成功！',
                        'url'		=> $uu,
                    );
                    echo json_encode($arrret);exit;

            }
            exit;
        }
        $this->display();
    }
	
	public function logout(){
		$_SESSION['vip_user'] = null;
		$_SESSION['user'] = null;
		$_SESSION['user_id'] = null;
		redirect(U('Mh/index'));
	}
}