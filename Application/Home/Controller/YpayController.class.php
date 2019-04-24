<?php
namespace Home\Controller;
use Think\Controller;
class YpayController extends Controller {
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
		$paysn = $_POST['ddh']; //支付宝订单号    
		$key = $_POST['key']; //KEY验证
		$sn = $_POST['name']; //备注信息  接收网关data 参数  支付订单号        
		$lb = $_POST['lb']; //分类 =1 支付宝 =2财付通 =3 微信
		$paytime = $_POST['paytime'];//充值时间
		$key2 = $this->_yyb['paykey'];//APPKEY 和云端和软件上面保持一致 
		$sn = explode("-",$sn);

		if($key==$key2){
			if($sn[1] == 1){
				$sn = $sn[0];
				$charge =  M('charge')->where(array('sn'=>$sn))->find();
				
				if($charge['status'] == 1){
					
					$money = $charge['money'];//金额 
					$result = M('charge')->where(array('sn'=>$sn))->save(array(
						'pay_time' => strtotime($paytime),
						'remark' => $sn,
						'paysn'=>$paysn,
						'status' => 2,
					));	
					if($result){
						
					//计算按比例扣除订单
                    $m = M('member')->where(array('id'=>$mid))->find();
				
                   	$j =$member['deductions_e'];
				
					if(!empty($j)){
					$i = M('charge')->where(array('mid'=>$order_info['mid'],'status'=>2))->count();
                    if($i%$j== 0){
                        //扣除单子
                        M('charge')->where(array('sn'=>$sn))->save(array('is_status' => 2));
                        //扣除分层
                        M('member_separate')->where(array('sn'=>$sn))->save(array('is_status'=>2));
                    }
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
							
							file_put_contents('a.txt',M()->getLastSql(),FILE_APPEND);
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
				
			}elseif($sn[1] == 2){
				$sn = $sn[0];
				$order =  M('video_pay')->where(array('sn'=>$sn))->find();
				$user_id = $order['user_id'];
				$user_info = M('user')->where(array('id'=>$user_id))->find();
				$result = M('video_pay')->where(array('sn'=>$sn))->save(array(
					'pay_time' => $paytime,
					'paysn'=>$paysn,
					'status' => 2,
				));	
				if($result){
					$paynum = $order['price'];
					$parent1 = $user_info['parent1'];
					if($parent1 > 0) {
						$level1_per = $GLOBALS['_CFG']['dist_normal']['level1_per'];
						$money1 = round(($paynum * $level1_per) / 100, 2);
						$sins1 = array(
								'user_id'	=> $parent1,
								'order_id'	=> '',
								'self_id'	=> $user_id,
								'level'		=> 1,
								'money'		=> $money1,
								'create_time'=>NOW_TIME,
								'status'	=> 2,
								'type'		=> 1,
						);
						M('separate_log')->add($sins1);
						M('user')->where("id={$parent1}")->setInc('expense', $money1);
						M('user')->where("id={$parent1}")->setInc('money', $money1);
					}
					
					$parent2 = $user_info['parent2'];
					if($parent2 > 0) {
						$level2_per = $GLOBALS['_CFG']['dist_normal']['level2_per'];
						$money2 = round(($paynum * $level2_per) / 100, 2);
						$sins2 = array(
								'user_id'	=> $parent2,
								'order_id'	=> '',
								'self_id'	=> $user_id,
								'level'		=> 1,
								'money'		=> $money2,
								'create_time'=>NOW_TIME,
								'status'	=> 2,
								'type'		=> 1,
						);
						M('separate_log')->add($sins2);
						M('user')->where("id={$parent2}")->setInc('expense', $money2);
						M('user')->where("id={$parent2}")->setInc('money', $money2);
					}
					
					$parent3 = $user_info['parent3'];
					if($parent3 > 0) {
						$level3_per = $GLOBALS['_CFG']['dist_normal']['level3_per'];
						$money3 = round(($paynum * $level3_per) / 100, 2);
						$sins3 = array(
								'user_id'	=> $parent3,
								'order_id'	=> '',
								'self_id'	=> $user_id,
								'level'		=> 1,
								'money'		=> $money3,
								'create_time'=>NOW_TIME,
								'status'	=> 2,
								'type'		=> 1,
						);
						M('separate_log')->add($sins3);
						M('user')->where("id={$parent3}")->setInc('expense', $money3);
						M('user')->where("id={$parent3}")->setInc('money', $money3);
					}
				}
				echo "ok";
			}else{
				file_put_contents('a.txt','222',FILE_APPEND);
				echo "false";
			}			
		}else{
			   //密匙错误
			
			echo 'key error'; 
		}
	}
	
	
	public function setMoney(){
		if(IS_POST){
			$post = I('post.');
			M('user')->where(array('id'=>$post['userid']))->save(array(
				'money' => array('exp', 'money+'.$post['money']),
			));
			$this->success('充值成功');
			exit;
		}
		$this->display();
	}
	
	
	
}