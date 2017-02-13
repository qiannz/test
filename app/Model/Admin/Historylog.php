<?php
class Model_Admin_Historylog extends Base {
	private static $_instance;

	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	public function getCount($type = '', $id = '') {
		if(!empty($type) && !empty($id)) {
			if ($type == 'good') {
				$where .= " and good_id = '{$id}'";
			} else if ($type == 'shop') {
				$where .= " and shop_id = '{$id}'";
			} else if ($type == 'user') {
				$where .= " and user_id = '{$id}'";
			} else if ($type == 'ticket') {
				$where .= " and ticket_id = '{$id}'";
			}
		}
		return $this->_db->fetchOne("select count(log_id) from oto_log where 1=1". $where . $this->_where);
	}
	
	public function getLogList($page, $pagesize = PAGESIZE, $type, $id) {
		if(!empty($type) && !empty($id)) {
			if ($type == 'good') {
				$where .= " and good_id = '{$id}'";
			} else if ($type == 'shop') {
				$where .= " and shop_id = '{$id}'";
			} else if ($type == 'user') {
				$where .= " and user_id = '{$id}'";
			} else if ($type == 'ticket') {
				$where .= " and ticket_id = '{$id}'";
			}
		}
		$start = ($page - 1) * $pagesize;
		$sql = "select * from oto_log where 1=1" . $where . $this->_where .  " order by created desc";
		$logs = $this->_db->limitQuery($sql, $start, $pagesize);
		
		$adminArray = $this->_db->fetchPairs("select id, userid from oto_admin");
		
		foreach($logs as & $logItem) {
			$logItem['admin_user_name'] = $adminArray[$logItem['admin_id']];
		}
		
		return $logs?$logs:array();
	}
	
	public function setWhere($getData) {
		$where = '';
		
		if($getData['pmodule']){
			$where .= "  and pmodule = '{$getData['pmodule']}'";
		}
		
		if($getData['cmodule']){
			$where .= "  and cmodule = '{$getData['cmodule']}'";
		}
		
		if($getData['activity']){
			$where .= "  and activity = '{$getData['activity']}'";
		}
		
		if($getData['field_name'] && $getData['field_value']){
			if($getData['field_name'] == 'good'){
				$gid = $this->_db->fetchCol("select good_id from oto_good where good_name = '{$getData['field_value']}'");
				$where .= " and ".$this->db_create_in($gid, 'good_id');
			} else if ($getData['field_name'] == 'shop') {
				$sid = $this->_db->fetchCol("select shop_id from oto_shop where shop_name = '{$getData['field_value']}'");
				$where .= " and ".$this->db_create_in($sid, 'shop_id');
			} else if ($getData['field_name'] == 'user') {
				$uid = $this->_db->fetchCol("select user_id from oto_user where user_name = '{$getData['field_value']}'");
				$where .= " and ".$this->db_create_in($uid, 'user_id');
			} else if ($getData['field_name'] == 'ticket') {
				$tid = $this->_db->fetchCol("select ticket_id from oto_ticket where ticket_title = '{$getData['field_value']}'");
				$where .= " and ".$this->db_create_in($tid, 'ticket_id');
			}	
		}		
		$this->_where = $where;
	}
	
	public function getPModel() {
		return $this->_db->fetchAll("select pmodule from oto_log where pmodule <> ''  group by pmodule");
	}
	
	public function getCModel($pmodule) {
		return $this->_db->fetchAll("select cmodule from oto_log where pmodule = '{$pmodule}'  and cmodule <> '' group by cmodule");
	}
}