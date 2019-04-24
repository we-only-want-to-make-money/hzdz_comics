<?php
namespace Admin\Controller;
use Think\Controller;
class NoticeController extends AdminController {
    // 通知列表
	public function index(){
		$this -> _list('notice');
	}
	
	// 编辑、添加通知
	public function edit(){
		if(!isset($_GET['id'])){
			$_POST['create_time'] = NOW_TIME;
		}
		$this -> _edit('notice', U('index'));
	}
	
	// 删除通知
	public function del(){
		$this -> _del('notice', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
}