<?php
namespace Home\Controller;
use Think\Controller;
class UcenterController extends HomeController {
	public function _initialize(){
		parent::_initialize();
	}
	
    public function index(){
		//我的下级米粒总额
		$this->assign('myson_money',M('user')->where(array('parent1|parent2|parent3'=>$this->user['id']))->sum('money'));
    	$this -> display();
    }
	
	//个人中心
	public function profile(){
		if(IS_POST){
			$_POST['birth'] = $_POST['date'];
			if(M('user')->where(array('id'=>$this->user['id']))->save($_POST)){
				$this->success('保存成功!',U('index'));
			}else{
				$this->success('保存失败!');
			}		
			exit;
		}
		$this->display();
	}
	
	//我的已买课程
	public function myGoods(){
		$this->display();
	}
	
	//我的已买课程
	public function getOrder(){
		$page = I('post.page');
		$pagesize = 5 ;
		$start = ($page-1)*$pagesize;
		$list = M('order')->where(array('user_id'=>$this->user['id'],'status'=>2))->order('create_time desc')->limit($start,$pagesize)->select();
		foreach($list as $k=>$v){
			$list[$k]['pic'] = M('goods')->where(array('id'=>$v['goods_id']))->getField('pic');
			$list[$k]['title'] = M('goods')->where(array('id'=>$v['goods_id']))->getField('name');
			$list[$k]['gid'] = M('goods')->where(array('id'=>$v['goods_id']))->getField('id');
			$list[$k]['cart'] = M('cart')->where(array('order_id'=>$v['id']))->select();
			$list[$k]['count'] = count($list[$k]['cart']);
		}
		$this->assign('list',$list);
		$this->assign('page',$page);
		$html = $this->fetch();
		if(!$list){
			$this->error($html);
		}else{
			$this->success($html);
		}
	}
	
	//播放已买课程
	public function play(){
		$gid = $_GET['gid'];
		$order = M('order')->where(array('user_id'=>$this->user['id'],'status'=>2))->select();
		if($order){
			foreach($order as $k=>$v){
				$gids = $v['goods_id'].','; 
			}
			$gids = substr($gids,0,-1);
			$gidsArr = explode(',',$gids);
			if(!in_array($gid,$gidsArr)){
				$this->error('您还未购买该课程');
				exit;
			}
		}else{
			$this->error('您还未购买课程');
			exit;
		}
		$info = M('goods')->find(intval($gid));
		$info['teacher'] = M('teacher')->find(intval($info['teacher']));
		$this->assign('info',$info);
		$this->assign('moneys',explode('|',$this->_site['grow']));
		$this->display();
	}
	
	// 我的推广二维码
	public function qrcode(){
		$this -> display();
	}
	
	// 显示/获取推广二维码图片
	public function get_qrcode(){
		header("Content-type: image/jpeg");		
		// 忽略用户取消，限制执行时间为90s
		ignore_user_abort();
		set_time_limit(90);		
		$path_info = get_qrcode_path($this -> user);
		// 目录不存在则创建
		if(!is_dir($path_info['path'])){
			mkdir($path_info['path'], 0777,1);
		}
		
		$dd = new \Common\Util\ddwechat($this -> _mp);
		
		if(!is_file($path_info['qrcode'])){
			$accesstoken = $dd -> getaccesstoken();
			$rs = $dd -> createqrcode('user_'.$this -> user['id'],null,$accesstoken);
			if(!$rs){
				if(APP_DEBUG){
					$this -> error($dd -> errmsg);
				}else{
					$this -> error('推广二维码生成失败，请稍后重试！');
				}
			}
			
			$qrcode_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$rs['ticket'];
			$qrcode_img = $dd -> exechttp($qrcode_url, 'get', null , true); //file_get_contents($qrcode_url);
			if(!$qrcode_img){
				$this -> error('获取二维码失败');
			}
			
			// 保存图片	
			$save = file_put_contents($path_info['qrcode'],$qrcode_img);

			if(!$save){
				$this -> error('二维码保存失败！');
			}
		}
		if($this->_qrcode['pic']){
			// 合成
			$path = trim($this->_qrcode['pic']);
			$im_dst = imagecreatefromjpeg($this->_qrcode['pic']);
		}else{
			$im_dst = imagecreatefromjpeg("./Public/images/qrcode.jpg");
		}
		
		$im_src = imagecreatefromjpeg($path_info['qrcode']);
		
		// 合成二维码（二维码大小282*282)
		imagecopyresized ( $im_dst, $im_src,365, 560, 0, 0, 160, 160, 430, 430);
		
		// 保存
		imagejpeg($im_dst, $path_info['new']);
		
		// 输出
		imagejpeg($im_dst);
		
		// 销毁
		imagedestroy($im_src);
		imagedestroy($im_dst);
	}
	
	//我的班级米粒
	public function myTeam(){
		$this->display();
	}
		
