<?php
namespace Home\Controller;
use Think\Controller;
class AgentController extends HomeController {
	public function _initialize(){
		parent::_initialize();
		
	}	
	
	//预存代理页面
	public function index(){
		$this->display();
	}
	
	public function pay(){
		$type = I('post.type');
		$money = I('money');
		$lv = get_lv_money($money);
		if(!$lv){
			$this->error('不存在对应等级的代理');
		}
		$data = array(
			'user_id' => $this->user['id'],
			'sn' => create_order_sn($this->user['id']),
			'lv' => $lv,
			'money' => $money,
			'wxid' => I('post.wxid'),
			'mobile' => I('post.mobile'),
			'create_time'=> time(),
			'name'=>I('post.true_name'),
			'separate'=>0,
		);
		$order_id = M('agent')->add($data);
		//更新用户表数据
		if(!$this->user['mobile']){
			$save['mobile'] = I('post.mobile');
			M('user')->where(array('id'=>$this->user['id']))->save($save);
		}
		if(!$this->user['wxid']){
			$save['wxid'] = I('post.wxid');
			M('user')->where(array('id'=>$this->user['id']))->save($save);
		}
		if(!$this->user['true_name']){
			$save['true_name'] = I('post.true_name');
			M('user')->where(array('id'=>$this->user['id']))->save($save);
		}
		if($order_id){
			if($type == 1){
				$params = wxPay($order_id,'agent','agent');
			}else{
				$params = $order_id;
			}
			$this->success($params);
		}else{
			$this->error('生成订单失败');
		}
		
	}
	
	
	//充值记录
	public function Record(){
		$list = M('agent')->where(array('user_id'=>$this->user['id']))->order('create_time desc')->select();
		$this->assign('total',M('agent')->where(array('user_id'=>$this->user['id'],'status'=>2))->sum('money'));
		$this->assign('list',$list);
		$this->display();
	}
	
	
	//支付宝支付方法
	public function ali_pay($order,$table=''){
		
	}
}