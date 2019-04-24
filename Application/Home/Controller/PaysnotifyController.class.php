<?php
namespace Home\Controller;
use Think\Controller;
class PaysnotifyController extends Controller {
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
		$GLOBALS['_CFG'] = $_CFG;
	}
	
    
	// 充值支付通知异步页面
	public function index(){
		$paysapi_id = $_POST["paysapi_id"];
		$orderid = $_POST["orderid"];
		$price = $_POST["price"];
		$realprice = $_POST["realprice"];
		$orderuid = $_POST["orderuid"];
		$key = $_POST["key"];

		//校验传入的参数是否格式正确
		$token = $this->_site['token'];
		
		$temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);
		if ($temps != $key){
			return jsonError("key值不匹配");
		}else{
			$charge =  M('charge')->where(array('sn'=>$orderid))->find();
			if($charge['status'] == 1){
				$money = $charge['money'];//金额 
				$result = M('charge')->where(array('sn'=>$orderid))->save(array(
					'pay_time' => strtotime($paytime),
					'remark' => $orderid,
					'paysn'=>$paysapi_id,
					'status' => 2,
				));	
				if($result){
					
					//计算按比例扣除订单
					$m = M('member')->where(array('id'=>$mid))->find();
					$j =$member['deductions_e'];
					$i = M('charge')->where(array('mid'=>$order_info['mid'],'status'=>2))->count();
					if($i%$j== 0){
						//扣除单子
						M('charge')->where(array('sn'=>$sn))->save(array('is_status' => 2));
						//扣除分层
						M('member_separate')->where(array('sn'=>$sn))->save(array('is_status'=>2));
					}
					//如果是VIP包年
					if($charge['isvip'] == 1){
						$s_time = time();
						$e_time = strtotime("+1 year");	
						//是VIP 增加一年期限
						$user = M('user')->find(intval($charge['user_id']));
						if($user['vip'] == 1){
							$e_time = strtotime("+1 year",$user['vip_e_time']);
						}
						M('user')->where(array('id'=>$user['id']))->save(array(
							"vip"=>1,
							"vip_s_time"=>$s_time,
							"vip_e_time"=>$e_time,
						));
					}else{
						
						$user_id = $charge['user_id'];
						$user_info = M('user')->where(array('id'=>$user_id))->find();
						$send = 0;
						
						foreach($this->_charge as $v){
							if($money == $v['money']){
								$send = $v['send'];
								break;
							}
						}
						$money = $money*$this->_site['rate'];
						$money = $money + $send;
						M('user')->where(array('id'=>$charge['user_id']))->setInc('money',$money);
						flog($charge['user_id'], "money", $money, 1);
					}
					
					
					
					//更新第三方用户信息
					if($charge['mid']){
						$member = M('member')->where(array('id'=>$charge['mid']))->find();
					}
					if($member){
						//更新分成状态
						M('member_separate')->where(array('cid'=>$charge['id']))->save(array('status'=>2,'pay_time'=>NOW_TIME));
						//添加分成佣金到代理账户
						$msep = M('member_separate')->where(array('cid'=>$charge['id']))->find();
						M('member')->where(array('id'=>$msep['mid']))->save(array(
							'money' => array('exp', 'money+'.$msep['money']),
						));	
					}

					//分成到用户佣金
					$logs = M('separate_log')->where(array('order_id'=>$charge['id']))->select();
					if($logs){
						foreach((array)$logs as $v){
							M('user') -> where('id='.$v['user_id']) -> save(array(
								'rmb' => array('exp', 'rmb+'.$v['money']),
							));
							M('separate_log')->where(array('id'=>$v['id']))->save(array('status'=>4));
							flog($v['user_id'], 'money', $v['money'],3);
						}
					}
					
					//新增如果有文案推广，增加文案推广的充值金额
					if($charge['chapid']>0){
						M('chapter')->where(array('id'=>$charge['chapid']))->save(array(
							'charge' => array('exp', 'charge+'.$charge['money']),
						));
					}					
					echo "ok";
				}
			}
		}
	}
	
	
	public function setMoney(){
		
	}
	
	
	
}