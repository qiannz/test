<?php
class Model_Admin_Nav extends Base
{
	private static $_instance;
	protected $_table = 'oto_nav';
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getPositionList($getData) {
		include ROOT_PATH.'lib/Third/Tree/tree.lib.php';
		$tree = new Tree();
		$plateAll = array();
		$where = " and `city` = '{$this->_ad_city}'";
		if(isset($getData['pos_id']) && !empty($getData['pos_id'])) {
			$where .= " and `pos_id` = '{$getData['pos_id']}'";
		}
		
		$sql = "select * from `{$this->_table}` where 1 {$where} order by `sequence` asc, `nav_id` asc";
		$plateAll = $this->_db->fetchAssoc($sql);
		$tree->setTree($plateAll, 'nav_id', 'nav_pid', 'nav_name');
		$sorted_acategories = array();
		$cate_ids = $tree->getChilds();
			
		foreach ($cate_ids as $id) {
			$sorted_acategories[] = array_merge($plateAll[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>1));
		}
		return $sorted_acategories;
	}
	
	public function checkName($name, $pos_id, $id) {
		if ($id == 0) {
			$conditions = " `nav_name` = '{$name}' and `pos_id` = '{$pos_id}' and `city` = '{$this->_ad_city}'";
		} else {
			$conditions = " `nav_name` = '{$name}' and `pos_id` = '{$pos_id}' and `nav_id` <> '{$id}' and `city` = '{$this->_ad_city}'";
		}
		$sql = "select count(nav_id) from `".$this->_table."` where {$conditions}";
		return $this->_db->fetchOne($sql) == 0;
	
	}
	
	public function add($getData) {
		$id = !$getData['id']?0:$getData['id'];
		$pos_id = $getData['pos_id'] ? intval($getData['pos_id']) : intval($getData['psid']);
		$nav_name = trim($getData['nav_name']);
		$pid = !$getData['pid']?0:$getData['pid'];
		$nav_url = $getData['nav_url'] ? trim($getData['nav_url']) : '';
	
		if($id == 0) {
			$insert_id = $this->_db->insert($this->_table, array(
					'nav_pid'		 => $pid,
					'nav_name'       => $nav_name,
					'pos_id'		 => $pos_id,
					'nav_url'        => $nav_url,
					'created'		 => REQUEST_TIME,
					'city'			 => $this->_ad_city
			));
			return $insert_id?$insert_id:false;
		} else {
			$affected_result = $this->_db->update($this->_table, array(
					'nav_pid'		 => $pid,
					'nav_name'       => $nav_name,
					'pos_id'		 => $pos_id,
					'nav_url'        => $nav_url,
					'updated'		 => REQUEST_TIME
			),
					"`nav_id` = '{$id}'"
			);
			return $affected_result?$affected_result:false;
		}
	}
	
	public function del($id) {
		$rowAll = array();
		$sql = "select * from `".$this->_table."` where `nav_pid` = '{$id}'";
		$rowAll = $this->_db->fetchAll($sql);
		$this->_db->delete($this->_table, "`nav_id` = '{$id}'");
		if(!empty($rowAll) && is_array($rowAll)){
			foreach ($rowAll as $rowItem){
				$this->del($rowItem['nav_id']);
			}
		}
		return true;
	}
		
	public function getNavRow($nav_id) {
		return $this->select("`nav_id` = '{$nav_id}'", $this->_table, '*', '', true);
	}
	
	public function getPosition($pos_id, $field = '*', $order = '') {
		return $this->select("`pos_id` = '{$pos_id}'", 'oto_position', $field, $order, true);
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = intval($getData['value']);
	
		return $this->_db->update($this->_table, array($column => $value), "`nav_id` = $id");
	}
	
	public function setNavCache() {
		$navArray = array();
		$pos_pid = $this->getPosId('nav');
		$navPosList = $this->select("`pos_pid` = '{$pos_pid}'", 'oto_position', '*', 'sequence asc, pos_id asc');
		
		foreach ($navPosList as $key => $row) {
			$navArray[$row['city']][$key]['data'] = $this->select("`pos_id` = '{$row['pos_id']}' and `nav_pid` = '0'", 'oto_nav', '*', 'sequence asc, nav_id asc');
			$navArray[$row['city']][$key]['pos_name'] = $row['pos_name'];
			$navArray[$row['city']][$key]['identifier'] = $row['identifier'];
		}
		
		
		foreach ($navArray as $key => $navList) {
			foreach ($navList as $skey => $snavList) {
				foreach ($snavList['data'] as $sskey => $ssnavList) {
					$navArray[$key][$skey]['data'][$sskey]['child'] = $this->select("`nav_pid` = '{$ssnavList['nav_id']}'", 'oto_nav', '*', 'sequence asc, nav_id asc');
				}
			}
		}
		
		$this->array_to_file($navArray, 'nav');
	}
	
	public function getNavCache() {
		$navArray = array();
		$navListArray = $this->select("`identifier` = 'nav'", 'oto_position', '*', 'sequence asc, pos_id asc');
		foreach ($navListArray as $navListItem) {
			$navPosList = $this->select("`pos_pid` = '{$navListItem['pos_id']}'", 'oto_position', '*', 'sequence asc, pos_id asc');
			foreach ($navPosList as $key => $row) {
				$navArray[$navListItem['city']][$key]['data'] = $this->select("`pos_id` = '{$row['pos_id']}' and `nav_pid` = '0'", 'oto_nav', '*', 'sequence asc, nav_id asc');
				$navArray[$navListItem['city']][$key]['pos_name'] = $row['pos_name'];
				$navArray[$navListItem['city']][$key]['identifier'] = $row['identifier'];
			}
		}

		foreach ($navArray as $key => $navList) {
			foreach ($navList as $skey => $snavList) {
				foreach ($snavList['data'] as $sskey => $ssnavList) {
					$navArray[$key][$skey]['data'][$sskey]['child'] = $this->select("`nav_pid` = '{$ssnavList['nav_id']}'", 'oto_nav', '*', 'sequence asc, nav_id asc');
				}
			}
		}
				
		$this->array_to_file($navArray, 'nav');
	}
}