<?php
class Model_Api_Goods extends Base
{
	private static $_instance;
	private $_table = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 拼接SQL语句
	 * @param unknown_type $sqlwhere
	 * @return $sql
	 */
	public function getGoodsSql($sqlwhere = '') {
		$where = '';
		$where .= " `good_status` <> '-1' and `is_auth` <> '-1' and `is_del` = '0' ";
		if ($sqlwhere) {
			$where .=  ' and '.$sqlwhere;
		}
		$order = " order by `created` desc";
		$sql = "select good_id, good_name, shop_id, shop_name, user_id, user_name, org_price, dis_price, concerned_number,favorite_number, is_auth
				from oto_good where {$where} {$order}";
		return $sql;
	}
	
	/**
	 * 优惠券SQL语句拼接
	 * @param unknown_type $sqlwhere
	 * @return $sql
	 */
	public function getTicketSQL($sqlwhere) {
		$where = '';
		$where .= $this->couponWhereSql();
		if ($sqlwhere) {
			$where .=  ' and '.$sqlwhere;
		}
		//现金券
		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
		$where .=  ' and `ticket_type` = ' . $ticket_type;
		
		$orderby = "order by `sequence` asc, `created` desc";
		$sql = "select ticket_id, ticket_uuid, ticket_title, ticket_type, ticket_summary, shop_id,
			shop_name,par_value,selling_price,start_time,end_time, valid_stime, valid_etime, cover_img,
			content, wap_content, total, has_led, limit_count, app_price, sequence
			from oto_ticket where {$where} {$orderby}";
		return $sql;
	}
	
	/**
	 * 获取配置信息
	 * 根据 全局设置标识(key) 获取 全局设置内容(value)
	 */
	public function getClicks($config_key) {
		$config_arr = include_once VAR_PATH.'config/config.php';
		$hot_clicks = $config_arr[$config_key];
		return $hot_clicks;
	}
	
	/**
	 * 获取上一页的GOOD_ID 
	 */
	public function getPrevious($gid, $sqlWhere = '') {
		$where = '';
		$where .= " good_id > '{$gid}' and good_status <> '-1' and is_auth <> '-1' and is_del = '0' ";
		if ($sqlWhere) {
			$where .=  ' and ' . $sqlWhere;
		}
		$order = " order by good_id asc limit 1";
		return $this->_db->fetchOne("select good_id from `oto_good` where ".$where.$order);
	}
	
	/**
	 * 获取下一页的GOOD_ID
	 */
	public function getNext($gid, $sqlWhere = '') {
		$where = '';
		$where .= " good_id < '{$gid}' and good_status <> '-1' and is_auth <> '-1' and is_del = '0' ";
		if ($sqlWhere) {
			$where .=  ' and '.$sqlWhere;
		}		
		$order = " order by good_id desc limit 1";
		return $this->_db->fetchOne("select good_id from `oto_good` where ".$where.$order);
	}
	
	/**
	 * 获取宝贝详情
	 */ 
	public function getGoodsRow($gid) {
		$goodRow = $this->select("good_id = '{$gid}'", 'oto_good', '*', '', true);
		foreach ($goodRow as $key => $value) {
			$goodRow[$key] = specialHtmlConversion($value);
		}
		return $goodRow;
	}
	
	/**
	 * 获取店铺
	 */ 
	public function getShopRow($sid) {
		$feilds = 'shop_id, shop_name, shop_address, brand_id, brand_name, lng, lat, phone, shop_img, favorite_number, notice, is_flag, is_enable';
		return $this->select("shop_id = '{$sid}'", 'oto_shop', "{$feilds}", '', true);
	}
	
