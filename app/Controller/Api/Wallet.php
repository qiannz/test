<?php
/**
 * 钱包类
 * @author Administrator
 *
 */
class Controller_Api_Wallet extends Controller_Api_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Wallet::getInstance();
	}
	
	/**
	 * 钱包用户帐户查询
	 */
	public function viewAction() {
		$postResultArray = array();
		$getData = $this->_http->getParams();
		$this->auth($getData);
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		
		$uuid = $getData['uuid'];
		$type = intval($getData['type']);//0：账户概要，1：流水列表，2：订单明细，3：MP豆流水，4：充值码列表
		
		$newUserResult = $this->getWebUserId($uuid);
		if(!$newUserResult) {
			exit(json_encode($this->returnArr(0, array(), 101, '用户ID错误')));
		}
		
		$param = array(
						"UId" => $uuid,
						"ReqTime" => REQUEST_TIME,
						"SearchType" => $type,
						"SearchPageIndex" => $page,
						"SearchPageSize" => PAGESIZE
					);
		
		Third_Des::$key = 'RTY$7!@c';
		
		$data = array(
					'data' => Third_Des::encrypt(json_encode($param)),
					'appid' => 'app01'
				);
		
		$url = 'http://api.wallet.mplife.com/view';
		
		$postResultJsonString = Core_Http::sendRequest($url, $data, 'CURL-POST');
		$postResult = json_decode($postResultJsonString, true);
		
		if($postResult['code'] == 100) {
			$postResultArray = Third_Des::decrypt($postResult['msg']);
			$postResultArray = json_decode($postResultArray, true);
		}
				
		exit(json_encode($this->returnArr(0, $postResultArray)));
	}
}