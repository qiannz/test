<?php
class Controller_Admin_Appset extends Controller_Admin_Abstract {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Appset::getInstance();
	}
	
	public function listAction() {
		$listArray = $this->_model->getList();
		$this->_tpl->assign('listArray', $listArray);
		$this->_tpl->display('admin/app_wheel_set_list.php');
	}
	
	public function addAction() {
		$getData = $this->_http->getParams();

	    $result = $this->_model->addEdit($getData);
	    	    
        if($result) {
            $appInfo = array();
            _exit($getData['id'] ? '编辑成功' : '添加成功', 100);
        }
	}
	
	public function delAction() {
		$id = $this->_http->get('id');
		$result = $this->_model->del($id);
		if($result) {
			_exit('删除成功', 1);
		}
	}
}