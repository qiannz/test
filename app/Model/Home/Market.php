<?php
class Model_Home_Market extends Base {

	private static $_instance;

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getMarketRow($mid, $city) {
		$key = 'wap_market_view_' . $mid;
		$data = $this->getData($key);
		if (empty($data)) {
			$sql ="select * from oto_market where market_id = '{$mid}' and city = '{$city}'";
			$marketInfo = $this->_db->fetchRow($sql);
			if ($marketInfo['head_img']) {
				$marketInfo['head_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $marketInfo['head_img'];
			} else {
				$marketInfo['head_img'] = '/images/wap/market_default_head.png';
			}
				
			if ($marketInfo['logo_img']) {
				$marketInfo['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $marketInfo['logo_img'];
			} else {
				$marketInfo['logo_img'] = '/images/wap/market_default_icon.png';
			}
			$data = $marketInfo;
			unset($marketInfo);
			$this->setData($key, $data);
		}
		return $data;
	}

	public function getShopBrand($mid, $city) {
		$key = 'wap_market_shop_brand_' . $mid;
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = "SELECT os.shop_id, os.shop_name, ob.brand_icon, ob.brand_name_zh, ob.brand_name_en FROM oto_shop os LEFT JOIN oto_brand ob
					ON os.brand_id = ob.brand_id
					WHERE os.market_id = '{$mid}'
					AND os.shop_status <> -1 AND os.shop_pid = 0
					AND os.city = '{$city}' AND ob.city = '{$city}'";
			$shop_brand = $this->_db->fetchAll($sql);
			foreach ($shop_brand as &$row) {
				$row['brand_icon'] = $row['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'] : '/images/wap/brand_default_icon.png';
				if($row['brand_name_zh'] && !$row['brand_name_en']) {
					$row['brand_name'] = $row['brand_name_zh'];
				} elseif(!$row['brand_name_zh'] && $row['brand_name_en']) {
					$row['brand_name'] = $row['brand_name_en'];
				} else {
					$row['brand_name'] = $row['brand_name_zh'] . ' ' . $row['brand_name_en'];
				}
			}
			$data = $shop_brand;		
			unset($shop_brand);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getMarketByRid($city) {
		$key = 'get_market_by_region_' . $city;
		$data = $this->getData($key);
		if (empty($data)) {
			$data = array();
			$region = $this->getRegion(0, false, $city);
			// 获取行政区以及行政区下面的商场 （有图和全部）
			foreach ($region as $region_id => $region_name) {
				$market_img = $this->_db->fetchAll("select market_id, market_name, logo_img from oto_market where region_id = '{$region_id}' and city = '{$city}' and logo_img <> '' order by sequence asc limit 5");
				foreach ($market_img as &$rowimg) {
					$rowimg['logo_img'] = $rowimg['logo_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $rowimg['logo_img'] : '/images/blank.png';
				}
				$data[] = array(
						'region_id' => $region_id,
						'region_name' => $region_name,
						'market_img' => $market_img,
						'market_no_img' => $this->_db->fetchAll("select market_id, market_name from oto_market where region_id = '{$region_id}' and city = '{$city}' order by sequence asc ")
						);
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getMarketByCid($cid, $city) {
		$key = 'web_get_market_by_cid_' . '_' .$cid. '_' . $city;
		$data = $this->getData($key);
		if (empty($data)) {
			$data = $this->_db->fetchAll("select * from oto_market where circle_id = '{$cid}' and city = '{$city}' order by sequence asc limit 8");
			foreach ($data as &$row) {
				$row['logo_img'] = $row['logo_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'] : '/images/blank.png';
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getCouponByMarketId($mid, $city) {
		$key = 'web_get_coupon_by_market_id_' . $mid . '_' . $city;
		$data = $this->getData($key);
		if (empty($data)) {
			$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			$sql_tids = "select a.ticket_id from oto_ticket_shop a left join oto_shop b on a.shop_id = b.shop_id where b.market_id = '{$mid}' group by a.ticket_id";
			$tidArrs = $this->_db->fetchCol($sql_tids);
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
					  FROM oto_ticket
					  WHERE `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1 AND `end_time` > '" . REQUEST_TIME . "' AND `start_time` < '" . REQUEST_TIME . "'
					  AND " . $this->db_create_in($tidArrs, 'ticket_id') . " and city = '{$city}' AND ticket_type = '{$ticket_type}'
			          ORDER BY created desc LIMIT 1";
			$data = $this->_db->fetchRow($c_sql);
			
			// 根据券ID获取适用店铺
			$shop_id_array = array($data['shop_id']);
			$shopIds = $this->_db->fetchCol("select shop_id from oto_ticket_shop where ticket_id = '{$data['ticket_id']}'");
			if ($shopIds) {
				$shop_id_array = array_merge($shop_id_array, $shopIds);
			}
			$s_sql = "select shop_id, shop_name from oto_shop where " . $this->db_create_in($shop_id_array, 'shop_id') . " and shop_status <> -1 and shop_pid = 0 and city = '{$city}' limit 3";
			$data['used_shop'] = $this->_db->fetchAll($s_sql);
			
			$data['selling_price'] = floor($data['selling_price']);
			$data['par_value'] = floor($data['par_value']);
			$data['cover_img'] = $data['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $data['cover_img'] : '/images/blank.png';
			$this->setData($key, $data);
		}
		return $data;	
	}	
	
	public function getShopByMarketId ($mid, $city) {
		$key = 'web_get_shop_by_market_id_' . $mid . '_' . $city;
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = "SELECT s.shop_id, s.shop_name, s.brand_id, s.brand_name, m.logo_img FROM oto_shop s
					LEFT JOIN oto_market m ON s.market_id = m.market_id
					WHERE s.market_id = '{$mid}' AND s.city = '{$city}' AND s.shop_status <> -1 AND s.shop_pid = 0 AND m.city = '{$city}'
					ORDER BY s.created desc";
			$data = $this->_db->fetchAll($sql);
			foreach ($data as &$row) {
				$sql_brand_icon = "select ob.brand_icon from oto_shop os left join oto_brand ob
									on os.brand_id = ob.brand_id 
									where os.shop_id = '{$row['shop_id']}' 
									AND os.city = '{$city}' AND os.shop_status <> -1 AND os.shop_pid = 0 AND ob.city = '{$city}'
									ORDER BY os.created desc limit 1";
				$brand_icon = $this->_db->fetchOne($sql_brand_icon);
				$row['brand_icon'] = $brand_icon ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brand_icon : '/images/blank.png';
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	//查询出关注该品牌的数量
	public function getUserNumByMarketId($market_id){
		return $this->_db->fetchOne("select count(favorite_id) from `oto_market_favorite` where `market_id` = '{$market_id}'");
	}
	
	public function hadFavoriteMarket($user_id,$market_id){
		$sql = "select 1 from oto_market_favorite where market_id = '{$market_id}' and user_id = '{$user_id}'";
		return $this->_db->fetchOne($sql) == 1;
	}
	
	public function getHotCircle($city, $limit = 9) {
		$key = "web_get_hot_circle_" . $city;
		$data = $this->getData($key);
		if (empty($data)) {
			$hotCircle = $this->_db->fetchAll("select circle_id, circle_name from oto_circle where city='{$city}' and is_show = 1 order by sequence asc, created desc limit {$limit}");
			$data = array();
			foreach ($hotCircle as &$row) {
				$data[$row['circle_id']] = array(
						'id' => $row['circle_id'],
						'name' => $row['circle_name'],
						'market' => $this->getMarketByCid($row['circle_id'], $city),
				);
			}
			
			$this->setData($key, $data);
		}
		return $data;
	}
}