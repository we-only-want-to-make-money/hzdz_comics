<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 系统ajax调用
 */
class IndexAjaxController extends HomeController {

	public function _initialize(){
		parent::_initialize();
	}
	

	
	//商品详情页，加入购物车
    public function addCart(){
		$goods_id = $_POST['gid'];
		$goods_info = M('goods')->find(intval($goods_id));
		if(!$goods_info){
			$this->error('未找到课程信息！');
		}
		if(M('cart')->where(array('user_id'=>$this->user['id'],'goods_id'=>$goods_id,'status'=>0))->find()){
			$this->error('该课程已经存在你购物车了！');
		}
		if($this->user['lv']>0 || $this->user['level'] >0){
			if(!$this->user['lv']){
				 $data['market_price'] = $goods_info['price'];
			 }else{
				$data['market_price'] = ($this->_lv[$this->user['lv']]['coupon'] * $goods_info['price'])/100 ;
			 }
		}else{
			$data['market_price'] = $goods_info['price'];
		}
		$data['user_id'] = $this->user['id'];
		$data['goods_id'] = $goods_id;
		$data['order_id'] = 0;
		$data['title'] = $goods_info['name'];
		$data['pic'] = $goods_info['pic'];
		$data['price'] = $goods_info['price'];
		$data['create_time'] = time();
		if(M('cart')->add($data)){
			$this->success('添加购物车成功');
		}else{
			$this->error('添加购物车失败');
		}
    }

	//删除购物车
	public function delCart(){
		$id = I('post.id');
		if(M('cart')->where(array('id'=>$id))->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
		
	}
	
	//对商品点赞
	public function dznums(){
		$gid = I('post.gid');
		if(M('goods')->where(array('id'=>$gid))->setInc('dznums',1)){
			$this->success('点赞成功');
		}else{
			$this->error('点赞失败');
		}
	}
	
	
	//视频打赏
	public function dsGoods(){
		if(IS_POST){
			$post = I('post.');
			if($post['gid'] && $post['money']){
				$data = array(
					'user_id'=>$this->user['id'],
					'sn'=>$this->user['id'].date('YmdHis').rand(1000,9999),
					'gid'=>$post['gid'],
					'money'=>$post['money'],
					'create_time'=>time(),
				);
				$data['id'] = M('grow')->add($data);
				if($data['id']){
					$params = wxPay($data['id'],'grow');
					$this -> success($params);
				}
			}else{
				$this->error('参数错误');
			}
		}else{
			$this->error('非法请求');
		}
	}
	
	/**
     * 公共上传图片方法
     */
	public function Upload(){
        $base64_image_content = I("post.img");
        $image_name = I("post.name");
        $len = I("post.size");
        $baseLen = strlen($base64_image_content);
        if($len!=$baseLen)  $this->error("上传图片不完整");
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $uploadFolder  = C('UPLOADPATH').date("Ymd")."/";
            if(!is_dir($uploadFolder)){
				if(!mkdir($uploadFolder, 0755, true)){
					$this->error('创建文件失败');
				}
            }
            $type = $result[2];
            if(empty($image_name)){
                $new_file = $uploadFolder.date("His")."_".mt_rand(0, 1000).".{$type}";
            }else{
                $new_file = $uploadFolder.$image_name."_".date("mdHis").".{$type}";
            }
			$img_64 = base64_decode(str_replace($result[1], '', $base64_image_content));
            if (file_put_contents($new_file,$img_64)){
                $this->success(complete_url($new_file));
            }
        }else{
            $this->error("图片不存在");
        }
        
    }
	
	
}