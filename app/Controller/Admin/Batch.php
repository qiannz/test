<?php
class Controller_Admin_Batch extends Controller_Admin_Abstract {
    private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Batch::getInstance();
	}
	
	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		
		$page_str = '';
		$getData = $this->_http->getParams();		
		$page_str = $this->getPageStr($getData);
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getList($page, $this->_ad_city);
		$page_info['item_count'] = $this->_model->getCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/warehousing/batch_list.php');
	}
	
	public function chooseShopAction() {
		$shop_name = $this->_http->get('sname');
		$data = Model_Admin_Shop::getInstance()->searchShopListByName($shop_name, $this->_ad_city);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('shop_name', $shop_name);	
		$this->_tpl->display('admin/warehousing/search_shop.php');
	}
	
	public function addAction() {
		$shop_id = $this->_http->get('sid');
		$shop_name = $this->_http->get('sname');
		
		if(!$shop_id) header301('/admin/batch/choose-shop');
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getParams();
			//开始处理数据
			session_start();
			//确认导入批次数据
			if(isset($getData['action']) && $getData['action'] == 'on') {
				$addResult = $this->_model->addBatchResult($_SESSION['WARE_HOUSE_TAMP'], $this->_userInfo, $this->_ad_city);
				if($addResult) {
					session_destroy();
					Custom_Common::showMsg(
						'新增入库成功',
						'',
						array(
							'list' => '返回入库记录',
							'add/sid:' . $_SESSION['WARE_HOUSE_TAMP']['detail']['sid'] . '/sname:' . $_SESSION['WARE_HOUSE_TAMP']['detail']['sname'] => '继续新增入库'
						)
					);
				} else {
					Custom_Common::showMsg(
						'<span style="color:red">出错了</span>'
					);
				}
			}
			$data = $this->_model->dataProcessingSnap($getData, $_FILES, $this->_ad_city);
			$_SESSION['WARE_HOUSE_TAMP'] = $data;
			$this->_tpl->assign('data', $data);
			$this->_tpl->display('admin/warehousing/batch_snap_list.php');
			exit();
		}
		
		//获取最新批次
		$good_batch = $this->_model->getLatestBatchNumber();
		
		$this->_tpl->assign('shop_id', $shop_id);
		$this->_tpl->assign('shop_name', $shop_name);
		$this->_tpl->assign('good_batch', $good_batch);
		$this->_tpl->display('admin/warehousing/batch_add.php');
	}
}