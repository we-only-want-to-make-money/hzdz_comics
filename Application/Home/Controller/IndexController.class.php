<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController {
	public function _initialize(){
		parent::_initialize();
	}
	
	
	// 商城首页
    public function index(){
		$banner = arraySort($this->_banner['config'],'sort');
		$this->assign('banner',$banner);
    	$this -> display();
    }
	
	//获取首页课程数据
	public function getGoods(){
		$page = $_POST['page']?$_POST['page']:1;
		$pagesize = 10;
		$start = ($page - 1)*$pagesize;
		$keyword = $_POST['keyword'];
		if($keyword){
			$where['name'] = array('like','%'.$keyword.'%');
		}		
		$where['status'] = 1;
		$goods = M('goods')->where($where)->order('sold desc')->limit($start,$pagesize)->select();
		$sql = M()->getLastSql();
		if($goods){
			foreach($goods as $k=>$v){
				$goods[$k]['sort_name'] = M('goods_sorts')->where(array('id'=>$v['sorts_id']))->getField('fname');
				$goods[$k]['teacher_name'] = M('teacher')->where(array('id'=>$v['teacher']))->getField('name');
				$goods[$k]['teacher_status'] = M('teacher')->where(array('id'=>$v['teacher']))->getField('status');
			}
		}
		$this->assign('goods',$goods);
		$this->assign('page',$page);
		$html = $this->fetch();
		if(!$goods){
			$this->error($html);
		}else{
			$this->success($html,$sql);
		}
	}
	
	//系统消息
	public function notice(){
		$this->display();
	}
	
	//获取系统消息
	public function getNotice(){
		$page = $_POST['page']?$_POST['page']:1;
		$pagesize = 20;
		$start = ($page - 1)*$pagesize;
		$list = M('notice')->order('create_time desc')->limit($start,$pagesize)->select();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$html = $this->fetch();
		if(!$list){
			$this->error($html);
		}else{
			$this->success($html);
		}
	}
	
	public function notice_info(){
		$id = $_GET['id'];
		$notice = M('notice')->where(array('id'=>$id))->find();
		$this->assign('notice',$notice);
		$this->display();
	}
	
	//课程详情
	public function goods(){
		$id = I('get.id');
		if(!$id){
			$this->error('非法访问');
		}
		$goods = M('goods')->find(intval($id));
		$goods['banner'] = explode(',',$goods['banner']);
		$goods['teacher'] = M('teacher')->where(array('id'=>$goods['teacher']))->find();
		//该教师其他课程
		$ogoods = M('goods')->where(array('id'=>array('neq',$id),'teacher'=>$goods['teacher']['id']))->select();
		
		$this->assign('ogoods',$ogoods);
		$this->assign('goods',$goods);
		$this->display();
	}
	
	//课程评价
	public function assess(){
		$gid = I('get.gid');
		$content = I('post.content');
		if(!$gid){
			$this->error('参数错误');
		}
		if(!$content){
			$this->error('请输入评论的内容');
		}
		if(M('assess')->add(array(
			'gid'=>$gid,
			'user_id'=>$this->user['id'],
			'content'=>$content,
			'create_time'=>time(),
		))){
			$this->success('评论成功');
		}else{
			$this->error('评论失败');
		}
	}
	
	//加载课程评价
	public function getAssess(){
		$page = I('post.page');
		$gid = I('post.gid');
		$pagesize = 5 ;
		$start = ($page-1)*$pagesize;
		$list = M('assess')->where(array('gid'=>$gid))->order('create_time desc')->limit($start,$pagesize)->select();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$html = $this->fetch();
		if(!$list){
			$this->error($html);
		}else{
			$this->success($html);
		}
	}
	
	//课程试听
	public function listening(){
		$id = I('get.id');
		if(!$id){
			$this->error('非法访问');
		}
		$info = M('goods')->find(intval($id));
		$info['teacher'] = M('teacher')->where(array('id'=>$info['teacher']))->find();
		$this->assign('info',$info);
		$this->assign('goods',$info);
		$this->assign('moneys',explode('|',$this->_site['grow']));
		$this->display();
	}
	
	
	//选择购买方式
	public function order_pay(){
		$gid = I('get.gid');
		$goods = M('goods')->find(intval($gid));
		$order = $this->createOrder($gid);
		
		if(!$goods){
			$this->error('未找到符合的课程！');
		}
		$this->assign('info',$order);
		$this->display();
	}
	
	//支付
	public function pay(){
		$checks = I('post.checks');
		$order_id = I('get.order_id');
		//根据订单生成分成记录
		separate($order_id);
		
		$order = M('order')->find(intval($order_id));
		//更新订单的支付方式
		M('order')->where(array('id'=>$order_id))->save(array('order_type'=>$checks));
		
		if($checks == 1){//微信支付
			$params = wxPay($order_id,'order','order');
			$this->assign('order',$order);
			$this->assign('params',$params);
			$this->display();
		}else if($checks == 2){//支付宝支付
			redirect(U('Alipay/ali_pay',array('order'=>$order_id,'table'=>'order')));
		}else{//余额支付
			if($this->user['money']<$order['money']){
				$this->error('您的余额不足');
			}			
			M('user')->where(array('id'=>$order['user_id']))->setDec('money',$order['money']);
			if($order['separate']>0){
				// 更新分成状态
				M('separate_log') -> where(array('order_id'=>$order_id)) -> setField('status', 2);
				//更新用户销售业绩+等级+分拥+购买额度
				upUser($order);
			}
			$save['status'] = 2;
			$save['pay_time'] = NOW_TIME;
			M('order') -> where('id='.$order_id) -> save($save);
			redirect(U('Ucenter/myGoods'));
		}
	}
	
	
	// 购物车
	public function cart(){
		$cart = M('cart');
		$list = $cart -> where(array('user_id' => $this -> user['id'],'status'=>0)) -> select();
		$this -> assign('list', $list);
		
		// 计算总数量
		$count = count($list);
		
		// 计算购物车总额
		$total = $this -> _get_cart_total($list);
		$this -> assign('total', $total);
		$this -> display();
	}
	
	//选择购物车数量计算价格
	public function cartChecked(){
		$ids = $_POST['cartIds'];
		if(!$ids){
			$total = '0.00';
		}else{
			$where['id']= array('in',$ids);
			$list = M('cart')->where($where)->select();
			$total = $this->_get_cart_total($list);
		}
		
		$this->SendAjax(1,$total);
	}
	
	// 计算购物车总额
	private function _get_cart_total($list = null, $type = 'price'){
		if(!$list){
			$cart = M('cart');
			$list = $cart -> where(array('user_id' => $this -> user['id'])) -> select();
		}
		$total = 0;
		if(is_array($list)){
			// 计算总额
			
			foreach($list as $v){
				$total += $v['market_price'];
			}
			// 如果是计算总价格则保留两位小数点
			if($type == 'price'){
				$total = sprintf("%.2f", $total);
			}
		}
		return $total;
	}
	
	
	//购物车购买
	public function cart_pay(){
		$cartid = I('get.cartid');
		if($cartid){
			$where['id'] = array('in',$cartid);
			$list = M('cart')->where($where)->select();
		}
		foreach($list as $k=>$v){
			$gids.=$v['goods_id'].',';
			if($v['order_id']>0){
				$this->error('该购物车课程已经提交订单');
			}
		}
		$separate_money = M('goods')->where(array('id'=>array('in',$gids)))->sum('separate_money');
		$gids = substr($gids,0,-1);
		$total = $this->_get_cart_total($list);
		
		$data = array(
			'sn'=>create_order_sn($this->user['id']),
			'user_id'=>$this->user['id'],
			'goods_id'=>$gids,
			'points'=>$total,
			'money'=>$total,
			'create_time'=>time(),
			'separate_money'=>$separate_money,
		);
		$data['id'] = M('order')->add($data);
		M('cart')->where($where)->save(array('order_id'=>$data['id'],'status'=>1));
		if($data){
			$this->assign('info',$data);
			$this->display();
		}else{
			$this->error('生成订单失败');
		}
	}
	
	
	//立即购买生成订单和购物车数据
	private function createOrder($id,$type){
		 if(!$id){
			 return;
		 } 
		 $goods = M('goods')->find(intval($id));
		 
		 if($this->user['lv']>0 || $this->user['level'] >0){
			 if(!$this->user['lv']){
				 $money = $goods['price'];
			 }else{
				 $money = ($this->_lv[$this->user['lv']]['coupon'] * $goods['price'])/100 ;
			 }
			
		}else{
			$money = $goods['price'];
		}
		 
		 $data = array(
			'sn' => create_order_sn($this->user['id']),
			'goods_id'=>$goods['id'],
			'user_id' => $this->user['id'],
			'points' => intval($goods['price']/$this->_site['rate']),
			'money' => $money,
			'create_time' => time(),
			'order_type' => $type,
		);
		//若设置了分成金额
		if($this->_site['dist'] == 2){
			$data['separate_money']  = $goods['separate_money'];			
		}else{
			$data['separate_money']  = $goods['price'];	
		}
		$data['id'] = M('order')->add($data);
		//添加购物车
		
		$cart['user_id'] = $this->user['id'];
		$cart['goods_id'] = $goods[id];
		$cart['order_id'] = $data['id'];
		$cart['title'] = $goods['name'];
		$cart['pic'] = $goods['pic'];
		$cart['market_price'] = $money;
		$cart['price'] = $goods['price'];
		$cart['create_time'] = time();
		$cart['status'] = 1;
		if(!M('cart')->where(array('order_id'=>$data['id']))->find()){
			M('cart')->add($cart);
		}
		return $data; 
	}
	
	//分类
	public function cates(){
		$this->assign('cates',M('goods_sorts')->where(array('pid'=>0))->select());
		$this->display();
	}
	
	//获取分类数据
	public function getCates(){
		
	}
	
	//申请讲师
	public function applyT(){
		if(IS_POST){
			$post = I('post.');
			$post['create_time'] = time();
			if(M('teacher')->where(array('mobile'=>$post['mobile']))->find()){
				$this->error('该手机号已经申请过了讲师');
			}
			if(M('teacher')->add($post)){
				$this->success('申请成功，等待审核');
			}else{
				$this->error('申请失败');
			}
			exit;
		}
		$this->display();
	}

	
}?>