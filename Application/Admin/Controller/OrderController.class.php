<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends AdminController {
    // 通知列表
	public function index(){
		$where = $this -> _get_where();
		$where['goods_id'] = '99999999';
		$where_paid = $where;
		$where_paid['status'] = array('eq',2);// array_merge(array('status' => ),$where);
		//var_dump($where_paid);
		
		// 总订单数
		$this -> assign('orders', M('order') -> where($where) -> count());

		// 已支付订单数
		//$this -> assign('orders_paid', M('order') -> where($where_paid) -> count());
		// 总额
		//$this -> assign('total', M('order') -> where($where) -> sum('total'));
		// 已支付总额
		//$this -> assign('total_paid', M('order') -> where($where_paid) -> sum('total'));

		// 微信支付
		//$this -> assign('wxpay', M('order') -> where($where) -> sum('wxpay'));
		// 余额支付
		//$this -> assign('money', M('order') -> where($where) -> sum('money'));
		// 积分支付
		//$this -> assign('points', M('order') -> where($where) -> sum('points'));
		
		$this -> _list('order',$where);
	}
	
	// 到处excel 
	public function export(){
		$where = $this -> _get_where();
		$list = M('order') -> where($where) -> select();
		
		// 表头
		$data[0] = array(
			'编号',
			'订单号',
			'下单用户ID',
			'收货人',
			'联系电话',
			'收货地址',
			'客户留言',
			'总额',
			'支付金额',
			'快递',
			'快递单号',
			'状态',
			'下单时间',
			'支付时间',
			'发货时间',
			'完成时间', // 16
			'商品',
			'单价',
			'数量',
			'小结'
		);
		foreach($list as $v){
			$paid = $v['points'] + $v['money'] + $v['wxpay'];
			$status = get_order_status($v['status']);
			
			$products  = M('order_product') -> where("order_id=".$v['id']) -> select();
			$pro = $products[0];
			$data[] = array(
				$v['id'],$v['sn'],$v['user_id'],$v['name'],$v['mobile'],$v['addr'],$v['msg'],$v['total'],$paid,
				$v['express'],$v['express_no'],$status,
				date('Y-m-d H:i:s', $v['create_time']),
				date('Y-m-d H:i:s', $v['pay_time']),
				date('Y-m-d H:i:s', $v['delivery_time']),
				date('Y-m-d H:i:s', $v['confirm_time']),
				$pro['title'],$pro['price'],$pro['nums'],$pro['price']*$pro['nums']
			);
			
			unset($products[0]);
			foreach($products as $pro){
				$data[] = array(
					'','','','','','','','','','','','','','','','',
					$pro['title'],$pro['price'],$pro['nums'],$pro['price']*$pro['nums']
				);
			}
		}
		
		$filename = NOW_TIME.".csv";
		header("Content-Type: application/force-download"); 
		header("Content-Type: application/octet-stream"); 
		header("Content-Type: application/download"); 
		header('Content-Disposition:inline;filename="'.$filename.'"'); 
		header("Content-Transfer-Encoding: binary"); 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Pragma: no-cache"); 
		
		foreach($data as $d){
			echo implode(',',$d);
			echo "\r\n";
		}
		
		die();
	}
	
	private function _get_where(){
		if(IS_POST){
			$_GET  = array_merge($_GET, $_POST);
			$_GET['p'] = 1; //如果是post的话回到第一页
		}
		if(!empty($_GET['status'])){
			$where['status'] = intval($_GET['status']);
		}
		
		if(!empty($_GET['user_id'])){
			$where['user_id'] = intval($_GET['user_id']);
		}
		
		
		if(!empty($_GET['time1']) && !empty($_GET['time2'])){
			$where['create_time'] = array(
				array('gt', strtotime($_GET['time1'])),
				array('lt', strtotime($_GET['time2']) + 86400)
			);
		}
		elseif(!empty($_GET['time1'])){
			$where['create_time'] = array('gt', strtotime($_GET['time1']));
		}
		elseif(!empty($_GET['time2'])){
			$where['create_time'] = array('lt', strtotime($_GET['time2'])+86400);
		}
		return $where;
	}
	
	// 订单详情
	public function detail(){
		$id = intval($_GET['id']);
		$order_info = M('order') -> find($id);
		if(!$order_info){
			$this -> error('订单不存在');
		}
		$this -> assign('info', $order_info);
		
		//查询商品信息
		$product_list = M('order_product') -> where(array('order_id' => $order_info['id'])) -> select();
		$this -> assign('products', $product_list);
		
		$this -> display();
	}
	
	// ajax设置快递信息,发货
	public function set_express(){
		$name = I('post.name');
		$no = I('post.no');
		$order_id = intval($_POST['order']);
		$rs = M('order') -> where("id={$order_id}") -> save(array(
			'express' => $name,
			'express_no' => $no,
			'delivery_time' => NOW_TIME, //发货时间
			'status' => 3 // 已发货状态
		));
		
		// 将分成改为待确认状态
		M('separate_log') -> where('order_id='.$order_id) -> setField('status', 3);
		
		if($rs){
			$order_info = M('order') -> find($order_id);
			$user_info = M('user') -> find($order_info['user_id']);
			
			$tplmsg = new \Common\Util\tplmsg;
			$tplmsg -> express_notice($user_info['openid'], $order_info);
			
			//confirm_order($order_info);
		
			$this -> success('操作成功！');
			exit;
		}else{
			$this -> error('操作失败，请重试！');
		}
	}
	
	// 取消订单
	public function cancle_order(){
		$order_id = intval($_POST['order']);
		$order_model = M('order');
		$order_info = $order_model -> find($order_id);
		
		// 订单状态设置为-1
		$order_model -> where('id='.$order_id) -> save(array(
			'status' => -1,
			'confirm_time' => NOW_TIME
		));

		// 分成状态设置为-1
		M('separate_log') -> where('order_id='.$order_id) -> setField('status', -1);
		
		// 如果是已完成状态，则表示分成已到帐，需要扣除
		if($order_info['status'] == 4){
			$user_model = M('user');
			$slog = M('separate_log') -> where('order_id='.$order_id) -> select();
			foreach($slog as $log){
				$user_model -> where('id='.$log['user_id']) -> save(array(
					'money' => array('exp', 'money-'.$log['money']),
					'points' => array('exp', 'points-'.$log['points']),
				));
				
				if($log['money'] >0){
					flog($log['user_id'],'money',$log['money'],11);
				}
				if($log['points'] >0){
					flog($log['user_id'],'points',$log['points'],11);
				}
			}
		}
		
		$data = array();
		//将已支付的费用原路返回
		if($order_info['money'] >0){
			$data['money'] = array('exp','money+'.$order_info['money']);
			flog($order_info['user_id'],'money', $order_info['money'],10);
		}
		if($order_info['points']>0){
			$data['points'] = array('exp','points+'.$order_info['points']);
			flog($order_info['user_id'],'points', $order_info['points'],10);
		}
		
		if(count($data) > 0){
			M('user') -> where('id='.$order_info['user_id']) -> save($data);
		}
		
		// 微信支付的部分需要手动返回
		
		$this -> success('操作成功！');
	}
	
	// 删除通知
	public function del(){
		$order_id = intval($_GET['id']);
		$order_info = M('order') -> find($order_id);
		if($order_info['status'] != -1){
			$this -> error('只有已关闭的订单才能删除');
		}
		
		// 删除订单
		M('order') -> delete($order_id);
		// 删除订单商品
		M('order_product') -> where(array('order_id' => $order_id)) -> delete();
		// 删除相关分成记录
		M('separate_log') -> where(array('order_id' => $order_id)) -> delete();
		
		$this -> success('订单删除成功', $_SERVER['HTTP_REDERER']);
	}
}?>