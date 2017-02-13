<?php
class Model_Admin_Store extends Base {
	private static $_instance;
	protected $_table = 'oto_store';
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function add($sname, $isApp) {
		return $this->_db->insert($this->_table, array('store_name' => $sname, 'created' => REQUEST_TIME, 'is_app' => $isApp, 'city' => $this->_ad_city));
	}
	
	public function getList() {
		return $this->_db->fetchAll("select * from oto_store where `city` = '{$this->_ad_city}' order by sequence asc");		
	}
	
	public function getAppList() {
		return $this->_db->fetchAll("select * from oto_store where is_app = 1 order by sequence asc");
	}
	
	public function edit($id, $sname, $mark, $isApp) {
		return $this->_db->update($this->_table, array('store_name' => $sname, 'mark' => $mark, 'is_app' => $isApp), "store_id = '{$id}'");
	}
	
	public function del($id) {
		return $this->_db->delete($this->_table, '`store_id` = ' . $id);
	}
	
	public function arrange($data) {
		$dataArray = array();
		foreach ($data as $item) {
			$dataArray[$item['store_id']] = $item['store_name'];
		}
		return $dataArray;
	}	
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		$result = $this->_db->update('oto_store',array($column => $value), "`store_id` = $id");
		if($result){
			return true;
		}
		return false;
	}
}