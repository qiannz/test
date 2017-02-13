<?php
class Controller_Admin_Order extends Controller_Admin_Abstract {
    private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Order::getInstance();	
	}
	
	public function listAction() {
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$page_str = '';
		$getData = $this->_http->getParams();
			
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}		
		
		$getData['page'] = $page;
		$getData['pagesize'] = PAGESIZE;
		$data = $this->_model->getList($getData);
		
		$page_info = $this->_get_page($page);
		$page_info['item_count'] = $data['totalNum'];
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/order_list.php');
	}
	
	public function changeOrderAction() {
		$getData = $this->_http->getParams();
		$excResultArr = Custom_AuthTicket::changeOrder($getData['method'], $getData['orderNo'], $this->_userInfo['userid'], $getData['expressCompany'], $getData['expressNumber']);
		if($excResultArr['code'] == 1) {
			_exit('sucess', 100);	
		} else {
			_exit('sucess', 300, $excResultArr['message']);
		}
	}
}