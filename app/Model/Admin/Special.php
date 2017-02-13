<?php
class Model_Admin_Special extends base{
	private static $_instance;
	protected $_table = 'special_content';
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
		return $this->_db->fetchOne("select count(*) from `".$this->_table."` where 1 ".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		$isDel = false;
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'title':
							if($value) {
								$where .= " and `title` like '".trim($value)."%'";
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
		$order = " order by `created` desc";
		$this->_order = $order;
	}
	
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	public function postSpecial($postData){
		$param = array();
		$param["title"] = Custom_String::HtmlReplace( $postData["title"], 1 );
		if( $postData["cover_img"] ){
			$param["cover_img"] = $postData["cover_img"];
		}
		$param["content"] = $postData['content'];
		//$param["wap_content"] = Custom_String::HtmlReplace( $postData["wap_content"], 1 );
		$city = !$postData['city'] ? $this->_ad_city : $postData['city'];
		$param['city'] = $city;
		$param["ip"] = CLIENT_IP;
		$sid = intval( $postData["sid"] );
		$gids = trim( $postData["gids"] , "," );
		//编辑
		if($sid){
			$insert_id = $sid;
			$param = array_merge($param,array("updated"=>REQUEST_TIME));
			$this->_db->update( $this->_table, $param , "`special_id`='{$sid}'" );
		} 
		//新增
		else {
			$param = array_merge($param,array("created"=>REQUEST_TIME));
			$insert_id = $this->_db->insert( $this->_table , $param );
		}
		
		if($gids) {
    		$sql = '';
    		$gidArr = explode(',', $gids);
    		foreach($gidArr as $good_id) {
    			$sql .= "('{$insert_id}', '{$good_id}'),";
    		}
    		 
    		if($sql) {
    			$sql = 'INSERT INTO `special_good` (`special_id`, `good_id`) values ' . substr($sql, 0, -1).
    					' ON DUPLICATE KEY UPDATE `good_id` = values(`good_id`)';
    			$this->_db->query($sql);
    		}
    	}
    	
    	return array('insert_id' => $insert_id);
	}
	
	//上传wap图片
	public function wapUploadImg($wap_img_url, $special_id = 0, $user_id = 0) {
		$sql = $sqlstr = '';
		if(!empty($wap_img_url)) {
			if(is_array($wap_img_url)) {
				foreach($wap_img_url as $img_url) {
					if($img_url) {
						$sqlstr .= "('{$special_id}', '{$user_id}', '{$img_url}', '". REQUEST_TIME ."'), ";
					}
				}
			} else {
				$sqlstr .= "('{$special_id}', '{$user_id}', '{$wap_img_url}', '". REQUEST_TIME ."'), ";
			}
	
			if($sqlstr) {
				$sql = "insert into `special_wap_img` (`special_id`, `user_id`, `img_url`, `created`) values " . substr($sqlstr, 0, -2);
				$query = $this->_db->query($sql);
				if($query) {
					$insertId = $this->_db->lastInsertId();
					return $insertId ? $insertId : 0;
				}
			}
		}
		return 0;
	}
	
	//修改排序
	public function img_ajax_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
		$result = $this->_db->update('special_wap_img',array($column => $value), "`id` = $id");
		if($result){
			exit(json_encode(true));
		}
	}
	
    //根据special_id获取折扣信息
    public function getSpecialRow( $special_id ){
    	$specialRow = $this->select("`special_id` = '{$special_id}'", $this->_table, '*', '', true);
    	return $specialRow ? $specialRow : array();
    }
    
    //根据special_id获取wap图片列表
    public function getWapImgList( $special_id ){
    	$wapImgList = $this->select("`special_id` = '{$special_id}'", 'special_wap_img', '*', 'sequence asc, created asc');
    	return $wapImgList;
    }
    
    //删除专题
    public function del($special_id) {
    	$sql = "update `{$this->_table}` set `is_del` = '1' where `special_id` in ({$special_id})";
    	return $this->_db->query($sql);
    }
    
    //取消删除专题
    public function unDel($special_id) {
		$sql = "update `{$this->_table}` set `is_del` = '0' where `special_id` in ({$special_id})";
    	return $this->_db->query($sql);    
    }
    
    //获取品牌列表
    public function getGoodList($filter, $city){
    	$where = '';
    	if($filter) {
	    	$filter = Custom_String::HtmlReplace( $filter, 1 );
	    	$where = " and `ticket_title` like '%{$filter}%'";
    	}
    	//商城商品，审核通过，上架，显示
		$where .= Model_Api_App::getInstance()->commodityWhereSql('commodity');    	
		$sql = "select ticket_id, ticket_title
				from `oto_ticket`
				where city = '{$city}' {$where} order by created desc";
    	$data = $this->_db->fetchAll($sql);
    	return $data ? $data : array();
    }
    
    public function getGoodListById($special_id) {
    	$sql = "select B.ticket_id as id, B.ticket_title as name
    			from `special_good` A
    			left join `oto_ticket` B on A.good_id = B.ticket_id
    			where A.special_id = '{$special_id}'
    			order by B.sequence asc, B.created desc";
    	$data = $this->_db->fetchAll($sql);
    	return $data ? $data : array();
    }
    
    public function checkRecommend($come_from_id, $pos_id) {
    	return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '6' and `pos_id` = '{$pos_id}' limit 1") == 1;
    }  

    public function recommend($getData) {
    	$arr = array(
    			'come_from_id' => $getData['sid'],
    			'come_from_type' => 6,
    			'title' => saddslashes($getData['title']),
    			'summary' => saddslashes($getData['summary']),
    			'pos_id' => $getData['pos_id'],
    			'www_url' => $getData['www_url'],
    			'img_url' => $getData['img_url'],
    			'created' => REQUEST_TIME,
    			'pmark' => !$getData['pmark'] ? 'discount' : $getData['pmark'],
    			'cmark' => !$getData['cmark'] ? 'special_view' : $getData['cmark'],
    			'city' => $getData['city'] ? $getData['city'] : $this->_ad_city
    	);
    	return $this->_db->insert('oto_recommend', $arr);
    }    
}