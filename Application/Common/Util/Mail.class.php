<?php
namespace Common\Util;
class Mail {
  public $to; // 收件人
  public $subject; // 主题
  public $body; // 邮件内容  
  public $errmsg; // 错误信息
  
  public function send(){
	$host = C('SMTP_HOST');
	$port = C('SMTP_PORT');
	$user = C('SMTP_USER');
	$pass = C('SMTP_PASS');
	$name = C('SMTP_NAME');
	
	// 检查配置
	if(empty($host) || empty($port) || empty($user) || empty($pass) || empty($name)){
		$this -> errmsg = 'SMTP邮局设置不完整！';
		return false;
	}
	  
	$mail = new \Common\Util\PHPMailer();
	$mail -> IsSMTP(); // 使用SMTP方式发送
	$mail -> CharSet='UTF-8'; // 设置邮件的字符编码
	$mail -> Host = $host;  // 您的企业邮局域名
	$mail -> Port = $port; // 端口
	$mail -> SMTPAuth = true; // 启用SMTP验证功能
	$mail -> Username = $user;  // 邮局用户名(请填写完整的email地址)
	$mail -> Password = $pass; // 邮局密码
	$mail -> From = $user; //邮件发送者email地址
	$mail -> FromName = $name;
	
	// 设置收件人,格式是AddAddress("收件人email","收件人姓名")
	if(!is_array($this -> to)){
		$this -> errmsg = '没有收件人';
		return false;
	}
	foreach($this -> to as $val){
		if(is_array($val))
			$mail -> AddAddress( $val[0], $val[1]);
		else
			$mail -> AddAddress($val);
	}
		
	$mail -> IsHTML(true);   //是否使用HTML格式
	$mail -> Subject = $this -> subject;  //邮件标题
	$mail -> Body = $this -> body; //邮件内容
	if(!$mail->Send())
	{
		$this -> errmsg = $mail->ErrorInfo;
		return false;
	}else{
		return true;
	}
  }
  
  // 添加收件人
  public function addTo($to){
	  $this -> to[] = $to;
  }
  
  // 设置主题
  public function setSubject($subject){
	$this -> subject = $subject;
  }
  
  // 设置邮件内容
  public function setBody($body){
	  $this -> body = $body;
  }
  
  // 发送注册邮件
  public function sendRegMail($arr){
		if( empty($arr['to']) ){
			$this -> errmsg = '发送注册邮件必须有接收用户';
			return false;
		}
		
		$this -> addTo($arr['to']);
		
		$this -> subject = C('MAIL_REG_TITLE');
		$this -> body = file_get_contents( C('MAIL_REG_BODY') );
		
		// 替换模板中的变量
		foreach($arr as $k => $v){
			$this -> body = str_replace('{'.$k.'}', $v, $this -> body);
		}
		
		return $this -> send();
  }
  
}

?>