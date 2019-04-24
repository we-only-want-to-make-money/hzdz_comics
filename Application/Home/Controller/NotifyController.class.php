<?php
namespace Home\Controller;
use Think\Controller;
class NotifyController extends Controller {
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
    
	// 微信支付通知异步页面
	public function index(){	
    	
		$jsapi = new \Common\Util\wxjspay;
		$jsapi -> set_param('key', $this -> _mp['key']);
		// 验证签名之前必须调用get_notify_data方法获取数据
		$data = $jsapi -> get_notify_data();
	    //file_put_contents('log2.txt',var_export($data,true),FILE_APPEND);
		
		if(!$jsapi->check_sign()){
			// 签名验证失败
			die('FAIL');
		}
		if($data['return_code'] != 'SUCCESS' || $data['result_code'] != 'SUCCESS'){
			die('FAIL');
		}
		$attach = json_decode($data['attach'], 1);
		//订单传入参数
		$order_id = intval($attach['order_id']);
		$table = $attach['table'];
		$type = $attach['type'];
		
		//订单信息
		$order_info = M($table) -> find($order_id);
		
		M('charge')->where(array('sn'=>$data['out_trade_no']))->save(array(
					'pay_time' => strtotime($data['time_end']),
					'remark' => $data['out_trade_no'],
					'paysn'=>$data['out_trade_no'],
					'status' => 2,
			));	
		
		// 查询此支付是否已处理
		if(M('pay_log') -> where(array('transaction_id' => $data['transaction_id'])) -> find()){
			die('SUCCESS');
		}else{
			$data['log_time'] = NOW_TIME;
			// 记录支付日志
			M('pay_log') -> add($data);
		}	
		// 获取用户信息，异步通知不执行网页认证授权，需要获取用户信息
		$this -> user = M('user') -> find(intval($order_info['user_id']));
		if(!$this -> user){
			die('Fail');
		}
		if($order_info['status'] !=1){
			die('FAIL');
		}
		$paid_fee = $data['total_fee']/100;
		// 已支付全部费用
		if($paid_fee >= $order_info['money']){		
			$save['status'] = 2;
			$save['pay_time'] = NOW_TIME;
			
			//如果有分成  //现阶段暂时立即购买立即分成（不提供退款） -- 商城订单
			if($type =='order'){
				// 更新分成状态为待确认
				M('separate_log') -> where(array('order_id'=>$order_id)) -> setField('status', 2);
				//更新用户销售业绩+等级+分拥+购买额度
				upUser($order_info);
			}
			
			//如果是预存信息
			if($type =='agent'){
				//给用户充值金额和变更代理等级
				$userData['money'] = array('exp', 'money+'.$paid_fee);
				if(!$this->user['join_lv_time']){
					$userData['join_lv_time'] = time();
				}
				if($this->user['lv']<$order_info['lv']){
					$userData['lv'] = $order_info['lv'];
				}
				M('user')->where(array('id'=>$this->user['id']))->save($userData);
				flog($this -> user['id'],'money',$data['total_fee']/100, 1); // 记录财务日志
				$save['pay_type'] = 1;
			}
			
			if($type =='grow'){
				M('goods') -> where('id='.$order_info['gid']) -> setInc('grow',$order_info['money']);
			}
			
			if($type == "charge"){	
				//如果是VIP包年
				if($order_info['isvip'] == 1){
					$s_time = time();
					$e_time = strtotime("+1 year");	
					//是VIP 增加一年期限
					if($this->user['vip'] == 1){
						$e_time = strtotime("+1 year",$this->user['vip_e_time']);
					}
					M('user')->where(array('id'=>$this->user['id']))->save(array(
						"vip"=>1,
						"vip_s_time"=>$s_time,
						"vip_e_time"=>$e_time,
					));
				}else{
					$send = 0;	
					$money = $paid_fee;
					foreach($this->_charge as $v){
						if($money == $v['money']){
							$send = $v['send'];
						}
					}
					$money = $money*$this->_site['rate'];
					$money = $money + $send;
					$userData['money'] = array('exp', 'money+'.$money);
					M('user')->where(array('id'=>$this->user['id']))->save($userData);
					flog($order_info['user_id'], "money", $money, 1);
				}
				
				//漫画充值，给用户增加金额和第三方公司更新分成
				$member = M('member')->where(array('id'=>$order_info['mid']))->find();
				
				if($member){
					//file_put_contents("log222.txt",  var_export('1111', true), FILE_APPEND);
					//更新分成状态
					  M('member_separate')->where(array('cid'=>$order_info['id']))->save(array('status'=>2,'pay_time'=>NOW_TIME));
						//file_put_contents("log7.txt",  var_export(M('member_separate')->getLastSql(), true), FILE_APPEND);
					//更新扣量表记录
					//M('member_desc')->where(array('cid'=>$order_info['id']))->save(array('status'=>2,'pay_time'=>NOW_TIME));
				
					$j =$member['deductions_e'];
						//file_put_contents("log2222.txt",  var_export($j, true), FILE_APPEND);
					if(!empty($j)){
					$i = M('charge')->where(array('mid'=>$order_info['mid'],'status'=>2))->count();
					
							//file_put_contents("log3333.txt",  var_export($i, true), FILE_APPEND);
                    if($i%$j== 0){
						//file_put_contents("log3.txt",  var_export($i, true), FILE_APPEND);
                        //扣除单子
                        M('charge')->where(array('sn'=>$order_info['sn']))->save(array('is_status' => 2));
                        //扣除分层
                        M('member_separate')->where(array('sn'=>$order_info['sn']))->save(array('is_status'=>2));
                    }
					}
					/*不需要分成结算
					//当天分成金额
					$separate = M('member_separate')->where(array('cid'=>$order_info['id'],'date'=>date('Ymd')))->sum('money');
					if($separate>0){
						//当天是否有结算数据，有则更新，没有则添加
						if(M('member_data')->where(array('mid'=>$order_info['mid'],'date'=>date('Ymd')))->find()){
							M('member_data')->where(array('mid'=>$order_info['mid'],'date'=>date('Ymd')))->save(array('money'=>$separate));
						}else{
							M('member_data')->add(array(
								'mid'=>$order_info['mid'],
								'date'=>date('Ymd'),
								'money'=>$separate,
							));
						}		
					}	
					*/
					//添加分成佣金到代理账户
					$msep = M('member_separate')->where(array('cid'=>$order_info['id']))->find();
					M('member')->where(array('id'=>$msep['mid']))->save(array(
						'money' => array('exp', 'money+'.$msep['money']),
					));
				}
				//分成到用户佣金
				$logs = M('separate_log')->where(array('order_id'=>$order_info['id']))->select();
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
				if($order_info['chapid']>0){
					M('chapter')->where(array('id'=>$order_info['chapid']))->save(array(
						'charge' => array('exp', 'charge+'.$order_info['money']),
					));
				}
					
			}
		}
		M($table) -> where('id='.$order_id) -> save($save);
		//充值完成添加日志
		die('SUCCESS');
	}

}