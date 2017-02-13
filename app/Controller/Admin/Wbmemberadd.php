<?php
/*
 * 微商会员控制器类
 */
class Controller_Admin_Wbmemberadd extends Controller_Admin_Abstract{
	private $_model;
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Wechatbusiness::getInstance();
	}
	
	//添加会员
	public function listAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$postResult = $this->_model->postUser($getData);
	
			Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'add', 'wbmember', $postResult['insert_id']);
	
			Custom_Common::showMsg(
			'会员新增成功',
			'back',
			array(
			'list' => '继续新增'
			)
			);
		}
		$this->_model->setWhere($getData);
		$this->_model->setOrder();
		$data = $this->_model->getMemberList(1);
		$this->_tpl->assign('from','add');
		$this->_tpl->assign('data',$data);
		$this->_tpl->display('admin/wbmember_add.php');
	}
}