<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller {
    public function index(){
		// 加载配置
		$config = M('config') -> select();
		if(!is_array($config)){
			die('请先在后台设置好各参数');
		}
		foreach($config as $v){
			$key = '_'.$v['name'];
			$this -> $key = unserialize($v['value']);
			$_CFG[$v['name']] = $this -> $key;
			$GLOBALS['_CFG'] = $_CFG;
		}

		// 验证URL
		if(isset($_GET['echostr'])){
			die($_GET['echostr']);
		}
				
		$dd = new \Common\Util\ddwechat;
		$this -> dd = $dd;
		$this -> data = $dd -> request();

		// 判断mp配置
		if(!$this -> _mp){
			$dd -> response('管理员没有配置公众号信息');
			exit;
		}
		
		// TODO 可以在这里判断fromusername和配置中的微信数据是否匹配来增加安全性
		$dd -> setParam($this -> _mp);
		//如果是关注
		if($this -> data['msgtype'] == 'event'){
			// 关注
			if($this -> data['event'] == 'subscribe'){
				$user_info = M('user') -> where("openid='".$this -> data['fromusername']."'") -> find();
				if(!$user_info){
					// 首次关注,将用户信息保存到数据库
					$accesstoken = $dd -> getaccesstoken();
					if(!$accesstoken && APP_DEBUG){
						$dd -> response('accesstoken获取失败:' . $dd -> errmsg);
					}
					$wechat_user = $dd -> getuserinfo($this -> data['fromusername']);
					if(!$wechat_user && APP_DEBUG){
						$dd -> response('获取用户信息失败:'. $dd -> errmsg);
					}
					$user_info = array(
							'openid' => $this -> data['fromusername'],
							'subscribe' => 1,
							'sub_time' => NOW_TIME,
							'nickname' => $wechat_user['nickname'],
							'headimg' => $wechat_user['headimgurl'],
							'sex' => $wechat_user['sex']
					);
					$rs = M('user') -> add($user_info);
					if(!$rs && APP_DEBUG){
						$dd -> response('保存用户信息失败');
					}
					
					$user_info['id'] = $rs;
					
					// 如果是带参数的二维码则锁定上级关系
					if(!empty($this -> data['eventkey'])){
						//$dd -> response($this -> data['eventkey']);
						$param = str_replace('qrscene_user_','', $this -> data['eventkey']);
						if(intval($param) >0){
							$parent_info = M('user') -> find(intval($param));
							M('user') -> where('id='.$rs) -> save(array(
								'parent1' => $parent_info['id'],
								'parent2' => $parent_info['parent1'],
								'parent3' => $parent_info['parent2']
							));
							// 增加上级的统计
							M('user') -> where('id='.$parent_info['id']) -> setInc('agent1');
							M('user') -> where('id='.$parent_info['parent1']) -> setInc('agent2');
							M('user') -> where('id='.$parent_info['parent2']) -> setInc('agent3');
							// 通知上级
							$tplmsg = new \Common\Util\tplmsg;
							$tplmsg -> invite_notice($parent_info, $user_info);
						}
					}
				}else{
					M('user')->where(array('id'=>$user_info['id']))->save(array('subscribe'=>1));
				}
				
				//如果设置了关注时回复关键词则调用回复
				if(!empty($this -> _site['subscribe'])){
					$this -> reply_by_keyword($this -> _site['subscribe'],$user_info['id']);
				}
			}
			
			// 取消关注
			elseif( $this -> data['event'] == 'unsubscribe'){
				$rs = M('user') -> where(array('openid' => $this -> data['fromusername'])) -> setField('subscribe', 0);
			}
			
			// 点击自定义菜单
			elseif( $this -> data['event'] == 'CLICK'){
				$this -> reply_by_keyword($this -> data['eventkey']);
			}
		}
		
		
		// 如果是发送文字
		elseif($this -> data['msgtype'] == 'text' && !empty($this -> data['content'])){
			$this -> reply_by_keyword($this -> data['content']);
			
		}
		
		// 未处理的事件全部返回空
		else{
			exit('success');
		}
		
		exit('success');
    }
	
	// 根据关键词回复
	private function reply_by_keyword($key,$user_id=""){
		
		$dd = &$this -> dd;
		//优先查询观看记录
		$user = M('user')->find(intval($user_id));
		$read = M('read')->where(array('user_id'=>$user_id))->order('create_time desc')->find();
		if(!$read){
			$sub = M('autoreply')->where(array('keyword'=>$key))->find();
			if($sub['type'] == 1){
				$dd->response($sub['content']);
			}elseif($sub['type'] == 2){
				file_put_contents('a.txt',$key,FILE_APPEND);
				// 查询所有文章
				$articles = M('article') -> where(array(
					'autoreply_id' => $sub['id']
				)) -> limit(10) -> order('id desc') -> select();

				foreach($articles as $article){
					$msgs[] = array(
						'title' => $article['title'],
						'description' => $article['desc'],
						'picurl' => complete_url($article['cover']),
						'url' => complete_url(U('Article/read?id='.$article['id']))
					);
				}
				file_put_contents('a.txt',var_export($msgs,1),FILE_APPEND);
				$dd -> response(array('articles' => $msgs), 'news');
			}
		}else{
			
			if($read['type'] == "xs"){
				$ji = M('book_episodes')->where(array('bid'=>$read['rid'],'ji_no'=>$read['episodes']))->find();
				$url = U('Book/inforedit',array('bid'=>$read['rid'],'ji_no'=>$read['episodes']));
			}else{
				$ji = M('mh_episodes')->where(array('mhid'=>$read['rid'],'ji_no'=>$read['episodes']))->find();
				$url = U('Mh/inforedit',array('mhid'=>$read['rid'],'ji_no'=>$read['episodes']));
			}
			$url = complete_url($url);
			$html = '欢迎关注'.$this->_site['name'].'，您上次看到了《'.$read['title'].'》第'.$read['episodes'].'章'.$ji['title']."\n\n";
			$html.='<a href="'.$url.'">【点击继续阅读】</a>';
			$html.="\n\n";
			$html.="为方便下次阅读，请置顶公众号";
			$html.="\n\n";
			$html.='点击签到，即可获得'.$this->_site['sign'].'书币';
			
			$dd -> send_msg($user['openid'],$html);
		}
	}
}