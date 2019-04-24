<?php
namespace Admin\Controller;
use Think\Controller;
class SelfmenuController extends AdminController {
    // 通知列表
	public function index(){
		$list = $this -> _get_menu_list();
		$this -> assign('list', $list);
		$this -> display();
	}
	
	// 编辑、添加通知
	public function edit(){
		// 查询所有一级菜单
		if(!IS_POST){
			$parents = M('selfmenu') -> where('pid=0') -> select();
			$this -> assign('parents', $parents);
		}else{
			// 不能选择自己作为父菜单
			if(isset($_GET['id']) && intval($_POST['id']) == intval($_GET['id'])){
				$this -> error('不能选择自己作为父菜单');
			}
		}
		$this -> _edit('selfmenu', U('index'));
	}
	
	// 删除通知
	public function del(){
		$this -> _del('selfmenu', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	// 发布自定义菜单
	public function send(){
		$list = $this -> _get_menu_list();
		$data = array();
		foreach($list as $v){
			$tmp = array(
				'name' => $v['name'],
				'type' => $v['type']
			);
			if($v['type'] == 'view'){
				$tmp['url'] = $v['extra'];
			}else{
				$tmp['key'] = $v['extra'];
			}
				
			// 顶级菜单
			if($v['pid'] == 0){
				$data[] = $tmp;
			}else{
				$data[count($data)-1]['sub_button'][] = $tmp;
			}
		}
				
		$dd = new \Common\Util\ddwechat($this -> _mp);
		$accesstoken = $dd -> getaccesstoken();
		$rs = $dd -> createmenu($dd -> jsonencode(array('button' => $data)), $accesstoken);
		if(!$rs){
			$this -> error("操作失败!".$dd -> errmsg);
		}else{
			$this -> success('操作成功！');
			exit;
		}
	}
	
	// 取得所有自定义菜单
	private function _get_menu_list(){
		$list = $this -> _get_list('selfmenu', null, 'sort desc');
		
		// 将数据转为树形结构
		$tree = array();
		foreach($list as &$v){
			if($v['pid'] == 0){
				$tree[] = $v;
				// 取得该顶级菜单的所有子菜单
				foreach($list as $val){
					if($val['pid'] == $v['id']){
						$tree[] = $val;
					}
				}
			}
		}
		return $tree;
	}
}