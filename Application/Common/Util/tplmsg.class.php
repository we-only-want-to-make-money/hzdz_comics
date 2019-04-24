<?php
namespace Common\Util;
/**
*	微信类，集成微信常用功能,目前只支持明文模式
*	@author DragonDean
*	@url	http://www.dragondean.cn
*/
class tplmsg{

	private $access_token;
	private $ddwechat;
	public $errmsg;
	
	/**
	*	构造函数，初始化参数
	*	@param array $param 数组格式的参数
	*/
	public function __construct(){
		
		$this -> ddwechat = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);
		//$this -> ddwechat -> setParam($GLOBALS['_CFG']['mp']);
		$this -> access_token = $this -> ddwechat -> getaccesstoken();
	}
	
	// 初始化，从官方获得模板id。执行之前
	public function init(){
		$tpls = array(
			'OPENTM207008612' => '升级成功通知',
		);
		
		$config = array();
		foreach($tpls as $key => $val){
			$id = $this -> ddwechat -> gettplid($key,$this -> access_token);
			if($id){
				$config[$key] = array(
					'id' => $id,
					'status' => 1
				);
			}
			else{
				$this -> errmsg[] = $this -> ddwechat -> errmsg;
			}
		}
		return $config;
	}
	
	// 升级成功通知
	public function goup($openid, $info){
		if(empty($GLOBALS['_CFG']['tplmsg']['OPENTM207008612']['id']) || $GLOBALS['_CFG']['tplmsg']['OPENTM207008612']['status'] != 1){
			return false;
		}
		
		$data  = array(
			'touser' => $openid,
			'template_id' => $GLOBALS['_CFG']['tplmsg']['OPENTM207008612']['id'],
			'url' => $info['url'],
			'data' => array(
				'first' => array(
					'value' => $info['title'],
					'color' => '#ea4a17',
				),
				// 代理姓名
				'keyword1' => array(
					'value' => $info['name'],
					'color' => '#666666',
				),
				// 代理微信				
				'keyword2' => array(
					'value' => $info['mobile'],
					'color' => '#666666',
				),
				// 代理手机				
				'keyword3' => array(
					'value' => $info['mobile'],
					'color' => '#666666',
				),
				'remark' => array(
					'value' =>	$info['remark'],
					'color' => '#9E9B9B',
				),
			)
		);
		$rt = $this -> ddwechat -> tplmsg($data, $this -> access_token);
		if(APP_DEBUG && !$rt){
			//var_dump($this -> ddwechat -> errmsg);
		}
	}
	
	
	
}
?>