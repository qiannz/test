<?php
class Controller_Api_Appversion extends Controller_Api_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_App::getInstance();
	}
	
	/**
	 * APP版本控制   是否需要强制更新
	 */
	public function infoAction() {
		$getData = $this->_http->getParams();

		//传输加密验证
		$this->auth($getData);
		
		if (empty($getData['type'])) {
			exit(json_encode($this->returnArr(0, array(), 101, '手机类型为空')));
		}
		
		if (empty($getData['version'])) {
			exit(json_encode($this->returnArr(0, array(), 102, '手机版本号为空')));
		}
		
		if ($getData['type'] == 'android' && empty($getData['channel'])) {
			exit(json_encode($this->returnArr(0, array(), 103, '安卓手机来源为空')));
		}

		$data = $this->_model->getVersionInfo($getData);
		               
		exit(json_encode($this->returnArr(1, $data)));
	}
	
}
