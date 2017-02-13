<?php
class Model_Admin_Sort extends Base {
	protected $_table = 'oto_sort_detail';
	private static $_instance;
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function setWhere($getData){
		$where = '';
		if(!empty($getData)){
			if(isset($getData['tid']) && !empty($getData['tid'])){
				$where .= " and `sort_id` = '".intval($getData['tid'])."'";
			}
		}
		$this->_where = $where;
	}
	
	public function setOrder($getData) {
		$order = '';
		if(isset($getData['tid']) && !empty($getData['tid'])){
			$order .= " order by sequence asc, created desc ";
		}else{
			$order .= " order by sort_detail_id desc ";
		}
	
		$this->_order = $order;
	}
	
	public function getCount(){
		return $this->_db->fetchOne("select count(*) from `".$this->_table."` where 1".$this->_where.$this->_order);
	}
	 
	public function getSortList($page, $pagesize = PAGESIZE)
	{
		$sortAll = array();
		$category_arrange = array();
		$start = ($page - 1) * $pagesize;
		 
		$categories = $this->getCategory();
		foreach ($categories as $key => $category){
			$category_arrange[$category['sort_id']] = $category['sort_name'];
		}
		$sql = "select * from `".$this->_table."` where 1 {$this->_where} {$this->_order}";
		$sortAll = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($sortAll as $key => $sort){
			$sortAll[$key]['sort_name'] = $category_arrange[$sort['sort_id']];
		}
		return $sortAll;
	}
	
	public function getCategory($cid = null)
	{
		$where = '';
		$categories = array();
		if (!empty($cid)) {
			$where = " and `sort_id` = '{$cid}'";
		}
		$sql = "select * from `oto_sort` where 1 {$where} order by `sort_id` desc";
		$categories = $this->_db->fetchAll($sql);
		return $categories;
	}
	
	
	public function unique($sort_detail_name, $sort_id, $id = 0)
	{
		$conditions = "`sort_detail_name` = '{$sort_detail_name}' AND `sort_id` = '{$sort_id}'";
		$id && $conditions .= " AND `sort_detail_id` <> $id";
	
		$sql = "select count(*) from `".$this->_table."` where $conditions";
		return $this->_db->fetchOne($sql) == 0;
	}

	public function unique_mark($sort_detail_mark, $id = 0)
	{
		$conditions = "`sort_detail_mark` = '{$sort_detail_mark}'";
		$id && $conditions .= " AND `sort_detail_id` <> $id";
	
		$sql = "select count(*) from `".$this->_table."` where $conditions";
		return $this->_db->fetchOne($sql) == 0;
	}
	// 分类操作：新增和修改
	public function sortOperate($postData){
		$id = isset($postData['id'])?intval($postData['id']):0;
		$sort_detail_name = trim($postData['sort_detail_name']);
		$sort_detail_mark = trim($postData['sort_detail_mark']);
		$sort_id = intval(trim($postData['sort_id']));
		$time = time();
		$setArr = array(
				'sort_detail_name' => $sort_detail_name,
				'sort_detail_mark' => $sort_detail_mark,
				'sort_id' => $sort_id,
				'created' => $time,
		);
		if($id == 0){
			if(!$this->unique($sort_detail_name, $sort_id, $id) || !$this->unique_mark($sort_detail_mark, $id)){
				return 'repeat';
			}
			$insert_id = $this->_db->insert($this->_table, $setArr);
			return $insert_id?$insert_id:false;
		}else{
			$affected_rows = $this->_db->update($this->_table, $setArr,"`sort_detail_id` = $id");
			return $affected_rows?$affected_rows:false;
		}
	}
	
	
	public function categoryOperate($postData){
		$id = isset($postData['id'])?intval($postData['id']):0;
		$sort_name = trim($postData['sort_name']);
		$sort_unique = trim($postData['sort_unique']);
		$setArr = array(
				'sort_name' => $sort_name,
				'sort_unique' => $sort_unique
		);
		if($id == 0){
			if($this->_db->fetchOne("select count(*) from `oto_sort` where `sort_name` = '{$sort_name}' and `sort_unique` = '{$sort_unique}'")){
				return 'repeat';
			}
			$insert_id = $this->_db->insert('oto_sort', $setArr);
			return $insert_id?$insert_id:false;
		}else{
			$affected_rows = $this->_db->update('oto_sort', $setArr,"`sort_id` = $id");
			return $affected_rows?$affected_rows:false;
		}
	}
	
	public function unique_category($sort_name, $id = 0)
	{
		$conditions = "`sort_name` = '{$sort_name}'";
		$id && $conditions .= " AND `sort_id` <> $id";
	
		$sql = "select count(*) from `oto_sort` where $conditions";
		return $this->_db->fetchOne($sql) == 0;
	}
	
	public function unique_unique($sort_unique, $id = 0)
	{
		$conditions = "`sort_unique` = '{$sort_unique}'";
		$id && $conditions .= " AND `sort_id` <> $id";
	
		$sql = "select count(*) from `oto_sort` where $conditions";
		return $this->_db->fetchOne($sql) == 0;
	}
	
	public function del_sort($id)
	{
		$delResult = $this->_db->delete($this->_table, "`sort_detail_id` = $id");
		return $delResult;
	}
	
	public function del_category($id)
	{
		$delResult1 = $this->_db->delete('oto_sort', "`sort_id` = '{$id}'");
		$delResult2 = $this->_db->delete($this->_table, "`sort_id` = '{$id}'", 0);
		return $delResult1 && $delResult2;
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		$result = $this->_db->update('oto_sort_detail',array($column => $value), "`sort_detail_id` = $id");
		if($result){
			echo json_encode(true);
		}
	}
}