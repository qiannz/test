<?php
class Model_Admin_Message extends Base
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
		return $this->_db->fetchOne("select count(tid) from `oto_message_thread` where ".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = '';
		$this->_where = "`is_del` = '0'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'question':
							if($value) {
								$where .= " and `question` like '%{$value}%'";
							}
							break;
						case 'author':
							if($value) {
								$where .= " and `user_name` = '{$value}'";
							}
							break;
						case 'type':
							if($value) {
								$where .= " and `type` = '{$value}'";
							}
							break;
					}
				}
			}
		}
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by `updated` desc, `reply_time` desc,  `created` desc";
		$this->_order = $order;
	}
	
	public function getThreadList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `oto_message_thread` where ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach($data as &$row) {
			$ticketRow = $this->select("`ticket_id` = '{$row['from_id']}'", "oto_ticket", 'ticket_uuid, ticket_title', '', true);
			$row['title'] = $ticketRow['ticket_title'];
			$row['ticket_uuid'] = $ticketRow['ticket_uuid'];
		}
		return $data ? $data : array();
	}
	
	public function getPostList($tid) {
		$sql = "select * from `oto_message_post` where `tid` = '{$tid}' and `is_del` = '0' order by pid asc";
		$data = $this->_db->fetchAll($sql);
		return $data ? $data : array();
	}
	
	public function delThread($id) {
		if(strpos($id, ',')) {
			$sql = "update `oto_message_thread` set `is_del` = '1' where `tid` in ({$id})";
		} else {
			$sql = "update `oto_message_thread` set `is_del` = '1' where `tid` = '{$id}' limit 1";
		}
		return $this->_db->query($sql);
	}
	
	public function delPost($id) {
		$sql = "update `oto_message_post` set `is_del` = '1' where `pid` = '{$id}' limit 1";
		return $this->_db->query($sql);
	}
}