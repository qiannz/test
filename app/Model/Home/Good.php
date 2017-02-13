<?php 
class Model_Home_Good extends Base
{
	private static $_instance;
	private $_table = 'oto_good';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getAjaxGoodList($store_id, $brand_id, $region_id, $circle_id, $market_id, $shop_id, $page, $order, $pagesize = 20) {
		//缓存键值
		$cacheKey = 'get_ajax_good_list_' . "_{$this->_city}_{$store_id}_{$brand_id}_{$region_id}_{$circle_id}_{$market_id}_{$shop_id}_{$page}_{$order}";
		$data = $this->getData($cacheKey);
		
		if (empty($data)) {
			$snapArray = $snapData = array();
			
			$where = "A.`good_status` <> '-1' and A.`is_auth` <> '-1' and `is_del` = '0' and `city` = '{$this->_city}'"; // and A.`img_url` <> ''
			$orderby = '';
			
			if($store_id) 	$where .= " and A.`store_id` = '{$store_id}'";
			if($brand_id) 	$where .= " and A.`brand_id` = '{$brand_id}'";
			if($region_id) 	$where .= " and A.`region_id` = '{$region_id}'";
			if($circle_id)	$where .= " and A.`circle_id` = '{$circle_id}'";
			if($market_id)	$where .= " and A.`market_id` = '{$market_id}'";
			if($shop_id) 	$where .= " and A.`shop_id` = '{$shop_id}'";
			
			if($order == 1) {
				$orderby = "order by `created` desc";				
			} elseif ($order == 2) {
				$orderby = "order by `clicks` desc, `created` desc";
			}
			
			$sqlC = "select count(A.good_id) from `oto_good` A where {$where}";
			$totalNum = $this->_db->fetchOne($sqlC);
			 
			$sql = "select 
					`good_id`, `good_name`, `shop_id`, `shop_name`, `dis_price`, `favorite_number`, `concerned_number`, `is_auth`,
					(select `img_url` from `oto_good_img` where `good_id` = A.good_id order by is_first desc, good_img_id asc limit 1) as `img_url`
					from `oto_good` A
					where {$where} {$orderby}";
			
			$snapArray = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
			foreach($snapArray as $key => $snap) {
				if($snap['img_url'] && is_file(ROOT_PATH . 'web/data/good/220/' . $snap['img_url'])) {
					list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/good/220/' . $snap['img_url']);						
					$snapArray[$key]['img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/good/220/' . $snap['img_url'];
				} else {
					list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/default.jpg');
					$snapArray[$key]['img_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL']. '/data/default.jpg';
				}
				$snapArray[$key]['width'] = $width;
				$snapArray[$key]['height'] = $height;
				$snapArray[$key]['dis_price'] = floor($snap['dis_price']);
			}
			$snapData['totalNum'] = $totalNum;
			$snapData['order'] = $order;
			$snapData['data'] = $snapArray;
			$snapData['totalPage'] = ceil($totalNum / $pagesize);
			$data = $snapData;
			unset($snapArray, $snapData);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
	
	public function getGoods($gid, $order = 1, $ticket = true, $pre_and_next_good = true, $good_or_wap = true) {
		$cacheKey = 'get_good_view_'. $gid .  '_' . intval($ticket) . '_' . intval($pre_and_next_good) . '_' . intval($good_or_wap);
		$data = $this->getData($cacheKey);
		if (empty($data)) {
			// 商品
			$data = $this->select(
									"good_id = '{$gid}'", 
									'oto_good',
									'good_id,good_name,shop_id,dis_price,org_price,user_name,created,concerned_number,favorite_number,is_auth,clicks,is_del',
									'',
									true
								 );
			if($ticket) {
				// 优惠券
				$sql_ticket = "select t.ticket_id, t.ticket_title
								from `oto_ticket_good` as `g`
								left join `oto_ticket` as `t` on `g`.`ticket_id` = `t`.`ticket_id`
								where `g`.`good_id` = '{$gid}' 
										and `t`.`is_auth` = '1'
										and `g`.`start_time` < '" .REQUEST_TIME. "' 
										and `g`.`end_time` > '" .REQUEST_TIME. "'
										and `t`.`ticket_status` = '1'
								order by `g`.`created` desc";
				$data['ticket'] = $this->_db->fetchAll($sql_ticket);
			}
			
			if($good_or_wap) {
				//商品图片信息
				$imgArr = $this->select("good_id = '{$gid}'", 'oto_good_img', 'good_img_id, img_url', 'is_first desc');
				foreach ($imgArr as $key=>$value) {
					$imgArr[$key]['img_url_small'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/good/small/' . $value['img_url'];
				}			
			} else {
				$imgArr = $this->select("good_id = '{$gid}'", 'oto_good_img', 'good_img_id, img_url', 'is_first desc', true);
				$imgArr['img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/good/640/' . $imgArr['img_url'];
			}
			$data['img'] = $imgArr;
			
			if($pre_and_next_good) {
				$data['preUrl'] = $this->getPreGoodUrl($gid, $order, $data['clicks'], $this->_city);
				$data['nextUrl'] = $this->getNextGoodUrl($gid, $order, $data['clicks'], $this->_city);
			}
			
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
	
	public function getShop($sid) {
		$cacheKey = 'get_shop_info_'. $sid;
		$data = $this->getData($cacheKey);
		if (empty($data)) {
			$data = $this->select(
									"shop_id = '{$sid}'", 
									'oto_shop', 
									'shop_id, shop_name, region_id, circle_id, shop_address, phone, brand_id, brand_name, lng, lat', 
									'', 
									true
								);
			//高德地图经纬度转百度地图
			$germanToBaiduRow = $this->germanToBaidu($data['lng'], $data['lat']);
			$data['lng'] = $germanToBaiduRow['lng'];
			$data['lat'] = $germanToBaiduRow['lat'];
			$data['circle_name'] = $this->_db->fetchOne("select circle_name from oto_circle where circle_id = '{$data['circle_id']}'");
			if($data['brand_id']) {
				$data['brand_detail'] = $this->select("brand_id = '{$data['brand_id']}'", 'oto_brand', 'brand_name_zh, brand_name_en, is_enable', '', true);
			}
			$this->setData($cacheKey, $data);
		}
		return $data;
	}

	public function submitGood($getData, $city='sh') {
		$user_name = $getData['user_name'];
		$user_id = $getData['user_id'];
		$img = trim($getData['img'], ',');
		$good_name = Custom_String::HtmlReplace(trim($getData['good_name']), 1);
		$org_price = intval($getData['list_price']);
		$dis_price = intval($getData['dis_price']);
		$region_id = intval($getData['region_id']);
		$circle_id = intval($getData['circle_id']);
		$shop_id = intval($getData['shop_id']);
		$shop_name = Custom_String::HtmlReplace(trim($getData['shop_name']), -1);
		$address = Custom_String::HtmlReplace(trim($getData['address']), 1);
		$is_auth = $getData['is_auth'] == 1 ? 1 : 0;
		if($shop_id) {
			$shopRow = $this->getShopFieldById($shop_id);
			$shop_name = $shopRow['shop_name'];
			$brand_id = $shopRow['brand_id'];
			$store_id = $shopRow['store_id'];
		} else {
			$lng = $lat = 0;
			$lngLatString = $this->getLatitudeAndLongitudeFormamap($address);
			list($lng, $lat) = explode(',', $lngLatString);
			
			if(!Model_Home_Shop::getInstance()->repeatShop($shop_name, 0, $this->_city)) {
				$pack_id = $this->getDefaultPack($this->_city);
				$paramShop = array(
						'pack_id' => $pack_id,
						'shop_name' => $shop_name,
						'region_id' => $region_id,
						'circle_id' => $circle_id,
						'shop_address' => $address,
						'lng' => $lng,
						'lat' => $lat,
						'created' => REQUEST_TIME,
						'city' => $this->_city
					);
				$shop_id = $this->_db->insert('oto_shop', $paramShop);
			}
		}
		
		$param = array(
				'good_name' => $good_name,
				'shop_id'   => $shop_id,
				'shop_name' => $shop_name,
				'user_id'   => $user_id,
				'user_name' => $user_name,				
				'org_price' => $org_price,
				'dis_price' => $dis_price,
				'brand_id'	=> $brand_id ? $brand_id : 0,
				'store_id'  => $store_id ? $store_id : 0,
				'region_id' => $region_id,
				'circle_id' => $circle_id,
				'is_auth' 	=> $is_auth,
				'created'   => REQUEST_TIME,
				'city' => $city
		);	 

		$insert_good_id = $this->_db->insert($this->_table, $param);
		if($insert_good_id && $insert_good_id > 0) {
			$imgArray = explode(',', $img);
			foreach ($imgArray as $value) {
				if($value) {
					$img_url = str_replace($GLOBALS['GLOBAL_CONF']['IMG_URL']. '/buy/good/small/', '', $value);
					$this->_db->update('oto_good_img', array('good_id' => $insert_good_id), "`img_url` = '{$img_url}'");
				}
			}
			return $shop_id;
		}
		
		return false;		
	}
	
	public function setCover($good_img_id, $good_id = 0) {
		if(!$good_id) {
			return $this->_db->update('oto_good_img', array('is_first' => 1), "`good_img_id` = '{$good_img_id}'");
		} else {
			$this->_db->update('oto_good_img', array('is_first' => 0), "`good_id` = '{$good_id}'", 0);
			$this->_db->update('oto_good_img', array('is_first' => 1), "`good_img_id` = '{$good_img_id}'");
			return true;
		}
	}
	
	public function getMarketCommend($region_id = 0) {
		$marketCommendArray = array();
		if(!$region_id) {
			$marketArray = $this->getMarket(0, true, $this->_city);
			foreach($marketArray as $marketItem) {
				foreach ($marketItem as $valueItem) {
					if($valueItem['is_show'] == 1) {
						$marketCommendArray[] = $valueItem;
					}
				}
			}
		} else {
			$marketCommendArray = $this->getMarket($region_id, true, $this->_city);
		}
		return $marketCommendArray;
	}
	
	public function getMarketDiscountMessage($market_id) {		
		//缓存键值
		$cacheKey = 'get_market_discount_message_' . $market_id;
		$data = $this->getData($cacheKey);
		if (empty($data)) {	
			$marketRow = array();
			$marketRow = $this->select("`market_id` = '{$market_id}'", 'oto_market', '*', '', true);
			if(!empty($marketRow) && !empty($marketRow['market_uid'])) {
				$discountMessage = Custom_AuthTicket::getDiscountsAssociated($marketRow['market_uid']);
				if($discountMessage['code'] == 1) {
					$marketRow['discount'] = array(
						'Title' => $discountMessage['message'][0]['Title'],
						'URL' => $discountMessage['message'][0]['URL'],
						'ImageUrl' => $discountMessage['message'][0]['ThematicImage']
					);
				}
				$data = $marketRow;
				$this->setData($cacheKey, $data);
			}
		}
		return $data;
	}
	
	public function getPreGoodUrl($gid, $order, $clicks, $city) {
		if($order == 1) {
			$preGid = $this->_db->fetchOne("select good_id from `oto_good` where `good_id` < '{$gid}' and `good_status` <> '-1' and `is_del` = '0' and `city` = '{$city}' order by `created` desc limit 1");
		} elseif($order == 2) {
		}
		
		return $preGid ? '/home/good/show/gid/' . $preGid .'/order/' . $order : '';
	}
	
	public function getNextGoodUrl($gid, $order, $clicks, $city) {
		if($order == 1) {
			$nextGid = $this->_db->fetchOne("select good_id from `oto_good` where `good_id` > '{$gid}' and `good_status` <> '-1' and `is_del` = '0' and `city` = '{$city}' order by `created` asc  limit 1");
		} elseif($order == 2) {
		}
		
		return $nextGid ? '/home/good/show/gid/' . $nextGid . '/order/' . $order : '';
	}
	
	public function getCircle($city) {
		$circleArray = array();
		$circleList = $this->getCircleByCircleId(0, true, $city);
		foreach($circleList as $circle_id => $circle_name) {
			$circleArray[] = array(
						'id' => $circle_id,
						'name' => $circle_name
					);
		}
		return $circleArray;
	}
	
	public function getDefaultPack($city) {
		$pack_id = 0;
		$packArray = $this->getPack(0, true, '', $city);
		foreach($packArray as $packItem) {
			if($packItem['pack_logo'] == 'basic') {
				$pack_id = $packItem['pack_id'];
				break;
			}
		}
		return $pack_id;
	}
}