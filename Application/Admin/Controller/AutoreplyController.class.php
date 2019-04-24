<?php
namespace Admin\Controller;
use Think\Controller;
class AutoreplyController extends AdminController {
    // 通知列表
	public function index(){
		$this -> _list('autoreply');
	}
	
	// 编辑、添加通知
	public function edit(){
		if(IS_POST){
			if(empty($_POST['keyword'])){
				$this -> error('关键词不能为空');
			}
		}
		$this -> _edit('autoreply', U('index'));
	}
	
	
	// 改变状态
	public function set_status(){
		$id = intval($_GET['id']);
		$status = intval($_GET['status']) > 0 ? 1 : 0;
		M('autoreply') -> where("id={$id}") -> setField('status', $status);
		$this -> success('操作成功！');
	}
	
	// 删除通知
	public function del(){
		$this -> _del('autoreply', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
}