<?php 
class Controller_Admin_Usergoodcollect extends Controller_Admin_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Usergoodcollect::getInstance();
	}

	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);

		$page_str = '';
		$getData = $this->_http->getParams();

		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page', 'isd'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		if(!$getData['uid'] && !empty($getData['uname'])) {
			$user_id = $this->getUserIdByUserName($getData['uname']);
		} else {
			$user_id = $getData['uid'];
		}
		$getData['user_id'] = $user_id;	
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getList($page);
		$page_info['item_count'] = $this->_model->getCount($user_id);

		if($page_str){
			$page_info['page_str'] = $page_str;
		}

		$this->_format_page($page_info);

		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->assign('page_str', trim($page_str, '/'));
		$this->_tpl->display('admin/user_good_collect.php');
	}
}