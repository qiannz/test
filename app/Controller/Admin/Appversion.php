<?php
class Controller_Admin_Appversion extends Controller_Admin_Abstract {
	private $_model;
	
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Appversion::getInstance();
	}
	
	
	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		$this->_model->setWhere($getData);
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
		$this->_tpl->display('admin/app_version_list.php');
	}
	
	public function addAction() {
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			$result = $this->_model->upAppVersion($postData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "添加{$postData['phone_type']}版本  <b>{$postData['version']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
				'APP版本添加成功',
				'back',
				array('add' => '继续添加APP版本', 'list' => '返回APP版本列表')
				);
			} else {
				Custom_Common::showMsg(
				'APP版本添加失败',
				'back'
				);
			}
		}
		$this->_tpl->display('admin/app_version_add.php');
	}
	
	public function editAction() {
		$id = $this->_http->get('id');
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			$result = $this->_model->upAppVersion($postData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "编辑{$postData['phone_type']}版本  <b>{$postData['version']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
				Custom_Common::showMsg(
				'APP版本编辑成功',
				'back',
				array('list' => '返回APP版本列表','edit/id:'.$postData['id'] => '重新编辑APP版本')
				);
			} else {
				Custom_Common::showMsg(
				'APP版本编辑失败',
				'back'
				);
			}
		}
		
		$data = $this->select("`id` = {$id}", "app_version", '*', '', true);
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/app_version_add.php');
	}	
	
	public function delAction(){
		$id = $this->_http->get('id');
		if(!$id){
			Custom_Common::showMsg("请您选择要删除的APP版本 ", 'back');
		}
		$row = $this->_db->fetchRow("select type, version from app_version where id = '{$id}'");
		$resultDel = $this->_model->del($id);
		if($resultDel){
			Custom_Log::log($this->_userInfo['id'], "删除 {$row['type']}APP版本 <b>{$row['version']}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			Custom_Common::showMsg("删除成功。 ", 'back',array('list' => '返回APP版本列表'));
		}
	}
	
	public function versionCheckAction() {
		$getData = $this->_http->getPost();
		$version = empty($getData['version'])?'':trim($getData['version']);
		$phone_type = empty($getData['phone_type']) ? '' : trim($getData['phone_type']);
		$id = empty($getData['id']) ? 0 : intval($getData['id']);
		$res = $this->_model->check_version($id, $version, $phone_type);
		if($res == 0){
			echo json_encode(true);
			exit;
		}
		exit;
	}
	
	public function versionCheckAndroidAction() {
		$getData = $this->_http->getPost();
		$version = empty($getData['version'])?'':trim($getData['version']);
		$phone_type = empty($getData['phone_type']) ? '' : trim($getData['phone_type']);
		$channel = empty($getData['channel']) ? '' : trim($getData['channel']);
		
		$id = empty($getData['id']) ? 0 : intval($getData['id']);
		$res = $this->_model->check_version_android($id, $version, $phone_type, $channel);
		if($res == 0){
			echo json_encode(true);
			exit;
		}
		exit;
	}
	
	
}