<?php
class Controller_Home_Discount extends Controller_Home_Abstract {

	public function __construct() {
		parent::__construct();
	}
	
	/*
	 * 折扣详情
	 */
	public function wapShowAction(){
		$discount_id = $this->_http->has('did')?$this->_http->get('did'):0;
		if (!$discount_id) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		$discountRow = Model_Api_Discount::getInstance()->getDiscountContent(array("did"=>$discount_id), $this->_city);
		if( empty($discountRow) ){
			Custom_Common::jumpto('/404/404.html');
		}
		$this->_tpl->assign('discount',$discountRow["discount"]);
		$this->_tpl->display('discount/discount.php');
	}
	
}