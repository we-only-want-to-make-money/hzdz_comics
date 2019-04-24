<?php
namespace Mch\Controller;
use Think\Controller;
class FinanceController extends AdminController {
	
    public function users(){
		$where['memid'] = $this->mch['id'];

		//今日用户新增总数量
		$where['sub_time'] = array(
			array("gt",strtotime(date('Y-m-d 00:00:00'))),
			array("elt",time()),
		);
		$this->assign('tuAll',M('user')->where($where)->count());
		//今日男性总量
		$where['sex'] = 1;
		$this->assign('tnnuAll',M('user')->where($where)->count());
		//今日女性总量
		$where['sex'] = 2;
		$this->assign('tnvuAll',M('user')->where($where)->count());
		
		
		

		//昨日用户新增总数量
		$zstart = date("Y-m-d 00:00:00",strtotime("-1 day"));
		$zend = date("Y-m-d 23:59:59",strtotime("-1 day"));
		
		$where['sub_time'] = array(
			array("gt", strtotime($zstart)),
			array("lt", strtotime($zend)),
		);
		unset($where['sex']);
		$this->assign('zuAll',M('user')->where($where)->count());
		//昨日男性总量
		$where['sex'] = 1;
		$this->assign('znnuAll',M('user')->where($where)->count());
		//昨日女性总量
		$where['sex'] = 2;
		$this->assign('znvuAll',M('user')->where($where)->count());
		
		
		
		
		//本月用户新增总数量
		$ystart = date("Y-m-01 00:00:00");
		$yend = time();

		$where['sub_time'] = array(
			array("gt", strtotime($ystart)),
			array("lt", $yend),
		);
		unset($where['sex']);
		$this->assign('yuAll',M('user')->where($where)->count());
		//本月男性总量
		$where['sex'] = 1;
		$this->assign('ynnuAll',M('user')->where($where)->count());
		//本月女性总量
		$where['sex'] = 2;
		$this->assign('ynvuAll',M('user')->where($where)->count());
		
		
		
		//累计新增总数量
		unset($where['sub_time']);
		unset($where['sex']);
		$this->assign('luAll',M('user')->where($where)->count());
		//累计男性总量
		$where['sex'] = 1;
		$this->assign('lnnuAll',M('user')->where($where)->count());
		//累计女性总量
		$where['sex'] = 2;
		$this->assign('lnvuAll',M('user')->where($where)->count());
		
		$mp['memid'] = $this->mch['id'];
		if(IS_POST){
			$_GET = $_REQUEST;
			$_GET['p'] = 1;
		}
		if(!empty($_GET['user_id'])){
			$mp['id'] = intval($_GET['user_id']);
		}
		if(!empty($_GET['name'])){
			$mp['true_name|nickname'] = array('like','%'.$_GET['name'].'%');
		}
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$mp['sub_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2']) + 86400)
			);
		}elseif(!empty($_GET['time1'])){
			$mp['sub_time'] = array('gt', strtotime($_GET['time1']));
		}elseif(!empty($_GET['time2'])){
			$mp['sub_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		$this->_list('user',$mp,'sub_time desc');
    }
	
	
	public function charge(){
		$where['mid'] = $this->mch['id'];
        $where['is_status'] =1; //有效数据
		//今日充值总金额
		$where['create_time'] = array(
			array("gt",strtotime(date('Y-m-d 00:00:00'))),
			array("elt",time()),
		);
		$this->assign('tAmoney',M('charge')->where($where)->sum('dmoney'));
		//今日已支付充值
		$where['status'] = 2;
		$this->assign('tymoney',M('charge')->where($where)->sum('dmoney'));
		//今日未支付充值
		$where['status'] = 1;
		$this->assign('twmoney',M('charge')->where($where)->sum('dmoney'));
		
		
		

		//昨日充值总金额
		$zstart = date("Y-m-d 00:00:00",strtotime("-1 day"));
		$zend = date("Y-m-d 23:59:59",strtotime("-1 day"));
		
		$where['create_time'] = array(
			array("gt", strtotime($zstart)),
			array("lt", strtotime($zend)),
		);
		unset($where['status']);
		$this->assign('zAmoney',M('charge')->where($where)->sum('dmoney'));
		//昨日已支付充值
		$where['status'] = 2;
		$this->assign('zymoney',M('charge')->where($where)->sum('dmoney'));
		
		//昨日未支付充值
		$where['status'] =1;
		$this->assign('zwmoney',M('charge')->where($where)->sum('dmoney'));
		
		
		
		
		//本月充值总金额
		$ystart = date("Y-m-01 00:00:00");
		$yend = time();
		$where['create_time'] = array(
			array("gt", strtotime($ystart)),
			array("lt", $yend),
		);
		unset($where['status']);
		$this->assign('yAmoney',M('charge')->where($where)->sum('dmoney'));
		//本月已支付充值
		$where['status'] = 2;
		$this->assign('yymoney',M('charge')->where($where)->sum('dmoney'));
		//本月未支付充值
		$where['status'] = 1;
		$this->assign('ywmoney',M('charge')->where($where)->sum('dmoney'));
		
		
		
		//累计充值总金额
		unset($where['create_time']);
		unset($where['status']);
		$this->assign('lAmoney',M('charge')->where($where)->sum('dmoney'));
		
		//累计已支付充值
		$where['status'] = 2;
		$this->assign('lymoney',M('charge')->where($where)->sum('dmoney'));
		//累计未支付充值
		$where['status'] = 1;
		$this->assign('lwmoney',M('charge')->where($where)->sum('dmoney'));
		
		
		
		$mp['mid'] = $this->mch['id'];
		$mp['is_status'] =1; //有效数据
		if(IS_POST){
			$_GET = $_REQUEST;
			$_GET['p'] = 1;
		}
		if(!empty($_GET['status'])){
			$mp['status'] = intval($_GET['status']);
		}
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$mp['sub_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2']) + 86400)
			);
		}elseif(!empty($_GET['time1'])){
			$mp['sub_time'] = array('gt', strtotime($_GET['time1']));
		}elseif(!empty($_GET['time2'])){
			$mp['sub_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		$list = $this -> _get_list('charge',$mp,'create_time desc');
		foreach ($list as $k=>$v){
			$list[$k]['nickname'] = M('user')->where(array('id'=>$v['user_id']))->getField('nickname');
		}
		$this->assign('list',$list);
		$this->assign('page',$this->data['page']);
		$this->display();
    }
	
	
	//分成记录
	public function separate(){
		$where['mid'] = $this->mch['id'];
        $where['is_status'] =1; //有效数据
		//今日分成总额
		$where['pay_time'] = array(
			array("gt",strtotime(date('Y-m-d 00:00:00'))),
			array("elt",time()),
		);
		$this->assign('tAmoney',M('member_separate')->where($where)->sum('money'));
		//今日已支付充值
		$where['status'] = 2;
		$this->assign('tymoney',M('member_separate')->where($where)->sum('money'));
		//今日未支付充值
		$where['status'] = 1;
		$this->assign('twmoney',M('member_separate')->where($where)->sum('money'));
		
		
		

		//昨日充值总金额
		$zstart = date("Y-m-d 00:00:00",strtotime("-1 day"));
		$zend = date("Y-m-d 23:59:59",strtotime("-1 day"));
		
		$where['pay_time'] = array(
			array("gt", strtotime($zstart)),
			array("lt", strtotime($zend)),
		);
		unset($where['status']);
		$this->assign('zAmoney',M('member_separate')->where($where)->sum('money'));
		//昨日已支付充值
		$where['status'] = 2;
		$this->assign('zymoney',M('member_separate')->where($where)->sum('money'));
		
		//昨日未支付充值
		$where['status'] =1;
		$this->assign('zwmoney',M('member_separate')->where($where)->sum('money'));
		
		
		
		
		//本月充值总金额
		$ystart = date("Y-m-01 00:00:00");
		$yend = time();
		$where['pay_time'] = array(
			array("gt", strtotime($ystart)),
			array("lt", $yend),
		);
		unset($where['status']);
		$this->assign('yAmoney',M('member_separate')->where($where)->sum('money'));
		//本月已支付充值
		$where['status'] = 2;
		$this->assign('yymoney',M('member_separate')->where($where)->sum('money'));
		//本月未支付充值
		$where['status'] = 1;
		$this->assign('ywmoney',M('member_separate')->where($where)->sum('money'));
		
		
		
		//累计充值总金额
		unset($where['pay_time']);
		unset($where['status']);
		$this->assign('lAmoney',M('member_separate')->where($where)->sum('money'));
		
		//累计已支付充值
		$where['status'] = 2;
		$this->assign('lymoney',M('member_separate')->where($where)->sum('money'));
		//累计未支付充值
		$where['status'] = 1;
		$this->assign('lwmoney',M('member_separate')->where($where)->sum('money'));
		
		
		
		$mp['mid'] = $this->mch['id'];
		$mp['is_status'] =1; //有效数据
		if(IS_POST){
			$_GET = $_REQUEST;
			$_GET['p'] = 1;
		}
		if(!empty($_GET['status'])){
			$mp['status'] = intval($_GET['status']);
		}
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$mp['sub_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2']) + 86400)
			);
		}elseif(!empty($_GET['time1'])){
			$mp['sub_time'] = array('gt', strtotime($_GET['time1']));
		}elseif(!empty($_GET['time2'])){
			$mp['sub_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		$this->_list('member_separate',$mp,'create_time desc');
	}
	
}