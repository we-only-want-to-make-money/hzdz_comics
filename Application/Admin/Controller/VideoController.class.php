<?php
namespace Admin\Controller;
use Think\Controller;

class VideoController extends AdminController {
	
	// 视频列表
	public function index(){
		$this -> _list('video',null, 'sort desc,id desc');
	}
	
	public function addVideo(){
		$this->display();
	}
	
	public function edit(){
		$this->_edit('video',U('index'));
	}
	
	public function upload(){
		define('PACKAGE_URL','./Upload/');
		$fsize = $_POST['size'];
		$findex =$_POST['indexCount'];
		$ftotal =$_POST['totalCount'];
		$ftype = $_POST['type'];
		$fdata = $_FILES['file'];
	
		//$name  = $_POST['title'];
		$summary = $_POST['summary'];
		$title = $_POST['title'];
		$cover_pic = $_POST['cover_pic'];
		$author = $_POST['author'];
		$price = $_POST['price'];
		$trytime = $_POST['trytime'];
		//$pic2 = $_POST['pic2'];
		//$urls = $_POST['urls'];
	
		$rand_name = $_POST['rand_tmp'];//临时文件随机名称
	
		$fname = mb_convert_encoding($_POST['name'],"gbk","utf-8");
		$e_fname = explode(".",$fname);
		$type = end($e_fname);
		$allowed_type = array('apk','ipa','jpg','png','gif','wmv','rmvb','mp4');
		if( !in_array($type,$allowed_type)){
			echo json_encode(array('res' => 'fail'));
			exit;
		}
	
		$truename = mb_convert_encoding($_POST['trueName'],"gbk","utf-8");
	
		$dir = PACKAGE_URL . $type ; //PACKAGE_URL为绝对路径，/var/www/....
	
		$fnname = rand(1000,9999).time().'.'.$type;
		$save = $dir."/".$fnname;
		if(!is_dir($dir))
		{
			mkdir($dir);
			chmod($dir,0777);//给文件夹以写的权限
		}
	
		//读取临时文件内容
		$temp = fopen($fdata["tmp_name"],"r+");//打开
		$filedata = fread($temp,filesize($fdata["tmp_name"]));//读取文件
	
		//将分段内容存放到新建的临时文件里面
		if(file_exists($dir."/".$rand_name."_".$findex.".tmp")) unlink($dir."/".$rand_name."_".$findex.".tmp");//是否存在当前的临时片名
		$tempFile = fopen($dir."/".$rand_name."_".$findex.".tmp","w+");//打开
	
		fwrite($tempFile,$filedata);//写入
		fclose($tempFile);//关闭
		fclose($temp);
		if($findex+1==$ftotal)
		{
			if(file_exists($save)) @unlink($save);
			//循环读取临时文件并将其合并置入新文件里面
			for($i=0;$i<$ftotal;$i++)
			{
			$readData = fopen($dir."/".$rand_name."_".$i.".tmp","r+");
			$writeData = fread($readData,filesize($dir."/".$rand_name."_".$i.".tmp"));//读取文件
	
			$newFile = fopen($save,"a+");
			fwrite($newFile,$writeData);
	
			fclose($newFile);
	
			fclose($readData);
	
			$resu = @unlink($dir."/".$rand_name."_".$i.".tmp");
			}
			$fnewszie = filesize($dir."/".$fname);
	
			if($fsize==$fnewszie)
			{
			$test = "succe";
			}else{
			$test = "fail";
			}
			if(M('video')->where(array('title'=>$title))->find()){
				M('video')->where(array('title'=>$title))->save(array('video_url'=>'./Upload/'.$type.'/'.$fnname,'author'=>$author,'price'=>$price,'trytime'=>$trytime,'create_time'=>NOW_TIME,'update_time'=>NOW_TIME));
			}else{
				M('video')->add(array('summary'=>$summary,'title'=>$title,'cover_pic'=>$cover_pic,'video_url'=>'./Upload/'.$type.'/'.$fnname,'author'=>$author,'price'=>$price,'trytime'=>$trytime,'create_time'=>NOW_TIME,'update_time'=>NOW_TIME));
			}
			$res = array("res"=>"success","test"=>$test,"fsize"=>$fsize,"newsize"=>$fnewszie,"url"=>mb_convert_encoding($truename."-".$fsize."/".$fname,'utf-8','gbk'),"package_url"=>'./Upload/'.$type.'/'.$fnname);
			echo json_encode($res);
		}
	}
	
	
    // 视频列表
	public function index1(){
		$this -> _list('video',null, 'sort desc,id desc');
	}
	
	// 编辑、添加视频
	public function edit1(){
		if(IS_POST){
			// 修改
			if(isset($_GET['id'])){
				$video_url = session('video_url');
				$_POST['video_url'] = trim($video_url);
				$_POST['update_time'] = NOW_TIME;
				$rs = M('video') -> where('id='.intval($_GET['id'])) -> save($_POST);
				$product_id = intval($_GET['id']);
				$this -> success('操作成功！', U('index'));
				exit;
			}
			// 添加
			else{
				$_POST['create_time'] = NOW_TIME;
				$_POST['update_time'] = NOW_TIME;
				$rs = M('video') -> add($_POST);
				$product_id = $rs;
				session('now_video_id', $rs);
			
				$this -> success('操作成功！', U('edit', array('id'=>$rs)));
				exit;
			}
		}
		
		if(intval($_GET['id'])>0) {
			$info = M('video') -> find($_GET['id']);
			session('now_video_id', $_GET['id']);
			$asdata = array(
					'info'			=> $info,
			);
			$this -> assign($asdata);
		}

		$this -> display();
	}
	
