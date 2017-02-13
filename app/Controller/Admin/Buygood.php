<?php
class Controller_Admin_Buygood extends Controller_Admin_Abstract {
    
    private $_model;
	
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Buygood::getInstance();
	}
	
	public function listAction() {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('buygood');
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
			
		$data = $this->_model->getList($page, $ticketType);
		$page_info['item_count'] = $this->_model->getCount($ticketType);
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
				
		$this->_tpl->display('admin/buygood_list.php');
	}
	
	public function addEditAction() {
		$recommendArray = $this->getTheRecommendedPosition('buygood', null, true, $this->_ad_city);
		if($this->_http->isPost()) {
			$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('coupon');
				
			$getData = $this->_http->getPost();
			if(!empty($_FILES['file_img_large'])) {
				$imgInfo = getimagesize($_FILES['file_img_large']['tmp_name']);
				if ($imgInfo['0'] != $recommendArray['buygood_img_large']['width'] || $imgInfo['1'] != $recommendArray['buygood_img_large']['height']) {
					$errMsg .= "封面图片尺寸错误，请上传宽高为 {$recommendArray['buygood_img_large']['width']} × {$recommendArray['buygood_img_large']['height']} 的图片"."\r\n";
				} else {
					$file_img_large = Custom_Upload::singleImgUpload($_FILES['file_img_large'], 'ticket');
					$getData['file_img_large'] = $file_img_large;
				}
			}
			
			if(!empty($_FILES['file_img_small'])) {
				$imgInfo = getimagesize($_FILES['file_img_small']['tmp_name']);
				if ($imgInfo['0'] != $recommendArray['buygood_img_small']['width'] || $imgInfo['1'] != $recommendArray['buygood_img_small']['height']) {
					$errMsg .= "封面图片尺寸错误，请上传宽高为 {$recommendArray['buygood_img_small']['width']} × {$recommendArray['buygood_img_small']['height']} 的图片"."\r\n";
				} else {
					$file_img_small = Custom_Upload::singleImgUpload($_FILES['file_img_small'], 'ticket');
					$getData['file_img_small'] = $file_img_small;
				}
			}
			
			$wap_img_url = array();
			if(!empty($_FILES['img_url'])) {
				foreach($_FILES['img_url']['tmp_name'] as $_key => $_value) {
					$wap_img_url[] = Custom_Upload::singleImgUpload(
							array(
									'name' => $_FILES['img_url']['name'][$_key],
									'type' => $_FILES['img_url']['type'][$_key],
									'tmp_name' => $_value,
									'error' => $_FILES['img_url']['error'][$_key],
									'size' => $_FILES['img_url']['size'][$_key]
							), 'ticketwap');
						
				}
			}
			
			$user_id = $this->getUserIdByUserName($getData['user_name']);
			$userInfo = array('user_id' => $user_id, 'user_name' => $getData['user_name']);
			if($getData['tid']) {
				$resultArr = Model_Admin_Ticket::getInstance()->addTuanTicket($getData, $userInfo, $this->_ad_city);
				if($resultArr['status'] == 100) {
					Custom_Log::log($this->_userInfo['id'], "编辑了团购商品： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
					//WAP图片上传
					Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $getData['tid'], $getData['sid'], $user_id);
					Custom_Common::showMsg(
						'团购商品编辑成功',
						'',
						array(
							'list/page:' . $getData['page'] => '返回团购商品',
							'add-edit/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] => '我要重新编辑'
						)
					);
				} else {
					Custom_Common::showMsg('团购商品编辑失败');
				}
			} else {
				$resultArr = Model_Admin_Ticket::getInstance()->addTuanTicket($getData, $userInfo, $this->_ad_city);
				if($resultArr['status'] == 100) {
					Custom_Log::log($this->_userInfo['id'], "新增了团购商品： <b>{$getData['ticket_title']}</b> ID：{$resultArr['insert_ticket_id']}", $this->pmodule, $this->cmodule, 'add', 'ticket', $resultArr['insert_ticket_id']);
					//WAP图片上传
					Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $resultArr['insert_ticket_id'], $getData['sid'], $user_id);
					Custom_Common::showMsg(
						'恭喜，你的团购商品添加成功！',
						'',
						array(
							'add-edit/sid:'. $getData['sid'] . '/uname:' . $getData['user_name']   => '继续新增团购商品',
							'list' => '返回券管理'
						)
					);
				} else {
					Custom_Common::showMsg('团购商品新增失败');
				}
			}
		}
		$tid = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		if($tid) {
			$row = $this->_model->getTuanRow($tid);
			//券关联店铺
			$ticketRelationShopArray = Model_Admin_Ticket::getInstance()->getRelationShopByTicketId($tid);
			$this->_tpl->assign('ticketRelationShopArray', $ticketRelationShopArray);
			//sku商品分类
			$skuListArray = Model_Home_Suser::getInstance()->getSkuPropList($row['category_id']);
			
			$ticketInfo = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_info', '*', '', true);
			$skuInfo = unserialize($ticketInfo['sku_info']);
			$propKeyValueArray = $skuInfo['PropKeyValue'];
			
			foreach ($skuListArray as $skuListKey => $skuListItem) {
				foreach ($skuListItem['Values'] as $skuValuesKey => $skuValuesItem) {
					if(array_key_exists($skuValuesItem['Pvs'], $propKeyValueArray)) {
						$skuListArray[$skuListKey]['Values'][$skuValuesKey]['ValueName'] =  $propKeyValueArray[$skuValuesItem['Pvs']];
					}
				}
			}
			
			$this->_tpl->assign('skuListArray', $skuListArray);
			
			//wap
			$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($tid);
			$this->_tpl->assign('wapImgData', $wapImgData);
		}
		$sid = $this->_http->get('sid');
		$user_name = $this->_http->get('uname');
		$user_id = $this->getUserIdByUserName($user_name);
		
		$shop_info = $this->getShopFieldById($sid);
		$region = $this->getRegion(0, true, $this->_ad_city);
		$row['region_name'] = $region[$shop_info['region_id']];
		$row['circle_name'] = $shop_info['circle_id'] ? $this->getCircleByCircleId($shop_info['circle_id'], true, $this->_ad_city) : '';
		$row['shop_name'] = $shop_info['shop_name'];
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		$skuArray = Model_Home_Suser::getInstance()->getSkuCategoryList();

		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('uname', $user_name);
		$this->_tpl->assign('regionArray', $region);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('skuArray', $skuArray);
		$this->_tpl->assign('recommendArray', $recommendArray);
		$this->_tpl->assign('activity', Model_Home_Suser::getActivityList($user_id));
		$this->_tpl->display('admin/add_tuan.php');
	}

	public function auditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				$backUrl = 'list/page:' . $getData['page'];
				$content = "团购商品：{$getData['title']}　团购商品ID：{$getData['tid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				Custom_Common::showMsg($getData['audit_type'] == 1?'审核通过':'审核不通过', '', array($backUrl => '返回商品列表'));
			} else {
				Custom_Common::showMsg('审核失败，请稍后再试');
			}
		}
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$tid = $this->_http->get('tid');
		$row = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
	
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/ticket_audit_tuan.php');
	}
	
	public function userShopAction() {
		$user_name = $this->_http->get('uname');
		if ($user_name) {
			$data = Model_Admin_Ticket::getInstance()->getShopListByBusUser($user_name);
		}
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('user_name', $user_name);
		$this->_tpl->display('admin/shop_choose_tuan.php');
	}
	
	public function checkUserShopAction() {
		$user_name = $this->_http->getPost('user_name');
		$res = Model_Admin_Ticket::getInstance()->check_user_shop($user_name);
		if(!$res){
			exit('ok');
		}
	}
	
	public function recommendAction() {
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = Model_Admin_Ticket::getInstance()->checkRecommend($getData['id'], $getData['pos_id']);
			if($checkRepeat){
				Custom_Common::showMsg(
					'当前团购商品在此推荐位重复，请重新选择推荐位',
					'/admin/buygood/recommend/id:' . $getData['id'] . '/title:' . $getData['title'] . '/page:' . $getData['page']
				);
			}
			
			$img_url = '';
			if($getData['width'] || $getData['height']) {
				$img_url = Custom_Upload::recommendImgUpload($_FILES, $getData);
				if ($img_url == 'img_error') {
					Custom_Common::showMsg(
					'图片尺寸不符合，请重新选择',
					'back',
					array(
					'recommend/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
					)
					);
				}
			}
			$getData['img_url'] = $img_url;		
				
			$ticketRow = $this->select("`ticket_id` = '{$getData['id']}'", 'oto_ticket', '*', '', true);
			$getData['title']  = $ticketRow['ticket_title'];
			$getData['summary'] = $ticketRow['ticket_summary'];
			$result = $this->_model->recommend($getData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了团购商品：  <b>{$ticketRow['ticket_title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend');
				Custom_Common::showMsg(
					'推荐成功',
					'',
					array('list/page:' . $getData['page'] => '返回团购商品')
				);
			}
		}
	
		$id = $this->_http->get('id');
		$title = $this->_http->get('title');
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('buygood', 'app_home_version_four')", "`identifier` not in ('index_latest_event','index_img_large','index_img_small','index_top_shop','app_home_large_icons','app_home_small_icons','app_home_limited_spike','app_home_daily_deals','app_home_buying_vouchers','app_home_big_drive_to')");
		
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('id', $id);
		$this->_tpl->assign('title', $title);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/recommend_tuan.php');
	}

	public function getSkuListAction() {
		$cid = intval($this->_http->get('cid'));
		$skuListArray = Model_Home_Suser::getInstance()->getSkuPropList($cid);
		
		exit(json_encode($skuListArray));
	}
	
	public function wapImgDelAction() {
		$id = $this->_http->get('id');
		$wapImgRow = $this->select("`id` = '{$id}'", 'oto_ticket_wap_img', '*', '', true);
		unlink(ROOT_PATH . 'web/data/ticketwap/' . $wapImgRow['img_url']);
		unlink(ROOT_PATH . 'web/data/buy/ticketwap/' . $wapImgRow['img_url']);
		$this->_db->delete('oto_ticket_wap_img', '`id` = ' . $id);
		exit(json_encode(array('status' => 'ok')));
	}
	
	public function imgAjaxColAction() {
		$getData = $this->_http->getPost();
	
		$this->_model->img_ajax_edit($getData);
	}
}