<?php 
class Model_Home_Suser extends Base
{
	private static $_instance;
	private $_table = '';
	private $_where = '';
	private $_url = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function setWhere($condition = '') {
		$this->_url = '/home/suser/my-good';
		if($condition) {
			if(isset($condition['auth']) && !empty($condition['auth'])) {
				$this->_url .= '/auth/' . intval($condition['auth']);
				switch ($condition['auth']) {
					case 1:
						$this->_where .= " and `is_auth` = '0'";
						break;
					case 2:
						$this->_where .= " and `is_auth` = '1'";
						break;
					case 3:
						$this->_where .= " and `is_auth` = '-1'";
						break;
				}					
			}
			
			if(isset($condition['gname']) && !empty($condition['gname'])) {
				$gname = Custom_String::HtmlReplace(urldecode($condition['gname']),2);
				$this->_where .= " and `good_name` like '%{$gname}%'";
				$this->_url .= '/gname/' . stripslashes($gname);
			}
			
			if(isset($condition['shop_id']) && !empty($condition['shop_id'])) {
				$sid = intval($condition['shop_id']);
				$this->_where .= " and `shop_id` = '{$sid}'";
				$this->_url .= '/sid/' . $sid;
			}
			
		}
		$this->_where .= "  and `good_status` <> '-1' and `is_del` = '0'";
	}
	
