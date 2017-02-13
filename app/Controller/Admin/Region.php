<?php 
class Controller_Admin_Region extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Region::getInstance();	
	}
	
	public function listAction() {		
		$data = $this->_model->getList();
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/region.php');
	}
	
	public function addAction() {
		$rname = $this->_http->get('rname');
		$result = $this->_model->add($rname);
		if($result) {
			$this->getRegion(0, false);
			Custom_Log::log($this->_userInfo['id'], "新增  <b>{$rname}</b> 成功", $this->pmodule, $this->cmodule, 'add');
			_exit('添加成功', 1);
		}
	}
	
	public function editAction() {
		$id = $this->_http->get('id');
		$rname = $this->_http->get('rname');
		$result = $this->_model->edit($id, $rname);
		if ($result) {
			$this->getRegion(0, false);
			Custom_Log::log($this->_userInfo['id'], "编辑行政区  <b>{$rname}</b> 成功", $this->pmodule, $this->cmodule, 'edit');
			_exit('编辑成功', 1);
		}
	
	}
	
	public function delAction() {
		$id = $this->_http->get('id');
		$rname = $this->_http->get('rname');
		$result = $this->_model->del($id);
		if($result) {
			Custom_Log::log($this->_userInfo['id'], "删除  <b>{$rname}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			_exit('删除成功', 1);
		}
	}
	
	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			$rname = $this->_http->get('rname');
			exit(json_encode(true));
		}
		exit(json_encode(false));
	}	
}