<?php
namespace Admin\Controller;
use Think\Controller;
class CustomController extends AdminController {
    // 通知列表
	public function index(){
		$this -> _list('arclist',array('pid'=>0),'create_time desc');
	}
	
	// 编辑、添加通知
	public function edit(){
		if(!isset($_GET['id'])){
			$_POST['create_time'] = NOW_TIME;
		}
		$this -> _edit('arclist', U('index'));
	}
	
	// 删除通知
	public function del(){
		$this -> _del('arclist', intval($_GET['id']));
		//删除所属文章
		M('arclist')->where(array('pid'=>$_GET['id']))->delete();
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	//文章列表
	public function arclist(){
		$title = M('arclist')->where(array('id'=>$_GET['pid']))->getField('title');
		$this->assign('title', $title);
		$this->_list('arclist',array('pid'=>$_GET['pid']),'sort desc');
	}
	
	//编辑文章
	public function editArc(){
		if(IS_POST){
			
			if(!isset($_GET['id'])){
				$_POST['create_time'] = NOW_TIME;
			}
			$_POST['show_cover'] = isset($_POST['show_cover']) ? 1 : 0;
		}
		$this -> _edit('arclist', U('arclist?pid='.$_GET['pid']));
	}
	
	//删除文章
	public function delArc(){
		$this -> _del('arclist', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	//发送消息
	public function custommsg(){
		$user = M('user')->where(array('isend'=>0,'isrec'=>1))->order('id asc')->limit(5)->select();
		if($user){
			$id = I('post.id');
			$arclist = M('arclist')->where(array('pid'=>$id))->order('sort desc')->select();
			if($arclist){
				foreach($arclist as $v){
					$url = "http://".$_SERVER['HTTP_HOST'].__ROOT__."/index.php?m=&c=Public&a=read&id=".$v['id'];
					$arclists[] = array(
						'title' => $v['title'],
						'description' => $v['desc'],
						'picurl' => complete_url($v['cover']),
						'url' => $url,
					);
				}
				$dd = new \Common\Util\ddwechat;
				$dd -> setParam($this -> _mp);
				$html = "";
				foreach($user as $k=>$v){
					$dd -> send_msg($v['openid'],$arclists, 'news');
					M('user')->where(array('id'=>$v['id']))->save(array('isend'=>1));
					$html.="<span>".$v['nickname']."于".date('Y-m-d H:i:s')."发送成功！</span>";
				}
				$this->success($html);
			}
		}else{
			M('user')->execute("update vv_user set isend=0");
			$this->error('已经发送所有用户了！');
		}
	}
	
	//清除发送缓存
	public function clearSend(){
		M('user')->execute("update vv_user set isend=0");
		$this->success('清除成功，现在可以发送了');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}