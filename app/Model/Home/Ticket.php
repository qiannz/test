<?php 
class Model_Home_Ticket extends Base
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
	
	public function getTicktRow($ticket_id, $is_cache = true) {
		$cacheKey = 'get_ticket_row_' . $ticket_id;
		$data = $this->getData($cacheKey);
		if(!$is_cache || empty($data)) {
			$ticketRow = $this->select(
									"`ticket_id` = '{$ticket_id}'",
									'oto_ticket',
									'*',
									'',
									true
								);
			$ticketRow['ticket_sort'] = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort');
			$ticketRow['ticket_mark'] = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
			$ticketRow['circle_name'] = $ticketRow['circle_id'] ? $this->getCircleByCircleId($ticketRow['circle_id']) : '';
			$ticketRow['wap_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/ticket/wap-show/tid/' . $ticket_id;
			if($ticketRow['ticket_mark'] == 'buygood') {
				$ticketSkuInfo = $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_info', '*', '', true);
				$skuInfoArray = unserialize($ticketSkuInfo['sku_info']);
				$ticketRow['url_small'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $ticketSkuInfo['file_img_small'];
				$ticketRow['url_large'] = $ticketSkuInfo['file_img_large'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $ticketSkuInfo['file_img_large'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_tuan.png';
				$propListArray = Model_Home_Suser::getInstance()->getSkuPropList($skuInfoArray['category_id']);
				$chooseSkuArray = array();
				foreach ($propListArray as $propListKey => $propListItem) {
					foreach($skuInfoArray['Prop'][$propListItem['PropId']] as $valueItem) {
						$chooseSkuArray[$propListKey]['PropId'] = $propListItem['PropId'];
						$chooseSkuArray[$propListKey]['PropName'] = $propListItem['PropName'];
						$chooseSkuArray[$propListKey]['child'][] = array(
									'PropValueId' => $valueItem['PropValueId'],
									'PropValueName' => $valueItem['PropValueName']
								);
					}
				}
				$ticketRow['sku_choose'] = $chooseSkuArray;
				
				if($ticketSkuInfo['user_name_limit'] == 1 || $ticketSkuInfo['mobile_limit'] == 1) {
					$ticketRow['climit'] = $ticketRow['limit_count'];
				}
				
				$ticketRow['skuPrice'] = json_encode($skuInfoArray['skuPrice']);
				
			}		
			//根据ticket_id 获取对应店铺的拥有者uuid
			$ticketRow['manager'] = $this->getPermissionUuidByTicketId($ticket_id);
			//wap 图片
			$ticketRow['wap_img'] = Model_Admin_Ticket::getInstance()->getWapImg($ticket_id);
			foreach($ticketRow['wap_img'] as $wapImgKey => $rowWapImg) {
				$ticketRow['wap_img'][$wapImgKey]['wap_img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticketwap/' . $rowWapImg['img_url'];
				if($rowWapImg['img_url']) {
					list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/ticketwap/' . $rowWapImg['img_url']);
					$ticketRow['wap_img'][$wapImgKey]['width'] = $width;
					$ticketRow['wap_img'][$wapImgKey]['height'] = $height;
				} else {
					$ticketRow['wap_img'][$wapImgKey]['width'] = 0;
					$ticketRow['wap_img'][$wapImgKey]['height'] = 0;
				}
			}
			//折扣
			$ticketRow['discount'] = round(($ticketRow['selling_price'] / $ticketRow['par_value']) * 10 , 1);
			//券状态
			if($ticketRow['start_time'] > REQUEST_TIME) {
				$ticketRow['voucher_status'] = 1; //未开始
			} elseif($ticketRow['start_time'] < REQUEST_TIME && $ticketRow['end_time'] > REQUEST_TIME) {
				$ticketRow['voucher_status'] = 2; //进行中
			} elseif($ticketRow['end_time'] < REQUEST_TIME) {
				$ticketRow['voucher_status'] = -1; //已结束
			}
			
			$data = $ticketRow;
			unset($ticketRow);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
	
	public function getGoodListByTicketId($ticket_id, $limit = 0, $page = 1, $pagesize = 20) {
		if($limit) {
			$cacheKey = 'get_good_list_by_ticket_id_' . $ticket_id .'_limit_' . $limit;
			$folder = '220';
		} else {
			$cacheKey = 'get_good_list_by_ticket_id_' . $ticket_id .'_page_' . $page;
			$folder = '220';
		}
		$data = $this->getData($cacheKey);
		
		if(empty($data)) {
			$snapArray = $snapData = array();
			$where = "A.`ticket_id` = '{$ticket_id}' 
					  and A.`end_time` > '". REQUEST_TIME ."' and A.`start_time` < '". REQUEST_TIME ."'
					  and B.`good_status` <> '-1' 
					  and B.`is_auth` <> '-1' 
					  and B.`is_del` = '0'";
			
			$sqlC = "select count(A.good_id)
					from `oto_ticket_good` as A
					left join `oto_good` as B on A.`good_id` = B.`good_id`
					where {$where}";
			$totalNum = $this->_db->fetchOne($sqlC);
			
			$sql = "select 
					B.`good_id`, B.`good_name`, B.`shop_id`, B.`shop_name`, B.`dis_price`, B.`favorite_number`, B.`concerned_number`,
					(select `img_url` from `oto_good_img` where `good_id` = B.good_id order by is_first desc, good_img_id asc limit 1) as `img_url`
					from `oto_ticket_good` as A
					left join `oto_good` as B on A.`good_id` = B.`good_id`
					where {$where} 
					order by B.`created` desc";
			if($limit) {
				$sql .= " limit {$limit}";
				$snapArray = $this->_db->fetchAll($sql);
			} else {
				$snapArray = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
			}
			
			foreach($snapArray as $key => $snap) {
				if($snap['img_url'] && is_file(ROOT_PATH . 'web/data/good/'.$folder.'/' . $snap['img_url'])) {
					list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/good/'.$folder.'/' . $snap['img_url']);						
					$snapArray[$key]['img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/good/'.$folder.'/' . $snap['img_url'];
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
	
	public function getTicketList($store_id = 0, $brand_id = 0, $region_id = 0, $circle_id = 0, $shop_id = 0, $order = 1, $ticket_class = 0, $city = 'sh') {
		//缓存键值
		$cacheKey = 'get_ticket_list_' . "{$city}_{$store_id}_{$brand_id}_{$region_id}_{$circle_id}_{$shop_id}_{$order}_{$ticket_class}";
		$data = $this->getData($cacheKey);
		
		if (empty($data)) {
			$snapArray = $snapData = array();
			$ticket_type = $this->getTicketTypeID('voucher');
		
			$where = "`city` = '{$city}' and `ticket_type` = '{$ticket_type}' and `ticket_status` = '1' and `is_auth` = '1' and `is_show` = '1' and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'";
			
			
			$orderby = '';
				
			if($store_id) 	$where .= " and `store_id` = '{$store_id}'";
			if($brand_id) 	$where .= " and `brand_id` = '{$brand_id}'";
			if($region_id) 	$where .= " and `region_id` = '{$region_id}'";
			if($circle_id)	$where .= " and `circle_id` = '{$circle_id}'";
			if($shop_id) 	$where .= " and `shop_id` = '{$shop_id}'";
			if($ticket_class) $where .= " and `ticket_class` = '{$ticket_class}'";
			
			;
			if($order == 1) {
				$orderby = "order by `sequence` asc,  `created` desc";
			} elseif ($order == 2) {
				$orderby = "order by `has_led` desc, `created` desc";
			}
				
			$sqlC = "select count(ticket_id) from `oto_ticket` where {$where}";
			$totalNum = $this->_db->fetchOne($sqlC);

			$sql = "select ticket_id,ticket_title,ticket_type,ticket_summary,shop_id,shop_name,par_value,selling_price,start_time,end_time,cover_img from oto_ticket where {$where} {$orderby}";
			$couponInfo = $this->_db->fetchAll($sql);
			$ticketsort = $this->getTicketSortById(0, 'ticketsort');
			foreach ($couponInfo as $key=>$value) {
				$couponInfo[$key]['shop_id'] = $value['shop_id'];
				$couponInfo[$key]['shop_name'] = $value['shop_name'];
				$couponInfo[$key]['coupon_type'] = $ticketsort[$value['ticket_type']]['sort_detail_mark'];
				if($ticketsort[$value['ticket_type']]['sort_detail_mark'] == 'coupon') {
					$couponInfo[$key]['dis_price'] = floor($value['par_value']);
				} elseif($ticketsort[$value['ticket_type']]['sort_detail_mark'] == 'voucher') {
					$couponInfo[$key]['dis_price'] = floor($value['selling_price']);
				}
				$couponInfo[$key]['par_value'] = floor($value['par_value']);
				$couponInfo[$key]['valid_time'] = date('Y', $value['start_time']).'年'.date('m.d', $value['start_time']).'-'.date('m.d', $value['end_time']).'日';
				$couponInfo[$key]['sort_name'] = $ticketsort[$value['ticket_type']]['sort_detail_name'];
			}
			$data['copon_info'] = $couponInfo;
			$data['coupon_num'] = count($couponInfo);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
	
	public function getTicketRow($ticket_id) {
		return $this->select(
				"`ticket_id` = '{$ticket_id}'",
				'oto_ticket',
				'*',
				'',
				true
		);
	}
	
	public function getTicketRowByTicketUuid($ticket_uuid) {
		return $this->select(
				"`ticket_uuid` = '{$ticket_uuid}'",
				'oto_ticket',
				'*',
				'',
				true
		);
	}
	
	
	public function getTicketInfoForMsg($ticket_id , $is_cache=false){
		$key = "get_ticket_info_for_msg_{$ticket_id}";
		$data = $this->getData($key);
		if(!$is_cache || empty($data)) {
			$ticketRow = $this->getTicketRow($ticket_id);
			$data = array();
			if( !empty($ticketRow) ){
				$shopRow = Model_Api_App::getInstance()->getShopFieldById($ticketRow["shop_id"],'*');
				$ticket_type = array("0"=>"","1"=>"商场券","2"=>"品牌券","3"=>"特卖券");//1：商场 2： 品牌  3： 特卖
				$data = array(
						'shop_id'	   => $ticketRow["shop_id"],
						'ticket_class' => $ticketRow['ticket_class'],
						'ticket_type'  => $ticket_type[$ticketRow['ticket_class']],
						'ticket_name'  => $ticketRow['ticket_title'],
						'ticket_time'  => datex($ticketRow["start_time"],"Y年m月d日")."-".datex($ticketRow["end_time"],"m月d日")." ".datex($ticketRow["start_time"],"H:i")."-".datex($ticketRow["end_time"],"H:i"),
						'selling_price'=> $ticketRow['app_price'] ? $ticketRow['app_price'] : $ticketRow['selling_price'],
						'shop_name'    => $shopRow['shop_name'],
						'shop_address' => $shopRow['shop_address'],
						'lng'          => $shopRow['lng'],
						'lat'          => $shopRow['lat'],
						'distance'     => -1
				);
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 领取优惠券
	 * @param unknown_type $ticket_id
	 * @param unknown_type $phone
	 * @param unknown_type $userInfo
	 */
	public function applyTicket($ticket_id, $phone, & $userInfo) {		
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
			$msg = '抱歉，此券已经过期！';
		}
		
		if($ticketRow['is_auth'] == 0) {
			$status = 102;
			$msg = '抱歉，此券已经下降！';
		}
		
		if($ticketRow['ticket_status'] == 0) {
			$status = 103;
			$msg = '抱歉，此券正在审核中。。。';
		}
		
		if($ticketRow['ticket_status'] == '-1') {
			$status = 104;
			$msg = '抱歉，此券未能通过审核！';
		}
		
		if($ticketRow['total'] - $ticketRow['has_led'] <= 0) {
			$status = 105;
			$msg = '抱歉，此券已领完';
			$extra = array('lave' => 0);
		}
		
		if($status == 100) {
			if($mark == 'coupon') {
				//相同的手机号码不能重复领取同一张优惠券
				if($this->whetherSamePhoneRepeat($ticket_id, $phone)) {
					$this->_db->rollBack();
					_exit('抱歉，本券你已经领取过一次了', 106);
				}
				//判断同一个用户，同一张优惠券,最多可以领取2次
				if($this->whetherSameUserRepeatTwo($userInfo['user_id'], $ticket_id)) {
					$this->_db->rollBack();
					_exit('抱歉，<b>' . $userInfo['user_name'] . '</b> 同一个用户名，每张优惠券只能领取2次', 107);
				}
				//开始领券
				$couponTicketsArray = Custom_AuthTicket::getCouponTickets($userInfo['uuid'], $userInfo['user_name'], $ticketRow['ticket_uuid'], $phone);				
				if($couponTicketsArray['code']  == 1) { //领取成功!
					if($this->insertTicketDetail($phone, $userInfo, $ticketRow, $couponTicketsArray['message']) && $this->updateTicketLed($ticket_id)) {
						$this->_db->commit();
						$this->updateUserTicket($userInfo['user_id']);
						_exit('恭喜你，当前优惠券领取成功！', 100, array('lave' => $ticketRow['total'] - $ticketRow['has_led'] - 1));
					}
				} else {
					$this->_db->rollBack();
					_exit('券领取失败，请稍后再试！', 110);
				}
			}	
		} else {
			$this->_db->rollBack();
			_exit($msg, $status, $extra);
		}
	}
	/**
	 * 购买现金券
	 * @param unknown_type $ticket_id
	 * @param unknown_type $userInfo
	 */
	public function applyTicketVoucher($ticket_id, & $userInfo) {
		$status = 100;
		$msg = '';
		$extra = array();
		$sql = "select ticket_id, ticket_title, ticket_uuid, ticket_type, user_name, shop_id, par_value,
				selling_price, start_time, end_time, valid_stime, valid_etime,
				content, ticket_status, total, has_led, is_auth, created
				from `oto_ticket` where `ticket_id` = '{$ticket_id}'";
		
		$ticketRow = $this->_db->fetchRow($sql);
						
		if($ticketRow['start_time'] > REQUEST_TIME) {
			$status = 101;
			$msg = '抱歉，还未到购买时间！';
		}
		
		if($ticketRow['end_time'] < REQUEST_TIME) {
			$status = 101;
			$msg = '抱歉，已经过期！';
		}
		
		if($ticketRow['is_auth'] == 0) {
			$status = 102;
			$msg = '抱歉，已经下架！';
		}
		
		if($ticketRow['ticket_status'] == 0) {
			$status = 103;
			$msg = '抱歉，正在审核中。。。';
		}
		
		if($ticketRow['ticket_status'] == '-1') {
			$status = 104;
			$msg = '抱歉，未能通过审核！';
		}
		$ticketRow['ticket_mark'] = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
		if( $ticketRow['ticket_mark'] == "crowdfunding" || $ticketRow['ticket_mark'] == "spike" ){
			$ticketObject = Custom_AuthTicket::getTicketDetailByTicketUuid($ticketRow['ticket_uuid']);
			if($ticketObject->status == 1) {
				$hasStock = $ticketObject->data->ProductStock; //还有多少库存
				if($hasStock <= 0) {
					$status = 105;
					$msg = '抱歉，已卖完';
					$extra = array('lave' => 0);
				}
			}
		}else{
			$ticketObject = Custom_AuthTicket::get_ticket_details_by_guid($ticketRow['ticket_uuid']);
			if($ticketObject->status == 1) {
				$hasStock = $ticketObject->data->Avtivities[0]->ProductStock; //还有多少库存
				if($hasStock <= 0) {
					$status = 105;
					$msg = '抱歉，已卖完';
					$extra = array('lave' => 0);
				}	
			}
		}
		if($status == 100) {
			$extra = array('guid' => $ticketRow['ticket_uuid']);
		}
		_exit($msg, $status, $extra);
		
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
	private function insertTicketDetail($phone, & $userInfo, & $ticketRow, $ticket_token) {
		$param = array(
					'ticket_id' => $ticketRow['ticket_id'],
					'ticket_title' => $ticketRow['ticket_title'],
					'ticket_uuid' => $ticketRow['ticket_uuid'],
					'ticket_token' => $ticket_token,
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
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
	
	public function getCouponInfo($type, $page, $pagesize = 20) {
		$key = "get_coupon_info_{$this->_city}_{$page}_t" . $type;
		$data = $this->getData($key);
		if (empty($data)) {
			$ticket_type = $this->getTicketTypeID('voucher');
			$start = ($page - 1) * $pagesize;
			$where = " and `city` = '{$this->_city}' and `ticket_status` = '1' and `is_auth` = '1' and `is_show` = 1 and `ticket_type` = '{$ticket_type}'";
			$order = " order by `sequence` asc, `created` desc";
			$limit = " limit {$start}, {$pagesize}";
						
			switch ($type) {
				case '1' :  // 商场/品牌
					$sql = "select * from oto_ticket 
							where `ticket_class` in (1 , 2) 
							{$where} 
							and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'
							{$order}
							{$limit}";					
					break;
				case '2' :  // 特卖
					$sql = "select * from oto_ticket 
							where `ticket_class` = 3 
							{$where} 
							and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'
							{$order}
							{$limit}";
					break;
				case '3' : // 过期
					$sql = "select * from oto_ticket 
							where `ticket_status` = '1' 
							{$where}
							and `end_time` < '" . REQUEST_TIME . "' 
							{$order}
							{$limit}";
					break;
			}
			$data = $this->getTicketListInfo($sql);
			$this->setData($key, $data);
		}
		return $data;
	}
		
	public function getTuanInfo($sid, $dtype, $dsort, $city, $page, $pagesize = 20) {		
		$key = "get_tuan_info_{$sid}_{$dtype}_{$dsort}_{$city}_{$page}";
		$data = $this->getData($key);
		if(empty($data)) {
			$data = array();
			$tuanResult = Custom_AuthTicket::getTuanList($sid, $dtype, $dsort, $city, $page, $pagesize);
			if($tuanResult['code'] == 1) {
				$data = $tuanResult['message']['Result'];
				foreach($data as & $row) {
					if($row['Images']) {
						foreach($row['Images'] as & $srow) {
							if($srow['Name'] == '240*240') {
								$row['img_url'] = $srow['Url'];
							}
						}
					} else {
						$row['img_url'] = '';
					}
					$row['wap_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . 'home/ticket/wap-show/tid/' . $row['CommonID'];
				}
				
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	private function getTicketListInfo($sql) {
		$couponInfo = $this->_db->fetchAll($sql);
		
		foreach ($couponInfo as $key=>$value) {
			$couponInfo[$key]['dis_price'] = floor($value['selling_price']);
			$couponInfo[$key]['par_value'] = floor($value['par_value']);
			$couponInfo[$key]['valid_time'] = date('Y.n.j', $value['valid_stime']).'-'.date('n.j', $value['valid_etime']);
			$couponInfo[$key]['cover_img'] = $value['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/cover/' . $value['cover_img'] : '/images/blank.png';
			$brandLogo = '';
			if($value['brand_id']) {
				$brandLogo = $this->_db->fetchOne("select brand_logo from `oto_brand` where brand_id = '{$value['brand_id']}'");
			}
			$couponInfo[$key]['brand_logo'] = $brandLogo ? $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/brand/' . $brandLogo : '/images/blank.png';
		}
		return $couponInfo;
	}
	
	public function getTicketTypeID($mark = 'voucher') {
		$ticketSortArray = $this->getTicketSortById(0, 'ticketsort');
		foreach($ticketSortArray as $ticketItem) {
			if($ticketItem['sort_detail_mark'] == $mark) {
				return $ticketItem['sort_detail_id'];
			}
		}
	}
	/**
	 * 获取团购关联店铺
	 * @param unknown_type $ticket_id
	 * @param unknown_type $shopInfo
	 */
	public function getAssociatedShops($ticket_id, $shopInfo = null) {
		$key = 'get_associated_shops_' . $ticket_id;
		$data = $this->getData($key);
		if(empty($data)) {
			$sql = "select B.shop_id, B.shop_name, B.shop_address, B.phone
					from `oto_ticket_shop` A
					left join `oto_shop` B on A.shop_id = B.shop_id
					where A.ticket_id = '{$ticket_id}' and B.shop_status <> '-1' and B.shop_pid = '0'
					order by B.sequence asc, B.created desc";
			$data = $this->_db->fetchAll($sql);
			$this->setData($key, $data);
		}
		if(!is_null($shopInfo)) {
			$data = array_merge(array(array('shop_id' => $shopInfo['shop_id'], 'shop_name' => $shopInfo['shop_name'], 'shop_address' => $shopInfo['shop_address'], 'phone' => $shopInfo['phone'])), $data);
		}
		foreach ($data as &$row) {
			$row['ticketNum'] = Model_Api_Goods::getInstance()->getTicketNumByShopId($row['shop_id']);
			$row['tuanNum'] = Model_Api_Goods::getInstance()->getTuanNumByShopId($row['shop_id']);
		}
		return $data;
	}
	/**
	 * 获取团购推荐商品
	 * @param unknown_type $identifier
	 * @param unknown_type $city
	 * @param unknown_type $limit
	 */
	public function getTuanRecommend($identifier, $city, $limit) {
		//缓存键值
		$key = 'get_tuan_recommend_' . $city  . '_' . $identifier . '_' . $limit;
		$data = $this->getData($key);
		
		if(empty($data)) {
			$pos_id = $this->getPosId($identifier, $city);
			if($identifier == 'buygood_hot') {
				$identifier = 'buygood_img_small';
			}
			$identifierRow = $this->getTheRecommendedPosition('buygood', $identifier, true, $city);
			$sql = "select A.img_url, A.www_url, B.ticket_id,B.ticket_uuid,B.start_time,B.end_time, A.title, B.par_value, B.selling_price, B.app_price, C.file_img_small, B.shop_id, B.shop_name
					from `oto_recommend` A
					left join `oto_ticket` B on A.come_from_id = B.ticket_id
					left join `oto_ticket_info` C on B.ticket_id = C.ticket_id
					where B.ticket_status <> '-1' and B.is_auth = '1' and B.is_show = '1' 
					and B.start_time < '".REQUEST_TIME."' and B.end_time > '". REQUEST_TIME ."'
					and A.city = '{$city}'
					and A.pos_id = '{$pos_id}'
					order by A.sequence asc, B.created desc
					limit {$limit}
					";
			$data = $this->_db->fetchAll($sql);
			
			foreach($data as & $row) {
				if($row['img_url']) {
					$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/recommend/' . $row['img_url'];
				} else {
					$row['imgUrl'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/' . $row['file_img_small'];
				}
				$row['width'] = $identifierRow['width'];
				$row['height'] = $identifierRow['height'];
				$row['format_time'] = datex($row['start_time'], 'm月d') . '日-' . datex($row['end_time'], 'd') . '日';
				
				if($row['app_price'] < 0) {
					$row['selling_price'] = 0;
				} elseif($row['app_price'] > 0) {
					$row['selling_price'] = $row['app_price'];
				}
			}
			
			$this->setData($key, $data);
		}
		
		return $data;
	}
	/**
	 * 根据ticket_id 获取对应店铺的拥有者uuid
	 * @param unknown_type $ticket_id
	 */
	function getPermissionUuidByTicketId($ticket_id) {
		$sql = "select C.uuid
				from `oto_ticket` A
				left join `oto_user_shop_competence` B on A.shop_id = B.shop_id
				left join `oto_user` C on B.user_id = C.user_id
				where A.ticket_id = '{$ticket_id}' and C.user_type <> '1'";
		$data = $this->_db->fetchCol($sql);
		return $data ? $data : array();
	}
}