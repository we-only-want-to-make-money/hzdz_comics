<?php
namespace Admin\Controller;
use Think\Controller;
class ReportController extends AdminController {
    
	// 统计报表
	public function index(){
		if(IS_POST){
			$stime = strtotime($_POST['stime']);
			$etime = strtotime($_POST['etime']);
			
			$where['date'] = array(array('gt',date('Ymd', $stime)), array('lt',date('Ymd', $etime)));
		}else{
			$limit = 7;
		}
		
		// 查询最近七天的报表
		$list = M('data') -> limit($limit) -> where($where) -> order('date desc') -> select();
		$data = array();
		for($i = count($list)-1; $i >=0; $i--){
			$val = $list[$i];
			$cates[] = $val['date'];
			$data['orders'][] = (int)$val['orders'];
			$data['wxpay'][] = sprintf("%2.f",$val['wxpay']);
			$data['total'][] = sprintf("%2.f",$val['total']);
			$data['subs'][] = (int)$val['subs'];
		}
		$this -> assign('data', json_encode($data));
		$this -> assign('cates', json_encode($cates));
		$this -> display();
	}
	
	// 实时数据
	public function instant(){
		$data = array();
		
		// 所有订单数
		$data['orders'] = M('order') -> count();
		
		// 已完成订单
		$data['order_4'] = M('order') -> where(array('status' => 4)) -> count();
		
		// 已取消订单
		$data['order_0'] = M('order') -> where(array('status' => -1)) -> count();
		
		// 代发货订单
		$data['order_3'] = M('order') -> where(array('status' => 2)) -> count();
		
		// 待支付订单
		$data['order_2'] = M('order') -> where(array('status' => 1)) -> count();
		
		// 总营业额
		$data['money_total'] = M('order') -> where(array('status' => array('gt' , 0))) -> sum('total');
		
		// 微信支付总入账
		$data['money_wxpay'] = M('order') -> where() -> sum('wxpay');
		
		// 会员总余额
		$data['user_money'] = M('user') -> sum('money');
		
		// 今日销售额
		$data['total_today'] = M('order') -> where(array(
			'create_time' => array('gt', strtotime('today')),
			'status' => array('gt', 1)
			)) -> sum('total');
			
		
		// 今日现金支付
		$data['money_today'] = M('order') -> where(array(
			'create_time' => array('gt', strtotime('today')),
			)) -> sum('wxpay');
		
		// 会员总积分
		$data['user_points'] = M('user') -> sum('points');
		
		// 分成总额
		$data['separate_money_total'] = M('separate_log') -> sum('money');
		$data['separate_points_total'] = M('separate_log') -> sum('points');
		
		// 待发放分成
		$data['separate_money_wait'] = M('separate_log') -> where('status=1') -> sum('money');
		$data['separate_points_wait'] = M('separate_log') -> where('status=1') -> sum('points');
		
		// 已发放金额
		$data['separate_money_done'] = M('separate_log') -> where('status=4') -> sum('money');
		$data['separate_points_done'] = M('separate_log') -> where('status=4') -> sum('points');
		
		$this -> assign('info', $data);
		$this -> display();
	}
	
}