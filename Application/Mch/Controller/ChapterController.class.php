<?php
namespace Mch\Controller;
use Think\Controller;
class ChapterController extends AdminController {
    // 漫画列表
	public function index(){
		if($_POST['title']){
			$_GET['title'] = $_POST['title'];
			$where['title'] = array('like','%'.$_POST['title'].'%');
		}
		$this -> _list('mh_list',$where, 'sort desc,id desc');
	}
	
	//漫画免费章节
	public function free(){
		$mhid = I('id', 0, 'intval');
		if(empty($mhid)) {
			$this->error('漫画ID不存在！', $_SERVER['HTTP_REFERER']);
		}
		$mhinfo = M('mh_list')->where("id={$mhid}")->find();
		$this->assign('mhinfo', $mhinfo);
		$this->assign('mhid', $mhid);
		$cond = array('mhid'=>$mhid,'ji_no'=>array('lt',$mhinfo['pay_num']));
		$this -> _list('mh_episodes',$cond, 'id asc');
	}
	
	//漫画
	public function doChapter(){
		$id = I('get.id');
		$mhid = I('get.mhid');
		$info = M('mh_episodes')->find(intval($id));
		if(!$info){
			$this->error('漫画数据错误！');
		}
		if($info['pics']){
			$info['banner'] = explode(',',$info['pics']);
		}
		$url= C('mh_config_url');
		foreach($info['banner'] as $k=>$v){
			$info['banner'][$k] = $url.$v;
		}
		$mhinfo = M('mh_list')->find(intval($mhid));
		$this->assign('info',$info);
		$this->assign('id',$id);
		$this->assign('mhid',$mhid);
		$this->assign('mhinfo',$mhinfo);
		
		//下一章节
		$nji_no = $info['ji_no']+1;
		$next = M('mh_episodes')->where(array('mhid'=>$mhid,'ji_no'=>$nji_no))->find();
		
		$this->assign('next',$next);
		
		$this->display();
	}
	
	//生成漫画文案
	public function createUrl(){
		if(IS_POST){
			$post = I('post.');
			$post['type'] = 1;
			$post['memid'] = $this->mch['id'];
			$post['create_time'] = time();
			$post['qrcode'] = $this->mch['gqrcode'];
			$chapid = M('chapter')->add($post);
			$url = "http://".$_SERVER['SERVER_NAME']."/index.php?m=&c=Mh&a=inforedit&mhid=".$post['mbid']."&ji_no=".$post['ji_no']."&sub=".$post['subjino']."&chapid=".$chapid;
			$jino = $post['ji_no'] -1;
			$burl = "http://".$_SERVER['SERVER_NAME']."/index.php?m=&c=Mh&a=inforedit&mhid=".$post['mbid']."&ji_no=".$jino."&sub=".$post['subjino']."&chapid=".$chapid;
			M('chapter')->where(array('id'=>$chapid))->save(array('url'=>$url,'burl'=>$burl));
			$this->success('生成文案链接成功！',U('Chapurl/index'));
		}else{
			$this->error('生成失败！');
		}
	}
}