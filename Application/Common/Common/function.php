<?php

// 对字符串进行加盐散列加密
function xmd5($str){
	return md5(md5($str).C('SAFE_SALT'));
}

// 获得当前的url
function get_current_url(){
	$url = "http://" . $_SERVER['SERVER_NAME'];
	$url .= $_SERVER['REQUEST_URI'];
	return $url;
}

// 补全url
function complete_url($url){
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	if(substr($url,0,1) == '.'){
		return $protocol . $_SERVER['SERVER_NAME'].__ROOT__.substr($url,1);
	}
	elseif(substr($url,0,7) != 'http://' && substr($url,0,8) != 'https://'){
		return $protocol . $_SERVER['SERVER_NAME'].$url;
	}
	else{
		return $url;
	}
	
}

//举报类型
function getJutxt($k){
	$list = C('JUB');
	dump($list);
	return $list[$k]?$list[$k]:"未知";
}

function openPath($dir){
	$arr = array();
	if (is_dir($dir)) {
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$arr[] = $file;
					dump($arr);
				}
			}
			closedir($handle);
		}
	}
	return $arr;
}

/**
 * 二维数组根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
function arraySequence($array, $field, $sort = 'SORT_DESC'){
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}


//二维数组排序
function arraySort($array, $field, $sort = 'SORT_DESC'){
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}


// 根据订单状态返回状态信息
function get_order_status($status){
	$status_str = '';
	switch($status){
		case -1: $status_str = '已关闭'; break;
		case 1: $status_str = '待支付'; break;
		case 2: $status_str = '已支付待发货'; break;
		case 3: $status_str = '待确认'; break;
		case 4: $status_str = '已完成'; break;
		default : $status_str = '未知状态';
	}
	return $status_str;
}

// 获取性别
function get_sex($status){
	$status_str = '';
	switch($status){
		case 1: $status_str = '先生'; break;
		case 2: $status_str = '女士'; break;
		default : $status_str = '未知状态';
	}
	return $status_str;
}


//获取上级ID和姓名
function getParent($parent){
	if(!$parent || $parent == 0){
		$name ='无上级';
	}else{
		$name = M('user')->where(array('id'=>$parent))->getField('true_name');
		if(!$name || $name == ''){
			$name = M('user')->where(array('id'=>$parent))->getField('nickname');
		}
	}
	return $name;
}

//获取姓名或昵称
function getUserName($user){
	if(!is_array($user)){
		$user = M('user')->find(intval($user));
	}
	if($user['true_name']){
		return $user['true_name'];
	}else{
		return $user['nickname'];
	}
}   

//获取订单支付类型
function getOrderType($type){
	$return = '';
	switch($type){
		case 1: $return = '微信支付';break;
		case 2: $return = '支付宝支付';break;
		case 3: $return = '余额支付';break;
		default : $return = '未知支付';
	}
	return $return;
}          

// 根据代理等级获取等级名称
function get_level_name($level=''){
	if(!$config){
		$config = $GLOBALS['_CFG']['level'];
	}
	foreach($config as $k=>$v){
		$arr[$k] = $v['name'];
	}
	if(!$arr[$level]){
		$arr[$level]='普通会员';	
	}
	return $arr[$level];
}

// 根据代理等级获取等级名称
function get_lv_name($lv=''){
	if(!$config){
		$config = $GLOBALS['_CFG']['lv'];
	}
	foreach($config as $k=>$v){
		$arr[$k] = $v['name'];
	}
	if(!$arr[$lv]){
		$arr[$lv]='普通会员';	
	}
	return $arr[$lv];
}

//获取合伙人和代理总称
function get_level_lv_name($level='',$lv=''){
	if(!$level && !$lv){
		$string = '普通会员';
	}
	$config_level = $GLOBALS['_CFG']['level'];
	$config_lv = $GLOBALS['_CFG']['lv'];

	if($level && !$lv){
		$string = $config_level[$level]['name'];
	}
	if(!$level && $lv){
		$string = $config_lv[$lv]['name'];
	}
	if($level && $lv){
		$string = $config_level[$level]['name'].'('.$config_lv[$lv]['name'].')';
	}
	return $string;
}


//根据金额获取对应等级
function get_lv_money($money){	
	if(!$money){
		return;
	}
	$config = $GLOBALS['_CFG']['lv'];
	for($i = count($config); $i>0; $i--){
		if($money >= $config[$i]['money']){
			return $i;
			break;
		}
	}
	return false;
}

//微信支付方法
function wxPay($order,$table='',$type=''){
	if(!is_array($order)){
		$order = M($table)->find(intval($order));
	}
	$user = M('user')->find(intval($order['user_id']));
	$jsapi = new \Common\Util\wxjspay;

	$param = $GLOBALS['_CFG']['mp'];
	$param['key'] = $GLOBALS['_CFG']['mp']['key'];
	$param['openid'] = $user['openid'];
	$param['body'] = $$GLOBALS['_CFG']['site']['name'].'在线支付';
	$param['out_trade_no'] = $order['sn'];
	$param['total_fee'] = $order['money'] * 100;
	$param['attach'] = json_encode(array(
			'order_id' => $order['id'],
			'table'=>$table,
			'type'=>$type,
		));
	$param['notify_url'] = "http://".$_SERVER['HTTP_HOST'].__ROOT__.'/notify.php';

	$jsapi -> set_param($param);
	$uo = $jsapi -> unifiedOrder();
	$jsapi_params = $jsapi -> get_jsApi_parameters();
	return $jsapi_params;
}


// 根据自定义菜单类型返回名称
function get_selfmenu_type($type){
	$type_name = '';
	switch($type){
		case 'click':
			$type_name = '点击推事件';
			break;
		case 'view':
			$type_name = '跳转URL';
			break;
		case 'scancode_push':
			$type_name = '扫码推事件';
			break;
		case 'scancode_waitmsg':
			$type_name = '扫码推事件且弹出“消息接收中”提示框';
			break;
		case 'pic_sysphoto':
			$type_name = '弹出系统拍照发图';
			break;
		case 'pic_photo_or_album':
			$type_name = '弹出拍照或者相册发图';
			break;
		case 'pic_weixin':
			$type_name = '弹出微信相册发图器';
			break;
		case 'location_select':
			$type_name = '弹出地理位置选择器';
			break;
		default : $type_name = '不支持的类型';
	}
	return $type_name;
}


// 根据用户信息取得推广二维码路径信息
function get_qrcode_path($user){
	if(!is_array($user)){
		$user = M('user') -> find($user);
	}
	
	$path = './Public/qrcode/'.date('ym/d/',$user['sub_time']);
	return array(
			'path'		=> $path,
			'new'		=> $path.$user['id'].'_dragondean.jpg',
			'head' 		=> $path.$user['id'].'_head.jpg',
			'qrcode'	=> $path.$user['id'].'_qrcode.jpg',
			'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path,1)
		);
}



// 根据用户信息取得推广二维码路径信息
function getAgentQrcode($imei){
	if(!$imei){
		return false;
	}
	$path = './Public/imei/';
	return array(
			'path'		=> $path,
			'qrcode'	=> $path.$imei.'_qrcode.jpg',
			'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path,1),
		);
}


// 根据用户信息取得推广二维码路径信息
function getChapQrcode($id){
	if(!$id){
		return false;
	}
	$path = './Public/chapter/';
	return array(
			'path'		=> $path,
			'qrcode'	=> $path.$id.'_qrcode.jpg',
			'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path,1),
		);
}

//获得财务记录动作名称
function get_finance_action($action){
	$return = '';
	switch($action){
		case 1: $return = '在线充值';break;
		case 2: $return = '余额支付';break;
		case 3: $return = '订单分成';break;
		case 4: $return = '提现成功';break;
		case 5: $return = '提现退回';break;
		case 6: $return = '取消订单';break;
		case 7: $return = '取消分成';break;
		case 8: $return = '消费漫画';break;
		case 9: $return = '消费小说';break;
		case 10: $return = '签到书币';break;
		case 11: $return = '提现退回';break;
		case 12: $return = '打赏书币';break;
		case 13: $return = '分享赠送书币';break;
		default : $return = '未知操作';
	}
	return $return;
}

// 根据订单提现申请返回状态信息
function get_withdraw_status($status){
	$status_str = '';
	switch($status){
		case -1: $status_str = '已拒绝'; break;
		case 1: $status_str = '待转帐'; break;
		case 2: $status_str = '提现成功'; break;
		case 3: $status_str = '已完成'; break;
		case 4: $status_str = '提交失败'; break;
		default : $status_str = '未知状态';
	}
	return $status_str;
}

// 根据分成请返回状态信息
function get_separate_status($status){
	$status_str = '';
	switch($status){
		case -1: $status_str = '已取消'; break;
		case 1: $status_str = '未分成'; break;
		case 2: $status_str = '已分成'; break;
		default : $status_str = '未知状态';
	}
	return $status_str;
}

// 根据分成请返回分成类型
function get_separate_type($type){
	$type_str = '';
	switch($type){
		case 'lv': $type_str = '代理分成'; break;
		case 'level': $type_str = '合伙人分成'; break;
		default : $type_str = '未知状态';
	}
	return $type_str;
}

//根据条件统计分成
function separate_all($user_id, $self_id) {
	$money_all = 0;
	if(is_numeric($user_id) && $user_id > 0 && is_numeric($self_id) && $self_id > 0 ) {
		$money_all = M('separate_log')->where("status=2 and user_id={$user_id} and self_id={$self_id} ")->sum('money');
		$money_all = floatval($money_all);
	}
	return sprintf("%.2f", $money_all);
}

// 根据订漫画分类返回分类名
function get_mh_cate_name($status){
	$status_str = 'error';
	switch($status){
		case 1: $status_str = '总裁'; break;
		case 2: $status_str = '穿越'; break;
		case 3: $status_str = '校园'; break;
		case 4: $status_str = '恐怖'; break;
		case 5: $status_str = '古风'; break;
		case 6: $status_str = '恋爱'; break;
		case 7: $status_str = '奇幻'; break;
		case 8: $status_str = '热血'; break;
		case 9: $status_str = '悬疑'; break;
		case 10: $status_str = '耽美'; break;
		case 11: $status_str = '都市'; break;
		case 12: $status_str = '爆笑'; break;
		case 13: $status_str = '真人'; break;
	}
	return $status_str;
}

// 将列表变成树形结构
function list_to_tree($list, $parent=0){
	$data = array();
	foreach($list as $v){
		if($v['pid'] == $parent){
			$data[] = array_merge(array(
				'_child' => list_to_tree($list, $v['id'])
			),$v);
		}
	}
	return $data;
}

// 将数变成列表
function tree_to_list($tree ,$level = 0){
	$data = array();
	foreach($tree as $v){
		$temp = $v['_child'];
		unset($v['_child']);
		$data[] = array_merge(array('_level' => $level),$v);
		if(is_array($temp) && count($temp) >0){
			$data = array_merge($data, tree_to_list($temp,$level+1));
		}		
	}
	return $data;
}



//根据金额获得千/万,flag==false 千和千以下不获得单位
function getKWmoney($money,$flag=false){
	if(!$money){
		return 0;
	}else{
		$html = "";
		if($flag){
			if($money>=1000 && $money<10000){
				$money = intval($money/1000);
				$html = "千";
			}elseif($money>=10000){
				$money = intval($money/10000);
				$html = "万";
			}
			$return = $money.$html;
		}else{
			if($money>=10000){
				$money = intval($money/10000);
				$html = "万";
			}
			$return = $money.$html;
		}
	}
	return $return;
}


/** 添加财务日志
*	type => money:余额记录,points:积分记录
*/
function flog($user_id, $type, $money, $action){
	M('finance_log') -> add(array(
		'user_id' => $user_id,
		'type' => $type,
		'money' => $money,
		'action' => $action,
		'create_time' => NOW_TIME
	));
}


