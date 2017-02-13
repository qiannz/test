<?php
class Model_Admin_Notice extends Base
{
	private static $_instance;
	protected $_where;
	protected $_order;
		
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getCount() {
		return $this->_db->fetchOne("select count(notice_id) from `oto_ticket_notice` where ".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = '';
		$this->_where = "`is_del` = '0'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'user_name':
							if($value) {
								$user_id = $this->getUserIdByUserName($value);
								$where .= " and `user_id` = '{$user_id}'";
							}
							break;
						case 'title':
							if($value) {
								$ticketRow = $this->select("`ticket_title` = '{$value}'", "oto_ticket", 'ticket_id', '', true);
								if($ticketRow) {
									$where .= " and `ticket_id` = '{$ticketRow['ticket_id']}'";
								}
							}
							break;
					}
				}
			}
		}
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by `created` desc";
		$this->_order = $order;
	}
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `oto_ticket_notice` where ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach($data as &$row) {
			$userInfo = $this->getUserByUserId($row['user_id']);
			$row['user_name'] = $userInfo['user_name'];
			$ticketRow = $this->select("`ticket_id` = '{$row['ticket_id']}'", "oto_ticket", 'ticket_title', '', true);
			$row['title'] = $ticketRow['ticket_title'];
		}
		return $data ? $data : array();
	}
	
	public function del($id) {
		if(strpos($id, ',')) {
			$sql = "update `oto_ticket_notice` set `is_del` = '1' where `notice_id` in ({$id})";
		} else {
			$sql = "update `oto_ticket_notice` set `is_del` = '1' where `notice_id` = '{$id}' limit 1";
		}
		return $this->_db->query($sql);
	}
}