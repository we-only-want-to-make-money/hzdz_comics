<?php
namespace Home\Controller;
use Think\Controller;
class MhHomeController extends Controller {
    public function _initialize(){
		// 加载配置
		$config = M('config') -> select();
		if(!is_array($config)){
			die('请先在后台设置好各参数');
		}
		foreach($config as $v){
			$key = '_'.$v['name'];
			$this -> $key = unserialize($v['value']);
			$_CFG[$v['name']] = $this -> $key;
		}
		$this -> assign('_CFG', $_CFG);
		$GLOBALS['_CFG'] = $_CFG;
		
		//print_r($this -> _level);
		
		if(APP_DEBUG && $_GET['user_id']) {
			session('user', M('user') -> find(intval($_GET['user_id'])));
			session('user_id', $_GET['user_id']);
			$this -> user = M('user') -> find(session('user.id'));
			$this -> assign('user', $this -> user);
		}
		

		//$this -> tplmsg = new \Common\Util\tplmsg;
		
		/* if(session('?user') && empty($_GET['spm'])){
			// 不能直接从session获取数据，不是最新的 
			$this -> user = M('user') -> find(session('user.id'));
			session('user_id', $user);
		} else {
			session('user_id', 0);
		} */
		
		
		// 统计昨日全站数据报告
		//$this -> _data_log();
		
		
		// 需要鉴定分销权限的模块
		/* $dist_arr = array( 'my_agent', 'separate', 'qrcode', 'get_qrcode','myshop');
		if(in_array(ACTION_NAME, $dist_arr) && !$this -> _can('dist') && $this -> user['dist']!=1){ // 所在等级没有分销权限，且没有设置的分销权限
			$this -> error('您没有分销权限！');
		} */
		
		// 需要提现权限的模块
		/* $withdraw_arr = array( 'withdraw', 'withdraw_add');
		if(in_array(ACTION_NAME, $withdraw_arr) && !$this -> _can('withdraw')){
			$this -> error('您没有提现权限！');
		} */
		
		// 需要转让权限的模块
		/* $deposit_arr = array('deposit','deposit_add');
		if(in_array(ACTION_NAME, $deposit_arr) && !$this -> _can('deposit')){
			$this -> error('您没有转让权限！');
		} */
		
		// 调用jssdk
		$dd = new \Common\Util\ddwechat();
		$dd -> setParam($this -> _mp);
		$jssdk = $dd -> getsignpackage();
		$this -> assign('jssdk', $jssdk);
	}
	
	//判断是否登陆过
	public function check_login() {
		$user_id = session('user_id');
		if(empty($user_id)) {
			redirect(U('MhPublic/login'));
		} else {
			$user_info = M('user')->where("id={$user_id}")->find();
			if($user_info['vip_level'] > 0) {
				if($user_info['vip_endtime'] < NOW_TIME) {
					M('user')->where("id={$user_id}")->setField('vip_level', 0);
				}
			}
		}
		return $user_id;
	}
	
	// 判断权限
	private function _can($type){
		//if($this -> _level[$this -> user['level']][$type] > 0){
			return true;
		//}else{
			//return false;
		//}
	}
	
	// 自动确认收货
	private function _auto_confirm(){
		if(!empty($this -> _site['auto_confirm']) && $this -> _site['auto_confirm'] >0){
			$time = strtotime('-'.$this -> _site['auto_confirm'].'days');
			// 所有发货时间超过制定时间的待收货的订单
			$orders = M('order') -> where(array(
				'delivery_time' => array('lt', $time),
				'status' => 3
			)) -> select();
			if($orders){
				foreach($orders as $order_info){
					confirm_order($order_info);
				}
			}
		}
	}
	
	//公共ajax返回函数
	public function SendAjax($status = 1,$msg = '操作成功',$url='',$flag=''){
		$data = array(
			'status'=>$status,
			'msg'=>$msg,
			'url'=>$url,
			'flag'=>$flag
		);
		$this->ajaxReturn($data);
	}
	
	// 未付款自动取消
	private function _auto_cancle(){
		if(!empty($this -> _site['auto_cancle']) && $this -> _site['auto_cancle'] >0){
			$time = strtotime('-'.$this -> _site['auto_cancle'].'days');
			// 所有发货时间超过制定时间的待收货的订单
			$orders = M('order') -> where(array(
				'create_time' => array('lt', $time),
				'status' => 1
			)) -> select();
			
			if($orders){
				foreach($orders as $order_info){
					cancle_order($order_info,-1);
				}
			}
		}
	}
	
	
	// 统计昨日全站数据报告
	private function _data_log(){
		$date = date('Ymd', strtotime('-1 day'));
		$info = M('data') -> where('date='.$date) -> find();
		// 如果有昨天的记录则结束
		if($info){
			return;
		}
		$etime = strtotime('today');
		$stime = $etime - 86400;
		$where['create_time'] = array('between', array($stime, $etime));
		$data['orders'] = M('order') -> where($where) -> count();
		$data['total']  = M('order') -> where($where) -> sum('money');
		if(!$data['total']){
			$data['total'] = 0;
		}
		$data['subs']   = M('user') -> where("sub_time between $stime and $etime") -> count();
		$data['date'] = $date;
		M('data') -> add($data);
	}
	
	
	
}