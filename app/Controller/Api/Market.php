<?php
class Controller_Api_Market extends Controller_Api_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Api_Market::getInstance();
    }
    /**
     * 首页
     */
    public function marketListAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    
    	$data = $this->_model->getMarket($getData, $this->_city);
    	exit(json_encode($this->returnArr(1, $data)));
    }
}
?>