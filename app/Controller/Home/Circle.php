<?php
class Controller_Home_Circle extends Controller_Home_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Circle::getInstance();
	}
	
	public function showAction() {	
		if (!$this->_user_id) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);	
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		$this->_tpl->display('circle/my_circle.php');
	}
	
	public function ajaxAction() {
		$goodArray = $this->_model->getAjaxGoodList(intval($this->_http->get('page')), $this->_userInfo['user_id']);
		exit(json_encode($goodArray));
	}
}