<?php
class Controller_Admin_App extends Controller_Admin_Abstract {
    private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_App::getInstance();
	}
	
	public function listAction() {
	    if ($this->_http->isPost()) {
	       $postData = $this->_http->getPost();
	       $result = $this->_model->edit($postData);
	       if ($result) {
	           $this->array_to_file($this->_model->unserializeConfig(), 'config'); //缓存
	           Custom_Common::showMsg('APP设置保存成功', 'back');
	       }
	    }
	    
	    // 每日惊喜
	    $daySurpriseArray = unserialize($this->_model->getAppList());
	    $every_day_surprise = $daySurpriseArray[$this->_ad_city];

	    $this->_tpl->assign('every_day_surprise', $every_day_surprise);
	    $this->_tpl->display('admin/app_list.php');
	}
}