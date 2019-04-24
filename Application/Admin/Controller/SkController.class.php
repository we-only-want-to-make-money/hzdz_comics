<?php
namespace Admin\Controller;
use Think\Controller;
class SkController extends AdminController {
    
	// 列表
	public function index(){
		$this -> _list('sk');
	}
	
	// 编辑、添加
	public function edit(){
		if(IS_POST){
			$form = array();
			foreach($_POST['form_name'] as $key => $val){
				$form[] = array(
					'name' => $val,
					'tips' => $_POST['form_tips'][$key]
				);
			}
			
			$_POST['form'] = serialize($form);
			if($_GET['id']){
				M('sk') -> where(array('id' => intval($_GET['id']))) -> save($_POST);
			}else{
				$_POST['create_time'] = NOW_TIME;
				M('sk') -> add($_POST);
			}
			
			$this -> success('操作成功！', U('index'));
			exit;
		}
		
		if($_GET['id']){
			$info = M('Sk') -> find(intval($_GET['id']));
			$info['form'] = unserialize($info['form']);
			$this -> assign('info', $info);
		}
		$this -> display();
	}
	
	// 删除
	public function del(){
		$this -> _del('sk', $_GET['id']);
		M('sk_order') -> where('sk_id='.intval($_GET['id'])) ->delete();
		$this -> success('删除成功！', $_SERVER['HTTP_REFERER']);
	}
	
	// 订单管理
	public function orders(){
		$id = intval($_GET['id']);
		$where['sk_id'] = $id;
		$list = $this -> _get_list('sk_order',$where);
		foreach($list as &$v){
			$v['form'] = unserialize($v['form']);
		}
		$this -> assign('list', $list);
		$this -> assign('page', $this -> data['page']);
		$this -> display();
	}
	
	// 收款地址
	public function url(){
		$id = intval($_GET['id']);
		$info = M('sk') -> find($id);
		$this -> assign('info', $info);
		$this -> display();
	}
	
	
}