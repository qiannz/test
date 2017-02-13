<?php
class Model_Active_Wheel extends Base {
	
	private static $_instance;
	private static $_appConfig;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function appConfigAll(){
		if(!self::$_appConfig) {
			self::$_appConfig =  $this->_db->fetchAll("select * from oto_app_wheel_set");
		}
		return self::$_appConfig;
	}
	/**
	 * 中奖记录
	 * @param unknown_type $winRow
	 * @param unknown_type $userInfo
	 */
	public function luckyRecords(& $winRow, & $userInfo) {
		exit();
		if(empty($userInfo)) {
			return 0;	
		}
		
		if($winRow['type'] == 'star') {
			$param = array(
					'type' => $winRow['type'],
					'user_id' => $userInfo['user_id'],
					'award_name' => $winRow['name'],
					'number' => $winRow['number'],
					'mobile' => $userInfo['phone_number'],
					'day_date' => datex(REQUEST_TIME, 'Y-m-d'),
					'ip' => CLIENT_IP,
					'created' => REQUEST_TIME
			);
			
			$params = array(
						'type' => 'winn',
						'user_id' => $userInfo['user_id'],
						'number' => $winRow['number'],
						'is_winning' => 1,
						'award_name' => $winRow['name'],
						'ip' => CLIENT_IP,
						'created' => REQUEST_TIME
					);
			$insertId = $this->_db->insert('oto_app_wheel_log', $param);
			$this->_db->insert('oto_app_wheel_star_log', $params);
			return $insertId;
		} else {
			$param = array(
					'type' => $winRow['type'],
					'user_id' => $userInfo['user_id'],
					'award_name' => $winRow['name'],
					'number' => 1,
					'mobile' => $userInfo['phone_number'],
					'day_date' => datex(REQUEST_TIME, 'Y-m-d'),
					'ip' => CLIENT_IP,
					'created' => REQUEST_TIME
			);			
			$insertId = $this->_db->insert('oto_app_wheel_log', $param);
			//中奖短信
			$this->sendMessage($winRow, $userInfo['phone_number']);
			return $insertId;
		}		
	}
	/**
	 * 统一某一个奖品今日兑奖数量
	 * @param unknown_type $type
	 */
	public function getTodayWinningNumber($type) {
		$day_date = datex(REQUEST_TIME, 'Y-m-d');
		$sql = "select sum(number) from `oto_app_wheel_log` where `type` = '{$type}' and `day_date` = '{$day_date}'";
		$todayNum = $this->_db->fetchOne($sql);
		return $todayNum ? $todayNum : 0;
	}
	
	/**
	 * 统一某一个奖品共计兑奖数量
	 * @param unknown_type $type
	 */
	public function getTotalWinningNumber($type) {
		$sql = "select sum(number) from `oto_app_wheel_log` where `type` = '{$type}'";
		$todayNum = $this->_db->fetchOne($sql);
		return $todayNum;
	}
		
	/**
	 * 统计还有多少幸运星
	 * @param unknown_type $user_id
	 */
	public function statisticsLucky($user_id) {
		$sql = "select sum(number) from `oto_app_wheel_star_log` where `user_id` = '{$user_id}'";
		$starNumber = $this->_db->fetchOne($sql);
		$this->updateUserStar($starNumber, $user_id);
		return $starNumber;
	}
	/**
	 * 更新用户主表幸运星数量
	 * @param unknown_type $starNumber
	 * @param unknown_type $user_id
	 */
	public function updateUserStar($starNumber, $user_id) {
		$this->_db->update('oto_user', array('star' => $starNumber), array('user_id' => $user_id));
	}
	/**
	 * 花费一个幸运星
	 * @param unknown_type $user_id
	 */
	public function spendOneStar($user_id, $winRow = '') {		
		$params = array(
				'type' => 'sweep',
				'user_id' => $user_id,
				'number' => '-1',
				'ip' => CLIENT_IP,
				'created' => REQUEST_TIME
		);
				
		if(!$winRow) {
			$params['award_name'] = '谢谢参与';		
		} else {
			$params['award_name'] = $winRow['name'];
		}
			
		$insertId = $this->_db->insert('oto_app_wheel_star_log', $params);
		return $insertId;
	}
	
	public function updateUserCode($user_id, $code) {
		return $this->_db->update('oto_user', array('code' => $code), array('user_id' => $user_id));
	}
	
	public function updateUserMobile($user_id, $mobile) {
		return $this->_db->update('oto_user', array('phone_number' => $mobile, 'code' => ''), array('user_id' => $user_id));
	}
	
	public function getMyWheel($user_id, $page, $pagesize = PAGESIZE) {
		$key = 'get_my_wheel_' . $user_id . '_' . $page;
		$data = $this->getData($key);
		//if(empty($data)) {
			$sql = "select * from `oto_app_wheel_star_log` where `user_id` = '{$user_id}' order by `created` desc";
			$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
			foreach ($data as & $row) {
				$row['month_day'] = datex($row['created'], 'm/d');
				$row['hour_minute'] = datex($row['created'], 'H:i ');
				if($row['type'] == 'sweep') {
					$row['action'] = '抽奖';
				} elseif($row['type'] == 'winn') {
					$row['action'] = '中奖';
				} elseif($row['type'] == 'back') {
					$row['action'] = '后台';
				}			
				$row['winning'] = $row['award_name'];
			}			
			$this->setData($key, $data);
		//}
		return $data;
	}
	
	public function sendMessage(& $winRow, $mobile) {
		switch ($winRow['type']) {
			case 'virtual':
			case 'real':
			case 'call':
				$message = $winRow['msg'];
				$code = Custom_Common::random(4);
				$resultMes = Custom_Send::sendMobileMessage($mobile,$message);
				//中奖短信日志
				$fileName = date('Ymd'). '.log';
				$logPath = LOG_PATH . 'wheel/' . date('Y') . '/' .date('m') . '/';
				logLog($fileName, var_export($winRow, true).var_export($resultMes, true), $logPath);
				break;
		}
	}
}