<?php
class Controller_Admin_Ticket extends Controller_Admin_Abstract {
	
	private $_model;
	private $_width = 640;
	private $_height = 300;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Ticket::getInstance();
	}
	
	public function couponListAction() {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('coupon');
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
		$this->_tpl->display('admin/ticket_list.php');		
	}
	
	// 新增/编辑 优惠券
	public function addCouponAction() {
		if($this->_http->isPost()) {
			$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('coupon');
			
			$getData = $this->_http->getPost();
			
			if(!empty($_FILES['file_img'])) {
				$imgInfo = getimagesize($_FILES['file_img']['tmp_name']);
				if ($imgInfo['0'] != 640 || $imgInfo['1'] != 300) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 640 × 300 的图片'."\r\n";
				} else {
					$cover_img = Custom_Upload::singleImgUpload($_FILES['file_img'], 'cover');
					$getData['cover_img'] = $cover_img;
				}
			}
			
			if($getData['tid']) {
				$editResult = $this->_model->updateTicket($getData,$ticketType);
				if($editResult) {
					Custom_Log::log($this->_userInfo['id'], "编辑了优惠券： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
					$this->_file->del('get_ticket_row_' . $getData['tid']);
					Custom_Common::showMsg(
						'优惠券编辑成功',
						'',
						array(
							'coupon-list' => '返回优惠卷列表',
							'add-coupon/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] => '我要重新编辑'
						)
					);
				} else {
					Custom_Common::showMsg(
						'优惠券编辑失败'
					);
				}
			} else {
				$insert_ticket_id = $this->_model->addTicket($getData,$ticketType);
				if($insert_ticket_id) {
					Custom_Log::log($this->_userInfo['id'], "新增了优惠券： <b>{$getData['ticket_title']}</b> ID：{$insert_ticket_id}", $this->pmodule, $this->cmodule, 'add', 'ticket', $insert_ticket_id);
					Custom_Common::showMsg(
						'恭喜，你的新券添加成功！',
						'',
						array(
							'add-coupon/sid:'. $getData['sid'] . '/uname:' . $getData['user_name']   => '继续新增券',
							'coupon-list'  => '返回券管理'
						)
					);
				} else {
					Custom_Common::showMsg(
						'系统忙，请稍后再试'
					);
				}
			}
		}
		$tid = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		if($tid) {
			$row = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);			
			$gidArray = Model_Home_Suser::getTicketGood($tid);
			if(is_array($gidArray)) {
				$gids = implode(',', $gidArray);
				$this->_tpl->assign('gids', $gids);
			}
			$this->_tpl->assign('tid', $tid);
			//券关联店铺
			$ticketRelationShopArray = $this->_model->getRelationShopByTicketId($tid);
			$this->_tpl->assign('ticketRelationShopArray', $ticketRelationShopArray);
		}
		$sid = $this->_http->get('sid');
		$uname = $this->_http->get('uname');
		$shop_info = $this->getShopFieldById($sid);
		$region = $this->getRegion(0, true, $this->_ad_city);
		$row['region_name'] = $region[$shop_info['region_id']];
		$row['circle_name'] = $shop_info['circle_id'] ? $this->getCircleByCircleId($shop_info['circle_id'], true, $this->_ad_city) : '';
		$row['shop_name'] = $shop_info['shop_name'];
		

		
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('uname', $uname);
		$this->_tpl->assign('regionArray', $region);
		$this->_tpl->display('admin/add_coupon.php');
	}
	
	// 新增/编辑 现金券
	public function addVoucherAction() {
		if ($this->_http->isPost()) {
			$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('voucher');
			$getData = $this->_http->getPost();
			$user_id = $this->getUserIdByUserName($getData['user_name']);
			if($_FILES['file_img']['error'] == 0) {
				$imgInfo = getimagesize($_FILES['file_img']['tmp_name']);
				if ($imgInfo['0'] != $this->_width || $imgInfo['1'] != $this->_height) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 ' . $this->_width . ' × ' . $this->_height . ' 的图片'."\r\n";
                    Custom_Common::showMsg(
	                    '<span style="color:red">'.$errMsg.'</span>',
	                    'back',
	                    array(
	                    	'voucher-list/page:' . $getData['page'] => '返回现金券列表'
	                    )
                    );  
				} else {
					$cover_img = Custom_Upload::singleImgUpload($_FILES['file_img'], 'cover');
					$getData['cover_img'] = $cover_img;
				}
 			}
			
			$wap_img_url = array();
			if($_FILES['img_url']['error'][0] == 0) {
				foreach($_FILES['img_url']['tmp_name'] as $_key => $_value) {
					$img_url = Custom_Upload::singleImgUpload(
							array(
									'name' => $_FILES['img_url']['name'][$_key],
									'type' => $_FILES['img_url']['type'][$_key],
									'tmp_name' => $_value,
									'error' => $_FILES['img_url']['error'][$_key],
									'size' => $_FILES['img_url']['size'][$_key]
							), 'ticketwap');
						
					if(!$img_url) {
						Custom_Common::showMsg('<span style="color:red">请检查图片格式，大小是否正确</span>', 'back');
					}
					$wap_img_url[] = $img_url;
				}
			}
						
			if($getData['tid']) {
				$editResult = $this->_model->updateTicket($getData, $ticketType);
				if($editResult) {
					Custom_Log::log($this->_userInfo['id'], "编辑了现金券： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
					$this->_file->del('get_ticket_row_' . $getData['tid']);
					
					//WAP图片上传
					$this->_model->wapUploadImg($wap_img_url, $getData['tid'], $getData['sid'], $user_id);
					Custom_Common::showMsg(
						'现金券编辑成功',
						'',
						array(
							'voucher-list/page:' . $getData['page'] => '返回现金券列表',
							'add-voucher/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '我要重新编辑'
						)
					);
				} else {
					Custom_Common::showMsg(
						'现金券编辑失败'
					);
				}
			} else {
				$addResult = $this->_model->addTicket($getData,$ticketType);			
				if($addResult) {
					Custom_Log::log($this->_userInfo['id'], "新增了现金券： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'add', 'ticket', $getData['tid']);
					//WAP图片上传
					$this->_model->wapUploadImg($wap_img_url, $addResult, $getData['sid'], $user_id);
					Custom_Common::showMsg(
						'恭喜，你的新券添加成功！',
						'',
						array(
							'add-voucher/sid:'. $getData['sid'] . '/uname:' . $getData['user_name']  => '继续新增券',
							'voucher-list'  => '返回券管理'
						)
					);
				} else {
					Custom_Common::showMsg(
						'系统忙，请稍后再试'
					);
				}
			}
		}
		
		$tid = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		if($tid) {
			$row = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
			$gidArray = Model_Home_Suser::getTicketGood($tid);
			if(is_array($gidArray)) {
				$gids = implode(',', $gidArray);
				$this->_tpl->assign('gids', $gids);
			}
			$this->_tpl->assign('tid', $tid);
			//券关联店铺
			$ticketRelationShopArray = $this->_model->getRelationShopByTicketId($tid);
			$this->_tpl->assign('ticketRelationShopArray', $ticketRelationShopArray);
			
			//wap
			$wapImgData = $this->_model->getWapImg($tid);
			$this->_tpl->assign('wapImgData', $wapImgData);
		}
		$sid = $this->_http->get('sid');
		$user_name = $this->_http->get('uname');
		$user_id = $this->getUserIdByUserName($user_name);
				
		$shop_info = $this->getShopFieldById($sid);
		$region = $this->getRegion(0, true, $this->_ad_city);
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		$row['region_name'] = $region[$shop_info['region_id']];
		$row['circle_name'] = $shop_info['circle_id'] ? $this->getCircleByCircleId($shop_info['circle_id'], true, $this->_ad_city) : '';
		$row['shop_name'] = $shop_info['shop_name'];
		
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('uname', $user_name);
		$this->_tpl->assign('regionArray', $region);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('activity', Model_Home_Suser::getActivityList($user_id));
		$this->_tpl->display('admin/add_voucher.php');
	}

	// 新增/编辑 自助券
	public function addSelfpayAction() {
		if ($this->_http->isPost()) {
			$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('selfpay');
				
			$getData = $this->_http->getPost();
			if($_FILES['file_img']['error'] == 0) {
				$imgInfo = getimagesize($_FILES['file_img']['tmp_name']);
				if ($imgInfo['0'] != 640 || $imgInfo['1'] != 300) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 640 × 300 的图片'."\r\n";
	
					Custom_Common::showMsg(
					'<span style="color:red">'.$errMsg.'</span>',
					'back',
					array(
					'voucher-list/page:' . $getData['page'] => '返回现金券列表'
					)
					);
				} else {
					$cover_img = Custom_Upload::singleImgUpload($_FILES['file_img'], 'cover');
					$getData['cover_img'] = $cover_img;
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
				
			if($getData['tid']) {
				$editResult = $this->_model->updateTicket($getData, $ticketType, true);
				if($editResult) {
					Custom_Log::log($this->_userInfo['id'], "编辑了自助买单券： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
					$this->_file->del('get_ticket_row_' . $getData['tid']);
					//WAP图片上传
					$this->_model->wapUploadImg($wap_img_url, $getData['tid']);
					Custom_Common::showMsg(
					'自助买单券编辑成功',
					'',
					array(
					'selfpay-list/page:' . $getData['page'] => '返回自助买单列表',
					'add-selfpay/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '我要重新编辑'
					)
					);
				} else {
					Custom_Common::showMsg(
					'自助买单券编辑失败'
					);
				}
			} else {
				$addResult = $this->_model->addTicket($getData,$ticketType, true);
				if($addResult) {
					Custom_Log::log($this->_userInfo['id'], "新增了自助买单券： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'add', 'ticket', $getData['tid']);
					//WAP图片上传
					$this->_model->wapUploadImg($wap_img_url, $addResult);
					Custom_Common::showMsg(
					'恭喜，你的自助买单券添加成功！',
					'',
					array(
					'add-selfpay/sid:'. $getData['sid'] . '/uname:' . $getData['user_name']  => '继续新增自助买单券',
					'selfpay-list'  => '返回自助买单列表'
					)
					);
				} else {
					Custom_Common::showMsg(
					'系统忙，请稍后再试'
					);
				}
			}
		}
	
		$tid = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		if($tid) {
			$row = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
			$gidArray = Model_Home_Suser::getTicketGood($tid);
			if(is_array($gidArray)) {
				$gids = implode(',', $gidArray);
				$this->_tpl->assign('gids', $gids);
			}
			$this->_tpl->assign('tid', $tid);
			//券关联店铺
			$ticketRelationShopArray = $this->_model->getRelationShopByTicketId($tid);
			$this->_tpl->assign('ticketRelationShopArray', $ticketRelationShopArray);
				
			//wap
			$wapImgData = $this->_model->getWapImg($tid);
			$this->_tpl->assign('wapImgData', $wapImgData);
		}
		$sid = $this->_http->get('sid');
		$user_name = $this->_http->get('uname');
		$user_id = $this->getUserIdByUserName($user_name);
	
		$shop_info = $this->getShopFieldById($sid);
		$region = $this->getRegion(0, true, $this->_ad_city);
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		$row['region_name'] = $region[$shop_info['region_id']];
		$row['circle_name'] = $shop_info['circle_id'] ? $this->getCircleByCircleId($shop_info['circle_id'], true, $this->_ad_city) : '';
		$row['shop_name'] = $shop_info['shop_name'];
	
	
	
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('uname', $user_name);
		$this->_tpl->assign('regionArray', $region);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('activity', Model_Home_Suser::getActivityList($user_id));
		$this->_tpl->display('admin/add_selfpay.php');
	}
	
	public function auditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$mark = $this->getTicketSortById($getData['type'], 'ticketsort', 'sort_detail_mark');
			$getData['mark'] = $mark;
			$result = $this->_model->audit($getData);
			if($result) {
				if($mark == 'coupon') {
					$markName = '优惠卷';
					$backUrl = 'coupon-list/page:' . $getData['page'];
				} elseif($mark == 'voucher') {
					if( $getData['audit_type'] == 1 ){
						Model_Api_Message::getInstance()->addPreNotice( "voucher" , "voucher_view" , $getData['tid'] );
					}
					$markName = '现金卷';
					$backUrl = 'voucher-list/page:' . $getData['page'];
				} elseif($mark == 'selfpay') {
					$markName = '自助买单卷';
					$backUrl = 'selfpay-list/page:' . $getData['page'];
				}
				
				$content = "{$markName}：{$getData['title']}　券ID：{$getData['tid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit', 'ticket', $getData['tid'] );
				Custom_Common::showMsg($getData['audit_type'] == 1?'券审核通过':'券审核不通过', '', array($backUrl => '返回券列表'));
			} else {
				Custom_Common::showMsg('审核失败，请稍后再试');
			}
		}
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$tid = $this->_http->get('tid');
		$row = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
		$type = $this->_http->get('type');
		$mark = $this->getTicketSortById($type, 'ticketsort', 'sort_detail_mark');
		
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('type', $type);
		$this->_tpl->assign('mark', $mark);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/ticket_audit.php');
	}
	
	public function exportAction() {		
		set_time_limit(0);
		$tid = $this->_http->get('tid');
		$title = $this->_http->get('title');
		$sname = $this->_http->get('sname');
		Custom_Log::log($this->_userInfo['id'], "券：<b>{$title}</b> 使用明细导出成功", $this->pmodule, $this->cmodule, 'export', 'ticket', $tid);
		$detailsArray = array();
		$details = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_detail', '*', '`detail_id` asc');
		foreach($details as $key => $item) {
			$detailsArray[$key]['user_name'] = $item['user_name'];
			$detailsArray[$key]['phone_number'] = $item['phone_number'];
			$detailsArray[$key]['created'] = date('Y-m-d H:i:s',$item['created']);
			$detailsArray[$key]['use_time'] = $item['is_use'] == 1 ? date('Y-m-d H:i:s',$item['use_time']) : '';
			$detailsArray[$key]['is_use'] = $item['is_use'] == 0 ? '未使用':'已使用';
			$detailsArray[$key]['ticket_title'] = $title;
			$detailsArray[$key]['shop_name'] = $sname;
		}
		
		$detailsArray = array_merge(array(array('用户名', '手机号码','领取时间','使用时间','是否使用', '券名称', '所属店铺')), $detailsArray);
		$excelObject = Third_Excel::getInstance('UTF-8', false, '使用明细');
		$excelObject->addArray($detailsArray);
		$excelObject->generateXML($title);
	}
	
	/**
	 * 推荐券
	 */
	public function recommendAction() {
		$id = $this->_http->get('id');
		$type = $this->_http->get('type');
		$mark = $this->getTicketSortById($type, 'ticketsort', 'sort_detail_mark');
		$ticketRow = $this->select("`ticket_id` = '{$id}'", 'oto_ticket', '*', '', true);
		
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = $this->_model->checkRecommend($getData['id'], $getData['pos_id']);
			if($checkRepeat){
				Custom_Common::showMsg(
					'<span style="color:red">当前券在此推荐位重复，请重新选择推荐位</span>',
					'recommend/id:' . $getData['id']. '/type:' . $getData['type'] . '/page:' . $getData['page']
				);
			}
			$positionRow = $this->select('pos_id = ' .  $getData['pos_id'], 'oto_position', '*', '', true);
			
			if($_FILES['uploadFile']['error'] == 0) {
				$imageInfo = getimagesize($_FILES['uploadFile']['tmp_name']);
				if ($positionRow['width'] && $positionRow['width'] != $imageInfo[0] || ($positionRow['height'] && $positionRow['height'] != $imageInfo[1]) ) {
					Custom_Common::showMsg(
						'<span style="color:red">图片尺寸不符合，请重新选择</span>',
						'back',
						array(
							'recommend/id:' . $getData['id']. '/type:' . $getData['type'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
				$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
			} else {
				if($positionRow['width'] && !in_array($positionRow['identifier'], array('app_home_six_love'))) {
					Custom_Common::showMsg(
						'<span style="color:red">请上传图片</span>',
						'back',
						array(
							'recommend/id:' . $getData['id'] . '/type:' . $getData['type'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			}
						
			$getData['img_url'] = $img_url ? $img_url : '';
			
			// 券信息
			if($mark == 'coupon') {
				$backUrl = 'coupon-list/page:' . $getData['page'];
			} elseif($mark == 'voucher') {
				$backUrl = 'voucher-list/page:' . $getData['page'];
			}
			
			$getData['title']  = $ticketRow['ticket_title'];
			$getData['summary'] = $ticketRow['ticket_summary'];
			$result = $this->_model->recommend($getData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了券名为  <b>{$ticketRow['ticket_title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend', 'ticket', $id);
				Custom_Common::showMsg(
					'推荐成功',
					'',
					array(
						$backUrl => '返回券列表'
					)
				);
			}
		}
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('index','recommend_goods','app_home_version_four', 'app_brand_version_four', 'commodity','app_home_version_six')", "`identifier` not in ('index_latest_event','index_img_large','index_img_small','index_top_shop','app_home_large_icons','app_home_small_icons','app_home_limited_spike','app_home_daily_deals','app_home_buying_vouchers','app_home_big_drive_to', 'app_brand_small', 'app_brand_big', 'app_brand_banner', 'app_band_recommend')");
		
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('id', $id);
		$this->_tpl->assign('ticketRow', $ticketRow);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('mark', $mark);
		$this->_tpl->assign('type', $type);
		$this->_tpl->display('admin/recommend_ticket_position.php');
	}
	
	public function getGoodAction() {
		$sid = $this->_http->get('sid');
		$sid = intval($sid);
		$goodsArray = $this->select("`shop_id` = '{$sid}' and `good_status` <> '-1' and `is_auth` <> '-1' and `is_del` = '0' AND `city` = '{$this->_ad_city}'", 'oto_good', 'good_id,good_name,dis_price', 'created desc');
		echo json_encode($goodsArray);
	}
	
	
	// 现金券列表
	public function voucherListAction() {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('voucher');
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
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getList($page, $ticketType);
		foreach ($data as &$row) {
			$row['activity_name'] = $this->_db->fetchOne("select activity_name from oto_activity where activity_id = '{$row['activity_id']}'");
		}
		$page_info['item_count'] = $this->_model->getCount($ticketType);
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->display('admin/voucher_list.php');
	}
	
	// 自助买单列表
	public function selfpayListAction() {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('selfpay');
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
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getList($page, $ticketType);
		foreach ($data as &$row) {
			$row['activity_name'] = $this->_db->fetchOne("select activity_name from oto_activity where activity_id = '{$row['activity_id']}'");
		}
		$page_info['item_count'] = $this->_model->getCount($ticketType);
	
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
	
		$this->_format_page($page_info);
	
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->display('admin/selfpay_list.php');
	}
	
	public function userShopAction() {
		$type = $this->_http->get('type');
		$user_name = $this->_http->get('uname');
		$shop_name = $this->_http->get('sname');
		if ($user_name) {
			$data = $this->_model->getShopListByBusUser($user_name, $shop_name);
		}
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('type', $type);
		$this->_tpl->assign('user_name', $user_name);
		$this->_tpl->assign('shop_name', $shop_name);
		$this->_tpl->display('admin/shop_choose.php');
	}
	
	public function getActAction() {
		$user_name = $this->_http->get('user_name');
		$user_id = $this->getUserIdByUserName($user_name);
		$activity = $this->select("user_id = '{$user_id}'", 'oto_activity', '*', 'created desc');
		echo json_encode($activity);
	}
	
	
	public function addActivityAction() {
		$activity_name = Custom_String::HtmlReplace(urldecode($this->_http->get('aname')), 2);
		$user_name = urldecode($this->_http->get('user_name'));
		$result = $this->_model->addActivity($activity_name, $user_name);
		if ($result) {
			_exit('活动添加成功', 1);
		}
	}
	
	public function checkUserAction() {
		$user_name = $this->_http->getPost('user_name');
		$res = $this->_model->check_user($user_name);
		if(!$res){
			exit('ok');
		}
	}

	public function checkUserShopAction() {
		$user_name = $this->_http->getPost('user_name');
		$res = $this->_model->check_user_shop($user_name);
		if(!$res){
			exit('ok');
		}
	}

	public function uploadAction() {
		if($this->_http->isPost()){
			$user_name = $this->_http->has('user_name') ? $this->_http->get('user_name') : DEFINED_USER_NAME;
			$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'ticket';
			$ticket_id = $this->_http->has('ticket_id') ? intval($this->_http->get('ticket_id')) : 0;
			$user_id = $this->getUserIdByUserName($user_name);
			$shop_id = intval($this->_http->get('shop_id'));
			
			$filePath = Custom_Upload::singleImgUpload($_FILES['uploadFile'], $folder);
	
			if($filePath){
				$param = array(
						'ticket_id'  => $ticket_id,
						'user_id'  	 => $user_id,
						'shop_id' 	 => $shop_id,
						'img_url'  	 => $filePath,
						'created' 	 => REQUEST_TIME
				);
				$aid = $this->_db->insert('oto_ticket_img', $param);
				$picArr = array(
						'error' => 0,
						'data' => array(
									array(
										'aid' => $aid,
										'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL']."/api/good/get-special-img-thumb/iid/{$aid}/type/{$folder}",
										'gid' => $ticket_id
										)
									)
								);
				exit(json_encode($picArr));
			}
			exit(json_encode(array('error' => 1)));
		}
	}
		
	public function delImgAction(){
		$aid = intval($this->_http->get("aid"));
		$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'ticket';
		if( $aid ){
			$this->_db->update(
					'oto_ticket_img',
					array('ticket_id' => '0'),
					array('id' => $aid)
			);
			
			exit(json_encode(array('status' => 'ok')));
		}
	}
	
	public function wapImgDelAction() {
		$id = intval($this->_http->get('id'));
		if( $id ) {
			$this->_db->update(
					'oto_ticket_wap_img',
					array('ticket_id' => '0'),
					array('id' => $id)
			);
			exit(json_encode(array('status' => 'ok')));
		}
	}
	
	public function ajaxColAction() {
		$getData = $this->_http->getPost();
		$result = $this->_model->ajax_module_edit($getData);
		if($result){
			exit(json_encode(true));
		}
	}

	public function imgAjaxColAction() {
		$getData = $this->_http->getPost();
		$result = $this->_model->img_ajax_edit($getData);
		if($result){
			exit(json_encode(true));
		}
	}
	
	public function shopDelAction(){
		$ticket_id = intval($this->_http->get("tid"));
		$shop_id = intval($this->_http->get("sid"));
		$this->_db->delete('oto_ticket_shop', array('ticket_id' => $ticket_id, "shop_id"=>$shop_id));
		exit(json_encode(array('status' => 'ok')));
	}
}