<?php
class Controller_Admin_Commodity extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Commodity::getInstance();
	}
	
	public function listAction() {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('commodity');
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
		$this->_tpl->display('admin/commodity_list.php');
	}
	
	public function addEditAction() {
		$tid = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		if($tid) {
			$row = $this->_model->getCommodityRow($tid);
		}
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
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
							), 'commodity');
					
					if(!$img_url) {
						Custom_Common::showMsg(
							'<span style="color:red">请检查图片格式，大小是否正确</span>',
							'back'
						);
					}
					$wap_img_url[] = $img_url;
				}
			}
				
			$user_id = $this->getUserIdByUserName($getData['user_name']);
			$userInfo = array('user_id' => $user_id, 'user_name' => $getData['user_name']);
			if($getData['tid']) {
				$resultArr = Model_Admin_Ticket::getInstance()->addCommodityTicket($getData, $userInfo, $this->_ad_city);
				if($resultArr['status'] == 100) {
					Custom_Log::log($this->_userInfo['id'], "编辑了商城商品： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
					//WAP图片上传
					Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $getData['tid'], $getData['sid'], $user_id);
					//统计店铺在线商品数量
					$this->updateQuantityCommodityNumByShopId($row['shop_id']);
					//统计品牌在线商品数量
					$this->updateQuantityCommodityNumByBrandId($row['brand_id']);
					
					Custom_Common::showMsg(
						'商城商品编辑成功',
						'',
						array(
							'list/page:' . $getData['page'] => '返回商品管理',
							'add-edit/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '我要重新编辑'
						)
					);
				} else {
					Custom_Common::showMsg('商城商品编辑失败');
				}
			} else {
				$resultArr = Model_Admin_Ticket::getInstance()->addCommodityTicket($getData, $userInfo, $this->_ad_city);
				if($resultArr['status'] == 100) {
					Custom_Log::log($this->_userInfo['id'], "新增了商城商品： <b>{$getData['ticket_title']}</b> ID：{$resultArr['insert_ticket_id']}", $this->pmodule, $this->cmodule, 'add', 'ticket', $resultArr['insert_ticket_id']);
					//WAP图片上传
					Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $resultArr['insert_ticket_id'], $getData['sid'], $user_id);
					Custom_Common::showMsg(
						'恭喜，你的商城商品添加成功！',
						'',
						array(
							'add-edit/sid:'. $getData['sid'] . '/uname:' . $getData['user_name'] . '/page:' . $getData['page']   => '继续新增商城商品',
							'list/page:' . $getData['page'] => '返回商品管理'
						)
					);
				} else {
					Custom_Common::showMsg('商城商品新增失败');
				}
			}
		}
		
		if($tid) {
			//券关联店铺
			$ticketRelationShopArray = Model_Admin_Ticket::getInstance()->getRelationShopByTicketId($tid);
			$this->_tpl->assign('ticketRelationShopArray', $ticketRelationShopArray);
			
			if($row['category_id']) {
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
			}
			//wap
			$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($tid);
			$this->_tpl->assign('wapImgData', $wapImgData);
		}
		
		$sid = $this->_http->get('sid');
		$uname = $this->_http->get('uname');
		
		$shop_info = $this->getShopFieldById($sid);
		$row['shop_name'] = $shop_info['shop_name'];
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		$skuArray = Model_Home_Suser::getInstance()->getSkuCategoryList();
		
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('uname', $uname);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('skuArray', $skuArray);
		
		$this->_tpl->display('admin/add_commodity.php');
	}
	
	public function auditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				if( $getData['audit_type'] == 1 ){
					//新品上线推送
					Model_Api_Message::getInstance()->addPreNotice("commodity","commodity_view",$getData['tid']);
				}
				$backUrl = 'list/page:' . $getData['page'];
				$content = "商城商品：{$getData['title']}　商城商品ID：{$getData['tid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit', 'ticket', $getData['tid']);
				Custom_Common::showMsg($getData['audit_type'] == 1?'审核通过':'审核不通过', '', array($backUrl => '返回商城商品'));
			} else {
				Custom_Common::showMsg('审核失败，请稍后再试，或者跟技术部核实信息');
			}
		}
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$tid = $this->_http->get('tid');
		$row = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
	
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/ticket_audit_commodity.php');
	}
			
	public function userShopAction() {
		$user_name = $this->_http->get('uname');
		$data = $this->_model->getShopListByUser($user_name, $this->_ad_city);
		
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('user_name', $user_name);
		$this->_tpl->display('admin/shop_choose_commodity.php');
	}
	
	public function checkUserShopAction() {
		$user_name = $this->_http->getPost('user_name');
		$res = $this->_model->check_user_shop($user_name);
		if(!$res){
			exit('ok');
		}
	}
	
	public function wapImgDelAction() {
		$id = intval($this->_http->get('id'));
    	$this->_db->update(
		    			'oto_ticket_wap_img', 
		    			array('ticket_id' => '0'), 
		    			array('id' => $id)
	    			); 
		exit(json_encode(array('status' => 'ok')));
	}
	
	public function imgAjaxColAction() {
		$getData = $this->_http->getPost();
	
		Model_Admin_Buygood::getInstance()->img_ajax_edit($getData);
	}
	
	/**
	 * 推荐商城商品
	 */
	public function recommendAction() {
		$id = $this->_http->get('id');
		$ticketRow = $this->select("`ticket_id` = '{$id}'", 'oto_ticket', '*', '', true);
	
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = Model_Admin_Ticket::getInstance()->checkRecommend($getData['id'], $getData['pos_id']);
			if($checkRepeat){
				Custom_Common::showMsg(
					'<span style="color:red">当前商品在此推荐位重复，请重新选择推荐位</span>',
					'recommend/id:' . $getData['id'] . '/page:' . $getData['page']
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
							'recommend/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
				$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
			} else {
				if($positionRow['width']) {
					Custom_Common::showMsg(
						'<span style="color:red">请上传图片</span>',
						'back',
						array(
							'recommend/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			}
			
			$getData['img_url'] = $img_url ? $img_url : '';

			$backUrl = 'list/page:' . $getData['page'];
			$getData['title']  = $ticketRow['ticket_title'];
			$getData['summary'] = $ticketRow['ticket_summary'];
			$result = $this->_model->recommend($getData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了商品名为  <b>{$ticketRow['ticket_title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend', 'ticket', $id);
				Custom_Common::showMsg(
					'推荐成功',
					'',
					array(
						$backUrl => '返回商城商品'
					)
				);
			}
		}
		
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('app_home_version_four', 'commodity','app_home_version_six')", "`identifier` not in ('app_home_recommended_coupons', 'app_home_recommended_for_you')");
	
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('id', $id);
		$this->_tpl->assign('ticketRow', $ticketRow);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/recommend_commodity.php');
	}	
}