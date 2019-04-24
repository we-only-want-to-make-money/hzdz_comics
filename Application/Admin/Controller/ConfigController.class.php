<?php
namespace Admin\Controller;
use Think\Controller;
class ConfigController extends AdminController {
	public function _initialize(){
		parent::_initialize();
	}
	
	public function _empty(){
		$this -> _save();
		$this -> display();
	}
	
	public function bookcate(){
		if(IS_POST){
			$config = array();
			foreach($_POST['pic'] as $key => $val){
				$config[$key+1] = array('pic' => $_POST['pic'][$key], 'url' => $_POST['url'][$key],'name' => $_POST['name'][$key], 'show' => $_POST['show'][$key],'isshow' => $_POST['isshow'][$key]);
			}
			$_POST = $config;
		}
		$this -> _save();
		$this -> display();
	}
	
	public function yook(){
		if(IS_POST){
			$config = array();
			foreach($_POST['pic'] as $key => $val){
				$config[$key+1] = array('pic' => $_POST['pic'][$key], 'url' => $_POST['url'][$key],'name' => $_POST['name'][$key], 'show' => $_POST['show'][$key],'isshow' => $_POST['isshow'][$key]);
			}
			$_POST = $config;
		}
		$this -> _save();
		$this -> display();
	}
	
	
	public function mhcate(){
		if(IS_POST){
			$config = array();
			foreach($_POST['pic'] as $key => $val){
				$config[$key+1] = array('pic' => $_POST['pic'][$key], 'url' => $_POST['url'][$key], 'name' => $_POST['name'][$key], 'show' => $_POST['show'][$key],'isshow' => $_POST['isshow'][$key]);
			}
			$_POST = $config;
		}
		$this -> _save();
		$this -> display();
	}
	
	//打赏设置
	public function send(){
		if(IS_POST){
			$config = array();
			foreach($_POST['pic'] as $key => $val){
				$config[$key+1] = array('pic' => $_POST['pic'][$key], 'money' => $_POST['money'][$key]);
			}
			$_POST = $config;
			$this -> _save();
		}
		$this -> display();
	}
	
	// 配置管理账号
	public function user(){
		if(IS_POST){
			if(empty($_POST['name'])){
				
				$this -> error('请正确填写登录名');
				
			}else if($_POST['pass'] != $_POST['pass2'] || empty($_POST['pass'])){
				
				$this -> error('请正确填写密码!');
			}
			
			M('admin')->where(array('username'=>$_POST['name']))->save(array('password'=>xmd5($_POST['pass'])));
			$this->success('操作成功！');
			exit;
		}
		
		$this -> display();
	}
	
	// 配置公众号
	public function mp(){
		if(IS_POST){
			if(!empty($_FILES['cert']) && $_FILES['cert']['name'] == 'cert.zip'){
				 $upload = new \Think\Upload();
				 $upload->maxSize   =     3145728 ;
				 $upload->exts      =     array('zip');
				 $upload->rootPath  =     './Public/cert/';
				 $upload->savePath  =     xmd5(time().rand()).'/';
				 $upload ->autoSub = false;
				 $info   =   $upload->upload();
				 if($info){
					$info = $info['cert'];
					
					// 解压
					$path = $upload->rootPath . $info['savepath'];
					$file = $path . $info['savename'];
					
					if(file_exists($file)){
						// 打开压缩文件
						$zip = new \ZipArchive();
						$rs = $zip -> open($file);
						if($rs && $zip -> extractTo($path)){
							$zip -> close();
							$_POST['cert'] = $path;
						}
						else{
							$this -> error('解压失败，请确认上传了正确的cert.zip');
						}
					}
					else{
						$this -> error('系统没找到上传的文件');
					}
				 }
				 else {
					$this -> error('证书上传错误');
				 }
			}
			else{
				$_POST['cert'] = $this -> _mp['cert'];
			}
		}
		$this -> _save();
		$this -> display();
	}
	

	// 配置等级
	public function level(){
		if(IS_POST){
			$config = array();
			foreach($_POST['name'] as $key => $val){
				if(empty($val)){
					continue;
				}else{
					// 条件
					empty($_POST['orders'][$key]) && $_POST['orders'][$key] = 0;
					empty($_POST['agent1'][$key]) && $_POST['agent1'][$key] = 0;
					empty($_POST['agent2'][$key]) && $_POST['agent2'][$key] = 0;
					empty($_POST['agent3'][$key]) && $_POST['agent3'][$key] = 0;
					empty($_POST['consume'][$key]) && $_POST['consume'][$key] = 0;
					
					empty($_POST['sales'][$key]) && $_POST['sales'][$key] = 0;
					empty($_POST['touzibi'][$key]) && $_POST['touzibi'][$key] = 0;
					// 权限
					empty($_POST['dist'][$key]) && $_POST['dist'][$key] = 0;
					empty($_POST['withdraw'][$key]) && $_POST['withdraw'][$key] = 0;
					empty($_POST['deposit'][$key]) && $_POST['deposit'][$key] = 0;
					empty($_POST['level'][$key]) && $_POST['level'][$key] = 0;
					empty($_POST['bonus'][$key]) && $_POST['bonus'][$key] = 0;
				}
				
				$level = intval($_POST['level'][$key]);
				if($level<0)$level = 0;
				elseif($level>3)$level = 3;
				
				$config[] = array(
					'name' => $_POST['name'][$key],
					'orders' => $_POST['orders'][$key],
					'agent1' => $_POST['agent1'][$key],
					'agent2' => $_POST['agent2'][$key],
					'agent3' => $_POST['agent3'][$key],
					'consume' => $_POST['consume'][$key],
					'sales' => $_POST['sales'][$key],
					'touzibi' => $_POST['touzibi'][$key],
					'dist' => $_POST['dist'][$key],
					'withdraw' => $_POST['withdraw'][$key],
					'deposit' => $_POST['deposit'][$key],
					'level' => $level,
					'bonus' => $_POST['bonus'][$key]
				);
			}
			
			unset($_POST);
			$_POST = $config;
			
			$this -> _save();
		}
		//print_r($this -> _level);
		$this -> display();
	}
	
