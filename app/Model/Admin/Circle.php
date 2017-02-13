<?php
class Model_Admin_Circle extends Base {
	private static $_instance;
	protected $_table = 'oto_circle';
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function add($region_id, $cname, $isHot) {
		return $this->_db->insert($this->_table, array('region_id' => $region_id, 'circle_name' => $cname, 'is_hot' => $isHot, 'created' => REQUEST_TIME, 'city' => $this->_ad_city));
	}
	
	public function edit($id, $region_id, $cname, $isHot) {
		return $this->_db->update($this->_table, array('region_id' => $region_id, 'circle_name' => $cname, 'is_hot' => $isHot, 'updated' => REQUEST_TIME), "circle_id = '{$id}'");
	}
	
	public function modi($postData, $city) {
		$circle_id = isset($postData['circle_id'])?intval($postData['circle_id']):0;
		$region_id = intval($postData['region_id']);
		$circle_name = trim($postData['circle_name']);
		$is_hot = $postData['is_hot'];
		$is_show =$postData['is_show'];

		$arr = array(
				'region_id'	     => $region_id,
				'circle_name'    => $circle_name,
				'is_hot'         => $is_hot,
				'is_show'        => $is_show,
		);
		if ($circle_id == 0) { // 新增
			$arr['created'] = REQUEST_TIME;
			$arr['city'] = $city;
			$insert_id = $this->_db->insert('oto_circle', $arr);
			return $insert_id?$insert_id:false;
		} else { // 编辑
			$arr['updated'] = REQUEST_TIME;
			$affected_rows = $this->_db->update('oto_circle', $arr, "`circle_id` = '{$circle_id}' and city = '{$city}'");
			return $affected_rows?$affected_rows:false;
		}
	}
	
	
	public function getCount($city) {
		return $this->_db->fetchOne("select count(*) from oto_circle where 1=1 and city = '{$city}' ". $this->_where);
	}
	
	public function getList($page, $city, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from oto_circle where 1=1 and city = '{$city}' " . $this->_where . " order by sequence asc, created desc";
		$circle = $this->_db->limitQuery($sql, $start, $pagesize);
		return $circle?$circle:array();
	}
	
	public function setWhere($getData) {
		$where = '';
		 
		if ($getData['region_id']) {
			$where .= "  and region_id = '{$getData['region_id']}'";
		}
		
		if($getData['circle_name']){
			$where .= "  and circle_name LIKE '%{$getData['circle_name']}%' ";
		}
		
		if($getData['is_show']){
			$where .= "  and is_show = '{$getData['is_show']}'";
		}
		
		if($getData['is_hot']){
			$where .= "  and is_hot = '{$getData['is_hot']}'";
		}
		
		$this->_where .= $where;
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		return $this->_db->update('oto_circle',array($column => $value), "`circle_id` = $id");
	}
	
	public function del($id) {
		return $this->_db->delete($this->_table, '`circle_id` = ' . $id);
	}
	
	public function recommend($getData) {
		$arr = array(
				'come_from_id' => $getData['id'],
				'come_from_type' => 7,
				'title' => saddslashes($getData['title']),
				'summary' => saddslashes($getData['summary']),
				'pos_id' => $getData['pos_id'],
				'www_url' => '',
				'img_url' => $getData['img_url'],
				'created' => REQUEST_TIME,
				'updated' => REQUEST_TIME,
				'pmark' => '',
				'cmark' => '',
				'city'	=> $this->_ad_city
		);
		return $this->_db->insert('oto_recommend', $arr);
	}
	
	public function checkRecommend($come_from_id, $pos_id) {
		return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '7' and `pos_id` = '{$pos_id}' limit 1") == 1;
	}	
}