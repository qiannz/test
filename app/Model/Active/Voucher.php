<?php
class Model_Active_Voucher extends Base {
	
	private static $_instance;
	private $_table = "oto_ticket_share_record";
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * 获取某人的某订单的分享数量
	 * @param unknown_type $user_id
	 * @param unknown_type $order_no
	 */
	public function getVoucherHadShareNum($user_id, $order_no) {
		$sql = "select count(share_id) from `{$this->_table}` where `user_id` = '{$user_id}' and `order_no` = '{$order_no}' and `had_received` = 1";
		$shareNum = $this->_db->fetchOne($sql);
		return $shareNum;
	}
	
	/**
	 * 获取参与用户信息
	 * @param unknown_type $user_id
	 * @param unknown_type $order_no
	 * @param unknown_type $mobile
	 */
	public function getVoucherShareUserInfo($user_id, $mobile, $order_no) {
		$where = "`user_id` = '{$user_id}' and `order_no` = '{$order_no}' and `mobile` = '{$mobile}'";
		$shareUserRow = $this->select($where, $this->_table, '*', '', true);
		return $shareUserRow ? $shareUserRow : array();
	}
	
	public function insertCode($user_id, $mobile, $order_no, $code) {
		$param = array(
					'user_id' => $user_id, 
					'mobile' => $mobile, 
					'code' => $code, 
					'order_no' => $order_no, 
					'ip' => CLIENT_IP, 
					'created' => REQUEST_TIME
				);
		return $this->_db->insert( $this->_table , $param);
	}

	public function updateCode($share_id, $code) {
		return $this->_db->update($this->_table, array('code' => $code), array('share_id' => $share_id) );
	}
	
	public function updateSendStatus($share_id, $award) {
		return $this->_db->update($this->_table, array('had_received' => '1', 'award' => $award), array('share_id' => $share_id) );
	}
	
	public function getVoucherOtherShareRecord($user_id) {
		$sql = "select mobile, award, created from `{$this->_table}` where `user_id` = '{$user_id}' and `had_received` = '1' order by created desc";
		$data = $this->_db->fetchAll($sql);
		foreach($data as &$row) {
			$row['mobile'] = substr_replace($row['mobile'], '****', 3, 4);
			$row['created'] = date('m/d', $row['created']);
		}
		return $data ? $data : array();
	}
}