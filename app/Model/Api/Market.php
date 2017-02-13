<?php
class Model_Api_Market extends Base
{
	private $_key;
	private static $_instance;

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_key = Core_Router::getModule() . '_' . Core_Router::getController(). '_' . Core_Router::getAction() . '_';
	}
	
	
	public function getMarket($postData, $city, $is_cache=false) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$page = intval($postData['page']);
		if( $page < 1 ){
			$page = 1;
		}
		$pagesize = PAGESIZE;
		$type = empty($postData['type'])?'':$postData['type'];//为benefits时，显示有优惠的商场列表；其他显示所有
		$key = "get_api_market_{$type}_{$lat}_{$lng}_{$city}_{$page}";
		$data = $this->getData($key);
		if( !$is_cache && empty($data) ) {
			if( $type=='benefits' ){
				$where = " and `has_selfpay`=1";
			}
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT market_id, market_name, market_address, logo_img,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market
						WHERE `city` = '{$city}' {$where}
						ORDER BY `sequence` desc, `distance` asc, `market_id` desc";
			} else {
				$sql = "SELECT market_id, market_name, market_address, logo_img 
						FROM oto_market
						WHERE `city` = '{$city}' {$where}
						ORDER BY `sequence` desc, `market_id` desc";
			}
			$data = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($data as &$row) {
				if(!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*)
								FROM oto_shop os
								LEFT JOIN oto_market om ON os.market_id = om.market_id
								WHERE os.shop_status <> '-1' AND os.shop_pid = '0' AND om.market_id = '{$row['market_id']}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
			}	
			$this->setData($key, $data);
		}
		return $data;
	}
}
?>