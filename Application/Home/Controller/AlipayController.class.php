<?php
namespace Home\Controller;
use Think\Controller;
class AlipayController extends Controller {
      //在类初始化方法中，引入相关类库    
       public function _initialize() {
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit');
		
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
		$GLOBALS['_CFG'] = $_CFG;
	
    }
	
	//支付宝支付
	public function ali_pay(){
		header("Content-type:text/html;charset=utf-8");
		$order = I('get.order');
		$table = I('get.table');
		if(!is_array($order)){
			$order = M($table)->find(intval($order));
		}
		$user = M('user')->find(intval($order['user_id']));
		
        if(empty($order)) {
			echo "<script>alert('订单未找到！');</script>";
			exit;
		}
		
		$create_time = time();
		$time_start = date("YmdHis", $create_time); //交易起始时间
		$time_expire = date("YmdHis", $create_time + 7200); //交易结束时间  订单号有效期2小时
		
		$pay_array = array(
			'out_trade_no' => $order['sn'], 
			'total' => $order['money'], 
			'order_id' => $order['id'], 
			'time_start'=>$time_start,
			'time_expire'=>$time_expire,
			'body'	=> $this->_site['name'] . '在线支付',
			'ordbody'	=> '支付',
			'ordshow_url'	=> U('Goods/index'),
			'type' =>$table,
		); 
		//调用支付接口
		$this->assign('pay_array',$pay_array);
		$this->display();
	}
	
    
    //doalipay方法
    public function doalipay(){
        header("Content-type:text/html;charset=utf-8");
        /**************************请求参数**************************/
        $payment_type = "1"; //支付类型 //必填，不能修改
        $notify_url = C('alipay.notify_url'); //服务器异步通知页面路径
        $return_url = C('alipay.return_url'); //页面跳转同步通知页面路径
        $seller_email = C('alipay.seller_email');//卖家支付宝帐户必填
        $out_trade_no = $_POST['trade_no'];//商户订单号 通过支付页面的表单进行传递，注意要唯一！
        $subject = $_POST['ordsubject'];  //订单名称 //必填 通过支付页面的表单进行传递
        $total_fee = $_POST['ordtotal_fee'];   //付款金额  //必填 通过支付页面的表单进行传递
        $body = $_POST['type'];  //订单描述 通过支付页面的表单进行传递
        $show_url = $_POST['ordshow_url'];  //商品展示地址 通过支付页面的表单进行传递
        $anti_phishing_key = "";//防钓鱼时间戳 //若要使用请调用类文件submit中的query_timestamp函数
        $exter_invoke_ip = get_client_ip(); //客户端的IP地址 
		$is_mobile = $_POST['is_mobile']; //是否手机访问 1手机 2PC
		
		$type = $_POST['type'];
        /************************************************************/
    
        //构造要请求的参数数组，无需改动
		if(1 == $is_mobile) {
			$alipay_config=C('alipay_config_mobile');
			$parameter = array(
				"service"       => $alipay_config['service'],
				"partner"       => $alipay_config['partner'],
				"seller_id"  	=> $alipay_config['seller_id'],
				"payment_type"	=> $alipay_config['payment_type'],
				"notify_url"	=> $alipay_config['notify_url'],
				"return_url"	=> $alipay_config['return_url'],
				"_input_charset"=> trim(strtolower($alipay_config['input_charset'])),
				"out_trade_no"	=> $out_trade_no,
				"subject"		=> $subject,
				"total_fee"		=> $total_fee,
				"show_url"		=> $show_url,
				//"app_pay"		=> "Y",//启用此参数能唤起钱包APP支付宝
				"body"			=> $body,
			);

			//建立请求
			$alipaySubmit = new \Vendor\Alipay\AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
			echo $html_text;
		} 	
    }
    
	
	/******************************
		服务器异步通知页面方
	*******************************/
    public function notifyurl(){
        $alipay_config=C('alipay_config');
        //计算得出通知验证结果
        $alipayNotify = new \Vendor\Alipay\AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
		file_put_contents('a.txt','走了11111',FILE_APPEND);
        if($verify_result) {
           //验证成功 
           $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
           $trade_no       = $_POST['trade_no'];          //支付宝交易号
           $trade_status   = $_POST['trade_status'];      //交易状态
           $total_fee      = $_POST['total_fee'];         //交易金额
           $notify_id      = $_POST['notify_id'];         //通知校验ID。
           $notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
           $buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
           $parameter = array(
             "out_trade_no"  => $out_trade_no, //商户订单编号；
             "trade_no"      => $trade_no,     //支付宝交易号；
             "total_fee"     => $total_fee,    //交易金额；
             "trade_status"  => $trade_status, //交易状态
             "notify_id"     => $notify_id,    //通知校验ID。
             "notify_time"   => $notify_time,  //通知的发送时间。
             "buyer_email"   => $buyer_email,  //买家支付宝帐号；
           );
		   
		   file_put_contents('a.txt',var_export($_POST),FILE_APPEND);
		   
           if ($_POST['trade_status'] == 'TRADE_SUCCESS' || $_POST['trade_status'] == 'TRADE_FINISHED') {
			   $table = $_POST['type'];
			   file_put_contents('a.txt',$table.'11111' ,FILE_APPEND);
			   if(!checkorderstatus($out_trade_no)){
					//进行订单处理，并传送从支付宝返回的参数；
					$order_info = M($table)->where("sn='{$out_trade_no}'")->find(); 
					$order_id = $order_info['id'];
					
					$this->user = M('user')->where(array('id'=>$order_info['user_id']))->find();
					if(!$order_info){
						die('FAIL');
					}
					
					if($order_info['status'] !=1){
						die('FAIL');
					}
					$paid_fee = $order_info['alipay'] + $data['total_fee'];
					
					// 已支付全部费用
					if($paid_fee >= $order_info['money']){
						
						file_put_contents('a.txt',$paid_fee.'MONEY',FILE_APPEND);
						
						$save['status'] = 2;
						$save['pay_time'] = NOW_TIME;
						if($table == 'order'){
							M('separate_log') -> where(array('order_id'=>$order_id)) -> setField('status', 2);
							//更新用户销售业绩+等级+分拥+购买额度
							upUser($order_info);
						}
						
						//如果是预存信息
						if($table =='agent'){
							//给用户充值金额和变更代理等级
							$userData['money'] = array('exp', 'money+'.$paid_fee);
							$save['pay_type'] = 2;
							if(!$this->user['join_lv_time']){
								$userData['join_lv_time'] = time();
							}
							if($this->user['lv']<$order_info['lv']){
								$userData['lv'] = $order_info['lv'];
							}
							M('user')->where(array('id'=>$this->user['id']))->save($userData);
							flog($this -> user['id'],'money',$data['total_fee']/100, 1); // 记录财务日志
							
						}
						
					}
					M($table) -> where('id='.$order_id) -> save($save);;
               } else {
				  
			   }
            }
                echo "success";        //请不要修改或删除
         }else {
			//验证失败
			echo "fail";
        }    
    }
	
    
    /*
     页面跳转处理方法；
    */
    public function returnurl(){
		header("Content-type:text/html;charset=utf-8");
        $alipay_config= C('alipay_config_mobile');
        $alipayNotify = new \Vendor\Alipay\AlipayNotify($alipay_config);//计算得出通知验证结果
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {
            //验证成功
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			$out_trade_no   = $_GET['out_trade_no'];      //商户订单号
			$trade_no       = $_GET['trade_no'];          //支付宝交易号
			$trade_status   = $_GET['trade_status'];      //交易状态
			$total_fee      = $_GET['total_fee'];         //交易金额
			$notify_id      = $_GET['notify_id'];         //通知校验ID。
			$notify_time    = $_GET['notify_time'];       //通知的发送时间。
			$buyer_email    = $_GET['buyer_email'];       //买家支付宝帐号；
				
			$data = array(
				"out_trade_no"     => $out_trade_no,      //商户订单编号；
				"trade_no"     => $trade_no,          //支付宝交易号；
				"total_fee"      => $total_fee,         //交易金额；
				"trade_status"     => $trade_status,      //交易状态
				"notify_id"      => $notify_id,         //通知校验ID。
				"notify_time"    => $notify_time,       //通知的发送时间。
				"buyer_email"    => $buyer_email,       //买家支付宝帐号
			);

			
			//区分每个表的订单
			$table = $_GET['body'];
			//file_put_contents('a.txt',$table.'222',FILE_APPEND);
			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
					//echo '支付宝支付完成';
					//进行订单处理，并传送从支付宝返回的参数；
					$order_info = M($table)->where("sn='{$out_trade_no}'")->find(); 
					$order_id = $order_info['id'];
					
					$this->user = M('user')->where(array('id'=>$order_info['user_id']))->find();
					if(!$order_info){
						die('FAIL');
					}
					
					if($order_info['status'] !=1){
						die('FAIL');
					}
					$paid_fee = $order_info['alipay'] + $data['total_fee'];
					
					// 已支付全部费用
					if($paid_fee >= $order_info['money']){
						
						file_put_contents('a.txt',$paid_fee.'MONEY',FILE_APPEND);
						
						$save['status'] = 2;
						$save['pay_time'] = NOW_TIME;
						if($table == 'order'){
							M('separate_log') -> where(array('order_id'=>$order_id)) -> setField('status', 2);
							//更新用户销售业绩+等级+分拥+购买额度
							upUser($order_info);
						}
						
						//如果是预存信息
						if($table =='agent'){
							//给用户充值金额和变更代理等级
							$userData['money'] = array('exp', 'money+'.$paid_fee);
							$save['pay_type'] = 2;
							if(!$this->user['join_lv_time']){
								$userData['join_lv_time'] = time();
							}
							if($this->user['lv']<$order_info['lv']){
								$userData['lv'] = $order_info['lv'];
							}
							M('user')->where(array('id'=>$this->user['id']))->save($userData);
							flog($this -> user['id'],'money',$data['total_fee']/100, 1); // 记录财务日志
							
						}
						
					}
					M($table) -> where('id='.$order_id) -> save($save);
			}else {
				echo "trade_status=".$_GET['trade_status'];
				echo "支付失败!";//跳转到配置项中配置的支付失败页面；
			}
			echo "success";        //请不要修改或删除
		}else {
			echo "支付失败！";	
		}
	}
	
}?>