	//获取我的班级米粒信息
	public function getTeam(){
		$status = I('post.status');
		$page = I('post.page')?I('post.page'):1;
		$pagesize = 5 ;
		$start = ($page-1)*$pagesize;
		$where[$status] = $this->user['id'];
 		$list = M('user')->where($where)->order('id desc')->limit($start,$pagesize)->select();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->assign('type',I('post.type'));
		$html = $this->fetch();
		if(!$list){
			$this->error($html);
		}else{
			$this->success($html);
		}
	}
	
	//下级学员查看
	public function TeamL(){
		$this->display();
	}
	
	//在线留言
	public function message(){
		$touser_id = $_GET['touser']? $_GET['touser']:0;
		$touser = M('user')->where(array('id'=>$touser_id))->find();
		session('message_time','1');
		$this->assign('touser',$touser);
		$this->display();
	}
	
	//在线留言
	public function doMsg(){
		if(IS_POST){
			if($_POST['msg']){
				$data = array(
					'fromuser' => $_POST['fromuser'],
					'fromuser_pic' => $_POST['fromuser_pic'],
					'touser' => $_POST['touser'],
					'touser_pic' => $_POST['touser_pic'],
					'message' => $_POST['msg'],	
					'create_time'=>time()
				);
				if(!M('user')->where(array('id'=>$data['fromuser']))->find()){
					$this->SendAjax(0,'发送者错误');
				}
				if(!M('user')->where(array('id'=>$data['touser']))->find()){
					$this->SendAjax(0,'接受者错误');
				}
				if(M('message')->add($data)){
					$this->SendAjax(1,'发送成功');
				}else{
					$this->SendAjax(0,'发送失败');
				}
			}
			$message_time = session('message_time');
			$touser = $_POST['touser'];
			$where = '((fromuser='.$this->user['id'].' and touser='.$touser.') or (fromuser='.$touser.' and touser='.$this->user['id'].')) and create_time>'.$message_time;
			$touser = M('message')->where($where)->order('create_time asc')->select();
			session('message_time',NOW_TIME);
			if($touser){
				$this->SendAjax(1,$touser,$where);
			}else{
				$this->SendAjax(0);
			}
	
		}else{
			$this->SendAjax(0,'非法请求');
		}
	}
	
	//意见反馈
	public function leaveMsg(){
		if(IS_POST){
			$data['message'] = $_POST['content'];
			$data['mobile'] = $_POST['mobile'];
			$data['username'] = $_POST['username'];
			if($data['message'] =='' || $data['mobile']=='' || $data['username']==''){
				$this->error('请输入必填信息');
			}
			$data['user_id'] = $this->user['id'];
			$data['create_time'] = time();
			if(M('message')->add($data)){
				$this->success('感谢您的宝贵意见!',U('Ucenter/index'));
			}else{
				$this->error('留言失败');
			}
			exit;
		}
		$this->display();
	}
	
	//收米信息
	public function mySeparate(){
		//一、二、三级收米人数
		$this->assign('sp1',$this->getSeparateCount('parent1'));
		$this->assign('sp2',$this->getSeparateCount('parent2'));
		$this->assign('sp3',$this->getSeparateCount('parent3'));
		$this->display();
	}
	
	protected function getSeparateCount($field){
		if(!$field && ($field!='parent1' || $field!='parent2' || $field!='parent3')){
			return 0;
		}
		
		$where[$field] = $this->user['id'];
		$list = M('user')->where($where)->select();
		if($list){
			foreach($list as $k=>$v){
				$list[$k]['separate'] = M('separate_log')->where(array('self_id'=>$v['id'],'status'=>2))->sum('money');
			}
			if(!$list[$k]['separate'] || $list[$k]['separate']==0){
				unset($list[$k]);
			}
		}
		return count($list);
	}
	
	//获取收米信息
	public function getSeparate(){
		$status = I('post.status');
		$page = I('post.page')?I('post.page'):1;
		$pagesize = 5 ;
		$start = ($page-1)*$pagesize;
		$where[$status] = $this->user['id'];
 		$list = M('user')->where($where)->order('id desc')->limit($start,$pagesize)->select();
		if($list){
			foreach($list as $k=>$v){
				$list[$k]['separate'] = M('separate_log')->where(array('self_id'=>$v['id'],'status'=>2))->sum('money');
			}
			if(!$list[$k]['separate'] || $list[$k]['separate']==0){
				unset($list[$k]);
			}
		}
		$this->assign('list',$list);
		$this->assign('page',$page);
		$html = $this->fetch();
		if(!$list){
			$this->error($html);
		}else{
			$this->success($html);
		}
	}
	
	//我的米粒信息
	public function myMl(){
		$this->display();
	}
	
