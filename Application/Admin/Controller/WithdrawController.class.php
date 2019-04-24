<?php
namespace Admin\Controller;
use Think\Controller;
class WithdrawController extends AdminController {
    // 列表
	public function index(){
		if(IS_POST){
			$_GET = $_REQUEST;
		}
		
		if(!empty($_GET['status'])){
			$where['status'] = intval($_GET['status']);
		}
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$where['create_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2'])+86400)
			);
		}elseif(!empty($_GET['time1'])){
			$where['create_time'] = array('gt', strtotime($_GET['time1']));
		}elseif(!empty($_GET['time2'])){
			$where['create_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		
		$list = $this -> _get_list('withdraw', $where);
		
		// 银行转账才需要显示银行卡信息
		if($this -> _site['withdraw'] == 1){
			foreach($list as &$v){
				$v['bank'] = unserialize($v['bank']);
			}
		}
		
		$this -> assign('list', $list);
		$this -> assign('page', $this -> data['page']);
		$this -> display();
	}
	
	// 拒绝
	public function refuse(){
		$id = intval($_GET['id']);
		$info = M('withdraw') -> find($id);
		if($info['status'] !=1){
			$this -> error('不能进行该操作');
		}
		M('withdraw') -> where('id='.$id) -> save(array(
			'status' => -1,
			'confirm_time' => NOW_TIME
		));
		
		// 拒绝后需要把余额退回到账户
		
		$user_info = M('user') -> find($info['user_id']);
		M('user') -> where('id='.$info['user_id']) -> save(array(
			'rmb' => array('exp', 'rmb+'.$info['money'])
		));
		flog($info['user_id'],'money',$info['money'], 11); // 记录财务日志
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	// 审核
	public function audit(){
		$id = intval($_GET['id']);
		$info = M('withdraw') -> find($id);
		if($info['status'] !=1){
			$this -> error('不能进行该操作');
		}
		M('withdraw') -> where('id='.$id) -> save(array(
			'status' => 2,
			'audit_time' => NOW_TIME
		));
		redirect($_SERVER['HTTP_REFERER']);
		
	}
	
	// 确认完成
	public function confirm(){
		$id = intval($_GET['id']);
		$info = M('withdraw') -> find($id);
		if($info['status'] !=2){
			$this -> error('不能进行该操作');
		}
		M('withdraw') -> where('id='.$id) -> save(array(
			'status' => 3,
			'confirm_time' => NOW_TIME
		));
		redirect($_SERVER['HTTP_REFERER']);
	}
	
}