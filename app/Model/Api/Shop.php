<?php
class Model_Api_Shop extends Base {
	private static $_instance;
	private $_table = '';
	private $_key = '';

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
	
	public function getUserType($uid) {
		return $this->_db->fetchOne("select user_type from oto_user where user_id = '{$uid}' and is_del = 0 and user_status = 0");
	}
	
	public function getSaleMan($sid) {
		return $this->_db->fetchCol("select user_id from oto_user_shop_competence where shop_id = '{$sid}'");
	}
	
	public function getUploadNum($sid, $where) {
		return $this->_db->fetchOne("SELECT count(*) from oto_good WHERE shop_id = '{$sid}' and " . $where . " AND is_del = 0 AND good_status <> -1");
	}
	
	public function getImg($sid, $where, $limit) {
		// 获取该店铺下符合条件的宝贝ID
		$gids = $this->_db->fetchCol("select good_id from oto_good where shop_id = '{$sid}' and " . $where . " and is_del = 0 and good_status <> -1 order by created desc");
		$sql = "select good_id, img_url from oto_good_img where ".  $this->db_create_in($gids, 'good_id') ." and good_id <> 0 group by good_id order by is_first desc, good_img_id desc limit {$limit}";
		$info = $this->_db->fetchAll($sql);
		foreach ($info as &$row) {
			$row['shop_img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/good/290/' . $row['img_url'];
		}
		return $info;
	}
	
	public function getTicketOnline($sid) {
		$sql = "select count(ticket_id) from `oto_ticket` where `shop_id` = '{$sid}' and `ticket_status` = '1' and `is_auth` = '1' and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'";
		return $this->_db->fetchOne($sql);
	}
	
	public function getGoodList($sid, $type, $where, $page, $pagesize = 10) {
		$key = 'api_shop_auto_good_' . $sid. '_' . $type . '_' . $page;
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($type == 'all') {
				$isAuth = "";
			} else {
				$isAuth = " and is_auth = '{$type}'";
			}
			$sql = "SELECT good_id, good_name, user_id, user_name, is_auth, org_price, dis_price, created FROM oto_good 
					WHERE shop_id = '{$sid}' AND good_status <> -1 AND is_del = 0 " . $isAuth . " AND " . $where . " ORDER BY created desc";
			$goodList = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($goodList as &$row) {
		    	$img_url = $this->_db->fetchOne("select img_url from oto_good_img where good_id = '{$row['good_id']}' order by  is_first desc, good_img_id asc limit 1");
		    	if (!empty($img_url)) {
		    		$row['first_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/good/290/' . $img_url;
		    	} else {
		    		$row['first_img'] = '';
		    	}				
		    	$row['created'] = date('Y.n.j', $row['created']);
			}
			$data = $this->returnArr(count($goodList), $goodList);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getShopListByUserId($user_id) {
		$shopList = $this->select("`user_id` = '{$user_id}' and `shop_pid` = '0'  and `shop_status` <> '-1'", 'oto_shop', 'shop_id,shop_name,brand_id','sequence desc, shop_id asc');
		foreach ($shopList as &$row) {
			$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$row['brand_id']}'");
			if (empty($brandInfo['brand_name_zh'])) {
				$row['brand_name'] = $brandInfo['brand_name_en'];
			} elseif (empty($brandInfo['brand_name_en'])) {
				$row['brand_name'] = $brandInfo['brand_name_zh'];
			} else {
				$row['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
			}
			if (empty($row['brand_name'])) {
				$row['brand_name'] = '';
			}
		}
		return $shopList;
	}
	
	public function getShopListBySid($sid) {
		$shopList = $this->select("`shop_id` = '{$sid}' and `shop_pid` = '0'  and `shop_status` <> '-1'", 'oto_shop', 'shop_id,shop_name,brand_id','sequence desc, shop_id asc');
		foreach ($shopList as &$row) {
			$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$row['brand_id']}'");
			if (empty($brandInfo['brand_name_zh'])) {
				$row['brand_name'] = $brandInfo['brand_name_en'];
			} elseif (empty($brandInfo['brand_name_en'])) {
				$row['brand_name'] = $brandInfo['brand_name_zh'];
			} else {
				$row['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
			}
			if (empty($row['brand_name'])) {
				$row['brand_name'] = '';
			}
		}
		return $shopList;
	}
	
	public function getPermissionShopByUserId($uid) {
		return $this->_db->fetchRow("select * from oto_user_shop_competence where user_id = '{$uid}'");
	}
	
	public function getGoodView($gid) {
		$goods = Model_Api_Goods::getInstance()->getGoodsRow($gid);
		$shops = Model_Api_Goods::getInstance()->getShopRow($goods['shop_id']);
		$goods['img'] = $this->getDetailImage($gid);
		$goods['shop_name'] = $shops['shop_name'];
		$data = $this->returnArr(1, $goods);
		return $data;
	}
	
	public function auth($gid, $status) {
		return $this->_db->update('oto_good', array('is_auth' => $status), "good_id = '{$gid}'");
	}
	
	public function editShopNotice($sid, $notice) {
		$sid = intval($sid);
		$not = Custom_String::HtmlReplace($notice);
		$param = array(
				'notice' => $not,
				'updated' => REQUEST_TIME
		);
		if($sid && $sid > 0) {
			//修改店铺
			$shopResult = $this->_db->update('oto_shop', $param, "`shop_id` = '{$sid}'");
			return true;
		}
		return false;
	}
	
	public function getTicketList($sid, $page, $pagesize = 10) {
		$start = ($page - 1) * $pagesize;
		$where = " `shop_id` = '{$sid}' and `ticket_status` = '1' and `is_auth` = '1' and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'";
		$orderby = " order by `has_led` desc, `created` desc";
		$sql = "select ticket_uuid,ticket_id,ticket_title,ticket_type,ticket_summary,shop_id,shop_name,par_value,selling_price,valid_stime,valid_etime,total,has_led from oto_ticket where {$where} {$orderby}";
		$couponInfo = $this->_db->limitQuery($sql, $start, $pagesize);
		$ticketsort = $this->getTicketSortById(0, 'ticketsort');
		foreach ($couponInfo as $key=>$value) {
			$couponInfo[$key]['shop_id'] = $value['shop_id'];
			$couponInfo[$key]['shop_name'] = $value['shop_name'];
			if($ticketsort[$value['ticket_type']]['sort_detail_mark'] == 'coupon') {
				$couponInfo[$key]['dis_price'] = floor($value['par_value']);
				$couponInfo[$key]['left_num'] = $value['total'] - $value['has_led'];
			} elseif($ticketsort[$value['ticket_type']]['sort_detail_mark'] == 'voucher') {
				$couponInfo[$key]['dis_price'] = floor($value['selling_price']);
				$str = Custom_AuthTicket::get_ticket_details_by_guid($value['ticket_uuid']);
				$couponInfo[$key]['left_num'] = $str->data->Avtivities[0]->ProductStock;
			}
			$couponInfo[$key]['valid_time'] = date('Y', $value['valid_stime']).'年'.date('n.d', $value['valid_stime']).'-'.date('n.d', $value['valid_etime']).'日';
			$couponInfo[$key]['sort_name'] = $ticketsort[$value['ticket_type']]['sort_detail_name'];
		}
		return  $this->returnArr(count($couponInfo), $couponInfo);;
	}
	
	/**
	 * 获取店铺列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getShopList( $getData , $city , $is_cache = false ){
		$shop_name = Custom_String::HtmlReplace($getData["sname"]);//店铺名称
		$type = !empty($getData["type"])?$getData["type"]:"";//benefits有优惠的店铺列表
		$store_id = intval($getData["cid"]);//分类id
		$brand_id = intval($getData["bid"]);//品牌id
		$market_id = intval($getData["mid"]);//商场id
		$lat = $getData["lat"];
		$lng = $getData["lng"];
		$page = intval( $getData["page"] );
		if( $page < 1 ) $page =1;
		$pageSize = intval($getData["pagesize"])?intval($getData["pagesize"]):PAGESIZE;
		$start = ($page - 1)*$pageSize;
		$key = "{$this->_key}{$store_id}_{$brand_id}_{$market_id}_{$shop_name}_{$type}_{$lat}_{$lng}_{$page}_{$pageSize}";
		$data = $this->getData($key);
		if(!$is_cache || empty($data)) {
			$where = " `city`='{$city}' AND `shop_pid` = 0 AND `shop_status` <> -1";
			if( $store_id > 0 ){
				$where .= " AND `store_id` = '{$store_id}'";
			}
			if( $brand_id > 0 ){
				$where .= " AND `brand_id` = '{$brand_id}'";
			}
			if( $market_id > 0 ){
				$where .= " AND `market_id` = '{$market_id}'";
			}
			if( $type == "benefits" ){
				$where .= " AND `has_selfpay` = '1'";
			}
			if( $shop_name ){
				$where .= " AND `shop_name` LIKE '%{$shop_name}%'";
			}
			
			if( $lat && $lng ){
				$sql_shop = "SELECT `shop_id`, `shop_name`, `has_selfpay` AS is_hui, brand_id,
							12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
							FROM `oto_shop`
							WHERE {$where}
							ORDER BY has_selfpay desc, distance asc";
			}else{
				$sql_shop = "SELECT `shop_id`, `shop_name`, `has_selfpay` AS is_hui, brand_id, -1 AS distance 
							FROM `oto_shop`
							WHERE {$where}
							ORDER BY has_selfpay desc, sequence asc, created desc";
			}
			$sql_shop .= " LIMIT {$start},{$pageSize}";
			$data = $this->_db->fetchAll($sql_shop);
			foreach ( $data as &$row ){
				$brand_icon = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/wap/default_brand_icon.png';
				if( $row['brand_id'] > 0 ){
					$brand = Model_Home_Brand::getInstance()->getBrandDetail($row['brand_id'], $city);
					$brand_icon = $brand["brand_icon"];
				}
				$row['brand_icon'] = $brand_icon; 
				$row['ticket_title'] = '';
				$row['ticket_num'] = $row['total_pay'] = $row['pay_user_count'] = 0;
				if( $type == 'benefits' ){
					$selfpay = Model_Api_App::getInstance()->getShopSelfPay($row["shop_id"],$city);
					$row['ticket_title'] = $selfpay['ticket_title'];
					//获取该店已买单用户数，该店已买单金额
					$clientResult = Custom_AuthTicket::getMerchantStatInfoToUser(array('shop_id' => $row["shop_id"]));
					if( isset($clientResult) && $clientResult['Code'] == 1 ){
						$row["total_pay"] = $clientResult['Result']['OrderTotalPriceByCustomPrice'];
						$row["pay_user_count"] = $clientResult['Result']['OrderUserCountByCustomPrice'];
					}
				}else{
					$row['ticket_num'] = Model_Api_Goods::getInstance()->getTicketNumByShopId($row['shop_id']);
				}
			}
			$this->setData($key , $data);
		}
		return $data;
	}
	
	/**
	 * 获取店铺下的品牌券和商场券
	 * @param unknown_type $shop_id
	 * @param unknown_type $city
	 */
	public function getShopVouchers( $shop_id , $city ){
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'voucher');
		$sql = "select *
					from `oto_ticket`
					where `ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `city` = '{$city}'
						and `ticket_status` = '1' and `is_auth` = '1' and `is_show`=1
						and `start_time`<'".REQUEST_TIME."' and `end_time`>'".REQUEST_TIME."' 
					UNION 
					select ot.*
					from oto_ticket ot left join oto_ticket_shop  ots
					on ot.ticket_id = ots.ticket_id 
					where ot.`ticket_type`='{$ticket_type}' and ots.shop_id = '{$shop_id}' and ot.`city` = '{$city}'
						and ot.`ticket_status` = '1' AND ot.`is_auth` = '1' AND ot.`is_show` = 1 
						AND ot.`end_time` > '" . REQUEST_TIME . "' AND ot.`start_time` < '" . REQUEST_TIME . "'
					ORDER BY `sequence` asc, `created` desc";
		$data = $this->_db->fetchAll($sql);
		if( !empty($data) ){
			foreach ($data as &$row ) {
				$row['dis_price'] = $row['app_price'] ? $row['app_price'] : $row['selling_price'];
				$row['selling_price'] = $row['app_price'] ? $row['app_price'] : $row['selling_price'];
				$row['app_price'] = ($row['app_price'] ? $row['app_price'] : $row['selling_price']) * 100;
			
				$row['content'] = Custom_String::HtmlReplace($row['content'], 1);
				$row['valid_time'] = date('Y.n.j', $row['valid_stime']).'-'.date('n.j', $row['valid_etime']);
				$row['cover_img'] = $row['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/' . $row['cover_img'] : '';
				$hasStock = 0;
				$ticketObject = Custom_AuthTicket::get_ticket_details_by_guid($row['ticket_uuid']);
				if( isset($ticketObject) && $ticketObject->status == 1 ) {
					$hasStock = $ticketObject->data->Avtivities[0]->ProductStock; //还有多少库存
				}
				$row['ticket_desc'] = round($row['selling_price'])."元购".round($row['par_value'])."元券\n已售{$hasStock}张";
			}
		}
		return $data;
	}
	
	/**
	* 获取分店信息
	* @param unknown_type $shop_id
	* @param unknown_type $city
	*/
	public function getBranchShop($shop_id , $brand_id , $city ){
		$sql = "SELECT `shop_id`,`shop_name`,`shop_address`,`has_selfpay` AS is_hui FROM `oto_shop` WHERE `brand_id`='{$brand_id}' AND `shop_id`<>'{$shop_id}' AND `shop_status` <> -1 AND `shop_pid` = 0 AND `city`='{$city}'";
		$data = $this->_db->fetchAll($sql);
		$selpay_shop = array();//有游惠的店铺
		$ticket_shop = array();//有券的店铺
		$other_shop  = array();//其他店铺
		foreach( $data as &$row ){
			$row["ticket_num"] = Model_Api_Goods::getInstance()->getTicketNumByShopId($row['shop_id']); 
			if( $row["is_hui"] == 1 ){
				$selpay_shop[] = $row;
			}else if( $row["ticket_num"] > 0){
				$ticket_shop[] = $row;
			}else{
				$other_shop[] = $row;
			}
		}
		$data = array_merge($selpay_shop,$ticket_shop,$other_shop);
		return empty($data)?array():$data;
	}
	
	//查询该商场下面店铺的数量
	public function getShopNumByMarketId($market_id){
		return $this->_db->fetchOne("select count(shop_id) from `oto_shop` where `shop_status` <> -1 AND `shop_pid` = 0  AND `market_id` = '{$market_id}'");
	}
	
}