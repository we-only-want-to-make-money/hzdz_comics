<?php
namespace Admin\Controller;
use Think\Controller;
class JubController extends AdminController {
    // 列表
	public function index(){
		$this->assign('jub',C('JUB'));
		$this -> _list('jubao');
	}
	
	public function del(){
		$this -> _del('jubao', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	

}