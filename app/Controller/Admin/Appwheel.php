<?php
class Controller_Admin_Appwheel extends Controller_Admin_Abstract {
	
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Appwheel::getInstance();
	}
	
	
	public function listAction() {
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$this->_model->setWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$appWheels = $this->_model->getAppWheelList($page);
		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('appWheels', $appWheels);
		$this->_tpl->assign('page', $page);
		
		$this->_tpl->display('admin/app_wheel_list.php');
	}
	
	public function validAction() {
		$id = $this->_http->get('id');
		$result = $this->_model->validWheel($id);
		if($result) {
			exit('ok');
		}
	}
	
}