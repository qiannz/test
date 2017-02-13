<?php
class Model_Admin_Appwheel extends Base {
	
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	public function getCount() {
		return $this->_db->fetchOne("select count(*) from oto_app_wheel_log where 1=1 " . $this->_where . " order by created desc");		
	}
	
	
	public function getAppWheelList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from oto_app_wheel_log where 1=1" . $this->_where . " order by created desc";
		$appWheels = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($appWheels as &$row) {
			$row['user_name'] = $this->_db->fetchOne("select user_name from oto_user where user_id = '{$row['user_id']}'");
		}
		return $appWheels?$appWheels:array();
	}

	public function setWhere($getData) {
		$where = '';
		
		if($getData['wheel_type']){
			$where .= "  and type = '{$getData['wheel_type']}'";
		}
		
		if($getData['mobile']){
			$where .= "  and mobile = '{$getData['mobile']}'";
		}
		
		if($getData['is_valid']) {
			if($getData['is_valid'] == 1) {
				$where .= "  and is_valid = '0'";
			} elseif($getData['is_valid'] == 2) {
				$where .= "  and is_valid = '1'";
			}
		}
		
		if($getData['award_name']){
			$where .= "  and award_name LIKE '%{$getData['award_name']}%'";
		}
		
		if($getData['user_name']){
			$user_id = $this->getUserIdByUserName($getData['user_name']);
			$where .= "  and user_id = '{$user_id}'";
		}
		
		$this->_where = $where;
	}
	
	public function validWheel($id) {
		return $this->_db->update('oto_app_wheel_log', array('is_valid' => 1, 'valid_time' => REQUEST_TIME), array('id' => $id));
	}
		
}