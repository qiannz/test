<?php
class Model_Api_Gift extends Base
{
	private static $_instance;
	private $_table = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function bindGift($getData) {
		$msgArray = array();
		
		if(isset($getData['imei'])) {
			$bindRow = $this->getBindGiftRecordRow($getData['imei']);
			if (!empty($bindRow)) {
				$msgArray = array(
						'msg' => '当前手机已绑定过新手包',
						'res' => 101,
						'extra' => $bindRow
				);
				return $msgArray;
			}	
			$type = 'android';
			$tokenkey = $getData['imei'];					
		} else {
			//手机KEY重复
			$bindRow = $this->getBindGiftRecordRow($getData['key']);
			if (!empty($bindRow)) {
				$msgArray = array(
							'msg' => '当前手机已绑定过新手包',
							'res' => 101,
							'extra' => $bindRow
						);
				return $msgArray;
			}
			$type = 'ios';
			$tokenkey = $getData['key'];
			if($tokenkey == '(null)') {
				$tokenkey = $getData['idfa'];
			}		
		}
		
		//手机号码重复
		if($this->isMobileBind($getData['mobile'])) {
			$msgArray = array(
					'msg' => '当前手机号码已绑定过新手包',
					'res' => 102,
			);
			return $msgArray;
		}
		
		$giftRow = $this->getBindGiftRow($getData['captcha']);
		
		if(empty($giftRow)) {
			$msgArray = array(
					'msg' => '该活动不存在',
					'res' => 104,
			);
			return $msgArray;
		}
		
		if(!empty($giftRow) && $giftRow['is_enable'] == 0) {
			$msgArray = array(
					'msg' => '该活动已经结束',
					'res' => 105,
			);
			return $msgArray;
		}
		
		$gift_id = $giftRow['gift_id'];
		$param = array(
					'gift_id' => $gift_id,
					'type' => $type,
					'captcha' => $giftRow['captcha'],
					'mobile' => $getData['mobile'],
					'tokenkey' => $tokenkey,
					'award_type' => intval($getData['type']),
					'ip' => $getData['ip'] ? $getData['ip'] : 'unknow',
					'created' => REQUEST_TIME
				);
		
		//$afectedNum = $this->_db->insert('oto_app_welcome_record', $param);
		$sql = $this->insertSql('oto_app_welcome_record', $param);
		$this->_db->query($sql);
		$afectedNum = $this->_db->lastInsertId();
		
		if($afectedNum !== 0) {
			$msgArray = array(
					'msg' => '绑定成功',
					'res' => 100,
			);
			return $msgArray;
		} else {
			$msgArray = array(
					'msg' => '绑定失败',
					'res' => 103,
			);
			return $msgArray;
		}		
	}
	
	public function getBindGiftRecordRow($getData, $field = null) {
		if($getData['key'] == '(null)') {
			$key = $getData['idfa'];
			$row = $this->select("`tokenkey` = '{$key}' and `mobile` = '{$getData['mobile']}' and `type` = 'ios'", 'oto_app_welcome_record', '*', '', true);
			return $row ? (is_null($field) ? $row : $row[$field]) : array();
		} else {
			$key = $getData['key'];
			$row = $this->select("`tokenkey` = '{$key}' and `type` = 'ios'", 'oto_app_welcome_record', '*', '', true);
			return $row ? (is_null($field) ? $row : $row[$field]) : array();
		}
		
	}
	
	public function getAndRoidBindGiftRecordRow($key, $field = null) {
		$row = $this->select("`tokenkey` = '{$key}' and `type` = 'android'", 'oto_app_welcome_record', '*', '', true);
		return $row ? (is_null($field) ? $row : $row[$field]) : array();
	}
	
	public function getBindGiftRow($captcha, $field = null) {
		$row = $this->select("`captcha` = '{$captcha}'", 'oto_app_welcome_gift', '*', '', true);
		return is_null($field) ? $row : $row[$field];
	}
	
	public function isMobileBind($mobile) {
		return $this->_db->fetchOne("select 1 from `oto_app_welcome_record` where `mobile` = '{$mobile}' limit 1") == 1;
	}
	
	public function deviceIosIsPresent($mac, $idfa) {
		$db = Core_DB::get('discount_line', null, true); 
		$sql = "select 1 from `ote_binding_details` where `device_mac` = '{$mac}' and `device_idfa` = '{$idfa}' limit 1";
		
		return $db->fetchOne($sql) == 1;
	}
	
	public function deviceAndRoidIsPresent($imei) {
		$db = Core_DB::get('discount_line', null, true);
		$sql = "select 1 from `ote_binding_android_details` where `device_imei` = '{$imei}' limit 1";
	
		return $db->fetchOne($sql) == 1;
	}
	
	public function getGiftData () {
		return $this->_db->fetchAll("select id, prize_name, prize_content from oto_app_welcome_prize where is_enable = 1");
	}
}