<?php
namespace Admin\Controller;
use Think\Controller;
class LindController extends Controller {
	
	public function _initialize(){
		// _开头的函数为内部函数，不能直接访问
		if(substr(ACTION_NAME,0,1) == '_'){
			$this -> error('访问地址错误！', U('Index/index'));
		}
		
		// 从数据读取配置参数
		$config = M('config') -> select();
		foreach($config as $v){
			$key = '_'.$v['name'];
			$this -> $key = unserialize($v['value']);
			$_CFG[$v['name']] = $this -> $key;
		}
		$this -> assign('_CFG', $_CFG);
		$GLOBALS['_CFG'] = $_CFG;
    }
	
    // 通知列表
	public function index(){
		if(IS_POST){
			$_GET = $_REQUEST;
		}
		if(!empty($_GET['id'])){
			$where['id'] = intval($_GET['id']);
		}
		if(!empty($_GET['level'])){
			$where['level'] = intval($_GET['level']);
		}
		if($_GET['reset']!=''){
			$where['reset'] = intval($_GET['reset']);
		}
		
		if(!empty($_GET['parent1'])){
			$where['parent1'] = intval($_GET['parent1']);
		}
		if(!empty($_GET['parent2'])){
			$where['parent2'] = intval($_GET['parent2']);
		}
		if(!empty($_GET['parent3'])){
			$where['parent3'] = intval($_GET['parent3']);
		}
		
		
		// 组合排序方式
		if(in_array($_GET['order'], array('id','expense_total', 'agent1','agent2','agent3','sub_time'))){
			$type = $_GET['type'] == 'asc' ? 'asc' : 'desc';
			$order = $_GET['order'].' '.$type;
		}
		
		//发送的升级模板消息
		$this->assign('tpls',M('tpl')->order('id desc')->select());
		$this -> _list('user', $where, $order);
	}

	
	
	
	// 充值/扣除
	public function charge(){
		$method = $_POST['method'];
		$money = $_POST['money'];
		$user_id = intval($_POST['user_id']);
		
		if($method ==1){
			M('user') -> where('id='.$user_id) -> save(array(
				'money' => array('exp', 'money+'.$money)
			));
			flog($user_id, 'money',$money, 15);
		}
		elseif($method ==2){
			M('user') -> where('id='.$user_id) -> save(array(
				'money' => array('exp', 'money-'.$money)
			));
			flog($user_id, 'money',$money, 16);
		}
		
		$this -> success('操作成功');
	}
	
	public function set_col(){
		$id = intval($_REQUEST['id']);
		$col = $_REQUEST['col'];
		$value = $_REQUEST['value'];
		
		if(!$table)$table = CONTROLLER_NAME;
		M($table) -> where('id='.$id) -> setField($col,$value);
		
		update_level(intval($_REQUEST['id']));
		$this -> success('操作成功',$_SERVER['HTTP_REFERER']);
	}
	
	public function sendMsg(){
		if(IS_POST){
			$ids = I('post.ids');
			$users = M('user')->where(array('id'=>array('in',$ids)))->select();
			$tpl = M('tpl')->find(intval($_POST['tid']));
			if($users){
				foreach($users as $k=>$v){
					$v['mobile'] = $this->_Asite['mobile'];
					$v['name'] = $this->_Asite['name'];
					$v['weixin'] = $this->_Asite['weixin'];
					$v['title'] = $tpl['title'];
					$v['remark'] = $tpl['remark'];
					$v['url'] = $tpl['url'];
					// 发送模板消息通知
					$tplmsg = new \Common\Util\tplmsg;
					$tplmsg -> goup($v['openid'], $v);
				}
				
				$this->success('发送成功');
			}
		}
	}
	
	// 通用简单列表方法
	protected function _list($table, $where= null, $order = null){
		$list = $this -> _get_list($table, $where, $order);
		$this -> assign('list', $list);
		$this -> assign('page', $this -> data['page']);
		$this -> display();
	}
	
