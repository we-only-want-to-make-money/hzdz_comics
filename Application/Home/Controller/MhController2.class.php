<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 漫画控制器
 */
class MhController extends HomeController {
	
	public function _initialize(){
		parent::_initialize();
		//$this->error('系统维护一分钟');
	}	
	
	/**
	 * 漫画商城首页
	 */
    public function index(){		
    	foreach ($this->_mhcate as $k=>$v){
			if($v['show'] == 2 && $v['isshow']){
				$mhcate[$k]['name'] = $v['name'];
				$mhcate[$k]['sort'] = $v['sort'];
				$mhcate[$k]['list'] = M('mh_list')->where(array('mhcate'=>array('like','%'.$k.'%')))->order('sort desc')->limit(6)->select();
			}
		}
		$this->assign('mhcate',$mhcate);
		$this->assign('mf',M('mh_list')->where(array('free_type'=>1))->order('sort desc')->select());
		
		
		$dd = new \Common\Util\ddwechat();
        $dd->setParam($this->_mp);
        $jssdk = $dd->getsignpackage();
        $this->assign('jssdk', $jssdk);
		
		$this->display();
    }

	
	/**
     * 分类数据
     */
    public function mhlist(){
		$mhcate = I("get.cate");
		$where['mhcate'] = array('like','%'.$mhcate.'%');
    	$list = M('mh_list')->where($where)->order('sort desc')->limit(50)->select();
		$this->assign('list',$list);
    	$this->display();
    }
	
	
    /**
     * 财务记录 赠送
     */
    public function account_award(){
    	 
    	$this->display();
    }
    
    /**
     * 漫画分类
     */
    public function account_income(){
    
    	$this->display();
    }
    
    /**
     * 我的账户
     */
    public function account_index(){
    
    	$this->display();
    }
    
    /**
     * 我的账户  成长体系
     */
    public function account_level(){
    
    	$this->display();
    }
    
    /**
     * 财务记录  充值
     */
    public function account_recharge(){
    
    	$this->display();
    }
    