/** 
*订单生成分成记录
*/
function separate($order){
	if(!is_array($order)){
		$order = M('order')->find(intval($order));
	}
	if(!$order){
		return false;
	}
	// 如果订单已分成则退出
	if($order['separate']>0){
		return false;
	}
	
	$user = M('user');
	
	// 查询用户信息
	$user_info = $user -> find($order['user_id']);
	if(!$user_info){
		return false;
	}
	
	$site = $GLOBALS['_CFG']['site'];
	
	// 如果是商品设置分成总额分成则计算可分成总额
	if($site['dist'] == 2){
		$total = $order['separate_money'];
	}
	// 否则分成金额就是订单总额
	else{
		$total = $order['money'];
	}
	// 循环分红
	for($i=1; $i<=3; $i++){
		// 检查是否有这一级别的上级
		if(empty($user_info['parent'.$i]) || $user_info['parent'.$i] <1){
			break;
		}
		
		// 查询上级资料
		$parent_info = $user -> find($user_info['parent'.$i]);
		
		if(!$parent_info){
			break; // 这级别代理都木有就没有在上一级了，直接跳出循环
		}
		
		//若上级合伙人或代理级别都没有则不分成
		if($parent_info['level']<1&&$parent_info['lv']<1){
			continue;
		}
		
		// 若等级为合伙人，则分合伙人等级分成
		if($parent_info['level']>0 && $parent_info['lv']>0){
			$dist = $GLOBALS['_CFG']['level'][$parent_info['level']]['separate'.$i];
			$type = 'level';
		}elseif($parent_info['level']>0 && $parent_info['lv']<1){
			$dist = $GLOBALS['_CFG']['level'][$parent_info['level']]['separate'.$i];
			$type = 'level';
		}elseif($parent_info['level']<1 && $parent_info['lv']>0){
			$dist = $GLOBALS['_CFG']['lv'][$parent_info['lv']]['separate'.$i];
			$type = 'lv';
		}
		//若分成比例没设置，则退出
		if(!$dist){
			continue;
		}
		// 进行分红
		$separate_money	= $total * $dist/100; // 分红金额
		M('separate_log') -> add(array(
				'user_id' => $user_info["parent{$i}"],
				'order_id' => $order['id'],
				'self_id' => $order['user_id'],
				'level' => $i,
				'money' => $separate_money,
				'status' => 1,
				'type'=>$type,
				'create_time' => NOW_TIME
			));
		
		M('order') -> where('id='.$order['id']) -> setInc('separate' , 1 );	
	}	
}


