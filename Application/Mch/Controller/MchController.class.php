<?php
namespace Mch\Controller;
use Think\Controller;
class MchController extends AdminController {
	
    public function index(){
		if(IS_POST){
			$_POST['password'] = xmd5($_POST['tpassword']);
			M('member')->where(array('id'=>$this->mch['id']))->save($_POST);
			$this->success('操作成功！',U('index'));
			exit;
		}
		$this->assign('info',M('member')->find(intval($this->mch['id'])));
		$this->display();
    }
	
	
	//代理提现
	public function withdraw(){
		if(IS_POST){
			$_GET = $_REQUEST;
			$_GET['p'] = 1;
		}
		if(!empty($_GET['status'])){
			$mp['status'] = intval($_GET['status']);
		}
		
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$mp['create_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2']) + 86400)
			);
		}elseif(!empty($_GET['time1'])){
			$mp['create_time'] = array('gt', strtotime($_GET['time1']));
		}elseif(!empty($_GET['time2'])){
			$mp['create_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		$mp['mid'] = $this->mch['id'];
		$this->_list('m_withdraw',$mp,'create_time desc');
	}
	
	public function edit(){
		if(IS_POST){
			$money = I('post.money');
			$m = M('member')->find(intval($this->mch['id']));
			if($m['money']<$money){
				$this->error('您的可提现佣金不足');
			}else{
				M('m_withdraw')->add(array(
					'mid'=>$this->mch['id'],
					'zfb'=>$this->mch['zfb'],
					'money'=>$money,
					'create_time'=>time(),
				));
				M('member')->where(array('id'=>$this->mch['id']))->setDec('money',$money);
				$this->success('申请成功，请等待审核',U('withdraw'));
			}
			exit;
		}
		$info = M('member')->find(intval($this->mch['id']));
		$this->assign('info',$info);
		$this->display();
	}
}