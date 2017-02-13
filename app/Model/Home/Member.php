<?php 
class Model_Home_Member extends Base
{
	private static $_instance;
	private $_table = 'oto_merchant_app';
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getMerchantAppByUserId($user_id, $shop_id = 0) {
		$where = '';
		if($shop_id) {
			$where .= " and `shop_id` = '{$shop_id}'";
		}
		return $this->select("`user_id` = '{$user_id}' {$where}", $this->_table, '*', '', true);
	}
	/**
	 * 商户入驻 新增/修改
	 * @param unknown_type $getData
	 * @param unknown_type $userInfo
	 */
	public function replaceMerchantApp($getData, & $userInfo, &$_file) {
		$real_name = Custom_String::HtmlReplace($getData['real_name']);
		$mobile = Custom_String::HtmlReplace($getData['mobile']);
		$shop_id = intval($getData['sid']);
		$shop_name = Custom_String::HtmlReplace(trim($getData['shop_name']), -1);
		$shop_address = Custom_String::HtmlReplace($getData['shop_address']);
		$id_img = Custom_Upload::singleImgUpload($_file['id_img'], 'verify');
		$bus_img = Custom_Upload::singleImgUpload($_file['bus_img'], 'verify');
		
		$user_id = $userInfo['user_id'];
		$user_name = $userInfo['user_name'];
		
		$param = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'real_name' => $real_name,
					'mobile' => $mobile,
					'shop_name' => $shop_name,
					'shop_address' => $shop_address,
					'id_img' => $id_img,
					'bus_img' => $bus_img,
					'auth_status' => 1
				);
		
		if($this->isMerchantExist($user_id, $shop_id)) {
			$param = array_merge($param, array('updated' => REQUEST_TIME));
			$this->_db->update($this->_table, $param, "`user_id` = '{$user_id}' and `shop_id` = '{$shop_id}'");
		} else {
			$param = array_merge($param, array('shop_id' => $shop_id, 'created' => REQUEST_TIME));
			$this->_db->insert($this->_table, $param);
		}
	}
	/**
	 * 判断商户入驻资料是否存在
	 * @param unknown_type $user_id
	 * @return boolean
	 */
	public function isMerchantExist($user_id, $shop_id) {
		return $this->_db->fetchOne("select 1 from `oto_merchant_app` where `user_id` = '{$user_id}' and `shop_id` = '{$shop_id}' limit 1") == 1;
	}
	
	public function getMerchantNumByUserId($user_id) {
		return $this->_db->fetchOne("select count(*) from `" . $this->_table . "` where `user_id` = '{$user_id}'");
	}
	/**
	 * 商户资料补全
	 * @param unknown_type $getData
	 * @param unknown_type $userInfo
	 * @param unknown_type $_file
	 */
	public function updateMerchantDataCompletion($getData, & $userInfo, & $_file) {
		$store_id = intval($getData['store_id']);
		$bname = Custom_String::HtmlReplace($getData['bname'], 2);
		$bid = Model_Admin_Shop::getInstance()->getBrandId($bname);
		$brand_img = Custom_Upload::singleImgUpload($_file['brand_img'], 'verify');
		$pack_id = intval($getData['pack_id']);
		$alipay_acount = Custom_String::HtmlReplace($getData['alipay_acount'], -1);
		$alipay_name = Custom_String::HtmlReplace($getData['alipay_name'], -1);
		
		$user_id = $userInfo['user_id'];
		$shop_id = intval($getData['sid']);
		
		$param = array(
					'store_id' => $store_id,
					'brand_id' => $bid,
					'brand_name' => $bname,
					'pack_id' => $pack_id,
					'brand_img' => $brand_img,
					'alipay_acount' => $alipay_acount,
					'alipay_name' => $alipay_name,
					'auth_status' => 3
				);
		$param = array_merge($param, array('updated' => REQUEST_TIME));
		$this->_db->update($this->_table, $param, "`user_id` = '{$user_id}' and `shop_id` = '{$shop_id}'");
	}
}