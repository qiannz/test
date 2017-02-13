<?php
class Model_Admin_Pack extends Base {
	private static $_instance;
	protected $_table = 'oto_pack';
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
	
	public function getCount() {
		return $this->_db->fetchOne("select count(*) from oto_pack where 1=1". $this->_where . " order by sequence asc");
	}
	
	public function getPackList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from oto_pack where 1=1" . $this->_where . " order by sequence asc";
		$packs = $this->_db->limitQuery($sql, $start, $pagesize);
		return $packs?$packs:array();
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if($getData['pack_name']){
			$where .= "  and pack_name like '%{$getData['pack_name']}%'";
		}
		$this->_where = $where;
	}
	
	public function pactModi($postData) {
		$pack_id = intval($postData['pack_id']);
		$pack_name = trim($postData['pack_name']);
		$pack_logo = trim($postData['pack_logo']);
		$good_num = intval($postData['good_num']);
		$ticket_num = intval($postData['ticket_num']);
		$pack_explan = trim($postData['pack_explan']);
		$arr = array(
				'pack_name'      => $pack_name,
				'pack_logo'      => $pack_logo,
				'good_num'       => $good_num,
				'ticket_num'     => $ticket_num,
				'pack_explan'    => $pack_explan,
		);
		if (!$pack_id) { // 新增
			$arr = array_merge($arr, array('city' => $this->_ad_city));
			$insert_id = $this->_db->insert('oto_pack', $arr);
			return $insert_id ? $insert_id : false;
		} else { // 编辑
			$affected_rows = $this->_db->update('oto_pack', $arr,"`pack_id` = $pack_id");
			return $affected_rows ? $affected_rows : false;
		}
	}
	
	public function del($pack_id) {
		$delResult = $this->_db->delete('oto_pack', "`pack_id` = $pack_id");
		return $delResult;
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		$result = $this->_db->update('oto_pack',array($column => $value), "`pack_id` = $id");
		if($result){
			$this->getPack(0, false); //缓存
			return true;
		}
		return false;
	}
	
	public function setDefault($pack_id) {
		$this->_db->update('oto_pack', array('is_default' => 0), array('city' => $this->_ad_city), false);
		return $this->_db->update('oto_pack', array('is_default' => 1), array('pack_id' => $pack_id, 'city' => $this->_ad_city));
	}
	
	public function check_logo($pack_logo, $pack_id) {
		$conditions = "`pack_logo` = '{$pack_logo}' and `city` = '{$this->_ad_city}'";
		$pack_id && $conditions .= " AND `pack_id` <> $pack_id";
		return $this->_db->fetchOne("select count(*) from oto_pack where $conditions");
	}
}