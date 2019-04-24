<?php
namespace Home\Model;
use Think\Model;

class OrderModel extends Model{
	
	// 改变订单状态
	function set_status($order_id, $status){
		$this -> where('id='.$order_id) -> setField('status', $status);
		// 支付或者完成或者取消订单需要更新分成状态
		if(in_array($status,array(-1,2,4))){
			// 订单状态支付后分成变成待确认状态
			if($status == 2){
				$status = 3;
			}
			M('separete_log') -> where('order_id='.$order_id) -> setField('status',$status);
			
			// 如果是完成则增加用户余额
			if($status == 4){
				$separate_logs = M('separate_log') -> where('order_id='.$order_id) -> select();
				foreach((array)$separate_logs as $separate_log){
					M('user') -> where('id='.$separate_log['user_id']) -> save(array(
						'money' => array('exp', 'money+'.$separate_log['money']),
						'points' => array('exp', 'points+'.$separate_log['points']))
					);
				}
			}
		}
	}
}