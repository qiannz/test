<?php
class Controller_Api_Appversionsix extends Controller_Api_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Api_Appversionsix::getInstance();
    }
    /**
     * 首页
     */
    public function homeAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    
    	$data = $this->_model->getHomeList($getData, $this->_city);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 猜你喜欢更多
     */
    public function loveMoreAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    	$data = $this->_model->getLoveList($getData, $this->_city);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 用户订单列表
     */
    public function getOrderListToUserAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    	//判断是否登录
    	$this->isLogin($getData['uuid'], $getData['uname']);
    	//分页
    	$page = !$getData['page'] || intval($getData['page']) < 0 ? 1 : intval($getData['page']);
    	//用户信息
    	$userInfo = $this->getWebUserId($getData['uuid']);
    	$param = array(
    				'mobile' 			=> 		$userInfo['Mobile'],
    				'uuid' 				=> 		$getData['uuid'],
    				'order_status' 		=> 		$getData['order_status'],
    				'order_time_slot' 	=> 		$getData['order_time_slot']
    			);
    	$data = Custom_AuthTicket::getOrderListToUser($param, $page);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 达人说列表
     */
    public function darenListAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    	
    	$data = $this->_model->getDarenList($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    public function activeAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    	$data = array();
    	$opentype = $getData["opentype"];
    	if( $opentype == "activity_one_yuan_purchase" || $opentype == "activity_one_yuan_purchase_view" ){
    		$cmark = "app_home_six_oneyuanpurchase";
    		$data = Model_Api_App::getInstance()->getListByMark($this->_city, 'app_home_version_six', $cmark, 1);
    	}else if( $opentype == "activity_come_and_grab" || $opentype=="activity_come_and_grab_view" ){
    		$cmark = "app_home_six_comeandgrap";
    		$data = Model_Api_App::getInstance()->getListByMark($this->_city, 'app_home_version_six', $cmark, 1);
    	}
    	exit(json_encode($this->returnArr(1, empty($data)?array():$data[0] )));
    }
}
?>