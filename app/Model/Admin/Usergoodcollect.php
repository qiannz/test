<?php
class Model_Admin_Usergoodcollect extends Base {
	private static $_instance;
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
		return $this->_db->fetchOne(
					"select count(favorite_id) from `oto_good_favorite` as A
					left join `oto_good` as B on A.good_id = B.good_id
					where 1 ".$this->_where
				);
	}
	
	// 用户商品收藏列表
	public function getList($page, $pagesize = PAGESIZE)
	{
		$start = ($page - 1) * $pagesize;
		$sql = "select B.*,(select user_name from oto_user where user_id = A.user_id) as username
		        from `oto_good_favorite` as A
				left join `oto_good` as B
				on A.good_id = B.good_id
				where 1 ".$this->_where.$this->_order;
		$goodArray = $this->_db->limitQuery($sql, $start, $pagesize);
		return $goodArray?$goodArray:array();
	}
	
	// 查询条件
	public function setWhere($getData) {
		$where = '';
		$isDel = false;
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'user_id':
							if($value) {
								$where .= " and A.`user_id` = '{$value}'";
							}
							break;
						case 'isd':
							if($value) {
								$isDel = true;
								$where .= " and B.`is_del` = '{$value}'";
							}
							break;
					}
				}
			}
		}
		if (!$isDel) {
			$where .= " and B.`is_del` = '0'";
		}
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by B.`created` desc";
		$this->_order = $order;
	}
}