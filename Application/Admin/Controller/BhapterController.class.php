<?php
namespace Admin\Controller;
use Think\Controller;
class BhapterController extends AdminController {
    // 漫画列表
	public function index(){
		if($_POST['title']){
			$_GET['title'] = $_POST['title'];
			$where['title'] = array('like','%'.$_POST['title'].'%');
		}
		$this -> _list('book',$where, 'sort desc,id desc');
	}
	
	//漫画免费章节
	public function free(){
		$bid = I('id', 0, 'intval');
		if(empty($bid)) {
			$this->error('小说ID不存在！', $_SERVER['HTTP_REFERER']);
		}
		$binfo = M('book')->where("id={$bid}")->find();
		$this->assign('binfo', $binfo);
		$this->assign('bid', $bid);
		$cond = array('bid'=>$bid,'ji_no'=>array('lt',$binfo['pay_num']));
		$this -> _list('book_episodes',$cond, 'id asc');
	}
	
	//漫画
	public function doChapter(){
		$id = I('get.id');
		$bid = I('get.bid');
		$info = M('book_episodes')->find(intval($id));
		if(!$info){
			$this->error('漫画数据错误！');
		}
		if($info['pics']){
			$info['banner'] = explode(',',$info['pics']);
		}
		$binfo = M('book')->find(intval($bid));
		$this->assign('info',$info);
		$this->assign('id',$id);
		$this->assign('bid',$bid);
		$this->assign('binfo',$binfo);
		
		//下一章节
		$nji_no = $info['ji_no']+1;
		$next = M('book_episodes')->where(array('bid'=>$bid,'ji_no'=>$nji_no))->find();
		$this->assign('next',$next);
		
		$this->display();
	}
	
	//生成漫画文案
	public function createUrl(){
		if(IS_POST){
			$post = I('post.');
			$post['type'] = 2;
			$post['create_time'] = time();
			$post['qrcode'] = $this->_site['qrcode'];
			$chapid = M('chapter')->add($post);
			$url = "http://".$_SERVER['SERVER_NAME']."/index.php?m=&c=Book&a=inforedit&bid=".$post['mbid']."&ji_no=".$post['ji_no']."&sub=".$post['subjino']."&chapid=".$chapid;
			$jino = $post['ji_no'] -1;
			$burl = "http://".$_SERVER['SERVER_NAME']."/index.php?m=&c=Book&a=inforedit&bid=".$post['mbid']."&ji_no=".$jino."&sub=".$post['subjino']."&chapid=".$chapid;
			M('chapter')->where(array('id'=>$chapid))->save(array('url'=>$url,'burl'=>$burl));
			$this->success('生成文案链接成功！',U('Chapurl/index'));
		}else{
			$this->error('生成失败！');
		}
	}
	
	
}