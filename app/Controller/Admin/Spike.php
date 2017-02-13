<?php
/**
 * 快来抢（秒杀）
 * @author qiannz
 *
 */
class Controller_Admin_Spike extends Controller_Admin_Abstract {
	
	private $_model;
	private $_img_width;
	private $_img_height;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Spike::getInstance();
		$this->_img_width = 750;
		$this->_img_height = 350;
	}
	
	public function listAction() {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('spike');
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
		$this->_tpl->display('admin/spike_list.php');
	}
	
	/**
	 * 快来抢（秒杀）新增与编辑
	 */
	public function addEditAction() {
		$tid = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		if($tid) {
			$row = $this->_model->getSpikeRow($tid);
		}
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
				
			if($_FILES['file_img']['error'] == 0) {
				$imgInfo = getimagesize($_FILES['file_img']['tmp_name']);
				if ($imgInfo['0'] != $this->_img_width || $imgInfo['1'] != $this->_img_height) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 750 × 350 的图片'."\r\n";
						
					Custom_Common::showMsg(
						'<span style="color:red">'.$errMsg.'</span>',
						'back',
						array(
							'list/page:' . $getData['page'] => '返回快来抢'
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
	
			$user_id = $this->getUserIdByUserName($getData['user_name']);
			$userInfo = array('user_id' => $user_id, 'user_name' => $getData['user_name']);
			if($getData['tid']) {
				$resultArr = Model_Admin_Ticket::getInstance()->addEditSpikeTicket($getData, $userInfo, $this->_ad_city);
				if($resultArr['status'] == 100) {
					Custom_Log::log($this->_userInfo['id'], "编辑了快来抢： <b>{$getData['ticket_title']}</b> ID：{$getData['tid']}", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
					//WAP图片上传
					Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $getData['tid'], $getData['sid'], $user_id);
	
					Custom_Common::showMsg(
						'快来抢编辑成功',
						'',
						array(
							'add-edit/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '我要重新编辑',
							'list/page:' . $getData['page'] => '返回快来抢'
						)
					);
				} else {
					Custom_Common::showMsg('快来抢编辑失败');
				}
			} else {
				$resultArr = Model_Admin_Ticket::getInstance()->addEditSpikeTicket($getData, $userInfo, $this->_ad_city);
				if($resultArr['status'] == 100) {
					Custom_Log::log($this->_userInfo['id'], "新增了快来抢： <b>{$getData['ticket_title']}</b> ID：{$resultArr['insert_ticket_id']}", $this->pmodule, $this->cmodule, 'add', 'ticket', $resultArr['insert_ticket_id']);
					//WAP图片上传
					Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $resultArr['insert_ticket_id'], $getData['sid'], $user_id);
					Custom_Common::showMsg(
						'恭喜，你的快来抢添加成功！',
						'',
						array(
							'add-edit/sid:'. $getData['sid'] . '/uname:' . $getData['user_name'] . '/page:' . $getData['page']   => '继续新增快来抢',
							'list/page:' . $getData['page'] => '返回快来抢'
						)
					);
				} else {
					Custom_Common::showMsg('快来抢新增失败');
				}
			}
		}
	
		if($tid) {
			//快来抢关联店铺
			$ticketRelationShopArray = Model_Admin_Ticket::getInstance()->getRelationShopByTicketId($tid);
			$this->_tpl->assign('ticketRelationShopArray', $ticketRelationShopArray);
	
			//wap
			$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($tid);
			$this->_tpl->assign('wapImgData', $wapImgData);
		}
	
		$sid = $this->_http->get('sid');
		$uname = $this->_http->get('uname');
	
		$shop_info = $this->getShopFieldById($sid);
		$row['shop_name'] = $shop_info['shop_name'];
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
	
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('uname', $uname);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('storeArray', $storeArray);
	
		$this->_tpl->display('admin/add_spike.php');
	}

	public function userShopAction() {
		$user_name = $this->_http->get('uname');
		$data = Model_Admin_Commodity::getInstance()->getShopListByUser($user_name, $this->_ad_city);
	
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('user_name', $user_name);
		$this->_tpl->display('admin/shop_choose_spike.php');
	}

	public function auditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				$backUrl = 'list/page:' . $getData['page'];
				$content = "快来抢：{$getData['title']}　ID：{$getData['tid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit', '', $getData['tid']);
				Custom_Common::showMsg($getData['audit_type'] == 1?'审核通过':'审核不通过', '', array($backUrl => '返回快来抢'));
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
		$this->_tpl->display('admin/ticket_audit_spike.php');
	}
	
	public function imgAjaxColAction() {
		$getData = $this->_http->getPost();
		$result = Model_Admin_Ticket::getInstance()->img_ajax_edit($getData);
		if($result){
			exit(json_encode(true));
		}
	}
	
	/**
	 * 推荐券
	 */
	public function recommendAction() {
		$id = $this->_http->get('id');
		$type = $this->_http->get('type');
		$ticketRow = $this->select("`ticket_id` = '{$id}'", 'oto_ticket', '*', '', true);
	
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = $this->_model->checkRecommend($getData['id'], $getData['pos_id']);
			if($checkRepeat){
				Custom_Common::showMsg(
				'<span style="color:red">当前快来抢商品在此推荐位重复，请重新选择推荐位</span>',
				'recommend/id:' . $getData['id']. '/type:' . $getData['type']. '/page:' . $getData['page']
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
			}
	
			$getData['img_url'] = $img_url ? $img_url : '';
	
			// 券信息
			$backUrl = 'list/page:' . $getData['page'];
			$getData['title']  = $ticketRow['ticket_title'];
			$getData['summary'] = $ticketRow['ticket_summary'];
			$result = $this->_model->recommend($getData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了快来抢名为  <b>{$ticketRow['ticket_title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend', 'ticket', $id);
				Custom_Common::showMsg(
				'推荐成功',
				'',
				array(
				$backUrl => '返回快来抢列表'
				)
				);
			}
		}
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('app_home_version_six')", "`identifier` in ('app_home_six_love')");
	
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('id', $id);
		$this->_tpl->assign('ticketRow', $ticketRow);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('type', $type);
		$this->_tpl->display('admin/recommend_spike_position.php');
	}
}