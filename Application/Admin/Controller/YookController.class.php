<?php
namespace Admin\Controller;
use Think\Controller;
class YookController extends AdminController {
    // 列表
	public function index(){
		if($_POST['title']){
			$_GET['p'] = 1; //如果是post的话回到第一页
			$_GET['title'] = $_POST['title'];
			$where['title'] = array('like','%'.$_POST['title'].'%');
		}
		$order = "sort desc";
		// 组合排序方式
		if(in_array($_GET['order'], array('readnum','chargenum', 'chargemoney'))){
			$type = $_GET['type'] == 'asc' ? 'asc' : 'desc';
			$order = $_GET['order'].' '.$type;
		}
		$this -> _list('yook',$where, $order);
	}
	
	// 编辑、添加漫画
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
				$rs = M('yook') -> where('id='.intval($_GET['id'])) -> save($_POST);		
				$product_id = intval($_GET['id']);
			}else{
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$rs = M('yook') -> add($_POST);
				$product_id = $rs;
			}
			$this -> success('操作成功！', U('index'));
			exit;
		}
		
		if(intval($_GET['id'])>0) {
			$info = M('yook') -> find($_GET['id']);
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
	
	
	public function episodes() {
		$yid = I('yid', 0, 'intval');
		if(empty($yid)) {
			$this->error('ID不存在！', $_SERVER['HTTP_REFERER']);
		}
		$info = M('yook')->where("id={$yid}")->find();
		$this->assign('info', $info);
		$this->assign('yid', $yid);
		$cond = array('yid'=>$yid);
		$this -> _list('yook_episodes',$cond, 'id desc');
	}
	
	// 编辑、添加漫画分集
	public function episodesedit(){
		$yid = I('yid', 0, 'intval');
		if(IS_POST){
			$yid = I('post.yid');

			if($_FILES && !empty($_FILES) && $_FILES['file']['size']>0){
				$rt = $this->upload($_FILES['file']);
				if($rt['errmsg']){
					$this->error($rt['errmsg']);
				}
				$_POST['info'] = $rt['url'];
			}

			if(isset($_GET['id'])) { // 修改
				$_POST['update_time'] = NOW_TIME;
				$rs = M('yook_episodes') -> where('id='.intval($_GET['id'])) -> save($_POST);
			} else { // 添加
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$_POST['yid'] = $yid;
				$rs = M('yook_episodes') -> add($_POST);
			}
			
			$cnt = M('yook_episodes')->where("yid={$yid}")->count();
			M('yook')->where("id={$yid}")->setField('episodes', $cnt);
			
			$this -> success('操作成功！', U('episodes')."&yid={$yid}");
			exit;
		}
	
		if(intval($_GET['id'])>0) {
			$info = M('yook_episodes') -> find($_GET['id']);
			
			$asdata = array(
					'info'			=> $info,
			);
			$this -> assign('info',$info);
		}
		$book = M('yook')->where("id={$yid}")->find();
		$this->assign('book', $book);
		$this->assign('yid', $yid);
		$this -> display();
	}
	
	
	// 删除漫画
	public function del(){
		$this -> _del('yook', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	// 删除漫画分集
	public function episodesdel(){
		$this -> _del('yook_episodes', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
		
		
	// 上传图片
	public function upload($file){
		if($file['size'] < 1 && $file['size']>0){
			return array('errmsg'=>'上传错误！','url'=>'');
		}else{
			$ext = $this -> _get_ext($file['name']);
			$new_name = $this -> _get_new_name($ext, 'mp3');
			if(move_uploaded_file($file['tmp_name'], $new_name['fullname'])){
				$url = $new_name['fullname'];
				return array('errmsg'=>'','url'=>$url);
			}else return array('errmsg'=>'文件保存错误！','url'=>'');
		}
	}
	
	/**
	*	根据文件名获取后缀
	*/
	private function _get_ext($file_name){
        return substr(strtolower(strrchr($file_name, '.')),1);
    }

    /**
	*	根据文件类型(后缀)生成文件名和路径
	*	@param return array('name', 'fullname' )
	*	* 文件名取时间戳和随机数的36进制而不是62进制是为了防止windows下大小写不敏感导致文件重名
	*/
	private function _get_new_name($ext, $dir = 'default'){
        $name 		= date('His') . substr(microtime(),2,8) . rand(1000,9999) . '.' . $ext;
        $path 		= './Public/' . $dir .'/'. date('Ymd') .'/';

        // 如果路径不存在则递归创建
        if(!is_dir($path)){
        	mkdir($path, 0777, 1);
        }

        return array(
        		'name'		=> $name,
        		'fullname'	=> $path . $name
        	);
    }

}