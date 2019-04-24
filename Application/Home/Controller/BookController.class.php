<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 漫画控制器
 */
class BookController extends HomeController {
	
	public function _initialize(){
		parent::_initialize();
	}	
	
	
	/**
	 * 小说首页
	 */
    public function index(){
    	foreach ($this->_bookcate as $k=>$v){
			if($v['show'] == 2 && $v['isshow']){
				$bookcate[$k]['name'] = $v['name'];
				$bookcate[$k]['list'] = M('book')->where(array('bookcate'=>array('like','%'.$k.'%')))->order('sort desc')->limit(6)->select();
			}
		}
		$this->assign('bookcate',$bookcate);
		$this->assign('mf',M('book')->where(array('free_type'=>1))->order('sort desc')->select());
		
		$dd = new \Common\Util\ddwechat();
        $dd->setParam($this->_mp);
        $jssdk = $dd->getsignpackage();
        $this->assign('jssdk', $jssdk);
		
		$this->display();
    }
	
	/**
     * 作品简介
     */
    public function bookinfo(){
    	$bid = I('bid', 'intval', 0);
    	if(empty($bid)) {
    		$this->error('非法访问漫画数据！', U('Book/index'));
    	}
    	
    	$info = M('book')->where("id={$bid}")->find();
    	if(empty($info)) {
    		$this->error('漫画数据缺失！', U('Book/index'));
    	}
    	M('book')->where("id={$bid}")->setInc('reader', 1);
    	
    	$tag = 2; //未收藏
    	$lock = 1; //1锁 2不锁
    	if(session('user_id') > 0) {
	    	$old = M('mh_collect')->where(array("mhid"=>$bid , "user_id"=>$this->user['id'],"type"=>"xs"))->find();
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
    	$first = M('book_episodes')->where("bid={$bid}")->order('ji_no asc')->find();
    	
		$huas = M('book_episodes')->where(array('bid'=>$bid))->count();
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
		$guess = M('book')->where(array('id'=>array('neq',$bid)))->order('rand()')->limit(6)->select();
		$this->assign('guess',$guess);
		
		//列出五天最新评论
		$this->assign('coments',M('comment')->where(array('cid'=>$bid,'type'=>"xs"))->order('create_time desc')->limit(5)->select());
		$this->assign('mcounts',M('comment')->where(array('cid'=>$bid,'type'=>"xs"))->count());
		
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
		
    	$bid = I('bid', 'intval', 0);
    	$ji_no = I('ji_no', 'intval', 0);
    	if(empty($bid) || empty($ji_no)) {
    		$this->error('非法访问数据！', U('Book/index'));
    	}
		
		//查询是否用户阅读
		if(!M('rlog')->where(array('rid'=>$bid,'ji_no'=>$ji_no,'user_id'=>$this->user['id'],'type'=>'xs'))->find()){
			/*M('rlog')->add(array(
				"rid"=>$bid,
				"user_id"=>$this->user['id'],
				"ji_no"=>$ji_no,
				"type"=>'xs',
			));*/
			M('book_episodes')->where(array('bid'=>$bid,'ji_no'=>$ji_no))->setInc('readnums',1);
		}
		
		$binfo = M('book')->where("id={$bid}")->find();
		
    	//查询小说最大章节
		$maxjino = M('book_episodes')->where(array('bid'=>$bid))->max('ji_no'); 
		
    	if($ji_no>$maxjino){
			redirect(U('Public/mbover',array('status'=>$binfo['status'],'type'=>'xs')));
			exit;
		}
    	$userinfo = M('user')->where(array("user_id"=>$this->user['id']))->find();
		
		//查看该用户是否看过本小说的章节
		$read = M('read')->where(array('rid'=>$bid,'user_id'=>$this->user['id'],'episodes'=>$ji_no,'type'=>'xs'))->find();
		
		//查看该用户是否看过本小说
		$reads = M('read')->where(array('rid'=>$bid,'user_id'=>$this->user['id'],'type'=>'xs'))->find();
		
		if($ji_no>=$binfo["pay_num"] && $binfo['free_type'] == 2 && $this->user['vip'] == 0 && $binfo['pay_num']>0){ //如果集大于付费级别
			//查看这集是否阅读过？
			if(!$read){
				$money = M('book_episodes')->where(array('ji_no'=>$ji_no,'bid'=>$bid))->getField("money");
				if(!$money || $money<=0){
					$money = $this->_site['xsmoney'];
				}
				if($this->user['money']<$money){
					$this->error('您的账户书币不足！',U('Mh/pay'));
				}
				M('user')->where(array('id'=>$this->user['id']))->setDec("money",$money);
				
				//查询是否有充值记录
				$read_charge = M('read_charge')->where(array('user_id'=>$this->user['id'],'rid'=>$bid,'type'=>'xs'))->find();
				if(!$read_charge){
					M('read_charge')->add(array(
						'user_id'=>$this->user['id'],
						'rid'=>$bid,
						'type'=>'xs',
						'create_time'=>NOW_TIME,
					));
					M('book')->where(array('id'=>$bid))->setInc('chargenum',1);
				}
				M('book')->where(array('id'=>$bid))->setInc('chargemoney',$money);

				flog($this->user['id'], "money", "-".$money, 8);					
			}
		}
		
		if(!$read){
			M('read')->add(array(
				'user_id'=>$this->user['id'],
				'rid'=>$binfo['id'],
				'title'=>$binfo['title'],
				'pic'=>$binfo['cover_pic'],
				'author'=>$binfo['author'],
				'summary'=>$binfo['summary'],
				'episodes'=>$ji_no,
				'type'=>'xs',
				'create_time'=>NOW_TIME,
			));
		}
		
		//若没阅读过则增加阅读量
		if(!$reads){
			M('book')->where(array('id'=>$bid))->setInc('readnum',1);
		}
		
    	$info = M('book_episodes')->where("bid={$bid} and ji_no={$ji_no}")->find();

    	if(empty($info) || empty($binfo)) {
    		$this->error('小说数据缺失！', U('Book/bookinfo')."&bid={$bid}");
    	}

		//若有文案链接的增加文案阅读量
		if($this->chapter){
			$read = M('chapter')->where(array('id'=>$this->chapter['id']))->getField('read');
			$read = $read+1;
			M('chapter')->where(array('id'=>$this->chapter['id']))->save(array("read"=>$read));
		}
		
		
		$likes = M('book_likes')->where("bid={$bid} and ji_no={$ji_no} and user_id=".$this->user['id'])->find();
    	$collect = M('mh_collect')->where(array("mhid"=>$bid , "user_id"=>$this->user['id'],"type"=>"xs"))->find();
		
    	$this->assign('info',$info);
		$this->assign('binfo',$binfo);
		$this->assign('bid',$bid);
		$this->assign('collect',$collect);
		$this->assign('likes',$likes);
    	
	
		$dd = new \Common\Util\ddwechat();
        $dd->setParam($this->_mp);
        $jssdk = $dd->getsignpackage();
        $this->assign('jssdk', $jssdk);
	
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
    	
    	$list = M('book')->where($cond)->order('sort desc')->select();
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
    	$list = M('book')->where("free_type=1")->order('sort desc')->limit(50)->select();
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
				$where['mhcate'] = array('like','%8%');
				$order = "sort desc";
			}
		}else{
			$order = "sort desc";
		}
    	$list = M('book')->where($where)->order($order)->select();
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
     * 最近更新
     */
    public function book_last(){
   
    	$list = M('book')->where("")->order('sort desc')->limit(50)->select();
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
     * 分类数据
     */
    public function booklist(){
		$bookcate = I("get.cate");
		$where['bookcate'] = array('like','%'.$bookcate.'%');
    	$list = M('book')->where($where)->order('is_new desc,id desc')->limit(50)->select();
		$this->assign('list',$list);
    	$this->display();
    }
	
   
   
   public function sign(){
	   if(IS_POST){
		   if($this->_site['sign'] == 0 || !$this->_site['sign']){
			   $this->error('未开启签到功能');
		   }
		   $date = date('Ymd');
		   $sign = M('sign')->where(array('date'=>$date,'user_id'=>$this->user['id']))->find();
		   if($sign){
			   $this->error('今日已签到!');
		   }else{
				$id = M('sign')->add(array(
					'user_id'=>$this->user['id'],
					'date'=>$date,
					'money'=>$this->_site['sign'],
					'create_time'=>NOW_TIME,
				));
				if($id){
					M('user')->where(array('id'=>$this->user['id']))->setInc('money',$this->_site['sign']);
					flog($this->user['id'], 'money', $this->_site['sign'], 10);
					$dd = new \Common\Util\ddwechat;
					
					//浏览记录
					$a = "\n";
					$read = M('read')->where(array('user_id'=>$this->user['id']))->order('create_time desc')->find();
					if($read){
						if($read['type'] == "mh"){
							$url = U('Mh/inforedit',array('mhid'=>$read['rid'],'ji_no'=>$read['episodes']));
						}else{
							$url = U('Book/inforedit',array('bid'=>$read['rid'],'ji_no'=>$read['episodes']));
						}
						$url = complete_url($url);
						$a = "\n\n".'<a href="'.$url.'">点击我继续上次阅读</a>'."\n\n";
					}
					
					//历史阅读记录
					$li = "历史阅读记录\n\n";
					$lishi = M('read')->distinct(true)->field('type,rid')->where(array('id'=>array('neq',$read['id']),'user_id'=>$this->user['id']))->select();
					if($lishi){
						foreach($lishi as $v){
							$max = M('read')->where(array('type'=>$v['type'],'rid'=>$v['rid']))->order('episodes desc')->find();
							if($read['type'] == "mh"){
								$url = U('Mh/inforedit',array('mhid'=>$max['rid'],'ji_no'=>$max['episodes']));
							}else{
								$url = U('Book/inforedit',array('bid'=>$max['rid'],'ji_no'=>$max['episodes']));
							}
							$url = complete_url($url);
							$li .= '<a href="'.$url.'">>'.$max['title'].'</a>'."\n\n";
						}
					}else{
						$li ="";
					}
					
					$html = '本次签到成功，赠送'.$this->_site['sign'].'书币，请明天继续签到哦!'.$a.$li.'为方便下次阅读，请置顶公众号';
					$dd -> send_msg($this->user['openid'],$html);
					$this->success('签到成功');
				}else{
					$this->error('签到失败');
				}
		   }
	   }else{
		   $this->error('非法请求!');
	   }
   }
  
   
   //获取账单记录
   public function getRecord(){
	   if(IS_POST){
		   $model = I('post.model');
		   $page = I('post.page');
		   $size = 20;
		   $start = ($page-1)*$size;
		   if($model == 1){
			   $list = M('charge')->where(array('user_id'=>$this->user['id'],'status'=>2))->order('create_time desc')->select();
		   }else{
			   $list = M('sign')->where(array('user_id'=>$this->user['id']))->order('create_time desc')->select();
		   }
		   if($list){
				if($model == 1){
					foreach($list as $k=>$v){
						$list[$k]['money'] = $v['money']*$this->_site['rate'];
						$list[$k]['time'] = date('Y-m-d H:i:s',$v['pay_time']);
					}
				}else{
					foreach($list as $k=>$v){
						$list[$k]['time'] = date('Y-m-d H:i:s',$v['create_time']);
					}
				}
			   $this->success($list);
		   }else{
			   if($page == 1){
				   $this->error('没有数据哟~');
			   }else{
				   $this->error('已加载完所有数据');
			   }
		   }
	   }else{
		   $this->error('非法请求！');
	   }
   }
   
	//加载漫画或小说的集数
	public function getJino(){
		if(IS_POST){
			$type = I('post.type');
			$model = I('post.model');
			$id = I('post.id');
			if($type == 'mh'){
				$info = M('mh_list')->where(array('id'=>$id))->find();
			}else{
				$info = M('book')->where(array('id'=>$id))->find();
			}
			$p = I('post.p');
			if($p == 1){
				$start = ($p - 1)*50+16;
			}else{
				$start = ($p - 1)*50+17;
			}
			$end = ($p)*50+16;
			if($end>=$info['episodes']){
				$end = $info['episodes'];
			}
		    for($i=$start;$i<=$end;$i++){
			   if($type == 'mh'){
				    $money = M('mh_episodes')->where(array('ji_no'=>$i,'mhid'=>$id))->getField('money');
					if(!$money || $money<=0){
						$money = $this->_site['mhmoney'];
					}
					$money = intval($money);
					$read = M('read')->where(array('episodes'=>$i,'rid'=>$id,'user_id'=>$this->user['id'],'type'=>$type))->find();
				    if($i>=$info['pay_num'] && $info['pay_num']>0){
					   if($read){
						   $html.= '<div class="item">';
					   }else{
						   $html.= '<div class="item lock">'; 
					   }
				   }else{
					   $money = 0;
					   $html.= '<div class="item">';
				   }
				   
				   $html.='<a href="'.U('Mh/inforedit',array('mhid'=>$id,'ji_no'=>$i)).'" class="">'.$i.'话';
				   $html.='<span>'.$money.'书币</span>';
				   $html.='</a>';
				   $html.='</div>';
			   }else{
					$money = M('book_episodes')->where(array('ji_no'=>$i,'bid'=>$id))->getField('money');
					if(!$money || $money<=0){
						$money = $this->_site['xsmoney'];
					}
					$money = intval($money);
					$read = M('read')->where(array('episodes'=>$i,'rid'=>$id,'user_id'=>$this->user['id'],"type"=>$type))->find();
				   if($i>=$info['pay_num'] && $info['pay_num']>0){
					   if($read){
						   $html.= '<div class="item">';
					   }else{
						   $html.= '<div class="item lock">'; 
					   }
				   }else{
					   $money = 0;
					   $html.= '<div class="item">';
				   }
				   
				   $html.='<a href="'.U('Book/inforedit',array('bid'=>$id,'ji_no'=>$i)).'" class="">'.$i.'章';
				   $html.='<span>'.$money.'书币</span>';
				   $html.='</a>';
				   $html.='</div>';
			   }
		   }
		   if($html){
			    $this->success($html);
		   }else{
			   $this->error('已经加载完所有章节了');
		   }
	   }else{
		   $this->error('非法请求！');
	   }
   }
   
   //漫画和小说评论
   public function Comments(){
	   $id = I('get.id');
	   $type = I('get.type');
	   $list = M('comment')->where(array('cid'=>$id,'type'=>$type))->order('create_time desc')->limit(20)->select();
	   $this->assign('list',$list);
	   
	   if($type == "mh"){
		   $info = M('mh_list')->find(intval($id));
	   }else{
		   $info = M('book')->find(intval($id));
	   }
	   if(!$info){
		   $this->error('数据错误');
	   }
	   $this->assign('info',$info);
	   $this->assign('counts',M('comment')->where(array('cid'=>$id,'type'=>$type))->count());
	   $this->display();
   }
   
   //发布评论
   public function addComment(){
	   if(IS_POST){
			$type = I('post.type');
			$id = I('post.id');
			$where = array(
				'user_id'=>$this->user['id'],
				'cid'=>$id,
				'type'=>$type,
				'create_time'=>array('egt',strtotime(date('Y-m-d'))),
			);
			if(M('comment')->where($where)->find()){
				$this->error('您今天已经发布了评论了！');
			}else{
				M('comment')->add(array(
					'headimg'=>$this->user['headimg'],
					'nickname'=>$this->user['nickname'],
					'user_id'=>$this->user['id'],
					'cid'=>$id,
					'content'=>I('post.content'),
					'type'=>$type,
					'create_time'=>time(),
				)); 
				$this->success('发布成功');
			}
	   }else{
		   $this->error('非法请求');
	   }
   }
   
   //加载评论
   public function loadComment(){
	   if(IS_POST){
			$type = I('post.type');
			$id = I('post.id');
			$page = I('post.page')?I('post.page'):2;
			$size = 20;
			$start = ($page - 1)*$size;
			$where = array(
				'cid'=>$id,
				'type'=>$type,
			);
			$list = M('comment')->where($where)->limit($start,$size)->order('create_time desc')->select();
			if($list){
				$html = "";
				foreach($list as $v){
					$html.='<li class="mui-table-view-cell mui-media">';
					$html.='<img class="mui-media-object mui-pull-left" src="'.$v['headimg'].'">';
					$html.='<div class="mui-media-body">'.$v['content'];
					$html.='<span class="mui-pull-right">'.date('Y-m-d',$v['create_time']).'</span>';
					$html.='</div>';
					$html.='</li>';
				}
				$this->success($html);
			}
	   }else{
		   $this->error('非法请求');
	   }
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
    		$bid = I('bid', 0, 'intval');
    		$ji_no = I('ji_no', 0, 'intval');
    		$old = M('book_likes')->where("bid={$bid} and ji_no={$ji_no} and user_id={$user_id}")->find();
    		if(empty($old)) {
    			$ins = array(
    					'bid'		=> $bid,
    					'ji_no'		=> $ji_no,
    					'user_id'	=> $user_id,
    					'create_time'=>NOW_TIME,
    			);
    			M('book_likes')->add($ins);
    			M('book')->where("id={$bid}")->setInc('likes', 1);
				M('book_episodes')->where(array('bid'=>$bid,'ji_no'=>$ji_no))->setInc('likes', 1);
    		} else {
    			M('book_likes')->where("bid={$bid} and ji_no={$ji_no} and user_id={$user_id}")->delete();
    			M('book')->where("id={$bid}")->setDec('likes', 1);
				M('book_episodes')->where(array('bid'=>$bid,'ji_no'=>$ji_no))->setDec('likes', 1);
    			$tag = 2;//取消点赞成功
    		}
    	} else {
    		$status = 2;
    		$info = '请先登录！';
    	}
    	 
    	$this->ajaxReturn(array('status'=>$status,'info'=>$info,'tag'=>$tag));
    }
	
