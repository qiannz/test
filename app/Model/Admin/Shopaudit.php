<?php
class Model_Admin_Shopaudit extends Base {
	private static $_instance;
	protected $_table = 'oto_merchant_audit';
	protected $_where;
    protected $_order;
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_where = '';
		$this->_order = '';
	}

	public function getCount(){
		return $this->_db->fetchOne("select count(audit_id) from `{$this->_table}` where 1 {$this->_where}");
	}
	
	public function getList($page, $pagesize = PAGESIZE)
	{
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `{$this->_table}` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data?$data:array();
	}
	
	public function setWhere($getData){
		$where = '';
		$isDel = false;
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'st':
							if($value == 1) {
								$where .= " and `audit_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `audit_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `audit_status` = '-1'";
							}
							break;
						case 'uname':
							if($value) {
								$where .= " and `user_name` = '{$value}'";
							}
							break;
						case 'isd':
							if($value == 1) {
								$isDel = true;
							}
							break;
					}
				}
			}
		}
	
		if($isDel) {
			$where .= " and `is_del` = '1'";
		} else {
			$where .= " and `is_del` = '0'";
		}		
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by `created` desc";
		$this->_order = $order;
	}
	
	public function getRowById($audit_id) {
		return $this->select("`audit_id` = '{$audit_id}'", $this->_table, '*', '', true);
	}
	
	public function audit($data, $row) {
		$aid = intval($data['aid']);
		$page = intval($data['page']);
		$audit_type = intval($data['audit_type']);
		$reason1 = trim($data['reason1']);
		$reason2 = trim($data['reason2']);
		
		if($audit_type == 2) {
			switch ($reason1) {
				case 1:
					$reason = '虚假信息';
					break;
				case 2:
					$reason = '重复认证';
					break;
				case 3:
					$reason = $reason2;
					break;
			}
		} elseif ($audit_type == 1) {
			$reason = '审核通过';
		}
		
		if($audit_type == 1) {
			$this->_db->beginTransaction();			
			$result1 = $this->_db->update($this->_table, array('audit_status' => '1', 'reason' => $reason), "`audit_id` = '{$aid}'");
			$result2 = $this->_db->update('oto_shop', array('user_id' => $row['user_id'], 'user_name' => $row['user_name']), "`shop_id` = '{$row['shop_id']}'");
			$result3 = $this->_db->update('oto_user', array('user_type' => '2'), "`user_id` = '{$row['user_id']}'");
			if($result1 && $result2 && $result3) {
				$this->_db->commit();
				return true;
			} else {
				$this->_db->rollBack();
				return false;
			}
			
		} elseif ($audit_type == 2) {
			return $this->_db->update($this->_table, array('audit_status' => '-1', 'reason' => $reason), "`audit_id` = '{$aid}'");
		}
		return false;		
	}
}