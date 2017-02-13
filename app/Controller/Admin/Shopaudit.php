<?php 
class Controller_Admin_Shopaudit extends Controller_Admin_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Shopaudit::getInstance();
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
		$this->_model->setOrder();
		
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
		$this->_tpl->assign('request', $_REQUEST);		
		$this->_tpl->display('admin/shop_unclaimed_list.php');
	}
	
	/**
	 * 认领审核
	 */
	public function auditAction() {
		$aid = $this->_http->get('aid');
		$page = $this->_http->get('page');
		$row = $this->_model->getRowById($aid);
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData, $row);
			if($result) {
				$content = "用户名：{$row['user_name']}　认领店铺：<b>{$row['shop_name']}</b> " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				Custom_Common::showMsg($getData['audit_type'] == 1?'店铺认领审核通过':'店铺认领审核不通过', '', array('list/page:' . $getData['page'] => '返回店铺认领'));
			}
		}
		
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('aid', $aid);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/shop_unclaimed_audit.php');
	}
}