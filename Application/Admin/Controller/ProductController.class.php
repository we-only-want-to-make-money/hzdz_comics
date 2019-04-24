<?php
namespace Admin\Controller;
use Think\Controller;
class ProductController extends AdminController {
    // 漫画列表
	public function index(){
		if($_POST['title']){
			$_GET['p'] = 1; //如果是post的话回到第一页
			$_GET['title'] = $_POST['title'];
			$where['title'] = array('like','%'.$_POST['title'].'%');
		}
		$order = "sort desc,id desc";
		// 组合排序方式
		if(in_array($_GET['order'], array('id','readnum','chargenum', 'chargemoney'))){
			$type = $_GET['type'] == 'asc' ? 'asc' : 'desc';
			$order = $_GET['order'].' '.$type;
		}
		$this -> _list('mh_list',$where, $order);
	}
	
	// 编辑、添加漫画
	public function edit(){
		if(IS_POST){	
			$mhcate = implode(',', $_POST['mhcate']);
			unset($_POST['mhcate']);
			$_POST['mhcate'] = $mhcate;
			$cateids = implode(',', $_POST['arrcateids']);
			unset($_POST['arrcateids']);
			$_POST['cateids'] = $cateids;
			// 修改
			if(isset($_GET['id'])){
				$_POST['update_time'] = NOW_TIME;
				$rs = M('mh_list') -> where('id='.intval($_GET['id'])) -> save($_POST);
		
				$product_id = intval($_GET['id']);
				$this->success('操作成功！');
			}
			// 添加
			else{
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$rs = M('mh_list') -> add($_POST);
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
					exit;
				}
			}
			
			

			$this -> success('操作成功！', U('index'));
			exit;
		}
		
