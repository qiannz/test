<?php
class Controller_Active_Voucher extends Base {
	
	private $_model;
	
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Voucher::getInstance();
		$this->_tpl->assign('title', '我的红包分你一份');
		$this->_tpl->assign('version', '20151022');
	}
		
	public function shareAction() {
		$output = array();
		$msg = $this->_http->get('msg');
		Third_Des::$key = '34npzntC';
		$http_build_query_string = Third_Des::decrypt($msg);	
		parse_str($http_build_query_string, $output);
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'voucher/share/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, $msg . ' | ' . var_export($output, true), $logPath);
		
		if(empty($output) || empty($output['uuid']) || empty($output['order_no'])) {
			exit(json_encode($this->returnArr(0, '', 300, '参数错误')));
		}
		
		$userInfo = $this->getWebUserId($output['uuid']);
		if(!$userInfo) {
			exit(json_encode($this->returnArr(0, '', 300, '用户错误')));
		}
		$this->_tpl->assign('userInfo', $userInfo);
		$this->_tpl->assign('output', $output);
		$this->_tpl->display('active/voucher/index.php');
	}
	
	//发送验证码
	public function sendCodeAction() {
		$user_id = $this->_http->getPost('uid');
		$mobile = $this->_http->getPost('mobile');
		$order_no = $this->_http->getPost('order_no');
		
		if(!preg_match('/^1[2-9][0-9]{9}$/', $mobile)) {
			_exit('fail', 101);
		}
		
		//获取参与用户信息
		$mobileRow = $this->_model->getVoucherShareUserInfo($user_id, $mobile, $order_no);
		
		if(empty($mobileRow)) {
			//获取已分享成功的红包数
			$shareNum = $this->_model->getVoucherHadShareNum($user_id, $order_no);
			if($shareNum >= 8 ) {
				_exit('fail', 102); //已领完
			}
		}
		
		$code = Custom_Common::random(4);
		$content = "您的验证码为：" .$code;
		$resultMes = Custom_Send::sendMobileMessage($mobile, $content);
		if($resultMes['SendSmsResult'] == 1) {
			if(!empty($mobileRow)) {
				$result = $this->_model->updateCode($mobileRow['share_id'], $code);
			} else {
				$result = $this->_model->insertCode($user_id, $mobile, $order_no, $code);
			}
			
			if($result){
				_exit('success', 100);
			}			
		}
	}
	
	public function verifyMobileCodeAction() {
		$user_id = $this->_http->getPost('uid');
		$mobile = $this->_http->getPost('mobile');
		$order_no = $this->_http->getPost('order_no');
		$code   = $this->_http->getPost('code');
		
		if(!$mobile || !preg_match( '/^1[2-9][0-9]{9}$/' , $mobile )){
			_exit('fail', 101); //手机错误
		}
	
		if(!$code){
			_exit('fail', 102); //验证码不存在
		}
		
		$mobileRow = $this->_model->getVoucherShareUserInfo($user_id, $mobile, $order_no);
		if(!empty($mobileRow)) {
			if($mobileRow['code'] == $code) {
				$this->_model->updateCode($mobileRow['share_id']);
				if($mobileRow['had_received'] == 0) {
					//获取已分享成功的红包数
					$shareNum = $this->_model->getVoucherHadShareNum($user_id, $order_no);
					if($shareNum >= 8 ) {
						_exit('fail', 105); //已领完
					}
					
					$winId = $this->won();
					//5元券
					if($winId == 1) {
						$award = 5;
						$CouponType = 1;
					} 
					//10元券
					elseif ($winId == 2) {
						$award = 10;
						$CouponType = 2;
					} 
					//20元券
					elseif ($winId == 3) {
						$award = 20;
						$CouponType = 3;
					}

					$param = array(
									'AppName' => 'buy20151019', //请求应用名称
									'AppRemark' => '买现金券送红包', //请求应用描述
									'CouponType' => $CouponType,
									'Mobile' => $mobileRow['mobile'],
									'SetId' => '176399DE-9A31-4506-A99F-B258F1038B8E'
								);
					$resultAddCoupon = Custom_AuthTicket::tempAddCoupon($param);
					if($resultAddCoupon['code'] == 1) {
						$mobileRow['award'] = $award;
						$this->_model->updateSendStatus($mobileRow['share_id'], $award);
						//同步注册用户
						//Custom_AuthLogin::get_user_by_mobile($mobileRow['mobile']);
					} else {
						_exit('红包领取失败，请稍后再试', 106); //红包领取失败
					}
				}
				
				$mobileRow['awardInfoList'] = $this->_model->getVoucherOtherShareRecord($user_id);
				$this->_tpl->assign('mobileRow', $mobileRow);
				$outHtml = $this->_tpl->fetch('active/voucher/home.php');
				_exit('sucess', 100, $outHtml);
			} else {
				_exit('fail', 104); //验证码错误
			}
		} else {
			_exit('fail', 103); //不存在
		}
	}

	private function won() {
		$prize_arr = array(
				array('id' => 1, 	'name' => '5元券', 		'v' => 70),
				array('id' => 2, 	'name' => '10元券' , 	'v' => 20),
				array('id' => 3, 	'name' => '20元券' , 	'v' => 10),
		);
	
		foreach ($prize_arr as $key => $val) {
			$arr[$val['id']] = $val['v'];
		}
	
		$rid = get_rand($arr); //根据概率获取奖项id
	
		$rs = $prize_arr[$rid-1];
		return $rs['id'];
	}
}

function get_rand($proArr) {
	$result = '';

	//概率数组的总概率精度
	$proSum = array_sum($proArr);

	//概率数组循环
	foreach ($proArr as $key => $proCur) {
		$randNum = mt_rand(1, $proSum);
		if ($randNum <= $proCur) {
			$result = $key;
			break;
		} else {
			$proSum -= $proCur;
		}
	}
	unset ($proArr);

	return $result;
}