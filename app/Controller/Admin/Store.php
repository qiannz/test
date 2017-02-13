<?php 
class Controller_Admin_Store extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Store::getInstance();
	}
	
	public function listAction() {		
		$data = $this->_model->getList();
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/store.php');
	}
	
	public function addAction() {
		$sname = $this->_http->get('sname');
		$isApp = $this->_http->get('is_app');
		$result = $this->_model->add($sname, $isApp);
		if($result) {
			$this->getStore(null, false, true);
			$this->getStore(null, false);
			Custom_Log::log($this->_userInfo['id'], "新增  <b>{$sname}</b> 成功", $this->pmodule, $this->cmodule, 'add');
			_exit('添加成功', 1);
		}
	}
	
	public function editAction() {
		$id = $this->_http->get('id');
		$sname = $this->_http->get('sname');
		$mark = $this->_http->get('mark');
		$isApp = $this->_http->get('is_app');
		$result = $this->_model->edit($id, $sname, $mark, $isApp);
		if ($result) {
			$this->getStore(null, false, true);
			$this->getStore(null, false);
			Custom_Log::log($this->_userInfo['id'], "编辑店铺分类  <b>{$sname}</b> 成功", $this->pmodule, $this->cmodule, 'edit');
			_exit('编辑成功', 1);
		}
		
	}
	
	public function delAction() {
		$id = $this->_http->get('id');
		$sname = $this->_http->get('sname');
		$result = $this->_model->del($id);
		if($result) {
			$this->getStore(null, false, true);
			$this->getStore(null, false);
			Custom_Log::log($this->_userInfo['id'], "删除  <b>{$sname}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			_exit('删除成功', 1);
		}
	}
	
	// ajax编辑排序
	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			$this->getStore(null, false, true);
			$this->getStore(null, false);
			exit(json_encode(true));
		}
		exit(json_encode(false));
	}
}