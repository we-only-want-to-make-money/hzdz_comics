<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends AdminController {
    public function index(){
		// 入口，已登录调到首页，未登录跳转到登陆
		
		if(session('?admin'))
			
			redirect(U('Admin/welcome'));
		else
			redirect(U('Index/login'));
    }
	
	// 登录
	public function login(){
		if(IS_POST){
			if(empty($_POST['user']) || empty($_POST['pass'])){
				$this -> assign('errmsg', '账号密码不能为空！');
			}else {
				$admininfo = M('admin')->where("username='{$_POST['user']}'")->find();
				if(!empty($admininfo)) {
					if(xmd5($_POST['pass']) == $admininfo['password']){
					
						session('admin', $admininfo);
						session('admin_id', $admininfo['id']);
					
						/* if(isset($_POST['remember'])){
							cookie('admin_user', $_POST['user']);
						} */
					
						redirect(U('Admin/welcome'));
						exit;
					}else{
						$this -> assign('errmsg', '密码不对');
					}
				} else {
					$this -> assign('errmsg', '账号不存在！');
				}
				
			} 
		}
		$this -> display();
	}
	
	//  退出
	public function logout(){
		session('admin',null);
		redirect(U('login'));		
	}
	
	
	public function test(){
		$path = "./Public/xiaoshuo/ff1f75a539329871e74c99373104fb78/";
		$temp = array();
		if (is_dir($path)) {
			$temp = array();
			if ($handle = opendir($path)) {
				$i = 1;
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$temp[] = $file;
					}
				}
				closedir($handle);
				sort ($temp,SORT_NUMERIC);
				reset ($temp);

				foreach ($temp as $v) {
					
					$str = file_get_contents($path.$v);
					$str = "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$str;
					$str = preg_replace('/\n|\r\n/','</p></br><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$str);
					$v = iconv('GBK','utf-8',$v);
					if(substr(strrchr($v , '.'), 1) == 'txt'){
						$before = $i-1;
						$next = $i+1;
						$title = trim(substr($v,0,-4));
						$str = iconv('GBK','utf-8',$str);
						$ds = array(
							"bid"=>$product_id,
							"title"=>$title,
							"ji_no"=>$i,
							"info"=>$str,
							"like"=>0,
							"before"=>$before,
							"next"=>$next,
							"money"=>0,
							"create_time"=>time(),
							"update_time"=>0,
						);
						dump($ds);
					}
					$i++;
				}	
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
}