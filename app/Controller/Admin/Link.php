<?php
class Controller_Admin_Link extends Controller_Admin_Abstract {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Link::getInstance();
	}
	
	public function listAction() {
		$getData = $this->_http->getParams();
		
		$data = $this->_model->getLinkList();
		$row = array(0 => 0);
		$map = array();
		
		foreach ($data as $key => $pcategory)
		{
			$row[$pcategory['id']] = $key + 1;
			$map[] = $row[$pcategory['pid']];
		}
		
		$this->_tpl->assign('map', json_encode($map));
		$this->_tpl->assign('max_layer', 2);

		
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', $_REQUEST);   
	    $this->_tpl->display('admin/app_link_list.php');
	    
	    
	}
	
	public function addAction() {
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			$insert_result = $this->_model->link_insert($postData);
			if($insert_result == 'repeat'){
				Custom_Common::showMsg(
				'当前分类下模块名称重复',
				'back',
				array('add/pid:' . $postData['pid'] => '继续添加模块', 'list' => '返回模块列表')
				);
			}
				
			if($insert_result){
				$this->array_to_file($this->_model->getDada(), 'appLink');
				Custom_Common::showMsg(
				'模块添加成功',
				'back',
				array('add/pid:' . $postData['pid'] => '继续添加模块', 'list' => '返回模块列表')
				);
			}else{
				Custom_Common::showMsg(
				'模块添加失败',
				'back'
				);
			}
		}
		//获取一级模块列表
		$pid = $this->_http->get('pid');
		$this->_tpl->assign('pid', $pid);
		$moduleArr = $this->_model->getModuleSelect();
		$this->_tpl->assign('moduleArr', $moduleArr);
		$this->_tpl->display('admin/app_link_add.php');
	}
	
	public function editAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			$insert_result = $this->_model->link_insert($postData);
			if($insert_result){
				$this->array_to_file($this->_model->getDada(), 'appLink');
				Custom_Common::showMsg(
				'链接设置编辑成功',
				'back',
				array('list' => '返回链接设置','edit/id:'.$this->_http->getPost('id') => '重新编辑该链接设置')
				);
			}else{
				Custom_Common::showMsg(
				'链接设置编辑失败',
				'back'
				);
			}
		}
		//获取一级模块列表
		$id = $this->_http->get('id');
		$moduleRow = $this->_model->get_info($id);
	
		$this->_tpl->assign('pid', $moduleRow['pid']);
		$this->_tpl->assign('moduleRow', $moduleRow);
		$moduleArr = $this->_model->getModuleSelect();
		$this->_tpl->assign('moduleArr', $moduleArr);
		$this->_tpl->display('admin/app_link_add.php');
	}
	
	public function delAction() {
		$id = $this->_http->get('id');
		if (!$id)
		{
			Custom_Common::showMsg("该选项不允许删除，请重新选择", 'back');
		}
		$ids = explode(',', $id);
		if(is_array($ids)) {
			foreach ($ids as $cid){
				if($cid){
					$this->_model->del($cid);
					$this->array_to_file($this->_model->getDada(), 'appLink');
				}
			}
		} else {
			$this->_model->del($id);
			$this->array_to_file($this->_model->getDada(), 'appLink');
		}
		Custom_Common::showMsg("删除链接成功。 ", 'back',array('list' => '返回链接列表'));
	}
	
	public function CheckNameAction(){
		$getData = $this->_http->getPost();
		$name = empty($getData['name'])?'':trim($getData['name']);
		$pid = empty($getData['pid']) ? 0 : intval($getData['pid']);
		$id = empty($getData['id']) ? 0 : intval($getData['id']);
		if (!$name)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->unique($name, $pid, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}
	
	
	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			$this->array_to_file($this->_model->getDada(), 'appLink');
			exit(json_encode(true));
		} else {
			exit(json_encode(false));
		}
	}
	
	
}