	public function getMl(){
		$page = I('post.page')?I('post.page'):1;
		$pagesize = 10;
		$start = ($page-1)*$pagesize;
		$where['user_id'] = $this->user['id'];
 		$list = M('finance_log')->where($where)->order('create_time desc')->limit($start,$pagesize)->select();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$html = $this->fetch();
		if(!$list){
			$this->error($html);
		}else{
			$this->success($html);
		}
	}
	
	
	//提现
	public function withdraw(){
		if(IS_POST){
			$status = $_POST['status'];
			$money = $_POST['money'];
			
			if($money==''){
				$this->ajaxReturn(array('status'=>0,'info'=>'请输入您要提现的金额'));
			}
			// 检查余额是否足够
			if($this -> user['money'] < $money){
				$this->ajaxReturn(array('status'=>0,'info'=>'您的余额不足'));
			}
			if($status==1){
				if($money<1 || $money>200){
					$this->ajaxReturn(array('status'=>0,'info'=>'您的提款金额不对'));
				}
				//添加微信红包
				$dd = new \Common\Util\ddwechat($this -> _mp);
				$arr = array();
				$arr['nonce_str']		= $dd->getnoncestr();
				$arr['mch_billno']		= time().mt_rand(1000, 9999);
				$arr['mch_id'] 			= $this -> _mp['mch_id'];
				$arr['wxappid'] 		= $this -> _mp['appid'];
				$arr['send_name'] 		= $this -> _site['name'];
				$arr['re_openid'] 		= $this -> user['openid'];
				$arr['total_amount'] 	= $money*100;
				$arr['min_value'] 		= $money*100;
				$arr['max_value'] 		= $money*100;
				$arr['total_num'] 		= 1;
				$arr['wishing'] 		= $this -> _site['name'].'提现';
				$arr['client_ip'] 		= $_SERVER['SERVER_ADDR'];
				$arr['act_name'] 		= '自助提现';
				$arr['remark'] 			= date('Y-m-d H:i:s').'申请提现';
				$arr['sign'] 			= $dd->getsign($arr, $this -> _mp['key']);
				
				$ssl = array(
					'sslcert' => $_SERVER['DOCUMENT_ROOT'] .__ROOT__. $this -> _mp['cert'].'apiclient_cert.pem',
					'sslkey'  => $_SERVER['DOCUMENT_ROOT'] .__ROOT__. $this -> _mp['cert'].'apiclient_key.pem',
				);
				$rt = $dd->redpack($arr, $ssl);
				if($rt['return_code'] == 'SUCCESS' && $rt['result_code'] == 'SUCCESS'){
					M('user') -> where("id=".$this -> user['id']) -> setDec('money',$money);
					
					$status = 2; // 提现完成
					flog($this -> user['id'],'money',$money, 3); // 记录退回财务日志
					$data = array(
						'user_id'=>$this->user['id'],
						'money'=>$money,
						'create_time'=>NOW_TIME,
						'confirm_time'=>NOW_TIME,
						'err_msg'=>$rt['return_code'],
						'status'=>$status,
					);
					M('withdraw')->add($data);
					$this->ajaxReturn(array('status'=>1,'info'=>'提现成功'));
				}else{
					$status = 4; // 提现失败
					$data = array(
						'user_id'=>$this->user['id'],
						'money'=>$money,
						'create_time'=>NOW_TIME,
						'confirm_time'=>NOW_TIME,
						'audit_time'=>NOW_TIME,
						'err_msg'=>$rt['err_code_des'],
						'status'=>$status,
					);
					M('withdraw')->add($data);
					$this->ajaxReturn(array('status'=>1,'info'=>$rt['err_code_des']));
				}
				
			}elseif($status==2){
				if($money<1 || $money>10000){
					$this->ajaxReturn(array('status'=>0,'info'=>'提现的金额不对'));
				}

				// 减少可用余额
				M('user') -> where("id=".$this -> user['id']) -> setDec('money',$money);
				// 增加提现记录
				$rs = M('withdraw') -> add(array(
					'user_id' => $this -> user['id'],
					'money' => $money,
					'bank' => serialize($bank),
					'bank_id' => intval($_POST['bank']),
					'way' => 2, // 提现方式，1银行卡，2一件转账
					'create_time' => NOW_TIME,
					'err_msg'=>'提现申请',
					'status' => 1
					));
				if($rs){
					$this->ajaxReturn(array('status'=>1,'info'=>'申请提现成功','url'=>U('Ucenter/index')));
					exit;
				}				
			}
			exit;
		}
		//已提现
		$this->display();
	}
	
	//提现记录
	public function withdraw_recode(){
		$page = $_POST['p']?$_POST['p']:1;
		$pagesize = 5;
		$stat = ($page-1)*$pagesize;
		$where['user_id'] = $this->user['id'];
		$where['status'] = 3;
		$count =M('withdraw')->where($where)->count();
		$list = M('withdraw')->where($where)->order('id desc')->limit($stat,$pagesize)->select();
		$page_list = ceil($count/$pagesize);

		$html = '';
		
		if(!$list){
			if($page==1){
				$html.='<li>无提现记录 <span class="f_r"></span></li>';	
				$this->ajaxReturn(array('status'=>1,'info'=>$html,'page_list'=>$page_list));
			}else{
				$this->ajaxReturn(array('status'=>0,'info'=>'已全部加载完成','page_list'=>$page_list));
			}
		}else{
			foreach($list as $k=>$v){
				$html.='<li>'.date('Y-m-d H:i:s',$v['create_time']).' 提现 <span class="f_r">'.$v['money'].'元</span></li>';
			}
			$this->ajaxReturn(array('status'=>1,'info'=>$html,'page_list'=>$page_list));
		}
		
		
	}
	
}?>