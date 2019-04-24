<?php
namespace Common\Util;
/**
*	微信类，集成微信常用功能,目前只支持明文模式
*	@author DragonDean
*	@url	http://www.dragondean.cn
*/
class ddwechat{
	public $appid;
	public $appsecret;
	public $token;
	public $accesstoken;
	protected $xmlStr;	//xml大小写对应配置
	protected $http;	//http操作对象
	public $data;	//微信服务器推送来的数据
	public $errmsg; //错误信息
	
	
	/**
	*	构造函数，通过数组初始化参数
	*	@param array $param 数组格式的参数
	*/
	public function __construct($param = null){
		if(is_array( $param)){
			foreach($param as $key => $val){
				$this->setParam($key, $val);
			}
		}
	}
	public function ddwechat($param){
		$this->__construct($param);
	}

	/**
	*	设置参数
	*	@param string $name 参数名
	*	@param mixed $value 值
	*/
	public function setParam($name, $value){
		if(is_array( $name)){
			foreach($name as $key => $val){
				$this->setParam($key, $val);
			}
		}else{
			$this->$name = $value;
		}
	}
	
	/**
	*	API接口验证
	*/
	public function validate(){
		if(isset($_GET['echostr'])){
			echo $_GET['echostr'];
			die();
		}
	}
	
