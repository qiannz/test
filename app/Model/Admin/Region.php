<?php
class Model_Admin_Region extends Base {
	private static $_instance;
	protected $_table = 'oto_region';
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function add($rname) {
		return $this->_db->insert($this->_table, array('region_name' => $rname, 'created' => REQUEST_TIME, 'city' => $this->_ad_city));
	}
	
	public function getList() {
		return $this->_db->fetchAll("select * from `{$this->_table}` where `city` = '{$this->_ad_city}' order by sequence asc, region_id asc");		
	}
	
	public function edit($id, $rname) {
		return $this->_db->update($this->_table, array('region_name' => $rname, 'updated' => REQUEST_TIME), "region_id = '{$id}'");
	}
	
	public function del($id) {
		return $this->_db->delete($this->_table, '`region_id` = ' . $id);
	}
		
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		return $this->_db->update($this->_table,array($column => $value), "`region_id` = $id");
	}
}