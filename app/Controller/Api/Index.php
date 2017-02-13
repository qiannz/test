<?php
class Controller_Api_Index extends Controller_Api_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Index::getInstance();
	}
	
	protected function auth($getData) {
		
		$SignData = $getData['SignData'];
		unset($getData['m']);
		unset($getData['c']);
		unset($getData['act']);
		unset($getData['api']);
		unset($getData['SignData']);
		
		$param = array();
		
		foreach($getData as $key => $value) {
			$param[strtolower($key)] = $value;
		}
		ksort($param);
		
		//$stringA = http_build_query($param);
		$stringA = '';
		foreach ($param as $k => $v) {
			$stringA .= "{$k}=".urldecode($v)."&";
		}
		$stringA = substr($stringA, 0, -1);
				
		$stringSignTemp = $stringA . '&key=8F0EC841B756454CB09C705E96BF6776';
		$stringSignTempMd5 = strtoupper(md5($stringSignTemp));
		
		//logLog('rebateAuthSync.log', $stringSignTemp . var_export($param, true) . ' | ' . $stringSignTempMd5 . ' | ' . $SignData);
		
		if($stringSignTempMd5 != $SignData) {
			_sexit('fail', 500, '验证失败');
		}
		
	}
	
	/**
	 * 营业员返利
	 */
	public function clerkRebatesAction() {
		
		//推荐用户 uid
		$cguid = $this->_http->get('cguid');
		//购买用户 uid
		$uguid = $this->_http->get('uguid');
		//券ID
		$ticket_id = $this->_http->get('ticket_id');
		//券UID
		$ticket_uuid = $this->_http->get('ticket_uuid');
		//店铺ID
		$shop_id = $this->_http->get('shop_id');
		//验证码
		$captcha = $this->_http->get('captcha');
		//用户IP
		$ip = $this->_http->get('ip');
		//订单号
		$OrderNo = $this->_http->get('OrderNo');
		
		$getData = $this->_http->getParams();
		
		//加密验证
		$this->auth($getData);
		
		//返利日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'rebate/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);

		
		//返利开关
		$configArray = @include VAR_PATH . 'config/config.php';
		$power = $configArray['TASK_CLERK_REBATES'];
		if(empty($power)) {
			_sexit('fail', 302, '返利关闭了');
		}
		
		//自己买，不能返利
		if($cguid && $cguid == $uguid) {
			_sexit('fail', 301.1, '自己买，不能返利');
		}
		
		//APP自动返利[TO店长]
		$resultArray = $this->_model->startSelfPayRebates($getData);
		_sexit($resultArray['msg'], $resultArray['res'], $resultArray['extra'] ? $resultArray['extra'] : array());
		/*
		$userClerkRow = $this->getUserByUuid($cguid);
		$userClientRow = $this->getUserByUuid($uguid);
		
		//券明细
		$ticketRow = $this->_model->getTicktRow($ticket_id, $ticket_uuid);
		$ticketRow['OrderPrice'] = $getData['OrderPrice']; //加入用户自定义价格
		//返利金额判断
		if(!$ticketRow['rebates']) {
			_sexit('fail', 303.1, '返利金额为零');
		}

		//如果营业员用户在名品街不存在，则新建一个用户
		if(empty($userClerkRow) && $cguid != '00000000-0000-0000-0000-000000000000') {
			$userClerkRow = $this->_model->newUserByUuid($cguid);
		}
			
		if(empty($userClerkRow)) {
			_sexit('fail', 301.2, '新用户创建失败');
		}
					
		//返利执行
		$resultArray = $this->_model->startedRebates($userClerkRow, $userClientRow, $ticketRow, $ticket_id, $shop_id, $captcha, $ip, $OrderNo);
		_sexit($resultArray['msg'], $resultArray['res'], $resultArray['extra']);
		*/
	}
	
	public function syncTicketAction() {
		$errMsg = $getData = array();
		
		$getData = $this->_http->getParams();
		
		//加密验证
		$this->auth($getData);
		
		//同步日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'sync/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
				
		//名品街券ID
		$tid = $this->_http->get('tid');
		if(!$tid) {
			$errMsg[] = '名品街券ID为空';
		}
		//超级购券ID
		$guid = $this->_http->get('guid');
		if(!$guid) {
			$errMsg[] = '超级购券ID为空';
		}
		//券标题
		$name = $this->_http->get('name');
		if(!$name) {
			$errMsg[] = '券标题为空';
		}
		//券简介
		$remark = $this->_http->get('remark');
		if(!$remark) {
			$errMsg[] = '券简介为空';
		}
		//售价
		$price = $this->_http->get('price');
		if(empty($getData['CanCustomPrice']) && !$price) {
			$errMsg[] = '券售价为空';
		}
		//原价（面值）
		$originalPrice = $this->_http->get('originalPrice');
		//APP售价
		$appPrice = $this->_http->get('appPrice');
		//券销售开始时间 (2014-06-30 11:11:11)
		$startDate = $this->_http->get('startDate');
		//券销售结束时间(2014-07-05 11:11:11)
		$endDate = $this->_http->get('endDate');
		if(!$startDate || !$endDate) {
			$errMsg[] = '券销售时间为空';
		}
		//券使用开始时间(2014-06-30 11:11:11)
		$useStartDate = $this->_http->get('useStartDate');
		//券使用结束时间(2014-07-07 11:11:11)
		$useEndDate = $this->_http->get('useEndDate');
		if(!$useStartDate || !$useEndDate) {
			$errMsg[] = '券使用时间为空';
		}
		//券总量
		$amount = $this->_http->get('amount');
		if(!$amount) {
			$errMsg[] = '券总量为空';
		}
		//限购数量
		$limitCount = $this->_http->get('limitCount');

		//限购周期('Activity','Hour','Day','Week','Weekly','month','Monthly', 'Minutes')
		$limitUnit = $this->_http->get('limitUnit');
		
		switch ($limitUnit) {
			case 0:
				$getData['limitUnit'] = 'Activity';
				break;
			case 1:
				$getData['limitUnit'] = 'Hour';
				break;
			case 2:
				$getData['limitUnit'] = 'Day';
				break;
			case 3:
				$getData['limitUnit'] = 'Week';
				break;
			case 4:
				$getData['limitUnit'] = 'Weekly';
				break;
			case 5:
				$getData['limitUnit'] = 'Month';
				break;
			case 6:
				$getData['limitUnit'] = 'Monthly';
				break;
			case 7:
				$getData['limitUnit'] = 'Minutes';
				break;											
		}
		
		//场次编号
		$code = $this->_http->get('code');		
		
		if(!empty($errMsg)) {
			_sexit('fail', 300, implode(',', $errMsg));
		}
		//判断券是否存在
		if(!$this->_model->existCoupon($tid, $guid)) {
			_sexit('fail', 301, '券不存在');
		}
				
		$syncTicketResult = $this->_model->syncTicket($getData);
		Model_Admin_Ticket::getInstance()->updateTicketPreNotice($tid,"voucher","voucher_view");
		if($syncTicketResult) {
			_sexit('success', 100, '同步成功');
		} else {
			_sexit('fail', 400, 'sql 错误');
		}
	}
	/**
	 * APP问卷调查
	 */
	public function testAction() {
		
		$inquireArray = Model_Home_Wap::getInstance()->getCurrentInquireByType('app');
		
		$sql = "select * from `oto_survey_items`";
		$result = array();
		$resultArray = $this->_db->fetchAll($sql);
		require_once ROOT_PATH . 'lib/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->setTitle('调查结果');
		
		$char = array(
					A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,G,U,V,W,S,Y,Z
				);
		foreach($inquireArray as $key => $item) {
			$objPHPExcel->getActiveSheet()->setCellValue($char[$key-1].'1', $item['title']);
		}
		
		$i = 2;
		foreach($resultArray as $item) {
			$itemArray  = unserialize($item['data']);
			foreach ($itemArray as $key => $items) {
				$objPHPExcel->getActiveSheet()->setCellValue($char[$key].$i, $items['value']);
			}
			$i++;
		}
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=调查结果.xls');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		
	}
	/**
	 * IOS 和 ANDROID 版本控制
	 */
	public function judgeVersionAction() {
		$configArray = @include VAR_PATH . 'config/config.php';
		$type = $this->_http->has('type') ? strval($this->_http->get('type')) : 'ios';
		if($type == 'ios') {
			exit(json_encode($this->returnArr(1, $configArray['VERSION_CONTROL_IOS'])));
		} elseif($type == 'android') {
			$param = array(
						'version' => $configArray['VERSION_CONTROL_ANDROID'],
						'channel' => $configArray['CHANNEL_CONTROL_ANDROID']
					);
			exit(json_encode($this->returnArr(1, $param)));
		}
	}
	/**
	 * 获取抵用券列表
	 */
	public function getVoucherListAction() {
		$getData = $this->_http->getParams();
		//获取抵用券日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'getVoucherList/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		$this->authAll($getData);
		
		if(!$getData['uuid'] && !$getData['mobile']) {
			exit(json_encode($this->returnArr(0, array(), 300, '用户ID和手机号码不能同时为空')));
		}

		$mobile = $getData['mobile'];
		$uuid = $getData['uuid'];
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$pagesize = !$getData['pagesize'] ? PAGESIZE : intval($getData['pagesize']);
		$status = !$getData['status'] ? -999 : $getData['status'];
		$type = !$getData['type'] || !in_array($getData['type'], array(1,2)) ? 0 : $getData['type'];
		
		$resultArr = Custom_AuthTicket::getVoucherList($mobile, $uuid, $status, $type, $page, $pagesize);

		if($resultArr['code'] == 1) {
			exit(json_encode($this->returnArr(1, $resultArr['message'])));
		} else {
			$logPath = LOG_PATH . 'getvoucherlist/error/' . date('Y') . '/' .date('m') . '/';
			logLog($fileName, var_export($resultArr, true), $logPath);
			exit(json_encode($this->returnArr(0, array(), 300, '获取抵用券列表失败')));
		}
	}
	/**
	 * 绑定抵用券
	 */
	public function bindVoucherAction() {
		$getData = $this->_http->getParams();
		//绑定抵用券日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'bindVoucher/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($getData, true), $logPath);
		
		$this->authAll($getData);
		
		if(!$getData['uuid'] && !$getData['mobile']) {
			exit(json_encode($this->returnArr(0, array(), 300, '用户ID和手机号码不能同时为空')));
		}
		
		$mobile = $getData['mobile'];
		$uuid = $getData['uuid'];
		$code = $getData['code'];
		$version = $getData['version'];
		
		$resultArr = Custom_AuthTicket::bindVoucher($mobile, $uuid, $code, $version);
		if($resultArr['code'] == 1) {
			exit(json_encode($this->returnArr(1)));
		} else {
			$logPath = LOG_PATH . 'bindVoucher/error/' . date('Y') . '/' .date('m') . '/';
			logLog($fileName, var_export($resultArr, true), $logPath);
			exit(json_encode($this->returnArr(0, array(), 300, '绑定失败')));
		}
	}
	
	/*
	 * 推送通知
	 */
	public function noticeAction() {
		set_time_limit(300);
		$getData = $this->_http->getParams();
		if ($this->_http->has("key")) {
			if($this->_http->get("key") != "SogsXdA3kzhvYgw5x0aFSWbbqoFTbeV3") {
				_sexit('fail', 500, 'Linux验证失败');
			}
			$logPath = LOG_PATH . 'notice/linux/' . date('Y') . '/' .date('m') . '/';
		} else {
			//加密验证
			$this->auth($getData);
			$logPath = LOG_PATH . 'notice/' . date('Y') . '/' .date('m') . '/';
		}
		
		//通知调用日志
		logLog(date('Ymd'). '.log', var_export($getData, true),  $logPath);
		
		//推送折扣通知
		$this->_model->discountNotice();
		
		//一元活动通知 OR 秒杀活动通知
		$this->_model->activityNotice();
		
		//群发通知推送
		$this->_model->sendPreNotice();
		
		_sexit('success', 100, '推送成功');
	}
}