	/**
	*	检查签名
	*	@return bool 
	*/
	private function checkSignature(){
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			 return true;
		}else{
			$this->errmsg = "签名验证失败！";
			 return false;
		}
    }
	
	/**
	*	获取微信推送来的xml数据并保存到data中
	*/
	public function request(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			$this->errmsg = "请求方式不对！";
			return false;
		}
		$xml = file_get_contents("php://input");
		$this->data = $this->xml2arr($xml);
		return $this->data;
	}

	
	/**
	*	回复被动消息
	*	@param array $msg 回复的消息内容数组
	*/
	public function response($data, $type = 'text'){
		$msg = array('msgtype' => $type);
		$msg['tousername'] = $this -> data['fromusername'];
		$msg['fromusername'] = $this -> data['tousername'];
		$msg['createtime'] = time();
		
		// 回复文本消息
		if($type == 'text'){
			if(is_array($data)){
				$msg['content'] = empty($data['content']) ? '未设置内容' : $data['content'];
			}else{
				$msg['content'] = $data;
			}
		}
		
		// 回复图文消息
		elseif($type == 'news'){
			$msg['articlecount'] = count($data['articles']);
			$i = 0;
			foreach($data['articles'] as $v){
				$msg['articles']['item['.$i.']'] = $v;
				$i++;
			}
			//print_r($msg);
		}
		echo $this->arr2xml($this->walkxmlvar($msg));
	}
	
	/**
	*	根据appid和appsecret获取accesstoken
	*	@param string $appid 
	*	@param string $appsecret
	*/
	public function getaccesstoken($appid = null, $appsecret = null){
		$appid || $appid = $this->appid;
		$appsecret || $appsecret = $this->appsecret;
		
		// 尝试从缓存读取
		$cache = S($appid.'_accesstoken');
		if($cache && $cache['expire'] > time() && !empty($cache['accesstoken'])){
			$this->accesstoken = $cache['accesstoken'];
			return $cache['accesstoken'];
		}
		
		// 缓存没有或者过期增从服务器获取
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
		$temp = $this->exechttp($url);
		if($temp && !isset($temp['errcode'])){
			$this->accesstoken = $temp['access_token'];
			S($appid.'_accesstoken', array('accesstoken' => $temp['access_token'], 'expire' => time() + 7000));
			return $this -> accesstoken;
		}
		return false;
	}
	
	/**
	*	获取微信服务器IP地址
	*	@param string $accesstoken 可选
	*/
	public function getwechatip($accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accesstoken ;
		return $this->exechttp($url);
	}
	
	/**
	*	添加/修改/删除客服账号,每个公众号最多添加10个客服账号
	*	@param  string $kfaccount 客服账号
	*	@param  string $nickname	客服昵称,最长6个汉字或12个英文字符
	*	@param  string $password	密码
	*	@param  string $action 		操作，默认是添加(add)，可选修改(update)或者删除(del)
	*	@param  string $accesstoken	可选参数
	*/
	public function kfaccount($kfaccount, $nickname, $password, $action = 'add' , $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$data = array( 'kf_account' => $kfaccount, 'nickname' => $nickname, 'password' => $password );
		$data = $this->jsonencode($data);
		$url = "https://api.weixin.qq.com/customservice/kfaccount/$action?access_token=".$accesstoken;
		return $this->exechttp($url, 'post', $data);
	}
	
	/**
	*	设置客服头像
	*/
	public function uploadkfhead(){
		//TODO
		return false;
	}
	
	/**
	*	获取所有客服账号
	*	@param string $accesstoken 可选参数
	*/
	public function getkflist($accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=".$accesstoken;
		return $this->exechttp($url);
	}
	
	/**
	*	发送客服消息
	*	@param array $msg 消息体数组
	*	@param string $accesstoken  可选参数
	*/
	public function custommsg($msg, $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$accesstoken;
		$rt= $this->exechttp($url, 'post', $this->jsonencode($msg));
		return $rt;
	}
	
	// 发送客服提示消息
	function send_msg($openid,$data,$type="text"){
		$dd = new \Common\Util\ddwechat($GLOBALS['_CFG']['mp']);
		$accesstoken = $dd -> getaccesstoken();
		if($type == "image"){
			$msg = array(
				'touser' => $openid,
				'msgtype' => 'image',
				'image' => array(
					'media_id' => $data
				)
			);
		}elseif($type == "text"){
			$msg = array(
				'touser' => $openid,
				'msgtype' => 'text',
				'text' => array(
					'content' => $data
				)
			);
		}elseif($type == "news"){
			$msg = array(
				'touser' => $openid,
				'msgtype' => 'news',
				'news' => array('articles'=>$data),
			);
		}
		$dd -> custommsg($msg);
	}
	
	/**
	*	创建分组
	*	一个公众账号，最多支持创建100个分组。
	*	@param string $name 分组名
	*	@param string $accesstoken 凭证，可选参数
	*/
	public function addgroup($name, $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=" . $accesstoken;
		$data = array( 'group' => array( 'name' => $name ));
		return $this->exechttp($url , 'post', $this->jsonencode($data));
	}
	
	/**
	*	查询所有分组
	*	@param string $accesstoken 可选参数
	*/
	public function listgroup($accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=" . $accesstoken;
		return $this->exechttp($url);
	}
	
	/**
	*	查询用户所在分组
	*	@param string $openid 用户的openid
	*	@param string $accesstoken 可选参数 
	*/
	public function getusergroupid($openid , $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=" . $accesstoken;
		$data = array( 'openid' => $openid );
		return $this->exechttp($url, 'post', $this->jsonencode($data));
	}
	
	/**
	*	修改分组名
	*	@param int $groupid 分组id，由微信分配
	*	@param string $name 分组名，30个字符以内
	*	@param string $accesstoken 可选参数
	*/
	public function updategroup($groupid, $name, $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token=" . $accesstoken;
		$data = array( 'group' => array( 'id' => $groupid, 'name' => $name ));
		return $this->exechttp($url , 'post', $this->jsonencode($data));		
	}
	
	/**
	*	移动用户分组
	*	@param string $openid 用户的openid
	*	@param int $togroupid 目标分组id
	*	@param string $accesstoken
	*/
	public function movetogroup($openid, $togroupid, $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=" . $accesstoken;
		$data = array('openid'=>$openid , 'to_groupid'=>$togroupid);
		return $this->exechttp($url , 'post', $this->jsonencode($data));
	}
	
	/**
	*	批量移动用户分组
	*	@param array $openidlist 要移动的用户openid数组
	*	@param int $togroupid 目标分组id
	*	@param string $accesstoken 可选参数
	*/
	public function movetogroupbatch($openidlist, $togroupid,  $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token=" . $accesstoken;
		$data = array('openid_list'=>$openidlist, 'to_groupid'=>$togroupid);
		return $this->exechttp($url , 'post', $this->jsonencode($data));
	}
	
	/**
	*	删除分组
	*	删除分组后，所有该分组内的用户自动进入默认分组
	*	@param int $groupid 要删除的分组id
	*	@param string $accesstoken 可选参数
	*/
	public function delgroup($groupid, $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/groups/delete?access_token=" . $accesstoken;
		$data = array( 'group' => array( 'id' => $groupid));
		return $this->exechttp($url , 'post', $this->jsonencode($data));
	}
	
	/**
	*	设置备注名
	*	@param string $openid 
	*	@param string $remark 备注名，少于30个字符
	*	@param string $accesstoken 可选参数
	*/
	public function updateremark($openid , $remark, $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=" . $accesstoken;
		$data = array( 'openid' => $openid, 'remark' => $remark);
		return $this->exechttp($url , 'post', $this->jsonencode($data));
	}
	
	/**
	*	获取用户基本信息（包括UnionID机制）
	*	@param string $openid
	*	@param string $accesstoken
	*/
	public function getuserinfo($openid, $accesstoken = null){
		$accesstoken || $accesstoken = $this->getaccesstoken();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=".$openid."&lang=zh_CN";
		return $this->exechttp($url);
	}
	
	
	/**
	*	获取用户列表
	*	@param string $nextopenid 第一个拉取的openid
	*	@param string $accesstoken
	*/
	public function getuserlist($nextopenid = null , $accesstoken = null){
		$accesstoken || $accesstoken = $this->accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" .$accesstoken. "&next_openid=".$nextopenid;
		return $this->exechttp($url);
	}
	
	/**
	*	发放红包接口 
	*	红包接口要求使用ssl和证书
	*	@param array $param 红包数据数组
	*/
	public function redpack($param, $ssl){
		$url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
		$xml = $this->arr2xml($param);
		$rt = $this->exechttp($url, 'post', $xml, true, $ssl);
		return $this->xml2arr($rt);
	}
	
	/**
	* 模板消息接口 
	*/
	public function tplmsg($param, $accesstoken = null){
		$accesstoken || $accesstoken = $this->getaccesstoken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accesstoken;
		return $this->exechttp($url, 'post', $this -> jsonencode($param));
	}
	
	/**
	*	企业付款
	*/
	public function mch_pay($param, $ssl){
		$param['nonce_str'] = $this -> getnoncestr();
		$param['spbill_create_ip'] = empty($_SERVER['SERVER_ADDR']) ? $s= $_SERVER['LOCAL_ADDR'] : $_SERVER['SERVER_ADDR'];
		$param['sign'] = $this -> getsign($param);
		$url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
		$xml = $this->arr2xml($param);
		$rt = $this->exechttp($url, 'post', $xml, true, $ssl);		
		return $this->xml2arr($rt);
	}
	
	/**
	* 申请退款
	*/
	public function refund($param,$ssl){
		$param['nonce_str'] = $this -> getnoncestr();
		$param['sign'] = $this -> getsign($param);
		$url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
		$xml = $this->arr2xml($param);
		$rt = $this->exechttp($url, 'post', $xml, true, $ssl);		
		return $this->xml2arr($rt);
	}
	
	/**
	* 获得JSSDK凭据
	*/
	public function getjsapiticket(){
		$data = S($this->appid.'_jsapi_ticket');
		if ($data['expire_time'] < time()) {
		  $accesstoken = $this->getaccesstoken();
		  // 如果是企业号用以下 URL 获取 ticket
		  // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accesstoken";
		  $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accesstoken";
		  $res = $this -> exechttp($url);//json_decode($this->httpGet($url));
		  $ticket = $res['ticket'];
		  if ($ticket) {
			$data['expire_time'] = time() + 7000;
			$data['jsapi_ticket'] = $ticket;
			S($this->appid.'_jsapi_ticket',$data);
		  }
		} else {
		  $ticket = $data['jsapi_ticket'];
		}

		return $ticket;
	}
	
	/**
	* 获得jssdk的参数
	*/
	public function getsignpackage() {
		$jsapiTicket = $this->getjsapiticket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->getnoncestr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
		  "appId"     => $this->appid,
		  "nonceStr"  => $nonceStr,
		  "timestamp" => $timestamp,
		  "url"       => $url,
		  "signature" => $signature,
		  "rawString" => $string
		);
		return $signPackage; 
	  }
	
	/**
	*	根据参数生成签名
	*	@param array $param 要参与签名的参数
	*	@param string $key 参与签名的支付密钥
	*	@return string  签名字符串
	*/
	public function getsign($param, $key = null){
		foreach ($param as $k => $v){
			$parameters[$k] = $v;
		}

		if(!$key) $key = $this -> key;
		
		//签名步骤一：按字典序排序参数
		ksort($parameters);
		$string =  urldecode(http_build_query($parameters));//$this->formatBizQueryParaMap($Parameters, false);
		//签名步骤二：在string后加入KEY
		$string = $string."&key=".$key;//<<<<<<<<<<<<=======
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	/**
	* 产生随机字符串，不长于32位
	* @param int $length
	* @return 产生的随机字符串
	*/
	public static function getnoncestr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}
	
	/**
	*	获取用户openid
	*	@param string $appid
	*	@param strng $appsecret
	*/
	public function getopenid($appid = null, $appsecret = null){
		$appid || $appid = $this->appid;
		$appsecret || $appsecret = $this->appsecret;
		if(!isset($_GET['code'])){
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($this->getcururl())."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
			header("location:$url");
			exit;
		}else{
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$_GET['code']."&grant_type=authorization_code";
			return $this->exechttp($url);
		}
	}
	
	/**
	*	获取当前的url
	*/
	public function getcururl(){
		
		return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	/**
	* 生成自定义菜单
	*/
	public function createmenu($data, $accesstoken){
		$accesstoken && $accesstoken = $this -> accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accesstoken;
		return $this -> exechttp($url, 'post', $data);
	}
	
	/**
	* 生成代餐二维码
	*/
	public function createqrcode($scene, $expire = null, $accesstoken){
		$accesstoken && $accesstoken = $this -> accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$accesstoken;
		// 有expires表示是临时二维码
		if($expire){
			if(is_numeric($scene)){
				$data = '{"expire_seconds": '.$expire.', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene.'}}}';
			}else{
				$data = '{"expire_seconds": '.$expire.', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_str": "'.$scene.'"}}}';
			}
		}else{
			if(is_numeric($scene)){
				$data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_id": '.$scene.'}}}';
			}else{
				$data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$scene.'"}}}';
			}
		}
		return $this -> exechttp($url, 'post', $data);
	}
	
	/**
	* 获取模板id
	*/
	public function gettplid($short_id, $accesstoken){
		$accesstoken && $accesstoken = $this -> accesstoken;
		$url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=" . $accesstoken;
		$data = json_encode(array(
			'template_id_short' => $short_id
		));
		$rt = $this -> exechttp($url, 'post', $data);
		if($rt){
			return $rt['template_id'];
		}
		else{
			return false;
		}
	}
	
	/**
	*	将数组转换为xml
	*	@param array $data	要转换的数组
	*	@param bool $root 	是否要根节点
	*	@return string 		xml字符串
	*	@link http://www.cnblogs.com/dragondean/p/php-array2xml.html
	*/
	private function arr2xml($data, $root = true){
		$str="";
		if($root)$str .= "<xml>";
		foreach($data as $key => $val){
			//去掉key中的下标[]
			$key = preg_replace('/\[\d*\]/', '', $key);
			if(is_array($val)){
				$child = $this->arr2xml($val, false);
				$str .= "<$key>$child</$key>";
			}else{
				$str.= "<$key><![CDATA[$val]]></$key>";
			}
		}
		if($root)$str .= "</xml>";
		return $str;
	}
	
	/**
	*	根据xmlStr.config.php的配置，将小写变量转为微信服务要求的首字母大写
	*	@param string $str	要转换的字符串
	*	@return string $rt	转换后的字符串，没有原样返回
	*/	
	private function getxmlvar($str){
		if(!is_array($this->xmlStr))$this->xmlStr = require "xmlStr.config.php";
		if( !empty($this->xmlStr[$str]))return $this->xmlStr[$str];
		else return $str;
	}
	
	/**
	*	递归将数组中的小写变量转为微信需要的首字母大写格式
	*	@param array 要转换的数组
	*	@return array 转换后的数组
	*/
	private function walkxmlvar($arr){
		if(!is_array($arr))return array();
		$newArr = array();
		foreach($arr as $key => $val){
			if(is_array($val)){
				$newArr[$this->getxmlvar($key)] = $this->walkxmlvar($val);
			}else {
				$newArr[$this->getxmlvar($key)] = $val;
			}
		}
		return $newArr;
	}
	
	/**
	*	返回http操作对象，避免多次include和new
	*/
	public function gethttp(){
		if(!$this->http){
			//include_once "ddhttp.class.php";
			$this->http = new \Common\Util\ddhttp;
		}
		return $this->http;
	}
	
	/**
	*	执行http请求数据并分析结果，默认是get方式，如果错误则返回false，否则返回json_decode后的数组
	*	@param string $url	要请求的数组
	*	@param string $method 请求方式get或者post
	*	@param mixed $data	数据
	*	@param bool $orig 是否原样返回数据
	*/
	public function exechttp($url, $method = 'get',$data = null , $orig = false, $ssl = null){
		$method = strtolower($method);
		$http = $this->gethttp();
		
		if(isset($ssl['sslkey'])) $http->sslkey = $ssl['sslkey'];
		if(isset($ssl['sslcert'])) $http->sslcert = $ssl['sslcert'];
		
		$temp = $http->$method($url, $data);

		if(!$temp){ //HTTP操作错误
			$this->errmsg = $http->errmsg;
			return false;
		}
		if($orig)return $temp;//原样返回数据
		$tempArr = json_decode($temp, 1);
		//print_r($tempArr);die('aaa');
		if(isset($tempArr['errcode']) && $tempArr['errcode'] != 0){
			$this->errmsg = $tempArr['errmsg']."(代码：".$tempArr['errcode'].")";
			return false;
		}else {
			return $tempArr;
		}
	}
	
	
	/**
	*	xml转为数组
	*	@param string $xml 原始的xml字符串
	*/
	public function xml2arr($xml){
		$xml = new \SimpleXMLElement($xml);
		if(!is_object($xml)){
			$this->errmsg = "xml数据接收错误";
			return false;
		}
		$arr = array();
		foreach ($xml as $key => $value) {
			$arr[strtolower($key)] = strval($value);
		}
		return $arr;
	}
	
	/**
	*	json_encode写法改进
	*	@param mixed $var 要encode的变量
	*/
	public function jsonencode($var){
		$var = $this -> decodeUnicode(json_encode($var));
		return $var;
	}
	
	/**
	*	将jsonencode 编码后的中文unicode还原成中文
	*/
	public function decodeUnicode($str){
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
			create_function(
				'$matches',
				'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
			),
			$str);
	}
	
}
?>