//更新用户销售业绩和购买金额和产品交易数(以用户真实购买的金额为标准)
function upUser($order){
	if(!is_array($order)){
		$order = M('order')->find(intval($order));
	}
	$separate = M('separate_log')->where(array('order_id'=>$order['id']))->select();
	foreach($separate as $k=>$v){
		//更改用户销售额度
		M('user')->where(array('id'=>$v['user_id']))->setInc('sales',$order['money']);
	}
	
	//更改用户总购买额度
	M('user')->where(array('id'=>$order['user_id']))->setInc('btotal',$order['money']);
	
	//更改产品交易数量
	M('goods')->where(array('id'=>array('in',$order['goods_id'])))->setInc('sold',1);
	//发放佣金
	doSeparate($order);
	//更改用户等级
	$user = M('user')->where(array('id'=>$v['user_id']))->find();
	upLv($user);
	
	return false;
}


/**
 * 获取惟一代理盐值
 * @return string
 */
//生成唯一用户uid
function Salt() {  
    $autoID = mt_rand(1, 550000);
    $autoCharacter = array("1","2","3","4","5","6","7","8","9","A","B","C","D","E");
    $len = 7-((int)log10($autoID) + 1);
    $i=1;
    $numberID = mt_rand(1, 2).mt_rand(1, 4);
    for($i;$i<=$len-1;$i++)
    {
        $numberID .= $autoCharacter[mt_rand(1, 13)];
    }
    return base_convert($numberID."E".$autoID, 16, 10); //--->这里因为autoid永远不可能为E所以使用E来分割保证不会重复
} 