	// 最热
	public function getHot($city, $page, $pagesize = 10) {
		$key = "get_api_goods_hot_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$hot_clicks = $this->getClicks('APP_HOT_CLICKS');
			$sql = $this->getGoodsSql("`clicks` > '{$hot_clicks}' and `city` = '{$city}'");		
			$hots = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($hots);
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 最新
	public function getNew($page, $pagesize = 10) {
		$key = 'api_goods_new_' . $page;
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql('');
			$new = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($new);
			foreach ($arr as &$row) {
				$row['avatar'] = $this->getUserAvatar($row['user_name']);
				if (empty($row['avatar'])) {
					$row['avatar'] = '';
				}
			}
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data;		
	}
	
	// 根据分类查询
	public function getGoodListByStore($storeId, $city, $page, $pagesize = 10) {
		$key = "get_api_goods_store_{$storeId}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`store_id` = '{$storeId}' and `city` = '{$city}'");
			$storeGood = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($storeGood);
			foreach ($arr as &$row) {
				$row['avatar'] = $this->getUserAvatar($row['user_name']);
			}
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 附近
	public function getNear($city, $post_lng, $post_lat, $distance, $page, $pagesize = 10) {
		$key = "get_api_goods_near_{$city}_{$post_lng}_{$post_lat}_{$distance}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$squares = returnSquarePoint($post_lng, $post_lat, $distance);
			$info_sql = "select shop_id, lat, lng from `oto_shop` 
						where lat <> 0 and lat > {$squares['right-bottom']['lat']} 
						and lat < {$squares['left-top']['lat']} 
						and lng > {$squares['left-top']['lng']} 
						and lng < {$squares['right-bottom']['lng']} 
						and shop_status <> '-1' and shop_pid = '0'";
			$shopInfoArray = $this->_db->fetchAssoc($info_sql);
			$shopIdArray = array_keys($shopInfoArray);
			$sql_nears = "select good_id, good_name, shop_id, shop_name, org_price, dis_price, concerned_number from oto_good 
						 where " . $this->db_create_in($shopIdArray, 'shop_id') . " and `good_status` <> '-1' and `is_auth` <> '-1' and `is_del` = '0' order by created desc";
			$near = $this->_db->limitQuery($sql_nears, $start, $pagesize);
			$near = $this->getGoodImg($near);
			foreach ($near as &$row) {
				$row['distance'] = getDistance($post_lat, $post_lng, $shopInfoArray[$row['shop_id']]['lat'], $shopInfoArray[$row['shop_id']]['lng']);
			}
			$data = $this->returnArr(count($near), $near);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 商圈查询宝贝
	public function getCircleGood($cid, $city, $page, $pagesize = 10) {
		$key = "get_api_goods_circle_{$cid}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`circle_id` = '{$cid}' and `city` = '{$city}'");
			$goods = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($goods);
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 商圈搜索后的详情页
	public function circleGoodView($gid, $cid, $uuid, $uname) {
		$key = 'api_good_detail_circle' . $gid;
		$data = $this->getData($key);
		if (empty($data)) {
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$previous_id = $this->getPrevious('oto_good', "circle_id = '{$cid}'", $gid);
			$next_id = $this->getNext('oto_good', "circle_id = '{$cid}'", $gid);
			$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/circle-good-view/cid/'.$cid.'/gid/'.$previous_id : '';
			$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/circle-good-view/cid/'.$cid.'/gid/'.$next_id : '';
			$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
			$data = $this->returnArr(1, $goods);
			$this->setData($key, $data);
		}
		return $data;
	} 
	
	public function getHotCircle() {
    	$sql = "select `circle_id` as `id`, `circle_name` as `name` from `oto_circle` where is_hot = 1 order by sequence asc, circle_id asc";
    	$data = $this->_db->fetchAll($sql);
    	return $data; 
	}

	// 收藏夹
	public function getFavGood($uuid, $uname, $city, $page, $pagesize = 10) {
		$uid = $this->checkUid($uuid, $uname);
		$key = "get_api_good_fav_{$uid}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = "SELECT g.good_id, g.good_name, g.shop_id, g.shop_name, g.org_price, g.dis_price, g.concerned_number 
					FROM oto_good_favorite f
					LEFT JOIN oto_good g ON f.good_id = g.good_id 
					WHERE f.user_id = '{$uid}' AND g.good_status <> '-1' and g.is_auth <> '-1' and g.is_del = '0' and g.city = '{$city}'
					ORDER BY g.created desc";
			$fav_good = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($fav_good);
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 收藏夹详情
	public function favView($uuid, $uname, $gid) {
		$key = 'good_fav_view_' . $uuid. '_' .$gid;
		$data = $this->getData($key);
		if (empty($data)) {
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
			$data = $this->returnArr(1, $goods);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	
	// 我发布的列表
	public function getPublishGood($uuid, $user_name, $city, $page, $pagesize = 10) {
		$uid = $this->checkUid($uuid, $user_name);
		$key = "get_api_good_publish_{$uid}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$uid = $this->checkUid($uuid, $user_name);
			$sql = "select good_id, good_name, shop_id, shop_name, org_price, dis_price, concerned_number, good_status, reason
					from oto_good 
					where `user_id` = '{$uid}' and `is_del` = '0' and `city` = '{$city}' 
					order by created desc";
			$publish_good = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($publish_good);
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 我发布的详情
	public function pulishView($uuid, $uname, $gid, $city) {
		$key = "get_api_publish_view_{$uuid}_{$gid}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			$uid = $this->checkUid($uuid, $uname);
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$previous_id = $this->_db->fetchOne("select good_id from oto_good where `user_id` = '{$uid}' and `good_id` > '{$gid}' and `city` = '{$city}' order by `good_id` asc limit 1");
			$next_id = $this->_db->fetchOne("select good_id from oto_good where `user_id` = '{$uid}' and `good_id` < '{$gid}' and `city` = '{$city}' order by `good_id` desc limit 1");															
			$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/publish-view/uuid/'.$uuid.'/uname/'.$uname.'/gid/'.$previous_id : '';
			$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/publish-view/uuid/'.$uuid.'/uname/'.$uname.'/gid/'.$next_id : '';
			$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
			$data = $this->returnArr(1, $goods);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 搜索宝贝
	public function searchGood($gname, $city, $page, $pagesize = 10) {
		$key = "get_api_good_searchgood_{$gname}_{$city}_{$page}";
		$data = $this->getData($key);		
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`good_name` like '%{$gname}%' and `city` = '{$city}'");
			$goods = $this->_db->limitQuery($sql, $start, $pagesize);
			$goodsCount = $this->_db->fetchOne("select count(good_id) from oto_good where good_name like '%{$gname}%' and good_status <> '-1' and is_auth <> '-1' and `is_del` = '0'");
			$arr = $this->getGoodImg($goods);
			$rs = array(
					'goodsNum' => $goodsCount,
					'data'     => $arr
					);
			$data = $this->returnArr(count($arr), $rs);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 搜索宝贝 详情页
	public function goodSearchView($postData, $city) {
		$gid = intval($postData['gid']);
		$gname = Custom_String::HtmlReplace(urldecode(trim($postData['gname'])), 1);
		$key = "api_good_search_view_{$gid}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$previous_id = $this->getPrevious($gid, "`good_name` like '%{$gname}%' and `city` = '{$city}'");
			$next_id = $this->getNext($gid, "good_name like '%{$gname}%' and `city` = '{$city}'");
			$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/good-search-view/gname/'.$gname.'/gid/'.$previous_id : '';
			$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/good-search-view/gname/'.$gname.'/gid/'.$next_id : '';
			$goods = $this->getView($goods, $shops, $postData['uuid'], $postData['uname'], $gid);
			$data = $this->returnArr(1, $goods);
			$this->setData($key, $data);			
		}
		return $data;
	}

	// 搜索该店铺下宝贝
	public function searchShop($sname, $city, $page, $pagesize = 10) {
		$key = "get_api_good_searchshop_{$sname}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`shop_name` = '{$sname}' and `city` = '{$city}'");
			$goods = $this->_db->limitQuery($sql, $start, $pagesize);
			$goodsCount = $this->_db->fetchOne("select count(good_id) from oto_good where `shop_name` = '{$sname}' and `good_status` <> '-1' and `is_auth` <> '-1' and `is_del` = '0'");
			$arr = $this->getGoodImg($goods);
			$rs = array(
					'goodsNum' => $goodsCount,
					'data'     => $arr
					);
			$data = $this->returnArr(count($arr), $rs);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 搜索该店铺下宝贝的详情
	public function shopSearchView($postData, $city) {
		$gid = intval($postData['gid']);
		$sname = $gname = Custom_String::HtmlReplace(urldecode(trim($postData['sname'])), 1);		
		$key = "api_shop_search_view_{$gid}_{$sname}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$previous_id = $this->getPrevious($gid, "`shop_name` = '{$sname}' and `city` = '{$city}'");
			$next_id = $this->getNext($gid, "`shop_name` = '{$sname}' and `city` = '{$city}'");
			$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/shop-search-view/sname/'.$sname.'/gid/'.$previous_id : '';
			$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/shop-search-view/sname/'.$sname.'/gid/'.$next_id : '';
			$goods = $this->getView($goods, $shops, $postData['uuid'], $postData['uname'], $gid);
			$data = $this->returnArr(1, $goods);
			$this->setData($key, $data);			
		}
		return $data;
	}
	
	// 详情页
	public function detail($gid, $type, $city, $lng, $lat, $uuid, $uname) {		
		$key = "get_api_good_detail_{$gid}_{$type}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			switch ($type) {
				case 'hot' :
					$hot_clicks = $this->getClicks('APP_HOT_CLICKS');
					$previous_id = $this->getPrevious($gid, "clicks > '{$hot_clicks}' and `city` = '{$city}'");
					$next_id = $this->getNext($gid, "clicks > '{$hot_clicks}' and `city` = '{$city}'");
					$goods = $this->getGoodsRow($gid);
					$shops = $this->getShopRow($goods['shop_id']);
					$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/detail/type/hot/gid/'.$previous_id : '';
					$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/detail/type/hot/gid/'.$next_id : '';
					$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
					$data = $this->returnArr(1, $goods);
					$this->setData($key, $data);
					break;
				case 'near' :
					if (empty($lat) || empty($lng)) {
						echo json_encode($this->returnArr(0 , '', 300, '无法获取坐标'));
						exit();
					}
					$squares = returnSquarePoint($lng, $lat, '0.5');
					$info_sql = "select shop_id from `oto_shop`
								where lat <> 0 and lat > {$squares['right-bottom']['lat']}
								and lat < {$squares['left-top']['lat']}
								and lng > {$squares['left-top']['lng']}
								and lng < {$squares['right-bottom']['lng']}
								and shop_status <> -1 and shop_pid = 0
								and city = '{$city}'";
					   
					$shop_ids = $this->_db->fetchCol($info_sql);
					$previous_id = $this->getPrevious($gid, $this->db_create_in($shop_ids, 'shop_id'));
					$next_id = $this->getNext($gid, $this->db_create_in($shop_ids, 'shop_id'));
					$goods = $this->getGoodsRow($gid);
					$shops = $this->getShopRow($goods['shop_id']);
					$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/detail/type/near/gid/'.$previous_id.'/lng/'.$lng.'/lat'.$lat : '';
					$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/detail/type/near/gid/'.$next_id.'/lng/'.$lng.'/lat'.$lat : '';
					$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
					$data = $this->returnArr(1, $goods);
					$this->setData($key, $data);
					break;
				case 'new':	
				default:
					$previous_id = $this->getPrevious($gid, "`city` = '{$city}'");
					$next_id = $this->getNext($gid, "`city` = '{$city}'");
					$goods = $this->getGoodsRow($gid);
					$shops = $this->getShopRow($goods['shop_id']);
					$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/detail/type/new/gid/'.$previous_id : '';
					$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/detail/type/new/gid/'.$next_id : '';
					$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
					$data = $this->returnArr(1, $goods);
					$this->setData($key, $data);
					break;
			}
		}
		return $data;
	}
	
	public function goodView($gid, $city, $uuid, $uname, $page, $pagesize) {
		$key = "get_api_good_view_{$gid}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$previous_id = $this->getPrevious($gid, "`city` = '{$city}'");
			$next_id = $this->getNext($gid, "`city` = '{$city}'");
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/good-detail/gid/'.$previous_id : '';
			$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/good-detail/gid/'.$next_id : '';			
			$goods['img'] = $this->getDetailImage($goods['good_id']);
			$goods['avatar'] = $this->getUserAvatar($goods['user_name']);
			$goods['shop_address'] = $shops['shop_address'];
			$goods['shop_name'] = $shops['shop_name'];
			$goods['created'] = date('Y.n.j', $goods['created']);
			$goods['lng'] = $shops['lng'];
			$goods['lat'] = $shops['lat'];
			
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`shop_id` = '{$goods['shop_id']}' and good_id <> '{$gid}'");
			$goodList = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($goodList);
			$goods['shop_img'] = $arr;
			
			$data = $this->returnArr(count($arr), $goods);
			$this->setData($key, $data);
		}
		//判断用户是否关注和收藏某个商品，不能缓存判断
		if (!empty($uuid) && !empty($uname)) {
			$uid = $this->checkUid($uuid, $uname);
			$data['is_like'] = $this->isLikeFav($uid, $gid, 'oto_good_concerned');
			$data['is_fav'] = $this->isLikeFav($uid, $gid, 'oto_good_favorite');
		}		
		return $data;
	}
	
	
	public function shopView($postData, $sid, $uid, $page, $pagesize, $city) {
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		
		$key = "get_api_shop_view_{$sid}_{$page}_{$pagesize}_{$lng}_{$lat}";
		
		$data = $this->getData($key);
		if (empty($data)) {
			if ($lng && $lat) {
				$sql = "SELECT
							shop_id, shop_name, shop_address, brand_id, brand_name, phone, shop_img, lat, lng, favorite_number, notice, is_flag, is_enable,
							12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
							FROM oto_shop where shop_id = '{$sid}'";
				$shopView = $this->_db->fetchRow($sql);
			} else {
				$shopView = $this->getShopRow($sid);
			}
			
			if($shopView['brand_id']) {
				$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$shopView['brand_id']}'");
				if (empty($brandInfo['brand_name_zh'])) {
					$shopView['brand_name'] = $brandInfo['brand_name_en'];
				} elseif (empty($brandInfo['brand_name_en'])) {
					$shopView['brand_name'] = $brandInfo['brand_name_zh'];
				} else {
					$shopView['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
				}				
				$shopView['brand_icon'] = $brandInfo['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_icon'] : '';
			} else {
				$shopView['brand_name'] = $shopView['brand_icon'] = '';
			}
			
			
			
			if ($shopView['is_flag'] == 1) {
				$flagInfo = Model_Home_Shop::getInstance()->getRecommendListByIdentifier($sid, 'app_recommend', $this->_city, 5);
				if ($flagInfo) {
					foreach ($flagInfo as &$row) {
						$row['detail_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/shop/' . $row['detail_img'];
					}
					$shopView['flag_info'] = $flagInfo;
				} else {
					$shopView['flag_info'] = array();
				}
			}
			
			$shopView['shop_img'] = $shopView['shop_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/shop/' . $shopView['shop_img'] : '';
			
			/**
 			// 直属于该店铺的券
			$getTicketSql1 = $this->getTicketSQL("`shop_id` = '{$sid}'");
			$ticket_info1 = $this->getTicketListInfo($getTicketSql1, 1, 50);			
			// 该店铺的关联券
			$tidArr = $this->_db->fetchCol("select ticket_id from oto_ticket_shop where shop_id = '{$sid}'");
			$getTicketSql2 = $this->getTicketSQL($this->db_create_in($tidArr, 'ticket_id'));
			$ticket_info2 = $this->getTicketListInfo($getTicketSql2, 1, 50);
			$tictetInfo = array_merge($ticket_info1, $ticket_info2);
			$shopView['ticket'] = $tictetInfo;
			*/
			
			$shopView['ticket'] = array();
			
			// 该店铺下的券列表(直属)
			$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			$c_sql1 = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, app_price, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND shop_id = '{$sid}' and city = '{$city}' AND ticket_type = '{$ticket_type}'
						ORDER BY created desc";
			$tickeInfo1 = $this->_db->fetchAll($c_sql1);
			foreach ($tickeInfo1 as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			// 该店铺下的券列表(关联)
			$tidArr = $this->_db->fetchCol("select ticket_id from oto_ticket_shop where shop_id = '{$sid}'");
			$c_sql2 =  "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, app_price, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND ". $this->db_create_in($tidArr, 'ticket_id') ." and city = '{$city}' AND ticket_type = '{$ticket_type}'
						ORDER BY created desc";
			$tickeInfo2 = $this->_db->fetchAll($c_sql2);
			foreach ($tickeInfo2 as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			$tictetInfo = array_merge($tickeInfo1, $tickeInfo2);
			
			$shopView['is_ticket'] = !empty($tictetInfo) ? 1 : 0;
			$shopView['ticket_list'] = $tictetInfo;
			
			
			// 该店铺下的团列表
			$ticket_type_tuan = Model_Home_Ticket::getInstance()->getTicketTypeID('buygood');
			$t_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, app_price, start_time, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND shop_id = '{$sid}' and city = '{$city}' AND ticket_type = '{$ticket_type_tuan}'
						ORDER BY created desc";
			$tuan_list = $this->_db->fetchAll($t_sql);
			foreach ($tuan_list as &$row) {
				$row['title'] = $row['ticket_title'];
				$tuan_img_small = $this->_db->fetchOne("select file_img_small from oto_ticket_info where ticket_id = '{$row['ticket_id']}'");
				$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $tuan_img_small;
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			$shopView['is_tuan'] = !empty($tuan_list) ? 1 : 0;
			$shopView['tuan_list'] = $tuan_list;
			

			// 该店铺的商品
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`shop_id` = '{$sid}'");
			$goodList = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($goodList);
			$shopView['good_img'] = $arr;
			
			$data = $shopView;
			unset($shopView);
			$this->setData($key, $data);
		}
		
		//店铺收藏数
		$data['favorite_number'] = Model_Api_App::getInstance()->getFavoriteNum('oto_shop_favorite', $sid);
		
		if ($uid) {
			$data['is_fav'] = intval($this->isShopFav($uid, $sid, 'oto_shop_favorite'));
		}
		
		$data = $this->returnArr(1, $data);
		
		return $data;
	}
	
	// 宝贝详情 券列表
	public function detailCoupon($gid, $city) {
		$key = "get_api_good_detail_coupon_{$city}_{$gid}";
		$data = $this->getData($key);
		if (empty($data)){
			$sql = "select t.ticket_id, t.ticket_uuid, t.ticket_title, t.ticket_type, t.ticket_summary, t.shop_id,
					t.shop_name, t.par_value, t.selling_price, t.start_time, t.end_time, t.valid_stime, t.valid_etime, 
					t.content , t.total, t.has_led, t.limit_count
					from `oto_ticket_good` as `g`
					left join `oto_ticket` as `t` on `g`.`ticket_id` = `t`.`ticket_id`
					where `g`.`good_id` = '{$gid}' and `g`.`start_time` < '" .REQUEST_TIME. "' and `g`.`end_time` > '" .REQUEST_TIME. "'
					and `t`.`ticket_status` = '1'
					and `t`.`is_auth` = '1'
					and `t`.`is_show` = '1'
					and `t`.`city` = '{$city}'
					order by `g`.`created` desc";
			$couponList = $this->_db->fetchAll($sql);
			$sortInfo = $this->getTicketSortById(0, 'ticketsort');
			foreach ($couponList as &$row) {
				$row['sort_name'] = $sortInfo[$row['ticket_type']]['sort_detail_name'];
				$row['sort_mark'] = $sortInfo[$row['ticket_type']]['sort_detail_mark'];
				if($row['sort_mark'] == 'coupon') {
					$row['dis_price'] = floor($row['par_value']);
					$row['surplus'] = $row['total'] - $row['has_led'];
				} elseif($row['sort_mark'] == 'voucher') {
					$row['dis_price'] = floor($row['selling_price']);
					$str = Custom_AuthTicket::get_ticket_details_by_guid($row['ticket_uuid']);
					if(is_object($str)) {
						$row['surplus'] = $str->data->Avtivities[0]->ProductStock;
					} else {
						$row['surplus'] = 0;
					}
				}
				$row['content'] = strip_tags($row['content']);
				$row['valid_time'] = date('Y.n.j', $row['valid_stime']).'-'.date('n.j', $row['valid_etime']);
			}
			$data = $this->returnArr(count($couponList), $couponList);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 券列表
	public function getCouponList($city, $page, $pagesize = 10) {
		$key = "get_api_good_list_coupon_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = $this->getTicketSQL("`city` = '{$city}'");
			$couponInfo = $this->getTicketListInfo($sql, $page, $pagesize);
			$data = $this->returnArr(count($couponInfo), $couponInfo);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 
	public function getShopCouponDetail($ticket_uuid, $postData, $city) {
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$key = "get_api_shop_coupon_detail_{$ticket_uuid}_{$lng}_{$lat}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = $this->getTicketSQL(" ticket_uuid = '{$ticket_uuid}'");
			$data = $this->getTicketListInfo($sql, 1);
			$data = $this->getShopInfoByTicket($data, $lng, $lat);
			$this->setData($key, $data);		
		}
		return $data;
	}
	
	
	// 2013-08-13 优惠券列表（新版）
	public function getCouponListNew($postData, $page, $pagesize = 10) {
		$key = 'api_coupon_list_new_' . $page;
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = $this->getTicketSQL('');
			$couponInfo = $this->getTicketListInfo($sql, $page, 100);
			if ($couponInfo) {
				$couponInfo = $this->getShopInfoByTicket($couponInfo, $lng, $lat);
			}
			$data = $this->returnArr(count($couponInfo), $couponInfo);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getTicketList($postData, $page, $pagesize = 10) {
		$mark = $postData['mark'];
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		if ($lng && $lat) {
			$key = 'api_ticket_list_' . $mark. '_' . $lng . $lat. '_' . $page;;
		} else {
			$key = 'api_ticket_list_' . $mark. '_' . $page;
		}
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$ticket_type = $this->_db->fetchOne("select sort_detail_id from oto_sort_detail where sort_detail_mark = '{$mark}'");
			$sql = $this->getTicketSQL("`ticket_type` = '{$ticket_type}'");
			$couponInfo = $this->getTicketListInfo($sql, $page, $pagesize);
			if ($couponInfo) {
				$couponInfo = $this->getShopInfoByTicket($couponInfo, $lng, $lat);
				$data = $this->returnArr(count($couponInfo), $couponInfo);
				$this->setData($key, $data);
			} else {
				$data = $this->returnArr(0, array());
				$this->setData($key, $data);
			}
		}
		return $data;
	}
	
	public function getTickeClasstList($postData, $city, $page, $pagesize = 10) {
		$class = $postData['class'];;
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		if ($lng && $lat) {
			$key = "get_api_ticket_class_list_{$class}_{$city}_{$lng}_{$lat}_{$page}";
		} else {
			$key = "get_api_ticket_class_list_{$class}_{$city}_{$page}";
		}
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$couponInfo = array();
			switch ($class) {
				case '1' :  // 商场/品牌
					$sql = $this->getTicketSQL("`ticket_class` in (1 , 2) and `city` = '{$city}'");
					$storeArray = $this->getSortByTicketSort($this->couponWhereSql() . " AND `ticket_class` in (1 , 2)", $city, $lng, $lat);
					break;
				case '2' :  // 特卖
					$sql = $this->getTicketSQL("`ticket_class` = '3' and `city` = '{$city}'");
					$storeArray = $this->getSortByTicketSort($this->couponWhereSql() . " AND `ticket_class` = 3", $city, $lng, $lat);
					break;
				case '3' : // 过期
					$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'voucher');
					$sql = "select * from oto_ticket 
							where `ticket_type` = '{$ticket_type}' and `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1 and `city` = '{$city}'
							and `end_time` < '" . REQUEST_TIME . "' 
							order by `sequence` asc, `created` desc";
					$where = "`ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1 and `end_time` < '" . REQUEST_TIME . "' ";
					$storeArray = $this->getSortByTicketSort($where, $city, $lng, $lat);
					break;
				default: // 商场/品牌
					$sql = $this->getTicketSQL("`ticket_class` in (1 , 2) and `city` = '{$city}'");
					$storeArray = $this->getSortByTicketSort($this->couponWhereSql() . " AND `ticket_class` in (1 , 2)", $city, $lng, $lat);
					break;
			}
			
			$couponInfo = $this->getTicketListInfo($sql, $page, $pagesize);
			
			if ($couponInfo) {
				$couponInfo = $this->getShopInfoByTicket($couponInfo, $lng, $lat);
			}
			$data = $this->returnArr(count($couponInfo), $couponInfo);
			$data['store'] = $storeArray;
			// 根据商场分类查询全列表
			$sql_market_ticket = $this->getTicketSQL("`ticket_class` = '1' and `city` = '{$city}'");
			$couponByMarket = $this->getTicketListInfo($sql_market_ticket, 1, 100);
			if ($couponByMarket) {
				$couponByMarket = $this->getShopInfoByTicket($couponByMarket, $lng, $lat);
			}
			$data['marketTicket'] = $couponByMarket;
			$marketTicketNum = $this->_db->fetchOne("select count(*) from oto_ticket where ticket_class = '1' and city = '{$city}'");
			$data['marketTicketNum'] = $marketTicketNum;
			
			$this->setData($key, $data);			
		}
		return $data;
	}
	
	// 根据券 获取商品分类和该分类的总数
	public function getSortByTicketSort($where, $city, $lng, $lat) {
		$storeArray = array();
		$storeArray = $this->getAppStore($city);
		foreach ($storeArray as &$row) {
			$storeNum = $this->_db->fetchOne("select count(*) from oto_ticket where ticket_sort = '{$row['id']}' and ". $where . " and city = '{$city}'");
			$row['storeNum'] = $storeNum;
			
			// 根据分类ID查询券列表
			$sql = $this->getTicketSQL("`ticket_sort` = '{$row['id']}' and `city` = '{$city}'");
			$couponInfo = $this->getTicketListInfo($sql, 1, 100);
			if ($couponInfo) {
				$couponInfo = $this->getShopInfoByTicket($couponInfo, $lng, $lat);
			}
			
			$row['storeTicket'] = $couponInfo;
		}
		return $storeArray;
	}
	
	public function getShopInfoByTicket($couponInfo, $lng, $lat) {
		foreach ($couponInfo as &$row) {
			$shopIdArr = $this->_db->fetchCol("select shop_id from oto_ticket_shop where ticket_id = '{$row['ticket_id']}'");
			$shopIdArr[] = $row['shop_id'];
			
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
			foreach($shop_info as &$row1) {
				$row1['coupon_num'] = $this->getTicketNumByShopId($row1['shop_id']);
			}
			$row['shopInfo'] = array();
			$row['shopInfo'] = $shop_info;
		}
		
		return $couponInfo;
	}
	
	
	public function getClist($page, $pagesize = 10) {
		$key = 'api_good_coupon_list_' . $page;
		$data = $this->getData($key);
		if (empty($data)) {
			$couponInfo = array();
			$start = ($page - 1) * $pagesize;
			$sql_getShop = "select shop_id, shop_name  from oto_ticket where " . $this->couponWhereSql() . " group by shop_name";
			$shopInfo = $this->_db->limitQuery($sql_getShop, $start, $pagesize);
			$couponInfo['shop'] = $shopInfo;
			foreach ($couponInfo['shop'] as &$row) {
				$sql = $this->getTicketSQL(" shop_id = '{$row['shop_id']}'");
				$rs = $this->getTicketListInfo($sql, 1);
				$row['coupon'] = $rs;
			}
			$data = $this->returnArr(count($shopInfo), $couponInfo);
			$this->setData($key, $data);
		}
		return $data;		
	}
	
	// 获取券剩余数
	public function getCouponSurplus($postData) {
		$t_id = intval($postData['ticket_id']);
		$t_uuid = $postData['ticket_uuid'];
		$sort_mark = $postData['sort_mark'];
		$data = array();
		if ($sort_mark == 'coupon') {
			$couponRow = $this->_db->fetchRow("select total, has_led from oto_ticket where ticket_id = '{$t_id}'");			
			$data['total'] = $couponRow['total'];
			$data['has_led'] = $couponRow['has_led'];
			$data['surplus'] = $couponRow['total'] - $couponRow['has_led'];
		} elseif($sort_mark == 'voucher') {
			$str = Custom_AuthTicket::get_ticket_details_by_guid($t_uuid);
			if(is_object($str)) {
				$data['surplus'] = $str->data->Avtivities[0]->ProductStock; // 剩余
				$data['total'] = $str->data->Avtivities[0]->ProductNum; // 总数
				$data['has_led'] = $str->data->Avtivities[0]->ProductDisplaySale; // 售出
				$data['status'] = $data['has_led'] >= $data['total'] ? 0 : 1;
			} else {
				$data['surplus'] = 0; // 剩余
				$data['total'] = 0; // 总数
				$data['has_led'] = 0; // 售出
				$data['status'] = 0;
			}
		}
		return $data;
	}
	
	
	// 我的券列表
	public function getMyCouponList($uuid, $uname, $is_tuan, $orderStatus, $page, $pagesize = PAGESIZE) {
		$uid = $this->checkUid($uuid, $uname);

		$ticketInfo = $couponInfo = $vaucherInfo = $vaucherInfo_new = array();
		$where = " `user_id` = '{$uid}' ";
		$orderby = "order by `created` desc";
		$sql = "select * from oto_ticket_detail where {$where} {$orderby}";
		$couponInfo = $this->_db->fetchAll($sql);
		foreach ($couponInfo as $key=>$value) {
			$couponInfo[$key]['shop_name'] = $this->_db->fetchOne("select shop_name from oto_shop where shop_id = '{$value['shop_id']}' limit 1");
			$coupon = $this->_db->fetchRow("select par_value, ticket_summary, content from oto_ticket where ticket_id = '{$value['ticket_id']}'");
			$couponInfo[$key]['dis_price'] = floor($coupon['par_value']);
			$couponInfo[$key]['par_value'] = floor($coupon['par_value']);
			$couponInfo[$key]['ticket_summary'] = $coupon['ticket_summary'];
			$couponInfo[$key]['content'] = strip_tags($coupon['content']);
			$couponInfo[$key]['valid_time'] = date('Y.n.j', $value['valid_stime']).'-'.date('n.j', $value['valid_etime']);
			if ($value['valid_etime']  <  REQUEST_TIME) {
				$couponInfo[$key]['used'] = 1; // 过期
			} else {
				$couponInfo[$key]['used'] = 0; // 正常
			}
		}
		$vaucherInfo = Custom_AuthTicket::getUserTicketList($uuid, $orderStatus, $is_tuan, $page, $pagesize);
		
		if ($vaucherInfo['code'] == '1') {
			Third_Des::$key = '34npzntC';
			$vaucherInfo_new = $vaucherInfo['message']['Results'];
			foreach ($vaucherInfo_new as & $row) {
				$msg = "uuid={$row['UserLoginID']}&order_no={$row['OrderNo']}";
				$http_build_query_string = urlencode(Third_Des::encrypt($msg));
				$row['voucherShareUrl'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/voucher/share/?msg=' . $http_build_query_string;
			}
		}
		
		$ticketInfo['c'] = array();
		$ticketInfo['v'] = $vaucherInfo_new;
		$ticketInfo['shareMsg'] = array(
					'title' => '我的红包分你一份',
					'description' => '一起来名品街约逛吧，100%专柜正品，优惠不停歇',
					'share_pictures' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/ticket_share.jpg'				
				);
		unset($couponInfo, $vaucherInfo, $vaucherInfo_new);
		//logLog('myCoupon.log', var_export($ticketInfo, true));
		$data = $this->returnArr(1, $ticketInfo);
		return $data;
	}

	//我的优惠券 => 根据用户名和订单号获取某个优惠券明细 
	public function getMyCouponOne($postData) {
		$OrderNo = $postData['orderNo'];
		$user_name = urldecode($postData['uname']);
		$key = "get_api_good_my_ticket_one_{$user_name}_{$OrderNo}" ;
		$data = $this->getData($key);
		if (empty($data)) {
			$ticketInfo = $voucherInfo = array();
			$ticketInfo = Custom_AuthTicket::getUserTicketOne($user_name, $OrderNo);
				
			if ($ticketInfo['code'] == '1') {
				$voucherInfo = $ticketInfo['message']['Results'];
			}
			$data = $this->returnArr(1, $voucherInfo);
		}
		return $data;
	}	
	/**
	 * 我的优惠券 （免费券）
	 */
	public function getMyTicketCoupon($uid, $uname, $page, $pagesize = 10) {
		$key = 'api_good_my_ticket_coupon_' . $uid . '_' . $page;
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$where = " `user_id` = '{$uid}' ";
			$orderby = "order by `created` desc";
			$sql = "select * from oto_ticket_detail where {$where} {$orderby}";
			$couponInfo = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($couponInfo as $key=>$value) {
				$couponInfo[$key]['shop_name'] = $this->_db->fetchOne("select shop_name from oto_shop where shop_id = '{$value['shop_id']}'");
				$coupon = $this->_db->fetchRow("select par_value, ticket_summary, content from oto_ticket where ticket_id = '{$value['ticket_id']}'");
				$couponInfo[$key]['dis_price'] = floor($coupon['par_value']);
				$couponInfo[$key]['ticket_summary'] = $coupon['ticket_summary'];
				$couponInfo[$key]['content'] = strip_tags($coupon['content']);
				$couponInfo[$key]['valid_time'] = date('Y.n.j', $value['valid_stime']).'-'.date('n.j', $value['valid_etime']);
				if ($value['valid_etime']  <  REQUEST_TIME) {
					$couponInfo[$key]['used'] = 1; // 过期
				} else {
					$couponInfo[$key]['used'] = 0; // 正常
				}
			}
			$data = $this->returnArr(count($couponInfo), $couponInfo);
		}
		return $data;
	}
	
	/**
	 * 我的优惠券 （现金券）
	 */
	public function getMyTicketVoucher($uuid, $uname, $page, $pagesize = 10) {
		$key = 'api_good_my_ticket_voucher_' . $uuid . '_' . $page;
		$data = $this->getData($key);
		if (empty($data)) {
			$vaucherInfo = Custom_AuthTicket::getUserTicketList($uuid, -1, $page, $pagesize);
			if ($vaucherInfo['code'] == '1') {
				$vaucherInfo = $vaucherInfo['message']['Results'];
			}else {
				$vaucherInfo = '';
			}	
			$data = $this->returnArr(count($vaucherInfo), $vaucherInfo);
		}
		return $data;
	}
	
	
	// 删除宝贝照片
	public function delImg($goodImgId, $gid) {
		if(Custom_Upload::imageDelete($goodImgId)) {
			return $this->returnArr(1, '', 100, '删除照片成功');
		} else {
			return $this->returnArr(0, '', 300, '删除照片失败');
		}
	}
	
	// 添加店铺
	public function addShop($postData, $city) {
		$sname = Custom_String::HtmlReplace(urldecode(trim($postData['sname'])), 1);  // 店铺名称
		$saddress = Custom_String::HtmlReplace(urldecode(trim($postData['saddress'])), 1);  // 店铺地址
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$arr = array(
					'shop_name'     => $sname,
					'shop_address'  => $saddress,
					'lng'           => $lng,
					'lat'           => $lat,
					'created'       => REQUEST_TIME,
					'city'			=> $city
				);
		return $this->_db->insert('oto_shop', $arr);
	}
	
	// 添加商品
	public function addGood($postData, $city) {
		$user_id   = $postData['user_id'];
		$good_name = Custom_String::HtmlReplace(trim(urldecode($postData['gname'])), 1);
		$shop_id   = $postData['sid'];
		$shop_info = $this->getShopFieldById($shop_id);
		$user_name = urldecode($postData['uname']);
		$oprice    = !$postData['oprice'] ? 0 : $postData['oprice'];
		$dis_price = $postData['dprice'];
		$ip = $postData['ip'];
		$arr = array (
					'good_name'  => $good_name,
					'shop_id'    => $shop_id,
					'shop_name'  => saddslashes($shop_info['shop_name']),
					'user_id'    => $user_id,
					'user_name'  => $user_name,
					'org_price'  => $oprice,
					'dis_price'  => $dis_price,
					'region_id'  => $shop_info['region_id'],
					'circle_id'  => $shop_info['circle_id'],
					'store_id'   => $shop_info['store_id'],
					'brand_id'   => $shop_info['brand_id'],
					'created'    => REQUEST_TIME,
					'city'		 => $city
				);
		// 商品入库
		$good_id = $this->_db->insert('oto_good', $arr);
		if ($good_id) {
			// 该商品的图片上传操作
			$img = $postData['img'];
			$imgs = explode('|', $img);
			foreach ($imgs as $key => $val) {
				Custom_Upload::goodsImgageUpload($val, $user_id, $good_id);
			}
			$this->updateUser($ip, $user_id);
			return true;
		}
		return false;
	}
	
	// 编辑商品
	public function editGood($postData) {
		$gid = intval($postData['gid']);
		$good_name = Custom_String::HtmlReplace(trim($postData['gname']), 1);
		$shop_id   = $postData['sid'];
		$shop_info = $this->getShopFieldById($shop_id);
		$user_id   = $this->checkUid($postData['uuid'], $postData['uname']);
		$org_price = !$postData['oprice'] ? 0 : $postData['oprice'];
		$dis_price = $postData['dprice'];

		$ip = $postData['ip'];
		$arr = array (
				'good_name'  => $good_name,
				'shop_id'    => $shop_id,
				'shop_name'  => saddslashes($shop_info['shop_name']),
				'org_price'  => $org_price,
				'dis_price'  => $dis_price,
				'region_id'  => $shop_info['region_id'],
				'circle_id'  => $shop_info['circle_id'],
				'store_id'   => $shop_info['store_id'],
				'brand_id'   => $shop_info['brand_id'],
				'updated'    => REQUEST_TIME
		);
		$rs = $this->_db->update('oto_good', $arr, "good_id = '{$gid}'");
		$this->updateUser($ip, $user_id);
		return $rs;
	}
	
	// 上传单图
	public function uploadImg($img, $uid, $gid) {
		$aid = Custom_Upload::goodsImgageUpload($img, $uid, $gid);
		if ($aid) {
			$imgInfo = $this->_db->fetchRow("select good_img_id, img_url from oto_good_img where good_img_id = '{$aid}'");
			$imgInfo['img_detail_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/good/640/' . $imgInfo['img_url'];
			$size = getimagesize(ROOT_PATH.'web/data/good/640/' . $imgInfo['img_url']);
			$imgInfo['width'] = $size['0'];
			$imgInfo['height'] = $size['1'];
			unset($imgInfo['img_url']);
			return $this->returnArr(1, $imgInfo, 100, '上传图片成功');
		} else {
			return $this->returnArr(0, '', 300, '上传图片失败');
		}
	}
	
	// 获取指定店铺下最新7张宝贝图片
	public function getImgByShop($sid, $gid) {
		// 获取该店铺下的所有宝贝ID
		$gids = $this->_db->fetchCol("select good_id from oto_good where shop_id = '{$sid}' and good_id <> '{$gid}' and is_del = 0 and good_status <> -1 order by created desc");
		$sql = "select good_id, img_url from oto_good_img where ".  $this->db_create_in($gids, 'good_id') ." group by good_id order by is_first desc, good_img_id desc limit 7";
		$info = $this->_db->fetchAll($sql);
		foreach ($info as &$row) {
			$row['shop_img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/good/640/' . $row['img_url'];
		}
		return $info;
	}
	
	// 判断是否已喜欢, 判断是否已收藏
	public function isLikeFav($uid, $gid, $table) {
		return $this->_db->fetchOne("select 1 from " . $table ." where good_id = '{$gid}' and user_id = '{$uid}' limit 1") == 1;
	}	
	
	// 判断店铺是否收藏
	public function isShopFav($uid, $sid, $table) {
		return $this->_db->fetchOne("select 1 from " . $table ." where shop_id = '{$sid}' and user_id = '{$uid}' limit 1") == 1;
	}
	
	// 判断品牌是否收藏
	public function isBrandFav($uid, $bid, $table) {
		return $this->_db->fetchOne("select 1 from " . $table ." where brand_id = '{$bid}' and user_id = '{$uid}' limit 1") == 1;
	}
	
	// 加喜欢
	public function addLike($postData) {
		$rs = $this->addConcern($postData['uname'], $postData['gid'], $postData['ip']);
		if ($rs) {
			return $this->returnArr(1, array(), 100, '加喜欢成功');
		} else {
			return $this->returnArr(0, array(), 300, '宝贝加喜欢失败');
		}
	}
	
	// 加收藏
	public function addFav($postData) {
		$rs = $this->addFavorite($postData['uname'], $postData['gid'], $postData['ip']);
		if ($rs) {
			return $this->returnArr(1, array(), 100, '加收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '加收藏失败');
		}
	}
	
	// 取消收藏
	public function cancelFavGood($postData, $uid) {
		$ip = is_null($postData['ip']) ? CLIENT_IP : $postData['ip'];
		$sql = "delete from oto_good_favorite where user_id = '{$uid}' and good_id = '{$postData['gid']}'";
		$result = $this->_db->query($sql);
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_good_favorite` where `good_id` = '{$postData['gid']}'");
			$this->_db->update('oto_good', array('favorite_number' => $num), "good_id = '{$postData['gid']}'");
			$this->updateUser($ip, $uid);
			return $this->returnArr(1, array(), 100, '取消收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '取消收藏失败');
		}
	}
	
	// 取消店铺收藏
	public function delFavShop($sid, $uid, $ip) {
		$ip = is_null($ip) ? CLIENT_IP : $ip;
		$sql = "delete from oto_shop_favorite where user_id = '{$uid}' and shop_id = '{$sid}'";
		$result = $this->_db->query($sql);
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_shop_favorite` where `shop_id` = '{$sid}'");
			$this->_db->update('oto_shop', array('favorite_number' => $num), "shop_id = '{$sid}'");
			$this->updateUser($ip, $uid);
			//删除oto_user_dynamic中相应的记录
			$this->removeFavoriteDynamic($uid,$sid,2);
				
			return $this->returnArr(1, array(), 100, '取消收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '取消收藏失败');
		}		
	}
	
	// 添加店铺收藏
	public function addShopFav($sid, $uid, $ip) {
		$ip = is_null($ip) ? CLIENT_IP : $ip;
		$result = $this->_db->replace('oto_shop_favorite', array('user_id' => $uid, 'shop_id' => $sid, 'created' => REQUEST_TIME));
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_shop_favorite` where `shop_id` = '{$sid}'");
			$this->_db->update('oto_shop', array('favorite_number' => $num), "shop_id = '{$sid}'");
			$this->updateUser($ip, $uid);
			//同步到oto_user_dynamic
			$shopName = $this->_db->fetchOne("SELECT `shop_name` FROM `oto_shop` WHERE `shop_id`='{$sid}'");
			$this->syncFavoriteDynamic(array('user_id' => $uid, 'from_id' => $sid, 'summary' => $shopName,'type'=>2, 'favorite_id'=>$result,'created' => REQUEST_TIME));
			
			return $this->returnArr(1, array(), 100, '店铺收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '店铺收藏失败');
		}
	}
	
	// 收藏的店铺列表
	public function getShopFav($lat, $lng, $uid, $city, $page, $pagesize = 10) {
		$key = "get_shop_fav_{$lat}_{$lng}_{$uid}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT b.shop_id, b.shop_name, b.shop_address, b.brand_id, b.brand_name, b.favorite_number,
				12756274*asin(Sqrt(power(sin(({$lat}-b.lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(b.lat*0.0174533)*power(sin(({$lng}-b.lng)*0.008726646),2))) as distance
				FROM oto_shop_favorite a LEFT JOIN oto_shop b ON a.shop_id = b.shop_id 
				WHERE a.user_id = '{$uid}' AND b.shop_status <> '-1' AND b.shop_pid = '0' and b.city = '{$city}'
				GROUP BY shop_name
				ORDER BY distance asc, b.created desc";
			}else{
				$sql = "SELECT b.* 
						FROM oto_shop_favorite a LEFT JOIN oto_shop b ON a.shop_id = b.shop_id 
						WHERE a.user_id = '{$uid}' AND b.shop_status <> '-1' AND b.shop_pid = '0' and b.city = '{$city}'
						ORDER BY b.created DESC";
			}
			$shopInfo = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($shopInfo as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				$brandRow = array();
				$brandRow = $this->_db->fetchRow("select brand_name_zh, brand_name_en from oto_brand where brand_id = '{$row['brand_id']}' limit 1");
				if(!empty($brandRow)) {
					if (empty($brandRow['brand_name_zh'])) {
						$row['brand_name'] = $brandRow['brand_name_en'];
					} elseif (empty($brandRow['brand_name_en'])) {
						$row['brand_name'] = $brandRow['brand_name_zh'];
					} else {
						$row['brand_name'] = $brandRow['brand_name_zh'] . "\n" . $brandRow['brand_name_en'];
					}
				} else {
					$row['brand_name'] = '';
				}
			}
			$data = $this->returnArr(count($shopInfo), $shopInfo);
			$this->setData($key, $data);
		}
		return $data;		
	}
	
	// 取消品牌收藏
	public function delFavBrand($bid, $uid, $ip) {
		$ip = is_null($ip) ? CLIENT_IP : $ip;
		$sql = "delete from oto_brand_favorite where user_id = '{$uid}' and brand_id = '{$bid}'";
		$result = $this->_db->query($sql);
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_brand_favorite` where `brand_id` = '{$bid}'");
			$this->_db->update('oto_brand', array('favorite_number' => $num), "brand_id = '{$bid}'");
			$this->updateUser($ip, $uid);
			//删除oto_user_dynamic中相应的记录
			$this->removeFavoriteDynamic($uid,$bid,4);
			return $this->returnArr(1, array(), 100, '取消收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '取消收藏失败');
		}		
	}
	
	// 添加品牌收藏
	public function addBrandFav($bid, $uid, $ip) {
		$ip = is_null($ip) ? CLIENT_IP : $ip;
		$result = $this->_db->replace('oto_brand_favorite', array('user_id' => $uid, 'brand_id' => $bid, 'created' => REQUEST_TIME));
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_brand_favorite` where `brand_id` = '{$bid}'");
			$this->_db->update('oto_brand', array('favorite_number' => $num), "brand_id = '{$bid}'");
			$this->updateUser($ip, $uid);
			//同步到oto_user_dynamic
			$brand = $this->_db->fetchRow("SELECT `brand_name_zh`,`brand_name_en` FROM `oto_brand` WHERE `brand_id`='{$bid}'");
			$this->syncFavoriteDynamic(array('user_id'=>$uid, 'from_id'=>$bid, 'summary'=>trim($brand["brand_name_zh"])?$brand["brand_name_zh"]:$brand["brand_name_en"],'type'=>4, 'favorite_id'=>$result,'created'=>REQUEST_TIME));
			return $this->returnArr(1, array(), 100, '品牌收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '品牌收藏失败');
		}		
	}
	
	// 获取头部推荐位
	public function getPhoneHead($pos_id, $city, $limit = 5, $is_cache = false) {
		$key = "get_api_phone_head_{$pos_id}_{$city}";
		$data = $this->getData($key);
		if (empty($data) || !$is_cache) {
			$sql = "select title, www_url, img_url, pmark, cmark from oto_recommend 
					where `pos_id` = '{$pos_id}' and `city` = '{$city}' 
					order by sequence asc, created desc 
					limit {$limit}";
			$arr= $this->_db->fetchAll($sql);
			foreach ($arr as &$row) {
				$row['ticket_uuid'] = '';
				$row['head_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/recommend/' . $row['img_url'];
				unset($row['img_url']);
				if (strpos($row['www_url'], 'i=')) {
					$ticket_uuid = substr($row['www_url'], -36);
					$row['ticket_uuid'] = $ticket_uuid;
				}
			}
			$data = $this->returnArr(count($arr), $arr);
			$this->setData($key, $data);
		}
		return $data ? $data : array();
	}
	
	public function getHead($pos_id, $city, $limit = 5) {
		$key = "get_api_head_{$city}_{$pos_id}";
		$data = $this->getData($key);
		if (empty($data)) {				
			$sql = "select title, www_url, img_url from oto_recommend 
					where `pos_id` = '{$pos_id}' and `city` = '{$city}'
					order by sequence asc, created desc 
					limit {$limit}";
			$data = $this->_db->fetchAll($sql);
			foreach ($data as &$row) {
				$row['head_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/recommend/' . $row['img_url'];
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getView($goods, $shops, $uuid, $uname, $gid){
		$goods['img'] = $this->getDetailImage($goods['good_id']);
		$goods['shop_img'] = $this->getImgByShop($goods['shop_id'], $gid);
		$goods['avatar'] = $this->getUserAvatar($goods['user_name']);
		$goods['shop_address'] = $shops['shop_address'];
		$goods['shop_name'] = $shops['shop_name'];
		$goods['lng'] = $shops['lng'];
		$goods['lat'] = $shops['lat'];
		if (!empty($uuid) && !empty($uname)) {
			$uid = $this->checkUid($uuid, $uname);
			$goods['is_like'] = $this->isLikeFav($uid, $gid, 'oto_good_concerned');
			$goods['is_fav'] = $this->isLikeFav($uid, $gid, 'oto_good_favorite');
		}		
		return $goods;
	}
	
	// 我的优惠券统计数量, 商品收藏统计数量, 上传的商品统计数量
	public function getMyNum($uuid, $uname) {
		$key = 'my_num_'. $uuid;
		$uid = $this->checkUid($uuid, $uname);
		$data = $this->getData($key);
		$uname = urldecode($uname);
		if (empty($data)) {
			$vaucherNum = 0;
			$myNum = $this->select("user_id = '{$uid}'", 'oto_user', 'favorite_number, concerned_number, ticket_number, good_number', '', true);
			$vaucherInfo = Custom_AuthTicket::getUserTicketList($uuid, -1);
			if ($vaucherInfo['code'] == '1') {
				$vaucherInfo = $vaucherInfo['message']['Results'];
				foreach ($vaucherInfo as &$row) {
					if (!empty($row['VCodeList'])) {
						$vaucherNum = $vaucherNum + 1;
					}
				}
			}
			$tickNum = $myNum['ticket_number'] + $vaucherNum;
			$myNum['ticketNum'] = $tickNum;
			$data = $this->returnArr(count($myNum), $myNum);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 优惠券领券逻辑
	public function applyTicket($ticket_id, $phone, $uuid, $uname) {
		// 获取网页端用户ID
		$uid = $this->checkUid($uuid, $uname);

		//事务开始
		$this->_db->beginTransaction();
		$status = 100;
		$msg = '';
		$extra = array();
		$sql = "select ticket_id, ticket_title, ticket_uuid, ticket_type, user_name, shop_id, par_value,
				selling_price, start_time, end_time, valid_stime, valid_etime,
				content, ticket_status, total, has_led, is_auth, created
				from `oto_ticket` where `ticket_id` = '{$ticket_id}' for update"; //获取券信息，加排他锁
		
		$ticketRow = $this->_db->fetchRow($sql);
		
		$mark = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
		
		
		if($ticketRow['start_time'] > REQUEST_TIME) {
			$status = 101;
			$msg = '抱歉，此券还未到领取时间！';
		}
		
		if($ticketRow['end_time'] < REQUEST_TIME) {
			$status = 101;
			$msg = '|抱歉，此券已经过期！';
		}
		
		if($ticketRow['is_auth'] == 0) {
			$status = 102;
			$msg = '|抱歉，此券已经下降！';
		}
		
		if($ticketRow['ticket_status'] == 0) {
			$status = 103;
			$msg = '|抱歉，此券正在审核中。。。';
		}
		
		if($ticketRow['ticket_status'] == '-1') {
			$status = 104;
			$msg = '|抱歉，此券未能通过审核！';
		}
		
		if($ticketRow['total'] - $ticketRow['has_led'] <= 0) {
			$status = 105;
			$msg = '|抱歉，此券已领完';
			//$extra = array('lave' => 0);
		}
		if($status == 100) {
			if($mark == 'coupon') {
				//相同的手机号码不能重复领取同一张优惠券
				if($this->whetherSamePhoneRepeat($ticket_id, $phone)) {
					$this->_db->rollBack();
					echo json_encode($this->returnArr(0, '', '106', '抱歉，本券你已经领取过一次了'));
					exit();
				}
				//判断同一个用户，同一张优惠券,最多可以领取2次
				if($this->whetherSameUserRepeatTwo($uid, $ticket_id)) {
					$this->_db->rollBack();
					echo json_encode($this->returnArr(0, '', '107', '抱歉，<b>' . $uname . '</b> 同一个用户名，每张优惠券只能领取2次'));
					exit();
				}
				//开始领券
				$couponTicketsArray = Custom_AuthTicket::getCouponTickets($uuid, $uname, $ticketRow['ticket_uuid'], $phone);
				if($couponTicketsArray['code']  == 1) { //领取成功!
					if($this->insertTicketDetail($phone, $uid, $uname, $ticketRow, $couponTicketsArray['message']) && $this->updateTicketLed($ticket_id)) {
						$this->_db->commit();
						//_exit('恭喜你，当前优惠券领取成功！', 100, array('lave' => $ticketRow['total'] - $ticketRow['has_led'] - 1));
						$this->updateUserTicket($uid);
						echo json_encode($this->returnArr(1, '', '100', '恭喜你，当前优惠券领取成功！'));
						exit();
					}
				} else {
					$this->_db->rollBack();
					echo json_encode($this->returnArr(0, '', '110', '券领取失败，请稍后再试！'));
					exit();
				}
			}	
		} else {
			$this->_db->rollBack();
			echo json_encode($this->returnArr(0, '', $status, $msg));
			exit();
		}
	}
	
	/**
	 * 判断同一个用户，相同的手机号码不能重复领取同一张优惠券
	 */
	private function whetherSamePhoneRepeat ($ticket_id, $phone) {
		return $this->_db->fetchOne("select 1 from `oto_ticket_detail` where `ticket_id` = '{$ticket_id}' and `phone_number` = '{$phone}'") == 1;
	}
	/**
	 * 判断同一个用户，同一张优惠券,最多可以领取2次
	 * @param unknown_type $user_id
	 * @param unknown_type $ticket_id
	 */
	private function whetherSameUserRepeatTwo($user_id, $ticket_id) {
		return $this->_db->fetchOne("select count(detail_id) from `oto_ticket_detail` where `user_id` = '{$user_id}' and `ticket_id` = '{$ticket_id}'") == 2;
	}
	/**
	 * 新增券领取记录
	 * @param unknown_type $phone
	 * @param unknown_type $userInfo
	 * @param unknown_type $ticketRow
	 */
	private function insertTicketDetail($phone, $uid, $uname, & $ticketRow, $ticket_token) {
		$param = array(
				'ticket_id' => $ticketRow['ticket_id'],
				'ticket_title' => $ticketRow['ticket_title'],
				'ticket_uuid' => $ticketRow['ticket_uuid'],
				'ticket_token' => $ticket_token,
				'user_id' => $uid,
				'user_name' => $uname,
				'phone_number' => $phone,
				'shop_id' => $ticketRow['shop_id'],
				'valid_stime' => $ticketRow['valid_stime'],
				'valid_etime' => $ticketRow['valid_etime'],
				'created' => REQUEST_TIME
		);
		return $this->_db->insert('oto_ticket_detail', $param);
	}
	/**
	 * 改变券领取数量
	 * @param unknown_type $ticket_id
	 */
	private function updateTicketLed($ticket_id) {
		//当前券被领取数量
		$sqlHasLed = "select count(detail_id) from oto_ticket_detail where `ticket_id` = '{$ticket_id}'";
		$hasLedNum = $this->_db->fetchOne($sqlHasLed);
	
		//修改当前券领取数量
		return $this->_db->update('oto_ticket', array('has_led' => $hasLedNum), "`ticket_id` = '{$ticket_id}'");
	}
	
	/**
	 * 雷达扫描模块一
	 * 1. 附近扫描
	 * 	  @param $lng
	 * 	  @param $lat 
	 * 	  @param $distance
	 * 2. 商圈商区扫描
	 * 	  @param $cid 
	 * 	  @param $rid 
	 */
	public function getScan($postData) {
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$distance = $postData['distance'];
		$rid = $postData['rid'];
		$cid = $postData['cid'];
		
		if ($distance) {
			$key = 'scan_num_'. $lng . '_' . $lat . '_' . $distance;
		}
		
 		if ($rid) {
 			$key = 'scan_num_region_'. $rid;
 		}
 		if ($cid) {
 			$key = 'scan_num_circle_'. $cid;
 		}
 		$data = $this->getData($key);
		if (empty($data)) {
			$sql = $this->scanCommen($lng, $lat, $distance, $rid, $cid);
			$shopInfo = $this->_db->fetchAssoc($sql);
			$rs = array();
			foreach($shopInfo as $row) {
				$rs[$row['brand_id']][] = $row;
			}
			$tmp = $rs;
			unset($tmp[0]);
			$shopIdArray = array_keys($shopInfo);
			
			// 优惠券个数
			$ticketNum = $this->_db->fetchOne("select count(ticket_id) from oto_ticket where " . $this->db_create_in($shopIdArray, 'shop_id') . " 
												and " . $this->couponWhereSql() . " order by created desc");
			// 宝贝个数
			$goodNum = $this->_db->fetchOne("select count(good_id) from oto_good where " . $this->db_create_in($shopIdArray, 'shop_id') . " and `good_status` <> '-1' and `is_auth` <> '-1' and `is_del` = '0' order by created desc");
			// 品牌个数
			$brandNum = count($tmp);
			// 店铺个数
			$shopNum = count($shopInfo);
			
			$data = array(
						'brandNum'  => $brandNum,
						'shopNum'   => $shopNum,
						'ticketNum' => $ticketNum,
						'goodNum'   => $goodNum,
					);
			// 附近活动个数
			if ($lng && $lat) {
				$activeInfo = Custom_AuthTicket::getArticleListByDistance($lng, $lat, $distance);
				$activeNum = count($activeInfo['message']['Result']);
				$data['activeNum'] = $activeNum;
			}
			
			$data = $this->returnArr(1, $data);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 雷达扫描模块二
	 * 1. 附近扫描
	 * 	  @param $lng
	 * 	  @param $lat
	 * 	  @param $distance
	 * 2. 商圈商区扫描
	 * 	  @param $cid
	 * 	  @param $rid
	 * 3. 区分扫描结果列表
	 * 	  @param type
	 */
	public function getScanList($postData, $city, $page, $pagesize = 10) {
		$type = $postData['type'];
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$distance = $postData['distance'];
		$rid = intval($postData['rid']);
		$cid = intval($postData['cid']);
		$key = "get_api_scan_list_{$type}_{$lng}_{$lat}_{$rid}_{$cid}_{$distance}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = $this->scanCommen($lng, $lat, $distance, $rid, $cid, $city);
			switch ($type) {
				case 'brand' :
					$brandIdArr =  $brindInfo = array();
					$shopInfo = $this->_db->fetchAssoc($sql);
					foreach($shopInfo as $row) {
						if($row['brand_id']) {
							$brandIdArr[] = $row['brand_id'];
						}
					}
					$sql1 = "select * from oto_brand where " . $this->db_create_in($brandIdArr, 'brand_id') . " and is_show = 1  order by firs_word asc, sequence asc";
					$brandHot = $this->_db->fetchAll($sql1);
					$sql2 = "select * from oto_brand where " . $this->db_create_in($brandIdArr, 'brand_id') . " order by firs_word asc, sequence asc";
					$brand = $this->_db->fetchAll($sql2);
					foreach ($brandHot as &$row_hot) {
						if (!empty($row_hot['brand_icon'])) {
							$row_hot['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row_hot['brand_icon'];
						}
					}
					foreach ($brand as &$row_all) {
						if (!empty($row_all['brand_icon'])) {
							$row_all['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row_all['brand_icon'];
						}
					}
					$brindInfo['hot'] = $brandHot;
					$brindInfo['normal'] = $brand;
					$data = $this->returnArr(count($brindInfo), $brindInfo);
					$this->setData($key, $data);
					break;
				case 'shop' :
					$start = ($page - 1) * $pagesize;
					$shopInfo = $this->_db->limitQuery($sql, $start, $pagesize);
					foreach ($shopInfo as &$row) {
						$brandInfo = array();
						$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
						$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$row['brand_id']}'");
						if($brandInfo) {
							if (empty($brandInfo['brand_name_zh'])) {
								$row['brand_name'] = $brandInfo['brand_name_en'];
							} elseif (empty($brandInfo['brand_name_en'])) {
								$row['brand_name'] = $brandInfo['brand_name_zh'];
							} else {
								$row['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
							}
							if (!empty($row['brand_logo'])) {
								$row['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_logo'];
							}							
						} else {
							$row['brand_name'] = '';
							$row['brand_logo'] = '';
						}
						$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
					}
					$data = $this->returnArr(count($shopInfo), $shopInfo);
					$this->setData($key, $data);
					break;
				case 'ticket' :
					$shopArray = $this->_db->fetchAssoc($sql);
					$shopIdArray = array_keys($shopArray);
					$getTicketSql = $this->getTicketSQL($this->db_create_in($shopIdArray, 'shop_id'));
					$couponInfo = $this->getTicketListInfo($getTicketSql, $page, $pagesize);
					foreach ($couponInfo as &$row) {
						$shop = $shopArray[$row['shop_id']];
						$shop['coupon_num'] = $this->getTicketNumByShopId($row['shop_id']);
						$row['shopInfo'][] = $shop;
					}
					$data = $this->returnArr(count($couponInfo), $couponInfo);
					$this->setData($key, $data);
					break;
				case 'good' :
					$start = ($page - 1) * $pagesize;
					$shopIdArray = array_keys($this->_db->fetchAssoc($sql));
					$sql = $this->getGoodsSql($this->db_create_in($shopIdArray, 'shop_id'));
					$data = $this->_db->limitQuery($sql, $start, $pagesize);
					$data = $this->getGoodImg($data);
					$data = $this->returnArr(count($data), $data);
					$this->setData($key, $data);
					break;
				case 'active' :
					$start = ($page - 1) * $pagesize;
					$activeInfo = Custom_AuthTicket::getArticleListByDistance($lng, $lat, $distance, $page);
					$data = $activeInfo['message']['Result'];	
					$data = $this->returnArr(count($data), $data);
					$this->setData($key, $data);
					break;
			}			
		}
		return $data;
	}
	
	public function getBrandViewHome($bid, $postData, $city, $page, $pagesize = 10) {
		$lng = $postData['lng'];
		$lat = $postData['lat']; 
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
        $key = "get_api_brand_view_home_{$uid}_{$bid}_{$lng}_{$lat}_{$city}_{$page}";
        $data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = "SELECT
					shop_id, shop_name, favorite_number, shop_address,
					12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance,
					lng, lat
					FROM oto_shop
					WHERE `shop_status` <> '-1' and `shop_pid` = '0' and `brand_id` = '{$bid}' and `city` = '{$city}'
					GROUP BY shop_name 
					ORDER BY distance asc";
			$allShop = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($allShop as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
				$row['has_tuan'] = $this->hasTuanByShop($row['shop_id']);
			}
			$brandInfo = $this->_db->fetchRow("select brand_name_zh, brand_name_en, brand_logo, brand_profile, brand_head, brand_icon, favorite_number from oto_brand where brand_id = '{$bid}'");
			
			if (!empty($brandInfo['brand_icon'])) {
				$brandInfo['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_icon'];
			}
			
			if (!empty($brandInfo['brand_head'])) {
				$brandInfo['brand_head'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_head'];
			}
			
			$brandInfo['brand_profile'] = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $brandInfo['brand_profile']);
			
			/**
			$ticket_type_voucher = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, app_price, start_time, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND brand_id = '{$bid}' and city = '{$city}' AND ticket_type = '{$ticket_type_voucher}' AND `ticket_class` = 2
						ORDER BY created desc";
			$ticket_list = $this->_db->fetchAll($c_sql);
			foreach ($ticket_list as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			$brandInfo['ticket_list'] = $ticket_list;
			*/
			
			// 券列表
			$ticket_type_voucher = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			//$sql_tids = "select a.ticket_id from oto_ticket_shop a left join oto_shop b on a.shop_id = b.shop_id where b.brand_id = '{$bid}' group by a.ticket_id";
			$sql_sids = "select shop_id from oto_shop where brand_id = '{$bid}'";
			$sidArrs = $this->_db->fetchCol($sql_sids);
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, app_price, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
					FROM oto_ticket
					WHERE " . $this->couponWhereSql() . " AND " . $this->db_create_in($sidArrs, 'shop_id') . "
					AND CITY = '{$city}' AND TICKET_TYPE = '{$ticket_type_voucher}' AND TICKET_CLASS = 2
					ORDER BY created desc";
			$ticket_list = $this->_db->fetchAll($c_sql);
			foreach ($ticket_list as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			$brandInfo['ticket_list'] = $ticket_list;

			
			// 团购列表
			$ticket_type_tuan = Model_Home_Ticket::getInstance()->getTicketTypeID('buygood');
			$t_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, app_price, start_time, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND brand_id = '{$bid}' and city = '{$city}' AND ticket_type = '{$ticket_type_tuan}' 
						ORDER BY created desc";
			$tuan_list = $this->_db->fetchAll($t_sql);
			foreach ($tuan_list as &$row) {
				$row['title'] = $row['ticket_title'];
				$tuan_img_small = $this->_db->fetchOne("select file_img_small from oto_ticket_info where ticket_id = '{$row['ticket_id']}'");
				$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $tuan_img_small;
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			$brandInfo['tuan_list'] = $tuan_list;
			
			$data = $this->returnArr(count($allShop), $allShop);
			$data['brandInfo'] = $brandInfo;		
			$this->setData($key, $data);
		}
		
		//品牌收藏数
		$data['brandInfo']['favorite_number'] = Model_Api_App::getInstance()->getFavoriteNum('oto_brand_favorite', $bid);
		
		if ($uid) {
			$data['brandInfo']['is_fav'] = intval($this->isBrandFav($uid, $bid, 'oto_brand_favorite'));
		}
		
		return $data;
	}
	
	public function getBrandViewScan($bid, $postData, $city, $page, $pagesize = 10) {
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$distance = $postData['distance'];
		$rid = $postData['rid'];
		$cid = $postData['cid'];
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);

		$key = "get_api_brand_view_{$bid}_{$cid}_{$lat}_{$lng}_{$distance}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($distance) {
				$sql = "SELECT
					shop_id, shop_name, favorite_number, shop_address, 
					12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance,
					lng, lat
					FROM oto_shop
					WHERE `shop_status` <> '-1' and `shop_pid` = '0' and `brand_id` = '{$bid}' and `city` = '{$city}'
					GROUP BY shop_name";
					
				$sql1 = $sql." HAVING distance <= {$distance} ORDER BY distance";
			}else {
				$where1 = "`city` = '{$city}'";
				if ($rid) {
					$where1 .= " and `region_id` = '{$rid}' and `brand_id` = '{$bid}' and ";
				} elseif ($cid) {
					$where1 .= " and `circle_id` = '{$cid}' and `brand_id` = '{$bid}' and ";
				}
				
				$sql_distance = '';
				$sql_group = '';
				if ($lng && $lat ) {
					$sql_distance = ", 12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance, lng, lat";
					$sql_group = " GROUP BY shop_name";
					$sql_order = " distance, ";
				}
				$sql1 = "SELECT shop_id, shop_name, favorite_number, shop_address ". $sql_distance . " FROM oto_shop  WHERE ". $where1 . " and shop_status <> -1 and shop_pid = 0 " .$sql_group ." order by ". $sql_order ." created desc";	
			}
			
			$allShop = $this->_db->limitQuery($sql1, $start, $pagesize);
			foreach ($allShop as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
				$row['has_tuan'] = $this->hasTuanByShop($row['shop_id']);
			}
			$brandInfo = $this->_db->fetchRow("select brand_name_zh, brand_name_en, brand_logo, brand_profile, brand_head, brand_icon, favorite_number from oto_brand where brand_id = '{$bid}'");
			if (!empty($brandInfo['brand_logo'])) {
				$brandInfo['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_logo'];
			}
			
			if (!empty($brandInfo['brand_icon'])) {
				$brandInfo['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_icon'];
			}
			
			$brandInfo['brand_profile'] = specialHtmlConversion($brandInfo['brand_profile']);
			
			if ($uid) {
				$brandInfo['is_fav'] = $this->isBrandFav($uid, $bid, 'oto_brand_favorite');
			}
			
			/**
			$ticket_type_voucher = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, app_price, start_time, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND brand_id = '{$bid}' and city = '{$city}' AND ticket_type = '{$ticket_type_voucher}' AND `ticket_class` = 2
						ORDER BY created desc";
			$ticket_list = $this->_db->fetchAll($c_sql);
			foreach ($ticket_list as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			*/
			
			// 券列表
			$ticket_type_voucher = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			//$sql_tids = "select a.ticket_id from oto_ticket_shop a left join oto_shop b on a.shop_id = b.shop_id where b.brand_id = '{$bid}' group by a.ticket_id";
			$sql_sids = "select shop_id from oto_shop where brand_id = '{$bid}'";
			$sidArrs = $this->_db->fetchCol($sql_sids);
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, app_price, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . " AND " . $this->db_create_in($sidArrs, 'shop_id') . "
						AND CITY = '{$city}' AND TICKET_TYPE = '{$ticket_type_voucher}' AND TICKET_CLASS = 2
						ORDER BY created desc";
			$ticket_list = $this->_db->fetchAll($c_sql);
			foreach ($ticket_list as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			
			}
			$brandInfo['ticket_list'] = $ticket_list;
				
			// 团购列表
			$ticket_type_tuan = Model_Home_Ticket::getInstance()->getTicketTypeID('buygood');
			$t_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, app_price, start_time, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND brand_id = '{$bid}' and city = '{$city}' AND ticket_type = '{$ticket_type_tuan}'
						ORDER BY created desc";
						$tuan_list = $this->_db->fetchAll($t_sql);
			foreach ($tuan_list as &$row) {
				$row['title'] = $row['ticket_title'];
				$tuan_img_small = $this->_db->fetchOne("select file_img_small from oto_ticket_info where ticket_id = '{$row['ticket_id']}'");
				$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $tuan_img_small;
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
			}
			$brandInfo['tuan_list'] = $tuan_list;

			$data = $this->returnArr(count($allShop), $allShop);
			$data['brandInfo'] = $brandInfo;
			$this->setData($key, $data);
		}
		return $data;
	}
	
	
	/**
	 * 获取品牌详情
	 * @param $bid
	 * @param $lat
	 * @param $lng
	 */
	public function getBrandView($uid, $bid, $postData, $city, $page, $pagesize = 10) {
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$distance = $postData['distance'];
		$rid = $postData['rid'];
		$cid = $postData['cid'];
		
		$key = "get_api_brand_view_{$bid}_{$lat}_{$lng}_{$distance}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($distance) {
				$sql = "SELECT
					shop_id, shop_name, favorite_number, shop_address, 
					12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance,
					lng, lat
					FROM oto_shop
					WHERE `shop_status` <> '-1' and `shop_pid` = '0' and `brand_id` = '{$bid}' and `city` = '{$city}'
					GROUP BY shop_name";
					
				$sql1 = $sql." HAVING distance <= {$distance} ORDER BY distance";
				$sql2 = $sql." HAVING distance > {$distance} ORDER BY distance";
			}else {
				$where1 = "`city` = '{$city}'";
				$where2 = "`city` = '{$city}'";
				
				if ($rid) {
					$where1 .= " and `region_id` = '{$rid}' and `brand_id` = '{$bid}'";
					$where2 .= " and `brand_id` = '{$bid}' and `region_id` <> '{$rid}'";
				} elseif ($cid) {
					$where1 .= " and `circle_id` = '{$cid}' and `brand_id` = '{$bid}'";
					$where2 .= " and `brand_id` = '{$bid}' and `circle_id` <> '{$cid}'";
				}
				
				$sql_distance = '';
				$sql_group = '';
				if ($lng && $lat ) {
					$sql_distance = ", 12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance, lng, lat";
					$sql_group = " GROUP BY shop_name";
					$sql_order = " distance, ";
				}
				$sql1 = "SELECT shop_id, shop_name, favorite_number, shop_address ". $sql_distance . " FROM oto_shop  WHERE ". $where1 . " and shop_status <> '-1' and shop_pid = '0' " .$sql_group ." order by ". $sql_order ." created desc";
				$sql2 = "SELECT shop_id, shop_name, favorite_number, shop_address ". $sql_distance . " FROM oto_shop  WHERE ". $where2 . " and shop_status <> '-1' and shop_pid = '0' " .$sql_group ." order by ". $sql_order ." created desc";
			}
			
			// 当前商圈店铺列表
			$currentShop = $allShop = $shopData = array();
			$currentShop = $this->_db->fetchAll($sql1);
			foreach ($currentShop as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
			}
			// 更多门店
			$allShop = $this->_db->limitQuery($sql2, $start, $pagesize);
			foreach ($allShop as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
			}

			if ($uid) {
				$shopData['is_fav'] = $this->isBrandFav($uid, $bid, 'oto_brand_favorite');
			}
			
			if ($page == 1) {
				$shopData['current'] = $currentShop;
				$shopData['all'] = $allShop;
			} else {
				$shopData['all'] = $allShop;
			}
			$data = $this->returnArr(count($allShop), $shopData);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 店铺详情页
	 * 显示店铺品牌icon、店铺名称、地理位置、关联的优惠券和店内商品
	 * @param unknown_type $sid
	 */
	public function getShopView($sid, $uid, $goodNum) {
		$key = "get_api_shop_view_{$sid}_{$goodNum}";
		$data = $this->getData($key);
		if (empty($data)) {
			$shopView = $this->getShopRow($sid);
			if($shopView['brand_id']) {
				$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$shopView['brand_id']}'");
				if (empty($brandInfo['brand_name_zh'])) {
					$shopView['brand_name'] = $brandInfo['brand_name_en'];
				} elseif (empty($brandInfo['brand_name_en'])) {
					$shopView['brand_name'] = $brandInfo['brand_name_zh'];
				} else {
					$shopView['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
				}
				if (!empty($brandInfo['brand_logo'])) {
					$shopView['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_logo'];
				}
			} else {
				$shopView['brand_name'] = $shopView['brand_logo'] = '';
			}
			
			if ($uid) {
				$shopView['is_fav'] = $this->isShopFav($uid, $sid, 'oto_shop_favorite');
			}
			
			$getTicketSql = $this->getTicketSQL("`shop_id` = '{$sid}'");
			$shopView['ticket'] = $this->getTicketListInfo($getTicketSql, 1);
			$goodInfo = $this->_db->fetchAll($this->getGoodsSql("`shop_id` = '{$sid}'"));
			$shopView['good_img'] = array_slice($this->getGoodImg($goodInfo), 1, $goodNum); // 暂时取7条记录
			$data = $this->returnArr(1, $shopView);
			$this->setData($key, $data);
		}
		return $data;		
	}
	
	/**
	 * 扫描模块
	 * 获取宝贝详情页
	 */
	public function getGoodView($gid, $postData) {
		$lng = $postData['lng'];
		$lat = $postData['lat'];
		$distance = $postData['distance'];
		$rid = $postData['rid'];
		$cid = $postData['cid'];
		$uuid = $postData['uuid'];
		$uname = urldecode($postData['uname']);
		if ($distance) {
			$key = 'scan_good_view_'. $gid . '_' . $lat . '_' . $lng . '_' . $distance;
		} else {
			if ($rid) {
				$key = 'scan_good_view_'. $gid. '_' . $rid;
			}
			if ($cid){
				$key = 'scan_good_view_'. $gid. '_' . $cid;
			}
		}
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = $this->scanCommen($lng, $lat, $distance, $rid, $cid);
			$shopIds = array_keys($this->_db->fetchAssoc($sql));
			$previous_id = $this->getPrevious('oto_good', $this->db_create_in($shopIds, 'shop_id'), $gid);
			$next_id = $this->getNext('oto_good', $this->db_create_in($shopIds, 'shop_id'), $gid);
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
			$goods['previous'] = intval($previous_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/scan-good-view/gid/'. $previous_id : '';
			$goods['next'] = intval($next_id) ? $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/goods/scan-good-view/gid/'. $next_id : '';
			$goods = $this->getView($goods, $shops, $uuid, $uname, $gid);
			$data = $this->returnArr(1, $goods);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 宝贝详情单页 通用接口
	 * @param $gid
	 */
	public function getGoodOneDetail($gid, $postData, $city, $page, $pagesize) {
		$uuid = $postData['uuid'];
		$uname = urldecode($postData['uname']);
		$key = "get_aid_good_one_detail_{$city}_{$gid}";
		$data = $this->getData($key);
		if (empty($data)) {
			$goods = $this->getGoodsRow($gid);
			$shops = $this->getShopRow($goods['shop_id']);
				
			$goods['img'] = $this->getDetailImage($goods['good_id']);
			$goods['avatar'] = $this->getUserAvatar($goods['user_name']);
			$goods['shop_address'] = $shops['shop_address'];
			$goods['shop_name'] = $shops['shop_name'];
			$goods['lng'] = $shops['lng'];
			$goods['lat'] = $shops['lat'];

				
			$start = ($page - 1) * $pagesize;
			$sql = $this->getGoodsSql("`shop_id` = '{$goods['shop_id']}' and `good_id` <> '{$gid}' and `city` = '{$city}'");
			$goodList = $this->_db->limitQuery($sql, $start, $pagesize);
			$arr = $this->getGoodImg($goodList);
			$goods['shop_img'] = $arr;
			$goods['created'] = date('Y.n.j', $goods['created']);
			$data = $goods;
			unset($goods);
			$this->setData($key, $data);
		}
		
		if (!empty($uuid) && !empty($uname)) {
			$uid = $this->checkUid($uuid, $uname);
			$data['is_like'] = intval($this->isLikeFav($uid, $gid, 'oto_good_concerned'));
			$data['is_fav'] = intval($this->isLikeFav($uid, $gid, 'oto_good_favorite'));
		}

		//获取商品收藏数
		$data['favorite_number'] = Model_Api_App::getInstance()->getFavoriteNum('oto_good_favorite', $gid);
		$data['concerned_number'] = Model_Api_App::getInstance()->getFavoriteNum('oto_good_concerned', $gid);
		
		$data = $this->returnArr(1, $data);
		
		return $data;
	}
	
	/**
	 * 扫描功能 前期选择条件
	 * @param unknown_type $lng
	 * @param unknown_type $lat
	 * @param unknown_type $distance
	 * @param unknown_type $rid
	 * @param unknown_type $cid
	 */
	public function scanCommen($lng, $lat, $distance, $rid, $cid, $city) {
		$where = " and `city` = '{$city}'";
		if ($distance) {
			$sql = "SELECT
					shop_id, shop_name, brand_id, brand_name, favorite_number, shop_address, 
					12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance,
					lng, lat, is_flag
					FROM oto_shop
					WHERE shop_status <> '-1' and shop_pid = '0' {$where}
					GROUP BY shop_name
					HAVING distance <= '{$distance}'
					ORDER BY distance ";
		} else {
			if ($rid) {
				$where .= " and `region_id` = '{$rid}'";
			}elseif ($cid) {
				$where .= " and `circle_id` = '{$cid}'";
			}
			
			$sql_distance = '';
			$sql_group = '';
			if ($lng && $lat) {
				$sql_distance = ", 12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance, lng, lat";
				$sql_group = " GROUP BY shop_name";
				$sql_order = " distance asc, ";
			}
			$sql = "SELECT shop_id, shop_name, brand_id, brand_name, favorite_number, shop_address ". $sql_distance . " FROM oto_shop  WHERE 1 ". $where . " and shop_status <> -1 and shop_pid = 0 " .$sql_group ." order by ". $sql_order ." created desc";
		}
		return $sql;
	}
	
	/**
	 * 获取优惠券信息  LIST
	 * @param unknown_type $sqlwhere
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 * @return string
	 */
	public function getTicketListInfo($sql, $page, $pagesize = 10) {
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
			$couponInfo[$key]['content'] = $value['content'];
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
		return $couponInfo;
	}
	
	// 商店下面是否有优惠券
	public function hasTicketByShop($shop_id) {
		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
		return $this->_db->fetchOne("select 1 from `oto_ticket` WHERE `shop_id` = '{$shop_id}' AND TICKET_TYPE = '{$ticket_type}' AND " . $this->couponWhereSql()) == 1;
	}
	
	// 商店下面是否有团购
	public function hasTuanByShop($shop_id) {
		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('buygood');
		return $this->_db->fetchOne("select 1 from `oto_ticket` WHERE `shop_id` = '{$shop_id}' AND TICKET_TYPE = '{$ticket_type}' AND " . $this->couponWhereSql()) == 1;
	}
	
	// 商场下面是否有优惠券
	public function hasTicketByMarket($mid, $city) {
		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
		$sql_tids = "select a.ticket_id from oto_ticket_shop a left join oto_shop b on a.shop_id = b.shop_id where b.market_id = '{$mid}'";
		$tidArrs = $this->_db->fetchCol($sql_tids);
		$c_sql = "SELECT 1 FROM oto_ticket
					WHERE " . $this->couponWhereSql() . " AND " . $this->db_create_in($tidArrs, 'ticket_id') . "
					AND CITY = '{$city}' AND TICKET_TYPE = '{$ticket_type}' AND TICKET_CLASS = 1
					ORDER BY created desc";
		return $this->_db->fetchOne($c_sql) == 1;
	}
	
	// 品牌下面是否有券
	public function hasTicketByBrand ($bid, $city) {
		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');

		$sql_sids = "select shop_id from oto_shop where brand_id = '{$bid}'";
		$shopArrs = $this->_db->fetchCol($sql_sids);
		$c_sql = "SELECT 1 FROM oto_ticket
					WHERE " . $this->couponWhereSql() . " AND " . $this->db_create_in($shopArrs, 'shop_id') . "
					AND CITY = '{$city}' AND TICKET_TYPE = '{$ticket_type}' AND TICKET_CLASS = 2
					ORDER BY created desc";
		return $this->_db->fetchOne($c_sql) == 1;
	}
	
	// 品牌下面是否有团购
	public function hasTuanByBrand ($bid) {
		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('buygood');
		return $this->_db->fetchOne("select 1 from `oto_ticket` WHERE `brand_id` = '{$bid}' AND `ticket_type` = '{$ticket_type}'  AND " . $this->couponWhereSql()) == 1;
	}
	
	/**
	 * 所有品牌列表
	 * @return 1. 热门品牌
	 * 		   2. 非热门品牌  按照收藏数，排序字段  来进行排序	
	 */
	public function getBrandList($city, $page, $pagesize = 10) {
		$key = "get_api_brand_list_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			
			$brindInfo = $hotBrand = $allBrand = array();
			//热门品牌
			$sqlHot = "select * from oto_brand 
					   where `is_show` = '1' and `city` = '{$city}'
					   order by `sequence` asc, `created` desc";
			$hotBrand = $this->_db->fetchAll($sqlHot);
			foreach ($hotBrand as &$row_hot) {
				if (!empty($row_hot['brand_logo'])) {
					$row_hot['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row_hot['brand_logo'];
				}
			}
			//全部非热门品牌
			$start = ($page - 1) * $pagesize;
			$sql = "select * from oto_brand 
					where `is_show` <> '1' and `city` = '{$city}'
					order by sequence asc, favorite_number desc, `created` desc";
			$allBrand = $this->_db->limitQuery($sql, $start, $pagesize);		
			foreach ($allBrand as &$row_all) {
				if (!empty($row_all['brand_logo'])) {
					$row_all['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row_all['brand_logo'];
				}
			}
			
			if ($page == 1) {
				$brindInfo['hot'] = $hotBrand;
				$brindInfo['all'] = $allBrand;
			} else {
				$brindInfo['all'] = $allBrand;
			}
			
			$data = $this->returnArr(count($allBrand), $brindInfo);
			$this->setData($key, $data);
		}
		return $data;		
	}
	
	/**
	 * 品牌列表 2014-08-20
	 */
	public function getBrandListNew($city) {
		$key = "get_api_brand_list_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
				
			$brindInfo = $hotBrand = $allBrand = array();
			//热门品牌
			$sqlHot = "select * from oto_brand
					   where `is_show` = '1' and `city` = '{$city}'
					   order by `sequence` asc, `created` desc";
			$hotBrand = $this->_db->fetchAll($sqlHot);
			foreach ($hotBrand as &$row_hot) {
				if (!empty($row_hot['brand_logo'])) {
					$row_hot['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row_hot['brand_logo'];
				}
			}
			//全部非热门品牌
			$sqlAll= "select * from oto_brand
				    where `is_show` <> '1' and `city` = '{$city}'
					order by sequence asc, favorite_number desc, `created` desc";
			$allBrand = $this->_db->fetchAll($sqlAll);
			foreach ($allBrand as &$row_all) {
				if (!empty($row_all['brand_logo'])) {
					$row_all['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row_all['brand_logo'];
				}
			}
				
			$brindInfo['hot'] = $hotBrand;
			$brindInfo['all'] = $allBrand;
				
			$data = $this->returnArr(count($allBrand), $brindInfo);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getBrandListByName($bname, $city, $page, $pagesize = 10) {
		$key = "get_api_brand_serach_by_name_{$bname}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = "select * from oto_brand 
					where (brand_name_zh like '%{$bname}%' or brand_name_en like '%{$bname}%') and `city` = '{$city}'
					order by `sequence` asc, `favorite_number` desc, `created` desc";
			$brandList = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($brandList as &$row) {
				if (!empty($row['brand_icon'])) {
					$row['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'];
				}
				$row['has_ticket'] = $this->hasTicketByBrand($row['brand_id'], $city);
				$row['has_tuan'] = $this->hasTuanByBrand($row['brand_id']);			
			}
			$data = $this->returnArr(count($brandList), $brandList);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	
	/**
	 * 收藏的品牌列表
	 * @param unknown_type $uid
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public function getFavBrandList($uid, $city, $page, $pagesize = 10) {
		$key = "get_api_fav_brand_list_{$uid}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$brindInfo = $allBrand = array();
			$brand_sql = "select brand_id from oto_brand_favorite where user_id = '{$uid}'";
			$brand_id_arr = $this->_db->fetchCol($brand_sql);
			$where = " and `city` = '{$city}' and " .  $this->db_create_in($brand_id_arr, 'brand_id');
			$sql_all = "select * from oto_brand where 1 {$where} order by created desc";
			$allBrand = $this->_db->limitQuery($sql_all, $start, $pagesize);
			foreach ($allBrand as &$row) {
				$row['brand_icon'] = $row['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'] : '';
			}
			$brindInfo['all'] = $allBrand;
			$data = $this->returnArr(count($allBrand), $brindInfo);
			$this->setData($key, $data);
		}
		return $data;		
	}
	
	/**
	 * 搜索品牌
	 * @return 根据关键字查询后的品牌， 按照收藏数，排序字段  来进行排序	
	 */
	public function getBrandByKey($key, $city, $page, $pagesize = 10) {
		$cachekey = "get_brand_by_key_{$key}_{$city}_{$page}";
		$data = $this->getData($cachekey);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = "select * from oto_brand 
					where (brand_name_zh like '%{$key}%' or brand_name_en like '%{$key}%') and `city` = '{$city}' 
					order by sequence asc, favorite_number desc, brand_id desc";
			$brandList = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($brandList as &$row) {
				if (!empty($row['brand_icon'])) {
					$row['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'];
				}
				$row['has_ticket'] = $this->hasTicketByBrand($row['brand_id'], $city);
				$row['has_tuan'] = $this->hasTuanByBrand($row['brand_id']);
			}
			$data = $this->returnArr(count($brandList), $brandList);
			$this->setData($cachekey, $data);
		}
		return $data;
	}
	
	/**
	 * 所有店铺列表
	 * @return 1. 存在经纬度  按照距离排序   根据收藏数排序
	 * 		   2. 不存在经纬度 根据收藏数排序	
	 */
	public function getShopList($postData, $city, $page, $pagesize = 10) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];		
		$key = "get_api_shop_list_{$lat}_{$lng}_{$city}_{$page}";
		$data = $this->getData($key);
		
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT shop_id, shop_name, shop_address, brand_id, brand_name, favorite_number, is_flag,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_shop
						WHERE shop_status <> '-1' and shop_pid = '0' and `city` = '{$city}'
						GROUP BY shop_name
						ORDER BY distance asc, favorite_number desc";
			} else {
				$sql = "SELECT shop_id, shop_name, shop_address, brand_id, brand_name, favorite_number, is_flag 
						FROM oto_shop 
						WHERE shop_status <> '-1' and shop_pid = '0' and `city` = '{$city}'
						order by favorite_number desc, created desc";
			}
			$shopList = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($shopList as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				if($row['brand_id']) {
					$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$row['brand_id']}'");
					if (empty($brandInfo['brand_name_zh'])) {
						$row['brand_name'] = $brandInfo['brand_name_en'];
					} elseif (empty($brandInfo['brand_name_en'])) {
						$row['brand_name'] = $brandInfo['brand_name_zh'];
					} else {
						$row['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
					}
					if (!empty($row['brand_logo'])) {
						$row['brand_logo'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_logo'];
					}
				} else {
					$row['brand_name'] = $row['brand_logo'] = '';
				}
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
			}
			$data = $this->returnArr(count($shopList), $shopList);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 搜索店铺
	 * @return 根据关键字查询后的店铺列表
	 *         1. 存在经纬度  按照距离排序   根据收藏数排序
	 * 		   2. 不存在经纬度 根据收藏数排序	
	 */
	public function getShopByKey($keyword, $postData, $city, $page, $pagesize = 10) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$key = "get_api_shop_by_key_{$lat}_{$lng}_{$keyword}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT shop_id, shop_name, shop_address, brand_id, brand_name, favorite_number, is_flag, 
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_shop
						WHERE `shop_status` <> '-1' and `shop_pid` = '0' and `shop_name` like '%{$keyword}%' and `city` = '{$city}'
						GROUP BY shop_name
						ORDER BY `distance` asc, `favorite_number` desc";
			} else {
				$sql = "SELECT shop_id, shop_name, shop_address, brand_id, brand_name, favorite_number, is_flag 
						FROM oto_shop 
						WHERE `shop_status` <> '-1' and `shop_pid` = '0' and `shop_name` like '%{$keyword}%' and `city` = '{$city}'
						order by `favorite_number` desc, `created` desc";
			}
			$shopList = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($shopList as &$row) {
				$row['has_ticket'] = $this->hasTicketByShop($row['shop_id']);
				if($row['brand_id']) {
					$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$row['brand_id']}'");
					if (empty($brandInfo['brand_name_zh'])) {
						$row['brand_name'] = $brandInfo['brand_name_en'];
					} elseif (empty($brandInfo['brand_name_en'])) {
						$row['brand_name'] = $brandInfo['brand_name_zh'];
					} else {
						$row['brand_name'] = $brandInfo['brand_name_zh'] . "\n" . $brandInfo['brand_name_en'];
					}
				} else {
					$row['brand_name'] = '';
				}
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
			}
			$data = $this->returnArr(count($shopList), $shopList);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getShopByName($sname, $city) {
		$key = "get_api_shop_by_name_{$sname}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			$sql = "SELECT shop_id, shop_name, shop_address, is_flag 
					FROM oto_shop WHERE `shop_status` <> '-1' and `shop_pid` = '0' 
					and `shop_name` LIKE '%{$sname}%' and `city` = '{$city}'
					order by favorite_number desc, created desc";
			
			$shopList = $this->_db->fetchAll($sql);
			foreach ($shopList as &$row) {
				$row['ticket_num'] = $this->getTicketNumByShopId($row['shop_id']);
			}
			$data = $this->returnArr(count($shopList), $shopList);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 我的获奖历史
	 * 1.天天向上 2：十全大补 3：街友最划算 4：店员最划算 5：提取现金 6:营业员推荐返利
	 */
	public function getMyWin($uid, $page, $pagesize = 10) {
		$key = 'my_winners_' . $uid . '_' . $page;
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = "select * from oto_task_log where `user_id` = '{$uid}' and `task_type` <> '5' order by `created` desc";
			$myWin = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($myWin as &$row) {
				switch ($row['task_type']) {
					case '1' :
						$row['task_win_name'] = '天天向上';
					break;
					case '2' :
						$row['task_win_name'] = '十全大补';
					break;
					case '3' :
						$row['task_win_name'] = '街友最划算';
					break;
					case '4' :
						$row['task_win_name'] = '店员最划算';
					break;
					case '6' :
						$row['task_win_name'] = '营业员推荐返利';
					break;
				}
				$row['win_time'] = date('Y-n-d', $row['created']);
			}
			$data = $this->returnArr(count($myWin), $myWin);			
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 获取用户类型
	 */
	public function getLocalUserInfo($uid) {
		return $this->select("`user_id` = '{$uid}'", 'oto_user', '*', '', true);
	}
		
	public function setKeywords($keyword, $type , $city) {
		if(empty($keyword)) {
			return;
		}		
		$keyword = strip_tags($keyword);
		$keywordId = $this->_db->fetchOne("select keyword_id from oto_search_keyword where `keyword_name` = '{$keyword}' and `keyword_type` = '{$type}' and `city` = '{$city}' limit 1");		
		if ($keywordId) {
			$sql = " update oto_search_keyword set keyword_searches = keyword_searches + 1 where keyword_id = '{$keywordId}'";
			$this->_db->query($sql);
		} else {
			$this->_db->insert('oto_search_keyword', array('keyword_name' => $keyword, 'keyword_searches' => 1, 'keyword_type' => $type, 'city' => $city));
		}
	}
	
	public function getHotKey($type, $city, $limit = PAGESIZE) {
		$key = "get_api_hot_key_{$type}_{$city}";
		$data = $this->getData($key);
		
		if(empty($data)) {
			$sql = "select `keyword_name` 
					from oto_search_keyword where `keyword_type` = '{$type}' and `city` = '{$city}' 
					order by `keyword_searches` desc limit {$limit}";
			$keyWords = $this->_db->fetchAll($sql);
			$data = $this->returnArr(count($keyWords), $keyWords);
		}
		
		return $data;
	}
	
	/**
	 * 获取店铺下 所有现金券的数目
	 */
	public function getTicketNumByShopId($shop_id) {
		$ticketType = $this->getTicketSortById(0, 'ticketsort', 'voucher');
		$voucherNum = $this->_db->fetchOne("select count(*) from oto_ticket where shop_id = '{$shop_id}' and ticket_type = '{$ticketType}' and " . $this->couponWhereSql());
		
		$couponNumOther = $this->_db->fetchOne("select count(*) 
									from oto_ticket ot left join oto_ticket_shop  ots
									on ot.ticket_id = ots.ticket_id 
									where ots.shop_id = '{$shop_id}' and ot.`ticket_status` = '1' AND ot.`is_auth` = '1' AND ot.`is_show` = 1 AND ot.`end_time` > '" . REQUEST_TIME . "' AND ot.`start_time` < '" . REQUEST_TIME . "'");
		
		$ticketNum = $voucherNum + $couponNumOther;
		return $ticketNum;
	}
	
	/**
	 * 获取店铺下 所有团购商品的数目
	 */
	public function getTuanNumByShopId($shop_id) {
		$ticketType = $this->getTicketSortById(0, 'ticketsort', 'buygood');
		$voucherNum = $this->_db->fetchOne("select count(*) from oto_ticket where shop_id = '{$shop_id}' and ticket_type = '{$ticketType}' and " . $this->couponWhereSql());
	
		$couponNumOther = $this->_db->fetchOne("select count(*)
				from oto_ticket ot left join oto_ticket_shop  ots
				on ot.ticket_id = ots.ticket_id
				where ots.shop_id = '{$shop_id}' and ot.`ticket_status` = '1' AND ot.`is_auth` = '1' AND ot.`is_show` = 1 AND ot.`end_time` > '" . REQUEST_TIME . "' AND ot.`start_time` < '" . REQUEST_TIME . "'");

		$ticketNum = $voucherNum + $couponNumOther;
		return $ticketNum;
	}
	
	public function getAppStore($city) {
		$data = @include VAR_PATH . 'config/store.php';
		$newData = array();
		foreach ($data[$city] as $id=>$name){
			$newData[] = array(
					'id' => $id,
					'name' => $name,
			);
		}
		return $newData;
	}
	
	// 获取超值精选
	public function getRecommendListByIdentifier($identifier, $city, $page, $pagesize = 10) {
		$key = "get_api_recommend_list_by_identifier_{$identifier}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$pos_id = $this->getPosId($identifier, $city);
			$start = ($page - 1) * $pagesize;
			
			$sql = "SELECT r.`come_from_id`, g.`good_id`, g.`good_name`, g.`shop_id`, g.`shop_name`, g.`user_id`, g.`user_name`, g.`org_price`, 
							g.`dis_price`, g.`concerned_number`, g.`favorite_number`, g.`is_auth` 
					FROM oto_recommend r 
					LEFT JOIN oto_good g 
					ON r.come_from_id = g.good_id 
					WHERE r.`pos_id` = '{$pos_id}' AND r.`come_from_type` = 1 AND g.`good_status` <> '-1' AND g.`is_auth` <> '-1' AND g.`is_del` = '0'
					ORDER by r.sequence asc, r.created desc";
			$pickInfo = $this->_db->limitQuery($sql, $start, $pagesize);
			$data = $this->getGoodImg($pickInfo);
			foreach ($data as &$row) {
				$avatar = $this->getUserAvatar($row['user_name']);
				if(!$avatar) {
					$row['avatar'] = '';
				} else {
					$row['avatar'] = $avatar;
				}
			}
			$data = $this->returnArr(count($data), $data);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getMarket($postData, $city, $page, $pagesize = PAGESIZE) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$key = "get_api_market_{$lat}_{$lng}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT market_id, market_name, market_address, logo_img,  
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market 
						WHERE `city` = '{$city}'
						ORDER BY `sequence` desc, `distance` asc, `market_id` desc";
			} else {
				$sql = "SELECT market_id, market_name, market_address, logo_img 
						FROM oto_market
						WHERE `city` = '{$city}'
						ORDER BY `sequence` desc, `market_id` desc";
			}
			$marketlist = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($marketlist as &$row) {
				if (!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*) 
								 FROM oto_shop os 
								 LEFT JOIN oto_market om ON os.market_id = om.market_id 
								 WHERE os.shop_status <> '-1' AND os.shop_pid = '0' AND om.market_id = '{$row['market_id']}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
				$row['has_ticket'] = $this->hasTicketByMarket($row['market_id'], $city);
			}
			
			$data = $this->returnArr(count($marketlist), $marketlist);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getMarketByName($postData, $city, $page, $pagesize = PAGESIZE) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$name = trim(urldecode($postData['name']));
		$key = "get_api_market_by_name_{$name}_{$lat}_{$lng}_{$city}_{$page}";		
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$lngLatArray = $this->germanToBaidu($lng, $lat);
				$lng = $lngLatArray['lng'];
				$lat = $lngLatArray['lat'];
				$sql = "SELECT market_id, market_name, market_address, logo_img,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market 
						WHERE market_name LIKE '%{$name}%' and `city` = '{$city}'
						ORDER BY `sequence` desc, `distance` asc, `market_id` desc";
			} else {
				$sql = "SELECT market_id, market_name, market_address, logo_img FROM oto_market 
						WHERE market_name LIKE '%{$name}%' and `city` = '{$city}'
						ORDER BY `sequence` desc, `market_id` desc";
			}
			$marketlist = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($marketlist as &$row) {
				if (!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*) 
								FROM oto_shop os LEFT JOIN oto_market om ON os.market_id = om.market_id 
								WHERE os.shop_status <> '-1' AND os.shop_pid = '0' AND om.market_id = '{$row['market_id']}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
				$row['has_ticket'] = $this->hasTicketByMarket($row['market_id'], $city);
			}
			$data = $this->returnArr(count($marketlist), $marketlist);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getMarketBySearch($postData, $city, $page, $pagesize = 10) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$distance = $postData['distance'];
		$region_id = intval($postData['region_id']);
		if(!$region_id) {
			$name = Custom_String::HtmlReplace(trim(urldecode($postData['name'])), 1);
			$key = "get_api_market_by_search_{$lat}_{$lng}_{$distance}_{$name}_{$city}_{$page}";
			$region_id = $this->_db->fetchOne("select region_id from `oto_region` where `region_name` = '{$name}' and `city` = '{$city}' limit 1");
		} else {
			$key = "get_api_market_by_search_{$lat}_{$lng}_{$distance}_{$region_id}_{$city}_{$page}";
		}
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($distance) { // 根据周边距离 查询商场
				$sql = "SELECT market_id, market_name, market_address, logo_img,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market
						where `city` = '{$city}'
						HAVING distance <= '{$distance}'
						ORDER BY distance asc, sequence desc";
			} else {  // 根据行政区 查询商场
				if ($lat && $lng) {
					$lngLatArray = $this->germanToBaidu($lng, $lat);
					$lng = $lngLatArray['lng'];
					$lat = $lngLatArray['lat'];
					$sql = "SELECT market_id, market_name, market_address, logo_img,
							12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
							FROM oto_market
							WHERE `region_id` = '{$region_id}' and `city` = '{$city}'
							ORDER BY sequence desc, distance asc";					
				} else {
					$sql = "SELECT market_id, market_name, market_address, logo_img FROM oto_market 
							WHERE `region_id` = '{$region_id}' and `city` = '{$city}' 
							ORDER BY sequence desc";
				}
			}
			$marketlist = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($marketlist as &$row) {
				if (!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*) FROM oto_shop os LEFT JOIN oto_market om ON os.market_id = om.market_id WHERE os.shop_status <> -1 AND os.shop_pid = 0 AND om.market_id = '{$row['market_id']}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
				$row['has_ticket'] = $this->hasTicketByMarket($row['market_id'], $city);
			}
			$data = $this->returnArr(count($marketlist), $marketlist);
			$this->setData($key, $data);
		}
		
		return $data;
	}
	
	
	public function getMarketView($market_id, $uid, $city) {
		$key = "get_api_market_view_{$market_id}_{$city}";
		$data = $this->getData($key);
		if (empty($data)) {
			$marketRow = $this->select("`market_id` = {$market_id} and `city` = '{$city}'", "oto_market", '*', '', true);
			if ($marketRow['head_img']) {
				$marketRow['head_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $marketRow['head_img'];
			}
			
			if ($marketRow['logo_img']) {
				$marketRow['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $marketRow['logo_img'];
			}
						
			$sql = "SELECT os.shop_id, os.shop_name, ob.brand_icon, ob.brand_name_zh, ob.brand_name_en 
					FROM oto_shop os 
					LEFT JOIN oto_brand ob ON os.brand_id = ob.brand_id
					WHERE os.market_id = '{$market_id}'  
					AND os.shop_status <> '-1' AND os.shop_pid = '0'
					AND os.city = '{$city}' AND ob.city = '{$city}'
					order by ob.sequence asc, ob.created desc";
			$shop_brand = $this->_db->fetchAll($sql);
			foreach ($shop_brand as &$row) {
				$row['brand_icon'] = $row['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'] : '';
				$row['brand_name_zh'] = $row['brand_name_zh'] ? $row['brand_name_zh'] : '';
				$row['brand_name_en'] = $row['brand_name_en'] ? $row['brand_name_en'] : '';
			}
			$marketRow['shopInfo'] = $shop_brand;
			
			/** 获取次商场下的优惠券
			$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, app_price, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . "
						AND market_id = '{$market_id}' and city = '{$city}' AND ticket_type = '{$ticket_type}' 
						ORDER BY created desc";
			$tickeInfo = $this->_db->fetchAll($c_sql);
			foreach ($tickeInfo as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
				
			}
			*/
			
			/**
			 * 获取次商场下的优惠券
			 * 1.根据商场ID获取店铺ID
			 * 2.根据店铺ID获取关联券ID
			 */
			$sql_tids = "select a.ticket_id from oto_ticket_shop a left join oto_shop b on a.shop_id = b.shop_id where b.market_id = '{$market_id}'";
			$tidArrs = $this->_db->fetchCol($sql_tids);
			$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
			$c_sql = "SELECT ticket_id, ticket_uuid, ticket_title, ticket_type, user_name, shop_id, shop_name, region_id, circle_id, par_value, selling_price, start_time, app_price, end_time, valid_stime, valid_etime,ticket_summary, content, ticket_status, total, has_led, cover_img,is_auth,created
						FROM oto_ticket
						WHERE " . $this->couponWhereSql() . " AND " . $this->db_create_in($tidArrs, 'ticket_id') . "
						AND CITY = '{$city}' AND TICKET_TYPE = '{$ticket_type}' AND TICKET_CLASS = 1
						ORDER BY created desc";
			$tickeInfo = $this->_db->fetchAll($c_sql);
			foreach ($tickeInfo as &$row) {
				$row['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/'.$row['cover_img'];
				$row['selling_price'] = !empty($row['app_price']) ? $row['app_price'] : $row['selling_price'];
				$row['app_price'] = ($row['app_price'] ? $row['app_price'] : $row['selling_price']) * 100;
			}
			
			$marketRow['ticketInfo'] = $tickeInfo;
			$data = $marketRow;
			unset($marketRow);
			$this->setData($key, $data);
		}
		//商场收藏数
		$data['favorite_number'] = Model_Api_App::getInstance()->getFavoriteNum('oto_market_favorite', $market_id);
		//当前用户是否已收藏
		if ($uid) {
			$data['is_fav'] = intval($this->isMarketFav($uid, $market_id, 'oto_market_favorite'));
		}
		$data = $this->returnArr(1, $data);
		
								
		return $data;
	}
	
	// 商场是否收藏
	public function isMarketFav($uid, $market_id, $table) {
		return $this->_db->fetchOne("select 1 from " . $table ." where market_id = '{$market_id}' and user_id = '{$uid}' limit 1") == 1;
	}
	
	// 添加商场收藏
	public function addMarketFav($market_id, $uid, $ip) {
		$ip = !$ip ? CLIENT_IP : $ip;
		$result = $this->_db->replace('oto_market_favorite', array('user_id' => $uid, 'market_id' => $market_id, 'created' => REQUEST_TIME));
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_market_favorite` where `market_id` = '{$market_id}'");
			$this->_db->update('oto_market', array('favorite_number' => $num), "market_id = '{$market_id}'");
			$this->updateUser($ip, $uid);
			//同步到oto_user_dynamic
			$marketName = $this->_db->fetchOne("SELECT `market_name` FROM `oto_market` WHERE `market_id`='{$market_id}'");
			$this->syncFavoriteDynamic(array('user_id' => $uid, 'from_id' => $market_id, 'summary' => $marketName,'type'=>3, 'favorite_id'=>$result,'created' => REQUEST_TIME));
			return $this->returnArr(1, array(), 100, '商场收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '商场收藏失败');
		}
	}
	
	// 取消商场收藏
	public function delFavMarket($mid, $uid, $ip) {
		$ip = !$ip ? CLIENT_IP : $ip;
		$sql = "delete from oto_market_favorite where user_id = '{$uid}' and market_id = '{$mid}'";
		$result = $this->_db->query($sql);
		if($result) {
			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_market_favorite` where `market_id` = '{$mid}'");
			$this->_db->update('oto_market', array('favorite_number' => $num), "market_id = '{$mid}'");
			$this->updateUser($ip, $uid);
			//删除oto_user_dynamic中相应的记录
			$this->removeFavoriteDynamic($uid,$mid,3);
			return $this->returnArr(1, array(), 100, '取消收藏成功');
		} else {
			return $this->returnArr(0, array(), 300, '取消收藏失败');
		}		
	}
	
	// 我收藏的商场列表
	public function favMarketList($uid, $postData, $city, $page, $pagesize = PAGESIZE) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];		
		$key = "get_fav_market_list_{$uid}_{$lat}_{$lng}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT om.market_id, om.market_name, om.market_address, om.logo_img, 
						12756274*asin(Sqrt(power(sin(({$lat}-om.lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(om.lat*0.0174533)*power(sin(({$lng}-om.lng)*0.008726646),2))) as distance
						FROM oto_market_favorite of 
						LEFT JOIN oto_market om ON om.market_id = of.market_id
						WHERE of.user_id = '{$uid}' and om.city = '{$city}'
						ORDER BY om.sequence asc, distance asc";
			} else {
				$sql = "SELECT om.market_id, om.market_name, om.market_address, om.logo_img 
						from oto_market_favorite of 
						left join oto_market om ON om.market_id = of.market_id
						WHERE of.user_id = '{$uid}' and om.city = '{$city}'
						ORDER BY om.sequence asc, om.market_id desc";
			}
			$marketlist = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($marketlist as &$row) {
				if (!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*) 
								 FROM oto_shop os 
								 LEFT JOIN oto_market om ON os.market_id = om.market_id 
								 WHERE os.shop_status <> '-1' AND os.shop_pid = '0' 
								 AND om.market_id = '{$row['market_id']}'
								 AND om.city = '{$city}' and os.city = '{$city}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
			}
			$data = $this->returnArr(count($marketlist), $marketlist);
			$this->setData($key, $data);
		}
		return $data;
		
	}
	
	// 品牌列表页 (按照品牌分类排序)
	public function getBrandStoreList($city) {
		$key = "get_api_brand_store_list_{$city}";
		$data = $this->getData($key);
		if(empty($data)) {
			$storeArray = array();
			$recom_small = Model_Home_Index::getInstance()->getRecommendListByIdentifier('brand_recom_small', $city, 8);
			foreach ($recom_small as &$row_s) {
				$row_s['has_ticket'] = !empty($row_s['come_from_id']) ? $this->hasTicketByBrand($row_s['come_from_id'], $city) : '';
				$row_s['has_tuan'] =  !empty($row_s['come_from_id']) ? $this->hasTuanByBrand($row_s['come_from_id']) : '';
			}
			
			$recom_big = Model_Home_Index::getInstance()->getRecommendListByIdentifier('brand_recom_big', $city, 4);
			foreach ($recom_big as &$row_b) {
				$row_b['has_ticket'] = !empty($row_b['come_from_id']) ? $this->hasTicketByBrand($row_b['come_from_id'], $city) : '';
				$row_b['has_tuan'] = !empty($row_b['come_from_id']) ? $this->hasTuanByBrand($row_b['come_from_id']) : '';
			}
			
			$store = $this->getStore(0, true, false, $city);
			foreach ($store as $store_id => $store_name) {
				$storeArray[] = array(
							'store_id' => $store_id,
							'store_name' => $store_name,
							'brand' => $this->_db->fetchAll("select brand_id, brand_name_zh, brand_name_en from oto_brand where store_id = '{$store_id}' order by sequence asc limit 3")
						);
			}
			$data = $this->returnArr(count($storeArray), $storeArray);
			$data['recom_small'] = $recom_small;
			$data['recom_big'] = $recom_big;
			$this->setData($key, $data);
		}

		return $data;
	}
	
	public function getBrandByStore($store_id, $postData, $city, $page, $pagesize = PAGESIZE) {
		$key = "get_api_brand_by_store_{$store_id}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			$sql = "select * from oto_brand 
					where store_id = '{$store_id}' and city = '{$city}' 
					order by `sequence` asc, `favorite_number` desc, `created` desc";
			$brands = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($brands as &$row) {
				if (!empty($row['brand_icon'])) {
					$row['brand_icon'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row['brand_icon'];
				}
	
				$row['has_ticket'] = $this->hasTicketByBrand($row['brand_id'], $city);
				$row['has_tuan'] = $this->hasTuanByBrand($row['brand_id']);
				
			}
			$data = $this->returnArr(count($brands), $brands);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	// 新版商场首页
	public function getMarketIndex($postData, $city, $limit = PAGESIZE) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$key = "get_api_market_index_{$lat}_{$lng}_{$city}";
		$data = $this->getData($key);
		if(empty($data)) {
			$marketIndex = array();
			// 1.后台推荐商场头图
			$recom_img = Model_Home_Index::getInstance()->getRecommendListByIdentifier('market_recom', $city, 3);
			$marketIndex['recom_img'] = $recom_img;
			// 2.热门商区
			$sql_hot_circle = "select circle_id, circle_name from oto_circle where `is_hot` = '1' and `city` = '{$city}' order by `sequence` limit 9";
			$hot_circle = $this->_db->fetchAll($sql_hot_circle);
			$marketIndex['hot_circle'] = $hot_circle;
			// 3.推荐商场
			if ($lat && $lng) {
				$lngLatArray = $this->germanToBaidu($lng, $lat);
				$lng = $lngLatArray['lng'];
				$lat = $lngLatArray['lat'];
				$sql = "SELECT market_id, market_name, market_address, logo_img,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market
						where is_show = 1 and city = '{$city}'
						ORDER BY sequence asc, distance asc, market_id desc
						limit {$limit}";
			} else {
				$sql = "SELECT market_id, market_name, market_address, logo_img 
						FROM oto_market 
						where is_show = 1 and city = '{$city}'
						ORDER BY sequence asc, market_id desc
						limit {$limit}";
			}
			$market_recom = $this->_db->fetchAll($sql);
			foreach ($market_recom as &$row) {
				if (!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*) 
								FROM oto_shop os 
								LEFT JOIN oto_market om ON os.market_id = om.market_id 
								WHERE os.shop_status <> '-1' AND os.shop_pid = '0' AND om.market_id = '{$row['market_id']}'
								and os.city = '{$city}' and om.city = '{$city}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
				$row['has_ticket'] = $this->hasTicketByMarket($row['market_id'], $city);
			}
			
			$marketIndex['market_recom'] = $market_recom;
			$data = $this->returnArr(1, $marketIndex);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getMarketByCircle($circle_id, $postData, $city, $page, $pagesize = PAGESIZE) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$key = "get_api_market_by_circle_{$circle_id}_{$lat}_{$lng}_{$city}_{$page}";
		$data = $this->getData($key);
		if (empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT market_id, market_name, market_address, logo_img,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market
						where circle_id = '{$circle_id}' and city = '{$city}'
						ORDER BY sequence asc, distance asc, market_id desc";
			} else {
				$sql = "SELECT market_id, market_name, market_address, logo_img 
						FROM oto_market 
						where circle_id = '{$circle_id}' and city = '{$city}' 
						ORDER BY sequence asc, market_id desc";
			}
			$market_list = $this->_db->limitQuery($sql, $start, $pagesize);
			foreach ($market_list as &$row) {
				if (!empty($row['logo_img'])) {
					$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
				}
				$sql_shop_num = "SELECT COUNT(*) 
								FROM oto_shop os 
								LEFT JOIN oto_market om ON os.market_id = om.market_id 
								WHERE os.shop_status <> -1 AND os.shop_pid = 0 AND om.market_id = '{$row['market_id']}'
								and os.city = '{$city}' and om.city = '{$city}'";
				$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
				$row['has_ticket'] = $this->hasTicketByMarket($row['market_id'], $city);
			}
			$data = $this->returnArr(count($market_list), $market_list);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getAllRecomMarket($postData, $city, $page, $pagesize = PAGESIZE) {
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		$key = "get_api_all_recom_market_{$lat}_{$lng}_{$city}_{$page}";
		$data = $this->getData($key);
		if(empty($data)) {
			$start = ($page - 1) * $pagesize;
			if ($lat && $lng) {
				$sql = "SELECT market_id, market_name, market_address, logo_img,
						12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as distance
						FROM oto_market
						where is_show = '1' and city = '{$city}'
						ORDER BY sequence asc, distance asc, market_id desc";
			} else {
				$sql = "SELECT market_id, market_name, market_address, logo_img 
						FROM oto_market 
						where is_show = '1' and city = '{$city}'
						ORDER BY sequence asc, market_id desc";
				}
				$market_list = $this->_db->limitQuery($sql, $start, $pagesize);
				foreach ($market_list as &$row) {
					if (!empty($row['logo_img'])) {
						$row['logo_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row['logo_img'];
					}
					$sql_shop_num = "SELECT COUNT(*) 
									FROM oto_shop os 
									LEFT JOIN oto_market om ON os.market_id = om.market_id 
									WHERE os.shop_status <> '-1' AND os.shop_pid = '0' AND om.market_id = '{$row['market_id']}'
									and os.city = '{$city}' and om.city = '{$city}'";
					$row['shop_num'] = $this->_db->fetchOne($sql_shop_num);
					$row['has_ticket'] = $this->hasTicketByMarket($row['market_id'], $city);
				}
				$data = $this->returnArr(count($market_list), $market_list);
				$this->setData($key, $data);
		}
		return $data;
	}
	
	// 合并优惠券查询条件
	public function couponWhereSql() {
		$couponSql = " `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1 AND `end_time` > '" . REQUEST_TIME . "' AND `start_time` < '" . REQUEST_TIME . "'";
		return $couponSql;
	}
	
	// 获取城市
	public function getCity($city) {
		$city = !$city ? 'sh' : $city;
		return $city;
	}
	/**
	 * 订单管理
	 * @param unknown_type $postData
	 */
	public function orderManagement($postData) {
		//订单编号
		$OrderNo = $postData['OrderNo'];
		//UUID
		$uuid = $postData['uuid'];
		//字符类型
		$AppType = $postData['AppType'];
		
		switch ($postData['type']) {
			//退款
			case 'Refund':
				$data = Custom_AuthTicket::orderProcessing($uuid, $OrderNo, $postData['type']);
				break;
			//取消订单
			case 'CancelOrder':
				$data = Custom_AuthTicket::orderProcessing($uuid, $OrderNo, $postData['type']);
				break;
			//继续支付
			case 'ContinueToPay':
				$data = Custom_AuthTicket::orderProcessing($uuid, $OrderNo, $postData['type'], $AppType);
				break;
		}
		
		$data = $this->returnArr(1, $data);
		return $data;
	}
}