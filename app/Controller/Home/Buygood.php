<?php
class Controller_Home_Buygood extends Controller_Home_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Ticket::getInstance();
	}
	
	public function listAction() {
		
		//团购轮播图
		$imgLargeList = Model_Home_Index::getInstance()->getRecommendListByIdentifier('buygood_banner', $this->_city, 4);
		$this->_tpl->assign('imgLargeList', $imgLargeList);
		
		$storeArray = $this->getStore(0, true, false, $this->_city);
		$storeAppArray = $this->getStore(0, true, true, $this->_city);
		
		//团购推荐
		$tuanRecommend = $this->_model->getTuanRecommend('buygood_hot', $this->_city, 5);
		$this->_tpl->assign('tuanRecommend', $tuanRecommend);
		
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('storeAppArray', array_slice($storeAppArray, 0, 5, true));
		$this->_tpl->display('ticket/tuan.php');
	}
	
	public function overDataAction() {
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$sid = !$this->_http->get('sid') ? 0 : intval($this->_http->get('sid'));
		$dtype = !$this->_http->get('dtype') ? 'time' : strval($this->_http->get('dtype'));
		$dsort = !$this->_http->get('dsort') ? 'desc' : strval($this->_http->get('dsort'));
		$tuanInfo = $this->_model->getTuanInfo($sid, $dtype, $dsort, $this->_city, $page);
		exit(json_encode($tuanInfo));
	}
}