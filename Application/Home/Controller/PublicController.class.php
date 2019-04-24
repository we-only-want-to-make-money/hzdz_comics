<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
	public function _initialize(){
		// 加载配置
		$config = M('config') -> select();
		if(!is_array($config)){
			die('请先在后台设置好各参数');
		}
		foreach($config as $v){
			$key = '_'.$v['name'];
			$this -> $key = unserialize($v['value']);
			$_CFG[$v['name']] = $this -> $key;
		}
		$this -> assign('_CFG', $_CFG);
		$GLOBALS['_CFG'] = $_CFG;
	}
	
	public function regist(){
		if(IS_POST){
			$post = I('post.');
			$find = M('member')->where(array('mobile'=>$post['mobile']))->find();
			if($find){
				$this->error('该手机号已被注册！');
			}
			//判断验证码
			if(session('code.value') != $post['code']){
				$this->error('短信验证码错误！');
			}
			
			$post['salt'] = Salt();
			$post['imei'] = xmd5($post['salt']);
			$post['create_time'] = NOW_TIME;
			
			$qrcode = $this->qrcode($post['imei']);
			$post['url'] = $qrcode['url'];
			$post['qrcode'] = $qrcode['qrcode'];
			$post['password'] = xmd5($post['tpassword']);
			$id = M('member')->add($post);
			if($id){
				$this->success("注册成功，请登录PC代理端后台进行登录！");
			}else{
				$this->error("注册失败!");
			}
			exit;
		}
		$this->display();
	}
	
	//生成二维码
	public function qrcode($imei){
		//获取推广码信息

		$path_info = getAgentQrcode($imei);		
		$url = "http://".$_SERVER['HTTP_HOST'].__ROOT__."/index.php?imei=".$imei;
		if(!is_file($path_info['qrcode'])){
			include COMMON_PATH.'Util/phpqrcode/phpqrcode.php';
			// 目录不存在则创建
			if(!is_dir($path_info['path'])){
				mkdir($path_info['path'], 0777,1);
			}
			$errorCorrectionLevel = 'L';
			$matrixPointSize = 6;
			\QRcode::png($url, $path_info['qrcode'], $errorCorrectionLevel, $matrixPointSize, 2);	
		}
		return array(
			'url'=>$url,
			'qrcode'=>$path_info['qrcode'],
		);
	}
	
	//发送短信验证码
	public function SendSms(){
		if(IS_POST){
			$mobile = I('post.mobile');
			$code = rand(100000,999999);
			session('code',array('value'=>$code,'expire'=>1800));
			$content = '您的短信码为：'.$code.',请在三十分钟内及时认证!';
			$return = sms($mobile,$content);
			if($return!=1){
				$this->error($return);
			}else{
				$this->success('发送成功,请注意手机查收！');
			}
		}else{
			$this->error('非法请求');
		}
	}
	
	public function test(){
		sms("18679176380","测试短信发送");
	}
	
	// 阅读文章
	public function read(){
		$id = intval($_GET['id']);
		$info = M('arclist')-> find($id);
		$this -> assign('info', $info);
		$this -> display();		
	}
	
}