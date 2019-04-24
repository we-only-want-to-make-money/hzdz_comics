<?php
namespace Home\Model;
use Think\Model;

class ProductModel extends Model{
	
	// 获取商品信息
	public function get_info($product_id, $attr = null){
		$product_info = $this -> find($product_id);
		if($attr){
			$attr_info = M('product_attr') -> where(array(
				'product_id' => $product_id,
				'attr' => $attr
			)) -> find();
			
			// 查到了属性对应的信息则价格和库存替换为属性的信息
			if($attr_info){
				$product_info['price'] = $attr_info['price'];
				$product_info['stock'] = $attr_info['stock'];
			}
			// 没有查到属性的信息则价格使用商品价格，库存为0
			else{
				$product_info['stock'] = 0;
			}
		}
		return $product_info;
	}
	
	// 增加库存
	public function set_stock_inc($nums, $product_id, $attr){
		M('product') -> where('id='.$product_id) -> setInc('stock', $nums);
		if($attr){
			M('product_attr') -> where(array(
				'product_id' => $product_id,
				'attr' => $attr
			)) -> setInc('stock', $nums);
		}
	}
	
	//减少库存
	public function set_stock_dec($nums, $product_id, $attr){
		M('product') -> where('id='.$product_id) -> setDec('stock', $nums);
		if($attr){
			M('product_attr') -> where(array(
				'product_id' => $product_id,
				'attr' => $attr
			)) -> setDec('stock', $nums);
		}
	}
}