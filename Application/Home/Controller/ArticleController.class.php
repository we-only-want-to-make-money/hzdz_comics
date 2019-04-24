<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends Controller {
    public function index(){
		die('错误访问');
    }
	
	// 阅读文章
	public function read(){
		$id = intval($_GET['id']);
		$info = M('article') -> find($id);
		
		// 设置了外链则直接跳转
		if(!empty($info['link'])){
			redirect($info['link']);
			exit;
		}
		
		$this -> assign('info', $info);
		$this -> display();		
	}
}