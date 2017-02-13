<?php
class Controller_Api_Deals extends Base {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Deals::getInstance();	
		$this->_city = !$this->_http->get('city') ? $this->_city : strval($this->_http->get('city'));
	}
	
	private function auth($getData) {
		$encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['Auth_Key'];
		if (!$getData['ssid'] || ($getData['ssid'] != md5($encryptString))) {
			exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
		}
	}
	
	public function getSpecialListAction() {
		$getData = $this->_http->getParams();
		$this->auth($getData);
		
		$specialArray = $this->_model->getSpecialList($getData, $this->_city);
		exit(json_encode($specialArray));
	}
	
}