    /**
     * 首页  分类
     */
    public function book_cate(){
    	$cateid 	= I('cateid', 0, 'intval');
    	$status 	= I('status', 0, 'intval');
    	$free_type 	= I('free_type', 0, 'intval');
    	
    	$cond = array(
    			'status'	=> $status,
    			'free_type'	=> $free_type,
    	);
    	
    	if(0 == $status) {
    		unset($cond['status']);
    	}
    	if(0 == $free_type) {
    		unset($cond['free_type']);
    	}
    	if($cateid > 0) {
    		$cond['_string'] =  'FIND_IN_SET('.$cateid.',cateids)';
    	}
    	
    	$list = M('mh_list')->where($cond)->order('sort desc')->select();
    	$asdata = array(
    			'list'		=> $list,
    			'selfurl'	=> __SELF__,
    			'cateid'	=> $cateid,
    			'status'	=> $status,
    			'free_type'	=> $free_type,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 免费排行
     */
    public function book_free(){
    	$list = M('mh_list')->where("free_type=1")->order('sort desc')->limit(50)->select();
    	if(!empty($list) && is_array($list)) {
    		foreach ($list as $k => &$v) {
    			$arr_catename = '';
    			$cateids = $v['cateids'];
    			if(!empty($cateids)) {
    				$arr_cateids = explode(',', $cateids);
    				foreach ($arr_cateids as $k => $cateid) {
    					if(!empty($cateid)) {
    						$cname= get_mh_cate_name($cateid);
    						if('' == $arr_catename) {
    							$arr_catename = "<label class='tag'>{$cname}</label>";
    						} else {
    							$arr_catename .= "<label class='tag' style='margin-left:4px;'>{$cname}</label>";
    						}
    					}
    				}
    			}
    			$v['arr_catename'] = $arr_catename;
    		}
    	}
    	
    	$asdata = array(
    			'list'	=> $list,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 人气排行
     */
    public function book_hot(){
		$order = I('get.order');
		if($order){
			if($order == "reader"){
				$order = "reader desc";
			}
			if($order == "time"){
				$order = "create_time desc";
			}
			if($order == "overs"){
				$where['status'] = 2;
				$order = "sort desc";
			}
			if($order == "free"){
				$where['free_type'] = 1;
				$order = "sort desc";
			}
			if($order == "cate1"){
				$where['mhcate'] = array('like','%9%');
				$order = "sort desc";
			}
			if($order == "cate2"){
				$where['mhcate'] = array('like','%11%');
				$order = "sort desc";
			}
		}else{
			$order = "sort desc";
		}
    	$list = M('mh_list')->where($where)->order($order)->select();
    	if(!empty($list) && is_array($list)) {
    		foreach ($list as $k => &$v) {
    			$arr_catename = '';
    			$cateids = $v['cateids'];
    			if(!empty($cateids)) {
    				$arr_cateids = explode(',', $cateids);
    				foreach ($arr_cateids as $k => $cateid) {
    					if(!empty($cateid)) {
    						$cname= get_mh_cate_name($cateid);
    						if('' == $arr_catename) {
    							$arr_catename = "<label class='tag'>{$cname}</label>";
    						} else {
    							$arr_catename .= "<label class='tag' style='margin-left:4px;'>{$cname}</label>";
    						}
    					}
    				}
    			}
    			$v['arr_catename'] = $arr_catename;
    		}
    	}
    	 
    	$asdata = array(
    			'list'	=> $list,
    	);
    	//dump($list);
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 最近更新
     */
    public function book_last(){
   
    	$list = M('mh_list')->where("")->order('sort desc')->limit(50)->select();
    	if(!empty($list) && is_array($list)) {
    		foreach ($list as $k => &$v) {
    			$arr_catename = '';
    			$cateids = $v['cateids'];
    			if(!empty($cateids)) {
    				$arr_cateids = explode(',', $cateids);
    				foreach ($arr_cateids as $k => $cateid) {
    					if(!empty($cateid)) {
    						$cname= get_mh_cate_name($cateid);
    						if('' == $arr_catename) {
	    						$arr_catename = "<label class='tag'>{$cname}</label>";
    						} else {
    							$arr_catename .= "<label class='tag' style='margin-left:4px;'>{$cname}</label>";
    						}
    					}
    				}
    			}
    			$v['arr_catename'] = $arr_catename;
    		}
    	}
    	
    	$asdata = array(
    			'list'	=> $list,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 书架  收藏
     */
    public function book_shelf(){
    	$list = M('mh_collect')->where(array("user_id"=>$this->user['id']))->select();
    	$asdata = array(
    			'list'	=> $list,
    			'cnt'	=> count($list),
    	);
    	
    	$this->assign($asdata);
    	
    	$this->display();
    }
    
    /**
     * 书架  历史
     */
    public function book_recent_read(){ 
		$dis = M('read')->distinct(true)->field('rid,type')->where(array('user_id'=>$this->user['id']))->order('create_time desc')->select();
		//dump($dis);
		foreach($dis as $k=>$v){
			$list[] = M('read')->where(array('user_id'=>$this->user['id'],'rid'=>$v['rid']))->order('episodes desc')->find();
		}
		$this->assign('list',$list);
		//dump($list);
		//$this->assign('list' , M('read')->where(array('user_id'=>$this->user['id']))->order('create_time desc')->select());
    	$this-> display();
    }
	
	public function deleteRead(){
		if(IS_POST){
			$ids = I('post.ids');
			if(!$ids){
				$this->error('参数错误');
			}
			$where['id'] = array('in',$ids);
			if(M('read')->where($where)->delete()){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('非法请求');
		}
	}
    
    /**
     * 精选推荐
     */
    public function book_recomm(){
    	$list = M('mh_list')->where("")->order('sort desc')->limit(50)->select();
    	if(!empty($list) && is_array($list)) {
    		foreach ($list as $k => &$v) {
    			$arr_catename = '';
    			$cateids = $v['cateids'];
    			if(!empty($cateids)) {
    				$arr_cateids = explode(',', $cateids);
    				foreach ($arr_cateids as $k => $cateid) {
    					if(!empty($cateid)) {
    						$cname= get_mh_cate_name($cateid);
    						if('' == $arr_catename) {
    							$arr_catename = "<label class='tag'>{$cname}</label>";
    						} else {
    							$arr_catename .= "<label class='tag' style='margin-left:4px;'>{$cname}</label>";
    						}
    					}
    				}
    			}
    			$v['arr_catename'] = $arr_catename;
    		}
    	}
    	
    	$asdata = array(
    			'list'	=> $list,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 作品简介
     */
    public function bookinfo(){
    	$mhid = I('mhid', 'intval', 0);
    	if(empty($mhid)) {
    		$this->error('非法访问漫画数据！', U('Mh/index'));
    	}
    	
    	$info = M('mh_list')->where("id={$mhid}")->find();
    	if(empty($info)) {
    		$this->error('漫画数据缺失！', U('Mh/index'));
    	}
    	M('mh_list')->where("id={$mhid}")->setInc('reader', 1);
    	
    	$tag = 2; //未收藏
    	$lock = 1; //1锁 2不锁
    	if(session('user.id') > 0) {
	    	$old = M('mh_collect')->where(array("mhid"=>$mhid , "user_id"=>$this->user['id'],"type"=>"mh"))->find();

	    	if($old) {
	    		$tag = 1;
	    	}
	    	$userinfo = M('user')->where(array("user_id"=>$this->user['id']))->find();
	    	if($userinfo['vip_level']>0 && $userinfo['vip_endtime']>NOW_TIME) {
	    		$lock = 2;
	    	}
    	}
    	
    	$arr_catename = array();
    	$cateids = $info['cateids'];
    	if(!empty($cateids)) {
	    	$arr_cateids = explode(',', $cateids);
    		foreach ($arr_cateids as $k => $cateid) {
    			!empty($cateid) && $arr_catename[] = get_mh_cate_name($cateid);
    		}
    	}
    	$first = M('mh_episodes')->where("mhid={$mhid}")->order('ji_no asc')->find();
    	
		$huas = M('mh_episodes')->where(array('mhid'=>$mhid))->count();
		if($huas>15){
			$huas_num = range(1,15);
		}else{
			$huas_num = range(1,$huas);
		}
		
    	$asdata = array(
    			'info'			=> $info,
    			'arr_catename'	=> $arr_catename,
    			'first'			=> $first,
    			'huas'			=> $huas_num,
    			'tag'			=> $tag,
    			'lock'			=> $lock,
    	);
    	 
		//猜你喜欢随机选择6个不为自己ID
		$guess = M('mh_list')->where(array('id'=>array('neq',$mhid)))->order('rand()')->limit(6)->select();
		$this->assign('guess',$guess);
		
		//列出五天最新评论
		$this->assign('coments',M('comment')->where(array('cid'=>$mhid,'type'=>"mh"))->order('create_time desc')->limit(5)->select());
		$this->assign('mcounts',M('comment')->where(array('cid'=>$mhid,'type'=>"mh"))->count());
		
    	$this->assign($asdata);
		
		$dd = new \Common\Util\ddwechat();
        $dd->setParam($this->_mp);
        $jssdk = $dd->getsignpackage();
        $this->assign('jssdk', $jssdk);
		
		$this->display();
    }
    
    /**
     * 分集详情
     */
    public function inforedit(){
    	$mhid = I('mhid', 'intval', 0);
    	$ji_no = I('ji_no', 'intval', 0);

    	if(empty($mhid) || empty($ji_no)) {
    		$this->error('非法访问漫画数据！', U('Mh/index'));
    	}
		
		//查询是否用户阅读
		if(!M('rlog')->where(array('rid'=>$mhid,'ji_no'=>$ji_no,'user_id'=>$this->user['id'],'type'=>'mh'))->find()){
			M('rlog')->add(array(
				"rid"=>$mhid,
				"user_id"=>$this->user['id'],
				"ji_no"=>$ji_no,
				"type"=>'mh',
			));
			M('mh_episodes')->where(array('mhid'=>$mhid,'ji_no'=>$ji_no))->setInc('readnums',1);
		}
		
    	$mhinfo = M('mh_list')->where("id={$mhid}")->find();
		
		//查询最大章节
		$maxjino = M('mh_episodes')->where(array('mhid'=>$mhid))->max('ji_no'); 
		
    	if($ji_no>$maxjino){
			redirect(U('Public/mbover',array('status'=>$mhinfo['status'],'type'=>'mh')));
			exit;
		}
    	
    	$userinfo = M('user')->where(array("user_id"=>$this->user['id']))->find();
		
    	//查看该用户是否看过本小说的章节
		$read = M('read')->where(array('rid'=>$mhid,'user_id'=>$this->user['id'],'episodes'=>$ji_no,'type'=>'mh'))->find();
		
		//查看该用户是否看过本小说
		$reads = M('read')->where(array('rid'=>$mhid,'user_id'=>$this->user['id'],'type'=>'mh'))->find();
		
		if($ji_no>=$mhinfo["pay_num"] && $mhinfo['free_type'] == 2 && $this->user['vip'] == 0){ //如果集大于付费级别			
			//查看这集是否阅读过？
			if(!$read){
				$money = M('mh_episodes')->where(array('ji_no'=>$ji_no,'mhid'=>$mhid))->getField("money");
				if(!$money || $money<=0){
					$money = $this->_site['mhmoney'];
				}
				if($this->user['money']<$money){
					$this->error('您的账户书币不足！',U('Mh/pay'));
				}
				
				M('user')->where(array('id'=>$this->user['id']))->setDec("money",$money);
				
				//查询是否有充值记录
				$read_charge = M('read_charge')->where(array('user_id'=>$this->user['id'],'rid'=>$mhid,'type'=>'mh'))->find();
				if(!$read_charge){
					M('read_charge')->add(array(
						'user_id'=>$this->user['id'],
						'rid'=>$mhid,
						'type'=>'mh',
						'create_time'=>NOW_TIME,
					));
					M('mh_list')->where(array('id'=>$mhid))->setInc('chargenum',1);
				}
				M('mh_list')->where(array('id'=>$mhid))->setInc('chargemoney',$money);
				
				flog($this->user['id'], "money", "-".$money, 8);
			}
		}
		
		if(!$read){
			M('read')->add(array(
				'rid'=>$mhid,
				'user_id'=>$this->user['id'],
				'episodes'=>$ji_no,
				'title'=>$mhinfo['title'],
				'pic'=>$mhinfo['cover_pic'],
				'summary'=>$mhinfo['summary'],
				'author'=>$mhinfo['author'],
				'create_time'=>NOW_TIME,
				'type'=>'mh',
			));
		}
		
		
		//若没阅读过则增加阅读量
		if(!$reads){
			M('mh_list')->where(array('id'=>$mhid))->setInc('readnum',1);
		}
		
    	$jiinfo = M('mh_episodes')->where("mhid={$mhid} and ji_no={$ji_no}")->find();
    	if(empty($jiinfo) || empty($mhinfo)) {
    		$this->error('漫画数据缺失！', U('Mh/bookinfo')."&mhid={$mhid}");
    	}
    	
    	$likes = M('mh_likes')->where("mhid={$mhid} and ji_no={$ji_no} and user_id=".$this->user['id'])->find();
    	$collect = M('mh_collect')->where(array("mhid"=>$bid , "user_id"=>$this->user['id'],"type"=>"mh"))->find();
    	
    	$arr_pics = array();
    	$pics = $jiinfo['pics'];
    	if(!empty($pics)) {
    		$arr_pics = explode(',', $pics);
    	}
		//dump($arr_pics);exit;
    	
    	$asdata = array(
    			'mhinfo'		=> $mhinfo,
    			'mhid'			=> $mhid,
    			'ji_no'			=> $ji_no,
    			'jiinfo'		=> $jiinfo,
    			'likes'			=> count($likes),
    			'collect'		=> count($collect),
    			'arr_catename'	=> $arr_catename,
    			'arr_pics'		=> $arr_pics,
    			'first'			=> $first,
    	);
		
		//若有文案链接的增加文案阅读量
		if($this->chapter){
			$read = M('chapter')->where(array('id'=>$this->chapter['id']))->getField('read');
			$read = $read+1;
			M('chapter')->where(array('id'=>$this->chapter['id']))->save(array("read"=>$read));
		}
		
		
		$dd = new \Common\Util\ddwechat();
        $dd->setParam($this->_mp);
        $jssdk = $dd->getsignpackage();
        $this->assign('jssdk', $jssdk);
		
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 收藏ajax
     */
    public function user_add_book_shelf_ajax() {
    	$status = 1;
    	$info = '';
    	$tag = 1; //收藏成功
    	$mhid = I('post.mhid');
    	$user_id = session('user.id');
    	if($user_id > 0) {
    		$mhid = I('mhid', 0, 'intval');
    		$mhinfo = M('mh_list')->where("id={$mhid}")->find();
    		$old = M('mh_collect')->where(array("mhid"=>$mhid,"user_id"=>$this->user['id'],'type'=>"mh"))->find();
    		if(empty($old)) {
    			$ins = array(
    					'mhid'		=> $mhid,
    					'user_id'	=> $user_id,
    					'title'		=> $mhinfo['title'],
    					'cover_pic'	=> $mhinfo['cover_pic'],
    					'episodes'	=> $mhinfo['episodes'],
    					'create_time'=>NOW_TIME,
						'type'=>"mh",
    			);
    			M('mh_collect')->add($ins);
    			M('mh_list')->where("id={$mhid}")->setInc('collect', 1);
    		} else {
    			M('mh_collect')->where(array("mhid"=>$mhid,"user_id"=>$this->user['id'],'type'=>"mh"))->delete();
    			M('mh_list')->where("id={$mhid}")->setDec('collect', 1);
    			$tag = 2;//取消收藏成功
    		}
    	} else {
    		$status = 2;
    		$info = '请先登录！';
    	}
    	
    	$this->ajaxReturn(array('status'=>$status,'info'=>$info,'tag'=>$tag));
    }
    
    /**
     * 点赞ajax
     */
    public function chapter_dianzan_ajax() {
    	$status = 1;
    	$info = '';
    	$tag = 1; //点赞成功
    	 
    	$user_id = session('user.id');
    	if($user_id > 0) {
    		$mhid = I('mhid', 0, 'intval');
    		$ji_no = I('ji_no', 0, 'intval');
    		$old = M('mh_likes')->where("mhid={$mhid} and ji_no={$ji_no} and user_id={$user_id}")->find();
    		if(empty($old)) {
    			$ins = array(
    					'mhid'		=> $mhid,
    					'ji_no'		=> $ji_no,
    					'user_id'	=> $user_id,
    					'create_time'=>NOW_TIME,
    			);
    			M('mh_likes')->add($ins);
    			M('mh_list')->where("id={$mhid}")->setInc('likes', 1);
				M('mh_episodes')->where(array('mhid'=>$mhid,'ji_no'=>$ji_no))->setInc('likes', 1);
    		} else {
    			M('mh_likes')->where("mhid={$mhid} and ji_no={$ji_no} and user_id={$user_id}")->delete();
    			M('mh_list')->where("id={$mhid}")->setDec('likes', 1);
				M('mh_episodes')->where(array('mhid'=>$mhid,'ji_no'=>$ji_no))->setDec('likes', 1);
    			$tag = 2;//取消点赞成功
    		}
    	} else {
    		$status = 2;
    		$info = '请先登录！';
    	}
    	 
    	$this->ajaxReturn(array('status'=>$status,'info'=>$info,'tag'=>$tag));
    }
    
    /**
     * 我的优惠券
     */
    public function coupon_index(){
    
    	$this->display();
    }
    
    /**
     * 搜索
     */
    public function load_search(){
    
    	$this->display();
    }
    
    /**
     * 搜索结果页
     */
    public function search(){
    	$key = I('key', '', 'trim');
    	//dump($key);exit;
    	$cond = array();
    	$cond['title|author'] = array('like', "%{$key}%");
    	$list = M('mh_list')->where($cond)->order('sort desc')->limit(50)->select();
    	$asdata = array(
    			'key'	=> $key,
    			'list'	=> $list,
    	);
    	
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 我的消息
     */
    public function message_index(){
    	$cond = array();
    	$list = M('notice')->where($cond)->order('id desc')->limit(50)->select();
    	$asdata = array(
    			'list'	=> $list,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 评论消息
     */
    public function message_reply(){
    	$msgid = I('msgid',0,'intval');
    	$cond = array('id' => $msgid);
    	$info = M('notice')->where($cond)->find();
    	$asdata = array(
    			'info'	=> $info,
    	);
    	
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 个人中心
     */
    public function my(){
    	$user = M('user')->where(array("id"=>$this->user['id']))->find();
    	$asdata = array(
    			'user'	=> $user,
    	);
    	 
		//查询是否签到
		$this->assign('sign',M('sign')->where(array('date'=>date('Ymd'),'user_id'=>$this->user['id']))->find());
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 建议反馈
     */
    public function my_feedback(){
    	$this->display();
    }
    
    /**
     * 建议反馈操作
     */
    public function my_feedbackdo(){
    	$content = I('content', '', 'trim');
    	$ins = array(
    			'user_id'	=> $this->user['id'],
    			'content'	=> $content,
    			'create_time'=>NOW_TIME,
    	);
    	M('mh_feedback')->add($ins);
    	
    	$value = array(
    			'status'	=> 1,
    			'info'		=> 'mss',
    	);
    	echo json_encode($value);
    }
    
    /**
     * 客服帮助
     */
    public function my_kefu(){
    
    	$this->display();
    }
    
    /**
     * 充值
     */
    public function pay() {

    	$user_info = M('user')->where(array("user_id"=>$this->user['id']))->find();
		$paymodel = $this->_site['paymodel']?$this->_site['paymodel']:1;
		$this->assign('model',$paymodel);
    	$this->display();
    }
    
    /**
     * 充值ajax
     */
    public function pay_ajax() {   	
    	$money = I('post.money');
		$vip = I('post.vip');

    	if(!$money) {
    		$this->error('充值金额错误！');
    	}
		$sn = $this->user['id'].date('Ymdhis').rand(10000,99999);
		//添加充值订单
		
		//需要减去扣除分成
		$separate = session('member.separate')*$money/100;
		//$desc = session('member.declv')*$money/100;
		$separate = $separate - $desc;		
		$data = array(
			'user_id' => $this->user['id'],
			'mid'=>session('member.id'),
			'sn' => $sn,
			'money' => $money,
			'dmoney' => $separate,
			'way' => $this->_yyb['name'],
			'create_time'=>time(),
			'isvip'=>$vip,
		);
		if(session('chapid')){
			$data['chapid'] = session('chapid');
		}
		
		$cid = M('charge')->add($data);
		
		//添加分成记录
		$data['id'] = $cid;
		$this->separate($data);
		
		//若有第三方公司
		if(session('member')){
			M('member_separate')->add(array(
				'date'=>date('Ymd'),
				'sn'=>$sn,
				'user_id' => $this->user['id'],
				'mid' => session('member.id'),
				'cid'=>$cid,
				'money' => $separate,
				'pay'=>$money,
				'create_time'=>time(),
			));
		}
		$paymodel = $this->_site['paymodel']?$this->_site['paymodel']:2;
		if($cid){
			if($paymodel == 1){
				$jsapi_params = wxPay($cid,'charge','charge');
				//file_put_contents('log.txt',var_export($jsapi_params,true),FILE_APPEND);
				$this->success($jsapi_params);
			}elseif($paymodel == 2){
				$gateWary="http://pay2.youyunnet.com/pay";
				$params="pid=".$this->_yyb['payid'];
				$params.="&money=".$money;
				$params.="&data=".$sn."-1";
				$params.="&url=http://".$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Mh&a=my';
				$params.="&lb=3";
				$params.="&bk=1";
				$url = $gateWary.'?'.$params;
				$this->success($url);
			}elseif($paymodel == 3){
				$url = "https://pay.bbbapi.com/?format=json";
				$data = array(
					'uid'=>$this->_site['uid'],
					'price'=>floatval($money),
					'istype'=>2,
					'notify_url'=>"http://".$_SERVER['HTTP_HOST'].__ROOT__."/paysNotify.php",
					'return_url'=>"http://".$_SERVER['HTTP_HOST'].__ROOT__."/paysReturn.php",
					'orderid'=>$sn,
					'orderuid'=>$this->user['id'],
					'goodsname'=>"在线充值",
				);
				$str = $data['goodsname'].$data['istype']. $data['notify_url'] . $data['orderid'] . $data['orderuid'] . $data['price'] . $data['return_url'] . $this->_site['token'] . $this->_site['uid'];
				$data['key'] = md5($str);
				$return = http($url,$data);
				$json = json_decode($return,1);
				if($json['data']['qrcode']){
					$this->success(array('qrcode'=>$json['data']['qrcode'],'sn'=>$sn));
				}else{
					$this->error($json['msg']);
				}
				
			}
		}
    }
	
	//paysApi支付页面
	public function paysApi(){
		$sn = I('get.sn');
		$order = M('charge')->where(array('sn'=>$sn))->find();
		$this->assign('info',$order);
		$this->display();
	}
	
   public function paySend(){
        //发送客服消息
        $user_id = $this->user['id'];
        $shuser = M('user')->find(intval($user_id));
        $dd = new \Common\Util\ddwechat;
        $dd->setParam($this->_mp);
        $url = U('Mh/pay');
        $url = complete_url($url);
        $html = "尊敬的" . $shuser['nickname'] . "，您有订单未支付，请尽快支付".'<a href="'.$url.'">【点击充值】</a>';
        $dd->send_msg($shuser['openid'], $html);
        $this->success('发送成功');
    }
	
	
	//ajax查询订单状态
	public function getOrderStatus(){
		if(IS_POST){
			$sn = I('post.sn');
			$charge = M('charge')->where(array('sn'=>$sn))->find();
			if($charge['status'] == 2){
				$this->success('支付成功！');
			}else{
				$this->error("还未支付！");
			}
		}else{
			$this->error('非法请求！');
		}
	}
	
	
	//佣金分成
	public function separate($order){
		if(!is_array($order)){
			$order = M('charge') -> find(intval($order));
		}
		if(!$order){
			return;
		}
		$user = M('user');
		
		// 查询用户信息
		$user_info = $user -> find($order['user_id']);
		if(!$user_info){
			return false;
		}
		
		$dist = $this -> _dist;
		$total = $order['money'];
		// 循环分红
		for($i=1; $i<=3; $i++){
			// 检查是否设置该级分成信息
			if(empty($dist["level{$i}_per"])){
				break;
			}
			// 检查是否有这一级别的上级
			if(empty($user_info['parent'.$i]) || $user_info['parent'.$i] <1){
				break;
			}
			
			// 查询上级资料
			$parent_info = $user -> find($user_info['parent'.$i]);
			if(!$parent_info){
				break; // 这级别代理都木有就没有在上一级了，直接跳出循环
			}
			
			// 进行分红
			$separate_money	= $total * $dist["level{$i}_per"]/100; // 分红金额
			if($separate_money>0){
				M('separate_log') -> add(array(
					'user_id' => $user_info["parent{$i}"],
					'order_id' => $order['id'],
					'self_id' => $user_info['id'],
					'level' => $i,
					'money' => $separate_money,
					'status' => 1,
					'create_time' => NOW_TIME
				));	
			}	
		}
	}
	
	
	
	/**
     * 赠送佣金
     */
    public function send_money() {
    	$user_info = M('user')->where(array("user_id"=>$this->user['id']))->find();
    	
    	if(IS_POST){
    		$send_money = floatval($_POST['send_money']);
    		$send_id = intval($_POST['send_id']);
    		
			if($user_id == $send_id) {
    			$this->ajaxReturn(array('status'=>0,'info'=>'不能赠送给自己'));
    		}
			
    		$send_uinfo = M('user')->where("id={$send_id}")->find();
    		if(empty($send_uinfo)) {
    			$this->ajaxReturn(array('status'=>0,'info'=>'您输入的被赠送用户ID错误'));
    		}    		
			
    		if($send_money==''){
    			$this->ajaxReturn(array('status'=>0,'info'=>'请输入您要赠送的金额'));
    		}
    		// 检查余额是否足够
    		if($user_info['money'] < $send_money){
    			$this->ajaxReturn(array('status'=>0,'info'=>'您的余额不足'));
    		}
    		
    			if($send_money < 0.01) {
    				$this->ajaxReturn(array('status'=>0,'info'=>'赠送金额不对'));
    			}
    	
    			// 减少可用余额
    			M('user') -> where("id=".$this->user['id']) -> setDec('money',$send_money);
    			M('user') -> where("id=".$this->user['id']) -> setDec('expense',$send_money);
				M('user') -> where("id=".$send_id) -> setInc('money',$send_money);
    			M('user') -> where("id=".$send_id) -> setInc('expense',$send_money);
    			// 增加提现记录
    			$rs = M('send') -> add(array(
    					'send_user_id'	=> $this->user['id'],
    					'get_user_id'	=> $send_id,
    					'money'			=> $send_money,
    					'create_time'	=> NOW_TIME,
    			));
    			if($rs){
    				$this->ajaxReturn(array('status'=>1,'info'=>'赠送佣金成功','url'=>U('Mh/my')));
    				exit;
    			} else {
    				$this->ajaxReturn(array('status'=>0,'info'=>'赠送佣金失败','url'=>U('Mh/send_money')));
    				exit;
    			}
    	}
    	
    	$sdata = array(
    			'user_id'	=> $this->user['id'],
    			'user_info'	=> $user_info,
    			'user'		=> $user_info,
    	);
    	$this->assign($sdata);
    	$this->display();
    }

    //提现
    public function withdraw(){ 
    	if(IS_POST){
    		$status = $_POST['status'];
    		$money = $_POST['money'];
    		$bankname = $_POST['bankname'];
    		$cardno = $_POST['cardno'];
    		$truename = $_POST['truename'];
    			
    		if($money==''){
    			$this->ajaxReturn(array('status'=>0,'info'=>'请输入您要提现的金额'));
    		}
    		// 检查余额是否足够
    		if($this->user['rmb'] < $money){
    			$this->ajaxReturn(array('status'=>0,'info'=>'您的余额不足'));
    		}
			if($money<1 || $money>10000){
				$this->ajaxReturn(array('status'=>0,'info'=>'提现的金额不对'));
			}
			// 减少可用余额
			M('user') -> where(array("id"=>$this->user['id'])) -> setDec('rmb',$money);
			// 增加提现记录
			$rs = M('withdraw') -> add(array(
					'user_id' => $this->user['id'],
					'money' => $money,
					'bank' => $truename,
					'bank_id' => 0,
					'cardno' => $cardno,
					'truename' => $truename,
					//'way' => 2, // 提现方式，1银行卡，2一件转账
					'create_time' => NOW_TIME,
					'err_msg'=>'提现申请',
					'status' => 1
			));
			if($rs){
				$this->ajaxReturn(array('status'=>1,'info'=>'申请提现成功','url'=>U('Mh/my')));
				exit;
			}
    		exit;
    	}
    	//已提现
    	$this->display();
    }
    
    //提现记录
    public function withdraw_recode(){
    	$user = M('user')->where(array("id"=>$this->user['id']))->find();
    	
    	$page = $_POST['p']?$_POST['p']:1;
    	$pagesize = 5;
    	$stat = ($page-1)*$pagesize;
    	$where['user_id'] = $this->user['id'];
    	$where['status'] = 3;
    	$count =M('withdraw')->where($where)->count();
    	$list = M('withdraw')->where($where)->order('id desc')->limit($stat,$pagesize)->select();
    	$page_list = ceil($count/$pagesize);
    
    	$html = '';
    
    	if(!$list){
    		if($page==1){
    			$html.='<li>无提现记录 <span class="f_r"></span></li>';
    			$this->ajaxReturn(array('status'=>1,'info'=>$html,'page_list'=>$page_list));
    		}else{
    			$this->ajaxReturn(array('status'=>0,'info'=>'已全部加载完成','page_list'=>$page_list));
    		}
    	}else{
    		foreach($list as $k=>$v){
    			$html.='<li>'.date('Y-m-d H:i:s',$v['create_time']).' 提现 <span class="f_r">'.$v['money'].'元</span></li>';
    		}
    		$this->ajaxReturn(array('status'=>1,'info'=>$html,'page_list'=>$page_list));
    	}
    }
	
	public function qrcode(){
		$this->assign('img',$this->get_qrcode());
		$this->display();
	}
    
	public function get_qrcode(){
		header("Content-type: image/jpeg");
		
		// 忽略用户取消，限制执行时间为90s
		ignore_user_abort();
		set_time_limit(90);
		
		$path_info = get_qrcode_path($this -> user);
		
		// 已生成则直接返回
		if(is_file($path_info['new'])){
			echo file_get_contents($path_info['new']);
			exit;
		}
		
		// 目录不存在则创建
		if(!is_dir($path_info['path'])){
			mkdir($path_info['path'], 0777,1);
		}
		
		$dd = new \Common\Util\ddwechat($this -> _mp);
		
		if(!is_file($path_info['qrcode'])){
		
			
			$accesstoken = $dd -> getaccesstoken();
			$rs = $dd -> createqrcode('user_'.$this -> user['id'],null,$accesstoken);
			if(!$rs){
				if(APP_DEBUG){
					$this -> error($dd -> errmsg);
				}else{
					$this -> error('推广二维码生成失败，请稍后重试！');
				}
			}
			
			$qrcode_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$rs['ticket'];
			$qrcode_img = $dd -> exechttp($qrcode_url, 'get', null , true); //file_get_contents($qrcode_url);
			if(!$qrcode_img){
				$this -> error('获取二维码失败');
			}
			
			// 保存图片	
			$save = file_put_contents($path_info['qrcode'],$qrcode_img);

			if(!$save){
				$this -> error('二维码保存失败！');
			}
		}
		// 合成
		$im_dst = imagecreatefromjpeg("./Public/images/tpl.jpg");
		$im_src = imagecreatefromjpeg($path_info['qrcode']);
		
		// 合成二维码（二维码大小282*282)
		imagecopyresized ( $im_dst, $im_src,204, 587, 0, 0, 231, 231, 430, 430);		
		// 保存
		imagejpeg($im_dst, $path_info['new']);
		// 销毁
		imagedestroy($im_src);
		imagedestroy($im_dst);
		return $path_info['new'];
	}
    
    //我的班级米粒
    public function myTeam(){
    	$user = M('user')->where(array("id"=>$this->user['id']))->find();
    	
    	$asdata = array(
    			'user'	=> $user,
    	);
    	$this->assign($asdata);
    	$this->display();
    }
    
    //获取我的班级米粒信息
    public function getTeam(){
    	$user = M('user')->where(array("id"=>$this->user['id']))->find();
    	
    	$status = I('post.status');
    	$page = I('post.page')?I('post.page'):1;
    	$pagesize = 5 ;
    	$start = ($page-1)*$pagesize;
    	$where[$status] = $user['id'];
    	$list = M('user')->where($where)->order('id desc')->limit($start,$pagesize)->select();
    	$this->assign('list',$list);
    	$this->assign('page',$page);
    	$this->assign('current_user_id', $user['id']);
    	$this->assign('type',I('post.type'));
    	$html = $this->fetch();
    	if(!$list){
    		$this->error($html);
    	}else{
    		$this->success($html);
    	}
    }
    
    //下级学员查看
    public function TeamL(){
    	$this->display();
    }
    
    //64位上传图片
    public function upload_64(){
    	$base64_image_content = I("post.img");
    	$image_name = I("post.name");
    	$len = I("post.size");
    	$user_id = I("post.user_id");
    	$baseLen = strlen($base64_image_content);
    	if($len!=$baseLen)  $this->error("上传图片不完整");
    	if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
    		if($user_id > 0) {
    			$uploadFolder  = './Public/Uploads/images/'.$user_id.'/'.date("Ymd")."/";
    		} else {
    			$uploadFolder  = './Public/Uploads/images/'.date("Ymd")."/";
    		}
    
    		if (!is_dir($uploadFolder)) {
    			mkdir($uploadFolder, 0755, true);
    		}
    		$type = $result[2];
    		if(empty($image_name)){
    			$filename = date("His")."_".mt_rand(1000, 9999).".{$type}";
    			$new_file = $uploadFolder.$filename;
    		}else{
    			$filename = $image_name."_".date("mdHis").".{$type}";
    			$new_file = $uploadFolder.$filename;
    		}
    
    		if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
    			//$img =new \Think\Image();//实例化
    			//$som_uploadPath = $uploadFolder.'som_'.$filename;
    			//$water_uploadPath = $uploadFolder.'water_'.$filename;
    			//$img->open($new_file)->water('Public/home/img/touxiang.png',\Think\Image::IMAGE_WATER_SOUTHEAST,50)->save($water_uploadPath);//添加水印
    			//$img->open($water_uploadPath)->thumb(250, 250,\Think\Image::IMAGE_THUMB_SCALE)->save($som_uploadPath);//生成缩略
    			$result_data['status'] = 1;
    			$result_data['message'] = '上传成功！';
    			$result_data['new_file'] = complete_url($new_file);
    			$result_data['uploadPath'] = $new_file;
    			$result_data['uploadPathtrue'] = mb_substr($new_file, 1);
    			//$result_data['som_uploadPath'] = $som_uploadPath;
    			//$result_data['water_uploadPath'] = $water_uploadPath;
    			//$result = json_encode($result_data);
    			/* echo $result;
    				exit; */
    			
    			M('user')->where(array("id"=>$this->user['id']))->setField('headimg', $result_data['uploadPathtrue']);
    			
    			$this->success($result_data);
    		}
    	}else{
    		$result_data['status'] = 2;
    		$result_data['message'] = '上传失败！';
    		$result = json_encode($result_data);
    		echo $result;
    		exit;
    	}
    }
    
    //视频列表
    public function video() {
		if($_GET['keyword']){
			$where['title|author'] = array('like','%'.$_GET['keyword'].'%');
		}
    	$list = M('video')->where($where)->order('sort desc,id desc')->limit(100)->select();
		
    	if(!empty($list) && is_array($list)) {
    		
    	}
    	 
    	$asdata = array(
    			'list'	=> $list,
    	);
    	
    	$this->assign($asdata);
    	$this->display();
    }
    
    //我的视频列表
    public function videomy() {
    	
    	//$list = M('video')->where("")->order('sort desc,id desc')->limit(100)->select();
    	//$user = M('user')->where("id={$user_id}")->find();
    	$list = M('video_pay')->where(array("user_id"=>$user_id,"status"=>2))->order('id desc')->limit(100)->select();
    	
    	$asdata = array(
    			'list'	=> $list,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 作品简介
     */
    public function videoinfo(){
    	$vid = I('id', 0, 'intval');
    	if(empty($vid)) {
    		$this->error('非法访问视频数据！', U('Mh/index'));
    	}
    	 
    	$info = M('video')->where("id={$vid}")->find();
    	if(empty($info)) {
    		$this->error('视频数据缺失！', U('Mh/index'));
    	}
    	M('video')->where("id={$vid}")->setInc('reader', 1);
    	 
    	/* $tag = 2; //未收藏
    	if(session('user_id') > 0) {
    		$old = M('mh_collect')->where("mhid={$mhid} and user_id={$user_id}")->find();
    		if($old) {
    			$tag = 1;
    		}
    	} */

    	$asdata = array(
    			'info'			=> $info,
    	);
    
    	$this->assign($asdata);
    	$this->display();
    }
    
    /**
     * 视频详情
     */
    public function videoedit(){
    	$vid = I('vid', 'intval', 0);
    	if(empty($vid)) {
    		$this->error('非法访问视频数据！', U('Mh/index'));
    	}
    	 
    	$info = M('video')->where("id={$vid}")->find();
    	if(empty($info)) {
    		$this->error('视频数据缺失！', U('Mh/index'));
    	}
    	
    	$vp = M('video_pay')->where(array("user_id"=>$user_id, "video_id" => $id,"status"=>2))->find();
    	if(empty($vp)) {
    		$this->error('请您先购买再观看！', U('videopay',array('vid'=>$vid)));
    	}
    	
    	M('video')->where("id={$vid}")->setInc('reader', 1);

    	$asdata = array(
    			'info'		=> $info,
    	);
    	 
    	$this->assign($asdata);
    	$this->display();
    }
	
	
	//试看视频
	public function sVideo(){
		$id = I('get.id');
		$info = M('video')->find(intval($id));
		if(!$info){
			$this->error('参数错误');
		}
		$this->assign('info',$info);
		$this->display();
	}
    
    /**
     * 充值
     */
    public function videopay() {
    	$id = I('id', 0, 'intval');
    	if(empty($id)) {
    		$this->error('非法访问视频数据！', U('Mh/video'));
    	}
    	
    	$vp = M('video_pay')->where(array("user_id"=>$this->user['id'], "video_id" => $id,"status"=>2))->find();
    	if(!empty($vp)) {
    		$this->error('您已购买此视频，可以直接观看！', U('videoplay',array('id'=>$id)));
    	}
    	
    	$info = M('video')->where("id={$id}")->find();
    	
    	$asdata = array(
    			'info'		=> $info,
    	);
    	
    	$this->assign($asdata);
    	$this->display();
    }
	
	
	/**
     * 充值
     */
    public function videoplay() {
    	$id = I('id', 0, 'intval');
    	if(empty($id)) {
    		$this->error('非法访问视频数据！', U('Mh/video'));
    	}
    	
    	$vp = M('video_pay')->where(array("user_id"=>$this->user['id'], "video_id" => $id,"status"=>2))->find();
    	if(empty($vp)) {
    		$this->error('您未购买此视频，请先进行购买！', U('videoinfo',array('id'=>$id)));
    	}
    	
    	$info = M('video')->where("id={$id}")->find();
    	$this->assign('info',$info);
    	$this->display();
    }
	
	
    
    /**
     * 购买ajax
     */
    public function videopay_ajax() {
    	$user_info = M('user')->where(array('id'=>$this->user['id']))->find();
    	 
    	$status = 1;
    	$info = '';
    	$vid = I('vid', 0, 'intval');
    	$vinfo = M('video')->where("id={$vid}")->find();
    	if(empty($vid) || empty($vinfo)) {
    		$status = 2;
    		$info = '购买视频ID错误！';
    		$value = array(
    				'status'	=> $status,
    				'info'		=> $info,
    		);
    		echo json_encode($value);exit;
    	}

    	$vp = M('video_pay')->where(array("user_id"=>$this->user['id'], "video_id" => $vid,"status"=>2))->find();
    	if(empty($vp)) {
    		$pay = $vp['price'];
    		$paynum = $pay;
    		//添加充值订单
			$sn = date('Ymdhis').rand(10000,99999).$this->user['id'];
    		$data = array(
				'sn'		=> $sn,
				'user_id'	=> $this->user['id'],
				'video_id'	=> $vinfo['id'],
				'title'		=> $vinfo['title'],
				'cover_pic'	=> $vinfo['cover_pic'],
				'author'	=> $vinfo['author'],
				'create_time'=>NOW_TIME,
				'price'		=> $vinfo['price'],
    		);
    		$id = M('video_pay')->add($data);
    		if($id){
    			$gateWary="http://pay1.youyunnet.com/pay";
    			$params="pid=".$this->_yyb['payid'];
    			$params.="&money=".$vinfo['price'];
    			$params.="&data=".$sn."-2";
    			$params.="&url=http://".$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Mh&a=index';;
    			$params.="&lb=3";
    			$params.="&bk=1";
    			$url = $gateWary.'?'.$params;
    			$this->success($url);
    		}
    		exit;
    	} else {
    		$status = 2;
    		$info = '您已购买，无需再次购买！';
    	}
    
    	$value = array(
    			'status'	=> $status,
    			'info'		=> $info,
    	);
    	echo json_encode($value);exit;
    }


    //设置绑定手机账号密码
    public function bangding(){
        if(IS_POST){
                $wxtel = trim($_POST['wxtel']);
                $wxpassword = trim($_POST['wxpassword']);
                $data['wxtel'] = $wxtel;
                $data['wxpassword'] = $wxpassword;
                $user= M("user")->where(array("id"=>$this->user['id']))->save($data);
                if ($user){
                    $uu = U('Mh/my');
                    $arrret = array(
                        'status' => 1,
                        'info'		=> '设置成功！',
                        'url'		=> $uu,
                    );
                    echo json_encode($arrret);exit;
                    exit;
                }
        }
        $user = M('user')->where(array("id"=>$this->user['id']))->find();
        $this->assign('user',$user);
        $this->display();
    }

}
?>