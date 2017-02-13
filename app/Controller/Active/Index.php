<?php
class Controller_Active_Index extends Base {
	
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Index::getInstance();

	}
	
	public function indexAction() {
		$city = $this->_http->has('city') ? strval($this->_http->get('city')) : $this->_city;
		$configArray = @include VAR_PATH . 'config/config.php';
		if($configArray['APP_DAY_SURPRISE'][0][$city]) {
			Custom_Common::jumpto($configArray['APP_DAY_SURPRISE'][0][$city]);
		}		
		$headArr = Model_Home_Index::getInstance()->getRecommendListByIdentifier('phone_head', $city, 1);		
		Custom_Common::jumpto($headArr[0]['www_url']);
	}
}