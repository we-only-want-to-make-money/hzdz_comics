<?php
namespace Admin\Controller;
use Think\Controller;
class MemberController extends AdminController {
    // 列表
	public function index(){
		$list = $this ->_get_list('member');
		foreach($list as $k=>$v){
			$list[$k]['wcount'] = M('m_withdraw')->where(array('mid'=>$v['id'],'status'=>array('lt',2)))->count();
		}
		$this->assign('list',$list);
		$this->assign('page',$this->data['page']);
		$this->display();
	}
	
	// 编辑/添加
	public function edit(){
		if(IS_POST){
			if(!$_POST['id']){
				if(M('member')->where(array('mobile'=>$_POST['mobile']))->find()){
					$this->error('代理手机号码已被添加');
					exit;
				}
				$_POST['salt'] = Salt();
				$_POST['imei'] = xmd5($_POST['salt']);
				$_POST['create_time'] = NOW_TIME;
				
				$qrcode = $this->qrcode($_POST['imei']);
				$_POST['url'] = $qrcode['url'];
				$_POST['qrcode'] = $qrcode['qrcode'];
				$_POST['password'] = xmd5($_POST['tpassword']);
				M('member')->add($_POST);
			}else{
				$info = M('member')->find(intval($_POST['id']));
				$qrcode = $this->qrcode($info['imei']);
				$_POST['qrcode'] = $qrcode['qrcode'];
				$_POST['url'] = $qrcode['url'];
				$_POST['password'] = xmd5($_POST['tpassword']);
				M('member')->where(array('id'=>$_POST['id']))->save($_POST);
			}
			$this->success('操作成功！',U('index'));
			exit;
			
		}
		if($_GET['id']>0){
			$this->assign('info',M('member')->find(intval($_GET['id'])));
		}
		$this->display();
	}
	
	// 删除
	public function del(){
		$this -> _del('member', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	public function showQrcode(){
		$id = I('get.id');
		$info = M('member')->find(intval($id));
		$this->assign('info',$info);
		$this->display();
	}
	
	public function setStatus(){
		$id = I('get.id');
		$info = M('member')->find(intval($id));
		$status = $info['status']?0:1;
		M('member')->where(array('id'=>$id))->save(array('status'=>$status));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	//生成二维码
	public function qrcode($imei){
		//获取推广码信息

		$path_info = getAgentQrcode($imei);		
		$url = "http://".$_SERVER['HTTP_HOST'].__ROOT__."/index.php?imei=".$imei;
		if(!is_file($path_info['qrcode'])){
			include COMMON_PATH.'Util/phpqrcode/phpqrcode.php';
			// 目录不存在则创建
			if(!is_dir($path_info['path'])){
				mkdir($path_info['path'], 0777,1);
			}
			$errorCorrectionLevel = 'L';
			$matrixPointSize = 6;
			\QRcode::png($url, $path_info['qrcode'], $errorCorrectionLevel, $matrixPointSize, 2);	
		}
		return array(
			'url'=>$url,
			'qrcode'=>$path_info['qrcode'],
		);
	}
	
	//充值记录
	public function charge(){
		$id = I('get.id');
		$this->_list('charge',array('mid'=>$id,'status'=>2),'create_time desc');
	}
	
	//扣量记录
	public function cdesc(){
		$id = I('get.id');
		//$this->_list('member_desc',array('mid'=>$id,'status'=>2),'create_time desc');
        $list = M('member_separate')
               ->alias('s')
               ->field('u.nickname,s.*')
               ->join('vv_user as u on u.id = s.user_id')
               ->where(array('s.mid'=>$id,'s.is_status'=>2))
               ->select();
        $this->assign('list',$list);
        $this -> display();
	}
	
	//分成记录
	public function separate(){
		$id = I('get.id');
		$this->_list('member_separate',array('mid'=>$id,'status'=>2,'is_status'=>1),'create_time desc');
	}
	
	
	
	//实时结算
	public function jiesuan(){
		$id = I('get.id');
		$this->_list('member_data',array('mid'=>$id),'date desc');
	}
	
	//实时结算
	public function withdraw(){
		$id = I('get.id');
		$this->_list('m_withdraw',array('mid'=>$id),'create_time desc');
	}
	
	//审核提现
	public function audi(){
		$id = I('get.id');
		$status = I('get.status');
		$with = M('m_withdraw')->find(intval($id));
		M('m_withdraw')->where(array('id'=>$id))->save(array('status'=>$status));
		if($status == -1){
			M('member')->where(array('id'=>$with['mid']))->setInc('money',$with['money']);
		}
		$this->success('操作成功！');
	}
	
	
	//账户提现
	public function doWith(){
		if(IS_POST){
			$post = I('post.');
			$m = M('member')->find(intval($post['id']));
			if($m['money']<$post['money']){
				$this->error('可提现金额不足');
			}else{
				M('m_withdraw')->add(array(
					'mid'=>$m['id'],
					'zfb'=>$m['zfb'],
					'money'=>$post['money'],
					'create_time'=>time(),
					'status'=>3,
				));
				M('member')->where(array('id'=>$m['id']))->setDec('money',$post['money']);
				$this->success('提现成功');
			}
		}else{
			$this->error('非法请求！');
		}
	}
}