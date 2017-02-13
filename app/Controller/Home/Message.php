<?php
class Controller_Home_Message extends Controller_Home_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Message::getInstance();
	}

	public function logoutAction() {
		cookie('API_CLIENT_SHOP_ID', '', 1, '/home/message/');
		cookie('API_CLIENT_USER_ID', '', 1, '/home/message/');
		Custom_Common::jumpto('/home/message/login');
	}
	
	public function veriyMobileAction() {
		$getData = $this->_http->getParams();
	 
		$mobile = $getData['mobile'];
		if(!preg_match('/^1[2-9][0-9]{9}$/', $mobile)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请输入正确的手机号码')));
		}
		 
		$code = $getData['code'];
		if(!$code){
			exit(json_encode($this->returnArr(0, array(), 300, '请输入验证码')));
		}
	
		$mianUserInfoRow = Custom_AuthLogin::get_user_by_mobile($mobile);
		if($mianUserInfoRow['GetUserInfosResult'] == 1) {
			$mobileRow = $this->getUserByUuid($mianUserInfoRow['userInfo']['UserId']);
			$shop_commodity_user_info = $this->select("`user_id` = '{$mobileRow['user_id']}'", 'oto_user_shop_commodity', '*', '', true);
			$shop_commodity_user_info['shop_name'] = $this->getShopFieldById($shop_commodity_user_info['shop_id'], 'shop_name');
			if(!empty($shop_commodity_user_info)) {
				if($mobileRow['code'] == $code) {
					//清空验证码
					$this->_db->update('oto_user', array('code' => ''), array('user_id' => $mobileRow['user_id']));
					//返回登录结果
					exit(json_encode($this->returnArr(1, $shop_commodity_user_info)));
				} else {
					exit(json_encode($this->returnArr(0, array(), 300, '验证码错误')));
				}
			} else {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不是店员')));
			}
		} else {
			exit(json_encode($this->returnArr(0, array(), 300, '手机用户不存在')));
		}
	}
	/**
	 * 发送验证码
	 */
	public function sendCodeAction() {
		$getData = $this->_http->getParams();
	 
		$mobile = $getData['mobile'];
		if(!preg_match('/^1[2-9][0-9]{9}$/', $mobile)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请输入正确的手机号码')));
		}
	
		$mianUserInfoRow = Custom_AuthLogin::get_user_by_mobile($mobile);
	
		if($mianUserInfoRow['GetUserInfosResult'] == 1) {
			$mobileRow = $this->getUserByUuid($mianUserInfoRow['userInfo']['UserId']);
			$shop_commodity_user_info = $this->select("`user_id` = '{$mobileRow['user_id']}'", 'oto_user_shop_commodity', '*', '', true);
			if(!empty($shop_commodity_user_info)) {
				$code = Custom_Common::random(4);
				$content = "您的验证码为：" .$code;
				$resultMes = Custom_Send::sendMobileMessage($mobile,$content);
				if($resultMes['SendSmsResult'] == 1){
					$this->_db->update('oto_user', array('code' => $code), array('user_id' => $mobileRow['user_id']));
					exit(json_encode($this->returnArr(1, array())));
				} else {
					exit(json_encode($this->returnArr(0, array(), 300, '验证码发送失败')));
				}
			} else {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不是店员')));
			}
		} else {
			exit(json_encode($this->returnArr(0, array(), 300, '手机用户不存在')));
		}
	}	
	public function loginAction() {
		$this->_tpl->display('wap/client/login.php');
	}
	
	public function threadAction() {
		if(!isset($_COOKIE['API_CLIENT_USER_ID']) || empty($_COOKIE['API_CLIENT_USER_ID'])) {
			Custom_Common::jumpto('/home/message/login');
		}
		
		$userInfo = $this->getUserByUserId(intval($_COOKIE['API_CLIENT_USER_ID']));
		if(empty($userInfo)) {
			Custom_Common::jumpto('/home/message/login');
		}
		
		
		$getData = array(
				'sid' => intval($_COOKIE['API_CLIENT_SHOP_ID']),
				'uid' => intval($_COOKIE['API_CLIENT_USER_ID'])
		);
		$data = Model_Api_App::getInstance()->getInfoList($getData, $this->_city);
				
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('wap/client/thread.php');
	}
	
	public function postAction() {
		if(!isset($_COOKIE['API_CLIENT_USER_ID']) || empty($_COOKIE['API_CLIENT_USER_ID'])) {
			Custom_Common::jumpto('/home/message/login');
		}
		
		$userInfo = $this->getUserByUserId(intval($_COOKIE['API_CLIENT_USER_ID']));
		if(empty($userInfo)) {
			Custom_Common::jumpto('/home/message/login');
		}

		$tid = $this->_http->get('tid');
		$frid = $this->_http->get('frid');
		$getData = array(
				'sid' => intval($_COOKIE['API_CLIENT_SHOP_ID']),
				'uid' => intval($_COOKIE['API_CLIENT_USER_ID']),
				'tid' => $tid,
				'frid' => $frid
		);
		
		$data = Model_Api_App::getInstance()->getInfoShow($getData, $this->_city);
		
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('uid', intval($_COOKIE['API_CLIENT_USER_ID']));
		$this->_tpl->assign('sid', intval($_COOKIE['API_CLIENT_SHOP_ID']));
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->assign('frid', $frid);
		$this->_tpl->display('wap/client/post.php');
	}
	
	public function infoReplyAction() {
		$getData = $this->_http->getParams();

	
		if(!$getData['uid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
		}
		 
		if(!$getData['sid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
		}
	
		if(!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '咨询ID不能为空')));
		}
	
		if(!$getData['frid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
		}
		 
		if(!$getData['qst']) {
			exit(json_encode($this->returnArr(0, array(), 300, '回复内容不能为空')));
		}
		 
		$userRow = $this->getUserByUserId($getData['uid'], 'user_id, uuid, user_name, user_type');
		 
		$userInfo = $this->getWebUserId($userRow['uuid']);
		 
		$getData['type'] = 'commodity';
		$result = Model_Api_Message::getInstance()->replyPersonalMessage($getData, $userInfo);
	
		if($result) {
			exit(json_encode($this->returnArr(1, array())));
		} else {
			exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
		}
	}	
}