	/**
	 * 短视频
	 */
	public function swfuplaod_ajax() {
		set_time_limit(1000); //超时10分钟
		ini_set('memory_limit','4096M');
	
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		} else if (isset($_GET["PHPSESSID"])) {
			session_id($_GET["PHPSESSID"]);
		}
	
		//session_start();
	
		$POST_MAX_SIZE = ini_get('post_max_size');
		$unit = strtoupper(substr($POST_MAX_SIZE, -1));
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
	
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			header("HTTP/1.1 500 Internal Server Error");
			echo "POST exceeded maximum allowed size.";
			exit(0);
		}
	
		$nowdate = date('Ymd');
		$save_path = getcwd() . "/Public/Uploads/video_url/{$nowdate}/";				// The path were we will save the file (getcwd() may not be reliable and should be tested in your environment)
		$save_path1 = "./Public/Uploads/video_url/{$nowdate}/";				// The path were we will save the file (getcwd() may not be reliable and should be tested in your environment)
	
		if(!file_exists($save_path)) {
			@mkdir($save_path, 0777, true);
			@chmod($save_path, 0777);
		}
	
		$upload_name = "Filedata";
		$max_file_size_in_bytes = 4096 * 1024 * 1024;				// 100M in bytes
		//$extension_whitelist = array("mov", "wmv", "rmvb", "mp4", "mkv");	// Allowed file extensions
		$extension_whitelist = array("mp4", "MP4");	// Allowed file extensions
		$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)
	
		$MAX_FILENAME_LENGTH = 260;
		$file_name = "";
		$file_extension = "";
		$uploadErrors = array(
				0=>"文件上传成功",
				1=>"上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
				2=>"上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
				3=>"上传的文件仅为部分文件",
				4=>"没有文件上传",
				6=>"缺少临时文件夹"
		);
	
		if (!isset($_FILES[$upload_name])) {
			$this->HandleError("No upload found in \$_FILES for " . $upload_name);
			exit(0);
		} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
			$this->HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
			exit(0);
		} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
			$this->HandleError("Upload failed is_uploaded_file test.");
			exit(0);
		} else if (!isset($_FILES[$upload_name]['name'])) {
			$this->HandleError("File has no name.");
			exit(0);
		}
	
		$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
		if (!$file_size || $file_size > $max_file_size_in_bytes) {
			$this->HandleError("File exceeds the maximum allowed size");
			exit(0);
		}
	
		if ($file_size <= 0) {
			$this->HandleError("File size outside allowed lower bound");
			exit(0);
		}
	
		$pathinfo = pathinfo($_FILES[$upload_name]['name']);
		//$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
		$file_name = date('His').mt_rand(1000,9999).'.'.$pathinfo['extension'];
		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
			$this->HandleError("Invalid file name");
			exit(0);
		}
	
		if (file_exists($save_path . $file_name)) {
			$this->HandleError("File with this name already exists");
			exit(0);
		}
	
		$path_info = pathinfo($_FILES[$upload_name]['name']);
		$file_extension = $path_info["extension"];
		$is_valid_extension = false;
		foreach ($extension_whitelist as $extension) {
			if (strcasecmp($file_extension, $extension) == 0) {
				$is_valid_extension = true;
				break;
			}
		}
		if (!$is_valid_extension) {
			$this->HandleError("Invalid file extension");
			exit(0);
		}
	
		if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$file_name)) {
			$this->HandleError("文件无法保存.");
			exit(0);
		} else {
			$now_video_id = session('now_video_id');
	
			$video_url1 = $save_path1.$file_name;
			$video_url = mb_substr($video_url1, 1);
			session('video_url', $video_url);
		}
	
		echo "File Received";
		exit(0);
	
	}
	
	public function episodes() {
		$mhid = I('mhid', 0, 'intval');
		//dump($mhid);exit;
		if(empty($mhid)) {
			$this->error('视频ID不存在！', $_SERVER['HTTP_REFERER']);
		}
		$mhinfo = M('video')->where("id={$mhid}")->find();
		$this->assign('mhinfo', $mhinfo);
		$this->assign('mhid', $mhid);
		$cond = array('mhid'=>$mhid);
		$this -> _list('mh_episodes',$cond, 'id desc');
	}
	
	// 编辑、添加视频分集
	public function episodesedit(){
		$mhid = I('mhid', 0, 'intval');
		
		if(IS_POST){
			/* $mhid = I('mhid', 0, 'intval');
			if(empty($mhid)) {
				$this->error('视频ID错误！', $_SERVER['HTTP_REFERER']);
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
			M('video')->where("id={$mhid}")->setField('episodes', $cnt);
			
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
		$mhinfo = M('video')->where("id={$mhid}")->find();
		$this->assign('mhinfo', $mhinfo);
		$this->assign('mhid', $mhid);
		$this -> display();
	}
	
	// 编辑视频分集图片
	public function episodeseditpic() {
		$mhid = I('mhid', 0, 'intval');
		$id = I('id', 0, 'intval');
		$mhinfo = M('video')->where("id={$mhid}")->find();
		$episodesinfo = M('mh_episodes')->where("id={$id}")->find();
		
		if(IS_POST){
			/* dump($mhid);
			dump($id);
			dump($_POST['pic']);
			exit; */
			$pics = implode(',', $_POST['pic']);
			M('mh_episodes')->where("id={$id}")->setField('pics', $pics);
			$this -> success('操作成功！', U('episodes')."&mhid={$mhid}");
			exit;
		}
		
		$arrpics = explode(',', $episodesinfo['pics']);
		
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
	
	// 删除视频
	public function del() {
		$this -> _del('video', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}
	
	// 删除视频分集
	public function episodesdel(){
		$this -> _del('mh_episodes', $_GET['id']);
		$this -> success('操作成功！', $_SERVER['HTTP_REFERER']);
	}

}