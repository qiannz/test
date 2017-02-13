<?php
/**
 * 商户入驻
 */
class Controller_Admin_Merchant extends Controller_Admin_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Merchant::getInstance();
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
		
		$this->_tpl->display('admin/merchant_list.php');
	}
	
	public function auditAction() {
		$uid = $this->_http->get('uid');
		$shop_id = $this->_http->get('shop_id');
		$page = $this->_http->get('page');
		$row = $this->select("`user_id` = '{$uid}' and `shop_id` = '{$shop_id}'", 'oto_merchant_app', '*', '', true);
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				$content = "用户名：{$getData['uname']}　入驻商户：<b>{$getData['shop_name']}</b> " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				Custom_Common::showMsg($getData['audit_type'] == 1?'商户入驻审核通过':'商户入驻审核不通过', '', array('list/page:' . $getData['page'] => '返回商户入驻'));
			}
		}
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('uid', $uid);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/merchant_audit.php');
	}
	
	/**
	 * 套餐详情页面
	 * 如果有支付记录  状态改为4， 用户状态改为 认证商户
	 */ 
	public function payAction() {
		$uid  = $this->_http->get('uid');
		$pack_id = $this->_http->get('pack_id');
		$shop_id = $this->_http->get('shop_id');
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->pay_audit($getData);
			if($result) {				
				$content = "用户名：{$getData['uname']}　入驻商户：<b>{$getData['shop_name']}</b> 已付款(入驻成功)";
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				echo 'ok';
				exit();
			}
		}
		
		$info = $this->select("`user_id` = '{$uid}' and `shop_id` = '{$shop_id}'", 'oto_merchant_app', '*', '', true);
		$info['pack_name'] = $this->getPack($info['pack_id'], false, 'pack_name');
		$info['store_name'] = $this->getStore($info['store_id']);
		$this->_tpl->assign('row', $info);
		$this->_tpl->display('admin/merchant_pay_audit.php');
	}
}