	/**
     * 搜索结果页
     */
    public function search(){
    	$key = I('key', '', 'trim');
    	//dump($key);exit;
    	$cond = array();
    	$cond['title|author'] = array('like', "%{$key}%");
    	$list = M('book')->where($cond)->order('sort desc')->limit(50)->select();
    	$asdata = array(
    			'key'	=> $key,
    			'list'	=> $list,
    	);
    	
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
    	$user_id = session('user.id');
		$bid = I('post.bid');
		$binfo = M('book')->find($bid);
    	if($user_id > 0) {
    		$old = M('mh_collect')->where(array("mhid"=>$bid,"user_id"=>$this->user['id'],'type'=>"xs"))->find();
    		if(empty($old)) {
    			$ins = array(
    					'mhid'		=> $bid,
    					'user_id'	=> $user_id,
    					'title'		=> $binfo['title'],
    					'cover_pic'	=> $binfo['cover_pic'],
    					'episodes'	=> $binfo['episodes'],
    					'create_time'=>NOW_TIME,
						'type'=>"xs",
    			);
    			M('mh_collect')->add($ins);
    			M('book')->where("id={$bid}")->setInc('collect', 1);
    		} else {
    			M('mh_collect')->where(array("mhid"=>$bid,"user_id"=>$this->user['id'],'type'=>"xs"))->delete();
    			M('book')->where("id={$bid}")->setDec('collect', 1);
    			$tag = 2;//取消收藏成功
    		}
    	} else {
    		$status = 2;
    		$info = '请先登录！';
    	}
    	
    	$this->ajaxReturn(array('status'=>$status,'info'=>$info,'tag'=>$tag));
    }
	