	// 获得一个列表,返回而不输出
	protected function _get_list($table, $where= null, $order = null){
		$model = M($table);
		$count = $model -> where($where) -> count();
		$page = new \Think\Page($count, 25);
		if(!$order){
			$order = "id desc";
		}
		$list = $model -> where($where) -> limit($page -> firstRow . ',' . $page -> listRows ) -> order($order) -> select();
		
		// 将数据保存到成员变量
		$this -> data = array(
			'list' => $list,
			'page' => $page -> show(),
			'count' => $count
		);
		
		return $list;
	}
	
	public function tplmsg(){
		$tplmsg = new \Common\Util\tplmsg();
		if(IS_POST){
			if($_POST['act'] == 'init'){
				$_POST = $tplmsg -> init();
				$this -> _save(false);
				if($_POST){
					$this -> success('成功获取了' .count($_POST). '个模板ID');
				}
				else{
					$this -> error(implode("\r\n",$tplmsg -> errmsg));
				}
				exit;
			}
			elseif($_POST['act'] == 'switch'){
				$GLOBALS['_CFG']['tplmsg'][$_POST['id']]['status'] = $_POST['status'] == 1 ? 1: 0;
				$_POST = $GLOBALS['_CFG']['tplmsg'];
				$this -> _save();
				$this -> success('操作成功！');
				exit;
			}
			
		}
		$this -> display();
	}
	
	//代理资料设置
	public function Asite(){
		$this->_save();
		$this->display();
	}
	
	private function _save($exit = true){
		// 通用配置保存操作
		if(IS_POST){
			// 有此配置则更新,没有则新增
			if(array_key_exists(ACTION_NAME, $this -> _CFG)){
				M('config') -> where(array('name' => ACTION_NAME)) -> save(array(
					'value' => serialize($_POST)
				));
			}else{
				M('config') -> add(array(
					'name' => ACTION_NAME,
					'value' => serialize($_POST)
				));
			}
			if($exit){
				$this -> success('操作成功！');
				exit;
			}
		}
	}
	
	
	// 通用编辑方法,根据POST自动增加或者修改
	protected function _edit($table, $url = null){
		$model = M($table);
		
		$id = intval($_GET['id']);
		if($id>0){
			$info = $model -> find($id);
			if(!$info)
				die('信息不存在');
			
			$this -> assign('info', $info);
		}
		if(IS_POST){
			if(!$url)
				$url = U('index');
			if($id>0){
				$_POST['id'] = $id;
				$model -> save($_POST);
				$this -> success('操作成功！', $url);
				exit;
			}else{
				$model -> add($_POST);
				$this -> success('添加成功！', $url);
				exit;
			}
		}
		
		$this -> display();
	}
	
	// 通用删除
	protected function _del($table,$id){
		if($id>0 && !empty($table)){
			M($table) -> delete($id);
		}
	}
	
	// 商品列表
	public function tpl(){
		$this->_list('tpl');
	}
	
	// 编辑、添加商品
	public function edit(){
		$this->_edit('tpl',U('tpl'));
	}
	
	
	
	// 删除商品
	public function del(){
		$this -> _del('tpl', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	//
	public function member(){
		$this -> _list('member');
	}
	
	public function editMember(){
		if(IS_POST){
			if(!$_POST['id']){
				if(M('member')->where(array('mobile'=>$_POST['mobile']))->find()){
					$this->error('代理手机号码已被添加');
					exit;
				}
				$_POST['salt'] = Salt();
				$_POST['imei'] = xmd5($_POST['salt']);
				$_POST['create_time'] = NOW_TIME;
				
				$qrcode = $this->qrcode($_POST['imei']);
				$_POST['url'] = $qrcode['url'];
				$_POST['qrcode'] = $qrcode['qrcode'];
				$_POST['password'] = xmd5($_POST['tpassword']);
				M('member')->add($_POST);
			}else{
				$_POST['password'] = xmd5($_POST['tpassword']);
				M('member')->where(array('id'=>$_POST['id']))->save($_POST);
			}
			$this->success('操作成功！',U('member'));
			exit;
			
		}
		if($_GET['id']>0){
			$this->assign('info',M('member')->find(intval($_GET['id'])));
		}
		$this->display();
	}
	
}?>