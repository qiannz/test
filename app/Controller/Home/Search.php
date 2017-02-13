<?php
class Controller_Home_Search extends Controller_Home_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Search::getInstance();
	}

	public function listAction() {
		$key = Custom_String::FilterSearch(trim($this->_http->get('keyword')));
		if ($key == "搜索关键字搜索商品") {
			$key = '';
		}
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		$this->_tpl->assign('key', $key);
		$this->_tpl->display('good/search_list.php');
	}
	
	public function ajaxAction() {
		$goodArray = $this->_model->getAjaxGoodList(trim($this->_http->get('keys')), intval($this->_http->get('page')));
		exit(json_encode($goodArray));
	}
}
