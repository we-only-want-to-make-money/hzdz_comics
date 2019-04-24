<?php
namespace Admin\Controller;
use Think\Controller;
class ArticleController extends AdminController {
    // 商品列表
	public function index(){
		$autoreply_id = I('autoreply', '0', 'intval');
		if(isset($_GET['autoreply'])){
			$where = array(
				'autoreply_id' => $autoreply_id
			);
		}

		$autoreply_name = M('autoreply')->where("id={$autoreply_id}")->getField('keyword');
		$this->assign('autoreply_name', $autoreply_name);
		$this -> _list('article', $where);
	}
	
	// 编辑、添加商品
	public function edit(){
		if(IS_POST){
			if(!isset($_GET['id']))
				$_POST['create_time'] = NOW_TIME;
			
			if(isset($_GET['autoreply'])){
				$_POST['autoreply_id'] = intval($_GET['autoreply']);
			}
			
			$_POST['show_cover'] = isset($_POST['show_cover']) ? 1 : 0;
		}
		$this -> _edit('article', U('index?autoreply='.$_GET['autoreply']));
	}
	
	// 删除商品
	public function del(){
		$this -> _del('article', $_GET['id']);
		$this -> success('删除成功！');
	}
}