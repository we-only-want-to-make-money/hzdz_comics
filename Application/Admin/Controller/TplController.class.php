<?php
namespace Admin\Controller;
use Think\Controller;
class TplController extends AdminController {
    // 商品列表
	public function index(){
		$this->_list('tpl');
	}
	
	// 编辑、添加商品
	public function edit(){
		$this->_edit('tpl');
	}
	
	
	
	// 删除商品
	public function del(){
		$this -> _del('tpl', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	
	
	
	
	
}