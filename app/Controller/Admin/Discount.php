<?php
/*
 * 折扣先报控制器类
 */
class Controller_Admin_Discount extends Controller_Admin_Abstract{
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Discount::getInstance();	
	}
	
	//折扣列表
	public function listAction(){
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		//翻页传递参数拼接
		$page_str = $this->getPageStr($getData);
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
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
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/discount_list.php');
	}
	
	public function addAction() {
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
							), 'discount');
						
					if(!$img_url) {
						Custom_Common::showMsg(
							'<span style="color:red">请检查图片格式，大小是否正确</span>',
							'back'
						);
					}
					$wap_img_url[] = $img_url;
				}
			}
			
			$postResult = $this->_model->postDiscount($getData);

			Custom_Log::log($this->_userInfo['id'], "折扣标题：{$getData['title']}, 折扣ID：{$postResult['insert_id']}", $this->pmodule, $this->cmodule, 'add', 'discount', $postResult['insert_id']);
			
			if( !empty( $wap_img_url ) ){
				$this->_model->wapImgUpload($wap_img_url, $postResult['insert_id']);
			}
			
			Custom_Common::showMsg(
				'折扣新增成功',
				'back',
				array(
					'add' => '继续新增',
					'list' => '返回折扣管理'
				)
			);		
		}
		//活动类型
		$sortTicketArray = $this->getTicketSortById(0, 'discounttype');
		$this->_tpl->assign("sortTicketArray",$sortTicketArray);
		
		//折扣分类
		$categoryArray  = $this->getTicketSortById( 0 , 'discountcategory' );
		$this->_tpl->assign("categoryArray",$categoryArray);
		
		//所属行政区
		$regionArray = $this->getRegion( 0 , true , $this->_ad_city);
		$this->_tpl->assign("regionArray",$regionArray);
		
		$this->_tpl->display('admin/discount_add.php');
	}
	
	public function editAction() {
		$discount_id = $this->_http->get('did');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
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
							), 'discount');
		
					if(!$img_url) {
						Custom_Common::showMsg(
						'<span style="color:red">请检查图片格式，大小是否正确</span>',
						'back'
						);
					}
					$wap_img_url[] = $img_url;
				}
			}
		
			$postResult = $this->_model->postDiscount($getData);
	
			Custom_Log::log($this->_userInfo['id'], "折扣标题：{$getData['title']}, 折扣ID：{$postResult['insert_id']}", $this->pmodule, $this->cmodule, 'edit', 'discount', $postResult['insert_id']);
	
			if( !empty( $wap_img_url ) ){
				$this->_model->wapImgUpload($wap_img_url, $postResult['insert_id']);
			}
	
			Custom_Common::showMsg(
				'折扣编辑成功',
				'back',
				array(
					'edit/did:' . $getData['did'] . '/page:' . $getData['page'] => '继续编辑',
					'list/' . '/page:' . $getData['page'] => '返回折扣管理'
				)
			);
		
		}
		
		$row = $this->_model->getDiscountRow($discount_id);
		$this->_tpl->assign("row",$row);
		//wap图片
		$wapImgList = $this->_model->getWapImgList($discount_id);
		$this->_tpl->assign("wapImgList",$wapImgList);
		
		//获取品牌列表
		$brandArray = $this->_model->getBrandListById($discount_id);
		$this->_tpl->assign("brandArray" , $brandArray);
		
		//活动类型
		$sortTicketArray = $this->getTicketSortById(0, 'discounttype');
		$this->_tpl->assign("sortTicketArray",$sortTicketArray);
		
		//折扣分类
		$categoryArray  = $this->getTicketSortById( 0 , 'discountcategory' );
		$this->_tpl->assign("categoryArray",$categoryArray);
		
		//所属行政区
		$regionArray = $this->getRegion( 0 , true , $this->_ad_city);
		$this->_tpl->assign("regionArray",$regionArray);
		
		$circleArray = $this->getCircleByRegionId($row['region_id'], false, true, $this->_ad_city);
		$this->_tpl->assign("circleArray",$circleArray);
		$marketArray = $this->getMarketByRidAndCid($row['region_id'], $row['circle_id'], $this->_ad_city);
		$this->_tpl->assign("marketArray",$marketArray);
		
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/discount_add.php');
	}
	
	public function getBrandListAction(){
		$filter = Custom_String::HtmlReplace( $this->_http->get("filter"), 3 );
		$data = $this->_model->getBrandList($filter, $this->_ad_city);
		exit(json_encode($data));
	}
	
	//wap图片删除
	public function wapImgDelAction(){
		$id = intval($this->_http->get("id"));
		if( $id ){
			$wapImgRow = $this->select("`id` = '{$id}'", 'discount_wap_img', '*', '', true);
			if( !empty($wapImgRow) && $this->_db->delete('discount_wap_img', '`id` = ' . $id) ){
				unlink(ROOT_PATH . 'web/data/discount/' . $wapImgRow['img_url']);
				unlink(ROOT_PATH . 'web/data/buy/discount/' . $wapImgRow['img_url']);
			}else{
				exit(json_encode(array('status' => 'fail')));
			}
		}
		exit(json_encode(array('status' => 'ok')));
	}
	
	//修改图片的序号
	public function imgAjaxColAction(){
		$getData = $this->_http->getPost();
		$this->_model->img_ajax_edit($getData);
	}
	
	//删除折扣的品牌
	public function brandDelAction(){
		$brand_id = intval($this->_http->get("bid"));
		$discount_id = intval($this->_http->get("did"));
		$this->_db->delete('discount_brand', array('discount_id' => $discount_id,"brand_id"=>$brand_id));
		exit(json_encode(array('status' => 'ok')));
	}
	
	//折扣删除
	public function delAction(){
		$id = $this->_http->get('id');
		$page = $this->_http->get('page');
		$result = $this->_model->del($id);
		if($result) {
			$row = $this->_model->getDiscountRow($id);
			$content = "折扣标题：{$row['title']} 　折扣ID：{$id}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'discount', $id);
			Custom_Common::showMsg(
				'删除成功', 
				'back'
			);
		}
	}
	
	//批量折扣删除
	public function delAllAction(){
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$result = $this->_model->del($ids);
		if($result) {
			$content = "折扣ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'discount', $ids);
			Custom_Common::showMsg(
				'批量删除成功',
				'back'
			);
		}
	}
	
	// 批量恢复删除
	public function unDelAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$result = $this->_model->unDel($ids);
		if($result) {
			$content = "折扣ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'unDel', 'discount', $ids);
			Custom_Common::showMsg('批量恢复成功', 'back', array('list' => '返回折扣管理'));
		}
	}
	
	//审核
	public function auditAction() {
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$did = $this->_http->get('did');
		$row = $this->_model->getDiscountRow($did);
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				$backUrl = 'list/page:' . $page;
				$content = "折扣标题：{$row['title']}　折扣ID：{$row['discount_id']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit', 'discount', $did);
				Custom_Common::showMsg($getData['audit_type'] == 1?'审核通过':'审核不通过', '', array($backUrl => '返回折扣管理'));
			} else {
				Custom_Common::showMsg('审核失败，请稍后再试，或者跟技术部核实信息');
			}
		}
		
	
		$this->_tpl->assign('did', $did);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/discount_audit.php');
	} 
	/**
	 * 推荐折扣
	 */
	public function recommendAction() {
		$did = $this->_http->get('did');
		$discountRow = $this->_model->getDiscountRow($did);
	
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = $this->_model->checkRecommend($getData['did'], $getData['pos_id']);
			if($checkRepeat){
				Custom_Common::showMsg(
					'<span style="color:red">当前折扣在此推荐位重复，请重新选择推荐位</span>',
					'recommend/did:' . $getData['did'] . '/page:' . $getData['page']
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
							'recommend/did:' . $getData['did'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			} else {
				if($positionRow['width']) {
					Custom_Common::showMsg(
						'<span style="color:red">请上传图片</span>',
						'back',
						array(
							'recommend/did:' . $getData['did'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			}
	
			$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
				
			$getData['img_url'] = $img_url ? $img_url : '';
	
			$backUrl = 'list/page:' . $getData['page'];
	
			$getData['title']  = $discountRow['title'];
			$getData['summary'] = $discountRow['wap_content'];
			$result = $this->_model->recommend($getData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了折扣名为  <b>{$discountRow['title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend', 'discount', $getData['did']);
				Custom_Common::showMsg(
					'推荐成功',
					'',
					array(
						$backUrl => '返回折扣管理'
					)
				);
			}
		}
	
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('app_home_version_four', 'discount','app_home_version_six')", "`identifier` not in ('app_home_recommended_coupons', 'app_home_recommended_for_you', 'discount_brand_recommend', 'discount_market_recommend', 'discount_circle_recommend')");
	
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('discountRow', $discountRow);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/discount_recommend.php');
	}
	/**
	 * 咨询
	 */
	public function consultationAction() {
		$page_str = '';
		$getData = $this->_http->getParams();
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$did = $getData['did'];
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getConsultationList($getData, $page);
		$page_info['item_count'] = $data['totalNum'];
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('did', $did);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data['data']);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/discount_consultation_list.php');
	}
	
	public function consultationShowAction() {
		$getData = $this->_http->getParams();
		$data = $this->_model->getConsultationPost($getData['tid']);
	
		$this->_tpl->assign('did', $getData['did']);
		$this->_tpl->assign('tid', $getData['tid']);
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/discount_consultation_show.php');
	}
	
	public function delConsultationAction() {
		$getData = $this->_http->getParams();
		$result = $this->_model->delConsultation($getData['id'], $getData['did']);
		if($result) {
			Custom_Common::showMsg(
				'删除成功', 
				'', 
				array(
					'consultation/did:' . $getData['did'] . '/page:' . $getData['page'] => '返回咨询管理'
				)
			);
		}		
	}
	
	public function delAllConsultationAction() {
		$getData = $this->_http->getParams();
		$result = $this->_model->delConsultation($getData['id'], $getData['did']);
		if($result) {
			Custom_Common::showMsg(
				'删除成功', 
				'', 
				array(
					'consultation/did:' . $getData['did'] . '/page:' . $getData['page'] => '返回咨询管理'
				)
			);		
		}
	}
	
	//批量恢复咨询删除
	public function unDelConsultationAction() {
		$getData = $this->_http->getParams();
		$result = $this->_model->unDelConsultation($getData['id'], $getData['did']);
		if($result) {
			Custom_Common::showMsg(
				'批量恢复成功', 
				'', 
				array(
					'consultation/did:' . $getData['did'] . '/page:' . $getData['page'] => '返回咨询管理'
				)
			);		
		}
	}
	
	public function delConsultationPostAction() {
		$getData = $this->_http->getParams();
	
		$result = $this->_model->delConsultationPost($getData['pid']);
		if($result) {
			Custom_Common::showMsg(
				'咨询问答明细删除成功',
				'',
				array(
					'consultation-show/did:' . $getData['did'] . '/tid:' . $getData['tid'] => '返回咨询明细',
					'consultation/did:' . $getData['did'] . '/tid:' . $getData['tid'] => '返回咨询管理'
				)
			);
		}
	}

	public function groupChatAction() {
		$getData = $this->_http->getParams();
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$did = $getData['did'];
		$type = $getData['type'] ? $getData['type']:'discount';
		$data = $this->_model->getGroupChat($did, $page , $type);
		
		$this->_tpl->assign('did', $did);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('type', $type);
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/discount_group_chat.php');		
	}
	
	public function delGroupChatPostAction() {
		$getData = $this->_http->getParams();
		$type = $getData["type"]?$getData["type"]:"discount";
		$result = $this->_model->delGroupChatPost($getData['gcid']);
		if($result) {
			Custom_Common::showMsg(
			'群聊记录删除成功',
			'',
			array(
				'group-chat/did:' . $getData['did']."/type:{$type}" => '返回群聊管理'
			)
			);
		}
	}
	public function unDelGroupChatPostAction() {
		$getData = $this->_http->getParams();
		$type = $getData["type"]?$getData["type"]:"discount";
		$result = $this->_model->unDelGroupChatPost($getData['gcid']);
		if($result) {
			Custom_Common::showMsg(
			'群聊记录取消删除成功',
			'',
			array(
			'group-chat/did:' . $getData['did']."/type:{$type}" => '返回群聊管理'
			)
			);
		}
	}
	// 上传图片
	public function uploadAction() {
		if($this->_http->isPost()){
			$user_name = $this->_http->has('user_name') ? $this->_http->get('user_name') : DEFINED_USER_NAME;
			$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'discount';
			$primary_id = $this->_http->has('primary_id') ? intval($this->_http->get('primary_id')) : 0;
			$user_id = $this->getUserIdByUserName($user_name);
			$filePath = Custom_Upload::singleImgUpload($_FILES['uploadFile'], $folder);
			if($filePath){
				$arr = array(
						'discount_id'   => $primary_id,
						'user_id'  => $user_id,
						'img_url'  => $filePath,
						'created' => REQUEST_TIME
				);
				$aid = $this->_db->insert('discount_img', $arr);
				$picArr = array(
						'error' => 0,
						'data' => array(
									array(
											'aid' => $aid,
											'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL']."/api/good/get-special-img-thumb/iid/{$aid}/type/".$folder,
											'gid' => $primary_id
										)
									)
								);
				exit(json_encode($picArr));
			}
			exit(json_encode(array('error' => 1)));
		}
	}
	
	//删除图片
	public function delImgAction() {
		$id = intval($this->_http->get('aid'));
		if( $id ){
			$imgRow = $this->select("`id` = '{$id}'", 'discount_img', '*', '', true);
			if( !empty($imgRow) && $this->_db->delete('discount_img', '`id` = ' . $id) ){
				unlink(ROOT_PATH . 'web/data/discount/' . $imgRow['img_url']);
				unlink(ROOT_PATH . 'web/data/buy/discount/' . $imgRow['img_url']);
			}else{
				exit(json_encode(array('status' => 'fail')));
			}
		}
		echo json_encode(array('status' => 'ok'));
	}
}