		if(intval($_GET['id'])>0) {
			$info = M('mh_list') -> find($_GET['id']);
			$cateids = $info['cateids'];
			$arrcateids = explode(',', $cateids);
			$mhcate = explode(",",$info['mhcate']);
			$asdata = array(
					'info'			=> $info,
					'arrcateids'	=> $arrcateids,
					'mhcate'		=> $mhcate,
			);
			$this -> assign($asdata);
		}
		$this -> display();
	}
	
	public function addEpisodes($path,$mhid){
		$temp = array();
		if (is_dir($path)) {
			var_dump($path);
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
					$str = trim($str);
					$str = explode("\r\n", $str);
					//krsort($str);		
					$str = implode(",",$str);
					$before = $i-1;
					$next = $i+1;
					$title = trim(substr($v,0,-4));

					$str = iconv('GBK','utf-8',$str);
					$title = iconv('GBK','utf-8',$title);
					$ds = array(
						"mhid"=>$mhid,
						"title"=>$title,
						"ji_no"=>$i,
						"pics"=>$str,
						"like"=>0,
						"before"=>$before,
						"next"=>$next,
						"money"=>0,
						"create_time"=>time(),
						"update_time"=>0,
					);
					M('mh_list')->where(array('id'=>$mhid))->save(array('episodes'=>$i));
					M('mh_episodes')->add($ds);
					$i++;
				}	
			}
		}
	}
	
	public function episodes() {
		$mhid = I('mhid', 0, 'intval');
		if(empty($mhid)) {
			$this->error('漫画ID不存在！', $_SERVER['HTTP_REFERER']);
		}
		$mhinfo = M('mh_list')->where("id={$mhid}")->find();
		$this->assign('mhinfo', $mhinfo);
		$this->assign('mhid', $mhid);
		$cond = array('mhid'=>$mhid);
		$this -> _list('mh_episodes',$cond, 'id desc');
	}
	
	// 编辑、添加漫画分集
	public function episodesedit(){
		$mhid = I('mhid', 0, 'intval');
		
		if(IS_POST){
			/* $mhid = I('mhid', 0, 'intval');
			if(empty($mhid)) {
				$this->error('漫画ID错误！', $_SERVER['HTTP_REFERER']);
			} */

			if(isset($_GET['id'])) { // 修改
				$_POST['update_time'] = NOW_TIME;
				$rs = M('mh_episodes') -> where('id='.intval($_GET['id'])) -> save($_POST);
			} else { // 添加
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$rs = M('mh_episodes') -> add($_POST);
			}
			
			$cnt = M('mh_episodes')->where("mhid={$mhid}")->count();
			M('mh_list')->where("id={$mhid}")->setField('episodes', $cnt);
			
			$this -> success('操作成功！', U('episodes')."&mhid={$mhid}");
			exit;
		}
	
		if(intval($_GET['id'])>0) {
			$info = M('mh_episodes') -> find($_GET['id']);
			
			$asdata = array(
					'info'			=> $info,
			);
			$this -> assign($asdata);
		}
		$mhinfo = M('mh_list')->where("id={$mhid}")->find();
		$this->assign('mhinfo', $mhinfo);
		$this->assign('mhid', $mhid);
		$this -> display();
	}
	
	// 编辑漫画分集图片
	public function episodeseditpic() {
		$mhid = I('mhid', 0, 'intval');
		$id = I('id', 0, 'intval');
		$mhinfo = M('mh_list')->where("id={$mhid}")->find();
		$episodesinfo = M('mh_episodes')->where("id={$id}")->find();
		
		if(IS_POST){
			$pics = trim($_POST['body']);
			$pics = explode("\r\n", $pics);
			krsort($pics);		
			$pics = implode(",",$pics);
			M('mh_episodes')->where("id={$id}")->setField('pics', $pics);
			$this -> success('操作成功！', U('episodes')."&mhid={$mhid}");
			exit;
		}
		
		$arrpics = str_replace(",","\r\n",$episodesinfo['pics']);
		$asdata = array(
				'mhid'			=> $mhid,
				'mhinfo'		=> $mhinfo,
				'episodesinfo'	=> $episodesinfo,
				'arrpics'		=> $arrpics,
				'id'			=> $id,
		);
		
		$this -> assign($asdata);
		$this -> display();
	}
	
	// 根据attr_table格式化数据
	private function format($attr_table){
		if(!is_array($attr_table)){
			$attr_table = unserialize($attr_table);
		}
		// 属性种类数
		$attr_count = count($attr_table['attr']) / count($attr_table['price']);
		// 最后的结果
		$rows = array();
		foreach($attr_table['price'] as $key => $val){
			$attr_tmp = array();
			for($i=0; $i<$attr_count;$i++){
				$attr_tmp[] = $attr_table['attr'][$key*$attr_count+$i];
			}
			$rows[] = array(
				'attr' => implode(',', $attr_tmp),
				'price' => $attr_table['price'][$key],
				'stock' => $attr_table['stock'][$key],
				'code' => $attr_table['code'][$key]
			);
		}
		return $rows;
		
	}
	
	//评论列表
	public function comments(){
		$cid = I('get.id');
		$this->_list("comment",array('cid'=>$cid,'type'=>"mh"),"create_time desc");
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
				'type'=>'mh',
				'create_time'=>time(),
			)); 
			$this->success('添加成功',U('comments',array('id'=>$cid)));
			exit;
		}
		$this->display();
		
	}
	
	// 删除漫画
	public function del(){
		$this -> _del('mh_list', $_GET['id']);
		// 删除相关的属性
		//M('product_attr') -> where(array('product_id'=>intval($_GET['id']))) -> delete();
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	// 删除漫画分集
	public function episodesdel(){
		$this -> _del('mh_episodes', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	/***以下是分类管理***/
	
	// 列表
	public function cate(){
		$list = M('product_cate') -> order('sort desc') -> select();
		$this -> assign('list', $list);
		$this -> display();
	}
	
	// 编辑
	public function cate_edit(){
		S('all_cate', null);
		$this -> _edit('product_cate',U('cate'));
	}
	
	// 删除
	public function cate_del(){
		S('all_cate', null);
		$this -> _del('product_cate', $_GET['id']);
		$this -> success('操作成功！', U('cate'));
	}
	
	
	//打赏列表
	public function sends(){
		$mxid = I('get.id');
		$this->_list("mxsend",array('mxid'=>$mxid,'type'=>"mh"),"create_time desc");
	}
}