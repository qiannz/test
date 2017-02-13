<?php
class Controller_Api_Shop extends Controller_Api_Abstract {

	private $_model;
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Shop::getInstance();
	}
	
	public function homeAction() {
		$postData = $this->_http->getPost();
		$myShopArray = array();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$sid = $postData['shop_id'];
		// 每日一说
		$every_day_says = Model_Api_Goods::getInstance()->getClicks('APP_DAILY_SAYS');
		$myShopArray['every_day_says'] = empty($every_day_says) ? '' : $every_day_says;
		
		//$uid = 10;
		// 获取用户状态
		$user_type = $this->_model->getUserType($uid);
		
		// 今日上传日期条件
		$stime = strtotime(date('Y-m-d'));
		$etime = strtotime(date('Y-m-d').' 23:59:59');
		
		// 每月第一天
		$firstDay = strtotime(date('Y-m-d', mktime(0,0,0,date('n'),1,date('Y'))));
		$today = REQUEST_TIME;
		
		if ($user_type == 2) {
			$shopList = $this->_model->getShopListByUserId($uid);
			if (empty($shopList)) {
				echo json_encode($this->returnArr(0, '', 201, '您还没有认领店铺'));
				exit();
			}
			$myShopArray['shopList'] = $shopList;
			$shopListFirstRow = current($shopList);
			if (empty($sid)) {
				$sid = $shopListFirstRow['shop_id'];
			}
			$salemanRow = $this->_model->getSaleMan($sid);
			$uidArr = implode(',', $salemanRow);
			if (empty($salemanRow)) { // 不存在营业员
				// 今日上传
				$myShopArray['s_num_today'] = $this->_model->getUploadNum($sid, "user_id = '{$uid}' AND created between '{$stime}' AND '{$etime}' ");
				$myShopArray['u_num_today'] = $this->_model->getUploadNum($sid, "user_id <> '{$uid}' AND created between '{$stime}' AND '{$etime}' ");
			
				// 本月上传
				$myShopArray['s_num_montn'] = $this->_model->getUploadNum($sid, "user_id = '{$uid}' AND created between '{$firstDay}' AND '{$today}' ");
				$myShopArray['u_num_montn'] = $this->_model->getUploadNum($sid, "user_id <> '{$uid}' AND created between '{$firstDay}' AND '{$today}' ");
			
				// 本月平均上传
				$myShopArray['s_num_montn_average'] = ceil($myShopArray['s_num_montn'] / date('j'));
				$myShopArray['u_num_montn_average'] = ceil($myShopArray['u_num_montn'] / date('j'));
			
				// 商户/营业员上传的图片（3张）
				$myShopArray['s_img'] = $this->_model->getImg($sid, "user_id = '{$uid}'", 3);
				// 网友上传的图片（4张）
				$myShopArray['u_img'] = $this->_model->getImg($sid, "user_id <> '{$uid}'", 4);
			
			} else { // 存在营业员
				// 今日上传
				$all_uids = $uidArr.','.$uid;
				$myShopArray['s_num_today'] = $this->_model->getUploadNum($sid, "user_id in (" . $all_uids . ") AND created between '{$stime}' AND '{$etime}'");
				$myShopArray['u_num_today'] = $this->_model->getUploadNum($sid, "user_id not in (" . $all_uids . ") AND created between '{$stime}' AND '{$etime}'");
			
				// 本月上传
				$myShopArray['s_num_montn'] = $this->_model->getUploadNum($sid, "user_id in (" . $all_uids . ") AND created between '{$firstDay}' AND '{$today}'");
				$myShopArray['u_num_montn'] = $this->_model->getUploadNum($sid, "user_id not in (" . $all_uids . ") AND created between '{$firstDay}' AND '{$today}'");
			
				// 本月平均上传
				$myShopArray['s_num_montn_average'] = ceil($myShopArray['s_num_montn'] / date('j'));
				$myShopArray['u_num_montn_average'] = ceil($myShopArray['u_num_montn'] / date('j'));
			
				// 商户/营业员上传的图片（3张）
				$myShopArray['s_img'] = $this->_model->getImg($sid, "user_id in (" . $all_uids . ")", 3);
				// 网友上传的图片（4张）
				$myShopArray['u_img'] = $this->_model->getImg($sid, "user_id not in (" . $all_uids . ")", 4);
			
			}
		}else if ($user_type == 3) {
			$user_shop_competence = $this->_model->getPermissionShopByUserId($uid);
			$sid = $user_shop_competence['shop_id'];
			if (empty($sid)) {
				echo json_encode($this->returnArr(0, '', 201, '您还没有认领店铺'));
				exit();
			}
			$myShopArray['shopList'] = $this->_model->getShopListBySid($sid);
			$salemanRow = $this->_model->getSaleMan($sid);
			$uidArr = implode(',', $salemanRow);
			// 今日上传
			$myShopArray['s_num_today'] = $this->_model->getUploadNum($sid, "user_id = '{$uid}' AND created between '{$stime}' AND '{$etime}'");
			$myShopArray['u_num_today'] = $this->_model->getUploadNum($sid, "user_id not in (" . $uidArr . ") AND created between '{$stime}' AND '{$etime}'");
				
			// 本月上传
			$myShopArray['s_num_montn'] = $this->_model->getUploadNum($sid, "user_id = '{$uid}' AND created between '{$firstDay}' AND '{$today}'");
			$myShopArray['u_num_montn'] = $this->_model->getUploadNum($sid, "user_id not in (" . $uidArr . ") AND created between '{$firstDay}' AND '{$today}'");
				
			// 本月平均上传
			$myShopArray['s_num_montn_average'] = ceil($myShopArray['s_num_montn'] / date('j'));
			$myShopArray['u_num_montn_average'] = ceil($myShopArray['u_num_montn'] / date('j'));
				
			// 商户/营业员上传的图片（3张）
			$myShopArray['s_img'] = $this->_model->getImg($sid, "user_id = '{$uid}'", 3);
			// 网友上传的图片（4张）
			$myShopArray['u_img'] = $this->_model->getImg($sid, "user_id not in (" . $uidArr . ")", 4);
		}
		// 同行业平均上传数
		$myShopArray['same_job_num'] = rand(20,45);		
		// 店铺公告
		$myShopArray['shop_note'] = $this->_db->fetchOne("select notice from oto_shop where shop_id = '{$sid}'");
		
		// 优惠券
		// 1. 现金券部分
		$voucherInfo = Custom_AuthTicket::getMerchantStat($sid);
		$soldToday = $soldMonth = $usedToday = $usedMonth = 0;
		if ($voucherInfo['code'] == 1) {
			$soldToday = $voucherInfo['message']['SoldToday']; // 今日验证的现金券
			$soldMonth = $voucherInfo['message']['SoldMonth']; // 本月验证的现金券
			$usedToday = $voucherInfo['message']['UsedToday']; // 今日销售的现金券
			$usedMonth = $voucherInfo['message']['UsedMonth']; // 本月销售的现金券
		}
		
		$myShopArray['SoldToday'] = $soldToday;
		$myShopArray['SoldMonth'] = $soldMonth;
		$myShopArray['UsedToday'] = $usedToday;
		$myShopArray['UsedMonth'] = $usedMonth;
		
		// 在售优惠券数
		$ticket_online_num = $this->_model->getTicketOnline($sid);
		$myShopArray['ticket_online_num'] = $ticket_online_num;
		
		echo json_encode($this->returnArr(1, $myShopArray));
		exit();
		
	}
	
	// 店铺公告编辑
	public function noticeEditAction() {
		$postData = $this->_http->getPost();
		$notice = $postData['not'];
		$sid = $postData['shop_id'];
		if(!empty($notice) && mb_strlen($notice, 'utf8') > 100 ) {
			echo json_encode($this->returnArr(0, '', 101, '店铺公告100个字以内（汉字算一个字符）'));
			exit();
		}
		$result = $this->_model->editShopNotice($sid, $notice);
		if ($result) {
			echo json_encode($this->returnArr(1, '', 100, '店铺公告编辑成功'));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', 300, '店铺公告编辑失败'));
			exit();
		}
	}
	
	// 认证商品列表
	public function AuthGoodListAction() {
		$postData = $this->_http->getPost();
		$type = $postData['type'];
		$sid = $postData['shop_id'];
		$page = max(1, intval($postData['page']));
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);

		// 获取用户状态
		$user_type = $this->_model->getUserType($uid);
		$salemanRow = $this->_model->getSaleMan($sid);
		$uidArr = implode(',', $salemanRow);
		$goodList = array();
		if ($user_type == 2) { // 商户
			if (empty($salemanRow)) { // 不存在营业员
				$goodList = $this->_model->getGoodList($sid, $type, "user_id <> '{$uid}'", $page); 
			} else { // 存在营业员
				$all_uids = $uidArr.','.$uid;
				$goodList = $this->_model->getGoodList($sid, $type, "user_id not in (" .$all_uids . ")", $page);
			}
		} elseif ($user_type == 3) { // 营业员
			$goodList = $this->_model->getGoodList($sid, $type, "user_id not in (" .$uidArr . ")", $page);
		}
		echo json_encode($goodList);
		exit();
	}
	
	// 管理商品列表
	public function manageGoodListAction() {
		$postData = $this->_http->getPost();
		$type = 'all';
		$sid = $postData['shop_id'];
		$page = max(1, intval($postData['page']));
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		
		
		// 获取用户状态
		$user_type = $this->_model->getUserType($uid);
		$salemanRow = $this->_model->getSaleMan($sid);
		$uidArr = implode(',', $salemanRow);
		$goodList = array();
		if ($user_type == 2) { // 商户
			if (empty($salemanRow)) { // 不存在营业员
				$goodList = $this->_model->getGoodList($sid, $type, "user_id = '{$uid}'", $page);
			} else { // 存在营业员
				$all_uids = $uidArr.','.$uid;
				$goodList = $this->_model->getGoodList($sid, $type, "user_id in (" .$all_uids . ")", $page);
			}
		} elseif ($user_type == 3) { // 营业员
			$goodList = $this->_model->getGoodList($sid, $type, "user_id in (" .$uidArr . ")", $page);
		}
		echo json_encode($goodList);
		exit();
		
	}
	
	// 上传商品
	public function addAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$result = Model_Api_Goods::getInstance()->addGood($postData);
		if ($result) {
			$this->updateQuantityTotalGoodByUserId($uid);
			echo json_encode($this->returnArr(1, '', 100, '添加成功'));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', 300, '添加失败'));
			exit();
		}
	}
	
	// 编辑商品前， 先读取商品详情
	public function goodViewAction() {
		$postData = $this->_http->getPost();
		$gid = $postData['gid'];
		if (empty($gid)) {
			echo json_encode($this->returnArr(0, '', 300, '请选择宝贝'));
			exit();
		}
		$goodView = $this->_model->getGoodView($gid);
		echo json_encode($goodView);
		exit();
	}
	
	// 编辑商品
	public function editAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$good_name = trim($postData['gname']);
		$gid = intval($postData['gid']);
		$dis_price = $postData['dprice'];
		$ip = $postData['ip'];
		if (empty($gid) || empty($good_name)) {
			$errorMsg .= '请填写宝贝名称|';
		}

		if (empty($dis_price)) {
			$errorMsg .= '请填写宝贝现价';
		}
		if (!empty($errorMsg)) {
			echo json_encode($this->returnArr(0, '', 300, $errorMsg));
			exit();
		}
		$result = Model_Api_Goods::getInstance()->editGood($postData);
		if ($result) {
			echo json_encode($this->returnArr(1, '', 100, '编辑成功'));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', 300, '编辑失败'));
			exit();
		}
	}
	
	// 修改宝贝时 上传单图
	public function uploadAction() {
		$postData = $this->_http->getPost();
		$img = $postData['img'];
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$gid = $postData['gid'];
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		if (empty($gid)) {
			echo json_encode($this->returnArr(0, '', 300, '请选择宝贝'));
			exit();
		}
		if (empty($img)) {
			echo json_encode($this->returnArr(0, '', 300, '请选择上传图片'));
			exit();
		}
		$result = Model_Api_Goods::getInstance()->uploadImg($img, $uid, $gid);
		echo json_encode($result);
		exit();
	}
	
	// 删除宝贝 图片
	public function delImgAction() {
		$postData = $this->_http->getPost();
		$goodImgId = $postData['goodImgId'];
		$gid = $postData['gid'];
		if (empty($goodImgId)) {
			echo json_encode($this->returnArr(0, '', 300, '请选择要删除的图片'));
			exit();
		}
		$result = Model_Api_Goods::getInstance()->delImg($goodImgId, $gid);
		echo json_encode($result);
		exit();
	}
	
	// 验证
	public function authGoodAction() {
		$postData = $this->_http->getPost();
		$gid = $postData['gid'];
		$status = $postData['status'];
		if (empty($gid)) {
			echo json_encode($this->returnArr(0, '', 300, '请选择要验证的宝贝'));
			exit();
		}
		$result = $this->_model->auth($gid, $status);
		if ($result) {
			echo json_encode($this->returnArr(1, '', 100, '操作成功'));
			exit();
		}
	}
	
	/**
	 * 优惠券验证  - 查询优惠券
	 */
	public function searchTicketAction() {
		$postData = $this->_http->getPost();
		$sid = intval($postData['sid']);
		$phone = $postData['phone'];
		
		//判断手机号码是否存在， 券ID 是否合法
		if(!preg_match('/^1[3|4|5|6|7|8|9][0-9]{9}$/', $phone)){
			echo json_encode($this->returnArr(0, '', 101, '请输入正确的手机'));
			exit();
		}
		
		if (!$sid) {
			echo json_encode($this->returnArr(0, '', 102, '请选择要查询的商家'));
			exit();
		}
		//开始查询
		$resultArray = Model_Home_Suser::getInstance()->searchTicketByPhone($sid, $phone);
		if($resultArray['res'] == 300) {
			echo json_encode($this->returnArr(0, '', $resultArray['res'], $resultArray['msg']));
			exit();
		} else {
			echo json_encode($this->returnArr(count($resultArray['extra']), $resultArray['extra']));
			exit();
		}
	}
	
	
	/**
	 * 优惠券验证 - 使用优惠券
	 */
	public function useTicketAction() {
		$postData = $this->_http->getPost();
		$sid = intval($postData['sid']);
		$phone = $postData['phone'];
		$detailIdString = $postData['items'];
		$resultArray = Model_Home_Suser::getInstance()->useTicket($sid, $phone, $detailIdString);
		echo json_encode($this->returnArr(1, '', $resultArray['res'], $resultArray['msg']));
		exit();
	}
	
	
	/**
	 * 现金券查询 - 查询已经购买的现金券
	 */
	public function searchVoucherAction() {
		$postData = $this->_http->getPost();
		$sid = intval($postData['sid']);
		$captcha = $postData['captcha'];
		//判断验证码是否存在， 券ID 是否合法
		if(!$captcha){
			echo json_encode($this->returnArr(0, '', 105, '请输入验证码'));
			exit();
		}
		if (!$sid) {
			echo json_encode($this->returnArr(0, '', 106, '请选择要查询的商家'));
			exit();
		}
		//开始查询		
		$resultArray = Model_Home_Suser::getInstance()->inquireTicketByCaptcha($sid, $captcha);
		if($resultArray['res'] == 100) {
			echo json_encode($this->returnArr(count($resultArray['extra']), $resultArray['extra']));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', $resultArray['res'], $resultArray['msg']));
			exit();
		}
	}
	
	/**
	 * 现金券验证  - 使用现金券
	 */
	public function vaildVoucherTicketAction() {
		$postData = $this->_http->getPost();
		$sid = intval($postData['sid']);
		$detailIdString = $postData['items'];
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$userType = $this->_model->getUserType($uid);
		$userInfo = array (
					'user_id' => $uid,
					'user_name' => $postData['uname'],
					'user_type' => $userType,
				);

		$resultArray = Model_Home_Suser::getInstance()->vaildVoucherTicket($sid, $detailIdString, $userInfo);
		if ($resultArray['res'] == 100) {
			echo json_encode($this->returnArr(1, $resultArray['extra'], $resultArray['res'], $resultArray['msg']));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', $resultArray['res'], $resultArray['msg']));
			exit();
		}
	}
	
	// 店铺下所有优惠券列表
	public function ticketListAction() {
		$postData = $this->_http->getPost();
		$sid = intval($postData['shop_id']); 
		$page = max(1, intval($postData['page']));
		if (!$sid) {
			echo json_encode($this->returnArr(0, '', 102, '请选择要查询的商家'));
			exit();
		}
		$ticketlist = $this->_model->getTicketList($sid, $page);
		echo json_encode($ticketlist);
		exit();
	}
	
	// 营业员所在店铺下的现金券
	public function shopCouponListAction() {
		$postData = $this->_http->getPost();
		$sid = intval($postData['shop_id']);
		$page = max(1, intval($postData['page']));
		if (!$sid) {
			echo json_encode($this->returnArr(0, '', 101, '请选择要查询的店铺'));
			exit();
		}
		$couponList = Custom_AuthTicket::getShopCouponList($sid, $page);
		if ($couponList['code'] == 1) {
			$rs = $couponList['message']['Result'];
			echo json_encode($this->returnArr(count($rs), $rs));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', 102, '数据异常，请重新查询'));
			exit();
		}
	}
	
	//获取店铺列表
	public function shopListAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		$data = $this->_model->getShopList($getData, $this->_city);
		exit(json_encode($this->returnArr(1, $data)));
	}
	
	//获取店铺详情
	public function shopDetailAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		if( !intval($getData['sid']) ){
			exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));	
		}
		$data = Model_Api_App::getInstance()->getCommodityShopDetail( $getData , $this->_city);		
		exit(json_encode($this->returnArr(1, $data)));
	}
}