<?php
class Controller_Admin_Pack extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Pack::getInstance();	
	}
		
	public function listAction() {
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		if(array_key_exists('pack_name', $getData)){
			if($getData['pack_name']){
				$page_str .= "pack_name:{$getData['pack_name']}/";
			}
		}
		
		$this->_model->setWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$packList = $this->_model->getPackList($page);
		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('packList', $packList);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/pack_list.php');
	}
	
	public function addAction() {
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$insert_result = $this->_model->pactModi($postData);
			if ($insert_result) {
				$this->getPack(0, false); //缓存
				Custom_Log::log($this->_userInfo['id'], "新增活动  <b>{$postData['pack_name']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
					'套餐添加成功',
					'back',
					array('add' => '继续添加套餐', 'list' => '返回套餐列表')
				);
			} else {
				Custom_Common::showMsg(
					'套餐添加失败',
					'back'
				);
			}
		}
		$this->_tpl->display('admin/pack_modi.php');
	}
	
	public function editAction() {
		$pack_id= $this->_http->get('pack_id');
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$update_result = $this->_model->pactModi($postData);
			if ($update_result) {
				$this->getPack(0, false); //缓存
				Custom_Log::log($this->_userInfo['id'], "编辑套餐  <b>{$postData['pack_name']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
				Custom_Common::showMsg(
					'套餐编辑成功',
					'back',
					array('list' => '返回套餐列表','edit/pack_id:'.$postData['pack_id'] => '重新编辑该套餐')
				);
			} else {
				Custom_Common::showMsg(
					'套餐编辑失败',
					'back'
				);
			}
		}
		$packRows = $this->select("`pack_id` = '{$pack_id}'", 'oto_pack', '*', '', true);
		$this->_tpl->assign('packRows', $packRows);
		$this->_tpl->display('admin/pack_modi.php');
	}
	
	public function delAction() {
		$pack_id = $this->_http->get('pack_id');
		$pack_name = $this->_http->get('pack_name');
		if (!$pack_id) {
			Custom_Common::showMsg("请您选择要删除的套餐 ", 'back');
		}
		$resultBack = $this->_model->del($pack_id);
		if ($resultBack) {
			Custom_Log::log($this->_userInfo['id'], "删除  <b>{$pack_name}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			Custom_Common::showMsg("删除套餐成功。 ", 'back',array('list' => '返回套餐列表'));
		}
	}
	
	// ajax编辑排序
	public function ajaxColAction(){
		if($this->_model->ajax_module_edit($this->_http->getPost())) {
			echo json_encode(true);
			exit;
		}
	}
	
	public function logoCheckAction() {
		$getData = $this->_http->getPost();
		$pack_logo = empty($getData['pack_logo'])?'':trim($getData['pack_logo']);
		$pack_id = empty($getData['pack_id']) ? 0 : intval($getData['pack_id']);
		$res = $this->_model->check_logo($pack_logo, $pack_id);
		if($res == 0){
			echo json_encode(true);
			exit;
		}
		exit;
	}
	
	public function setDefaultAction() {
		$pack_id = $this->_http->get('packId');
		$rs = $this->_model->setDefault($pack_id);
		if ($rs) {
			$this->getPack(0, false); //缓存
			echo 'ok';
			exit();
		}
	}
}