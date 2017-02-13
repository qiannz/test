<?php
class Controller_Api_Gift extends Base {
	
	private $_model;
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Gift::getInstance();	
	}

	private function auth($getData) {
		$encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['Auth_Key'];
	
		if (empty($getData['ssid'])) {
			echo json_encode($this->returnArr(0, '', 400, '请下载最新版本！'));
			exit();
		}
	
		if ($getData['ssid'] != md5($encryptString)) {
			echo json_encode($this->returnArr(0, '', 300, '验证失败，请下载最新版本！'));
			exit();
		}
	}
	
	private function authVersionTwo($getData) {
		$encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
		
		if (empty($getData['ssid'])) {
			echo json_encode($this->returnArr(0, '', 400, '请下载最新版本！'));
			exit();
		}
		
		Third_Des::$key = '34npzntC';
		if ($getData['ssid'] != Third_Des::encrypt($encryptString)) {
			echo json_encode($this->returnArr(0, '', 300, '验证失败，请下载最新版本！'));
			exit();
		}
	}
	
	/**
	 * 绑定新手包 V 1.0
	 */
	public function bindAction() {
		$parseUrl = parse_url($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		if ($_SERVER['HTTP_HOST'] != $parseUrl['host']) {
			exit('404.4!');
		}
		$getData = $this->_http->getParams();
		//验证
		$this->auth($getData);
		
		
		
		$errMsg = array();
		//判断手机号码
		if(!$getData['mobile']) {
			$errMsg[] = '手机号码不能为空';
		}
		
		if($getData['mobile'] && !preg_match('/^1[3|4|5|6|7|8|9][0-9]{9}$/', $getData['mobile'])) {
			$errMsg[] = '请输入正确的手机号码';
		}
		//判断验证码
		if(empty($getData['captcha'])) {
			$errMsg[] = '验证码不能为空';
		}
		
		if($getData['captcha'] && !preg_match('/^[a-zA-Z][a-z0-9A-Z]+$/', $getData['captcha'])) {
			$errMsg[] = '请输入正确的验证码';
		}
		
		if($getData['type'] && !in_array($getData['type'], array(1,2))) {
			$errMsg[] = '请选择正确的奖品';
		}
		
		//判断手机唯一KEY
		if(empty($getData['key'])) {
			$errMsg[] = '手机唯一标记不能为空';
		}
		
		if(!empty($errMsg)) {
			echo json_encode($this->returnArr(0, '', 99, implode('|', $errMsg)));
			exit();
		}
		
		echo json_encode($this->returnArr(0, '', 100, '绑定成功'));
		exit();
	}
	
	/**
	 * 绑定新手包 V 2.0
	 */
	public function bindVersionTwoAction() {
		$parseUrl = parse_url($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		if ($_SERVER['HTTP_HOST'] != $parseUrl['host']) {
			exit('404.4!');
		}
		
		$getData = $this->_http->getParams();
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'gift/bind/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		//验证
		$this->authVersionTwo($getData);
		
		/*
		//设备验证
		if(isset($getData['imei']) && !empty($getData['imei'])) {
			if(!$this->_model->deviceAndRoidIsPresent($getData['imei'])) {
				echo json_encode($this->returnArr(0, '', 101, '绑定失败'));
				exit();
			}			
		} else {
			if(!$this->_model->deviceIosIsPresent($getData['mac'], $getData['idfa'], $getData['idfv'])) {
				echo json_encode($this->returnArr(0, '', 101, '绑定失败'));
				exit();
			}		
		}
		*/
		
		$errMsg = array();
		//判断手机号码
		if(!$getData['mobile']) {
			$errMsg[] = '手机号码不能为空';
		}
	
		if($getData['mobile'] && !preg_match('/^1[3-9][0-9]{9}$/', $getData['mobile'])) {
			$errMsg[] = '请输入正确的手机号码';
		}
		//判断验证码
		if(empty($getData['captcha'])) {
			$errMsg[] = '验证码不能为空';
		}
	
		if($getData['captcha'] && !preg_match('/^[a-zA-Z][a-z0-9A-Z]+$/', $getData['captcha'])) {
			$errMsg[] = '请输入正确的验证码';
		}
	
		/*
		if($getData['type'] && !in_array($getData['type'], array(1,2))) {
			$errMsg[] = '请选择正确的奖品';
		}
		*/
	
		if(isset($getData['imei'])) {
			//判断手机唯imei (ANDROID)
			if(empty($getData['imei'])) {
				$errMsg[] = '手机唯一标记不能为空';
			}				
		} else {
			//判断手机唯一KEY (IOS)
			if(empty($getData['key'])) {
				$errMsg[] = '手机唯一标记不能为空';
			}
		}
	
		if(!empty($errMsg)) {
			echo json_encode($this->returnArr(0, '', 99, implode('|', $errMsg)));
			exit();
		}
	
		$bindMsg = $this->_model->bindGift($getData);
		if(isset($bindMsg['extra'])) {
			echo json_encode($this->returnArr(0, $bindMsg['extra'], $bindMsg['res'], $bindMsg['msg']));
			exit();
		} else {
			echo json_encode($this->returnArr(0, '', $bindMsg['res'], $bindMsg['msg']));
			exit();
		}
	}	
	/**
	 * 验证活动是否存在
	 */
	public function queryAwardAction() {
		$getData = $this->_http->getParams();
		//判断验证码
		if(!$getData['captcha']) {
			echo json_encode($this->returnArr(0, '', 300, '验证码不能为空'));
			exit();
		}
		
		$giftRow = $this->_model->getBindGiftRow($getData['captcha']);
		
		if(empty($giftRow)) {
			echo json_encode($this->returnArr(0, '', 300, '该活动不存在'));
			exit();
		}
		
		if(!empty($giftRow) && $giftRow['is_enable'] == 0) {
			echo json_encode($this->returnArr(0, '', 300, '该活动已经结束'));
			exit();
		}
		
		echo json_encode($this->returnArr(1, '', 100, '该活动正常'));
		exit();		
	}
	/**
	 * 根据手机KEY 查询 IOS 是否已绑定新手包
	 */
	public function queryBindAction() {
		$getData = $this->_http->getParams();
		if(!empty($getData)) {
			$giftRow = $this->_model->getBindGiftRecordRow($getData);
			if(empty($giftRow)) {
				echo json_encode($this->returnArr(0, '', 300 , '查询失败，记录不存在'));
				exit();
			} else {
				echo json_encode($this->returnArr(0, $giftRow, 100 , '查询成功'));
				exit();
			}
		}
		echo json_encode($this->returnArr(0, '', 301 , '参数值丢失'));
		exit();
	}

	/**
	 * 根据imei 查询 android 是否已绑定新手包
	 */
	public function queryAndBindAction() {
		$imei = $this->_http->get('imei');
		if($imei) {
			$giftRow = $this->_model->getAndRoidBindGiftRecordRow($imei);
			if(empty($giftRow)) {
				echo json_encode($this->returnArr(0, '', 300 , '查询失败，记录不存在'));
				exit();
			} else {
				echo json_encode($this->returnArr(0, array($giftRow), 100 , '查询成功'));
				exit();
			}
		}
		echo json_encode($this->returnArr(0, '', 301 , 'imei值丢失'));
		exit();
	}	
	
	/**
	 * 奖品内容
	 */
	public function getGiftAction() {
		$giftData = $this->_model->getGiftData();
		echo json_encode($this->returnArr(count($giftData),$giftData));
		exit();
	}
	
	public function getGiftTestAction() {
		$giftData = $this->_db->fetchAll("select id, prize_name, prize_content from oto_app_welcome_prize");
		echo json_encode($this->returnArr(count($giftData),$giftData));
		exit();
	}
}