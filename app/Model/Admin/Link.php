<?php
class Model_Admin_Link extends Base {
	
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getLinkList() {
		include ROOT_PATH.'lib/Third/Tree/tree.lib.php';
		$tree = new Tree();
		$plateAll = array();

	
		$sql = "select * from `oto_app_module` order by `sequence` asc, `id` asc";
		$plateAll = $this->_db->fetchAssoc($sql);
		$tree->setTree($plateAll, 'id', 'pid', 'name');
		$sorted_acategories = array();
		$cate_ids = $tree->getChilds();
			
		foreach ($cate_ids as $id) {
			$sorted_acategories[] = array_merge($plateAll[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>1));
		}
		return $sorted_acategories;
	}
	
	// 获取父名称
	public function getModuleSelect($pid = 0){
		$moduleParent = array();
		$sql = "select * from `oto_app_module` where `pid` = {$pid} order by sequence asc, id asc";
		$moduleParent = $this->_db->fetchAll($sql);
		return $moduleParent;
	}
	
	// 新增链接名称
	public function link_insert($postData){
		$id = !$postData['id']?0:$postData['id'];
		$name = trim($postData['name']);
		$pid = !$postData['pid']?0:$postData['pid'];
		$mark = trim($postData['mark']);
		$sequence = intval($postData['sequence']);
		if($id == 0){
			if(!$this->unique($name, $pid)){
				return 'repeat';
			}
			$insert_id = $this->_db->insert('oto_app_module', array(
					'name' => $name,
					'pid' => $pid,
					'mark' => $mark,
					'sequence' => $sequence,
			));
			return $insert_id?$insert_id:false;
		}else{
			$affected_rows = $this->_db->update('oto_app_module', array(
					'name' => $name,
					'pid' => $pid,
					'mark' => $mark,
					'sequence' => $sequence,
			),
					"`id` = $id"
			);
			return $affected_rows?$affected_rows:false;
		}
	}
	
	// 获取信息函数
	public function get_info($id){
		$sql = "select * from `oto_app_module` where `id` = '{$id}'";
		return $this->_db->fetchRow($sql);
	}
	
	public function del($id) {
		$rowAll = array();
		$sql = "select * from `oto_app_module` where `pid` = '{$id}'";
		$rowAll = $this->_db->fetchAll($sql);
		$this->_db->delete('oto_app_module', "`id` = '{$id}'");
		if(!empty($rowAll) && is_array($rowAll)){
			foreach ($rowAll as $rowItem){
				$this->del($rowItem['id']);
			}
		}
		return true;
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = intval($getData['value']);
	
		return $this->_db->update('oto_app_module', array($column => $value), "`id` = $id");
	}
	
	public function getDada() {
		$data = array();
		$father = $this->_db->fetchAll("select * from oto_app_module where pid = 0 order by sequence asc, id asc");
		foreach ($father as $row) {
			$child = $this->_db->fetchAll("select name, mark from oto_app_module where pid = '{$row['id']}'");
			$data[$row['mark']] = array(
									'name'  => $row['name'],
									'child' => $child,
								);
		}
		return $data;
	}
	
	
	// 验证函数
	public function unique($name, $pid, $id = 0)
	{
		$conditions = "pid = '$pid' AND name = '$name'";
		$id && $conditions .= " AND id <> '" . $id . "'";
	
		$sql = "select count(*) from `oto_app_module` where $conditions";
		return $this->_db->fetchOne($sql) == 0;
	}
}