<?php
class Controller_Api_Message extends Controller_Api_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Message::getInstance();
	}
	/**
	 * .NET 加密验证
	 * @param unknown_type $getData
	 */
	private function verify($getData) {
		Third_Des::$key = 'IN0xMmwV';
		$ssid = $getData['sid'];
		$encrypt_uuid = $getData['uuid'];
		$time = $getData['time'];
		
		$decrypt_ssid =  Third_Des::encrypt($encrypt_uuid . '|'. $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'] . '|' . $time);
		$decrypt_ssid = urldecode($decrypt_ssid);
		if(empty($ssid) || $ssid != $decrypt_ssid) {
			_sexit('验证失败', 300);
		}
	}	
	/**
	 * 某个商品的公告列表 (参数 ：tid, time, ssid)
	 */
	public function noticeListAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'tid 不能为空')));
		}
		
		$noticeList = $this->_model->getNoticeList($getData);
		
		exit(json_encode($this->returnArr(1, $noticeList)));
	}
	/**
	 * 新增某个商品的公告(参数 : tid, uuid, uname, time, ssid, content, ip)
	 */
	public function addNoticeAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'tid 不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		if(!$userInfo['user_type'] || $userInfo['user_type'] == 1) {
			exit(json_encode($this->returnArr(0, array(), 300, '用户身份错误')));
		}
				
		//新增公告
		$addResult = $this->_model->addNotice($getData, $userInfo);
		if($addResult) {
			exit(json_encode($this->returnArr(1, array('nid' => $addResult), 100)));
		}
	}
	/**
	 * 删除某个商品的公告(参数 : tid, nid,  uuid, uname, time, ssid)
	 */
	public function delNoticeAction() {
		$getData = $this->_http->getParams();
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/delNotice/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'tid 不能为空')));
		}
		//公告ID
		if(!$getData['nid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'nid 不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		if(!$userInfo || $userInfo['user_type'] == 1) {
			exit(json_encode($this->returnArr(0, array(), 300, '用户身份错误')));
		}
		//删除公告
		$delResult = $this->_model->delNotice($getData, $userInfo);
		if($delResult) {
			exit(json_encode($this->returnArr(1, array())));
		}
	}
	/**
	 * 个人留言列表
	 * 参数：frid, type[good：商品 | brand：品牌 | shop：店铺 | market：商场 | voucher：现金券 | buygood：团购商品]
	 * 		 uuid, uname, time, ssid,
	 */
	public function personalMessageAction() {
		$getData = $this->_http->getParams();
		
		//通知日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/personalMessage/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//传输加密验证
		$this->auth($getData);
		
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'frid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		//获取TID
		$tid = $this->_model->getThreadTid($getData, $userInfo);
		if(!$tid) {
			exit(json_encode($this->returnArr(1, array())));
		}
		$getData['tid'] = $tid;
		
		$personalMessage = $this->_model->getPersonalMessage($getData, $userInfo);
		
		exit(json_encode($this->returnArr(1, $personalMessage)));
	}
	
	public function personalMessageTwoAction(){
		$getData = $this->_http->getParams();
		
		//通知日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/personalMessageTwo/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//传输加密验证
		$this->auth($getData);
		
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'frid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		
		//商品信息
		$ticketInfo = Model_Home_Ticket::getInstance()->getTicketInfoForMsg($getData['frid'],true);
		if( empty($ticketInfo) ){
			exit(json_encode($this->returnArr(0, array(), 300, '找不到咨询商品信息')));
		}
		$lat = $getData["lat"];
		$lng = $getData["lng"];
		if( $lat>0 && $lng>0 && $ticketInfo['lat']>0 && $ticketInfo['lng']>0 ){
			$ticketInfo["distance"] = getDistance($lat, $lng, $ticketInfo['lat'], $ticketInfo['lng']);
		}
		//店员列表
		$shopStaff = Model_Admin_Shop::getInstance()->getShopStaffManagementList($ticketInfo["shop_id"], $this->_city);
		//获取TID
		$tid = $this->_model->getThreadTid($getData, $userInfo);
		if(!$tid) {
			exit(json_encode($this->returnArr(1, array("ticket"=>$ticketInfo,"message"=>array(),"staff"=>$shopStaff))));
		}
		$getData['tid'] = $tid;	
		$personalMessage = $this->_model->getPersonalMessage($getData, $userInfo);
		
		exit(json_encode($this->returnArr(1, array("ticket"=>$ticketInfo,"message"=>$personalMessage,"staff"=>$shopStaff) )));
	}
	
	
	/**
	 * 营业员，商户留言明细
	 * 参数：tid, frid, type[good：商品 | brand：品牌 | shop：店铺 | market：商场 | voucher：现金券 | buygood：团购商品]
	 * 		 uuid, uname, time, ssid,
	 */
	public function clerkNoticeShowAction() {
		$getData = $this->_http->getParams();
	
		//通知日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/clerkNoticeShow/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
	
		//传输加密验证
		$this->auth($getData);
		//留言tid
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'tid 不能为空')));
		}
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, 'frid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
	
		$personalMessage = $this->_model->getPersonalMessage($getData, $userInfo);
	
		exit(json_encode($this->returnArr(1, $personalMessage)));
	}
	
	/**
	 * 添加新的留言
	 * 参数：frid, type[good：商品 | brand：品牌 | shop：店铺 | market：商场 | voucher：现金券 | buygood：团购商品  | commodity: 商城商品], 
	 * 		 uuid, uname, time, ssid, qst
	 */
	public function newPersonalMessageAction() {
		$getData = $this->_http->getParams();
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/newPersonalMessage/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '商品 frid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//留言内容
		if(!$getData['qst']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言内容，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		
		$addNewPersonalMessageResult = $this->_model->addNewPersonalMessage($getData, $userInfo);
		if($addNewPersonalMessageResult) {
			/*
			 * 新建咨询时候不再发送通知
			//发送通知
			switch ($getData['type']) {
				case 'voucher':
					$openType = "voucher_advisory";
					break;
				case 'buygood':
					$openType = "nine_buy_advisory";
					break;
			}
			//发送通知
			if($getData['type'] == 'voucher' || $getData['type'] == 'buygood') {
				$param = array("message_type"=>"advisory",
								"charter_user_id"=>$userInfo['user_id'],
						   		"charter_member"=>$userInfo['user_name'],
								"charter_member_avator"=>$userInfo['Avatar50'],
						);
				$this->_model->addPreNotice($getData['type'], $openType, $getData['frid'],$param);
			}
			//<<
			*/
			exit(json_encode($this->returnArr(1, array('tid' => $addNewPersonalMessageResult))));
		}
	}
	/**
	 * 追加留言
	 * 参数：frid, tid, type[good：商品 | brand：品牌 | shop：店铺 | market：商场 | voucher：现金券 | buygood：团购商品 | commodity: 商城商品], 
	 * 		 uuid, uname, time, ssid, qst
	 */
	public function appendPersonalMessageAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '商品 frid 不能为空')));
		}
		//留言TID
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言 tid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//留言内容
		if(!$getData['qst']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言内容，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		
		$appendPersonalMessageResult = $this->_model->appendPersonalMessage($getData, $userInfo);
		if($appendPersonalMessageResult) {
			exit(json_encode($this->returnArr(1, array())));
		} else {
			exit(json_encode($this->returnArr(0, array(), 300, '追加留言失败')));
		}
	}
	/**
	 * 回复留言
	 * 参数：frid, tid, type[good：商品 | brand：品牌 | shop：店铺 | market：商场 | voucher：现金券 | buygood：团购商品 | commodity: 商城商品],
	 * 		 uuid, uname, time, ssid, qst
	 */
	public function replyPersonalMessageAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '商品 frid 不能为空')));
		}
		//留言TID
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言 tid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//留言内容
		if(!$getData['qst']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言内容，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid'], true);
		
		$replyPersonalMessageResult = $this->_model->replyPersonalMessage($getData, $userInfo);
		if($replyPersonalMessageResult) {
			exit(json_encode($this->returnArr(1, array())));
		}
	}
	/**
	 * 认证商户、营业员 问答列表
	 * 参数：frid, type[good：商品 | brand：品牌 | shop：店铺 | market：商场 | voucher：现金券 | buygood：团购商品],
	 * 		 rtype [0： 全部 | 1：未回复  2： 已回复 ]
	 * 		 uuid, uname, time, ssid
	 */
	public function clerkNoticeListAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//现金券ID，商品ID
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '商品 frid 不能为空')));
		}
		//留言类型
		if(!$getData['type']) {
			exit(json_encode($this->returnArr(0, array(), 300, '留言类型，不能为空')));
		}
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		
		$clerkNoticeList = $this->_model->getClerkNoticeList($getData, $userInfo);
		exit(json_encode($this->returnArr(1, $clerkNoticeList)));
	}
	/**
	 * 个人信息列表
	 * 参数：uuid, uname, time, ssid
	 */
	public function myPersonalMessageAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);

		$myPersionalMessage = $this->_model->getMyPersonalMessage($getData, $userInfo);
		
		exit(json_encode($this->returnArr(1, $myPersionalMessage)));
	}
	
	/**
	 * 个人未读信息数
	 * 参数：uuid, uname, time, ssid
	 */
	public function myPersionUnreadMessageNumAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		
		//更新预放的消息
		$this->_model->updateUserPreNotice($userInfo["user_id"] , $this->_city);
		
		//$myPersionUnReadMessageNum = $this->_model->getMyPersionUnReadMessageNum($userInfo);
		$privateletterNum = $this->_model->getMyPersionUnReadMessageNumByType($userInfo,2,$this->_city);
		$noticeNum = $this->_model->getMyPersionUnReadMessageNumByType($userInfo,3,$this->_city);
		$myPersionUnReadMessageNum =  $privateletterNum + $noticeNum;
		$fans_number = Model_Api_User::getInstance()->getFansNum($userInfo["user_id"]);
		$concerned_user_number = Model_Api_User::getInstance()->getConcenedUsersNum($userInfo["user_id"]);
		exit(json_encode($this->returnArr(1, array('num' => strval($myPersionUnReadMessageNum), "fans_number"=>$fans_number, "concerned_user_number"=>$concerned_user_number))));
	}
	
	/**
	 * 个人未读信息数
	 * 参数：uuid, uname, time, ssid
	 */
	public function myPersonalUnreadMessageNumTwoAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		
		//更新预放的消息
		$this->_model->updateUserPreNotice($userInfo["user_id"] , $this->_city);
		
		$data = array();
		//活动券未读通知数
		$data["activity_num"] = $this->_model->getMyPersionUnReadMessageNumByType($userInfo,1,$this->_city);
		//私信未读通知数
		$data["privateletter_num"] = $this->_model->getMyPersionUnReadMessageNumByType($userInfo,2,$this->_city);
		//通知未读通知数
		$data["notice_num"] = $this->_model->getMyPersionUnReadMessageNumByType($userInfo,3,$this->_city);
		//粉丝数量
		$data["fans_number"] = Model_Api_User::getInstance()->getFansNum($userInfo["user_id"]);
		//关注人数
		$data["concerned_user_number"] = Model_Api_User::getInstance()->getConcenedUsersNum($userInfo["user_id"]);
		exit(json_encode($this->returnArr(1, $data)));
	}
	
	/**
	 * 个人信息列表
	 * 参数：uuid, uname, time, ssid
	 */
	public function myPersonalMessageTwoAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
	
		$myPersionalMessage = $this->_model->getMyPersionUnReadMessageListByType($getData, $userInfo , $this->_city);
		exit(json_encode($this->returnArr(1, $myPersionalMessage)));
	}
	/**
	 * 将消息置为已读(单个)
	 */
	public function readAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		$user_id = intval($userInfo['user_id']);
		$id = intval($getData["id"]);
		if( $id && $user_id ){
			$this->_model->updateReadValue($id,$user_id);
		}
		exit(json_encode($this->returnArr(1,array())));
	}
	
	/**
	 * 将指定类型下的消息置为已读
	 */
	public function readAllAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		//验证是否登录
		$this->isLogin($getData['uuid'], $getData['uname']);
		//用户身份详情
		$userInfo = $this->getWebUserId($getData['uuid']);
		$user_id = intval($userInfo['user_id']);
		$notice_type = intval($getData["notice-type"]);
		if( $user_id ){
			$this->_model->updateReadValue(0,$user_id,$notice_type);
		}
		exit(json_encode($this->returnArr(1,array())));
	}
	
	
	/**
	 * 券|团购 验证通知
	 * type [system]
	 * sid, uuid, message, order_no
	 */
	public function verifyNoticeAction() {
		$getData = $this->_http->getParams();
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/verifyNotice/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//传输加密验证
		$this->verify($getData);		
		
		//发送通知
		$userInfo = $this->getWebUserId($getData['uuid']);
		$this->_model->sendNotice(
							array('user_id'=>$userInfo['user_id'],
								  'type' => $getData['type'], 
								  'opentype'=>'home_order' ,
								  'notice_type' => 3,
								  'message' => $getData['message'], 
								  'order_no' => $getData['order_no'],
								  'city'=>$this->_city)
						);
		//<<
		_sexit('sucess', 100);
	}
	/**
	 * 券|团购 过期通知提示
	 * type [system]
	 * sid, uuid, message, order_no
	 */
	public function tipsExpiredAction() {
		$getData = $this->_http->getParams();
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/tipsExpired/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//传输加密验证
		$this->verify($getData);
		
		//发送通知
		$userInfo = $this->getWebUserId($getData['uuid']);
		$this->_model->sendNotice(
							array('user_id'=>$userInfo['user_id'],
								  'type' => $getData['type'], 
								  'opentype'=>'home_order' , 
								  'notice_type' => 3,
								  'message' => $getData['message'], 
								  'order_no' => $getData['order_no'],
								  'city'=>$this->_city)
						);
		//<<
		_sexit('sucess', 100);
	}
	
	/**
	 * 通知
	 */
	public function pushNoticeAction(){
		$getData = $this->_http->getParams();
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/'.$getData['opentype'].'/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		//传输加密验证
		$this->verify($getData);
		
		$userInfo = $this->getWebUserId($getData['uuid'], true);
		
		if( $getData['opentype'] == 'home_feedback' ){//反馈回复
			$notice_type = 2;
		}else if( $getData['opentype'] == 'home_order' ){//商品订单
			$notice_type = 3;
			$order_no = $getData["order_no"];
		}else{//钱包信息（'home_wallet_index'），新手包 （'home_starter_kit'）
			$notice_type = 3;
		}
		//发送通知
		$this->_model->sendNotice(
				array('user_id' 	=> 	$userInfo['user_id'],
					  'type' 		=> 	$getData['type'], 
					  'opentype'	=>	$getData['opentype'], 
					  'notice_type'	=> 	$notice_type,
					  'message' 	=> 	$getData['message'], 
					  'order_no' 	=> 	$getData['order_no'],
					  'city' 		=> 	$this->_city
				)
		);
		
		_sexit('sucess', 100);
	}
}