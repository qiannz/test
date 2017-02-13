<?php
class Model_Admin_Recommend extends Base
{
	private static $_instance;
	protected $_where;
	protected $_order;
	
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
	
	public function getCount() {
		return $this->_db->fetchOne("select count(recommend_id) from `oto_recommend` where 1=1". $this->_where);
	}

	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `oto_recommend` where 1=1" . $this->_where .  $this->_order;
		$recommend = $this->_db->limitQuery($sql, $start, $pagesize);
		
		foreach ($recommend as &$row) {
			$positionInfo = $this->_db->fetchRow("select pos_name, identifier from oto_position where pos_id = '{$row['pos_id']}'");
			$row['pos_name'] = $positionInfo['pos_name'];
			$row['identifier'] = $positionInfo['identifier'];
		}
		
		return $recommend?$recommend:array();
	}

	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if($getData['title']){
			$where .= "  and `title` like '%{$getData['title']}%'";
		}
		
		if($getData['pos_id']){
			$where .= "  and pos_id = '{$getData['pos_id']}'";
		}
		
		$this->_where = $where;
	}
	
	public function setOrder($getData) {
		if($getData['pos_id']){
			$order = " order by `sequence` asc,  `created` desc";
		} else {
			$order = " order by `created` desc";
		}
		$this->_order = $order;
	}
		
	public function recommendEdit($postData) {
		$arr = array (
				'come_from_id' => $postData['come_from_id'] ? $postData['come_from_id'] : 0,
				'title'     => $postData['title'],
				'summary'  => $postData['summary'],
				'pos_id'        => $postData['pos_id'],
				'www_url'       => trim($postData['www_url']),
				'img_url'       => $postData['img_url'],
				'pmark'	=> $postData['pmark'],
				'cmark' => $postData['cmark'],
				'city' => $this->_ad_city
		);
		if (!$postData['id']) {
			$arr['created'] = REQUEST_TIME;
			$arr['updated'] = REQUEST_TIME;
			return $this->_db->insert('oto_recommend', $arr);
		} else {
			$arr['updated'] = REQUEST_TIME;
			return $this->_db->update('oto_recommend', $arr, "recommend_id = '{$postData['id']}'");
		}
	}
	
	public function del($id) {
		return $this->_db->delete('oto_recommend', "`recommend_id` = $id");
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		$result = $this->_db->update('oto_recommend',array($column => $value), "`recommend_id` = $id");
		if($result){
			return true;
		}
		return false;
	}
}