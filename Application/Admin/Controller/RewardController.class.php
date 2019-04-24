<?php
namespace Admin\Controller;
use Think\Controller;
class RewardController extends AdminController {
    // 记录列表
	public function index(){
		$this -> assign('level', get_level_name_arr($this -> _level));
		$this -> _list('reward');
	}
	
	// 编辑、添加
	public function edit(){
		if(IS_POST){
			$level = intval($_POST['level']);
			$money = floatval($_POST['money']);
			if($money <= 0 ){
				$this -> error('金额有误!');
			}
			
			$user_model = M('user');
			$count = $user_model -> where(array('level' => $level)) -> count();
			if($count <= 0 ){
				$this -> errror('这个等级没有用户，不需要发放绩效');
			}
			
			// 保存记录
			M('reward') -> add(array(
				'level' => $level,
				'money' => sprintf('%.2f', $money),
				'total' => $money*$count,
				'nums' => $count,
				'create_time' => NOW_TIME
			));
			
			$user_list = $user_model -> where(array('level' => $level)) -> select();
			foreach($user_list as $user){
				$user_model -> where('id='.$user['id']) -> save(array(
					'money' => array('exp', 'money+'.$money)
				));
				// 添加财务记录
				flog($user['id'], 'money', $money, 4);
			}
			
			$this -> success('操作成功！', U('index'));
			exit;
		}
		
		// 查询昨天的销售额
		$time1 = strtotime('today');
		$time2 = $time1  - 86400;
		$last_sales = M('order') -> where(array(
			'pay_time' => array('between',array($time2,$time1))
		)) -> sum('total');
		
		$this -> assign('last_sales', $last_sales);
		
		$this -> display();
	}
	
	
	// 获取用户数量
	public function get_user_count(){
		$level = intval($_POST['level']);
		$count = M('user') -> where(array('level' => $level)) -> count();
		$this -> ajaxReturn(array(
			'nums' => $count,
			'bonus' => $GLOBALS['_CFG']['level'][$level]['bonus']
		));
	}
}