	//对漫画和小说打赏AJAX
	public function mxSend(){
		if(IS_POST){
			$post = I('post.');
			$money = $this->_send[$post['sid']]['money'];
			if($money>$this->user['money']){
				$this->error('您的书币不足,是否立即去充值!',U('Mh/pay'));
			}else{
				if(M('user')->where(array('id'=>$this->user['id']))->setDec('money',$money)){
					flog($this->user['id'], 'money', 0-$money, 12);
					$post['user_id'] = $this->user['id'];
					$post['headimg'] = $this->user['headimg'];
					$post['nickname'] = $this->user['nickname'];
					$post['create_time'] = date('Y-m-d H:i:s');
					$post['pic'] = $this->_send[$post['sid']]['pic'];
					$post['money'] = $this->_send[$post['sid']]['money'];
					M('mxsend')->add($post);
					if($post['type'] == "mh"){
						M('mh_list')->where(array('id'=>$post['mxid']))->setInc('send',$money);
					}else{
						M('book')->where(array('id'=>$post['mxid']))->setInc('send',$money);
					}
					$this->success('打赏成功！');
				}else{
					$this->error('数据错误!');
				}
			}
		}else{
			$this->error('非法请求！');
		}
	}
	
	//加载小说或漫画的打赏记录
	public function LoadSend(){
		if(IS_POST){
			$mxid = I('post.mxid');
			$page = I('post.page');
			$type = I('post.type');
			$size = 10;
			$start = ($page -1) * $size;
			
			$list = M('mxsend')->where(array('mxid'=>$mxid,'type'=>$type))->order('create_time desc')->limit($start,$size)->select();
			if($list){
				$this->success($list);
			}else{
				if($page !=1){
					$this->error('没有更多的打赏记录了哟~！',M()->getLastSql());
				}else{
					$this->error('');
				}
			}
		}else{
			$this->error('非法请求！');
		}
	}
	
	
	
