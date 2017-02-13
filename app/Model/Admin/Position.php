<?php
class Model_Admin_Position extends Base
{
	private static $_instance;
	protected $_table = 'oto_position';
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getPositionList() {
		include ROOT_PATH.'lib/Third/Tree/tree.lib.php';
		$tree = new Tree();
		$plateAll = array();
		$sql = "SELECT * FROM `oto_position` where `city` = '{$this->_ad_city}' order by `sequence` asc, `pos_id` asc";
		$plateAll = $this->_db->fetchAssoc($sql);
		$tree->setTree($plateAll, 'pos_id', 'pos_pid', 'pos_name');
		$sorted_acategories = array();
		$cate_ids = $tree->getChilds();
		 
		foreach ($cate_ids as $id) {
			$sorted_acategories[] = array_merge($plateAll[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>1));
		}
		return $sorted_acategories;
	}
	 
	public function add($getData) {
		$id = !$getData['id']?0:$getData['id'];
		$pos_name = trim($getData['pos_name']);
		$identifier = trim($getData['identifier']);
		if($getData['pos_pid']) {
			$pid = $getData['pos_pid'];
		} elseif ($getData['pid']) {
			$pid = $getData['pid'];
		} else {
			$pid = 0;
		}
		$width = intval($getData['width']);
		$height = intval($getData['height']);
		$pos_url = $getData['pos_url'] ? trim($getData['pos_url']) : '';
	
		if($id == 0) {
			$insert_id = $this->_db->insert($this->_table, array(
					'pos_pid'		 => $pid,
					'pos_name'       => $pos_name,
					'identifier'     => $identifier,
					'width'          => $width,
					'height'         => $height,
					'pos_url'		 => $pos_url,
					'created'		 => REQUEST_TIME,
					'city'   		 => $this->_ad_city
			));
			return $insert_id?$insert_id:false;
		} else {
			$affected_result = $this->_db->update($this->_table, array(
					'pos_pid'		 => $pid,
					'pos_name'       => $pos_name,
					'identifier'     => $identifier,
					'width'          => $width,
					'height'         => $height,
					'pos_url'		 => $pos_url,
					'updated'		 => REQUEST_TIME
					),
					"`pos_id` = $id"
			);
			return $affected_result?$affected_result:false;
		}
	}
	
	public function del($id) {
		$rowAll = array();
		$sql = "select * from `".$this->_table."` where `pos_pid` = '{$id}'";
		$rowAll = $this->_db->fetchAll($sql);
		$this->_db->delete($this->_table, "`pos_id` = '{$id}'");
		if(!empty($rowAll) && is_array($rowAll)){
			foreach ($rowAll as $rowItem){
				$this->del($rowItem['pos_id']);
			}
		}
		return true;	
	}
	 
	// 获取一级板块名
	public function getParentSortList($pid = 0) {
		if (empty($pid)) {
			$sql = "select * from `".$this->_table."` where `pos_pid` = '0' and `city` = '{$this->_ad_city}'";
		} else {
			$sql = "select * from `".$this->_table."` where `pos_pid` = '0' and `pos_id` <> '{$pid}' and `city` = '{$this->_ad_city}'";
		}
		return $this->_db->fetchAll($sql);
	}
	
	public function checkName($name, $id) {
		if ($id == 0) {
			$conditions = " `pos_name` = '{$name}' and `city` = '{$this->_ad_city}'";
		} else {
			$conditions = " pos_name = '{$name}' and `pos_id` <> '{$id}' and `city` = '{$this->_ad_city}'";
		}
		$sql = "select count(pos_id) from `".$this->_table."` where {$conditions}";
		return $this->_db->fetchOne($sql) == 0;
	
	}
	
	public function unique($identifier, $id) {
		if ($id == 0) {
			$conditions = " `identifier` = '{$identifier}' and `city` = '{$this->_ad_city}'";
		} else {
			$conditions = " `identifier` = '{$identifier}' and pos_id <> '{$id}' and `city` = '{$this->_ad_city}'";
		}
		$sql = "select count(pos_id) from `".$this->_table."` where $conditions";
		return $this->_db->fetchOne($sql) == 0;
	}
	
	public function getPositionRow($pos_id) {
		return $this->select("`pos_id` = '{$pos_id}'", $this->_table, '*', '', true);
	}
    
    public function ajax_module_edit($getData){
    	$column = $getData['column'];
    	$id = $getData['id'];
    	$value = $getData['value'];
    
    	return $this->_db->update($this->_table, array($column => $value), "`pos_id` = $id");
    }
}