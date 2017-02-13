<?php
class Controller_Admin_Config extends Controller_Admin_Abstract {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Config::getInstance();
	}
	
	public function listAction() {
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		if(array_key_exists('config_ex', $getData)){
			if($getData['config_ex']){
				$page_str .= "config_ex:{$getData['config_ex']}/";
			}
		}
			
		$this->_model->setWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$configs = $this->_model->getConfigList($page);
		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('configs', $configs);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/config_list.php');
	}
	
	public function showAction() {
		$configKey = $this->_http->get('config_key');
		$configValues = $this->_model->getConfigValue($configKey);
		$this->_tpl->assign('configs', $configValues);
		$this->_tpl->assign('configKey', $configKey);
		$this->_tpl->display('admin/config_show.php');
	}
	
	public function editAction() {
		$id = $this->_http->get('id');
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$result = $this->_model->addOne($postData);
			if ($result) {
				$this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
				Custom_Log::log($this->_userInfo['id'], "编辑设置  <b>{$postData['config_key']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
				Custom_Common::showMsg(
					'全局设置编辑成功',
					'back',
					array('show/config_key:'.$postData['config_key'] => '返回全局设置详情','edit/id:'.$postData['id'] => '重新编辑该设置')
				);
			} else {
				Custom_Common::showMsg(
					'全局设置编辑失败',
					'back'
				);
			}
		}
		
		$configRow = $this->_model->getConfigRow($id);
		if (substr($configRow['config_value'], 0,2) == 'a:'){
			$configRow['value_data'] = unserialize($configRow['config_value']);
		} else {
			$configRow['value_data'] = $configRow['config_value'];
		}
		$this->_tpl->assign('configRow', $configRow);
		$this->_tpl->display('admin/config_edit.php');
	}
	
	// 新增单例配置
	public function addOneAction() {
		if($this->_http->isPost()){
			$getData = $this->_http->getPost();
			$result = $this->_model->addOne($getData);
			if($result) {
				$this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
				Custom_Log::log($this->_userInfo['id'], "新增全局配置  <b>{$getData['config_key']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
					'全局配置添加成功',
					'back',
					array('add-one' => '继续添加全局配置', 'list' => '返回列表')
				);
			}
		}
		$this->_tpl->display('admin/config_addone.php');
	}
	
	// 新增多例配置
	public function addMoreAction() {
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			$result = $this->_model->addMore($postData);
			if($result) {
				$this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
				Custom_Log::log($this->_userInfo['id'], "新增全局配置  <b>{$postData['config_key']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
				'全局配置添加成功',
				'back',
				array('add-more' => '继续添加全局配置', 'list' => '返回列表')
				);
			}		
		}
		$this->_tpl->display('admin/config_addmore.php');
	}
	
	// 删除
	public function deleteAction() {
		$id = $this->_http->get('id');
		if (!$id) {
			Custom_Common::showMsg("请您选择要删除的配置信息 ", 'back');
		}
		$configRow = $this->_db->fetchRow("select config_key, config_ex from oto_config where config_id = '{$id}'");
		$info = $configRow['config_key']. '-' . $configRow['config_ex'];
		$result = $this->_model->del($id);
		if ($result) {
			$this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
			Custom_Log::log($this->_userInfo['id'], "删除  <b>{$info}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			Custom_Common::showMsg("删除全局配置成功。 ", 'back',array('list' => '返回全局配置列表'));
		}
	}
}