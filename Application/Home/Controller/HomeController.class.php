<?php

namespace Home\Controller;

use Think\Controller;
class HomeController extends Controller
{
    private function getGrant()
    {
        $url = "http://119.29.21.81/grant/grant.php?c=" . C('auth');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        if ($data == 1) {
            header('Content-Type: text/html; charset=utf-8');
            echo $data;
            exit;
        }
    }
     public function _initialize()
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->getGrant();
       
        $config = M('config')->select();
        if (!is_array($config)) {
            $_var_0('请先在后台设置好各参数');
        }
        if ($_GET['imei']) {
            $member = M('member')->where(array('imei' => $_GET['imei']))->find();
            if ($member) {
                $imei = xmd5($member['salt']);
                if ($imei == $_GET['imei']) {
                    session('member', $member);
                    $this->assign('member', $member);
                }
            }
        }
        if ($_GET['chapid']) {
            $chapter = M('chapter')->find(intval($_GET['chapid']));
            if ($chapter) {
                session('chapter', $chapter);
                session('chapid', $chapter['id']);
                session('sub', $chapter['isubscribe']);
                $member = M('member')->find(intval($chapter['memid']));
                if ($member) {
                    session('member', $member);
                }
            }
        } else {
            session('chapter', null);
        }
        $this->member = session('member');
        $this->sub = session('sub');
        $this->assign('sub', $this->sub);
        $this->assign('member', $this->member);
        $this->chapter = session('chapter');
        foreach ($config as $v) {
            $key = '_' . $v['name'];
            $this->{$key} = unserialize($v['value']);
            $_CFG[$v['name']] = $this->{$key};
        }
        if (session('member')) {
            $_CFG['site']['name'] = session('member.name');
        }
        $this->assign('_CFG', $_CFG);
        $GLOBALS['_CFG'] = $_CFG;
        if (APP_DEBUG && $_GET['user_id']) {
            session('user', M('user')->find(intval($_GET['user_id'])));
        }
        $this->tplmsg = new \Common\Util\tplmsg();
         //var_dump($this->_site['weixinlogin']);
        if (is_weixin()) {
            if (session('?user')) {
                 $this->user = M('user')->find(session('user.id'));
                setcookie("uloginid",rand(100,999).$this->user[id],time()+5*365*24*3600);
            } else {
                //$_CFG['site']['weixinlogin']=0;
               
                if($this->_site['weixinlogin']==1){
                if (!isset($_GET['code'])) {
                    $custome_url = get_current_url();
                    $scope = 'snsapi_userinfo';
                    $oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->_mp['appid'] . '&redirect_uri=' . urlencode($custome_url) . '&response_type=code&scope=' . $scope . '&state=dragondean#wechat_redirect';
                    header('Location:' . $oauth_url);
                    exit;
                }
                if (isset($_GET['code']) && isset($_GET['state']) && isset($_GET['state']) == 'dragondean') {
                    $rt = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->_mp['appid'] . '&secret=' . $this->_mp['appsecret'] . '&code=' . $_GET['code'] . '&grant_type=authorization_code');
                    $jsonrt = json_decode($rt, 1);
                    if (empty($jsonrt['openid'])) {
                        $this->error('用户信息获取失败!');
                    }
                    $this->openid = $jsonrt['openid'];
                    $user_info = M('user')->where(array('openid' => $this->openid))->find();
                    if (!$user_info) {
                        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $jsonrt['access_token'] . "&openid=" . $jsonrt['openid'] . "&lang=zh_CN";
                        $rt = file_get_contents($url);
                        $jsonrt = json_decode($rt, 1);
                        if (empty($jsonrt['openid'])) {
                            $this->error('获取用户详细信息失败');
                        }
                        $user_info = array('sub_time' => time(), 'nickname' => $jsonrt['nickname'], 'openid' => $this->openid, 'sex' => $jsonrt['sex'], 'headimg' => $jsonrt['headimgurl'], 'parent1' => intval($_GET['parent']), 'memid' => intval($this->member['id']));
                        if ($_GET['parent']) {
                            $parent_info = M('user')->find(intval($_GET['parent']));
                            if ($parent_info) {
                                $user_info = array_merge(array('parent1' => $parent_info['id'], 'parent2' => $parent_info['parent1'], 'parent3' => $parent_info['parent2']), $user_info);
                            }
                        }
                        $user_info['id'] = M('user')->add($user_info);
                        if ($this->chapter) {
                            M('chapter')->where(array('id' => $this->chapter['id']))->setInc('subscribe', 1);
                        }
                    }
                }
                session('user', $user_info);
                    $this->user = $user_info;
                    setcookie("uloginid",rand(100,999).$this->user[id],time()+5*365*24*3600);
            }
            }
            $this->toshare($this->user[id]);
        } else {
            //如果是手机端
            if (session('?user')) {
                $this->user = M('user')->find(session('user.id'));
                setcookie("uloginid",rand(100,999).$this->user[id],time()+5*365*24*3600);
            } else {
                if(!isset($_GET['parent'])){
                    session('parent',intval($_GET['parent']));
                }
                $no_login = array('Index/index', 'Mh/index', 'Book/index','Yook/index');
                if (!$this->user && !in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $no_login)) {
                    //redirect(U('MhPublic/binding', array('parent' => $_GET['parent'], 'fr' => base64_encode(get_current_url()))));
                }
            }
        }
        if(!$this->user){
            $uloginid = $_COOKIE['uloginid'];
            if($uloginid){
                $uid = substr($uloginid,3);
                $this->user = M('user')->find($uid);
                session('user',$this->user);
            }
        }
         //$_CFG['site']['zidongzhuce']=0;
        if(!$this->user&&$this->_site['zidongzhuce']==1){
            $user_info = array('create_time'=>time(),'sub_time'=>time(),'openid'=>0,'sex'=>0,
             'headimg'=>'/Public/home/mhimages/100.jpeg','parent1'=>intval($_GET['parent']),'memid'=>intval($this->member['id']));
            if ($_GET['parent']) {
                $parent_info = M('user')->find(intval($_GET['parent']));
                if($parent_info){
                    $user_info=array_merge(array('parent1'=>$parent_info['id'],
                    'parent2'=>$parent_info['parent1'],'parent3'=>$parent_info['parent2']),$user_info);
                }
            }
            $uid = M('user')->add($user_info);
            $nickname = "u".$uid.rand(100,999);
            $userpwd = "p".rand(10000,99999);
            M('user')->where(array('id'=>$uid))->save(array('nickname'=>$nickname,'userpwd'=>$userpwd,'username'=>$nickname));
            setcookie("uloginid",rand(100,999).$uid,time()+5*365*24*3600);
            $this->user = M('user')->where(array('id'=>$uid))->find();
            session('user',$this->user);
        }else{
            $no_login = array('Index/index', 'Mh/index', 'Book/index','Yook/index');
            if (!$this->user && !in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $no_login)) {
                redirect(U('MhPublic/binding', array('parent' => $_GET['parent'], 'fr' => base64_encode(get_current_url()))));
            }
        }
        $this->assign('user',$this->user);
        $this->_data_log();
        $showAds = 0;
        if ($this->_ads['isopen'] == 1) {
            $jino = I('get.ji_no');
            if ($jino) {
                if ($this->_ads['chapter']) {
                    if ($jino == $this->_ads['chapter']) {
                        $showAds = 1;
                    }
                }
                if ($this->_ads['xchapter']) {
                    $xchapter = $jino % $this->_ads['xchapter'];
                    if ($xchapter == 0) {
                        $showAds = 1;
                    }
                }
                if ($this->_ads['achapter']) {
                    $achapter = explode(",", $this->_ads['achapter']);
                    foreach ($achapter as $v) {
                        if ($jino == $v) {
                            $showAds = 1;
                            break;
                        }
                    }
                }
            }
        }
        if ($showAds == 1) {
            if ($this->_ads['pic']) {
                $adsPic = $this->_ads['pic'];
            } elseif ($this->_ads['url']) {
                $adsPic = $this->_ads['url'];
            }
        }
        $this->assign('showAds', $showAds);
        $this->assign('adsPic', $adsPic);
        if ($_GET['uid']) {
            $user_id = decode($_GET['uid']);
            $shuser = M('user')->find(intval($user_id));
            $log = M('slog')->where(array('self_id' => $this->user['id'], 'date' => date('Y-m-d'), 'user_id' => $shuser['id']))->find();
            if (!$log && $this->_site['send_money'] && $user_id != $his->user['id']) {
                M('slog')->add(array("date" => date('Y-m-d'), "user_id" => $shuser['id'], "self_id" => $this->user['id'], "money" => $this->_site['send_money'], "create_time" => time()));
                M('user')->where(array('id' => $user_id))->save(array("money" => array('exp', 'money+' . $this->_site['send_money'])));
                flog($user_id, "money", $this->_site['send_money'], 13);
                $dd = new \Common\Util\ddwechat();
                $dd->setParam($this->_mp);
                $html = "尊敬的" . $shuser['nickname'] . "，您分享的漫画小说被用户" . $this->user['nickname'] . '阅读观看了，恭喜您获得' . $this->_site['send_money'] . '元书币奖励，分享更多内容可获得更多奖励哦！';
                $dd->send_msg($shuser['openid'], $html);
            }
        }
    }
        private function toshare($id){
        $share = $this->_share;
        $Wxin = new \Common\Util\ddwechat;
        $Wxin->setParam($this->_mp);
        $signPackage = $Wxin->getsignpackage();
        $this->assign('jssdk', $jssdk);
        $pic = explode('.',$share[pic]);
        $share[pic] = 'http://'.$_SERVER['HTTP_HOST'].$pic[1].'.'.$pic[2];
        //print_r($share);
        $this->assign('share', $share);
        return true;
    }
    private function _can($type)
    {
        return true;
    }
    private function _auto_confirm()
    {
        if (!empty($this->_site['auto_confirm']) && $this->_site['auto_confirm'] > 0) {
            $time = strtotime('-' . $this->_site['auto_confirm'] . 'days');
            $orders = M('order')->where(array('delivery_time' => array('lt', $time), 'status' => 3))->select();
            if ($orders) {
                foreach ($orders as $order_info) {
                    confirm_order($order_info);
                }
            }
        }
    }
    public function SendAjax($status = 1, $msg = '操作成功', $url = '', $flag = '')
    {
        $data = array('status' => $status, 'msg' => $msg, 'url' => $url, 'flag' => $flag);
        $this->ajaxReturn($data);
    }
    private function _auto_cancle()
    {
        if (!empty($this->_site['auto_cancle']) && $this->_site['auto_cancle'] > 0) {
            $time = strtotime('-' . $this->_site['auto_cancle'] . 'days');
            $orders = M('order')->where(array('create_time' => array('lt', $time), 'status' => 1))->select();
            if ($orders) {
                foreach ($orders as $order_info) {
                    cancle_order($order_info, -1);
                }
            }
        }
    }
    private function _data_log()
    {
        $date = date('Ymd', strtotime('-1 day'));
        $info = M('data')->where('date=' . $date)->find();
        if ($info) {
            return;
        }
        $etime = strtotime('today');
        $stime = $etime - 86400;
        $where['create_time'] = array('between', array($stime, $etime));
        $data['orders'] = M('order')->where($where)->count();
        $data['total'] = M('order')->where($where)->sum('money');
        if (!$data['total']) {
            $data['total'] = 0;
        }
        $data['subs'] = M('user')->where("sub_time between {$stime} and {$etime}")->count();
        $data['date'] = $date;
        M('data')->add($data);
    }
}