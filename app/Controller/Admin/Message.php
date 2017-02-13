<?php
class Controller_Admin_Message extends Controller_Admin_Abstract {
    private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Message::getInstance();
	}
	
	public function listAction() {
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$page_str = '';
		$getData = $this->_http->getParams();
			
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getThreadList($page);
		$page_info['item_count'] = $this->_model->getCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);		
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/message_list.php');
	}
	
	public function showAction() {
		$tid = intval($this->_http->get('tid'));
		$page = intval($this->_http->get('page'));
		$data = $this->_model->getPostList($tid);
		
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/message_show.php');
	}
	
	public function delThreadAllAction() {
		$id = $this->_http->get('id');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		$result = $this->_model->delThread($id);
		if($result) {
			Custom_Common::showMsg('留言删除成功', '', array('list/page:' . $page => '返回留言列表'));
		}		
	}
	
	public function delPostAction() {
		$id = intval($this->_http->get('id'));
		$tid = intval($this->_http->get('tid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		$result = $this->_model->delPost($id);
		if($result) {
			Custom_Common::showMsg(
				'留言问答明细删除成功', 
				'back', 
				array(
					'show/tid:' . $tid . '/page:' . $page => '返回留言明细',
					'list/page:' . $page => '返回留言列表'
				)
			);
		}
	}
}