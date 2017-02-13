<?php
class Model_Active_Scratch extends Base {
	
	private static $_instance;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getMobileRow($user_id) {
		return  $this->select_one("`user_id` = '{$user_id}'" , "oto_ticekt_scratch");
	}
	
	public function inertMobileRow($param) {
		return $this->_db->insert("oto_ticekt_scratch", $param);
	}
	
	
	public function updateSendStatus($user_id) {
		return $this->_db->update("oto_ticekt_scratch", array('is_sync' => '1'), array('user_id' => $user_id) );
	}
	
	public function getLatestRow() {
		$latestRow = $this->select("`is_award` = '1'", "oto_ticekt_scratch", '*', 'created desc', true);
		if($latestRow) {
			$latestRow['mobile'] = substr_replace($latestRow['mobile'], "****" , 3, 4);
		}
		return $latestRow;
	}
	
	public function getAwardNum() {
		return $this->_db->fetchOne("select count(id) from `oto_ticekt_scratch` where `is_award` = '1'");
	}
	
	public function sendMessage($mobile, $code) {
		$content = "恭喜您，获得100元名品购现金抵用券。 请在APP名品购栏目里使用，可以购买任何一件不低于100的商品。 三日内有效。 券码：{$code}";	
		Custom_Send::sendMobileMessage($mobile,  $content);
	}
	
	public function getUserId( $output ){
		$user_id = 0;
		if( !empty($output['uuid']) ){
			$userInfo = $this->getWebUserId($output['uuid'], true);
			if( !empty($userInfo["Mobile"]) && !empty($userInfo['user_id']) ){
				$user_id = $userInfo['user_id'];
				cookie('ONEYUANPURCHASE_USER_ID', Third_Des::encrypt($user_id));
			}else{
				cookie('ONEYUANPURCHASE_USER_ID', '');
			}
		}
		return $user_id;
	}
}