<?php
namespace Common\Util;
/**
*	微信支付操作类
*	@author Dragondean
*	@date 2015-05-14
*/
class wxjspay{
	
	public function _construct($param = null){
		if(is_array($param)){
			foreach($param as $k => $v){
				$this -> set_param($k, $v);
			}
		}
	}
	
	/**
	* 获得jsapi参数
	*/
	public function get_jsApi_parameters(){
		$time = NOW_TIME;
		$data = array(
			'appId' => $this -> values['appid'],
			'timeStamp' => "$time", // 时间戳为整数类型的在ios会出错
			'nonceStr' => $this -> make_nocestr(),
			'package' => 'prepay_id='.$this -> prepay_id,
			'signType' => 'MD5'
		);
		$data['paySign'] = $this -> make_sign($data);
		return json_encode($data);
	}
	
	/**
	* 统一下单接口
	*/
	public function unifiedOrder(){
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		$param = array(
			'appid' 			=> $this -> values['appid'],
			'mch_id' 			=> $this -> values['mch_id'],
			'nonce_str' 		=> $this -> make_nocestr(),
			'body' 				=> $this -> values['body'],
			'out_trade_no' 		=> $this -> values['out_trade_no'],
			'total_fee' 		=> $this -> values['total_fee'],
			'spbill_create_ip'	=> $_SERVER['REMOTE_ADDR'],
			'notify_url' 		=> $this -> values['notify_url'],
			'trade_type' 		=> 'JSAPI',
			'openid' 			=> $this -> values['openid']
		);
		
		// 可选参数,请求参数含义请参考https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
		$option = array(
			'device_info',
			'detail',
			'attach',
			'fee_type',
			'time_start',
			'time_expire',
			'goods_tag',
			'product_id',
			'limit_pay',
		);
		foreach($option as $v){
			if(!empty($this -> values[$v]))
				$param[$v] = $this -> values[$v];
		}
		$param['sign'] = $this -> make_sign($param);
		$xml = $this -> arr2xml($param);
		$rt = $this -> http_post($url, $xml);
		
		$rt_arr = $this -> xml2arr($rt);
		if($rt_arr['result_code'] == 'SUCCESS' && $rt_arr['return_code'] == 'SUCCESS'){
			$this -> prepay_id = $rt_arr['prepay_id'];
			return $rt_arr['prepay_id'];
		}else{
			$this -> uoerror = "统一下单接口调用失败";
			return false;
		}
	}
	
	/**
	*	获取通知数据
	*/
	public function get_notify_data(){
		if(!IS_POST)return;
		$xml = file_get_contents("php://input");
		$this -> notify_data = $this -> xml2arr($xml);
		return $this -> notify_data;
	}
	
	/**
	* 验证通知的签名
	*/
	public function check_sign(){
		$data = $this -> notify_data;
		unset($data['sign']); // 签名值不参与签名
		$sign = $this -> make_sign($data);
		if(!$sign){
			$sign ='sign签名错误';
		}
		if($sign == $this -> notify_data['sign'])
			return true;
		else return false;
	}
	
	/**
	*	设置参数
	*	@param string $name 参数名
	*	@param mixed $value 值
	*/
	public function set_param($name, $value){
		if(is_array($name)){
			foreach($name as $key => $val){
				$this -> set_param($key, $val);
			}
		}else
			$this->values[$name] = $value;
	}
	
	
	/**
	* 生成随机字符串
	*/
	public function make_nocestr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
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
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function make_sign($values, $key = null)
	{
		if(empty($key))
			$key = $this -> values['key'];
		//签名步骤一：按字典序排序参数
		ksort($values);
		//$string = $this->ToUrlParams();
		$string =  urldecode(http_build_query($values));
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".$key;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	
	/**
	*	GET方式抓取数据
	*	@param string $url 要抓取的URL
	*/
	private function http_get($url, $data = null){
		$this->url = $url;
		$data && $this->data = $data;
		$this->method = "GET";
		return $this->excrequest();
	}
	
	/**
	*	POST提交数据并返回内容
	*	@param string $url 要请求的地址
	*	@param mixed $data 提交的数据
	*/
	private function http_post($url, $data = null){
		$this->url = $url;
		$this->method = "POST";
		$this->data = $data;
		return $this->excrequest();
	}
	
	/**
	*	执行请求并返回数据
	*	@access private 
	*/
	private function excrequest(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		if($this->sslcert){
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $this -> values['sslcert']);
		}
		if($this->sslkey){
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $this -> values['sslkey']);
		}
		//echo $this->data;
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno = curl_errno($ch);
		if(!$errorno)return $tmpInfo;
		else{
			$this->errmsg = $errorno;
			return false;
		}
	}
}
?>