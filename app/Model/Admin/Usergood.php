<?php
class Model_Admin_Usergood extends Base {
	private static $_instance;
	protected $_table = 'oto_good';
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
		return $this->_db->fetchOne("select count(good_id) from `".$this->_table."` where 1".$this->_where);
	}
	
	// 用户商品列表
	public function getList($page, $pagesize = PAGESIZE)
	{
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
		$goodArray = $this->_db->limitQuery($sql, $start, $pagesize);		 
		return $goodArray?$goodArray:array();
	}
	
	// 查询条件
	public function setWhere($getData){
		$where = '';
		$isDel = false;
		if(!empty($getData)){
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'uid':
							if($value) {
								$where .= " and `user_id` = '{$value}'";
							}
							break;
						case 'uname':
							if($value) {
								$where .= " and `user_name` = '{$value}'";
							}
							break;
						case 'isd':
							if($value) {
								$isDel = true;
								$where .= " and `is_del` = '{$value}'";
							}
							break;
					}
				}
			}
		}
		if(!$isDel) {
			$where .= " and `is_del` = '0'";
		}
		
		$this->_where .= $where;		
	}
	
	public function setOrder($getData) {
		$order = " order by `created` desc";
		$this->_order = $order;
	}
}