<?php 
class Controller_Admin_Usergood extends Controller_Admin_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Usergood::getInstance();
	}

	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);

		$page_str = '';
		$getData = $this->_http->getParams();

		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getList($page);
		$page_info['item_count'] = $this->_model->getCount();

		if($page_str){
			$page_info['page_str'] = $page_str;
		}

		$this->_format_page($page_info);

		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->display('admin/user_good_list.php');
	}
}