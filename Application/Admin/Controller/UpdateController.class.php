<?php
namespace Admin\Controller;
use Think\Controller;
class UpdateController extends AdminController {
	var $update_url = 'http://up.efucms.com/?c=Update&ver={ver}&domain={domain}&code={code}';
    
	// 更新首页
	public function index(){
		$this -> assign('version_no', intval(C('VERSION')));
		$this -> display();
	}
	
	// 构成升级url
	private function get_update_url(){
		$code = require( COMMON_PATH.'Conf/auth.php');
		$update_url = str_replace(array('{ver}', '{domain}','{code}'), array(intval(C('VERSION')), $_SERVER['HTTP_HOST'],$code), $this -> update_url);
		return $update_url;
	}
	
	// 检查更新
	public function check(){
		$update_url = $this -> get_update_url();
		
		// 获取版本信息
		$json = file_get_contents($update_url);
		$data = json_decode($json, 1);
		if(!empty($data['unauthorized'])){
			$this -> display('unauthorized');
			exit;
		}
		
		$this -> assign('info', $data);
		$this -> display();
	}
	
	// 执行更新操作
	public function do_update(){
		$update_url = $this -> get_update_url();
		
		// 获取版本信息
		$json = file_get_contents($update_url);
		$data = json_decode($json, 1);
		if(!empty($data['unauthorized'])){
			$this -> error('请购买正版程序！');
		}
		
		if($data['ver'] == 0){
			$this -> error('暂时没有更新');
			exit;
		}
		
		// zip文件保存路径
		$cache_file = NOW_TIME.".zip";
		file_put_contents($cache_file, file_get_contents($data['file']));
		
		// 打开压缩文件
		$zip = new \ZipArchive();
		$rs = $zip -> open($cache_file);
		
		// 解压缓存路径
		$cache_path = './update_cache';
		if(!is_dir($cache_path)){
			mkdir($cache_path, 0777, 1);
		}
		
		// 3.解压到指定目录
		$zip -> extractTo($cache_path);
		$zip -> close();
		// 删除压缩文件
		unlink($cache_file);
		
		
		// 4.如果有sql文件则对其处理
		if(file_exists($cache_path.'/data.sql')){
			$sql_content = file_get_contents($cache_path.'/data.sql');
			
			// 替换表前缀
			$sql_content = str_replace('dd_', C('DB_PREFIX'),$sql_content);
			
			// 分解sql语句
			$sql_arr = explode(";\r\n", $sql_content);
			
			// 循环执行
			$model = M();
			foreach($sql_arr as $sql){
				$sql = trim($sql);
				if(!empty($sql)){
					$model -> execute($sql);
				}
			}
			
			// 处理完成后删除sql文件
			unlink($cache_path.'/data.sql');
		}
		
		// 5.复制到网站目录
		recurse_copy($cache_path, $_SERVER['DOCUMENT_ROOT'].__ROOT__);
		
		// 删除缓存目录
		delete_dir($cache_path);
		
		// 保存最后的更新信息
		$conf_file = COMMON_PATH.'Conf/ver.php';
		file_put_contents($conf_file, "<?php\r\n return ".var_export(array('VERSION' => $data['ver']), 1).';');
		
		$this -> success('更新完成！');
	}
	
	// 初始化，清除数据
	public function reset(){
		if(IS_POST){
			if(xmd5($_POST['pass']) != $this -> _user['pass']){
				$this -> error('密码错误！');
				exit;
			}
			$table = array(
				// 用户数据
				'user' => array(
					'addr' => '用户地址',
					'bank_card' => '用户银行卡',
					'deposit' => '转账数据',
					'finance_log' => '财务记录',
					'order' => '订单',
					'order_product ' => '订单产品',
					'pay_log' => '微信支付记录',
					'separate_log' => '分成记录',
					'user' => '用户数据',
					'withdraw' => '提现数据',
				),
				// 产品数据 
				'product' => array(
					'product' => '产品',
					'product_attr' => '产品属性',
					'product_cate' => '产品分类',
				),
				// 公众号相关数据 
				'mp' => array(
					'selfmenu' => '自定义菜单',
					'article' => '自动回复文章',
					'autoreply' => '自动回复',
				),
				// 收款功能 
				'sk' => array(
					'sk' => ' 收款',
					'sk_order' => ' 收款记录',
				),
				// 其他
				'other' => array(
					'notice' => '通知公告',
				)
			);
			foreach($table as $type => $arr){
				if(!empty($_POST[$type]) && $_POST[$type] == 1){
					foreach($arr as $k => $v){
						M() -> execute("TRUNCATE ".C('DB_PREFIX').$k);
					}
				}
			}
			$this -> success('操作成功！',$_SERVER['HTTP_REFERER']);
			exit;
		}
		
		$this -> display();
	}
}

// 递归复制目录
function recurse_copy($src, $dst){
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ($file = readdir($dir))){
        if (($file != '.') && ($file != '..')){
            if (is_dir($src . '/' . $file)){
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            }else{
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// 删除一个目录
function delete_dir($dirname){
    $result = false;
    if(! is_dir($dirname)){
        echo " $dirname is not a dir!";
        exit(0);
    }
    $handle = opendir($dirname);
    while(($file = readdir($handle)) !== false){
        if($file != '.' && $file != '..'){
            $dir = $dirname . DIRECTORY_SEPARATOR . $file;
            is_dir($dir) ? delete_dir($dir) : unlink($dir);
        }
    }
    closedir($handle);
    $result = rmdir($dirname) ? true : false;
    return $result;
}