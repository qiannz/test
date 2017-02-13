<?php
class Controller_Admin_Img extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Img::getInstance();	
	}

	public function listAction() {
		
		$data = $this->_model->getList();
		foreach ($data as &$row) {
		    $row['config_value'] = unserialize($row['config_value']);
		}
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/config_img.php');
	}
	
	public function addAction() {
		$name = $this->_http->get('name');
		$width = $this->_http->get('width');
		$height = $this->_http->get('height');
		$water = $this->_http->get('water');
		
		$result = $this->_model->add($name, $width, $height, $water);
		if($result) {
            $appInfo = array();
			$this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
			Custom_Log::log($this->_userInfo['id'], "新增商品图片  <b>{$name}</b> 成功", $this->pmodule, $this->cmodule, 'add');
			_exit('添加成功', 1);
		}
	}
	
	public function delAction() {
		$id = $this->_http->get('id');
		$pname = $this->_http->get('pname');
		$result = $this->_model->del($id);
		if($result) {
			$appInfo = array();
			$this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
			Custom_Log::log($this->_userInfo['id'], "删除商品图片 <b>{$pname}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			_exit('删除成功', 1);
		}
	}
	

}