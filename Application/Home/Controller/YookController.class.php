<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 听书控制器
 */
class YookController extends HomeController {
	
	public function _initialize(){
		parent::_initialize();
	}	
	
	
	/**
	 * 听书首页
	 */
    public function index(){
    	foreach ($this->_yook as $k=>$v){
			if($v['show'] == 2 && $v['isshow']){
				$bookcate[$k]['name'] = $v['name'];
				$bookcate[$k]['list'] = M('yook')->where(array('bookcate'=>array('like','%'.$k.'%')))->order('sort desc')->limit(6)->select();
			}
		}
		$this->assign('bookcate',$bookcate);
		$this->assign('mf',M('yook')->where(array('free_type'=>1))->order('sort desc')->select());
		
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
    	$yid = I('yid', 'intval', 0);
    	if(empty($yid)) {
    		$this->error('非法访问数据！', U('Book/index'));
    	}
    	
    	$info = M('yook')->where("id={$yid}")->find();
    	if(empty($info)) {
    		$this->error('数据缺失！', U('index'));
    	}
		
		$this->assign('info',$info);
		
    	M('yook')->where("id={$yid}")->setInc('reader', 1);
    	
    	$tag = 2; //未收藏
    	$lock = 1; //1锁 2不锁
    	if(session('user_id') > 0 || $this->user['id']) {
	    	$old = M('mh_collect')->where(array("mhid"=>$yid , "user_id"=>$this->user['id'],"type"=>"ys"))->find();

	    	if($old) {
	    		$tag = 1;
	    	}
	    	$userinfo = M('user')->where(array("user_id"=>$this->user['id']))->find();
	    	if($userinfo['vip_level']>0 && $userinfo['vip_endtime']>NOW_TIME) {
	    		$lock = 2;
	    	}
    	}

		$this->assign('tag',$tag);
		
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
    	
    	$list = M('yook')->where($cond)->order('sort desc')->select();
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
    	$list = M('yook')->where("free_type=1")->order('sort desc')->limit(50)->select();
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
    	$list = M('yook')->order('sort desc')->limit(50)->select();
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
   
    	$list = M('yook')->where("")->order('sort desc')->limit(50)->select();
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
    	$list = M('yook')->where($where)->order('is_new desc,id desc')->limit(50)->select();
		$this->assign('list',$list);
    	$this->display();
    }
	
   
	//加载听书集数
	public function getJino(){
		if(IS_POST){
			$page = I('post.page')?I('post.page'):1;
			$yid = I('post.yid');
			$start = ($page - 1)*20;
			$list = M('yook_episodes')->where(array('yid'=>$yid))->order('ji_no asc')->limit($start,20)->select();
			$info = M('yook')->find(intval($yid));
		    if($list){
			   $html = "";
			   foreach($list as $v){
				    $money = $v['money'];
				    if(!$money || $money<0 || $money == 0){
						$money = $this->_site['xsmoney'];
					}
					 $read = M('book_ys_read')->where(array('user_id'=>$this->user['id'],'yid'=>$yid,'ji_no'=>$v['ji_no']))->find();
					if($read){
						$isread = 1;
					}else{
						$isread = 2;
					}
				    $html.= '<li _title="'.$v['title'].'" _path="'.complete_url($v['info']).'" _pay_num = "'.$info['pay_num'].'" _jino="'.$v['ji_no'].'" onclick="play(this);">';
					if($isread == 2 && $v['ji_no']>=$info['pay_num']){
						$span = '<span style="color:#FF5722;">(付费:&nbsp;'.intval($money).'&nbsp;书币)</span>&nbsp;';
						$html.= $span.$v['title'];
					}else{
						$html.= $v['title'];
					}
					
					$html.= '<a href="javascript:;">';
					$html.= '<i class="fa fa-play-circle-o"></i>';
					$html.= '</a>';
					$html.= '</li>';
			   }
			   $this->success($html);
		    }else{
			   $this->error('已经加载完所有章节了');
		    }
	    }else{
		   $this->error('非法请求！');
	    }
   }
   
   
   //付费章节扣除
   public function paynumPlay(){
	   if(IS_POST){
		   $jino = I('post.jino');
		   $yid = I('post.yid');
		   
		   $read = M('book_ys_read')->where(array('user_id'=>$this->user['id'],'yid'=>$yid,'ji_no'=>$jino))->find();
		   
		    if(!$read && $this->user['vip']!=1){
			    $info = M('yook_episodes')->where(array('yid'=>$yid,'ji_no'=>$jino))->find();
			    if(!$info['money'] || $info['money'] == 0){
				   $money = $this->_site['xsmoney'];
			    }else{
					$money = $info['money'];
				}
				
			    if($this->user['money']<$money){
				   $this->error('账户余额不足！');
			    }else{
					M('user')->where(array('id'=>$this->user['id']))->setDec('money',$money);
					M('book_ys_read')->add(array(
						'user_id'=>$this->user['id'],
						'yid'=>$yid,
						'ji_no'=>$jino,
						'create_time'=>NOW_TIME,
					));
			    }
		    }
		   $this->success('支付成功！');
		  
	   }else{
		   $this->error('非法请求！');
	   }
   }
   
   
  
	/**
     * 搜索结果页
     */
    public function search(){
    	$key = I('key', '', 'trim');
    	//dump($key);exit;
    	$cond = array();
    	$cond['title|author'] = array('like', "%{$key}%");
    	$list = M('yook')->where($cond)->order('sort desc')->limit(50)->select();
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
		$yid = I('post.yid');

    	if($user_id > 0) {
    		$binfo = M('yook')->where("id={$yid}")->find();
    		$old = M('mh_collect')->where(array("mhid"=>$yid,"user_id"=>$this->user['id'],'type'=>"ys"))->find();
    		if(empty($old)) {
    			$ins = array(
    					'mhid'		=> $yid,
    					'user_id'	=> $user_id,
    					'title'		=> $binfo['title'],
    					'cover_pic'	=> $binfo['cover_pic'],
    					'episodes'	=> $binfo['episodes'],
    					'create_time'=>NOW_TIME,
						'type'=>"ys",
    			);
    			M('mh_collect')->add($ins);
    			M('book')->where("id={$yid}")->setInc('collect', 1);
    		} else {
    			M('mh_collect')->where(array("mhid"=>$yid,"user_id"=>$this->user['id'],'type'=>"ys"))->delete();
    			M('book')->where("id={$yid}")->setDec('collect', 1);
    			$tag = 2;//取消收藏成功
    		}
    	} else {
    		$status = 2;
    		$info = '请先登录！';
    	}
    	
    	$this->ajaxReturn(array('status'=>$status,'info'=>$info,'tag'=>$tag));
    }
	


}
?>