	public function getMyGoodList($user_id, $shop_id, $page, $pagesize = 20) {
		$snapArray = $data = array();
		
		$sqlC = "select count(good_id) from `oto_good` where 1 {$this->_where}";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
		
		$sql = "select `good_id`, `good_name`, `user_name`, `dis_price`, `created`, `is_auth`, `favorite_number`, `concerned_number` 
				from `oto_good` 
				where 1 {$this->_where} 
				order by `created` desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		$snapArray['data'] = $data;
		
		$snapArray['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, $this->_url);
		
		return $snapArray;
	}
	
	public function getBuyGoodList($getData) {
		$snapArray = $data = array();
		$pagesize = $getData['pagesize'];
		$page = $getData['page'];
		$shop_id = $getData['shop_id'];
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'buygood');
		$where = "`ticket_type` = '{$ticket_type}' and `shop_id` = '{$shop_id}'";
		$sqlC = "select count(ticket_id) from `oto_ticket` where {$where}";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
	
		$sql = "select *
				from `oto_ticket`
				where {$where}
				order by `sequence` asc, `created` desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		foreach($data as & $row) {
			$row['is_online'] = true;
			if($row['end_time'] < REQUEST_TIME) {
				$row['is_online'] = false;
			}
			if($row['start_time'] > REQUEST_TIME) {
				$row['apply_status'] = '0';
			} elseif ($row['start_time'] < REQUEST_TIME && $row['end_time'] > REQUEST_TIME) {
				$row['apply_status'] = '1';
			} elseif ($row['end_time'] < REQUEST_TIME) {
				$row['apply_status'] = '-1';
			}			
		}
		$snapArray['data'] = $data;
	
		$snapArray['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, '/home/suser/buy-good');
	
		return $snapArray;
	}
	
	public function getSoldOrderList($getData) {
		$snapArray = $data = array();
		$pagesize = $getData['pagesize'];
		$page = $getData['page'];
		$shop_id = $getData['shop_id'];
		//pr($getData);
		$data = array();
		
		$url = '/home/suser/sold-orders/sid/' . $shop_id;
		
		$orderData = array(
					'IsTuan' => 1,
					'MerchantCommonId' => $shop_id,		
				);
		//判断刷选时间
		if($getData['startDate'] && $getData['overDate'] && $getData['overDate'] >= $getData['startDate']) {
			$orderData = array_merge(array(
						'OrderStartTime' => $getData['startDate'] . ' 00:00:00',
						'OrderEndTime' => $getData['overDate'] . ' 23:59:59'
					), $orderData);
			
			$url .= '/startDate/' . $getData['startDate'] . '/overDate/' .  $getData['overDate'];
		}
		//订单状态
		if($getData['orderStatus']) {
			switch ($getData['orderStatus']) {
				case 1://已取消
					$orderData = array_merge(array('OrderStatus' => 0), $orderData);
					$url .= '/orderStatus/' . $getData['orderStatus'];
					break;
				case 2://等待付款
					$orderData = array_merge(array('OrderStatus' => 1), $orderData);
					$url .= '/orderStatus/' . $getData['orderStatus'];
					break;
				case 3://完成支付（已付款）
					$orderData = array_merge(array('OrderStatus' => 2), $orderData);
					$url .= '/orderStatus/' . $getData['orderStatus'];
					break;
				case 4://申请退款
					$orderData = array_merge(array('OrderStatus' => 4), $orderData);
					$url .= '/orderStatus/' . $getData['orderStatus'];
					break;
			}
		}
		//提货方式
		if($getData['receiveType']) {
			switch ($getData['receiveType']) {
				case 1://快递送货
					$orderData = array_merge(array('ReceiptStatus' => 1), $orderData);
					$url .= '/receiveType/' . $getData['receiveType'];
					break;
				case 2://到店自提
					$orderData = array_merge(array('ReceiptStatus' => 0), $orderData);
					$url .= '/receiveType/' . $getData['receiveType'];
					break;
			}
			
		}
		//订单号
		if($getData['orderNum']) {
			$orderData = array_merge(array('OrderNo' => $getData['orderNum']), $orderData);
			$url .= '/orderNum/' . $getData['orderNum'];
		}
		//商品名称
		if($getData['productName']) {
			$orderData = array_merge(array('ProductName' => $getData['productName']), $orderData);
			$url .= '/productName/' . $getData['productName'];
		}
		//手机号码
		if($getData['mobile']) {
			$orderData = array_merge(array('Mobile' => $getData['mobile']), $orderData);
			$url .= '/mobile/' . $getData['mobile'];
		}
				
		$dataResult = Custom_AuthSku::getOrderList($orderData, $page, $pagesize);
		if($dataResult['code'] == 1) {
			$data = $dataResult['message'];
		
		
			//翻页最大边界值判断
			$maxPage = ceil($data['Paging']['RecCount'] / $pagesize);
			if($page > $maxPage) $page = $maxPage;
			$snapArray['totalNum'] = $data['Paging']['RecCount'];
			
			foreach($data['Result'] as & $orderItem) {
				$orderItem['Order']['OrderTime'] = datex(strtotime($orderItem['Order']['OrderTime']), 'Y-m-d H:i:s');
			}
			
			$snapArray['data'] = $data['Result'];
			
			$snapArray['pagestr'] = Custom_Page::get($data['Paging']['RecCount'], $pagesize, $page, $url);
			
			return $snapArray;	
		}	
	}
	
	public function getValidRecordList($getData, $shop_id, $page, $pagesize = 20) {
		$snapArray = $data = array();
		$where = $url = '';
		//搜索条件
		if($getData) {				
			if(!empty($getData['sdate']) && !empty($getData['edate'])) {
				$sdate = strtotime($getData['sdate']);
				$edate = strtotime($getData['edate']);
				$where = " and `verify_time` >= '{$sdate}' and `verify_time` <= '{$edate}'";				
				$url .= '/sdate/' . $sdate . '/edate/' . $edate;
			}
				
			if(isset($condition['title']) && !empty($getData['title'])) {
				$title = Custom_String::HtmlReplace(trim($getData['title']));
				$where .= " and `ticket_title` like '%{$title}%'";
				$url .= '/title/' . $title;
			}

			if(isset($condition['vid']) && !empty($getData['vid'])) {
				$vid = intval($getData['vid']);
				$where .= " and `verify_shop_id` = '{$vid}'";
				$url .= '/vid/' . $vid;
			}
		}
		
		$sqlC = "select count(id) from `oto_ticket_verify` where `owner_shop_id` = '{$shop_id}' {$where}";
		$totalNum = $this->_db->fetchOne($sqlC);
		
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
	
		$sql = "select * from `oto_ticket_verify` where `owner_shop_id` = '{$shop_id}' {$this->_where} order by `verify_time` desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		$snapArray['data'] = $data;
	
		$snapArray['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, $url);
	
		return $snapArray;
	}
	
	public function getValidShopList($shop_id) {
		$shopArray = array();
		$sql = "select verify_shop_id, verify_shop_name from oto_ticket_verify where `owner_shop_id` = '{$shop_id}' group by verify_shop_id";
		$shopArray = $this->_db->fetchAll($sql);
		return $shopArray;
	}
	
	public function getMyCouponList($user_id, $shop_id, $page, $pagesize = 20) {
		$snapArray = $data = array();
		$buy_good_ticket_type = $this->getTicketSortById(0, 'ticketsort', 'buygood');
		$where = " and `ticket_type` <> '{$buy_good_ticket_type}'";
		$sqlC = "select count(ticket_id) from `oto_ticket` where `shop_id` = '{$shop_id}' {$where}";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
		
		$sql = "select `ticket_id`, `ticket_type`, `ticket_title`, `par_value`, `user_id`, `user_name`, `reason`,
					   `created`, `start_time`, `end_time`, `valid_stime`, `valid_etime`, `ticket_status`, `has_led`, `is_auth` 
				from `oto_ticket`
				where `shop_id` = '{$shop_id}' {$where}
				order by `created` desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		$ticketsort = $this->getTicketSortById(0, 'ticketsort');
		foreach($data as & $row) {
			$row['ticket_type_name'] = $ticketsort[$row['ticket_type']]['sort_detail_name'];
			$row['ticket_type_mark'] = $ticketsort[$row['ticket_type']]['sort_detail_mark'];
			$row['is_online'] = true;
			if($row['end_time'] < REQUEST_TIME) {
				$row['is_online'] = false;
			}
		}
		$snapArray['data'] = $data;
		
		$snapArray['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, '/home/suser/coupon-list');
		
		return $snapArray;		
	}
	
	public function getTicketRow($ticket_id, $shop_id) {
		$ticketRow = $this->select(
				"`ticket_id` = '{$ticket_id}' and `shop_id` = '{$shop_id}'",
				'oto_ticket',
				'ticket_title,ticket_type, start_time, end_time, total, cover_img',
				'',
				true
			);
		
		$ticketRow['sort_detail_name'] = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort');
		$gidArray = $this->getTicketGood($ticket_id);
		if(is_array($gidArray)) {
			$ticketRow['gids'] = implode(',', $gidArray);
		}
		return $ticketRow;
	}
	
	public function getTicketGood($ticket_id) {
		return $this->_db->fetchCol("select good_id from `oto_ticket_good` where `ticket_id` = '{$ticket_id}'");
	}
	
	public function getShopIdListByUserId($user_id) {
		return $this->select("`user_id` = '{$user_id}' and `shop_pid` = '0'  and `shop_status` <> '-1'", 'oto_shop', 'shop_id,shop_name,is_flag','sequence desc, shop_id asc');
	}
	/**
	 * 根据用户ID获取操作权限
	 * @param unknown_type $user_id
	 */
	public function getPermissionShopByUserId($user_id, $city = 'sh') {
		$sql = "select USR.shop_id, USR.competence, SHP.shop_name, SHP.is_flag
				from `oto_user_shop_competence` USR
				left join `oto_shop` SHP on USR.shop_id = SHP.shop_id
				where USR.user_id = '{$user_id}' and USR.city = '{$city}'
				order by SHP.sequence asc, SHP.shop_id asc";
		return $this->_db->fetchAssoc($sql);	
	}
	
	public function goodDel($good_id) {
		return $this->_db->update('oto_good', array('good_status' => '-1'), "`good_id` = '{$good_id}'");
	}
	
	public function goodAuth($good_id) {
		return $this->_db->update('oto_good', array('is_auth' => '1'), "`good_id` = '{$good_id}' and `is_auth` = '0'");
	}
	
	public function goodAuthNo($good_id) {
		return $this->_db->update('oto_good', array('is_auth' => '-1'), "`good_id` = '{$good_id}' and `is_auth` = '0'");
	}

	public function ticketOff($ticket_id) {
		//同步券
		$ticketRow = $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket', '*', '', true);
		$mark = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
				
		$syncResult = Model_Admin_Ticket::getInstance()->syncAudit($mark, $ticket_id, $this->_city);
		if($syncResult) {
			return $this->_db->update('oto_ticket', array('is_auth' => '0'), "`ticket_id` = '{$ticket_id}' and `is_auth` = '1'");
		}
	}
	
	public function uniqueBrandName($brand_name) {
		$posf = strpos($brand_name, '[');
		if($posf !== false) {
			$brand_name_zh = substr($brand_name, 0, $posf);
			$brand_name_en = substr($brand_name, $posf + 1, -1);
			$sql = "select 1 from `oto_brand` where `brand_name_zh` = '{$brand_name_zh}' and `brand_name_en` = '{$brand_name_en}' limit 1";
		} else {
			$sql = "select 1 from `oto_brand` where `brand_name_zh` = '{$brand_name}' or `brand_name_en` = '{$brand_name}' limit 1";
		}
		return $this->_db->fetchOne($sql) == 1;
	}
	
	public function addShop($getData, & $userInfo) {
		$sname = Custom_String::HtmlReplace($getData['sname'], 1);
		$rid = intval($getData['rid']);
		$cid = intval($getData['cid']);
		$ad = Custom_String::HtmlReplace($getData['ad'], 1);
		$bname = Custom_String::HtmlReplace($getData['bname'], 2);
		$stid = intval($getData['stid']);
		$not = Custom_String::HtmlReplace($getData['not']);		
		$bid = Model_Admin_Shop::getInstance()->getBrandId($bname);
		
		$lng = $lat = 0;
		$lngLatString = $this->getLatitudeAndLongitudeFormamap($ad);
		list($lng, $lat) = explode(',', $lngLatString);
		
		$param = array(
					'store_id' => $stid,
					'shop_name' => $sname,
					'region_id' => $rid,
					'circle_id' => $cid,
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
					'shop_address' => $ad,
					'brand_id' => $bid,
					'brand_name' => $bname,
					'lng' => $lng,
					'lat' => $lat,
					'created' => REQUEST_TIME
				);
		$insert_shop_id = $this->_db->insert('oto_shop', $param);
		if($insert_shop_id) {
			return $insert_shop_id;
		}
		return false;	
	}
	
	public function editGood($getData) {
		$img = trim($getData['img'], ',');
		$good_name = trim($getData['good_name']);
		$org_price = trim($getData['list_price']);
		$dis_price = trim($getData['dis_price']);
		$shop_id = intval($getData['sid']);
		$gid = intval($getData['gid']);
		$shopRow = $this->getShopFieldById($shop_id);
		$param = array(
				'good_name' => $good_name,
				'org_price' => $org_price,
				'dis_price' => $dis_price,
				'brand_id'	=> $shopRow['brand_id'],
				'store_id'  => $shopRow['store_id'],
				'region_id' => $shopRow['region_id'],
				'circle_id' => $shopRow['circle_id'],
				'shop_id' => $shop_id,
				'shop_name' => $shopRow['shop_name'],
				'updated' => REQUEST_TIME
		);
		$result = $this->_db->update('oto_good', $param, "`good_id` = '{$gid}'");
		if($result) {
			$this->_file->del('get_good_view_'. $gid);
			return true;
		}
		return false;
	}
	
	public function editShop($getData, & $userInfo){
		$sid = intval($getData['shop_id']);
		$sname = Custom_String::HtmlReplace($getData['sname'], 1);
		$rid = intval($getData['rid']);
		$cid = intval($getData['cid']);
		$ad = Custom_String::HtmlReplace($getData['ad'], 1);
		$bname = Custom_String::HtmlReplace($getData['bname'], 2);
		$stid = intval($getData['stid']);
		$not = Custom_String::HtmlReplace($getData['not']);		
		$bid = Model_Admin_Shop::getInstance()->getBrandId($bname);
		
		$lng = $lat = 0;
		$lnglat = Core_Http::sendRequest($GLOBALS['GLOBAL_CONF']['Get_Latitude_And_Longitude'], array('city' => '上海', 'address' =>  $ad, 'output' => 'json'));
		$lnglatObject = json_decode($lnglat);
		if(isset($lnglatObject->result->location->lng)) {
			$lng = $lnglatObject->result->location->lng;
		}
		if(isset($lnglatObject->result->location->lat)) {
			$lat = $lnglatObject->result->location->lat;
		}
		$param = array(
				'store_id' => $stid,
				'shop_name' => $sname,
				'region_id' => $rid,
				'circle_id' => $cid,
				'user_id' => $userInfo['user_id'],
				'user_name' => $userInfo['user_name'],
				'shop_address' => $ad,
				'brand_id' => $bid,
				'brand_name' => $bname,
				'notice' => $not, 
				'lng' => $lng,
				'lat' => $lat,
				'updated' => REQUEST_TIME
		);
		if($sid && $sid > 0) {
			//事务开始
			$this->_db->beginTransaction();
			
			//修改店铺
			$shopResult = $this->_db->update('oto_shop', $param, "`shop_id` = '{$sid}'");
			//修改店铺对应商品
			$goodResult = $this->_db->update('oto_good', array(
					'shop_name' => $sname,
					'brand_id'  => $bid,
					'store_id'  => $stid,
					'region_id' => $rid,
					'circle_id' => $cid
			), "`shop_id` = '{$sid}'", false);
			//修改对应券表
			$tickResult = $this->_db->update('oto_ticket', array(
					'brand_id'  => $bid,
					'store_id'  => $stid,
					'shop_name'	=> $sname,
					'region_id' => $rid,
					'circle_id' => $cid
			), "`shop_id` = '{$sid}'", false);
			if($shopResult && $goodResult && $tickResult) {
				//事务确认
				$this->_db->commit();
				return true;
			} else {
				//事务回滚
				$this->_db->rollBack();
			}
		} 
		return false;
	}
	
	public function editShopNotice($getData, & $userInfo) {
		$sid = intval($getData['shop_id']);
		$not = Custom_String::HtmlReplace($getData['not']);
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
	/**
	 * 根据 	分类标识 获取分类ID
	 * @param unknown_type $mark
	 * @return unknown
	 */
	public function getTicketIdByTicketUnique($mark) {
		$sortTicketArray = $this->getTicketSortById(0, 'ticketsort');
		foreach ($sortTicketArray as $key => $item) {
			if($item['sort_detail_mark'] == $mark) {
				return $key;
			}
		}
	}
	/**
	 * 根据用户ID  获取用户历史活动
	 * @param unknown_type $user_id
	 */
	public function getActivityList($user_id) {
		return $this->select("`user_id` = '{$user_id}'", 'oto_activity', 'activity_id, activity_name', 'created desc');
	}
	/**
	 * 根据活动名称 和 用户ID 获取 活动ID
	 * @param unknown_type $activity_name
	 * @param unknown_type $user_id
	 * @return number
	 */
	public function getActivityIdByName($activity_name, $user_id) {
		$activity_id = 0;
		$activityArray = $this->getActivityList($user_id);
		foreach($activityArray as $item) {
			if($item['activity_name'] == $activity_name) {
				$activity_id = $item['activity_id'];
				break;
			}
		}
		
		if(!$activity_id) {
			$activity_id = $this->_db->insert('oto_activity', array('user_id' => $user_id, 'activity_name' => $activity_name, 'created' => REQUEST_TIME));
		}
		return $activity_id;
	}
	/**
	 * 券新增
	 * @param unknown_type $getData
	 * @param unknown_type $userInfo
	 */
	public function addTicket($getData, & $userInfo, $city = 'sh') {
		$shop_id = intval($getData['sid']);
		$ctype = $getData['ctype'];
		$ticket_type = $this->getTicketIdByTicketUnique($ctype);
		$ticket_title = Custom_String::HtmlReplace($getData['t_title'], 1);
		$activity_id = 0;
		$getData['activity_name'] && $activity_name = Custom_String::HtmlReplace($getData['activity_name'], 2);
		if($activity_name) {
			$activity_id = $this->getActivityIdByName($activity_name, $userInfo['user_id']);
		}
		$ticket_summary = Custom_String::HtmlReplace($getData['summary'], 1);
		$shopRow = $this->getShopFieldById($shop_id);
		$par_value = $getData['p_value'];
		$selling_price = $getData['s_value'];
		$start_time = strtotime($getData['sdate']);
		$end_time = strtotime($getData['edate']);
		$valid_stime =  strtotime($getData['stime']);
		$valid_etime =  strtotime($getData['etime']);
		$cover_img = $getData['cover_img'];
		$content = Custom_String::cleanHtml($getData['content']);
		$total = intval($getData['total']);
		$limit_count = $getData['climit'] ? intval($getData['climit']) : 0;
		$limit_unit = $getData['unit'] ? $getData['unit'] : 'Activity'; 
		
		//事务开始
		$this->_db->beginTransaction();
		
		$data = array(
					'ticket_title' => $ticket_title,
					'ticket_type' => $ticket_type,
					'activity_id' => $activity_id,
					'ticket_summary' => $ticket_summary,
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
					'brand_id' => $shopRow['brand_id'],
					'store_id' => $shopRow['store_id'],
					'region_id' => $shopRow['region_id'],
					'circle_id' => $shopRow['circle_id'],
					'shop_id' => $shop_id,
					'shop_name' => $shopRow['shop_name'],
					'par_value' => $par_value,
					'selling_price' => $selling_price,
					'start_time' => $start_time,
					'end_time' => $end_time,
					'valid_stime' => $valid_stime,
					'valid_etime' => $valid_etime,
					'cover_img' => $cover_img,
					'content' => $content,
					'total' => $total,
					'limit_count' => $limit_count,
					'limit_unit' => $limit_unit,
					'is_show' => 1,
					'created' => REQUEST_TIME,
					'city' => $city
				);
		
		//新增券
		$insert_ticket_id = $this->_db->insert('oto_ticket', $data);
		//有图片的话，记录券图片库
		$imgInsertResult = true;
		preg_match_all("/<img.*?src=[\\\'| \\\"](.*?(?:[\.gif|\.jpg|\.png]))[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
		$img_attachment = array();
		if(!empty($matches[1]))
		{
			foreach ($matches[1] as $img_url){
				if(strpos($img_url,$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/740/') !== false){
					$img_attachment[] = $img_url;
				}
			}
			$img_attachment = array_unique($img_attachment);
			$imgCount = count($img_attachment);
			$j = 0;
			if(is_array($img_attachment) && $imgCount > 0) {
				foreach ($img_attachment as $key => $imgUrl) {
					$img_url = str_replace($GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/740/', '', $imgUrl);
					if($this->updateTicketImg($img_url, $insert_ticket_id, $userInfo['user_id'])) {
						$j++;
					}
				}
				
				if($imgCount != $j) {
					$imgInsertResult = false;
				}
			}
		}
		//关联商品
		$goodInsertResult = true;
		$goodArray = explode(',', trim($getData['gids'], ','));
		$goodCount = count($goodArray);
		if(is_array($goodArray) && $goodCount > 0) {
			$k = 0;
			foreach ($goodArray as $good_id) {
				if($this->addTicketGood($insert_ticket_id, $good_id, $start_time, $end_time)) {
					$k++;
				}
			}
			if($goodCount != $k) {
				$goodInsertResult = false;
			}
		}
		
		if($insert_ticket_id && $insert_ticket_id > 1 && $imgInsertResult && $goodInsertResult) {
			//事务确认
			$this->_db->commit();
			return true;
		} else {
			//事务回滚
			$this->_db->rollBack();
			return false;
		}
	}
	/**
	 * 券编辑
	 * @param unknown_type $getData
	 * @param unknown_type $userInfo
	 */
	public function editTicket($getData, & $userInfo) {
		$ticket_id = intval($getData['tid']);
		$shop_id = intval($getData['sid']);
		$total = intval($getData['total']);
		$cover_img = $getData['cover_img'];
		
		$ticketRow = $this->getTicketRow($ticket_id, $shop_id);
		$mark = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
		//事务开始
		$this->_db->beginTransaction();
		
		if($mark == 'voucher') {
			$data = array(
					'total' => $total,
					//'ticket_status' => 0,
					'updated' => REQUEST_TIME
			);
		} else {
			$data = array(
					'total' => $total,
					'updated' => REQUEST_TIME
			);			
		}
		
		if(!empty($cover_img)) {
			$data = array_merge(array('cover_img' => $cover_img), $data);
		}
		//编辑券
		$edit_ticket_result = $this->_db->update(
				'oto_ticket',
				$data,
				array(
						'ticket_id =' => $ticket_id,
						'shop_id' => $shop_id
				)
		);
		//关联商品
		$goodInsertResult = true;
		$goodArray = trim($getData['gids'], ',') ? explode(',', trim($getData['gids'], ',')) : array();		
		$goodCount = count($goodArray);
		if(is_array($goodArray) && $goodCount > 0) {
			//先删除券和商品的关联
			$this->_db->delete('oto_ticket_good', array('ticket_id' => $ticket_id), 0);
			//重新新增关联
			$k = 0;
			foreach ($goodArray as $good_id) {
				if($this->addTicketGood($ticket_id, $good_id, $ticketRow['start_time'], $ticketRow['end_time'])) {
					$k++;
				}
			}
			if($goodCount != $k) {
				$goodInsertResult = false;
			}
		}
		
		//同步券
		Model_Admin_Ticket::getInstance()->syncAudit($mark, $ticket_id, $this->_city);
		
		if($edit_ticket_result && $goodInsertResult) {
			//事务确认
			$this->_db->commit();
			return true;
		} else {
			//事务回滚
			$this->_db->rollBack();
			return false;
		}		
	}
	
	public function updateTicketImg($img_url, $ticket_id, $user_id) {
		return $this->_db->update('oto_ticket_img', 
					array(
						'ticket_id' => $ticket_id
					),
					array(
						'user_id =' => $user_id,
						'img_url =' => $img_url		
					)
				);			
	}
	
	public function addTicketGood($ticket_id, $good_id, $start_time, $end_time) {
		return $this->_db->insert('oto_ticket_good', array(
					'ticket_id' => $ticket_id,
					'good_id' => $good_id,
					'start_time' => $start_time,
					'end_time' => $end_time,
					'created' => REQUEST_TIME
				));
	}
	
	private function getShopListByTicketId($ticket_id) {
		$shopList = array();
		$shopArray = $this->select("`ticket_id` = '{$ticket_id}'" , "oto_ticket_shop", "shop_id");
		foreach($shopArray as $shopItem) {
			$shopList[] = $shopItem['shop_id'];
		}
		return $shopList;
	} 
	/**
	 * 查询优惠券
	 * @param unknown_type $shop_id
	 * @param unknown_type $phone
	 */
	public function searchTicketByPhone($shop_id, $phone) {
		$msgArray = array();

		$sql = "select ticket_id from oto_ticket_detail where `shop_id` = '{$shop_id}' and 
																`phone_number` = '{$phone}' and 
																`valid_stime` < '".REQUEST_TIME."' and 
																`valid_etime` > '".REQUEST_TIME."' and
																`is_use` = '0' limit 1";
		$ticket_id = $this->_db->fetchOne($sql);
		if(!$ticket_id) {
			$msgArray['msg'] = '该手机号码无优惠券可用';
			$msgArray['res'] = 300;			
		}
		
		$shopList = $this->getShopListByTicketId($ticket_id);
		array_push($shopList, $shop_id);
		$shopList = array_unique($shopList);
		
		$ticketArray = $this->select(
					"`phone_number` = '{$phone}' and `valid_stime` < '".REQUEST_TIME."' and `valid_etime` > '".REQUEST_TIME."' and `is_use` = '0' and " . $this->db_create_in($shopList, 'shop_id'), 
					'oto_ticket_detail',
					'detail_id, ticket_id, ticket_title, ticket_uuid, shop_id',
					'`created` desc'
				);

		$msgArray['msg'] = 'success';
		$msgArray['res'] = 100;
		$msgArray['extra'] = $ticketArray;
		
		return $msgArray;
	}
	/**
	 * 查询现金券
	 * @param unknown_type $shop_id
	 * @param unknown_type $captcha
	 */
	public function inquireTicketByCaptcha($shop_id, $captcha) {
		$msgArray = array();
		$ticketArray = Custom_AuthTicket::getTicketVerifyList($shop_id, $captcha);
		if ($ticketArray['code'] == 1) {
			if(count($ticketArray['message']) == 1) {
				if($ticketArray['message'][0]['Status'] == 1) {
					$msgArray['msg'] = '验证码  '.$captcha.' 已验证';
					$msgArray['res'] = 102;
					return $msgArray;
				} elseif($ticketArray['message'][0]['Status'] == -1) {
					$msgArray['msg'] = '验证码  '.$captcha.' 已过期';
					$msgArray['res'] = 103;
					return $msgArray;
				}				
			}
			$backMessage = array();
			foreach($ticketArray['message'] as $messageKey => $messageItem) {
				//根据券ID 获取所有关联店铺
				$sql = "select ticket_id from `oto_ticket` where `ticket_uuid` = '{$messageItem['ProudctId']}' limit 1";
				$ticket_id = $this->_db->fetchOne($sql);
				$shopList = $this->getShopListByTicketId($ticket_id);
				//合并券的创建店铺到关联店铺数组中
				array_push($shopList, $messageItem['MerchantCommonId']);
				$shopList = array_unique($shopList);
				//判断券状态 以及券是否 在关联店铺和主店铺中
				if($messageItem['Status'] == 0 && in_array($shop_id, $shopList)) {
					$backMessage[] = $messageItem;
				}
			}
			
			if(empty($backMessage)) {
				$msgArray['msg'] = '此券不能在本店使用';
				$msgArray['res'] = 104;
				$msgArray['extra'] = $backMessage;
			} else {
				$msgArray['msg'] = 'success';
				$msgArray['res'] = 100;
				$msgArray['extra'] = $backMessage;
			}
		} elseif($ticketArray['code'] == -1) {
			$msgArray['msg'] = '该验证码不存在，请确认！';
			$msgArray['res'] = 101;
		}
		return $msgArray;
	}
	/**
	 * 使用优惠券
	 * @param unknown_type $shop_id
	 * @param unknown_type $phone
	 * @param unknown_type $itemsId
	 * @param unknown_type $itemsUid
	 */
	public function useTicket($shop_id, $phone, $detailIdString, & $userInfo) {
		$ticketModel = $detailIdUsedArray = $ticketUsedArray = $msgArray = array();
		$detailIdString = trim($detailIdString, ',');
		
		$sql = "select ticket_token, detail_id, ticket_id, ticket_title, user_name, shop_id from `oto_ticket_detail` 
				where `detail_id` in ($detailIdString) and `is_use` = '0'
				order by created asc";		
		$ticketDetailArray = $this->_db->fetchAssoc($sql);
		
		foreach ($ticketDetailArray as $ticketDetail) {
			$ticketModel[] = array(
						'MerchantCommonID' => $shop_id,
						'ID' => $ticketDetail['ticket_token'],
						'Verifier' => $ticketDetail['user_name']				
					);
		}		
		$useTicketArray = Custom_AuthTicket::useTicket($ticketModel);		
		if(is_array($useTicketArray)) {
				if($useTicketArray['code'] == 1) {
					while (list($ticket_token, $ticket_status)= each($useTicketArray['message'])) {
						if($ticket_status == 1) {
							//改变券使用状态
							$this->updateTicketUseStatus($ticketDetailArray[$ticket_token]['detail_id']);
							$detailIdUsedArray[] = $ticketDetailArray[$ticket_token]['detail_id'];
							$ticketUsedArray[] = $ticketDetailArray[$ticket_token]['ticket_id'];
							//记录验证记录
							$this->recordVerificationRecords($shop_id, $ticketDetailArray[$ticket_token], $userInfo, $phone, 'coupon');
						}						
					}
				}
			
			//统计券使用状态
			if(!empty($ticketUsedArray)) {
				$ticketUsedUniqueArray = array_unique($ticketUsedArray);
				foreach ($ticketUsedUniqueArray as $ticket_id) {
					$this->updateTicketUsed($ticket_id);
				}
				//_exit('恭喜，选取的优惠券已被使用！', 100, $detailIdUsedArray);
				$msgArray['msg'] = '恭喜，选取的优惠券已被使用！';
				$msgArray['res'] = 100;
				$msgArray['extra'] = $detailIdUsedArray;
			} else {
				//_exit('抱歉，选取的优惠券使用失败！', 300);
				$msgArray['msg'] = '抱歉，选取的优惠券使用失败！';
				$msgArray['res'] = 300;
			}
		} else {
			//_exit('抱歉，选取的优惠券使用失败！', 300);
			$msgArray['msg'] = '抱歉，选取的优惠券使用失败！';
			$msgArray['res'] = 300;
		}
		return $msgArray;
	}
	/**
	 * 记录验证记录
	 * @param unknown_type $shop_id
	 * @param unknown_type $ticketDetailRow
	 */
	private function recordVerificationRecords($shop_id, $ticketDetailRow, &$userInfo, $verify_code_phone, $ticketLogo = 'coupon') {
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique($ticketLogo);
		$param = array(
					'ticket_id' => $ticketDetailRow['ticket_id'],
					'ticket_title' => $ticketDetailRow['ticket_title'],
					'ticket_type' => $ticketType,
					'owner_shop_id' => $ticketDetailRow['shop_id'],
					'owner_shop_name' => $this->getShopName($ticketDetailRow['shop_id']),				
					'verify_shop_id' => $shop_id,
					'verify_shop_name' => $this->getShopName($shop_id),		
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
					'verify_code_phone' => $verify_code_phone,
					'verify_time' => REQUEST_TIME,
					'verify_ip' => CLIENT_IP
				);
		
		$this->_db->insert('oto_ticket_verify', $param);
	}
	/**
	 * 街友获得刮奖机会
	 * @param unknown_type $uuid
	 * @param unknown_type $ticketUsedNum
	 */
	public function taskClientLog($uuid, $ticketUsedNum) {
		$task_start_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']);
		$task_end_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_END_TIME']);
		//判断是否在任务开展时间内
		if(REQUEST_TIME > $task_start_time && REQUEST_TIME < $task_end_time) {
			$taskClientArray = array();
			$userRow = $this->getUserByUuid($uuid);
			for($i = 0; $i < $ticketUsedNum; $i++) {
				$taskClientArray[$i] = array('user_id' => $userRow['user_id'], 'created' => REQUEST_TIME);
			}
			$this->_db->insertBatch('oto_task_client_log', $taskClientArray);
		}
	}

	/**
	 * 营业员 获得刮奖机会
	 * @param unknown_type $ticketUsedNum
	 */
	public function taskClerkLog($user_id, $ticketUsedNum) {
		$task_start_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']);
		$task_end_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_END_TIME']);
		//判断是否在任务开展时间内
		if(REQUEST_TIME > $task_start_time && REQUEST_TIME < $task_end_time) {
			$taskClerkArray = array();
			for($i = 0; $i < $ticketUsedNum; $i++) {
				$taskClerkArray[$i] = array('user_id' => $user_id, 'created' => REQUEST_TIME);
			}
			$this->_db->insertBatch('oto_task_clerk_log', $taskClerkArray);
		}	
	}
	/**
	 * 使用现金券
	 * @param unknown_type $shop_id
	 * @param unknown_type $detailIdString
	 */
	public function vaildVoucherTicket($shop_id, $captcha, $detailIdString, $sidString, $tidString, & $userInfo) {
		$ticketModel = $ticketUsedArray = $msgArray = array();
		$ticketIdArray = array();
		
		$detailIdArray = explode(',', $detailIdString);
		$sidArray = explode(',', $sidString);
		$tidArray = explode(',', $tidString);
		
		if(!empty($detailIdArray)) {
			foreach ($detailIdArray as $key => $id) {
				$ticketModel[] = array(
							'MerchantCommonID' => $sidArray[$key],
							'ID' => $id,
							'Verifier' => $userInfo['user_name']
						);
				$ticketIdArray[$id] = $tidArray[$key];
			}
						
			$validTicketArray = Custom_AuthTicket::vaildVoucherTicket($ticketModel);
			if($validTicketArray['code'] == 1) {
				while (list($ticket_uuid, $ticket_status)= each($validTicketArray['message'])) {
					if($ticket_status == 1) {
						$ticketUsedArray[] = $ticket_uuid;
						//记录验证记录
						$ticketDetailRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($ticketIdArray[$ticket_uuid]);
						$this->recordVerificationRecords($shop_id, $ticketDetailRow, $userInfo, $captcha, 'voucher');
					}
				}
				
				/*
				//>>触发任务奖励				
				if(!empty($ticketUsedArray)) {
					//街友中奖机会发放
					$ticketUuidArray = $ticketUsedArray;
					$ticket_uuid = array_shift($ticketUuidArray);
					$this->taskClientLog($validTicketArray['result'][$ticket_uuid], count($ticketUsedArray));
					//营业员中奖机会发放
					if($userInfo['user_type'] == 3) {
						$this->taskClerkLog($userInfo['user_id'], count($ticketUsedArray));
					}
				}
				*/
			}
			//统计券使用状态
			if(!empty($ticketUsedArray)) {
				//_exit('恭喜，选取的现金券已被验证！', 100, $ticketUsedArray);
				$msgArray['msg'] = '恭喜，选取的现金券已被验证！';
				$msgArray['res'] = 100;
				$msgArray['extra'] = $ticketUsedArray;
				
			} else {
				//_exit('抱歉，选取的现金券验证失败！', 300);
				$msgArray['msg'] = '抱歉，选取的现金券验证失败！';
				$msgArray['res'] = 300;
			}
		} else {
			//_exit('抱歉，选取的现金券验证失败！', 300);
			$msgArray['msg'] = '抱歉，选取的现金券验证失败！';
			$msgArray['res'] = 300;
		}
		return $msgArray;
	}
	/**
	 * 改变优惠券使用状态
	 * @param unknown_type $detailIdArray
	 */
	private function updateTicketUseStatus($detail_id) {
		$this->_db->update('oto_ticket_detail', array('is_use' => 1, 'use_time' => REQUEST_TIME), "`detail_id` = '{$detail_id}'");		
	}
	/**
	 * 改变优惠券使用数量
	 * @param unknown_type $ticket_id
	 */
	private function updateTicketUsed($ticket_id) {
		//当前券被使用数量
		$sqlHasUsed = "select count(detail_id) from oto_ticket_detail where `ticket_id` = '{$ticket_id}' and `is_use` = '1'";
		$hasUsedNum = $this->_db->fetchOne($sqlHasUsed);
		//修改当前券使用数量
		return $this->_db->update('oto_ticket', array('used' => $hasUsedNum), "`ticket_id` = '{$ticket_id}'");
	}	
	/**
	 * 套餐为基础型的情况下 判断 上传商品是否超出
	 * @param unknown_type $shop_id
	 */
	public function merchantGoodsUploadLimit($shop_id, $city='sh') {
		$shopRow = $this->getShopFieldById($shop_id);
		$pack_id = $shopRow['pack_id'];
		$packRow = $this->getPack($pack_id, true, '', $city);
		if($packRow['pack_logo'] == 'basic') {
			$sql = "select count(good_id) from `oto_good` where `shop_id` = '{$shop_id}' and `is_auth` = '1' and `is_del` = '0'";
			$goodNum = $this->_db->fetchOne($sql);
			if($goodNum > $packRow['good_num']) {
				Custom_Common::showMsg('抱歉，你当前店铺可上传的认证商品数已达上限！<br>你可以通过开通套餐的方式来移除限制！');
			}
		} else {
			if( REQUEST_TIME > $shopRow['pack_etime']) {
				$pack_basic_id = $this->getBasicIdByBasicLog('basic', $city);
				$this->_db->update('oto_shop', array('pack_id' => $pack_basic_id), array('shop_id' => $shop_id));
				Custom_Common::showMsg('抱歉，你的' . $packRow['pack_name'] . '已过期，系统已自动降级为基础型套餐');
			}
		}
	}
	/**
	 * 套餐为基础型的情况下 判断 当前在线券是否超出
	 * @param unknown_type $shop_id
	 */
	public function merchantCouponsUploadLimit($shop_id, $city='sh') {
		$shopRow = $this->getShopFieldById($shop_id);
		$pack_id = $shopRow['pack_id'];
		$packRow = $this->getPack($pack_id, true, '', $city);
		if($packRow['pack_logo'] == 'basic') {
			$sql = "select count(ticket_id) from `oto_ticket` 
					where `shop_id` = '{$shop_id}' 
							and `ticket_status` = '1'
							and `is_auth` = '1'
							and `start_time` < '".REQUEST_TIME."'
							and `end_time` > '".REQUEST_TIME."'";
			$goodNum = $this->_db->fetchOne($sql);
			if($goodNum > $packRow['ticket_num']) {
				Custom_Common::showMsg('抱歉，你当前店铺可上传的有效券数已达上限！<br>你可以通过开通套餐的方式来移除限制！');
			}
		} else {
			if( REQUEST_TIME > $shopRow['pack_etime']) {
				$pack_basic_id = $this->getBasicIdByBasicLog('basic', $city);
				$this->_db->update('oto_shop', array('pack_id' => $pack_basic_id), array('shop_id' => $shop_id));
				Custom_Common::showMsg('抱歉，你的' . $packRow['pack_name'] . '已过期，系统已自动降级为基础型套餐');
			}
		}		
	}
	
	private function getBasicIdByBasicLog($pack_logo, $city) {
		$packArray = $this->getPack(0, true, '', $city);
		foreach($packArray as $packItem) {
			if($packItem['pack_logo'] == $pack_logo) {
				return $packItem['pack_id'];
			}
		}
	}
	
	public function getAccountListByShopId($shop_id, $stime, $etime,$page, $pagesize = PAGESIZE) {
		
		$data = array();
		$start_time = strtotime($stime) ? strtotime($stime) : 0;
		$over_time = strtotime($etime) ? strtotime($etime) : 0;
		$accountData = Custom_AuthTicket::getAccountBookList($shop_id, $start_time, $over_time, $page, $pagesize);
		if($accountData['code'] == 1) {
			$data['data'] = $accountData['message']['Results'];
			foreach($data['data'] as & $row) {
				$row['PostTime'] = strtotime($row['PostTime']);
			}
			$data['balance'] = $accountData['message']['TotalBalance'];
			$url = '/home/suser/my-account/sid/' . $shop_id;
			if ($start_time) {
				$url .= '/stime/' . $stime;
			}
			if ($over_time) {
				$url .= '/etime/' . $etime;
			}
			$data['pagestr'] = Custom_Page::get($accountData['message']['Paging']['RecCount'], $pagesize, $page, $url);
		}
		return $data;
	}
	
	public function shopDecorationEdit($getData, $uploadPath, & $userInfo) {
		$param = array(
					'user_id' => $userInfo['user_id'],
					'shop_id' => intval($getData['sid']),
					'pos_id' => intval($getData['pos_id']),
					'detail_title' => Custom_String::HtmlReplace($getData['title']),
					'detail_url' => Custom_String::HtmlReplace($getData['url']),
					'detail_img' => $uploadPath,
					'created' => REQUEST_TIME
				);
		
		if($getData['did']) {
			if(!$uploadPath) unset($param['detail_img']);
			$param = array_merge($param, array('updated' => REQUEST_TIME));
			$this->_db->update('oto_shop_decoration', $param, array('shop_details_id' => intval($getData['did'])));
		} else {
			$this->_db->insert('oto_shop_decoration', $param);
		}
		return true;
	}

	private function setShopDecorationWhere($condition = '') {
		$url = '/home/suser/shop-decoration/sid/' . $condition['shop_id'];
		$where = '';
		if($condition) {
			if(isset($condition['title']) && !empty($condition['title'])) {
				$title = Custom_String::HtmlReplace(urldecode($condition['title']),2);
				$where .= " and `detail_title` like '%{$title}%'";
				$url .= '/title/' . stripslashes($title);
			}
				
			if(isset($condition['pos_id']) && !empty($condition['pos_id'])) {
				$pos_id = intval($condition['pos_id']);
				$where .= " and `pos_id` = '{$pos_id}'";
				$url .= '/pos_id/' . $pos_id;
			}
				
		}
		return array('url' => $url, 'where' => $where);
	}
	
	private function setShopDecorationOrder($condition = '') {
		$order = ' order by `created` desc';
		if($condition) {
			if(isset($condition['pos_id']) && !empty($condition['pos_id'])) {
				$order = ' order by `sequence` asc';
			}
		}
		return $order;
	}
	
	public function getShopDecorationList($user_id, $getData, $page, $pagesize = PAGESIZE, $position) {
		$snapArray = $data = array();
		$positionArray = array();
		foreach ($position as $positionRow) {
			$positionArray[$positionRow['pos_id']] = $positionRow;
		}
		
		$conditionArray = $this->setShopDecorationWhere($getData);
		$order = $this->setShopDecorationOrder($getData);
		
		$sqlC = "select count(shop_details_id) from `oto_shop_decoration` where `shop_id` = '{$getData['shop_id']}' {$conditionArray['where']}";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
	
		$sql = "select * from `oto_shop_decoration` where `shop_id` = '{$getData['shop_id']}' {$conditionArray['where']} {$order}";
		
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		foreach($data as &$dataRow) {
			$dataRow['pos_name'] = $positionArray[$dataRow['pos_id']]['pos_name'];
		}
		$snapArray['data'] = $data;
	
		$snapArray['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, $conditionArray['url']);
	
		return $snapArray;
	}
	
	public function shopDecorationDel($did, $shop_id, $user_id) {
		return $this->_db->delete('oto_shop_decoration', array('shop_details_id' => $did, 'shop_id' => $shop_id, 'user_id' => $user_id));	
	}
	
	public function shopModuleEdit($getData) {
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
		
		$result = $this->_db->update('oto_shop_decoration',array($column => $value), "`shop_details_id` = $id");
		if($result){
			return true;
		}
		return false;		
	}
	
	public function getSkuCategoryList() {
		$key = 'get_sku_category_list_all';
		$data = $this->getData($key);
		if(empty($data)) {
			$data = array();
			$skuCategoryResult = Custom_AuthSku::getCategoryList();
			if($skuCategoryResult['code'] == 1) {			
				$skuCategoryArray = $skuCategoryResult['message'];
				foreach($skuCategoryArray as $skuKey => $skuItem) {
					if($skuItem['CategoryParentId'] == 0) {
						$data[] = $skuItem;
					}
				}
				foreach ($data as $dkey => $dItem) {
					foreach($skuCategoryArray as $skuKey => $skuItem) {
						if($dItem['CategoryId'] == $skuItem['CategoryParentId']) {
							$data[$dkey]['child'][] = $skuItem;
						}
					}
				}
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function getSkuPropList($cid) {
		$key = 'get_sku_prop_list_personal_' . $cid;
		$data = $this->getData($key);
		if(empty($data)) {
			$data = array();
			$skuPropResult = Custom_AuthSku::getPropList($cid);
			if($skuPropResult['code'] == 1) {
				$data = $skuPropResult['message'];
				$this->setData($key, $data);
			}
		}
		return $data;
	}
}