	// 充值
	public function charge(){
		if(IS_POST){
			$config = array();
			foreach($_POST['money'] as $key => $val){
				if(empty($val)){
					continue;
				}else{
					// 条件
					empty($_POST['send'][$key]) && $_POST['send'][$key] = 0;
					empty($_POST['ishot'][$key]) && $_POST['ishot'][$key] = 0;
				}
				
				$config[$key+1] = array(
					'money' => $_POST['money'][$key],
					'send' => $_POST['send'][$key],
					'ishot' => $_POST['ishot'][$key],
				);
			}
			
			unset($_POST);
			$_POST = $config;
			$this -> _save();
		}
		$this -> display();
	}
	
	// 轮播图设置
	public function banner(){
		if(IS_POST){
			$_POST['config'] = array();
			foreach($_POST['pic'] as $key => $val){
				$_POST['config'][] = array('pic' => $_POST['pic'][$key], 'url' => $_POST['url'][$key]);
			}
			unset($_POST['pic']);
			unset($_POST['url']);
		}
		$this -> _save();
		$this -> display();
	}
	
	// 轮播图设置
	public function xbanner(){
		if(IS_POST){
			$_POST['config'] = array();
			foreach($_POST['pic'] as $key => $val){
				$_POST['config'][] = array('pic' => $_POST['pic'][$key], 'url' => $_POST['url'][$key]);
			}
			unset($_POST['pic']);
			unset($_POST['url']);
		}
		$this -> _save();
		$this -> display();
	}
	
	// 轮播图设置
	public function ybanner(){
		if(IS_POST){
			$_POST['config'] = array();
			foreach($_POST['pic'] as $key => $val){
				$_POST['config'][] = array('pic' => $_POST['pic'][$key], 'url' => $_POST['url'][$key]);
			}
			unset($_POST['pic']);
			unset($_POST['url']);
		}
		$this -> _save();
		$this -> display();
	}
	
	
	// 模板设置
	public function tpl(){
		$action = empty($_GET['action']) ? 'index' : $_GET['action'];
		if(IS_POST){
			$tpl = $_POST[$action];
			$_POST = $this -> _tpl;
			$_POST[$action] = $tpl;
			$this -> _save();
		}
		
		// 读取模板
		$tpl_path = APP_PATH . 'Home/View/Index/'.$action;
		$dir_list = scandir($tpl_path);
		foreach($dir_list as $k => $v){
			if(in_array($v,array('.','..'))  || !is_dir($tpl_path.'/'.$v)){
				unset($dir_list[$k]);
			}
		}
		$this -> assign('dir_list', $dir_list);
		$this -> assign('action', $action);
		$this -> assign('tpl_path',$tpl_path);
		$this -> display();
	}
	
	// 自定义样式
	public function css(){
		$css_file = '.'.__ROOT__ . '/Public/css/user.css';
		if(IS_POST){
			file_put_contents($css_file, $_POST['content']);
			$this -> success('操作成功！');
			exit;
		}
		
		$css_content = file_get_contents($css_file);
		$this -> assign('content', $css_content);
		$this -> display();		
	}
	
	
	// 模板消息设置 => 初始化
	public function tplmsg(){
		$tplmsg = new \Common\Util\tplmsg();
		if(IS_POST){
			if($_POST['act'] == 'init'){
				$_POST = $tplmsg -> init();
				$this -> _save(false);
				if($_POST){
					$this -> success('成功获取了' .count($_POST). '个模板ID');
				}
				else{
					$this -> error(implode("\r\n",$tplmsg -> errmsg));
				}
				exit;
			}
			elseif($_POST['act'] == 'switch'){
				$GLOBALS['_CFG']['tplmsg'][$_POST['id']]['status'] = $_POST['status'] == 1 ? 1: 0;
				$_POST = $GLOBALS['_CFG']['tplmsg'];
				$this -> _save();
				$this -> success('操作成功！');
				exit;
			}
			
		}
		$this -> display();
	}

   
	
	private function _save($exit = true){
		// 通用配置保存操作
		if(IS_POST){
			// 有此配置则更新,没有则新增
			if(array_key_exists(ACTION_NAME, $this -> _CFG)){
				M('config') -> where(array('name' => ACTION_NAME)) -> save(array(
					'value' => serialize($_POST)
				));
			}else{
				M('config') -> add(array(
					'name' => ACTION_NAME,
					'value' => serialize($_POST)
				));
			}
			if($exit){
				$this -> success('操作成功！');
				exit;
			}
		}
	}
	
	
	
}?>