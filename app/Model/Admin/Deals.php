<?php
class Model_Admin_Deals extends Base
{
	private static $_instance;
	private $_table = 'oto_deals';
	
	public static function getInstance()
	{
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
	
	public function getCount($ticketType) {
		return $this->_db->fetchOne("select count(deals_id) from `".$this->_table."` where 1 ".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'name':
							if($value) {
								$where .= " and `deals_name` like '%".trim($value)."%'";
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
	
	public function getList($page, $ticketType, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	public function addEdit($getData) {
		$params = array(
					'deals_name' => Custom_String::HtmlReplace($getData['deals_name'], 1),
					'discount' => Custom_String::HtmlReplace($getData['discount'], 1),
					'had_ticket' => intval($getData['had_ticket']),
					'voucher_id' => Custom_String::HtmlReplace($getData['voucher_id'], 1),
					'start_time' => strtotime($getData['start_time']),
					'end_time' => strtotime($getData['end_time']),
					'img' => $getData['img'],
					'link' => $getData['link']
				);
		
		if(!$getData['deals_id']) {
			$params = array_merge(array('city' => $this->_ad_city, 'created' => REQUEST_TIME), $params);
			return $this->_db->insert($this->_table, $params);
		} else {
			return $this->_db->update($this->_table, $params, array('deals_id' => intval($getData['deals_id'])));
		}
	}
	
	public function getDealsRow($deals_id) {
		return $this->select("`deals_id` = '{$deals_id}'", $this->_table, '*', '', true);
	}

	public function del($deals_id) {
		return $this->_db->delete($this->_table, "`deals_id` = '{$deals_id}'");
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		return $this->_db->update($this->_table, array($column => $value), "`deals_id` = $id");
	}	
}