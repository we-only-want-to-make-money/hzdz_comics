<?php
namespace Admin\Controller;
use Think\Controller;
class BookController extends AdminController {
    // 列表
	public function index(){
		if($_POST['title']){
			$_GET['p'] = 1; //如果是post的话回到第一页
			$_GET['title'] = $_POST['title'];
			$where['title'] = array('like','%'.$_POST['title'].'%');
		}
		$order = "sort desc";
		// 组合排序方式
		if(in_array($_GET['order'], array('id','readnum','chargenum', 'chargemoney'))){
			$type = $_GET['type'] == 'asc' ? 'asc' : 'desc';
			$order = $_GET['order'].' '.$type;
		}
		$this -> _list('book',$where, $order);
	}
	
	// 编辑、添加小说
	public function edit(){
		if(IS_POST){
			$cateids = implode(',', $_POST['arrcateids']);
			unset($_POST['arrcateids']);
			$_POST['cateids'] = $cateids;
			
			$bookcate = implode(',', $_POST['bookcate']);
			unset($_POST['bookcate']);
			$_POST['bookcate'] = $bookcate;
			
			// 修改
			if(isset($_GET['id'])){
				$_POST['update_time'] = NOW_TIME;
				$rs = M('book') -> where('id='.intval($_GET['id'])) -> save($_POST);
		
				$product_id = intval($_GET['id']);
				$this->success('操作成功！');
			}else{
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$rs = M('book') -> add($_POST);
				$product_id = $rs;
				
				//若上传了分集压缩包
				if(!empty($_FILES['cert'])){
					 $upload = new \Think\Upload();
					 $upload->maxSize   =     200*1024*1024 ;
					 $upload->exts      =     array('zip','rar');
					 $upload->rootPath  =     './Public/xiaoshuo/';
					 $upload->savePath  =     xmd5(time().rand()).'/';
					 $upload ->autoSub = false;
					 $info   =   $upload->upload();
					 if($info){
						$info = $info['cert'];
						// 解压
						$path = $upload->rootPath . $info['savepath'];
						$file = $path . $info['savename'];
						if(file_exists($file)){
							// 打开压缩文件
							$zip = new \ZipArchive();
							$rs = $zip -> open($file);
							if($rs && $zip -> extractTo($path)){
								$zip -> close();
								//解压完成之后删除
								unlink($file);
								$_POST['cert'] = $path;
							}else{
								$this -> error('解压失败!');
							}
						}else{
							$this -> error('系统没找到上传的文件');
						}
					}else {
						$this -> error('上传错误');
					}
					$this->addEpisodes($path,$product_id);
					 $this -> success('操作成功！', U('index'));
					// $this -> success('操作成功！', U('index'));
					exit;
				}
			}
			
			exit;
		}
		
		if(intval($_GET['id'])>0) {
			$info = M('book') -> find($_GET['id']);
			$cateids = $info['cateids'];
			$arrcateids = explode(',', $cateids);
			$bookcate = explode(",",$info['bookcate']);
			$asdata = array(
					'info'			=> $info,
					'arrcateids'	=> $arrcateids,
					'bookcate'		=> $bookcate,
			);
			$this -> assign($asdata);
		}
		$this -> display();
	}
	
	public function addEpisodes($path,$bid){
		$temp = array();
		if (is_dir($path)) {

			$temp = array();
			if ($handle = opendir($path)) {
				$i = 1;
				while (false !== ($fp = readdir($handle))) {
					if ($fp != "." && $fp != "..") {
						$temp[] = $fp;
					}
				}
				closedir($handle);
				sort ($temp,SORT_NUMERIC);
				reset ($temp);
				foreach ($temp as $v) {
					$str = file_get_contents($path.$v);
					
					$str = "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$str;

					$str = preg_replace('/\n|\r\n/','</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$str);

					$before = $i-1;
					$next = $i+1;
					$title = trim(substr($v,0,-4));
					// var_dump(iconv('GBK','UTF-8',$v));
					$str = iconv('GBK','UTF-8',$str);
					$title = iconv('GBK','UTF-8',$title);

					$ds = array(
						"bid"=>$bid,
						"title"=>$title,
						"ji_no"=>$i,
						"info"=>$str,
						"like"=>0,
						"before"=>$before,
						"next"=>$next,
						"money"=>0,
						"create_time"=>time(),
						"update_time"=>0,
					);
					M('book')->where(array('id'=>$bid))->save(array('episodes'=>$i));
					M('book_episodes')->add($ds);
					$i++;
				}	
			}
		}
	}
	

	
	public function episodes() {
		$bid = I('bid', 0, 'intval');
		if(empty($bid)) {
			$this->error('ID不存在！', $_SERVER['HTTP_REFERER']);
		}
		$info = M('book')->where("id={$bid}")->find();
		$this->assign('info', $info);
		$this->assign('bid', $bid);
		$cond = array('bid'=>$bid);
		$this -> _list('book_episodes',$cond, 'id desc');
	}
	
	// 编辑、添加小说分集
	public function episodesedit(){
		$bid = I('bid', 0, 'intval');
		if(IS_POST){
			$bid = I('post.bid');
			if(isset($_GET['id'])) { // 修改
				$_POST['update_time'] = NOW_TIME;
				$rs = M('book_episodes') -> where('id='.intval($_GET['id'])) -> save($_POST);
			} else { // 添加
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$_POST['bid'] = $bid;
				$rs = M('book_episodes') -> add($_POST);
			}
			
			$cnt = M('book_episodes')->where("bid={$bid}")->count();
			M('book')->where("id={$bid}")->setField('episodes', $cnt);
			
			$this -> success('操作成功！', U('episodes')."&bid={$bid}");
			exit;
		}
	
		if(intval($_GET['id'])>0) {
			$info = M('book_episodes') -> find($_GET['id']);
			
			$asdata = array(
					'info'			=> $info,
			);
			$this -> assign('info',$info);
		}
		$book = M('book')->where("id={$bid}")->find();
		$this->assign('book', $book);
		$this->assign('bid', $bid);
		$this -> display();
	}
	
	
	// 删除小说
	public function del(){
		$this -> _del('book', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	// 删除小说分集
	public function episodesdel(){
		$this -> _del('book_episodes', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	//评论列表
	public function comments(){
		$cid = I('get.id');
		$this->_list("comment",array('cid'=>$cid,'type'=>"xs"),"create_time desc");
	}
		
	public function delComment(){
		$this -> _del('comment', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}	
	
	public function addComment(){
		$cid = I('get.cid');
		if(IS_POST){
			$user = M('user')->order('rand()')->find();
			M('comment')->add(array(
				'headimg'=>$user['headimg'],
				'nickname'=>$user['nickname'],
				'user_id'=>$user['id'],
				'cid'=>$cid,
				'content'=>I('post.content'),
				'type'=>'xs',
				'create_time'=>time(),
			)); 
			$this->success('添加成功',U('comments',array('id'=>$cid)));
			exit;
		}
		$this->display();
		
	}
	
	
	//打赏列表
	public function sends(){
		$mxid = I('get.id');
		$this->_list("mxsend",array('mxid'=>$mxid,'type'=>"xs"),"create_time desc");
	}

}