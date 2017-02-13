<?php
class Controller_Admin_Notice extends Controller_Admin_Abstract {
    private $_model;

	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Notice::getInstance();	
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
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/notice_list.php');
	}
	
	public function delAllAction() {
		$id = $this->_http->get('id');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
	
		$result = $this->_model->del($id);
		if($result) {
			Custom_Common::showMsg('公告删除成功', '', array('list/page:' . $page => '返回公告管理'));
		}
	}
}