<?php
namespace Admin\Controller;
use Think\Controller;
class ChargeController extends AdminController {
    // 通知列表
	public function index(){
		$where = $this -> _get_where();
		$where['status'] = 2;
		$list = $this -> _get_list('charge',$where);
		foreach ($list as $k=>$v){
			$list[$k]['nickname'] = M('user')->where(array('id'=>$v['user_id']))->getField('nickname');
		}
		$this->assign('list',$list);
		$this->assign('page',$this->data['page']);
		$this->display();
	}
	
	private function _get_where(){
		if(IS_POST){
			$_GET  = array_merge($_GET, $_POST);
			$_GET['p'] = 1; //如果是post的话回到第一页
		}
		
		if(!empty($_GET['user_id'])){
			$where['user_id'] = intval($_GET['user_id']);
		}
		
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$where['create_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2']) + 86400)
			);
		}
		elseif(!empty($_GET['time1'])){
			$where['create_time'] = array('gt', strtotime($_GET['time1']));
		}
		elseif(!empty($_GET['time2'])){
			$where['create_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		return $where;
	}
	
}?>