//根据购买额度更改代理等级
function upLv($user){
	if(!is_array($user)){
		$user = M('user')->find(intval($user));
	}
	$lv = get_lv_money($user['btotal']);
	if($user['lv']<$lv){
		M('user')->where(array('id'=>$user['id']))->save(array('lv'=>$lv));
	}
}

//发放分拥到帐户(总分拥)
function doSeparate($order){
	if(!is_array($order)){
		$order = M('order')->find(intval($order));
	}
	$separate = M('separate_log')->where(array('order_id'=>$order['id']))->select();
	foreach($separate as $v){
		M('user')->where(array('id'=>$v['user_id']))->setInc('money',$v['money']);
		M('user')->where(array('id'=>$v['user_id']))->setInc('expense',$v['money']);
		flog($v['user_id'],'money',$v['money'],3);
	}
}

/** 获取课程分类
*/
function getSorts($sorts_id){
	return M('goods_sorts')->where(array('id'=>$sorts_id))->getField('fname');
}



function create_order_sn($user_id) {
	$sn = date("Ymd").mt_rand(100,999).mt_rand(1000,9999).$user_id;
    return $sn;
}



 //判断是否微信打开
 function is_weixin() { 
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
        return true; 
    } return false; 

 }

 function is_test(){
    $iss = 1;
    return $iss;
 }
