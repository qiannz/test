<?php
class Model_Home_Shop extends Base {
	
	private static $_instance;
	private $_table = '';
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getCoupon($sid) {
		$cacheKey = 'shop_show_coupon_' . $sid;
		$data = $this->getData($cacheKey);
		if (empty($data)){
			$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'voucher');
			$sql_coupon = "select ticket_id,ticket_title,ticket_type,ticket_summary,shop_id,shop_name,par_value,selling_price,start_time,end_time 
							from `oto_ticket` 
							where `shop_id`='{$sid}' and `ticket_type` = '{$ticket_type}' and `ticket_status` = '1' and `is_auth` = '1' and end_time > '" . REQUEST_TIME ."' and start_time < '" . REQUEST_TIME ."'  
							order by created desc";
			$couponInfo = $this->_db->fetchAll($sql_coupon);
			foreach ($couponInfo as $key=>$value) {
				$couponInfo[$key]['shop_id'] = $value['shop_id'];
				$couponInfo[$key]['shop_name'] = $value['shop_name'];
				$couponInfo[$key]['dis_price'] = floor($value['selling_price']);
				$couponInfo[$key]['valid_time'] = date('Y', $value['start_time']).'年'.date('m.d', $value['start_time']).'-'.date('m.d', $value['end_time']).'日';
				$couponInfo[$key]['sort_name'] = '现金券';
			}
			$data['copon_info'] = $couponInfo;
			$data['coupon_num'] = count($couponInfo);
			$data['shop_info'] = $this->select("`shop_id` = '{$sid}'",
							'oto_shop',
							'user_id,shop_name,notice,brand_name,region_id,circle_id,store_id',
							'',
							true
							);
			$this->setData($cacheKey, $data);
		}
		return $data;	
	}
	
	public function getAjaxGoodList($shop_id, $page, $order, $pagesize = 20) {
		//缓存键值
		$cacheKey = 'get_ajax_shop_good_list_' . " {$shop_id}_{$page}_{$order}";
		$data = $this->getData($cacheKey);
	
		if (empty($data)) {
			$snapArray = $snapData = array();
				
			$where = "A.`shop_id` = '{$shop_id}' and A.`good_status` <> '-1' and A.`is_auth` <> '-1' and A.`is_del` = '0'";
			$orderby = '';				
				
			if($order == 1) {
				$orderby = "order by `created` desc";
			} elseif ($order == 2) {
				$orderby = "order by `clicks` desc, `created` desc";
			}
				
			$sqlC = "select count(A.good_id) from `oto_good` A where {$where}";
			$totalNum = $this->_db->fetchOne($sqlC);
	
			$sql = "select
					`good_id`, `good_name`, `shop_id`, `shop_name`, `dis_price`, `favorite_number`, `concerned_number`,
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
			$snapData['data'] = $snapArray;
			$snapData['totalPage'] = ceil($totalNum / $pagesize);
			$data = $snapData;
			unset($snapArray, $snapData);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
	/**
	 * 根据店铺ID获取所有相关推荐
	 * @param unknown_type $shop_id
	 */
	public function getFlagShopAllRecommend($shop_id) {
		//缓存键值
		$cacheKey = 'get_flag_shop_all_recommend_' . $shop_id;
		$data = $this->getData($cacheKey);	
		if (empty($data)) {	
			$snapArray = array();
			$snapArray['shopBackground'] = array_shift($this->getRecommendListByIdentifier($shop_id, 'shop_background', $this->_city, 1));
			$snapArray['shopFigure'] = $this->getRecommendListByIdentifier($shop_id, 'shop_figure', $this->_city, 4);
			$snapArray['shopGood'] = $this->getRecommendListByIdentifier($shop_id, 'shop_good', $this->_city, 16);
			$snapArray['shopActive'] = $this->getRecommendListByIdentifier($shop_id, 'shop_active', $this->_city, 8);
			$snapArray['shopBar'] = array_shift($this->getRecommendListByIdentifier($shop_id, 'shop_bar', $this->_city, 1));
			$data = $snapArray;
			unset($snapArray);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
	/**
	 * 根据店铺ID获取相关推荐列表
	 * @param unknown_type $shop_id
	 * @param unknown_type $identifier
	 * @param unknown_type $limit
	 */
	public function getRecommendListByIdentifier($shop_id, $identifier, $city, $limit = 1) {
		$pos_id = $this->getPosId($identifier, $city);
		$listArray = $this->select(
				"`pos_id` = '{$pos_id}' and `shop_id` = '{$shop_id}'", 
				'oto_shop_decoration',
				'detail_title, detail_url, detail_img',
				'sequence asc, created desc',
				$limit
		);
		if($listArray && $limit == 1) {
			$listTmpArray = array();
			$listTmpArray[] = $listArray;
			$listArray = $listTmpArray;
		}
		return $listArray;
	}
	
	public function setAudit(&$userInfo, $getData) {
		$ty = intval($getData['ty']);
		$user_id = $userInfo['user_id'];
		$user_name = $userInfo['user_name'];
		$shop_id = intval($getData['sid']);
		$full_name = Custom_String::HtmlReplace($getData['full_name']);
		$phone_number = Custom_String::HtmlReplace($getData['phone_number'], 0);
		$id_img = $getData['id_img'];
		$bus_img = $getData['bus_img'];

		if($ty == 1) {
			$shopInfo = $this->select("shop_id = '{$shop_id}'", 'oto_shop', 'shop_name, shop_address', '',true);			
			$arr = array(
						'user_id'      => $userInfo['user_id'],
						'user_name'    => $userInfo['user_name'],
						'full_name'    => $full_name,
						'phone_number' => $phone_number,
						'shop_id'      => $shop_id,
						'shop_name'    => $shopInfo['shop_name'],
						'shop_address' => $shopInfo['shop_address'],
						'id_img'       => $id_img,
						'bus_img'      => $bus_img,
						'created'      => REQUEST_TIME
					);
			return $this->_db->insert('oto_merchant_audit', $arr);
		} elseif($ty == 2) {
			$arr = array(
					'user_type'    => 3,
					'full_name'    => $full_name,
					'phone_number' => $phone_number,
					'id_img'       => $id_img,
					'bus_img'      => $bus_img,
			);
			
			return $this->_db->update('oto_user', $arr, "user_id = '{$userInfo['user_id']}' and user_name = '{$userInfo['user_name']}'");			
		}
	}

	public function unique($type, $user_id, $shop_id = 0) {
		if($type == 1) {
			return $this->_db->fetchOne("select 1 from `oto_merchant_audit` where `audit_status` = '0' and `shop_id` = '{$shop_id}' and `user_id` = '{$user_id}' limit 1") == 1;
		} elseif ($type == 2) {
			return $this->_db->fetchOne("select 1 from `oto_user` where `user_type` = '3' and `user_id` = '{$user_id}' limit 1") == 1;
		}
	}
	
	public function unique_app($shop_id, $user_id = 0) {
		$where = '';
		if($user_id) {
			$where .= " and `user_id` = '{$user_id}'";
		}
		return $this->_db->fetchOne("select 1 from `oto_merchant_app` where `shop_id` = '{$shop_id}' {$where} and `auth_status` <> '-1' limit 1") == 1;
	}
	/**
	 * 判断店铺名称是否重复
	 * @param unknown_type $shop_id
	 */
	public function repeatShop($shop_name, $shop_id = 0, $city='sh') {
		$where = " and `city` = '{$city}'";
		if($shop_id) {
			$where .= " and `shop_id` <> '{$shop_id}'";
		}
		$sql = "select 1 from `oto_shop` where `shop_pid` = '0' and `shop_name` = '{$shop_name}' and `shop_status` <> '-1' {$where} limit 1";
		return $this->_db->fetchOne($sql) == 1;
	}
	/**
	 * 根据品牌ID获取品牌详情
	 * @param unknown_type $brand_Id
	 * @param unknown_type $field
	 * @return unknown
	 */
    public function getBrandById($brand_Id, $field = '*') {
    	$row = $this->select("`brand_id` = '{$brand_Id}'", 'oto_brand', $field, '', true);
    	if($field != '*' && strpos($field, ',') === false) {
    		return $row[$field];
    	}
    	return $row;
    }
    /**
     * 根据店铺ID获取关注该店铺的用户数
     * @param unknown_type $brand_id
     */
    public function getUserNumByShopId($shop_id) {
    	$favoriteUserNum = $this->_db->fetchOne("select count(user_id) from `oto_shop_favorite` where `shop_id` = '{$shop_id}'");
    	return $favoriteUserNum;
    }
    /**
     * 判断某用户是否已关注一个店铺
     * @param unknown_type $brand_id
     * @param unknown_type $user_id
     */
    public function hadFavoriteShop($shop_id, $user_id) {
    	return $this->_db->fetchOne("select 1 from `oto_shop_favorite` where `user_id` = '{$user_id}' and `shop_id` = '{$shop_id}' limit 1") == 1;
    }
    
    public function flagOpen($shop_id, $user_id) {
    	return $this->_db->update('oto_shop', array('is_enable' => 1), array('shop_id' => $shop_id, 'user_id' => $user_id));
    }
    
    /**
     * 获取店铺的券和游惠
     * @param unknown_type $getData
     */
    public function getShopTickets( $getData ){
    	$page = intval($getData["page"])<1?1:intval($getData["page"]);
    	$pageSize = intval($getData["pagesize"])<1?PAGESIZE:intval($getData["pagesize"]);
    	$start = ($page-1)*$pageSize;
    	$shop_id = intval($getData["sid"]);
    	$user_id = intval($getData["uid"]);
    	$time = REQUEST_TIME;
    	$voucher_ticket_type = $this->getTicketSortById(0, 'ticketsort', 'voucher');
    	$selfpay_ticket_type = $this->getTicketSortById(0, 'ticketsort', 'selfpay');
    	$where = " AND `shop_id`='{$shop_id}' AND `ticket_status` = '1' AND `is_auth` = '1' AND `is_show` = 1";
    	$where .= " AND `start_time`<'{$time}' AND `end_time`>'{$time}'";
    	$sql = "SELECT `ticket_id`,`ticket_uuid`,`ticket_title`,`par_value`,`selling_price`,`ticket_summary`,`created` 
    			FROM `oto_ticket` 
    			WHERE `ticket_type` = '{$voucher_ticket_type}' {$where}
    			UNION 
    			SELECT `ticket_id`,`ticket_uuid`,`ticket_title`,`par_value`,`selling_price`,`ticket_summary`,`created`  
    			FROM `oto_ticket` 
    			WHERE `ticket_type` = '{$selfpay_ticket_type}' {$where}
    			ORDER BY `created` DESC
    			LIMIT {$start},{$pageSize}";
    	$data = $this->_db->fetchAll( $sql );
    	$uuid = $this->_db->fetchOne("SELECT `uuid` FROM `oto_user` WHERE `user_id`='{$user_id}'");
    	foreach ($data as &$row){
    		$row["valid_date"] = "券有效期：".datex($row["valid_stime"],"m.d")."日-".datex($row["valid_etime"],"m.d")."日";
    		$fileName = ROOT_PATH . 'web/data/code/ticket/' . $user_id . '_'.$row["ticket_id"].'.png';
    		$content = "http://superbuy.mplife.com/wap/pay/payorder.aspx?token=&stamp={$time}&productID={$row["ticket_uuid"]}&amount=1&app=mpbuy&platform=wap";
    		$row["bcard_url"] = Custom_Image::twoCode($content, $fileName);
    	}
    	return empty($data)?array():$data;
    }
}