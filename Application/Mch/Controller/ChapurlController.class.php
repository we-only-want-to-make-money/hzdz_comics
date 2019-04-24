<?php
namespace Mch\Controller;
use Think\Controller;
class ChapurlController extends AdminController {
    // 链接列表
	public function index(){
		if($_POST['title']){
			$_GET['p'] = 1;
			$_GET['title'] = $_POST['title'];
			$where['title'] = array('like','%'.$_POST['title'].'%');
		}
		if($_POST['type']){
			$_GET['p'] = 1;
			$_GET['type'] = $_POST['type'];
			$where['type'] = intval($_POST['type']);
		}
		$where['memid'] = $this->mch['id'];
		$this -> _list('chapter',$where, 'create_time desc');
	}
	
	// 删除
	public function del(){
		$this -> _del('chapter', intval($_GET['id']));
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	public function showQrcode(){
		$id = I('get.id');
		$url = M('chapter')->where(array('id'=>$id))->getField('url');
		$img = $this->createQrcode($id,$url);
		$this->assign('img',$img);
		$this->display();
	}
	
	//生成链接
	public function createQrcode($id,$url){
		//获取推广码信息
		$path_info = getChapQrcode($id);		
		if(!is_file($path_info['qrcode'])){
			include COMMON_PATH.'Util/phpqrcode/phpqrcode.php';
			// 目录不存在则创建
			if(!is_dir($path_info['path'])){
				mkdir($path_info['path'], 0777,1);
			}
			$errorCorrectionLevel = 'L';
			$matrixPointSize = 6;
			\QRcode::png($url, $path_info['qrcode'], $errorCorrectionLevel, $matrixPointSize, 2);	
		}
		return $path_info['qrcode'];
	}

}