/**
 * 获取全部数据
 * @param  string $type  tree获取树形结构 level获取层级结构
 * @param  string $order 排序方式
 * @return array         结构数据
 */
 function getTreeData($table='Menu',$type='tree',$order='',$name='name',$child='id',$parent='pid'){
    // 判断是否需要排序
    if(empty($order)){
        $data=M($table)->select();
    }else{
        $data=M($table)->order($order.' desc')->select();
    }
    // 获取树形或者结构数据
    if($type=='tree'){
        $data=\Org\Nx\Data::tree($data,$name,$child,$parent);
    }elseif($type="level"){
        $data=\Org\Nx\Data::channelLevel($data,0,'&nbsp;',$child);
    }
    return $data;
}


//获取当前距离时间
function gettoTime($time){
	if($time){
		$to_time = time()-$time;
		if(($to_time/3600)<1){
			$string ='刚刚';
		}
		if(($to_time/3600)>1 && ($to_time/3600)<24){
			$string = intval($to_time/3600).'小时前';
		}
		if(($to_time/3600)>=24){
			if(($to_time/(3600*24))>30){
				$string = '1个月前';
			}else{
				$string = intval($to_time/(3600*24)).'天前';
			}
		}
	}
	return $string;
}


// 获得一个标的列表
function show_list($table, $where = null, $order = "id desc", $pagesize = 6 , $handler = null){
	$count = M($table) -> where($where) -> count();
	$page = new \Think\Page($count, $pagesize);
	$list = M($table) -> where($where) -> limit($page -> limit()) -> order($order) -> select();
	//echo M()->getLastSql();
    $handler='';
	if($handler && !empty($list)){
		foreach($list as &$item){
			$item = $handler($item);
		}
	}
	return array(
		'list'	=> $list,
		'page'	=> $page -> show(),
		'count' => $count,
		'total_pages' => $page -> total_pages()
	);
}

//发送短信
function sms($mobile, $con){
	$site = $GLOBALS['_CFG']['site'];
	$content = '【'.$site['smssign'].'】'.$con;
	$url = "http://api.smsbao.com/sms?u=".$site['smsuser']."&p=".md5($site['smspsw'])."&m=".$mobile."&c=".urlencode($content);
	$rt = file_get_contents($url);
	$statusStr = array(
		"0" => "短信发送成功",
		"-1" => "参数不全",
		"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
		"30" => "密码错误",
		"40" => "账号不存在",
		"41" => "余额不足",
		"42" => "帐户已过期",
		"43" => "IP地址限制",
		"50" => "内容含有敏感词"
	);
	if($rt =="0"){
		return 1;
	}else{
		return $statusStr[$rt];
	}
}


/**
 * 对称加密算法之加密
 */
function encode($string = '', $skey = 'Lswig') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 对称加密算法之解密
 */
function decode($string = '', $skey = 'Lswig') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}



function http($url,$data,$method="POST"){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);
	$errorno = curl_errno($ch);
	if(!$errorno)return $tmpInfo;
	else{
		$this->errmsg = $errorno;
		return false;
	}
}

 //返回错误
function jsonError($message = '',$url=null) 
{
	$return['msg'] = $message;
	$return['data'] = '';
	$return['code'] = -1;
	$return['url'] = $url;
	return json_encode($return);
}

//返回正确
function jsonSuccess($message = '',$data = '',$url=null) 
{
	$return['msg']  = $message;
	$return['data'] = $data;
	$return['code'] = 1;
	$return['url'] = $url;
	return json_encode($return);
}

?>