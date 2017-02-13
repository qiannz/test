<?php
class Model_Api_App extends Base {
	
	private static $_instance;
	private $_key = '';
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_key = Core_Router::getModule() . '_' . Core_Router::getController(). '_' . Core_Router::getAction() . '_';
	}
	
	public function getPosIdByMark($city, $pmark, $cmark) {
		$position = include VAR_PATH . 'config/position.php';
		$pos_id =  $position[$city][$pmark]['child'][$cmark]['pos_id'];
		return $pos_id ? $pos_id : 0;
	}
	/**
	 * APP首页新版4.0
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getHomeList($getData, $city) {
		
		$data = array();
		
		//大图标  app_home_large_icons
		$data['app_home_large_icons'] = $this->getListByMark($city, 'app_home_version_four', 'app_home_large_icons', 3);
		//小图标  app_home_small_icons
		$data['app_home_small_icons'] = $this->getListByMark($city, 'app_home_version_four', 'app_home_small_icons', 5);
		//限量秒杀 	app_home_limited_spike
		$data['app_home_limited_spike'] = $this->getListByMark($city, 'app_home_version_four', 'app_home_limited_spike', 1);
		//每日特卖 	app_home_daily_deals
		$data['app_home_daily_deals'] = $this->getListByMark($city, 'app_home_version_four', 'app_home_daily_deals', 1);
		//抢购代金券 	app_home_buying_vouchers
		$data['app_home_buying_vouchers'] = $this->getListByMark($city, 'app_home_version_four', 'app_home_buying_vouchers', 1);
		//大牌驾到 	app_home_big_drive_to
		$data['app_home_big_drive_to'] = $this->getListByMark($city, 'app_home_version_four', 'app_home_big_drive_to', 1);
		//推荐券 	app_home_recommended_coupons
		$data['app_home_recommended_coupons'] = $this->getRecommendMore('app_home_recommended_coupons', 1, $city);
		$data['app_home_recommended_coupons'] = array_slice($data['app_home_recommended_coupons'], 0, 4);
		
		//为您推荐 	app_home_recommended_for_you
		$data['app_home_recommended_for_you'] = $this->getRecommendMore('app_home_recommended_for_you', 1, $city);
		
		return $data;
	}
	/**
	 * 获取更多为你推荐
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getRecommendMore($mark, $page = 1, $city = 'sh') {
		$time = REQUEST_TIME;
		$pos_id = $this->getPosIdByMark($city, 'app_home_version_four', $mark);
		$start = ($page - 1) * PAGESIZE;
		$sql = "select `A`.*, `B`.`ticket_type`, `B`.`par_value`, `B`.`end_time`,`B`.`ticket_id`,`B`.`ticket_uuid`, `C`.`brand_icon`, `C`.`brand_name_zh`, `C`.`brand_name_en` 
				from `oto_recommend` as `A` 
				left join `oto_ticket` as `B` on `A`.`come_from_id` = `B`.`ticket_id`
				left join `oto_brand` as `C` on `B`.`brand_id` = `C`.`brand_id`
				where `A`.`pos_id` = '{$pos_id}' and `B`.`is_auth` = '1' and `B`.`is_show` = '1' and `B`.`ticket_status` = '1' and `B`.`start_time` < '{$time}' and `B`.`end_time` > '{$time}' 
				order by `A`.`sequence` asc, `A`.`created` desc";
		$tmpArr = $this->_db->limitQuery($sql, $start, PAGESIZE);
		
		foreach($tmpArr as & $tmpItem) {
			
			
			if($tmpItem['brand_icon']) {				
				$img_tmp = $tmpItem['brand_icon'];
				$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $img_tmp;
				$dir_url = ROOT_PATH . 'web/data/brand/' . $img_tmp;
			} elseif($tmpItem['img_url']) {
				$img_tmp = $tmpItem['img_url'];
				$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $img_tmp;
				$dir_url = ROOT_PATH . 'web/data/recommend/' . $img_tmp;
			} else {
				$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/recommend_for_you.png';
				$dir_url = ROOT_PATH . 'web/data/app/recommend_for_you.png';
			}
			unset($tmpItem['brand_icon'], $tmpItem['img_url']);
			
			$tmpItem['img_url'] =  $www_url;
			list($tmpItem['width'], $tmpItem['height']) = getimagesize($dir_url);
			
			
			//剩余天数
			if($tmpItem['end_time'] < REQUEST_TIME) {
				$tmpItem['the_remaining_days'] = 0;
			} else {
				$tmpItem['the_remaining_days'] = floor(($tmpItem['end_time'] - REQUEST_TIME) / ( 3600 * 24 ));
			}
			
			//默认定位
			if($tmpItem['come_from_type'] == 2 || (empty($tmpItem['pmark']) && empty($tmpItem['cmark']))) {
				$ticket_type_name = $this->getTicketSortById($tmpItem['ticket_type'], 'ticketsort', 'sort_detail_mark');
				if($ticket_type_name == 'voucher') {
					$tmpItem['pmark'] = 'voucher';
					$tmpItem['cmark'] = 'voucher_view';
				} elseif($ticket_type_name == 'buygood') {
					$tmpItem['pmark'] = 'buy';
					$tmpItem['cmark'] = 'nine_buy_view';
				}
			}
			
			//品牌名称
			$tmpItem['brand_name'] = $tmpItem['brand_name_zh'] ? $tmpItem['brand_name_zh'] : $tmpItem['brand_name_en'];
			
			unset($tmpItem['brand_name_zh'], $tmpItem['brand_name_en']);
		}
		
		
		return $tmpArr ? $tmpArr : array();
	}
	
	/**
	 * 根据城市，推荐标识，获取推荐内容
	 */
	public function getListByMark($city, $pmark, $cmark, $limit) {
		$data = $tmpArr = array();
		$pos_id = $this->getPosIdByMark($city, $pmark, $cmark);
		$tmpArr = $this->select("`pos_id` = '{$pos_id}'", 'oto_recommend', '*', 'sequence asc, created desc', $limit);
		if($limit == 1 && $tmpArr) {
			$data[] = $tmpArr;
		} else {
			$data = $tmpArr;
		}
		unset($tmpArr);
		foreach($data as & $row) {
			if($row['img_url']) {
				$img_tmp = $row['img_url'];
				$row['img_url'] =  $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $img_tmp;
				list($row['width'], $row['height']) = getimagesize(ROOT_PATH . 'web/data/recommend/' . $img_tmp);
			} else {
				$row['img_url'] = '';
				$row['width'] = $row['height'] = 0;
			}
			
			//额外参数
// 			if( 'app_home_six_oneyuanpurchase' == $cmark ) {
// 				$row['extra'] = array(
// 							array(
// 								'text' => '众筹说明',
// 								'url' => 'http://promo.mplife.com/other/20151103/',
// 								'icon' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/active/oneyuanpurchase/explain.png'		
// 							),
// 							array(
// 								'text' => '众筹订单',
// 								'url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/oneyuanpurchase/order-list',
// 								'icon' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/active/oneyuanpurchase/order.png'
// 							)
// 						);
// 			}
			
			if( 'discount' != $pmark ){
				if($row['come_from_id']) {
					$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($row['come_from_id']);
					if($ticketRow) {
						$row['ticket_uuid'] = $ticketRow['ticket_uuid'];
					}
				}
			}
		}
		return $data ? $data : array(); 
	}
	
	
	/**
	 * 根据券ID获取对应的品牌信息
	 * @param unknown_type $ticket_id
	 */
	public function getBrandRowByTicketId($ticket_id) {
		$sql = "select `A`.`par_value`, `A`.`end_time`, `B`.* from `oto_ticket` as `A`
				left join `oto_brand` as `B` on `A`.`brand_id` = `B`.`brand_id`
				where `A`.`ticket_id` = '{$ticket_id}' and `A`.`brand_id` <> '0'
				and `A`.`is_auth` = '1' and `A`.`is_show` = '1' and `A`.`ticket_status` = '1' limit 1";
		$brandRow = $this->_db->fetchRow($sql);
		
		if($brandRow) {
			//品牌头像
			if($brandRow && $brandRow['brand_logo']) {
				$brandRow['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandRow['brand_logo'];
				
			}
			//剩余天数
			if($brandRow['end_time'] < REQUEST_TIME) {
				$brandRow['the_remaining_days'] = 0;
			} else {
				$brandRow['the_remaining_days'] = floor(($brandRow['end_time'] - REQUEST_TIME) / ( 3600 * 24 ));
			}
		}
		
		return $brandRow ? $brandRow : array();
	}
	
	/**
	 * 获取团购推荐信息
	 * @param unknown_type $identifier
	 * @param unknown_type $page
	 * @param unknown_type $city
	 * @param unknown_type $pagesize
	 */
	public function getTuanRecommend($identifier, $page, $city, $pagesize = PAGESIZE) {
		$data = $dataTmp = array();
		//缓存键值
		$key = $this->_key . '_' . $identifier  . '_' . $page . '_' . $city;
		$data = $this->getData($key);
	
		if(empty($data)) {
			$pos_id = $this->getPosId($identifier, $city);
			if($identifier == 'buygood_hot') {
				$identifier = 'buygood_img_small';
			}
			$identifierRow = $this->getTheRecommendedPosition('buygood', $identifier, true, $city);
			$start = ($page - 1) * $pagesize;
			
			$where = "B.ticket_status = '1' 
						and B.is_auth = '1' 
						and B.is_show = '1' 
						and B.end_time > '". REQUEST_TIME ."' 
						and A.city = '{$city}'
						and A.pos_id = '{$pos_id}'";
			
			$sqlC = "select count(A.recommend_id)
					from `oto_recommend` A
					left join `oto_ticket` B on A.come_from_id = B.ticket_id
					left join `oto_ticket_info` C on B.ticket_id = C.ticket_id
					where {$where}";
			$totalNum = $this->_db->fetchOne($sqlC);
			$sql = "select  A.title, A.img_url, A.www_url, B.ticket_id,B.ticket_uuid,B.start_time,B.end_time, B.par_value, B.selling_price, B.app_price, C.file_img_small, B.shop_id, B.shop_name, B.total
					from `oto_recommend` A
					left join `oto_ticket` B on A.come_from_id = B.ticket_id
					left join `oto_ticket_info` C on B.ticket_id = C.ticket_id
					where {$where}
					order by A.sequence asc, B.created desc";
			$dataTmp = $this->_db->limitQuery($sql, $start, $pagesize);
				
			foreach($dataTmp as & $row) {
				if($row['img_url']) {
					$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/recommend/' . $row['img_url'];
				} else {
					$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/' . $row['file_img_small'];
				}
				$row['width'] = $identifierRow['width'];
				$row['height'] = $identifierRow['height'];
		
				if($row['app_price'] < 0) {
					$row['selling_price'] = 0;
				} elseif($row['app_price'] > 0) {
					$row['selling_price'] = $row['app_price'];
				}
				
				//商品状态
				$row['surplus'] = $row['sold'] = 0;
				if($row['start_time'] > REQUEST_TIME) {
					$row['good_status'] = 0; //未开始
				} else {
					$row['good_status'] = 1; //进行中
					$ticketObject = Custom_AuthTicket::get_ticket_details_by_guid($row['ticket_uuid']);
					if($ticketObject->status != 0) {
						$row['surplus'] = $ticketObject->data->Avtivities[0]->ProductStock; // 剩余
						$row['total'] 	= $ticketObject->data->Avtivities[0]->ProductNum; // 总数
						$row['sold'] = $ticketObject->data->Avtivities[0]->ProductDisplaySale; // 售出
					}
				}
			}
			$data['data'] = $dataTmp;
			$data['totalNum'] = $totalNum;
			$data['description'] = '今日新品';
			$this->setData($key, $data);
		}
	
		return $data;
	}
	
	public function getTuanByStoreId($store_id, $page, $city, $is_new = false, $pagesize = PAGESIZE) {
		$data = $dataTmp = array();
		//缓存键值
		$key = $this->_key . '_' . $store_id  . '_' . $page . '_' . $city . ($is_new ? '_totay' : '');
		$data = $this->getData($key);
		if(empty($data)) {
			$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'buygood');
			$identifierRow = $this->getTheRecommendedPosition('buygood', 'buygood_img_small', true, $city);
			$start = ($page - 1) * $pagesize;
			
			//今日新品
			if($is_new) {
				$todayTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				$where = "A.ticket_status = '1'
				and A.is_auth = '1'
				and A.is_show = '1'
				and A.created > '". $todayTime ."'
				and A.city = '{$city}'
				and A.ticket_type = '{$ticket_type}'";
			} else {
				$where = "A.ticket_status = '1'
							and A.is_auth = '1'
							and A.is_show = '1'
							and A.end_time > '". REQUEST_TIME ."'
							and A.city = '{$city}' " .( 
								$store_id ? "and A.ticket_sort = '{$store_id}'" : ""
								)
							. "and A.ticket_type = '{$ticket_type}'";
			}
					
			$sqlC = "select count(A.ticket_id)
						from `oto_ticket` A 
						left join `oto_ticket_info` B on A.ticket_id = B.ticket_id
						where {$where}";
			$totalNum = $this->_db->fetchOne($sqlC);
			
			$sql = "select A.ticket_id, A.ticket_title as title, A.ticket_uuid, A.start_time, A.end_time, A.par_value, A.selling_price, A.app_price, B.file_img_small, A.shop_id, A.shop_name, A.total
					from `oto_ticket` A
					left join `oto_ticket_info` B on A.ticket_id = B.ticket_id
					where {$where}
					order by A.sequence asc, A.created desc";
					$dataTmp = $this->_db->limitQuery($sql, $start, $pagesize);
	
			foreach($dataTmp as & $row) {
				$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/' . $row['file_img_small'];
				$row['width'] = $identifierRow['width'];
				$row['height'] = $identifierRow['height'];
		
				if($row['app_price'] < 0) {
					$row['selling_price'] = 0;
				} elseif($row['app_price'] > 0) {
					$row['selling_price'] = $row['app_price'];
				}
			}
			$data['data'] = $dataTmp;
			$data['totalNum'] = $totalNum;
			$data['description'] = '今日新品';
			$this->setData($key, $data);
		}
	
		return $data;				
	}
	
	/**
	 * 获取分类对应的团购商品数量
	 * @param unknown_type $city
	 */
	public function getStoreNum($city) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(empty($data)) {
			$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'buygood');
			$data = Model_Api_Goods::getInstance()->getAppStore($city);
			foreach($data as & $row) {
				$sql = "select count(ticket_id) from `oto_ticket` 
							where ticket_status = '1' 
							and is_auth = '1' 
							and is_show = '1' 
							and end_time > '". REQUEST_TIME ."' 
							and city = '{$city}' 
							and ticket_sort = '{$row['id']}'
							and ticket_type = '{$ticket_type}'";
				$row['num'] = $this->_db->fetchOne($sql);
			}
			
			$this->setData($key, $data);
		}
		return $data;
	}
	
	
	public function getVersionInfo($getData) {
		$type = strtolower(trim($getData['type'])); // 手机类型
		$version = trim($getData['version']); // 版本号
		$channel = trim($getData['channel']); // 安卓来源
		
		if ($type == 'ios') {
			$data = $this->_db->fetchRow("select * from app_version where type = '{$type}' and version = '{$version}' limit 1");
		} else {
			$data = $this->_db->fetchRow("select * from app_version where type = '{$type}' and version = '{$version}' and channel = '{$channel}' limit 1");
		}
		return $data ? $data : array();
	}

	/**
	 * 根据父分类，获取字分类
	 * @param unknown_type $pmark
	 * @param unknown_type $city
	 * @param unknown_type $limit
	 */
	public function getBrandCategoryList($pmark, $city, $limit) {
		$listArray = array();
		$posIdentifierArray = $this->getTheRecommendedPosition($pmark, null, true, $city);	
		foreach($posIdentifierArray as $key =>$identifierItem) {
			$listArray[$key] = $identifierItem;
			$listArray[$key]['child'] = $this->select(
					"`pos_id` = '{$identifierItem['pos_id']}'", 'oto_recommend', 
					'come_from_id, come_from_type, title, summary, www_url, img_url', 
					'sequence asc, created desc', 
					$limit
			);
		}
		
		return $listArray;
	}
	/**
	 * 判断品牌下是否有在售现金券
	 * @param unknown_type $brand_id
	 */
	public function hasTicketByBrandId($brand_id, $city) {
		$sql = "select 1 from `oto_ticket` where `brand_id` = '{$brand_id}' and `city` = '{$city}'" . $this->couponWhereSql() . ' limit 1';
		return $this->_db->fetchOne($sql) == 1;
	}
	
	/**
	 * 判断商场下是否有在售现金券
	 * @param unknown_type $market_id
	 * @param unknown_type $city
	 */
	public function hasTicketByMarketId($market_id, $city) {
		$sql = "select 1 from `oto_ticket` where `market_id` = '{$market_id}' and `city` = '{$city}'" . $this->couponWhereSql() . ' limit 1';
		return $this->_db->fetchOne($sql) == 1;
	}
	
	/**
	 * 判断商场下是否有在售现金券
	 * @param unknown_type $market_id
	 * @param unknown_type $city
	 */
	public function hasTicketByMarket($mid, $city) {
		$sql_tids = "select a.ticket_id from oto_ticket_shop a left join oto_shop b on a.shop_id = b.shop_id where b.market_id = '{$mid}'";
		$tidArrs = $this->_db->fetchCol($sql_tids);
		$c_sql = "SELECT 1 FROM oto_ticket
					WHERE  ". $this->db_create_in($tidArrs, 'ticket_id') . $this->couponWhereSql() . "
					AND city = '{$city}' AND TICKET_CLASS = 1 limit 1";
		return $this->_db->fetchOne($c_sql) == 1;
	}
	/**
	 * 判断店铺下是否有在售现金券
	 * @param unknown_type $shop_id
	 */
	public function hasTicketByShopId($shop_id, $city) {
		$sql = "select 1 from `oto_ticket` where `shop_id` = '{$shop_id}' and `city` = '{$city}'" . $this->couponWhereSql() . ' limit 1';
		return $this->_db->fetchOne($sql) == 1;
	}
	/**
	 * 获取商场下的在售现金券
	 * @param unknown_type $market_id
	 */
	public function getTicketByMarketId($market_id, $city) {
		$data = array();
		$sql = "select * from `oto_ticket` where `market_id` = '{$market_id}' and `city` = '{$city}'" . $this->couponWhereSql() . ' order by sequence asc, created desc';
		$ticketArray = $this->_db->fetchAll($sql);
		foreach($ticketArray as $key => $row) {
			$data[$key]['ticket_id'] = $row['ticket_id'];
			$data[$key]['ticket_uuid'] = $row['ticket_uuid'];
			$data[$key]['ticket_title'] = $row['ticket_title'];
			$data[$key]['selling_price'] = $row['app_price'] > 0 ? $row['app_price'] : $row['selling_price'];
			$ticketObject = Custom_AuthTicket::get_ticket_details_by_guid($row['ticket_uuid']);
			if(is_object($ticketObject)) {
				$data[$key]['surplus'] = $ticketObject->data->Avtivities[0]->ProductStock; // 剩余
				$data[$key]['total'] = $ticketObject->data->Avtivities[0]->ProductNum; // 总数
				$data[$key]['has_led'] = $ticketObject->data->Avtivities[0]->ProductDisplaySale; // 售出
			} else {
				$data[$key]['surplus'] = $data[$key]['total'] = $data[$key]['has_led'] = 0;
			}
			$data[$key]['par_value'] = $row['par_value'];
		}
		return $data;
	}
	/**
	 * 获取品牌下的在售现金券
	 * @param unknown_type $brand_id
	 */
	public function getTicketByBrandId($brand_id, $city) {
		$data = array();
		$sql = "select * from `oto_ticket` where `brand_id` = '{$brand_id}' and `city` = '{$city}'" . $this->couponWhereSql() . ' order by sequence asc, created desc';
		$ticketArray = $this->_db->fetchAll($sql);
		foreach($ticketArray as $key => $row) {
			$data[$key]['ticket_id'] = $row['ticket_id'];
			$data[$key]['ticket_uuid'] = $row['ticket_uuid'];
			$data[$key]['ticket_title'] = $row['ticket_title'];
			$data[$key]['selling_price'] = $row['app_price'] > 0 ? $row['app_price'] : $row['selling_price'];
			$ticketObject = Custom_AuthTicket::get_ticket_details_by_guid($row['ticket_uuid']);
			if(is_object($ticketObject)) {
				$data[$key]['surplus'] = $ticketObject->data->Avtivities[0]->ProductStock; // 剩余
				$data[$key]['total'] = $ticketObject->data->Avtivities[0]->ProductNum; // 总数
				$data[$key]['has_led'] = $ticketObject->data->Avtivities[0]->ProductDisplaySale; // 售出
			} else {
				$data[$key]['surplus'] = $data[$key]['total'] = $data[$key]['has_led'] = 0;
			}
			$data[$key]['par_value'] = $row['par_value'];
		}
		return $data;
	}
	/**
	 * 获取商场下的店铺
	 * @param unknown_type $market_id
	 * @param unknown_type $city
	 */
	public function getShopListByMarketId($market_id, $city) {		
		$sql = "select A.shop_id, A.shop_name, B.brand_icon 
				from `oto_shop` as A
				left join `oto_brand` as B on A.brand_id = B.brand_id
				where A.`market_id` = '{$market_id}' and A.`city` = '{$city}' order by A.sequence asc, A.created desc";
		$shopArray = $this->_db->fetchAll($sql);
		foreach($shopArray as & $row) {
			$row['brand_icon'] = $row['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_brand.png';
		}
		
		return $shopArray;
	}
	/**
	 * 获取品牌下的店铺
	 * @param unknown_type $brand_id
	 * @param unknown_type $city
	 */
	public function getShopListByBrandId($brand_id, $city) {
		$sql = "select A.shop_id, A.shop_name, B.brand_icon
				from `oto_shop` as A
				left join `oto_brand` as B on A.brand_id = B.brand_id
				where A.`brand_id` = '{$brand_id}' and A.`city` = '{$city}' order by A.sequence asc, A.created desc";
		$shopArray = $this->_db->fetchAll($sql);
		foreach($shopArray as & $row) {
			$row['brand_icon'] = $row['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_brand.png';
			$row['have_ticket'] = intval($this->hasTicketByShopId($row['shop_id'], $city));
		}
		
		return $shopArray;
	}
	/**
	 * 在售现金券条件
	 */
	public function couponWhereSql($mark = 'voucher') {
		//现金券分类ID
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', $mark);
		
		$couponSql = "  AND `ticket_type` = '{$ticket_type}' AND `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1 AND `end_time` > '" . REQUEST_TIME . "' AND `start_time` < '" . REQUEST_TIME . "'";
		return $couponSql;
	}
	
	/**
	 * 在售商城商品条件
	 */
	public function commodityWhereSql($mark = 'commodity') {
		//商城商品分类ID
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', $mark);
	
		$couponSql = "  AND `ticket_type` = '{$ticket_type}' AND `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1 AND `end_time` > '".REQUEST_TIME."' AND `start_time` < '".REQUEST_TIME."'";
		return $couponSql;
	}
	/**
	 * 根据推荐位获取券列表
	 * @param unknown_type $pmark
	 * @param unknown_type $cmark
	 * @param unknown_type $city
	 * @param unknown_type $limit
	 */
	public function getRecommendTicketByMark($pmark, $cmark, $city, $limit) {
		$positionRow = $this->getTheRecommendedPosition($pmark, $cmark, true, $city);
				
		$sql = "select B.ticket_id, B.ticket_uuid, B.ticket_title, B.par_value, B.selling_price, B.app_price, B.cover_img from `oto_recommend` A
				left join `oto_ticket` B on A.come_from_id = B.ticket_id
				where A.pos_id = '{$positionRow['pos_id']}' 
				order by A.sequence asc, A.created desc 
				limit {$limit}";
		
		$ticketArray = $this->_db->fetchAll($sql);
		foreach($ticketArray as & $ticketRow) {
			$ticketRow['img_url'] = $ticketRow['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $ticketRow['cover_img'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_tuan.png';
			$ticketRow['selling_price'] = $ticketRow['app_price'] ? $ticketRow['app_price'] : $ticketRow['selling_price'];
			unset($ticketRow['cover_img'], $ticketRow['app_price']);
		}
		
		return $ticketArray;
	}
	/**
	 * 获取品牌首页
	 * (non-PHPdoc)
	 * @see Base::getBrand()
	 */	
	public function getBrand($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			//品牌头部通栏
			$data['banner'] = $this->getListByMark($city, 'app_brand_version_four', 'app_brand_banner', 5);
			//什么流行榜，奢侈品，我吐，扯淡
			$data['category'] = array_values($this->getBrandCategoryList('app_brand_sort', $city, 8));
			//品牌小图
			$smallArray = $this->getListByMark($city, 'app_brand_version_four', 'app_brand_small', 5);
			foreach($smallArray as & $smallRow) {
				$smallRow['have_ticket'] = intval($this->hasTicketByBrandId($smallRow['come_from_id'], $city));
			}
			$data['small'] = $smallArray;
			//品牌大图
			$bigArray = $this->getListByMark($city, 'app_brand_version_four', 'app_brand_big', 5);
			foreach($bigArray as & $bigRow) {
				$bigRow['have_ticket'] = intval($this->hasTicketByBrandId($bigRow['come_from_id'], $city));
			}
			$data['big'] = $bigArray;
			//品牌推荐券
			$data['ticket'] = $this->getRecommendTicketByMark('app_brand_version_four', 'app_brand_ticket', $city, 5);
			//推荐品牌
			$data['recommend'] = $this->getBrandRecommendMore('app_brand_version_four', 'app_band_recommend', 1, $city);
			//获取分类品牌数量
			$data['class'] = $this->getBrandNumByStore($city);
			
			$this->setData($key, $data);
		}
		
		return $data;		
	}
	/**
	 * 根据推荐位获取，推荐品牌
	 * @param unknown_type $pmark
	 * @param unknown_type $cmark
	 * @param unknown_type $page
	 * @param unknown_type $city
	 */
	public function getBrandRecommendMore($pmark, $cmark, $page, $city) {
		$pos_id = $this->getPosIdByMark($city, $pmark, $cmark);
		
		$start = ($page - 1) * PAGESIZE;
		$sql = "select `A`.*, `B`.`brand_icon`, `B`.`brand_name_zh`, `B`.`brand_name_en`
				from `oto_recommend` as `A`
				left join `oto_brand` as `B` on `A`.`come_from_id` = `B`.`brand_id`
				where `A`.`pos_id` = '{$pos_id}'
				order by `A`.`sequence` asc, `A`.`created` desc";
		
		$tmpArr = $this->_db->limitQuery($sql, $start, PAGESIZE);
	
		foreach($tmpArr as & $tmpItem) {
			if($tmpItem['brand_icon']) {
				$img_tmp = $tmpItem['brand_icon'];
				$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $img_tmp;
				$dir_url = ROOT_PATH . 'web/data/brand/' . $img_tmp;
			} elseif($tmpItem['img_url']) {
				$img_tmp = $tmpItem['img_url'];
				$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $img_tmp;
				$dir_url = ROOT_PATH . 'web/data/recommend/' . $img_tmp;
			} else {
				$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/recommend_for_you.png';
				$dir_url = ROOT_PATH . 'web/data/app/recommend_for_you.png';
			}
			unset($tmpItem['brand_icon'], $tmpItem['img_url']);
				
			$tmpItem['img_url'] =  $www_url;
			list($tmpItem['width'], $tmpItem['height']) = getimagesize($dir_url);
				
			//品牌名称
			$tmpItem['brand_name'] = $tmpItem['brand_name_zh'] ? $tmpItem['brand_name_zh'] : $tmpItem['brand_name_en'];
			
			$tmpItem['have_ticket'] = intval($this->hasTicketByBrandId($tmpItem['come_from_id'], $city));
			
			unset($tmpItem['brand_name_zh'], $tmpItem['brand_name_en']);
		}
	
		return $tmpArr ? $tmpArr : array();
	}
	/**
	 * 获取分类品牌列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getBrandList($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			$where = '';
			if($getData['storeid']) {
				$where .= " and `store_id` = '{$getData['storeid']}'";
			}
			$sql = "select * from `oto_brand` where 1 {$where} order by sequence asc, created desc";
			$data = $this->_db->fetchAll($sql);
			foreach($data as & $row) {
				if($row['brand_icon']) {
					$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'];
					$dir_url = ROOT_PATH . 'web/data/brand/' . $row['brand_icon'];
				} else {
					$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_brand.png';
					$dir_url = ROOT_PATH . 'web/data/app/default_brand.png';
				}
				
				$row['brand_icon'] =  $www_url;
				list($row['width'], $row['height']) = getimagesize($dir_url);
				
				$row['have_ticket'] = intval($this->hasTicketByBrandId($row['brand_id'], $city));
			}
			$this->setData($key, $data);
		}	
		
		return $data;
	}
	/**
	 * 获取分类品牌数量
	 * @param unknown_type $city
	 */
	public function getBrandNumByStore($city) {
		$data = Model_Api_Goods::getInstance()->getAppStore($city);
		foreach($data as & $row) {
			$sql = "select count(brand_id) from `oto_brand` where store_id = '{$row['id']}' and city = '{$city}'";
			$row['num'] = $this->_db->fetchOne($sql);
		}
		return $data;
	}
	
	/**
	 * 获取区域商场数量
	 * @param unknown_type $city
	 */
	public function getMarketNumByRegion($city) {
		$data = array();
		$regionArray = $this->getRegion(0, true, $city);
		$i = 0;
		foreach($regionArray as  $id  => $name) {
			$data[$i]['id'] = $id;
			$data[$i]['name'] = $name;
			$sql = "select count(market_id) from `oto_market` where region_id = '{$id}' and city = '{$city}'";
			$data[$i]['num'] = $this->_db->fetchOne($sql);
			$i++;
		}
		return $data;
	}
	
	/**
	 * 获取APP端推荐商圈
	 * @param unknown_type $city
	 */
	public function getAppHostCircle($city) {
		$sql = "select circle_id as id, circle_name as name from `oto_circle` where `is_hot` = '1' and `city` = '{$city}' order by sequence asc, created desc";
		return $this->_db->fetchAll($sql);
	}
	/**
	 * 获取商场首页
	 * (non-PHPdoc)
	 * @see Base::getMarket()
	 */
	public function getMarket($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			//推荐商圈
			$data['circle'] = $this->getAppHostCircle($city);
			//商场通栏
			$data['banner'] = $this->getListByMark($city, 'market', 'market_banner', 5);
			//推荐商场
			$data['recommend'] = $this->getMoreRecommendMarket($getData, $city);
			//区域商场统计
			$data['class'] = $this->getMarketNumByRegion($city);
			
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 获取商场列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getMarketList($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			//分类商场数据
			$data['data'] = $this->getMoreRecommendMarket($getData, $city);
			//区域商场统计
			$data['class'] = $this->getMarketNumByRegion($city);
			$this->setData($key, $data);
		}
		return $data;
	}
	/**
	 * 获取商场详情
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getMarketDetail($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			//商场详情
			$market_id = intval($getData['mid']);
			$marketRow = $this->select("`market_id` = '{$market_id}'", 'oto_market', '*', '', true);
			$marketRow['head_img'] = $marketRow['head_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $marketRow['head_img'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_market.png';
			$marketRow['logo_img'] = $marketRow['logo_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $marketRow['logo_img'] : '';
			$data['detail'] = $marketRow;
			//商场关联券
			$data['ticket'] = $this->getTicketByMarketId($market_id, $city);
			//商场店铺
			$data['shop'] = $this->getShopListByMarketId($market_id, $city);
			$this->setData($key, $data);
		}
		//获取即时收藏数
		if($is_cache) {
			$data['detail']['favorite_number'] = $this->getFavoriteNum('oto_market_favorite', $market_id);
		}
		
		return $data;
	}
	/**
	 * 添加商场收藏
	 * @param unknown_type $market_id
	 */
	public function addMarketFav($market_id, $user_id) {
		$ip = !$ip ? CLIENT_IP : $ip;
		$result = $this->_db->replace('oto_market_favorite', array('user_id' => $user_id, 'market_id' => $market_id, 'created' => REQUEST_TIME));
		if($result) {
			$num = $this->getFavoriteNum('oto_market_favorite', $market_id);
			$this->_db->update('oto_market', array('favorite_number' => $num), "market_id = '{$market_id}'");
			$this->updateUser($ip, $user_id);
			return true;
		}
		return false;
	}
	/**
	 * 添加品牌收藏
	 * @param unknown_type $brand_id
	 */
	public function addBrandFav($brand_id, $user_id) {
		$ip = !$ip ? CLIENT_IP : $ip;
		$result = $this->_db->replace('oto_brand_favorite', array('user_id' => $user_id, 'brand_id' => $brand_id, 'created' => REQUEST_TIME));
		if($result) {
			$num = $this->getFavoriteNum('oto_brand_favorite', $brand_id);
			$this->_db->update('oto_brand', array('favorite_number' => $num), "brand_id = '{$brand_id}'");
			$this->updateUser($ip, $user_id);
			return true;
		}
		return false;
	}
	/**
	 * 获取收藏数
	 * @param unknown_type $tableName
	 * @param unknown_type $id
	 */
	public function getFavoriteNum($tableName, $id) {
		$where = '';
		switch ($tableName) {
			case 'oto_market_favorite':
				$where = " and `market_id` = '{$id}'";
				break;
			case 'oto_brand_favorite':
				$where = " and `brand_id` = '{$id}'";
				break;
			case 'oto_good_favorite':
				$where = " and `good_id` = '{$id}'";
				break;
			case 'oto_shop_favorite':
				$where = " and `shop_id` = '{$id}'";
				break;
			case 'oto_good_concerned':
				$where = " and `good_id` = '{$id}'";
				break;
			case 'oto_ticket_favorite':
				$where = " and `ticket_id` = '{$id}'";
				break;
		}
		
		return $this->_db->fetchOne("select count(*) from `{$tableName}` where 1 {$where}");
	}
	
	/**
	 * 获取收藏数
	 * @param unknown_type $tableName
	 * @param unknown_type $id
	 */
	public function getUserFavoriteNum($tableName, $user_id) {
		return $this->_db->fetchOne("select count(*) from `{$tableName}` where `user_id` = '{$user_id}'");
	}	
	/**
	 * 判断是否收藏
	 * @param unknown_type $tableName
	 * @param unknown_type $id
	 */
	public function isFavorite($tableName, $id, $user_id) {
		$where = '';
		switch ($tableName) {
			case 'oto_market_favorite':
				$where = "`market_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_brand_favorite':
				$where = "`brand_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_good_favorite':
				$where = "`good_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_shop_favorite':
				$where = "`shop_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_good_concerned':
				$where = "`good_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_ticket_favorite':
				$where = "`ticket_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'discount_favorite' :
				$where = "`discount_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_group_chat_praise' :
				$where = "`chat_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_user_dynamic_like':
				$where = "`dynamic_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
		}
	
		if($where) {
			return intval($this->_db->fetchOne("select count(*) from `{$tableName}` where {$where}") == 1);
		}
		
		return 0;
	}
	/**
	 * 取消收藏
	 * @param unknown_type $tableName
	 * @param unknown_type $id
	 */
	public function delFavorite($tableName, $id, $user_id) {
		$where = '';
		switch ($tableName) {
			case 'oto_market_favorite':
				$where = "`market_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_brand_favorite':
				$where = "`brand_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_good_favorite':
				$where = "`good_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_shop_favorite':
				$where = "`shop_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_good_concerned':
				$where = "`good_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_ticket_favorite':
				$where = "`ticket_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'discount_favorite':
				$where = "`discount_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_group_chat_praise' :
				$where = "`chat_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
			case 'oto_user_dynamic_like':
				$where = "`dynamic_id` = '{$id}' and `user_id` = '{$user_id}'";
				break;
		}
	
		if($where) {
			return $this->_db->delete($tableName, $where);
		}
	
		return false;
	}	
	/**
	 * 新增收藏
	 * @param unknown_type $tableName
	 * @param unknown_type $id
	 * @param unknown_type $user_id
	 */
	public function addFavorite($tableName, $id, $user_id) {
		$param = array();
		switch ($tableName) {
			case 'oto_market_favorite':
				$param = array(
					'market_id' => $id
				);
				break;
			case 'oto_brand_favorite':
				$param = array(
					'brand_id' => $id
				);				
				break;
			case 'oto_good_favorite':
				$param = array(
					'good_id' => $id
				);				
				break;
			case 'oto_shop_favorite':
				$param = array(
					'shop_id' => $id
				);
				break;
			case 'oto_good_concerned':
				$param = array(
					'good_id' => $id
				);
				break;
			case 'oto_ticket_favorite':
				$param = array(
					'ticket_id' => $id
				);
				break;
			case 'discount_favorite':
				$param = array(
					'discount_id' =>$id
				);
				break;
			case 'oto_group_chat_praise' :
				$param = array(
						'chat_id' =>$id
				);
				break;
			case 'oto_user_dynamic_like':
				$param = array(
						'dynamic_id' =>$id
				);
				break;
		}
		
		if($param) {
			$param = array_merge($param, array('user_id' => $user_id, 'created' => REQUEST_TIME));
			$sql = $this->insertSql($tableName, $param);
			return $this->_db->query($sql);
		}
		return false;
	}	
	/**
	 * 用户收藏数量统计
	 * @param unknown_type $user_id
	 */
	public function updateQuantityFavByUserId($tableName, $user_id) {
		$param = array();
		switch ($tableName) {
			case 'oto_market_favorite':
				$param = array(
					'market_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
			case 'oto_brand_favorite':
				$param = array(
					'brand_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
			case 'oto_good_favorite':
				$param = array(
					'good_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
			case 'oto_shop_favorite':
				$param = array(
					'shop_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
			case 'oto_good_concerned':
				$param = array(
					'concerned_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
			case 'oto_ticket_favorite':
				$param = array(
					'commodity_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
			case 'discount_favorite':
				$param = array(
					'discount_favorite_number' => $this->getUserFavoriteNum($tableName, $user_id)
				);
				break;
		}
		
		if($param) {
			return $this->_db->update('oto_user', $param, "user_id = '{$user_id}'");
		}
		
		return false;
	}
	/**
	 * 根据推荐位获取，推荐商场
	 * @param unknown_type $lat
	 * @param unknown_type $lng
	 * @param unknown_type $city
	 * @param unknown_type $page
	 * @param unknown_type $is_show
	 * @param unknown_type $pagesize
	 */
	public function getMoreRecommendMarket($getData, $city, $pagesize = PAGESIZE) {
		$where = '';
		if($getData['rid']) {
			$where .= " and `region_id` = '".intval($getData['rid'])."'";
		}
		
		if($getData['isr']) {
			$where .= " and `is_show` = '".intval($getData['isr'])."'";
		}
		
		if($getData['name']) {
			$where .= " and `market_name` like '%".strip_tags(trim(urldecode($getData['name'])))."%'";
		}
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$lat = $getData['lat'];
		$lng = $getData['lng'];
		
		$start = ($page - 1) * $pagesize;
		if ($lat && $lng) {
			$sql = "SELECT market_id, market_name, market_address, intro, trafficInfo, logo_img,
					12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
					FROM oto_market
					WHERE `city` = '{$city}' {$where}
					ORDER BY `sequence` asc, `distance` asc";
		} else {
			$sql = "SELECT market_id, market_name, market_address, intro, trafficInfo, logo_img, 0 as distance
					FROM oto_market
					WHERE `city` = '{$city}' {$where}
					ORDER BY `sequence` desc, `market_id` desc";
		}
		
		$marketArray = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($marketArray as &$row) {
			$row['logo_img'] = $row['logo_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/recommend_for_you.png';
			$row['have_ticket'] = intval($this->hasTicketByMarket($row['market_id'], $city));
		}

		return $marketArray;
	}
	/**
	 * 获取品牌详情
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 * @return Ambigous <mixed, multitype:>
	 */
	public function getBrandDetail($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			//品牌详情
			$brand_id = intval($getData['bid']);
			$brandRow = $this->select("`brand_id` = '{$brand_id}'", 'oto_brand', '*', '', true);
			$marketRow['brand_head'] = $brandRow['brand_head'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $brandRow['brand_head'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_brand_head.png';
			$marketRow['brand_logo'] = $brandRow['brand_logo'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $brandRow['brand_logo'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_brand.png';
			$data['detail'] = $brandRow;
			//品牌关联券
			$data['ticket'] = $this->getTicketByBrandId($brand_id, $city);
			//品牌店铺
			$data['shop'] = $this->getShopListByBrandId($brand_id, $city);
			
			$this->setData($key, $data);
		}
		//获取即时收藏数
		if($is_cache) {
			$data['detail']['favorite_number'] = $this->getFavoriteNum('oto_brand_favorite', $brand_id);
		}
		
		return $data;
	}
	/**
	 * 获取券列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getTicketList($getData, $city, $is_cache = false) {
		//1：商场 2： 品牌  3： 特卖   默认1
		$ticket_class = $getData['classid'] && in_array($getData['classid'], array(1,2,3)) ? $getData['classid'] : 1;
		$getData['ticket_class'] = $ticket_class;
		//券分类
		$ticket_sort = intval($getData['storeid']);
		$getData['ticket_sort'] = $ticket_sort;
		//分页
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		
		$key = $this->_key . '_' . $ticket_class . '_' . $ticket_sort . '_' . $page . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			$data['data'] = $this->getSaleVoucher($getData, $page, $city);
			$classArray = Model_Api_Goods::getInstance()->getAppStore($city);
			$classArray = array_merge(array(array( 'id' => 0, 'name' => '全部')), $classArray);
			$data['class'] = $classArray;
			$this->setData($key, $data);
		}
		return $data;
	}

	/**
	 * 获取券列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getVoucherTicketList($getData, $city, $is_cache = false) {
		//1：商场 2： 品牌  3： 特卖   默认1
		$ticket_class = $getData['classid'] && in_array($getData['classid'], array(1,2,3)) ? $getData['classid'] : 1;
		$getData['ticket_class'] = $ticket_class;
		//券分类
		$ticket_sort = intval($getData['storeid']);
		$getData['ticket_sort'] = $ticket_sort;
		//分页
		$page = !$getData['page'] ? 1 : intval($getData['page']);
	
		$key = $this->_key . '_' . $ticket_class . '_' . $ticket_sort . '_' . $page . '_' . $city;
		$data = $this->getData($key);
	
		if(!$is_cache || empty($data)) {
			$data = array();
			$data['data'] = $this->getSaleVoucherClean($getData, $page, $city);
			$classArray = Model_Api_Goods::getInstance()->getAppStore($city);
			$classArray = array_merge(array(array( 'id' => 0, 'name' => '全部')), $classArray);
			$data['class'] = $classArray;
			$this->setData($key, $data);
		}
		return $data;
	}
	/**
	 * 获取券列表(0,1,2,3) 0：全部 1：商场 2： 品牌  3： 特卖       默认：0
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getVoucherTicketAllList($getData, $city, $is_cache = false) {
		//1：商场 2： 品牌  3： 特卖   默认1
		$ticket_class = $getData['classid'] && in_array($getData['classid'], array(1,2,3)) ? $getData['classid'] : 0;
		$getData['ticket_class'] = $ticket_class;
		//券分类
		$ticket_sort = intval($getData['storeid']);
		$getData['ticket_sort'] = $ticket_sort;
		//分页
		$page = !$getData['page'] ? 1 : intval($getData['page']);
	
		$key = $this->_key . '_' . $ticket_class . '_' . $ticket_sort . '_' . $page . '_' . $city;
		$data = $this->getData($key);
	
		if(!$is_cache || empty($data)) {
			$data = array();
			$data['data'] = $this->getSaleVoucherAllClean($getData, $page, $city);
			$classArray = Model_Api_Goods::getInstance()->getAppStore($city);
			$data['class'] = $classArray;
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 获取现金券详情
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getVoucherDetail($getData , $city , $is_cache = false ){
		$lng = $getData['lng'];
		$lat = $getData['lat'];
		$ticket_uuid = trim($getData['product_id']);
		$ticket_id = intval($getData['tid']);
		$key = "{$this->_key}{$ticket_uuid}_{$ticket_id}_{$lng}_{$lat}_{$city}";
		$data = $this->getData($key);
		if (empty($data) || !$is_cache) {
			if( $ticket_uuid ){
				$ticketRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($ticket_uuid);
			}else{
				$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($ticket_id);
			}
			if(empty($ticketRow)) {
				exit(json_encode($this->returnArr(0, '', 102, '券不存在')));
			} elseif($ticketRow['is_auth'] == 0) {
				exit(json_encode($this->returnArr(0, '', 103, '券已下架')));
			} elseif($ticketRow['is_show'] == 0) {
				exit(json_encode($this->returnArr(0, '', 104, '券不显示')));
		    } elseif($ticketRow['ticket_status'] == '-1') {
				exit(json_encode($this->returnArr(0, '', 105, '券审核不通过')));
		    }  elseif($ticketRow['ticket_status'] == '0') {
				exit(json_encode($this->returnArr(0, '', 106, '券未审核')));
		    } else {
		    	$status = array();
		    	$ticketRow['surplus'] = 0; // 剩余
		    	$ticketRow['total'] = 0; // 总数
		    	$ticketRow['has_led'] = 0; // 售出
		    	//未开始现金券
		    	if( $ticketRow["start_time"] > REQUEST_TIME ) {
		    		$status = array("stat" => "-1", "text" => "未开始");
		    	} 
		    	//已结束现金券
		    	elseif( $ticketRow["end_time"] < REQUEST_TIME  ) {
		    		//获取券销量
		    		$ticketDetailObject = Custom_AuthTicket::get_ticket_details_by_guid($ticketRow["ticket_uuid"]);
		    		if(is_object($ticketDetailObject)) {
		    			$ticketRow['surplus'] = $ticketDetailObject->data->Avtivities[0]->ProductStock; // 剩余
		    			$ticketRow['total'] = $ticketDetailObject->data->Avtivities[0]->ProductNum; // 总数
		    			$ticketRow['has_led'] = $ticketDetailObject->data->Avtivities[0]->ProductDisplaySale; // 售出
		    		}
					$status = array("stat" => "-1", "text" => "已过期");
				} 
				//进行中现金券
				else {
					$ticketRow['sort_mark'] = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
		    		//获取券销量
			    	$ticketDetailObject = Custom_AuthTicket::get_ticket_details_by_guid($ticketRow["ticket_uuid"]);
			    	if(is_object($ticketDetailObject)) {
			    		$ticketRow['surplus'] = $ticketDetailObject->data->Avtivities[0]->ProductStock; // 剩余
			    		$ticketRow['total'] = $ticketDetailObject->data->Avtivities[0]->ProductNum; // 总数
			    		$ticketRow['has_led'] = $ticketDetailObject->data->Avtivities[0]->ProductDisplaySale; // 售出
			    	}
			    	if($ticketRow['surplus'] <= 0) {
			    		$status = array("stat" => "-1", "text" => "已售完");
			    	} else {
			    		$status = array("stat" => "1", "text" => "立即购买");
			    	}
				}
				$ticketRow["status"] = $status;
				//处理商品价格
				if($ticketRow['is_free'] == 1) {
					$ticketRow['selling_price'] = 0;
				} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] > 0) {
					$ticketRow['selling_price'] = $ticketRow['app_price'];
				} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] < 0) {
					$ticketRow['selling_price'] = 0;
				}
				unset($ticketRow['app_price'], $ticketRow['is_free'], $ticketRow['ticket_status']);
				$ticketRow['content'] = $ticketRow['content'];
				$ticketRow['valid_time'] = date('Y.n.j', $ticketRow['valid_stime']).'-'.date('n.j', $ticketRow['valid_etime']);
				$ticketRow['cover_img'] = $ticketRow['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/' . $ticketRow['cover_img'] : '';
				$ticketRow['manager'] = Model_Home_Ticket::getInstance()->getPermissionUuidByTicketId($ticketRow['ticket_id']);
					
				//WAP图片
				$wapImgList = Model_Admin_Ticket::getInstance()->getWapImg($ticketRow['ticket_id']);
				$ticketRow['wap_img'] = array();
				foreach($wapImgList as $rowWapImg) {
					$widthHeightRow = $this->getImageWidthHeight($rowWapImg['img_url'], 'ticketwap');
					if( empty($widthHeightRow['width']) || empty($widthHeightRow['height']) ){
						continue;
					}
					$wap_img_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticketwap/' . $rowWapImg['img_url'];
					$ticketRow['wap_img'][] = array('wap_img_url'=>$wap_img_url,
										'width'=>$widthHeightRow["width"],
										'height'=>$widthHeightRow["height"]
					);
				}
				$ticketRow["shopInfo"] = $this->getShopListByTicketId($ticketRow["ticket_id"], $ticketRow["shop_id"], $lng, $lat);
			}
			$data = $ticketRow;
			$this->setData($key,$data);
		}
		return $data;
	}
	/**
	 * 获取指定券相关的店铺
	 * @param unknown_type $ticket_id
	 * @param unknown_type $shop_id
	 * @param unknown_type $lng
	 * @param unknown_type $lat
	 */
	public function getShopListByTicketId($ticket_id, $shop_id, $lng, $lat) {
		$shopIdArr = $this->_db->fetchCol("select shop_id from oto_ticket_shop where ticket_id = '{$ticket_id}'");
		$shopIdArr[] = $shop_id;
		if ($lng && $lat) {
			$sql_shop = "SELECT
				shop_id, shop_name, favorite_number, shop_address,
				12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
				FROM oto_shop
				WHERE shop_status <> -1 and shop_pid = 0 and " . $this->db_create_in($shopIdArr, 'shop_id') . "
						ORDER BY distance asc";
		}else{
			$sql_shop = "SELECT
				shop_id, shop_name, favorite_number, shop_address
				FROM oto_shop
				WHERE shop_status <> -1 and shop_pid = 0 and " . $this->db_create_in($shopIdArr, 'shop_id');
		}
				
		$shop_info = $this->_db->fetchAll($sql_shop);
		foreach($shop_info as &$row) {
			$row['coupon_num'] = Model_Api_Goods::getInstance()->getTicketNumByShopId($row['shop_id']);
		}
		return $shop_info;
	}
	/**
	 * 获取在售现金券(不调用远程接口)
	 * @param unknown_type $city
	 */
	public function getSaleVoucherClean($getData, $page, $city, $pagesize = PAGESIZE) {
		$where = '';
		if($getData['ticket_class']) {
			$where .= " and `ticket_class` = '{$getData['ticket_class']}'";
		}
		
		if($getData['ticket_sort']) {
			$where .= " and `ticket_sort` = '{$getData['ticket_sort']}'";
		}
		//如果是品牌券，并且分类是空的话，默认显示所有现金券（包括品牌，商场，特卖）（脑残的需求）
		if($getData['ticket_class'] == 2 && $getData['ticket_sort'] == 0) {
			$where = '';
		}
		
		$sql = "select ticket_id, ticket_uuid, ticket_title, ticket_type, ticket_class, ticket_summary, shop_id,
				shop_name,par_value,selling_price,start_time,end_time, valid_stime, valid_etime, cover_img,
				content, wap_content, total, has_led, limit_count, app_price
				from `oto_ticket` where `city` = '{$city}' {$where} " . $this->couponWhereSql() . " order by sequence asc, created desc";
		
		$start = ($page - 1) * $pagesize;
		$couponInfo = $this->_db->limitQuery($sql, $start, $pagesize);
		if( !empty($couponInfo) ){
			$couponInfo = $this->formatVouncherResult($couponInfo);
		}
		return $couponInfo;		
	}
	/**
	 * 获取在售现金券(不调用远程接口，同时包含全部入口)
	 * @param unknown_type $city
	 */	
	public function getSaleVoucherAllClean($getData, $page, $city, $pagesize = PAGESIZE) {
		$where = '';
		if($getData['ticket_class']) {
			$where .= " and `ticket_class` = '{$getData['ticket_class']}'";
		}
	
		if($getData['ticket_sort']) {
			$where .= " and `ticket_sort` = '{$getData['ticket_sort']}'";
		}

		$sql = "select ticket_id, ticket_uuid, ticket_title, ticket_type, ticket_class, ticket_summary, shop_id,
		shop_name,par_value,selling_price,start_time,end_time, valid_stime, valid_etime, cover_img,
		content, wap_content, total, has_led, limit_count, app_price
		from `oto_ticket` where `city` = '{$city}' {$where} " . $this->couponWhereSql() . " order by sequence asc, created desc";
	
		$start = ($page - 1) * $pagesize;
		$couponInfo = $this->_db->limitQuery($sql, $start, $pagesize);
		if( !empty($couponInfo) ){
			$couponInfo = $this->formatVouncherResult($couponInfo);
		}
		return $couponInfo;
	}

	/**
	 * 格式化现金券
	 * @param unknown_type $couponInfo 现金券列表
	 */
	public function formatVouncherResult( $couponInfo ){
		foreach ($couponInfo as $key=>$value) {
			$couponInfo[$key]['dis_price'] = $value['app_price'] ? $value['app_price'] : $value['selling_price'];
			$couponInfo[$key]['selling_price'] = $value['app_price'] ? $value['app_price'] : $value['selling_price'];
			$couponInfo[$key]['app_price'] = ($value['app_price'] ? $value['app_price'] : $value['selling_price']) * 100;
		
			$couponInfo[$key]['content'] = Custom_String::HtmlReplace($value['content'], 1);
			$couponInfo[$key]['valid_time'] = date('Y.n.j', $value['valid_stime']).'-'.date('n.j', $value['valid_etime']);
			$couponInfo[$key]['cover_img'] = $couponInfo[$key]['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/' . $couponInfo[$key]['cover_img'] : '';
		}
		return $couponInfo;
	}
	
	/**
	 * 获取在售现金券
	 * @param unknown_type $city
	 */
	public function getSaleVoucher($getData, $page, $city, $pagesize = PAGESIZE) {
		$where = '';
		if($getData['ticket_class']) {
			$where .= " and `ticket_class` = '{$getData['ticket_class']}'";						
		}
		
		if($getData['ticket_sort']) {
			$where .= " and `ticket_sort` = '{$getData['ticket_sort']}'";
		}
		//如果是品牌券，并且分类是空的话，默认显示所有现金券（包括品牌，商场，特卖）（脑残的需求）
		if($getData['ticket_class'] == 2 && $getData['ticket_sort'] == 0) {
			$where = '';
		}
		
		
						
		$sql = "select ticket_id, ticket_uuid, ticket_title, ticket_type, ticket_class, ticket_summary, shop_id,
				shop_name,par_value,selling_price,start_time,end_time, valid_stime, valid_etime, cover_img,
				content, wap_content, total, has_led, limit_count, app_price
				from `oto_ticket` where `city` = '{$city}' {$where} " . $this->couponWhereSql() . " order by sequence asc, created desc";
		
		$start = ($page - 1) * $pagesize;
		$couponInfo = $this->_db->limitQuery($sql, $start, $pagesize);
		$ticketsort = $this->getTicketSortById(0, 'ticketsort');
		foreach ($couponInfo as $key=>$value) {
			$couponInfo[$key]['sort_name'] = $ticketsort[$value['ticket_type']]['sort_detail_name'];
			$couponInfo[$key]['sort_mark'] = $ticketsort[$value['ticket_type']]['sort_detail_mark'];
			if($couponInfo[$key]['sort_mark'] == 'coupon') {
				$couponInfo[$key]['dis_price'] = $value['par_value'];
				$couponInfo[$key]['surplus'] = $value['total'] - $value['has_led'];
			}elseif($couponInfo[$key]['sort_mark'] == 'voucher') {
				$str = Custom_AuthTicket::get_ticket_details_by_guid($value['ticket_uuid']);
				if(is_object($str)) {
					$couponInfo[$key]['surplus'] = $str->data->Avtivities[0]->ProductStock; // 剩余
					$couponInfo[$key]['total'] = $str->data->Avtivities[0]->ProductNum; // 总数
					$couponInfo[$key]['has_led'] = $str->data->Avtivities[0]->ProductDisplaySale; // 售出
				} else {
					$couponInfo[$key]['surplus'] = 0; // 剩余
					$couponInfo[$key]['total'] = 0; // 总数
					$couponInfo[$key]['has_led'] = 0; // 售出
				}
				$couponInfo[$key]['dis_price'] = $value['app_price'] ? $value['app_price'] : $value['selling_price'];
				$couponInfo[$key]['selling_price'] = $value['app_price'] ? $value['app_price'] : $value['selling_price'];
				$couponInfo[$key]['app_price'] = ($value['app_price'] ? $value['app_price'] : $value['selling_price']) * 100;
			}
			$couponInfo[$key]['content'] = Custom_String::HtmlReplace($value['content'], 1);
			$couponInfo[$key]['valid_time'] = date('Y.n.j', $value['valid_stime']).'-'.date('n.j', $value['valid_etime']);
			$couponInfo[$key]['cover_img'] = $couponInfo[$key]['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/' . $couponInfo[$key]['cover_img'] : '';
			$couponInfo[$key]['manager'] = Model_Home_Ticket::getInstance()->getPermissionUuidByTicketId($value['ticket_id']);
			
			//WAP图片
			$couponInfo[$key]['wap_img'] = Model_Admin_Ticket::getInstance()->getWapImg($value['ticket_id']);
			foreach($couponInfo[$key]['wap_img'] as & $rowWapImg) {
				$rowWapImg['wap_img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticketwap/' . $rowWapImg['img_url'];
				if($rowWapImg['img_url']) {
					list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/ticketwap/' . $rowWapImg['img_url']);
					$rowWapImg['width'] = $width;
					$rowWapImg['height'] = $height;
				} else {
					$rowWapImg['width'] = 0;
					$rowWapImg['height'] = 0;
				}
			}
			
		}
		$couponInfo = Model_Api_Goods::getInstance()->getShopInfoByTicket($couponInfo, $getData['lng'], $getData['lat']);
		return $couponInfo;
	}
	//开始商城商品模块========================================================================================================>>
	/**
	 * 获取商城商品新品首页
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getNewCommodity($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = array();
			//商城商品广告大图
			$data['large_banner'] = $this->getListByMark($city, 'commodity', 'commodity_banner_large', 5);
			//商城商品广告小图
			$data['small_banner'] = $this->getListByMark($city, 'commodity', 'commodity_banner_small', 2);
			//获取最新20件商品
			$data['product_list'] = $this->getNewCommodityMore(
						array(
							'page' => 1,
							'pagesize' => 20,
							'w' => 240,
							'lng' => $getData['lng'],
							'lat' => $getData['lat'],
							'city' => $city
						)
					);
			
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 获取名品购首页 2015-12-28
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getCommodityNewVersionTwo($getData, $city, $is_cache = false) {
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
	
		if(!$is_cache || empty($data)) {
			$data = array();
			//名品购广告
			$data['banner'] = $this->getListByMark($city, 'commodity', 'commodity_banner_large', 5);
			//获取最新20件商品
			$data['good'] = $this->getNewCommodityMore(
					array(
							'page' => 1,
							'pagesize' => 20,
							'w' => 240,
							'lng' => $getData['lng'],
							'lat' => $getData['lat'],
							'city' => $city
					)
			);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 
	 * @param unknown_type $mark
	 * @param unknown_type $page
	 * @param unknown_type $city
	 */
	public function getNewCommodityMore($getData) {
		$data = $snapArr = array();
		
		$name	 	=	Custom_String::HtmlReplace(urldecode($getData['name']), 2);//商品名称
		$page 		= 	$getData['page'];
		$lng 		= 	$getData['lng'];
		$lat 		= 	$getData['lat'];
		$w			=	$getData['w'];
		$city 		= 	$getData['city'];
		$pagesize 	= 	$getData['pagesize'];
		$cid		=	intval($getData['cid']); //商品分类ID
		$bid		=	intval($getData['bid']); //品牌ID
		$des		=	!$getData['des'] ? 'new' :  $getData['des'];
		if(!in_array($des, array('new', 'pri_h_l', 'pri_l_h', 'dis'))) {
			$des = 'new';
		}
		
		$start = ($page - 1) * $pagesize;
		$where = $order = '';
		//刷选商品名称
		if($name) {
			$where .= " and A.`ticket_title` like '%{$name}%'";
		}
		//排除当前的商品
		if($getData['ticket_id']) {
			$where .= " and A.`ticket_id` <> '{$getData['ticket_id']}'";
		}
		//店铺ID
		if ($getData['shop_id']) {
			$where .= " and A.`shop_id` = '{$getData['shop_id']}'";
		}
		//商品分类ID
		if($cid) {
			$where .= " and A.`ticket_sort` = '{$cid}'";
		}
		//品牌ID
		if($bid) {
			$where .= " and A.`brand_id` = '{$bid}'";
		}
		//排序
		switch($des) {
			//销售价格正排
			case 'pri_h_l':
				$order = " A.selling_price desc";
				break;
			case 'pri_l_h':
				$order = " A.selling_price asc";
				break;
			case 'dis':
				if($lat && $lng) {
					$order = " distance asc";
				} else {
					$order = " A.sequence asc, A.created desc";
				}
				break;
			case 'new':
			default :
					$order = " A.sequence asc, A.created desc";
				break;
		}
		//商城商品，审核通过，上架，显示
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$where .= "  AND A.`ticket_type` = '{$ticket_type}' AND A.`ticket_status` = '1' AND A.`is_auth` = '1' AND A.`is_show` = 1 AND A.`start_time`<'".REQUEST_TIME."' AND A.`end_time`>'".REQUEST_TIME."'";
		
		if($lat && $lng) {
			$sql = "select A.*, B.favorite_number, B.commodity_number, B.brand_id,
					12756274*asin(Sqrt(power(sin(({$lat}-B.lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(B.lat*0.0174533)*power(sin(({$lng}-B.lng)*0.008726646),2))) as `distance`
					from `oto_ticket` A
					left join `oto_shop` B on A.shop_id = B.shop_id
					where A.city = '{$city}' and B.shop_status <> '-1' {$where} order by {$order}";
		} else {
			$sql = "select A.*, B.favorite_number, B.commodity_number, B.brand_id
					from `oto_ticket` A
					left join `oto_shop` B on A.shop_id = B.shop_id
					where A.city = '{$city}' and B.shop_status <> '-1' {$where} order by {$order}";			
		}		
		$snapArr = $this->_db->limitQuery($sql, $start, $pagesize);
		
		$lngLatArr = $this->getShopLngLatAll(0, true, $city);
		
		if($getData['uuid'] && $getData['uname']) {
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
		}
		foreach($snapArr as & $row) {
			//距离
			if($lng && $lat) {
				if(isset($lngLatArr[$row['shop_id']])) {
					$row['distance'] = getDistance($lat, $lng, $lngLatArr[$row['shop_id']]['lat'], $lngLatArr[$row['shop_id']]['lng']);
				}
			} else {
				$row['distance'] = 0;
			}
			$row['lng'] = $lngLatArr[$row['shop_id']]['lng'];
			$row['lat'] = $lngLatArr[$row['shop_id']]['lat'];
			
			$row['imgList'] = $this->getTicketWapImg($row['ticket_id'], 'commodity', $w);		
			//商品原始图片
			list($row['originalImgList'] , ) = $this->formatTicketImg($row['ticket_id'], 'commodity' , 1);
			//图片九宫格处理
			$row['thumb_img'] = $this->imageFormat($row['ticket_id'], 'commodity');
			//处理商品价格
			if($row['is_free'] == 1) {
				$row['selling_price'] = 0;
			} elseif($row['is_free'] == 0 && $row['app_price'] > 0) {
				$row['selling_price'] = $row['app_price'];
			}
			unset($row['app_price']);
			
			//折扣
			$row['discount'] = round(($row['selling_price'] / $row['par_value']) * 10 , 1);
			
			//预设收藏状态为0
			if($user_id) {
				$row['is_favorite'] = $this->isFavorite('oto_ticket_favorite', $row['ticket_id'], $user_id);
			} else {
				$row['is_favorite'] = 0;
			}
			
			//格式化时间
			$row['format_created'] = Custom_Time::getTime2($row['created']);
			//店铺品牌图片
			$row['brand'] = Model_Home_Brand::getInstance()->getBrandDetail($row['brand_id'], $city);
		}
		
		$data['data'] = $snapArr;
		if($snapArr) {
			$data['current_total'] = ($page - 1) * $pagesize + count($snapArr);
		} else {
			$data['current_total'] = 0;
		}
		unset($snapArr, $lngLatArr);
		return $data;
	}
	/**
	 * 九宫格图片处理
	 * @param unknown_type $ticket_id
	 * @param unknown_type $iids
	 */
	function imageFormat($ticket_id, $folder = 'commodity') {
		$iids = 'iid';
		$imgList = Model_Admin_Ticket::getInstance()->getWapImg($ticket_id);
		if(empty($imgList)) {
			$iids = 'id';
			$imgList = Model_Admin_Ticket::getInstance()->getImg($ticket_id);
			$folder = 'ticket';
		}
		$wapImgArr = array();
		$i = 0;
		foreach ( $imgList as $rowWapImg ) {
			$widthHeightRow = $this->getImageWidthHeight($rowWapImg['img_url'], $folder);
			if( empty($widthHeightRow['width']) || empty($widthHeightRow['height']) ){
				continue;
			}
			
			$widthHeightRow640 = $this->getImageWidthHeight( $rowWapImg ['img_url'], $folder, 640 );
			$wapImgArr[$i]['W640'] = array (
					'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/640/type/{$folder}",
					'width' => empty($widthHeightRow640['width']) ? 0 : $widthHeightRow640['width'],
					'height' => empty($widthHeightRow640['height']) ? 0 : $widthHeightRow640['height']
					);
			
			$widthHeightRow400 = $this->getImageWidthHeight( $rowWapImg['img_url'], $folder, 400 );
			$wapImgArr[$i]['W400'] = array (
					'img_url' => $GLOBALS ['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/400/type/{$folder}",
					'width' => empty($widthHeightRow400['width']) ? 0 : $widthHeightRow400['width'],
					'height' => empty($widthHeightRow400['height']) ? 0 : $widthHeightRow400['height']
					);
			
			$widthHeightRow240 = $this->getImageWidthHeight( $rowWapImg ['img_url'], $folder, 240 );
			$wapImgArr[$i]['W240'] = array (
					'img_url' => $GLOBALS ['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/240/type/{$folder}",
					'width' => empty($widthHeightRow240['width']) ? 0 : $widthHeightRow240['width'],
					'height' => empty($widthHeightRow240['height']) ? 0 : $widthHeightRow240['height']
					);
			$i++;
		}
		return $wapImgArr;	
	}
	/**
	 * 距离手机用户最近的店铺列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @param unknown_type $is_cache
	 */
	public function getCommodityDistance($getData, $city, $pagesize = 20, $is_cache = false) {
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$lng = $getData['lng'];
		$lat = $getData['lat'];
		$key = $this->_key . '_' . $page . '_' . $lng . '_' . $lat . '_' . $city;
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$data = $snapArr = array();
			$start = ($page - 1) * $pagesize;
			$where = "`shop_status` <>'-1' and `commodity_number` > '0' and `city` = '{$city}'";
			if($lng && $lat) {

				$sql = "select
						shop_id, shop_name, shop_address, brand_id, favorite_number, commodity_number,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						from 
						oto_shop 
						where `lng` > 0 and `lat` > 0 and {$where} 
						order by distance asc";
				
			} else {
				$sql = "select
						shop_id, shop_name, shop_address, brand_id, favorite_number, commodity_number
						from
						oto_shop
						where {$where}
						order by commodity_number desc";			
			}
			$snapArr = $this->_db->limitQuery($sql, $start, $pagesize);
			
			foreach($snapArr as & $row) {
				//距离取整
				$row['distance'] = floor($row['distance']);
				//最新商品
				$row['product_list'] = $this->getLatestCommodityByShopId($row['shop_id']);
				//店铺品牌LOGO 125 × 125
				$row['brand'] = Model_Home_Brand::getInstance()->getBrandDetail($row['brand_id'], $city);
			}
			
			$data['data'] = $snapArr;
			$data['current_total'] = ($page - 1) * $pagesize + count($snapArr);
			unset($snap);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 获取店铺最新3件商品
	 * @param unknown_type $shop_id
	 */
	public function getLatestCommodityByShopId($shop_id, $limit = 3) {
		$data = array();
		
		$sql = "select ticket_id, ticket_title, par_value, selling_price, is_free from `oto_ticket` where `shop_id` = '{$shop_id}'" . $this->commodityWhereSql() . " order by created desc limit {$limit}";
		$data = $this->_db->fetchAll($sql);
		
		foreach($data as & $row) {
			$imgList = array();
			$imgList = Model_Admin_Ticket::getInstance()->getWapImg($row['ticket_id']);
			//第一张图片
			$firstRow = $imgList[0];
			if($firstRow) {
				$widthHeightRow = $this->getImageWidthHeight($firstRow['img_url'], 'commodity', 240);
				$row['firstImg'] = array(
						'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$firstRow['id']}/w/240/type/commodity",
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
						);
			}
			
			//处理商品价格
			if($row['is_free'] == 1) {
				$row['selling_price'] = 0;
			} elseif($row['is_free'] == 0 && $row['app_price'] > 0) {
				$row['selling_price'] = $row['app_price'];
			}
			unset($row['app_price'], $row['par_value'], $row['is_free']);
		}
		
		return $data;
	}
	/**
	 * 获取站点商品分类
	 * @param unknown_type $city
	 */
	public function getStoreList($city) {
		$data = array();
		$key = $this->_key . '_' . $city;
		$data = $this->getData($key);
		if(empty($data)) {
			$sql = "select * from `oto_store` where `city` = '{$city}' order by `sequence` asc, `store_id` asc";
			$data = $this->_db->fetchAll($sql);
			foreach($data as & $row) {
				$row['logo'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/brand/' . $row['mark'] . '.jpg';
			}
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 获取商城品牌列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getCommodityBrandList($getData, $city, $is_cache = false) {
		$store_id = intval($getData['cid']);//商品分类
		$type = empty($getData["type"])?'':$getData["type"];//新增参数
		if($getData['uuid'] && $getData['uname']) {
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			$key = $this->_key . '_' . $user_id . '_' . $store_id . '_' . $type . '_' . $city;
		} else {
			$key = $this->_key . '_' . $store_id . '_' . $type . '_' . $city;
		}
		
		$data = $this->getData($key);
		
		if(!$is_cache || empty($data)) {
			$where_child = '';
			if($user_id) {
				if( $type == 'benefits' ){//筛选有游惠的品牌
					$where_child .= " and B.`has_selfpay` = '1'";
				}
				if( $store_id ) {
					$where_child .= " and B.store_id = '{$store_id}'";
				}
				$sql = "select B.* 
						from 
						`oto_brand_favorite` A
						left join `oto_brand` B on A.brand_id = B.brand_id
						where A.user_id = '{$user_id}' {$where_child} and B.city = '{$city}'
						order by B.sequence asc, B.created desc";
			} else {
				//有商品的品牌
				if( $type == 'new' ){//新的品牌列表需要显示所有的品牌，无需判断有无商品
					
				}else if( $type == 'benefits' ){//筛选有游惠的品牌
					$where_child .= " and `has_selfpay` = '1'";
				}else{
					$where_child .= " and `commodity_number` > '0'";
				}
				if($store_id) {
					$where_child .= " and store_id = '{$store_id}'";
				}
				$sql = "select * from `oto_brand` where 1 {$where_child} and `city` = '{$city}' order by sequence asc, created desc";
			}
			$data = $this->_db->fetchAll($sql);
			foreach($data as & $row) {
				if($row['brand_name_zh']) {
					$row['brand_name'] = $row['brand_name_zh'] . ($row['brand_name_en'] ? '(' . $row['brand_name_en'] . ')'  : '');
				} else {
					$row['brand_name'] = $row['brand_name_en'];
				}
				unset($row['brand_name_zh'], $row['brand_name_en']);
				if($row['brand_icon']) {
					$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'];
					$dir_url = ROOT_PATH . 'web/data/brand/' . $row['brand_icon'];
				} else {
					$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_brand.png';
					$dir_url = ROOT_PATH . 'web/data/app/default_brand.png';
				}
		
				$row['brand_icon'] =  $www_url;
				list($row['width'], $row['height']) = getimagesize($dir_url);
				$row['shop_num'] = 0;
				if( $type !='new' && $type !='benefits' ){//新的品牌列表不需要显示店铺数量，兼容旧的
					$row['shop_num'] = intval($this->getShopNumByBrandId($row['brand_id']));
				}
			}
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 商城
	 * 根据品牌ID获取店铺列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @return Ambigous <multitype:, multitype:>
	 */
	public function getCommodityShopList($getData, $city, $is_cache = false) {
		$brand_id = $getData['bid'];
		
		if($getData['uuid'] && $getData['uname']) {
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			$key = $this->_key . '_' . $user_id . '_' . $brand_id . '_' . $city;
		} else {
			$key = $this->_key . '_' . $brand_id . '_' . $city;
		}
		
		$data = $this->getData($key);
		if(!$is_cache || empty($data)) {
			$snap = array();
			$where = '';
			if($user_id) {
				$sql = "select B.shop_id, B.shop_name, B.shop_address
						from `oto_shop_favorite` A
						left join `oto_shop` B on A.shop_id = B.shop_id
						where A.user_id = '{$user_id}' and B.brand_id = '{$brand_id}' and B.shop_status <> '-1'
						order by B.has_selfpay desc, B.created desc";
			} else {
				$sql = "select shop_id, shop_name, shop_address
						from `oto_shop`					
						where `brand_id` = '{$brand_id}' and `city` = '{$city}' and shop_status <> '-1'
						order by has_selfpay desc, created desc";
			}
			$snap = $this->_db->fetchAll($sql);
			foreach($snap as & $row) {
				$row['good_num'] = intval($this->getCommodityNumByShopId($row['shop_id']));
			}

			$data['data'] = $snap;
			$data['brand'] = Model_Home_Brand::getInstance()->getBrandDetail($brand_id, $city);
			unset($snap);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 根据品牌ID获取相关店铺数量
	 * @param unknown_type $brand_id
	 */
	public function getShopNumByBrandId($brand_id) {
		return $this->_db->fetchOne("select count(shop_id) from `oto_shop` where `brand_id` = '{$brand_id}' and `shop_status` <> '-1'");
	}
	/**
	 * 根据店铺ID获取商城商品数量
	 * @param unknown_type $brand_id
	 */
	public function getCommodityNumByShopId($shop_id) {
		return $this->_db->fetchOne("select count(ticket_id) from `oto_ticket` where `shop_id` = '{$shop_id}'" . $this->commodityWhereSql());
	}
	/**
	 * 根据品牌ID获取商城商品数量
	 * @param unknown_type $brand_id
	 */
	public function getCommodityNumByBrandId($brand_id) {
		return $this->_db->fetchOne("select count(ticket_id) from `oto_ticket` where `brand_id` = '{$brand_id}'" . $this->commodityWhereSql());
	}
	/**
	 * 获取商城商品详情，店铺简介，同店铺更多商品
	 */
	public function getCommodityDetail($getData, $city, $is_cache = false) {
		$ticket_id = $getData['tid'];
		$key = $this->_key . '_' . $ticket_id . '_' . $city;
		$data = $this->getData($key);
		if(!$is_cache || empty($data)) {
			$snapArr = array();
			$snapArr = $this->getCommodityRow($ticket_id);
			if(empty($snapArr)) {
				return array();
			}
			//商品缩略图片
			$snapArr['thumbImgList'] = $this->getTicketWapImg($ticket_id, 'commodity', 240);
			list($snapArr['imgList'],$snapArr['firstImg']) = $this->formatTicketImg($ticket_id, "commodity" , 3 , 640 );

			$data['detail'] = $snapArr;
			$data['shop'] = $this->getShopFieldById($snapArr['shop_id']);
			$data['brand'] = Model_Home_Brand::getInstance()->getBrandDetail($snapArr['brand_id'], $city);
			$data['good'] = $this->getNewCommodityMore(
								array(
									'page' => 1,
									'pagesize' => 20,
									'uuid' => $getData['uuid'],
									'uname' => $getData['uname'],
									'ticket_id' => $snapArr['ticket_id'],
									'shop_id' => $snapArr['shop_id'],
									'w' => 240,
									'city' => $city
								)
							);
			unset($snapArr);
			$this->setData($key, $data);
		}
		
		$data['detail']['is_favorite'] = 0;
		if($getData['uuid'] && $getData['uname']) {
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			$data['detail']['is_favorite'] = $this->isFavorite('oto_ticket_favorite', $ticket_id, $user_id);
		}
		
		return $data;
	}
	/**
	 * 获取商城商品详情
	 * @param unknown_type $ticket_id
	 * @return unknown
	 */
	public function getCommodityRow($ticket_id) {
		$row = array();
		$sql = "select * from `oto_ticket` where `ticket_id` = '{$ticket_id}'";
		$row = $this->_db->fetchRow($sql);
		if($row) {
			if(empty($row['ticket_summary'])) {
				$row['ticket_summary'] = $row['ticket_title'];
			}
			//处理商品价格
			if($row['is_free'] == 1) {
				$row['selling_price'] = 0;
			} elseif($row['is_free'] == 0 && $row['app_price'] > 0) {
				$row['selling_price'] = $row['app_price'];
			}
			unset($row['app_price']);
				
			//折扣
			$row['discount'] = round(($row['selling_price'] / $row['par_value']) * 10 , 1);
		}
		return $row;
	}
	/**
	 * 商城店铺详情页
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getCommodityShopDetail($getData, $city, $is_cache = false) {
		$shop_id = intval($getData['sid']);
		$key = $this->_key . '_' . $shop_id . '_' . $city;
		$data = $this->getData($key);
		if(!$is_cache || empty($data)) {
			$shopRow = array();
			$shopRow = $this->select("`shop_id` = '{$shop_id}' and `shop_status` <> '-1'" , 'oto_shop', '*', '', true);
			$shopRow['shop_img'] =  $shopRow['shop_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/shop/' . $shopRow['shop_img'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/default_shop.png';
			$data['detail'] = $shopRow;
			$brand = Model_Home_Brand::getInstance()->getBrandDetail($shopRow['brand_id'], $city);
			if( !empty($brand) ){
				$data['brand'] = $brand;
			}
			$data['good'] = $this->getNewCommodityMore(
					array(
							'page' => 1,
							'pagesize' => 20,
							'shop_id' => $shopRow['shop_id'],
							'w' => 240,
							'lat' => $getData['lat'],
							'lng' => $getData['lng'],
							'city' => $city
					)
			);
			//获取自定义买单
			$selfpay = $this->getShopSelfPay($shop_id, $city);
			if( !empty($selfpay) ){
				$pay_user_count = 0;
				$clientResult = Custom_AuthTicket::getMerchantStatInfoToUser(array('shop_id' => $shop_id));
				if( isset($clientResult['Code']) && $clientResult['Code'] == 1 ){
					$pay_user_count = $clientResult['Result']['OrderUserCountByCustomPrice'];
				}
				$selfpay['ticket_desc'] = "{$pay_user_count}人已享";
				$data['selfpay'] = $selfpay;
			}

			//获取店铺支持的品牌券和商场券
			$data['vouchers'] =Model_Api_Shop::getInstance()->getShopVouchers($shop_id, $city);
			
			//所在商场
			if($shopRow["market_id"]) {
				$marketRow = Model_Home_Market::getInstance()->getMarketRow($shopRow["market_id"], $city);
			}
			
			if($shopRow["market_id"] && !empty($marketRow) ){
				$shop_num = Model_Api_Shop::getInstance()->getShopNumByMarketId($shopRow['market_id']);
				$data['market'] = array(
						"market_id"=>$marketRow["market_id"],
						"market_uid"=>$marketRow["market_uid"],
						"logo_img"=>$marketRow["logo_img"],
						"market_name"=>$marketRow["market_name"],
						"shop_num"=>$shop_num
						);
			}
			
			//获取所有分店
			$data['branch_shops'] = array();
			if( $shopRow['brand_id'] > 0 ){
				$data['branch_shops'] = Model_Api_Shop::getInstance()->getBranchShop($shop_id,$shopRow['brand_id'], $city);
			}
			
			unset($shopRow);
			$this->setData($key, $data);
		}
				
		$data['detail']['is_favorite'] = 0;
		if($getData['uuid'] && $getData['uname']) {
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			$data['detail']['is_favorite'] = $this->isFavorite('oto_shop_favorite', $shop_id, $user_id);
			
			foreach($data['good']['data'] as & $goodItem) {
				$goodItem['is_favorite'] = $this->isFavorite('oto_ticket_favorite', $goodItem['ticket_id'], $user_id);
			}
		}
		return $data;
	}
	
	/**
	 * 获取店铺自定义买单
	 * @param unknown_type $shop_id
	 */
	public function getShopSelfPay($shop_id, $city) {
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'selfpay');
		//直属游惠
		$sql = "select ticket_uuid, ticket_title
				from `oto_ticket`
				where `ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `city` = '{$city}' and `ticket_status` = '1' and `is_auth` = '1' AND `end_time` > '" . REQUEST_TIME . "' AND `start_time` < '" . REQUEST_TIME . "'
				order by `created` desc limit 1";
		$row = $this->_db->fetchRow($sql);
		//关联游惠
		if(empty($row)) {
			$sql_tids = "select ticket_id from `oto_ticket_shop` where `shop_id` = '{$shop_id}'";
			$tidArrs = $this->_db->fetchCol($sql_tids);
			$sql = "select ticket_uuid, ticket_title 
					from `oto_ticket` 
					where `ticket_type` = '{$ticket_type}' and " . $this->db_create_in($tidArrs, 'ticket_id') . " and `city` = '{$city}' and `ticket_status` = '1' and `is_auth` = '1' AND `end_time` > '" . REQUEST_TIME . "' AND `start_time` < '" . REQUEST_TIME . "'    
					order by `created` desc limit 1";
			$row = $this->_db->fetchRow($sql);
		}
		return $row ? $row : array();
	}
	
	/**
	 * 获取获取图片真实宽高
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getImageWidthHeight($img_url, $folder, $w = 0, $h = 0) {
		//缩略图生成
		if($w == 0 && $h == 0) {
			$dir_url = ROOT_PATH . 'web/data/' . $folder . '/' . $img_url;
		} else {
			$dir_url = Model_Api_Good::getInstance()->imgThumb($img_url, $folder, $w, $h, false);
		}
		list($width, $height) = getimagesize($dir_url);	
		return array('width' => $width, 'height' => $height);
	}
	/**
	 * 跟据商品ID，获取图片地址，图片宽高
	 * @param unknown_type $ticket_id
	 * @param unknown_type $folder
	 * @param unknown_type $w
	 */
	public function getTicketWapImg($ticket_id, $folder = 'commodity', $w = 240) {
		$data = $wapImgData = array();
		$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($ticket_id);
		if( empty($wapImgData) ){
			$folder = 'ticket';
			$wapImgData = Model_Admin_Ticket::getInstance()->getImg($ticket_id);
		}
		
		foreach($wapImgData as & $wapImgItem) {
			$widthHeightRow = array();
			$widthHeightRow = $this->getImageWidthHeight($wapImgItem['img_url'], $folder, $w);
			$data[] =
				array(
						'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/".($folder=="ticket"?"id":"iid")."/{$wapImgItem['id']}/w/{$w}/type/{$folder}",
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height'],
					);
		
		}

		return $data;
	}
	/**
	 * 获取店铺粉丝
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getShopFans($getData, $city, $pagesize = 20, $is_cache = false) {
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$start = ($page - 1) * $pagesize;
		if($getData['uuid'] && $getData['uname']) {
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
		}
		$shop_id = intval($getData['sid']);
		$key = $this->_key . '_' . $shop_id . '_' . $user_id . '_' . $page. '_' . $city;
		$data = $this->getData($key);
		
		if($user_id) {
			$myFriendArr = $this->getMyFriendArr($user_id);	
		}
				
		if(!$is_cache || empty($data)) {
			$snapArr = array();
			$sql_c = "select count(A.`favorite_id`)
					from 
					`oto_shop_favorite` A
					left join `oto_user` B on A.user_id = B.user_id 
					where A.`shop_id` = '{$shop_id}'";
			$totalNum = $this->_db->fetchOne($sql_c);
			
			$sql = "select A.*, B.uuid, B.user_name, B.shop_number
					from 
					`oto_shop_favorite` A
					left join `oto_user` B on A.user_id = B.user_id 
					where A.`shop_id` = '{$shop_id}' 
					order by A.created asc";
			
			
			$snapArr = $this->_db->limitQuery($sql, $start, $pagesize);

			foreach($snapArr as & $row) {
				if($user_id && $row['user_id'] != $user_id && in_array($row['user_id'], $myFriendArr)) {
					$row['relation'] = 1; //好友关系
				} elseif($user_id && $row['user_id'] == $user_id) {
					$row['relation'] = 2; //我自己
				}else {
					$row['relation'] = 0; //陌生人
				}
				$row['user_avatar'] = $this->getUserAvatarByUuid($row['uuid']);
			}
			
			$data['data'] = $snapArr;
			$data['totalNum'] = $totalNum;
			unset($snapArr);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	
	/**
	 * 获取好友数组
	 * @param unknown_type $user_id
	 */
	public function getMyFriendArr($user_id) {
		$sql = "select `to_user_id` from `oto_user_concerned` where `from_user_id` = '{$user_id}' order by created asc";
		return $this->_db->fetchCol($sql);
	}
	/**
	 * 新增用户关注
	 * @param unknown_type $getData
	 */
	public function addUserConcerned($getData) {
		$fromUserRow = $this->getWebUserId($getData['fuuid']);
		$from_user_id = $fromUserRow['user_id'];
		$toUserRow = $this->getUserByUuid($getData['tuuid']);
		$to_user_id = $toUserRow['user_id'];
		
		if($from_user_id ==  $to_user_id) {
			return array('message' => '不能关注自己', 'result' => 300);
		}
		
		$sql = "select 1 from `oto_user_concerned` where `from_user_id` = '{$from_user_id}' and `to_user_id` = '{$to_user_id}' limit 1";
		$hadAttention = $this->_db->fetchOne($sql);
		
		if($hadAttention == 1) {
			return array('message' => '用户已关注', 'result' => 300);
		} else {
			$this->_db->insert('oto_user_concerned', array('from_user_id' => $from_user_id, 'to_user_id' => $to_user_id, 'created' => REQUEST_TIME));
			Model_Api_User::getInstance()->updateFansNumberByUid($to_user_id);
			Model_Api_User::getInstance()->updateConcernedNumberByUid($from_user_id);
			Model_Api_Message::getInstance()->addPreNotice("system", "home_fans_list", 0 , array(
				"to_user_id"=> $to_user_id,
				"charter_user_id"=>$from_user_id,
				"charter_member"=>$fromUserRow["user_name"],
				"charter_member_avator"=>$fromUserRow["Avatar50"]
			));
			return array('message' => '加关注成功', 'result' => 100);
		}
	}
	
	/**
	 * 取消用户关注
	 * @param unknown_type $getData
	 */
	public function cancelUserConcerned($getData) {
		$fromUserRow = $this->getUserByUuid($getData['fuuid']);
		$from_user_id = $fromUserRow['user_id'];
		$toUserRow = $this->getUserByUuid($getData['tuuid']);
		$to_user_id = $toUserRow['user_id'];
	
		$sql = "select 1 from `oto_user_concerned` where `from_user_id` = '{$from_user_id}' and `to_user_id` = '{$to_user_id}' limit 1";
		$hadAttention = $this->_db->fetchOne($sql);
	
		if($hadAttention == 1) {
			$this->_db->delete('oto_user_concerned', array('from_user_id' => $from_user_id, 'to_user_id' => $to_user_id));
			Model_Api_User::getInstance()->updateFansNumberByUid($to_user_id);
			Model_Api_User::getInstance()->updateConcernedNumberByUid($from_user_id);
			return array('message' => '取消关注成功', 'result' => 100);
		} else {
			return array('message' => '你们不是好友', 'result' => 300);
		}
	}
	
	//开始商城营业员端 >>>
	
	/**
	 * 营业员首页
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getClerkHome($getData, $city) {
		$data = array();
		$user_id = intval($getData['uid']);
		$shop_id = intval($getData['sid']);
		$order_time_slot = !isset($getData['order_time_slot']) ? -1 : intval($getData['order_time_slot']);
		
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		//审核通过的商品
		$sql = "select count(ticket_id) from `oto_ticket` where `ticket_type` = '{$ticket_type}' and `user_id` = '{$user_id}' and `shop_id` = '{$shop_id}' and `ticket_status` = '1' and `is_show` = '1'";
		$data['audit_through_num'] = $this->_db->fetchOne($sql);
		//审核未通过的商品
		$sql = "select count(ticket_id) from `oto_ticket` where `ticket_type` = '{$ticket_type}' and `user_id` = '{$user_id}' and `shop_id` = '{$shop_id}' and `ticket_status` = '-1' and `is_show` = '1'";
		$data['not_audit_through_num'] = $this->_db->fetchOne($sql);
		//我参与的咨询
		$sql = "select count(*) from (select * from `oto_message_post` where `user_id` = '{$user_id}' and `shop_id` = '{$shop_id}' and `type` = 'commodity'  group by tid  ) A";
		$data['advisory_number'] = intval($this->_db->fetchOne($sql));
		//统计数据
		$userRow = $this->getUserByUserId($user_id, '*');
		$getMerchantStatInfo = Custom_AuthTicket::getMerchantStatInfo(
				array(
						'shop_id' => $shop_id,
						'uuid' => $userRow['uuid'],
						'order_time_slot' => $order_time_slot
						)
		);
		$delivery_number = $verification_number = $today_order_number = $UnDeliverCount = $UnUsedCount = 0;
		if($getMerchantStatInfo['Code'] == 1) {
			$delivery_number = (int) $getMerchantStatInfo['Result']['DeliveredCount'];
			$verification_number = (int) $getMerchantStatInfo['Result']['VerifyCount'];
			$today_order_number = (int) $getMerchantStatInfo['Result']['PaidTodayCount']; //今日已支付
			$UnDeliverCount = (int) $getMerchantStatInfo['Result']['UnDeliverCount'];//未发货
			$UnUsedCount = (int) $getMerchantStatInfo['Result']['UnUsedCount'];//待自提
		}
		//打包发货
		$data['delivery_number'] = $delivery_number;
		//办理自提
		$data['verification_number'] = $verification_number;
		//店铺今日待处理的订单数量
		$data['today_order_number'] = $UnDeliverCount + $UnUsedCount;
		//店铺未回复咨询
		$data['unanswered_advisory_number'] = $this->getShopNoReplyNum(array('sid' => $shop_id), $city);
		
		return $data;
	}
	/**
	 * 店长首页
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getManagerHome($getData, $city) {
		$data = array();
		$user_id = intval($getData['uid']);
		$shop_id = intval($getData['sid']);
		$order_time_slot = !isset($getData['order_time_slot']) ? -1 : intval($getData['order_time_slot']);
		
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		//审核通过的商品
		$sql = "select count(ticket_id) from `oto_ticket` where `ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `ticket_status` = '1' and `is_show` = '1'";
		$data['audit_through_num'] = $this->_db->fetchOne($sql);
		//审核未通过的商品
		$sql = "select count(ticket_id) from `oto_ticket` where `ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `ticket_status` = '-1' and `is_show` = '1'";
		$data['not_audit_through_num'] = $this->_db->fetchOne($sql);
		//店长待审核的商品数
		$sql = "select count(ticket_id) from `oto_ticket` where `ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `ticket_status` = '0' and `is_show` = '1'";
		$data['wait_audit_number'] = $this->_db->fetchOne($sql);
		//统计数据
		$userRow = $this->getUserByUserId($user_id, '*');
		$getMerchantStatInfo = Custom_AuthTicket::getMerchantStatInfo(
				array(
						'shop_id' => $shop_id,
						'uuid' => $userRow['uuid'],
						'order_time_slot' => $order_time_slot
				)
		);
		$today_order_number = $UnDeliverCount = $UnUsedCount = 0;
		if($getMerchantStatInfo['Code'] == 1) {
			$today_order_number = (int) $getMerchantStatInfo['Result']['PaidTodayCount']; //今日已支付
			$UnDeliverCount = (int) $getMerchantStatInfo['Result']['UnDeliverCount'];//未发货
			$UnUsedCount = (int) $getMerchantStatInfo['Result']['UnUsedCount'];//待自提
		}
		//店铺今日待处理的订单数量
		$data['today_order_number'] = $UnDeliverCount + $UnUsedCount;
		//店铺新增收益
		$sql = "SELECT `last_profit_view_time` 
				FROM `oto_user_shop_commodity` 
				WHERE `shop_id`='{$shop_id}' AND `user_id`='{$user_id}'";
		$lastViewTime = $this->_db->fetchOne($sql);
		$data['unread_profit_number'] = $this->getProfitList(array("sid"=>$shop_id,"stime"=>$lastViewTime),2);
		return $data;
	}
	/**
	 * 获取店铺图片
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getShopPhoto($getData, $city, $pagesize = PAGESIZE) {
		$data = $wapImgData = array();
		$shop_id = intval($getData['sid']);
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$start = ($page - 1) * $pagesize;
		
		$sql = "select * from `oto_ticket_wap_img` where `shop_id` = '{$shop_id}' group by img_url order by sequence asc, created desc";
		$wapImgData = $this->_db->limitQuery($sql, $start, $pagesize);
		
		foreach($wapImgData as & $wapImgItem) {
			$widthHeightRow = array();
			$widthHeightRow = $this->getImageWidthHeight($wapImgItem['img_url'], 'commodity', 240);
			$data[] =
				array(
						'id' => $wapImgItem['id'],
						'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$wapImgItem['id']}/w/240/type/commodity",
						'width' => intval($widthHeightRow['width']),
						'height' => intval($widthHeightRow['height']),
					);
		
		}
		
		return $data;
	}
	/**
	 * 商品上传
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function goodUpload($getData, $city) {
		//图片IDS
		$img_ids = $getData['iids'] ? trim($getData['iids'], ',') : '';
		
		$shop_id = intval($getData['sid']);
		$user_id = intval($getData['uid']);
		$param = array(
					'tid'				=> 		intval($getData['tid']),
					'ticket_title' 		=>		$getData['title'],
					'p_value'			=> 		$getData['o_price'], 		//原价（面值）
					's_value'			=> 		$getData['d_price'], 		//折扣价（现价）
					'a_price'			=>		$getData['d_price'],		
					'sid'				=> 		$shop_id,
					'ticket_summary'	=>		$getData['summary'], 		//简介
					'selling_points'	=> 		$getData['s_point'], 		//卖点
					'is_show'			=>		1, //显示
					'is_auth'			=>		1, //上架
					'CanWap' 			=> 		1,
					'CanApp'			=>		1,
					'end_time'			=>		strtotime($getData['e_time']) === false ? 0 : $getData['e_time']." 23:59:59"
				);
		
		$userRow = $this->getUserByUserId($user_id);
		$userInfo = array(
					'user_id'	=>	$userRow['user_id'], //用户ID
					'user_name'	=>	$userRow['user_name'], //用户名
				);
		
		$result = Model_Admin_Ticket::getInstance()->addCommodityTicket($param, $userInfo, $city);
		
		if($result['status'] == 100) {
			$insert_ticket_id = $result['insert_ticket_id'];
			
			//关联商品图片
			if($insert_ticket_id && $img_ids) {
				$sql = "select * from `oto_ticket_wap_img` where `id` in ({$img_ids})";
				$imgArr = $this->_db->fetchAll($sql);
				foreach($imgArr as & $imgRow) {
					if($imgRow['ticket_id'] == 0) {
						$this->_db->update('oto_ticket_wap_img', array('ticket_id' => $insert_ticket_id), array('id' => $imgRow['id']));
					} elseif ($imgRow['ticket_id'] == $insert_ticket_id) {
						
					} else {
						if(!$this->checkTicketWapImg($insert_ticket_id, $user_id, $shop_id, $imgRow['img_url'])) {
							Model_Admin_Ticket::getInstance()->wapUploadImg($imgRow['img_url'], $insert_ticket_id, $shop_id, $user_id);
						}
					}
				}
			}
			
			//统计店铺商品数量
			$this->updateQuantityCommodityNumByShopId($shop_id);
			
			return $insert_ticket_id;
		}
		
		return 0;
	}
	/**
	 * 检查图片唯一性
	 * @param unknown_type $ticket_id
	 * @param unknown_type $user_id
	 * @param unknown_type $shop_id
	 * @param unknown_type $img_url
	 */
	public function checkTicketWapImg($ticket_id, $user_id, $shop_id, $img_url) {
		$sql = "select 1 from `oto_ticket_wap_img` 
					where `ticket_id` = '{$ticket_id}'
					and `user_id` = '{$user_id}'
					and `shop_id` = '{$shop_id}'
					and `img_url` = '{$img_url}'
					limit 1
				";
		
		return $this->_db->fetchOne($sql) == 1;
	}
	/**
	 * 商品列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getGoodList($getData, $city, $pagesize = PAGESIZE) {
		$where = '';
		$data = $tmpArr = $snapArr = array();
		$user_id = intval($getData['uid']);//我的用户ID
		$ticket_sort = intval($getData['classid']);//商品分类ID
		$shop_id = intval($getData['sid']); //店铺ID
		$status = intval($getData['status']); //待审核的商品
		$my = intval($getData['my']); //我上传的商品
		$auth = intval($getData['auth']); //下架的商品
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$start = ($page - 1) * $pagesize;
		//商城商品，店铺商品，显示商品
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$where = "`ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `is_show` = '1' and `city` = '{$city}'";
		//只展示自己发布的商品
		if($my) {
			$where .= " and `user_id` = '{$user_id}'";
		}
		
		//下架商品，不管审核状态和商品分类
		if($auth) {
			$where .= " and `is_auth` = '0'";
		}		
		//上架商品
		else
		{
			//商品有分类
			if($ticket_sort) {
				$where .= " and `ticket_sort` = '{$ticket_sort}'";
			}
			//商品有状态
			if($status) {
				//待审核商品
				if(1 == $status) {
					$where .= " and `ticket_status` = '0'";
				} 
				//已审核商品
				elseif(2 == $status) {
					$where .= " and `ticket_status` = '1'";
				}
			}
			//商品无分类，无状态时
			if(empty($ticket_sort) && empty($status)) {
				if($page == 1) {
					$sql = "select * from `oto_ticket` where {$where} and `ticket_status` = '-1'  order by updated desc";
					$tmpArr = $this->_db->fetchAll($sql);
				}
				//审核不通过
				$where .= " and `ticket_status` <> '-1'";
			}
			
		}
		
		//获取商城分类商品数量
		$data['category'] = $this->getGoodClassNum($getData, $city);
		//获取下架商品数量
		$data['shelves'] = $this->getGoodShelvesNum($getData, $city);
		//店铺商城商品（我的）
		$sql = "select * from `oto_ticket` where {$where} order by updated desc";
		$snapArr = $this->_db->limitQuery($sql, $start, $pagesize);
		if(!empty($tmpArr)) {
			$snapArr = array_merge($tmpArr, $snapArr);
		}
		//格式化商品信息
		$snapArr = $this->formatPicture($snapArr, $user_id, 'commodity');
		
		$data['good'] = $snapArr;
		
		unset($snapArr);
		
		return $data;
	}
	
	/**
	 * 商城商品明细
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getGoodDetail($getData, $city) {
		$imgList = array();
		$ticket_id = intval($getData['tid']);
		
		$row = Model_Home_Ticket::getInstance()->getTicketRow($ticket_id);
		
		$imgList = Model_Admin_Ticket::getInstance()->getWapImg($row['ticket_id']);
		if($imgList) {
			foreach ($imgList as & $imgItem) {
				$widthHeightRow = array();
				$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], 'commodity');
				$row['imgList'][] = array(
						'iid' => $imgItem['id'],
						'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/commodity/" . $imgItem['img_url'],
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
				);
			}
			//第一张缩略图
			if(isset($imgList[0])) {
				$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'commodity', 240);
				$row['img_first'] = array(
						'iid' => $imgList[0]['id'],
						'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$imgList[0]['id']}/w/240/type/commodity",
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
						);
			}
		} else {
			$row['imgList'] = $row['img_first'] = array();
		}
			
		//处理商品价格
		if($row['is_free'] == 1) {
			$row['selling_price'] = 0;
		} elseif($row['is_free'] == 0 && $row['app_price'] > 0) {
			$row['selling_price'] = $row['app_price'];
		}
		unset($row['app_price']);	

		return $row;
	}
	/**
	 * 获取商城分类商品数量
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getGoodClassNum($getData, $city) {
		$where = '';
		$data = array();
		$user_id = intval($getData['uid']);//我的用户ID
		$shop_id = intval($getData['sid']); //店铺ID
		$auth = intval($getData['auth']); //下架的商品
		$status = intval($getData['status']); //待审核的商品
		$my = intval($getData['my']); //我上传的商品
		
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$where = "`ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `is_show` = '1' and `city` = '{$city}'";
		//只展示自己的商品
		if($my) {
			$where .= " and `user_id` = '{$user_id}'";
		}

		//下架商品，不管审核状态和商品分类
		if($auth) {
			$where .= " and `is_auth` = '0'";
		}
		//上架商品
		{
			if($status) {
				//待审核商品
				if(1 == $status) {
					$where .= " and `ticket_status` = '0'";
				}
				//已审核商品
				elseif(2 == $status) {
					$where .= " and `ticket_status` = '1'";
				}
			}
				
		}
		
		$storeArray = $this->getStoreList($city);
		foreach($storeArray as $storeItem) {
			$num = $this->_db->fetchOne("select count(ticket_id) from `oto_ticket` where {$where} and `ticket_sort` = '{$storeItem['store_id']}'"); 
			$data[] = array(
						'id' => $storeItem['store_id'],
						'name' => $storeItem['store_name'],
						'num' => $num
					);
		}
		
		
		$totalNum = $this->_db->fetchOne("select count(ticket_id) from `oto_ticket` where {$where}");
		
		$data = array_merge(array(array('id' => '0', 'name' => '全部', num => $totalNum)), $data);
		
		return array('data' => $data, 'totalNum' => $totalNum);
	}
	/**
	 * 获取下架商品数量
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getGoodShelvesNum($getData, $city) {
		$where = '';
		$user_id = intval($getData['uid']);//我的用户ID
		$shop_id = intval($getData['sid']); //店铺ID
		$my = intval($getData['my']); //我上传的商品
		$ticket_sort = intval($getData['classid']);//商品分类ID
		
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$where = "`ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}' and `is_show` = '1' and `city` = '{$city}'";
		//只展示自己的商品
		if($my) {
			$where .= " and `user_id` = '{$user_id}'";
		}
		//分类商品
		elseif($ticket_sort) {
			$where .= " and `ticket_sort` = '{$ticket_sort}'";
		}
		
		$sql = "select count(ticket_id) from `oto_ticket` where {$where} and `is_auth` = '0'";
		$num = $this->_db->fetchOne($sql);
		
		return $num ? $num : 0;
	}
	/**
	 * 获取店铺未回复咨询数量
	 */
	public function getShopNoReplyNum($getData, $city) {
		$shop_id = intval($getData['sid']);
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$couponSql = "  AND B.`ticket_type` = '{$ticket_type}' AND B.`ticket_status` = '1' AND B.`is_auth` = '1' AND B.`is_show` = 1";
		$sql = "select count(*) from
				`oto_message_thread` A
				left join `oto_ticket` B on A.from_id = B.ticket_id
				where A.`type` = 'commodity' and A.`shop_id` = '{$shop_id}' and A.`reply_time` = '0' {$couponSql}";
		$num = $this->_db->fetchOne($sql);
		return $num;
	}
	/**
	 * 获取店铺咨询列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getInfoList($getData, $city, $pagesize = PAGESIZE) {
		$data = $data1 = $data2 = array();
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$shop_id = intval($getData['sid']);
		$start = ($page - 1) * $pagesize;
		
		//现金券分类ID
		$voucher_ticket_type = $this->getTicketSortById(0, 'ticketsort', 'voucher');
		//商城商品分类ID
		$commodity_ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		//包含现金券和名品购商品的咨询
		if(isset($getData['has_voucher']) && $getData['has_voucher'] == '1') {
			$couponSql = "  AND B.`ticket_type` in ( '{$voucher_ticket_type}', '{$commodity_ticket_type}' ) AND B.`ticket_status` = '1' AND B.`is_auth` = '1'";
				
			if($page == 1) {
				$sql = "select A.*, B.ticket_id, B.ticket_type, B.ticket_class
						from `oto_message_thread` A
						left join `oto_ticket` B on A.from_id = B.ticket_id
						where A.`shop_id` = '{$shop_id}' and A.`reply_time` = '0' {$couponSql}
						order by A.`updated` desc";
				$data1 = $this->_db->fetchAll($sql);
			}
				
			$sql = "select A.*, B.ticket_id, B.ticket_type, B.ticket_class
					from `oto_message_thread` A
					left join `oto_ticket` B on A.from_id = B.ticket_id
					where A.`shop_id` = '{$shop_id}' and A.`reply_time` > '0' {$couponSql}
					order by A.`reply_time` desc";			
		}
		//只有名品购商品的咨询
		else {
			$couponSql = "  AND B.`ticket_type` = '{$commodity_ticket_type}' AND B.`ticket_status` = '1' AND B.`is_auth` = '1'";
			
			if($page == 1) {
				$sql = "select A.*, B.ticket_id, B.ticket_type, B.ticket_class 
						from `oto_message_thread` A
						left join `oto_ticket` B on A.from_id = B.ticket_id
						where A.`shop_id` = '{$shop_id}' and A.`reply_time` = '0' {$couponSql}
						order by A.`updated` desc";
				$data1 = $this->_db->fetchAll($sql);
			}
			
			$sql = "select A.*, B.ticket_id, B.ticket_type, B.ticket_class
					from `oto_message_thread` A
					left join `oto_ticket` B on A.from_id = B.ticket_id
					where A.`shop_id` = '{$shop_id}' and A.`reply_time` > '0' {$couponSql}
					order by A.`reply_time` desc";
		
		}
		$data2 = $this->_db->limitQuery($sql, $start, $pagesize);
		$y_m_d = mktime(0, 0, 0 , date('m'), date('d'), date('Y'));
		$data = array_merge($data1, $data2);
		foreach($data as & $row) {
			$folder = $this->getTicketSortById($row['ticket_type'], 'ticketsort', 'sort_detail_mark');
			list($row['imgList'], $row['img_first']) = $this->formatTicketImg($row['ticket_id'], $folder);	
			//格式化时间
			if($row['updated'] > $y_m_d) {
				if(REQUEST_TIME - $row['updated'] < 60) {
					$row['format_time'] =  intval( (REQUEST_TIME - $row['updated'])) . '秒之前' ;
				} else if(REQUEST_TIME - $row['updated'] < 3600) {
					$row['format_time'] =  intval( (REQUEST_TIME - $row['updated']) / 60 ) . '分钟之前' ;
				} else {
					$row['format_time'] =  intval( (REQUEST_TIME - $row['updated']) / 60 / 60 ) . '小时之前' ;
				}
			}
			//昨天
			elseif($row['updated'] > strtotime('-1 day', $y_m_d) && $row['updated'] < $y_m_d) {
				if($row['updated'] > strtotime('-1 day', $y_m_d) + 12 * 3600) {
					$row['format_time'] = '昨天下午';
				} else {
					$row['format_time'] = '昨天上午';
				}
			}
			//昨天之前
			else {
				$row['format_time'] = date('Y-m-d', $row['updated']);
			}					
		}
		
		return $data;
	}
	/**
	 *  咨询明细
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getInfoShow($getData, $city) {
		$data = $imgList = array();
		if(!isset($getData['type'])) { 
			$getData['type'] = 'commodity';
		}
		
		$data['good'] = $this->getCommodityRow($getData['frid']);
		
		list($data['good']['imgList'], $data['good']['first_img']) = $this->formatTicketImg($getData['frid'], $getData['type']);
		
		$data['detail'] = Model_Api_Message::getInstance()->getPersonalMessage($getData);
		//是否当前营业员的回复
		foreach ($data['detail'] as & $row) {
			$row['is_my_message'] = 0;
			if($row['user_id'] == $getData['uid']) {
				$row['is_my_message'] = 1;
			}
			
			$row['format_time'] = datex($row['created'], 'Y-m-d H:i:s');
		}
		
		return $data;
	}
	
	/**
	 * 获取我的商城商品收藏
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getFavCommodity($getData, $city, $is_cache = false) {
		$user_id = $getData['user_id'];
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$pagesize = !$getData['pagesize'] ? PAGESIZE : intval($getData['pagesize']);
		$start = ($page - 1) * $pagesize;
		$lng = $getData['lng'];
		$lat = $getData['lat'];
		
		$key = $this->_key . '_' . $user_id . '_' . $page;
		$data = $this->getData($key);
		if (!$is_cache || empty($data)) {
			$where = '';
			$lngLatArr = $this->getShopLngLatAll(0, true, $city);
			
			//商城商品，审核通过，上架，显示
			$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
			$where .= "  AND B.`ticket_type` = '{$ticket_type}' AND B.`ticket_status` = '1' AND B.`is_auth` = '1' AND B.`is_show` = 1 AND B.`start_time`<'".REQUEST_TIME."' AND B.`end_time`>'".REQUEST_TIME."'";
			$sql = "select B.*
					from `oto_ticket_favorite` A
					left join `oto_ticket` B on A.ticket_id = B.ticket_id
					where A.`user_id` = '{$user_id}' {$where} and B.`city` = '{$city}'
					order by A.created desc";
			
			$data = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($data as & $row) {
				$imgList = array();
				//商品原始图片
				$imgList = Model_Admin_Ticket::getInstance()->getWapImg($row['ticket_id']);
				foreach ($imgList as & $imgItem) {
					$widthHeightRow = array();
					$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], 'commodity');
					$row['imgList'][] = array(
							'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/commodity/" . $imgItem['img_url'],
							'width' => $widthHeightRow['width'],
							'height' => $widthHeightRow['height']
					);
				}
				//第一张缩略图
				if(isset($imgList[0])) {
					$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'commodity', 240);
					$row['img_first'] = array(
							'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$imgList[0]['id']}/w/240/type/commodity",
							'width' => $widthHeightRow['width'],
							'height' => $widthHeightRow['height']
							);
				}
					
				//处理商品价格
				if($row['is_free'] == 1) {
					$row['selling_price'] = 0;
				} elseif($row['is_free'] == 0 && $row['app_price'] > 0) {
					$row['selling_price'] = $row['app_price'];
				}
				unset($row['app_price']);
				//折扣
				$row['discount'] = round(($row['selling_price'] / $row['par_value']) * 10 , 1);	

				//距离
				if($lng && $lat) {
					if(isset($lngLatArr[$row['shop_id']])) {
						$row['distance'] = getDistance($lat, $lng, $lngLatArr[$row['shop_id']]['lat'], $lngLatArr[$row['shop_id']]['lng']);
					}
				} else {
					$row['distance'] = 0;
				}
			}
			
			unset($lngLatArr);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	
	/**
	 * 商户订单列表
	 * @param unknown_type $getData
	 */
	public function getOrderListToShop($getData, $page, $page_size = PAGESIZE) {
		$shop_id = intval($getData['sid']);
		$receipt_status = !isset($getData['receipt_status']) ? -1 : $getData['receipt_status'];
		$order_status = !isset($getData['order_status']) ? -1 : $getData['order_status'];
		$display_status = !isset($getData['display_status']) ? -999 : $getData['display_status'];
		$order_time_slot = !isset($getData['order_time_slot']) ? -1 : intval($getData['order_time_slot']);
		$param = array(
					'shop_id' => $shop_id,
					'receipt_status' => $receipt_status, //取货方式
					'order_status' => $order_status, //订单状态
					'display_status' => $display_status,
					'order_time_slot' => $order_time_slot //时间段
				);
		
		$data = Custom_AuthTicket::getOrderListToShop($param, $page, $page_size);
		if($data['code'] == 1) {
			foreach($data['message']['Result'] as & $row) {
				$ticketRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($row['ProductId']);
				//获取订单相关商品的图片（先从wap图片中查找，再到富文本编辑器中查找）
				$imgList = array();
				$imgList = Model_Admin_Ticket::getInstance()->getWapImg($ticketRow['ticket_id']);
				$row["FirstImg"] = array();
				$firstImgRow = array();
				if( is_array($imgList) && !empty($imgList)){
					foreach ($imgList as & $imgItem) {
						$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], 'commodity');
						$row['imgList'][] = array(
									'iid' => $imgItem['id'],
									'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/commodity/" . $imgItem['img_url'],
									'width' => $widthHeightRow['width'],
									'height' => $widthHeightRow['height']
								);
					}
					
					$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'commodity' ,240);
					if($widthHeightRow) {
						$row["FirstImg"] = array(
									'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$imgList[0]['id']}/w/240/type/commodity",
									'width' => $widthHeightRow['width'],
									'height' => $widthHeightRow['height']
								);
					}
				} else {
					$imgList = Model_Admin_Ticket::getInstance()->getImg($ticketRow['ticket_id']);
					foreach ($imgList as & $imgItem) {
						$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], 'ticket');
						$row['imgList'][] = array(
									'id' => $imgItem['id'],
									'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/ticket/" . $imgItem['img_url'],
									'width' => $widthHeightRow['width'],
									'height' => $widthHeightRow['height']
								);
					}
					$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'ticket' ,240);
					if($widthHeightRow) {
						$row["FirstImg"] = array(
									'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/id/{$imgList[0]['id']}/w/240/type/ticket",
									'width' => $widthHeightRow['width'],
									'height' => $widthHeightRow['height']
								);
					}
				}
				$row['ticket_title'] = $ticketRow['ticket_title'];			
			}
		}
		return $data;
	}
	/**
	 * 商户订单统计
	 */	
	public function getMerchantStatInfo($getData) {
		$shop_id = intval($getData['sid']);
		$user_id = intval($getData['uid']);
		$userRow = $this->getUserByUserId($user_id, 'user_id, uuid, user_name');
		$order_time_slot = !isset($getData['order_time_slot']) ? -1 : intval($getData['order_time_slot']);
		$param = array(
				'shop_id' => $shop_id,
				'uuid' => $userRow['uuid'],
				'order_time_slot' => $order_time_slot //时间段
		);
		$data = Custom_AuthTicket::getMerchantStatInfo($param);
		return $data;
	}
	/**
	 * 商户订单详情
	 * @param unknown_type $getData
	 */
	public function getOrderInfoToShop($getData) {
		$shop_id = intval($getData['sid']);
		$user_id = intval($getData['uid']);
		$order_no = $getData['order_no'];
		$order_id = $getData['order_id'];
		
		$param = array(
		    "shop_id"	=> $shop_id,
		    "order_no"	=> $order_no,
		    "order_id"	=> $order_id
		);
		
		$data = Custom_AuthTicket::getOrderInfoToShop($param);
		if($data['Code'] == 1) {
			$ticketRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($data['Result']['ProductId']);
			//获取订单相关商品的图片（先从wap图片中查找，再到富文本编辑器中查找）
			$imgList = array();
			$imgList = Model_Admin_Ticket::getInstance()->getWapImg($ticketRow['ticket_id']);
			$data['Result']["FirstImg"] = array();
			$firstImgRow = array();
			if( is_array($imgList) && !empty($imgList)){
				$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'commodity' ,240);
				$data['Result']["FirstImg"] = array(
						'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$imgList[0]['id']}/w/240/type/commodity",
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
						);
			} else {
				$imgList = Model_Admin_Ticket::getInstance()->getImg($ticketRow['ticket_id']);
				$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'ticket' ,240);
				$data['Result']["FirstImg"] = array(
						'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/id/{$imgList[0]['id']}/w/240/type/ticket",
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
						);
			}
			$data['Result']['ticket_title'] = $ticketRow['ticket_title'];
			
			//处理商品价格
			if($ticketRow['is_free'] == 1) {
				$ticketRow['selling_price'] = 0;
			} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] > 0) {
				$ticketRow['selling_price'] = $ticketRow['app_price'];
			}
			$data['Result']['selling_price'] = $ticketRow['selling_price'];
		}
		return $data;		
	}
	/**
	 * 订单发货
	 */
	public function setOrderDeliver($getData) {
		$shop_id = intval($getData['sid']);
		$user_id = intval($getData['uid']);
		$order_no = $getData['order_no'];
		$userInfo = $this->getUserByUserId($user_id, 'uuid, user_name');
		$express_company = $getData['express_company'];
		$express_number = $getData['express_number'];
		
		$param = array(
				"shop_id"	=> $shop_id,
				"order_no"	=> $order_no,
				"user_name"	=> $userInfo['user_name'],
				'uuid' => $userInfo['uuid'],
				'express_company' => $express_company,
				'express_number' => $express_number,
		);
		
		$data = Custom_AuthTicket::setOrderDeliver($param);
		return $data;		
	}
	/**
	 * 商户检查验证码
	 * @param unknown_type $getData
	 */
	public function checkVCodeShop($getData) {
		$shop_id = intval($getData['sid']);
		$code = $getData['code'];
		
		$param = array(
				"shop_id"	=> 	$shop_id,
				"code"		=> 	$code,
				
		);
		
		$data = Custom_AuthTicket::checkVCodeShop($param);
		return $data;		
	}
	/**
	 * 商户使用验证码
	 * @param unknown_type $getData
	 */
	public function useVCodeToShop($getData) {
		$shop_id = intval($getData['sid']);
		$code = $getData['code'];
		$user_id = intval($getData['uid']);
		$userInfo = $this->getUserByUserId($user_id, 'uuid, user_name');
		$version = $getData['version'];
		
		$param = array(
				"shop_id"	=> 	$shop_id,
				"code"		=> 	$code,
				"user_name"	=> $userInfo['user_name'],
				'uuid' => $userInfo['uuid'],
				'version' => $version
		);
		
		$data = Custom_AuthTicket::useVCodeToShop($param);
		return $data;		
	}
	/**
	 * 格式化图片和商品信息
	 * @param unknown_type $data
	 * @param unknown_type $user_id
	 * @param unknown_type $folder
	 */
	public function formatPicture($data, $user_id, $folder) {
		$y_m_d = mktime(0, 0, 0 , date('m'), date('d'), date('Y'));
		foreach($data as & $row) {
			$imgList = array();
			//商品原始图片(非图文混排)
			$imgList = Model_Admin_Ticket::getInstance()->getWapImg($row['ticket_id']);
			if($imgList) {
				foreach ($imgList as & $imgItem) {
					$widthHeightRow = array();
					$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], $folder);
					$row['imgList'][] = array(
							'iid' => $imgItem['id'],
							'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/{$folder}/" . $imgItem['img_url'],
							'width' => $widthHeightRow['width'],
							'height' => $widthHeightRow['height']
							);
				}
				//第一张缩略图
				if(isset($imgList[0])) {
					$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], $folder, 240);
					$row['img_first'] = array(
							'iid' => $imgList[0]['id'],
							'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$imgList[0]['id']}/w/240/type/{$folder}",
							'width' => $widthHeightRow['width'],
							'height' => $widthHeightRow['height']
							);
				}
			} else {
				//商品原始图片(图文混排)
				$imgList = Model_Admin_Ticket::getInstance()->getImg($row['ticket_id']);
				if($imgList) {
					foreach ($imgList as & $imgItem) {
						$widthHeightRow = array();
						$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], 'ticket');
						$row['imgList'][] = array(
								'id' => $imgItem['id'],
								'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/ticket/" . $imgItem['img_url'],
								'width' => $widthHeightRow['width'],
								'height' => $widthHeightRow['height']
						);
					}
					//第一张缩略图
					if(isset($imgList[0])) {
						$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'ticket', 240);
						$row['img_first'] = array(
								'id' => $imgList[0]['id'],
								'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/id/{$imgList[0]['id']}/w/240/type/ticket",
								'width' => $widthHeightRow['width'],
								'height' => $widthHeightRow['height']
								);
					}
				} else {
					$row['imgList'] = $row['img_first'] = array();
				}
			}
	
			//处理商品价格
			if($row['is_free'] == 1) {
				$row['selling_price'] = 0;
			} elseif($row['is_free'] == 0 && $row['app_price'] > 0) {
				$row['selling_price'] = $row['app_price'];
			}
			unset($row['app_price']);
			//折扣
			$row['discount'] = round(($row['selling_price'] / $row['par_value']) * 10 , 1);
			//我发布的
			$row['is_my_publish'] = 0;
			if($user_id == $row['user_id']) {
				$row['is_my_publish'] = 1;
			}
			//格式化时间
			if($row['updated'] > $y_m_d) {
				if(REQUEST_TIME - $row['updated'] < 60) {
					$row['format_time'] =  intval( (REQUEST_TIME - $row['updated'])) . '秒之前' ;
				} elseif(REQUEST_TIME - $row['updated'] < 3600) {
					$row['format_time'] =  intval( (REQUEST_TIME - $row['updated']) / 60 ) . '分钟之前' ;
				} else {
					$row['format_time'] =  intval( (REQUEST_TIME - $row['updated']) / 60 / 60 ) . '小时之前' ;
				}
			}
			//昨天
			elseif($row['updated'] > strtotime('-1 day', $y_m_d) && $row['updated'] < $y_m_d) {
				if($row['updated'] > strtotime('-1 day', $y_m_d) + 12 * 3600) {
					$row['format_time'] = '昨天下午';
				} else {
					$row['format_time'] = '昨天上午';
				}
			}
			//昨天之前
			else {
				$row['format_time'] = date('Y-m-d', $row['updated']);
			}
		}
	
		return $data;
	}
	
	/**
	 * 格式化图片
	 * @param unknown_type $row 记录
	 * @param unknown_type $folder 文件夹
	 * @param unknown_type $img_type 1：原始图;  2：第一张缩略图; 3：原始图and第一张缩略图 ;
	 * @param unknown_type $w 宽度
	 */
	public function formatTicketImg( $ticket_id , $folder, $img_type = 3 ,$w = 240){
		$originalImgList = $imgFistThumb = $thumbImgList = array();
		$imgList = Model_Admin_Ticket::getInstance()->getWapImg($ticket_id);
		if($imgList) {
			if( $img_type == 1 || $img_type == 3 ){//原始图片
				foreach ($imgList as & $imgItem) {
					$widthHeightRow = array();
					$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], $folder);
					$originalImgList[] = array(
							'iid' => $imgItem['id'],
							'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/{$folder}/" . $imgItem['img_url'],
							'width' => $widthHeightRow['width'],
							'height' => $widthHeightRow['height']
							);
				}
			}
			if( $img_type == 2 || $img_type == 3 ){//第一张缩略图
				if(isset($imgList[0])) {
					$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], $folder, $w);
					$imgFistThumb = array(
							'iid' => $imgList[0]['id'],
							'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$imgList[0]['id']}/w/{$w}/type/{$folder}",
							'width' => $widthHeightRow['width'],
							'height' => $widthHeightRow['height']
							);
				}
			}
		} else {
			//商品原始图片(图文混排)
			$imgList = Model_Admin_Ticket::getInstance()->getImg($ticket_id);
			if($imgList) {
				//原始图片列表
				if( $img_type == 1 || $img_type == 3 ){
					foreach ($imgList as & $imgItem) {
						$widthHeightRow = array();
						$widthHeightRow = $this->getImageWidthHeight($imgItem['img_url'], 'ticket');
						$originalImgList[] = array(
								'id' => $imgItem['id'],
								'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/ticket/" . $imgItem['img_url'],
								'width' => $widthHeightRow['width'],
								'height' => $widthHeightRow['height']
						);
					}
				}
				//第一张缩略图
				if( $img_type == 2 || $img_type == 3 ){
					if(isset($imgList[0])) {
						$widthHeightRow = $this->getImageWidthHeight($imgList[0]['img_url'], 'ticket', $w);
						$imgFistThumb = array(
								'id' => $imgList[0]['id'],
								'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/id/{$imgList[0]['id']}/w/{$w}/type/ticket",
								'width' => $widthHeightRow['width'],
								'height' => $widthHeightRow['height']
								);
					}
				}
			}
		}
		return array($originalImgList , $imgFistThumb);
	}
	//店长收益主页
	public function getManagerProfitMain( $getData ){
		$user_id = $getData["uid"];//用户id
		$shop_id = $getData["sid"];//店铺id
		$data = array(
				"total_profit"=>0,//总收益
				"withdraw_profit"=>0,//提现
				"remain_profit"=>0,//余额
				"profit_list"=>array()//返现列表
				);
		//获取该店所有的营业员和店长
		$shopUserIds = $this->getShopUserIds($shop_id);
		if( empty($shopUserIds) ){
			return $data;
		}
		//总收益
		$totalProfit = $this->getShopTotalProfit(array("sid"=>$shop_id,"shop_user_ids"=>$shopUserIds));
		$data["total_profit"] = $totalProfit;
		//提现金额
		$shopUserIdStr = implode(",", $shopUserIds);
		$sql = "select sum(amount) 
				from `oto_task_money` 
				where `user_id` IN ({$shopUserIdStr})";
		$withdrawProfit = $this->_db->fetchOne($sql);
		$data["withdraw_profit"] = $withdrawProfit;
		//余额
		$remainProfit = $totalProfit - $withdrawProfit;
		$data["remain_profit"] = $remainProfit;
		
		$data["profit_list"] = $this->getProfitList(array(
					"sid"=>$shop_id,
					"shop_user_ids"=>$shopUserIds,
					"page"=>1,
					"pagesize"=>1
				));
		$sql = "UPDATE `oto_user_shop_commodity` SET `last_profit_view_time`='".REQUEST_TIME."' WHERE `shop_id`='{$shop_id}' AND `user_id`='{$user_id}'";
		$this->_db->query( $sql );
		return $data;
	}
	
	/**
	 * 获取指定时间的店铺总收益
	 * @param unknown_type $param
	 */
	public function getShopTotalProfit($param){
		$shop_id = $param["sid"];
		$shopUserIds = array();
		if( !empty($param["shop_user_ids"])  ){
			$shopUserIds = $param["shop_user_ids"];
		}else{
			$shopUserIds = $this->getShopUserIds($shop_id);
		}
		if( empty($shopUserIds) ){
			return 0;
		}
		$where = $this->db_create_in($shopUserIds,'`user_id`');
		if( !empty($param["stime"]) ){
			$where .= " AND `created`>'{$param["stime"]}'";
		}
		if( !empty($param["etime"]) ){
			$where .= " AND `created`<='{$param["etime"]}'";
		}
		$shopUserIdStr = implode(",", $shopUserIds);
		
		$sql = "SELECT SUM(`award`)
				FROM `oto_task_clerk_coupon`
				WHERE `shop_id`='{$shop_id}' AND `type`='1' AND {$where}";
		return 0+$this->_db->fetchOne($sql);
	}
	
	//收益详情
	public function getManagerProfitDetail( $getData ){
		$shop_id = intval($getData["sid"]);
		$user_id = intval($getData["uid"]);
		$stime = $getData["stime"];
		$etime = $getData["etime"];
		
		//获取店铺营业员和店长列表
		$sql = "SELECT B.`user_id`,B.`uuid`,B.`real_name`,B.`user_name`,B.`mobile`,B.`user_type`
				FROM `oto_user_shop_commodity` AS A 
				LEFT JOIN `oto_user` AS B ON B.`user_id`=A.`user_id` 
				WHERE A.`shop_id`='{$shop_id}'";
		$user_list = $this->_db->fetchAll( $sql );
		//获取店铺用户对应的发布商品数量（通过审核的）
		$userCommodityNum = $this->getShopUserCommodityNum($shop_id, $stime, $etime);
		$commodityTotal = $replyTotal = $shipNum = $fromMentionNum = $orderNum =0;//商品总数、回复咨询总数、发货总数、自提总数、订单总数
		foreach( $userCommodityNum as $k => $v ){
			$commodityTotal += $v;
		}
		foreach( $user_list as &$row ){
			if( $row["real_name"] ){
				$row["user_name"] = $row["real_name"];
			}else if( empty($row["user_name"]) ){
				$row["user_name"] = substr($row['mobile'], -4);
			}
			//查看店铺每个员工回复的数量
			$row["reply_num"] = $this->getUserReplyThreadNum($user_id, $shop_id, $stime, $etime);
			$replyTotal += $row["reply_num"];
			$cNum = 0;//用户发布的商品数量
			if( array_key_exists($row["user_id"], $userCommodityNum) ){
				$cNum = $userCommodityNum[$row["user_id"]];
			}
			$row["commodity_num"] = $cNum.( ( $cNum==0||$commodityTotal==0 )?'':' ('.round($cNum*100/$commodityTotal).'%)' );
			list($row["Ship_num"],$row["from_mention_num"]) = $this->getUserOrderNum($shop_id, $user_id, $getData["sdate"], $getData["edate"]);
			$shipNum += $row["Ship_num"];//已发货数
			$fromMentionNum += $row["from_mention_num"];
		}
		$orderNum = $shipNum+$fromMentionNum;//订单总数
		$totalProfit = $this->getShopTotalProfit($getData);//总收益
		$transactionMoney = $this->getProfitList($getData,1);//交易金额？
		$data = array();
		//区域1
		$data["section_one"] = array(
				"order_num"=>strval($orderNum),//订单数量
				"transaction_money"=>strval($transactionMoney),//交易金额
				"total_profit"=>strval($totalProfit)//总收益
		);
		//区域2
		//根据订单总数量计算已发货和自提的百分比
		if( $orderNum > 0 ){
			foreach ( $user_list as &$row ){
				if( $row["Ship_num"] > 0 ){//已发货数
					$row["Ship_num"] .= " (".round($row["Ship_num"]*100/$orderNum)."%)";
				}
				if( $row["from_mention_num"] > 0 ){//自提数
					$row["from_mention_num"] .= " (".round($row["from_mention_num"]*100/$orderNum)."%)";
				}
			}
		}

		$data["section_two"] = empty($user_list)?array():$user_list; //店内信息列表
		$getData["page"] = 1;
		$getData["pagesize"] = 5;
		//区域3
		$data["section_three"] = $this->getProfitList($getData); //返现列表
		return $data;
	}
	
	/**
	 * 获取店员（营业员，店长）发货和自提数量
	 * @param unknown_type $shop_id 店铺id
	 * @param unknown_type $user_id 用户id
	 * @param unknown_type $sdate 开始日期 Y-m-d H:i:s
	 * @param unknown_type $edate 结束日期
	 */
	public function getUserOrderNum( $shop_id , $user_id , $sdate , $edate ){
		$userRow = $this->getUserByUserId($user_id,"uuid");
		//获取用户订单
		$param = array(
				'shop_id' => $shop_id,
				'uuid' => $userRow['uuid'],
				'order_time_slot' => -1, //时间段
				'order_start_time'=>$sdate,
				'order_end_time'=>$edate
		);
		$data = Custom_AuthTicket::getMerchantStatInfo($param);
		if($data['Code'] == 1) {
			$result = $data['Result'];
			return array($result['DeliveredCount'],
						$result['VerifyCount']
					);
		}
		return array(0,0);
	}
	
	/**
	 * 获取用户回复的咨询数
	 * @param unknown_type $user_id 用户id
	 * @param unknown_type $stime 查询开始时间
	 * @param unknown_type $etime 查询结束时间
	 * @return number
	 */
	public function getUserReplyThreadNum( $user_id, $shop_id, $stime, $etime ){
		$sql = "SELECT A.* 
				FROM `oto_message_post` AS A 
				LEFT JOIN `oto_message_thread` AS B ON B.`tid` = A.`tid`
				WHERE A.`user_id`='{$user_id}' AND B.`shop_id`='{$shop_id}' AND A.`created`>'{$stime}' AND A.`created`<='{$etime}' 
				GROUP BY `tid`";
		$res = $this->_db->fetchAll( $sql );
		return count($res);
	}
	
	/**
	 * 获取指定时间内店员通过审核的商品数量
	 * @param unknown_type $shop_id 店铺id
	 * @param unknown_type $stime 查询开始时间
	 * @param unknown_type $etime 查询结束时间
	 * @return multitype:unknown array()
	 */
	public function getShopUserCommodityNum( $shop_id , $stime , $etime ){
		$shopUserIds = $this->getShopUserIds($shop_id);
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$where = "`shop_id`='{$shop_id}' AND `ticket_type` = '{$ticket_type}' AND `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1";
		$where .= " AND `user_id` IN(".implode(",", $shopUserIds).") AND `audit_time`>'{$stime}' AND `audit_time`<='{$etime}'";
		$sql = "SELECT `user_id`,COUNT(*) AS cnt FROM `oto_ticket` WHERE {$where} GROUP BY `user_id`";
		$res = $this->_db->fetchAll($sql);
		$data = array();
		foreach( $res as $row ){
			$data[$row['user_id']] = $row['cnt']; 
		}
		return $data;
	}
	
	/**
	 * 获取返现记录列表，返现总额，返现记录数
	 * @param unknown_type $getData
	 * @param unknown_type $type 类型 0：返现记录列表 1：返现总额 2：返现记录数
	 */
	public function getProfitList( $getData , $type = 0 ){
		$page = intval($getData["page"])<1?1:intval($getData["page"]);
		$pageSize = intval($getData["pagesize"])>0?intval($getData["pagesize"]):6;
		$start = ($page-1)*$pageSize;
		$where = "";
		$shop_id = $getData["sid"];
		if( !empty($getData["stime"]) ){
			$where .= " AND A.`created`>'{$getData["stime"]}'";
		}
		if( !empty($getData["etime"]) ){
			$where .= " AND A.`created`<='{$getData["etime"]}'";
		}
		$shopUserIds = "";
		if( !empty($getData["shop_user_ids"]) ){
			$shopUserIds = $getData["shop_user_ids"];
		}else{
			$shopUserIds = $this->getShopUserIds($shop_id);
		}
		$shopUserIdStr = implode(",", $shopUserIds);
		if( $type == 1 ){//返现总额（目前商品价格部分未同步过来，需要根据返利比率计算;最好把交易价格同步到order_price）
			if( !$shopUserIdStr ){
				return 0;
			}
			$sql = "SELECT A.*,B.rebates FROM `oto_task_clerk_coupon` AS A
					LEFT JOIN `oto_ticket` AS B ON B.`ticket_id` = A.`ticket_id`
					WHERE A.`type`='1' AND A.`shop_id`='{$shop_id}' AND A.`user_id` IN ({$shopUserIdStr}) {$where}";
			$data = $this->_db->fetchAll($sql);
			$totalMoney = 0;
			foreach($data as $row){
				if( $row["order_price"]>0 ){
					$totalMoney += $row["order_price"];
				}else if( $row["rebates"]>0 ){
					$totalMoney += $row["award"]/$row["rebates"];
				}
			}
			return $totalMoney;
		}else if( $type == 2 ){//返现记录数
			if( !$shopUserIdStr ){
				return 0;
			}
			$sql = "SELECT COUNT(*) FROM `oto_task_clerk_coupon` AS A
					WHERE A.`shop_id`='{$shop_id}' {$where} AND A.`user_id` IN ({$shopUserIdStr}) AND A.`type`='1'";
			return $this->_db->fetchOne($sql);
		}else{//type为0；返现列表
			if( !$shopUserIdStr ){
				return array();
			}
			$sql = "SELECT * FROM `oto_task_clerk_coupon` AS A
					WHERE A.`shop_id`='{$shop_id}' {$where} AND A.`user_id` IN ({$shopUserIdStr}) AND A.`type`='1'
					ORDER BY `created` DESC
					LIMIT {$start},{$pageSize}";
			$data = $this->_db->fetchAll( $sql );
			foreach( $data as &$row ){
				$row["created"] = datex($row["created"],"y-m-d");
				$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($row["ticket_id"]);
				if( $ticketRow["order_price"] == 0 && $ticketRow["rebates"]!=0 ){
					$row["order_price"] = $row["award"]/$ticketRow["rebates"];//根据返利比例计算出支付金额
				}
				$row["award"] = "+".$row["award"]."元";
			}
			return empty($data)?array():$data;
		}
	}
	
	/**
	 * 获取店铺用户
	 * @param unknown_type $shop_id 店铺id
	 */
	public function getShopUserIds( $shop_id ){
		$sql = "SELECT `user_id` 
				FROM `oto_user_shop_commodity` 
				WHERE `shop_id`='{$shop_id}'";
		$shopUserIds = $this->_db->fetchCol($sql);
		return $shopUserIds;
	}
	
	/**
	 * 获取店铺未提现金额
	 * @param unknown_type $shopUserIdStr 用户ids字符串，中间以逗号隔开
	 */
	public function getShopRemainProfit( $shopUserIdStr ){
		$sql = "select sum(award)
				from `oto_task_log`
				where `user_id` IN ({$shopUserIdStr})";
		$logBonus = $this->_db->fetchOne($sql);
		$sql = "select sum(amount)
				from `oto_task_money`
				where `user_id` IN ({$shopUserIdStr}) and `operat_status` = '1'";
		$appBonus = $this->_db->fetchOne($sql);
		return $logBonus-$appBonus;
	}
	
	//获取提现记录
	public function getWithdrawList( $getData ){
		$page = intval($getData["page"])<1?1:intval($getData["page"]);
		$pageSize = intval($getData["pagesize"])>0?intval($getData["pagesize"]):6;
		$start = ($page-1)*$pageSize;
		$shopUserIds = $this->getShopUserIds($getData["sid"]);
		$shopUserIdStr = implode(",", $shopUserIds);
		$sql = "SELECT `amount`,`bank_name`,`paypal_name`,`app_time`
				FROM `oto_task_money` 
				WHERE `user_id` IN ({$shopUserIdStr})
				ORDER BY `app_time` DESC 
				LIMIT {$start},{$pageSize}";
		$data = $this->_db->fetchAll($sql);
		foreach( $data as &$row){
			$row["amount"] .= "元";
			$row["app_time"] = datex($row["app_time"],"y-m-d"); 
		}
		return empty($data)?array():$data;
	}
}
