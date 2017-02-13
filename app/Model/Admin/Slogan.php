<?php
class Model_Admin_Slogan extends base{
private static $_instance;
	protected $_table = 'oto_slogan';
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
		return $this->_db->fetchOne("select count(*) from `".$this->_table."` where 1".$this->_where);
	}
	
	public function setWhere($getData) {
		$isDel = false;
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'name':
							if($value) {
								$where .= " and `name` LIKE '%{$value}%'";
							}
							break;
						case 'category':
							if($value) {
								$where .= " and `category` = '{$value}'";
							}
							break;
						case 'isd':
							if($value) {
								$isDel = true;
								$where .= " and `is_del` = '1'";
							}
							break;
					}
				}
			}
		}
		if (!$isDel) {
			$where .= " and `is_del` = '0'";
		}
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by `created` desc , `slogan_id` desc";
		$this->_order = $order;
	}
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	public function postSlogan( $getData ){
		$data = array();
		$data["category"] = intval($getData["category"]);
		$data["name"] = Custom_String::HtmlReplace($getData["name"]);
		$data["name"] = str_replace("｛", "{", $data["name"]);
		$data["name"] = str_replace("｝", "}", $data["name"]);
		$slogan_id = intval($getData["sid"]);
		if( $slogan_id > 0 ){//编辑
			 $this->_db->update($this->_table,$data,array("slogan_id"=>$slogan_id));
		}else{//修改
			$data["created"] = REQUEST_TIME;
			$slogan_id = $this->_db->insert($this->_table,$data);
		}
		$slogans = $this->getSlogans();
		$this->array_to_file($slogans, 'slogan' );
		return $slogan_id;
	}
	
	public function getSloganById( $slogan_id ){
		$sql = "SELECT * FROM `oto_slogan` WHERE `slogan_id` = '{$slogan_id}'";
		return $this->_db->fetchRow($sql);
	}
	
	public function changeSloganDelStat( $slogan_ids , $is_del ){
		$sql = "UPDATE `oto_slogan` SET `is_del` = '{$is_del}' WHERE `slogan_id` IN ({$slogan_ids})";
		return $this->_db->query($sql);
	}
	
	public function getSlogans(){
		$sql = "SELECT * FROM `oto_slogan` WHERE `is_del`= 0 ORDER BY `category` ASC";
		$res = $this->_db->fetchAll($sql);
		$data = array();
		foreach( $res as $row ){
			$data[$row["category"]][$row["slogan_id"]] = $row["name"];
		}
		return $data;
	}
}
