<?php
class Model_Admin_Batchgood extends Base {
	private static $_instance;
	private $_where;
	private $_order;
	private $_table = 'oto_ticket';
	
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
		$sql = "SELECT count(*) 
				FROM  `oto_ticket_batch` AS A  
				LEFT JOIN `oto_ticket` AS B ON B.`ticket_id` = A.`ticket_id` 
				WHERE {$this->_where}";
		return $this->_db->fetchOne($sql);
	}
	
	public function setWhere($getData) {
		$where = "`city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'gname':
							if($value){
								$where .= " and B.`ticket_title` LIKE '%".trim($value)."%'";
							}
							break;
						case 'batch':
							if($value) {
								$where .= " and A.`good_batch` = '".trim($value)."'";
							}
							break;
						case 'bname':
							if($value) {
								$brandRow = Model_Admin_Brand::getInstance()->getBrandRowByName($value, $this->_ad_city);
								if($brandRow) {
									$where .= " and B.`brand_id` = '{$brandRow['brand_id']}'";
								}
							}
							break;
						case 'stime':
							if(strtotime($value . ':00:00') !==  false) {
								$where .= " and B.`created` >= '". strtotime($value . ':00:00') ."'";
							}
							break;
						case 'etime':
							if(strtotime($value . ':00:00') !==  false) {
								$where .= " and B.`created` <= '". strtotime($value . ':00:00') ."'";
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
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "SELECT *
				FROM `oto_ticket_batch` AS A 
				LEFT JOIN `oto_ticket` AS B ON B.`ticket_id` = A.`ticket_id`
				WHERE {$this->_where} 
				{$this->_order}";
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ( $data as &$row ){
			$shop_info = $this->getShopFieldById($row['shop_id']);
			$row['shop_name'] = $shop_info['shop_name'];
			$row['brand_name'] = $this->getBrand($row['brand_id']);
		}
		return $data ? $data : array();
	}
	
	//获取商品的批次信息
	public function getGooodBatchRow( $ticket_id ){
		$sql = "SELECT * FROM `oto_ticket_batch` WHERE `ticket_id`='{$ticket_id}'";
		return $this->_db->fetchRow($sql);
	}
	
	//获取商品的sku信息
	public function getGoodSku( $ticket_id , $good_color = '' , $good_size = '' ){
		$sql = "SELECT * FROM `oto_ticket_sku` WHERE `ticket_id`='{$ticket_id}'";
		if( $good_color ){
			$sql .= " AND `good_color` = '{$good_color}'";
		}
		if( $good_size ){
			$sql .= " AND `good_size` = '{$good_size}'";
		}
		return $this->_db->fetchAll($sql);
	}
	
	//修改商品
	public function updateGoodInfo( $getData ){
		$ticket_id = intval($getData['tid']);
		if( !$ticket_id ){
			return false;
		}
		$shop_id = intval($getData['sid']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$selling_points = $getData['selling_points'];
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_class = intval($getData['ticket_class']);
		$user_name_limit = intval($getData['user_name_limit']);
		$mobile_limit = intval($getData['mobile_limit']);
		$limit_count = intval($getData['limit_count']);
		$limit_unit = intval($getData['limit_unit']);
		$start_time = strtotime($getData['start_time']);
		$end_time = strtotime($getData['end_time']);
		$p_value = $getData['p_value'];
		$s_value = $getData['s_value'];
		$a_price = $getData['a_price'];
		$is_free = intval($getData['is_free']);
		$ticket_summary = $getData['ticket_summary'];
		$wap_content = $getData['wap_content'];
		$free_shipping = $getData['free_shipping'];
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		
		$good_number = $getData['good_number'];
		$good_barcode = trim($getData['good_barcode']);
		$good_texture = $getData['good_texture'];
		$good_match_crowd = $getData['good_match_crowd'];
		$good_years = $getData['good_years'];
		$good_season = $getData['good_season'];
		$good_color = $getData['good_color'];
		$good_size = $getData['good_size'];
		
		$param = array(
				'ticket_title'   => $ticket_title,
				'ticket_sort'    => $ticket_sort,
				'ticket_class'   => $ticket_class,
				'par_value'      => $p_value,
				'selling_price'  => $s_value,
				'app_price'      => $a_price,
				'is_free'        => $is_free,
				'selling_points' => $selling_points,
				'ticket_summary' => $ticket_summary,
				'free_shipping'  => $free_shipping,
				'wap_content'    => $wap_content,
				'start_time'     => $start_time,
				'end_time'       => $end_time,
				'limit_count'    => $limit_count,
				'limit_unit'     => $limit_unit,
				'is_auth'        => $is_auth,
				'is_show'        => $is_show,
				'updated'		 => REQUEST_TIME
				);
		$batchParam = array(
				'user_name_limit'    => $user_name_limit,
				'mobile_limit'       => $mobile_limit,
				'good_number'        => $good_number,
				'good_barcode'       => $good_barcode,
				'good_texture'       => $good_texture,
				'good_match_crowd'   => $good_match_crowd,
				'good_years'         => $good_years,
				'good_season'        => $good_season
				);
		
		$flag = $this->_db->update( 'oto_ticket', $param, array('ticket_id'=>$ticket_id) );
		if( $flag ){
			$flag = $this->_db->update( 'oto_ticket_batch', $batchParam , array('ticket_id'=>$ticket_id) );
		}
		return $flag;
	}
	
	public function updateMarketNum( $ticket_id , $color , $size , $num ){
		$sql = "UPDATE `oto_ticket_sku` 
				SET `good_warehouse_num`=`good_warehouse_num`-{$num},`good_market_num`=`good_market_num`+{$num} 
				WHERE `ticket_id`='{$ticket_id}' AND `good_color`='{$color}' AND `good_size`='{$size}' AND `good_warehouse_num`>={$num}";
		$res = $this->_db->query($sql);
		return is_numeric($res)?true:false;
	}
	
	public function updateRollbackNum( $ticket_id , $color , $size , $num ){
		$sql = "UPDATE `oto_ticket_sku`
				SET `good_warehouse_num`=`good_warehouse_num`-{$num},`good_rollback_num`=`good_rollback_num`+{$num}
				WHERE `ticket_id`='{$ticket_id}' AND `good_color`='{$color}' AND `good_size`='{$size}' AND `good_warehouse_num`>={$num}";
		$res = $this->_db->query($sql);
		return is_numeric($res)?true:false;
	}
	
	public function updateWarehouseNum( $ticket_id , $color , $size , $num ){
		$sql = "UPDATE `oto_ticket_sku`
		SET `good_market_num`=`good_market_num`-{$num},`good_warehouse_num`=`good_warehouse_num`+{$num}
		WHERE `ticket_id`='{$ticket_id}' AND `good_color`='{$color}' AND `good_size`='{$size}' AND `good_market_num`-`good_sold_num`>={$num}";
		$res = $this->_db->query($sql);
		return is_numeric($res)?true:false;
	}
}