	//举报小说漫画
	public function jubao(){
		$rid = I('get.rid');
		$type = I('get.type');
		if(!$rid || !$type){
			$this->error('参数错误！');
		}
		if($type == "xs"){
			$db = M('book');
		}else{
			$db = M('mh_list');
		}
		$info = $db->find(intval($rid));
		if(!$info){
			$this->error('信息错误！');
		}
		if(IS_POST){
			$jid = I('post.jid');
			if($jid == "seqing"){
				$save = array(
					"seqing"=>array("exp","seqing+1"),
					"nums"=>array("exp","nums+1"),
				);
				$add = array(
					"seqing"=>1,
					"nums"=>1,
					"rid"=>$rid,
					"type"=>$type,
					"title"=>$info['title'],
				);
			}
			if($jid == "xuexing"){
				$save = array(
					"xuexing"=>array("exp","xuexing+1"),
					"nums"=>array("exp","nums+1"),
				);
				$add = array(
					"xuexing"=>1,
					"nums"=>1,
					"rid"=>$rid,
					"type"=>$type,
					"title"=>$info['title'],
				);
			}
			if($jid == "baoili"){
				$save = array(
					"baoili"=>array("exp","baoili+1"),
					"nums"=>array("exp","nums+1"),
				);
				$add = array(
					"baoili"=>1,
					"nums"=>1,
					"rid"=>$rid,
					"type"=>$type,
					"title"=>$info['title'],
				);
			}
			if($jid == "weifa"){
				$save = array(
					"weifa"=>array("exp","weifa+1"),
					"nums"=>array("exp","nums+1"),
				);
				$add = array(
					"weifa"=>1,
					"nums"=>1,
					"rid"=>$rid,
					"type"=>$type,
					"title"=>$info['title'],
				);
			}
			if($jid == "daoban"){
				$save = array(
					"daoban"=>array("exp","daoban+1"),
					"nums"=>array("exp","nums+1"),
				);
				$add = array(
					"daoban"=>1,
					"nums"=>1,
					"rid"=>$rid,
					"type"=>$type,
					"title"=>$info['title'],
				);
			}
			if($jid == "qita"){
				$save = array(
					"qita"=>array("exp","qita+1"),
					"nums"=>array("exp","nums+1"),
				);
				$add = array(
					"qita"=>1,
					"nums"=>1,
					"rid"=>$rid,
					"type"=>$type,
					"title"=>$info['title'],
				);
			}
			if(M('jubao')->where(array('rid'=>$rid,'type'=>$type))->find()){
				M('jubao')->where(array('rid'=>$rid,'type'=>$type))->save($save);
			}else{
				M('jubao')->add($add);
			}
			$this->success("举报成功！");
		}
		$url = get_current_url();
		$this->assign('list',C('JUB'));
		$this->assign('url',$url);
		$this->display();
	}

}
?>