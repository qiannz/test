<?php
class Model_Admin_Ticket extends Base {
	private static $_instance;
	protected $_table = 'oto_ticket';
	protected $_where;
	protected $_order;
	
	public static function getInstance() {
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
	
	public function getCount($ticketType) {
		return $this->_db->fetchOne("select count(ticket_id) from `".$this->_table."` where ticket_type = '{$ticketType}'".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'app':
							if($value == 1) {
								$where .= " and `start_time` > '". REQUEST_TIME ."'";
							} elseif($value == 2) {
								$where .= " and `start_time` < '". REQUEST_TIME ."' and `end_time` > '". REQUEST_TIME ."'";
							} elseif($value == 3) {
								$where .= " and `end_time` < '". REQUEST_TIME ."'";
							}						
							break;
						case 'st':
							if($value == 1) {
								$where .= " and `ticket_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `ticket_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `ticket_status` = '-1'";
							}
							break;
						case 'isa':
							if($value == 1) {
								$where .= " and `is_auth` = '1'";
							} elseif($value == 2) {
								$where .= " and `is_auth` = '0'";
							}
							break;
						case 'iss':
							if($value == 1) {
								$where .= " and `is_show` = '1'";
							} elseif($value == 2) {
								$where .= " and `is_show` = '0'";
							}
							break;
						case 'title':
							if($value) {
								$where .= " and `ticket_title` like '%".trim($value)."%'";
							}
							break;
						case 'act_name':
							if($value) {
								$act_id = $this->_db->fetchCol("select activity_id from oto_activity where activity_name like '%{$value}%' order by created desc");
								$where .= " and ".$this->db_create_in($act_id, 'activity_id');
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
	
	public function getList($page, $ticketType, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where ticket_type = '{$ticketType}' ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($data as $key => $item) {
			if($item['start_time'] > REQUEST_TIME) {
				$data[$key]['apply_status'] = '0';
			} elseif ($item['start_time'] < REQUEST_TIME && $item['end_time'] > REQUEST_TIME) {
				$data[$key]['apply_status'] = '1';
			} elseif ($item['end_time'] < REQUEST_TIME) {
				$data[$key]['apply_status'] = '-1';
			}
		}
		return $data ? $data : array();
	}
	
	public function addTicket($getData, $ticketType, $is_pay_self = false) {
		$user_name = $getData['user_name'];
		$user_id = $this->getUserIdByUserName($user_name);
		$shop_id = intval($getData['sid']);
		$ticket_type = $ticketType;
		$ticket_class = intval($getData['ticket_class']);
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$activity_id = intval($getData['activity_id']);
		$ticket_summary = Custom_String::HtmlReplace($getData['ticket_summary'], 1);
		$shopRow = $this->getShopFieldById($shop_id);
		if($is_pay_self) {
			$par_value = 0;
			$selling_price = 0;
			$app_price = -1;
		} else {
			$par_value = $getData['par_value'];
			$selling_price = $getData['selling_price'] ? $getData['selling_price'] : 0;
			$app_price = $getData['app_price'] ? $getData['app_price'] : 0;
		}
		$start_time = strtotime($getData['start_time']);
		$end_time = strtotime($getData['end_time']);
		$valid_stime =  strtotime($getData['valid_stime']);
		$valid_etime =  strtotime($getData['valid_etime']);
		$content = Custom_String::cleanHtml($getData['content']);
		$wap_content = strip_tags(trim($getData['wap_content']));
		$cover_img = $getData['cover_img'];
		$total = intval($getData['total']);
		$is_free = intval($getData['is_free']);
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		$limit_count = $getData['limit_count'] ? intval($getData['limit_count']) : 0;		
		$limit_unit = $getData['limit_unit'] ? $getData['limit_unit'] : 'Activity'; 
		$is_sale = intval($getData['is_sale']);
		$sale_code = $is_sale == 1 ? $getData['sale_code'] : '';
		$can_share = intval($getData['can_share']);
		$city = !$this->_ad_city ? 'sh' : $this->_ad_city;
		
		//事务开始
		$this->_db->beginTransaction();
		
		$data = array(
				'ticket_title' => $ticket_title,
				'ticket_type' => $ticket_type,
				'ticket_class' => $ticket_class,
				'ticket_sort' => $ticket_sort,
				'activity_id' => $activity_id,
				'ticket_summary' => $ticket_summary,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'brand_id' => $shopRow['brand_id'],
				'store_id' => $shopRow['store_id'],
				'region_id' => $shopRow['region_id'],
				'circle_id' => $shopRow['circle_id'],
				'shop_id' => $shop_id,
				'shop_name' => $shopRow['shop_name'],
				'par_value' => $par_value,
				'selling_price' => $selling_price,
				'app_price' => $app_price,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'valid_stime' => $valid_stime,
				'valid_etime' => $valid_etime,
				'content' => $content,
				'wap_content' => $wap_content,
				'total' => $total,
				'is_free' => $is_free,
				'is_auth' => $is_auth,
				'is_show' => $is_show,
				'limit_count' => $limit_count,
				'limit_unit' => $limit_unit,
				'is_sale' => $is_sale,
				'sale_code' => $sale_code,
				'can_share' => $can_share,
				'city' => $city,
				'created' => REQUEST_TIME
		);

		if(!empty($cover_img)) {
			$data = array_merge(array('cover_img' => $cover_img), $data);
		}
		
		//新增券
		$insert_ticket_id = $this->_db->insert('oto_ticket', $data);
		//关联商品
		$goodInsertResult = true;
		$goodArray = trim($getData['gids'], ',') ? explode(',', trim($getData['gids'], ',')) : array();
		$goodCount = count($goodArray);
		if(is_array($goodArray) && $goodCount > 0) {
			$k = 0;
			foreach ($goodArray as $good_id) {
				if(Model_Home_Suser::getInstance()->addTicketGood($insert_ticket_id, $good_id, $start_time, $end_time)) {
					$k++;
				}
			}
			if($goodCount != $k) {
				$goodInsertResult = false;
			}
		}
		
		//券关联图片处理
		preg_match_all( "/<img.*?src=[\\\'| \\\"](http:\/\/.*\/api\/good\/get\-special\-img\-thumb\/iid\/[1-9][0-9]*\/type\/ticket\/w\/740)[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
		$img_attachment = array();
		if(!empty($matches[1]))
		{
			foreach ($matches[1] as $img_url){
				$img_attachment[] = str_replace(
						array(
								$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/good/get-special-img-thumb/iid/',
								'/type/ticket/w/740'
						), '', $img_url);
			}
			$img_attachment_ids = '';
			$img_attachment = array_unique($img_attachment);
			$img_attachment_ids = implode(',', $img_attachment);
			if(!empty($img_attachment_ids)) {
				$sql = "select * from `oto_ticket_img` where `id` in ({$img_attachment_ids})";
				$imgArr = $this->_db->fetchAll($sql);
				foreach($imgArr as & $imgRow) {
					if($imgRow['ticket_id'] == 0) {
						$this->_db->update('oto_ticket_img', array('ticket_id' => $insert_ticket_id), array('id' => $imgRow['id']));
					} elseif ($imgRow['ticket_id'] == $insert_ticket_id) {
		
					} else {
						if(!$this->checkTicketImg($insert_ticket_id, $imgRow['user_id'], $shop_id, $imgRow['img_url'])) {
							$param = array(
									'ticket_id'  => $insert_ticket_id,
									'user_id'  	 => $imgRow['user_id'],
									'shop_id' 	 => $shop_id,
									'img_url'  	 => $imgRow['img_url'],
									'created' 	 => REQUEST_TIME
							);
							$sql = $this->insertSql('oto_ticket_img', $param);
							$this->_db->query($sql);
						}
					}
				}
			}
		}
		
		//券店铺关联
		$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
		$shopArray = array_unique($shopArray);
		$shopCount = count($shopArray);
		if(is_array($shopArray) && $shopCount > 0) {
			$sql_shop_str = '';
			foreach($shopArray as $sid) {
				//主店铺不能再被关联咯
				if($sid && $shop_id != $sid) {
					$sql_shop_str .= "('{$insert_ticket_id}', '{$sid}'),";
				}
			}
			
			if($sql_shop_str) {
				$sql_shop = 'insert ignore into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
				$this->_db->query($sql_shop);
			}
		}	

		
		if($insert_ticket_id && $insert_ticket_id > 0 && $goodInsertResult) {
			//事务确认
			$this->_db->commit();
			return $insert_ticket_id;
		} else {
			//事务回滚
			$this->_db->rollBack();
			return false;
		}
	}
	
	// 编辑优惠券
	public function updateTicket($getData, $ticketType, $is_pay_self = false) {
		$tid = intval($getData['tid']);
		$user_name = $getData['user_name'];
		$user_id = $this->getUserIdByUserName($user_name);
		$shop_id = intval($getData['sid']);
		$ticket_type = $ticketType;
		$ticket_class = intval($getData['ticket_class']);
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$activity_id = intval($getData['activity_id']);
		$ticket_summary = Custom_String::HtmlReplace($getData['ticket_summary'], 1);
		$shopRow = $this->getShopFieldById($shop_id);
		if($is_pay_self) {
			$par_value = 0;
			$selling_price = 0;
			$app_price = -1;
		} else {
			$par_value = $getData['par_value'];
			$selling_price =  $getData['selling_price'] ? $getData['selling_price'] : 0;
			$app_price =  $getData['app_price'] ? $getData['app_price'] : 0;
		}
		$start_time = strtotime($getData['start_time']);
		$end_time = strtotime($getData['end_time']);
		$valid_stime =  strtotime($getData['valid_stime']);
		$valid_etime =  strtotime($getData['valid_etime']);
		$content = Custom_String::cleanHtml($getData['content']);
		$wap_content = strip_tags(trim($getData['wap_content']));
		$cover_img = $getData['cover_img'];
		$total = intval($getData['total']);
		$is_free = intval($getData['is_free']);
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		
		$limit_count = $getData['limit_count'] ? intval($getData['limit_count']) : 0;
		$limit_unit = $getData['limit_unit'] ? $getData['limit_unit'] : 'Activity';
		$is_sale = intval($getData['is_sale']);
		$sale_code = $is_sale == 1 ? $getData['sale_code'] : '';
		$can_share = intval($getData['can_share']);
		//事务开始
		$this->_db->beginTransaction();
		
		$data = array(
				'ticket_title' => $ticket_title,
				'ticket_type' => $ticket_type,
				'ticket_class' => $ticket_class,
				'ticket_sort' => $ticket_sort,
				'activity_id' => $activity_id,
				'ticket_summary' => $ticket_summary,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'brand_id' => $shopRow['brand_id'],
				'store_id' => $shopRow['store_id'],
				'region_id' => $shopRow['region_id'],
				'circle_id' => $shopRow['circle_id'],
				'shop_id' => $shop_id,
				'shop_name' => $shopRow['shop_name'],
				'par_value' => $par_value,
				'selling_price' => $selling_price,
				'app_price' => $app_price,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'valid_stime' => $valid_stime,
				'valid_etime' => $valid_etime,
				'content' => $content,
				'wap_content' => $wap_content,
				'ticket_status' => 0, // 需要重新审核
				'total' => $total,
				'is_free' => $is_free,
				'is_auth' => $is_auth,
				'is_show' => $is_show,
				'limit_count' => $limit_count,
				'limit_unit' => $limit_unit,
				'is_sale' => $is_sale,
				'sale_code' => $sale_code,
				'can_share' => $can_share,
				'updated' => REQUEST_TIME
		);
		
		if(!empty($cover_img)) {
			$data = array_merge(array('cover_img' => $cover_img), $data);
		}
		//编辑增券
		$edit_ticket_result = $this->_db->update('oto_ticket', $data, "`ticket_id` = '{$tid}'");
		
		//关联商品
		$goodInsertResult = true;
		$goodArray = trim($getData['gids'], ',') ? explode(',', trim($getData['gids'], ',')) : array();
		$goodCount = count($goodArray);
		if(is_array($goodArray) && $goodCount > 0) {
			//先删除券和商品的关联
			$this->_db->delete('oto_ticket_good', array('ticket_id' => $tid), 0);
			$k = 0;
			foreach ($goodArray as $good_id) {
				if(Model_Home_Suser::getInstance()->addTicketGood($tid, $good_id, $start_time, $end_time)) {
					$k++;
				}
			}
			if($goodCount != $k) {
				$goodInsertResult = false;
			}
		}
		
		//券关联图片处理
		preg_match_all( "/<img.*?src=[\\\'| \\\"](http:\/\/.*\/api\/good\/get\-special\-img\-thumb\/iid\/[1-9][0-9]*\/type\/ticket\/w\/740)[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
		$img_attachment = array();
		if(!empty($matches[1]))
		{
			foreach ($matches[1] as $img_url){
				$img_attachment[] = str_replace(
						array(
								$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/good/get-special-img-thumb/iid/',
								'/type/ticket/w/740'
						), '', $img_url);
			}
			$img_attachment_ids = '';
			$img_attachment = array_unique($img_attachment);
			$img_attachment_ids = implode(',', $img_attachment);
			if(!empty($img_attachment_ids)) {
				//先断开图片和券的关联
				$this->_db->update('oto_ticket_img', array('ticket_id' => 0), array('ticket_id' => $tid), 0);
				//接着把图片和券关联上
				$sql = "select * from `oto_ticket_img` where `id` in ({$img_attachment_ids})";
				$imgArr = $this->_db->fetchAll($sql);
				foreach($imgArr as & $imgRow) {
					if($imgRow['ticket_id'] == 0) {
						$this->_db->update('oto_ticket_img', array('ticket_id' => $tid), array('id' => $imgRow['id']));
					} elseif ($imgRow['ticket_id'] == $tid) {
				
					} else {
						if(!$this->checkTicketImg($tid, $imgRow['user_id'], $shop_id, $imgRow['img_url'])) {
							$param = array(
									'ticket_id'  => $tid,
									'user_id'  	 => $imgRow['user_id'],
									'shop_id' 	 => $shop_id,
									'img_url'  	 => $imgRow['img_url'],
									'created' 	 => REQUEST_TIME
							);
							$sql = $this->insertSql('oto_ticket_img', $param);
							$this->_db->query($sql);
						}
					}
				}
			}
		}
		//调整优惠券领取表: 券的使用开始和结束时间
		$ticketUpdateResult = true;
		$ticketDetailsArray = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_detail', 'detail_id, ticket_id');
		$ticketDetailsNum = count($ticketDetailsArray);
		if(is_array($ticketDetailsArray) && $ticketDetailsNum > 0) {
			$ticketUpdateResult = $this->_db->update(
						'oto_ticket_detail', 
						array(
								'valid_stime' => $valid_stime, 
								'valid_etime' => $valid_etime
						),
						array(
								'ticket_id =' => $tid
						),
						0
					);
		}
		
		//券店铺关联
		$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
		$shopArray = array_unique($shopArray);
		$shopCount = count($shopArray);
		if(is_array($shopArray) && $shopCount > 0) {
			//先删除之前的店铺关联
			$this->_db->delete('oto_ticket_shop', array('ticket_id' => $tid), 0);
			$sql_shop_str = '';
			foreach($shopArray as $sid) {
				//主店铺不能再被关联咯
				if($sid && $shop_id != $sid) {
					$sql_shop_str .= "('{$tid}', '{$sid}'),";
				}
			}
			
			if($sql_shop_str) {
				$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
				$this->_db->query($sql_shop);
			}
		}
		
		$this->updateTicketPreNotice( $tid , "voucher" ,"voucher_view" );
		
		if($edit_ticket_result && $goodInsertResult && $ticketUpdateResult) {
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
	 * 检查图片唯一性
	 * @param unknown_type $ticket_id
	 * @param unknown_type $user_id
	 * @param unknown_type $shop_id
	 * @param unknown_type $img_url
	 */
	public function checkTicketImg($ticket_id, $user_id, $shop_id, $img_url) {
		$sql = "select 1 from `oto_ticket_img`
					where `ticket_id` = '{$ticket_id}'
					and `user_id` = '{$user_id}'
					and `shop_id` = '{$shop_id}'
					and `img_url` = '{$img_url}'
					limit 1
				";
	
		return $this->_db->fetchOne($sql) == 1;
	}
	/**
	 * 同步券时间到message数据库
	 * @param unknown_type $ticket_id 券id
	 * @param unknown_type $start_time 券开始时间
	 * @param unknown_type $end_time 券结束时间
	 */
	public function syncTicketInfoToMsgDb( $ticket_id , $start_time , $end_time ){
		if( !$ticket_id || !$start_time || !$end_time ){
			return;
		}
		$msg_db = Core_DB::get('message', null, true);
		$msg_db->query("INSERT INTO `oto_ticket`(`ticket_id`,`start_time`,`end_time`) VALUES ('{$ticket_id}','{$start_time}','{$end_time}') ON DUPLICATE KEY UPDATE `start_time`='{$start_time}',`end_time`='{$end_time}'");
	}
	
	/**
	 * 更新pre_notice中券的信息
	 * @param unknown_type $from_id
	 * @param unknown_type $type
	 * @param unknown_type $open_type
	 */
	public function updateTicketPreNotice( $from_id , $type , $opentype ){
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($from_id);
		if( $type == "voucher" ) {//券修改
			$this->syncTicketInfoToMsgDb($from_id, $ticketRow["start_time"], $ticketRow["end_time"]);
				
			$ticket_class = "";
			if( 1 == $ticketRow["ticket_class"] ){
				$ticket_class = "商场券";
			}elseif( 2 == $ticketRow["ticket_class"] ){
				$ticket_class = "品牌券";
			}elseif( 3 == $ticketRow["ticket_class"] ){
				$ticket_class = "特卖券";
			}
			$message = Model_Api_Message::getInstance()->configMsg("voucher_view_new", array(
					"{category}"=>$ticket_class,
					"{title}"=>$ticketRow["ticket_title"],
					"{time}"=>datex($ticketRow["start_time"],"Y.m.d")."-".datex($ticketRow["end_time"],"m.d")
			));
			$noticeData = array(
					"message"=>$message,
					"start_time"=>$ticketRow["start_time"],
					"end_time"=>$ticketRow["end_time"]
			);
			$this->_db->update('oto_pre_notice', $noticeData , array("type"=>$type,"opentype"=>$opentype,"from_id"=>$from_id,"notice_type"=>1));
				
			$shopInfo = $this->_db->fetchRow("SELECT * FROM `oto_shop` WHERE `shop_id`='{$ticketRow["shop_id"]}'");
			if( $ticketRow["ticket_class"] == 1 ){//商场
				if( $shopInfo["market_id"] ){//关注的商场的商场券
					$marketName = $this->_db->fetchOne("SELECT `market_name` FROM `oto_market` WHERE `market_id`='{$shopInfo["market_id"]}'");
					$message = Model_Api_Message::getInstance()->configMsg("voucher_market_concerned", array(
							"{category}"=>$marketName,
							"{title}"=>$ticketRow["ticket_title"]
					));
				}
			}else if($ticketRow["ticket_class"] == 2){//品牌
				if( $shopInfo["brand_id"] ){//关注的品牌的品牌券
					$brandName = $this->getBrand($shopInfo["brand_id"]);
					$message = Model_Api_Message::getInstance()->configMsg("voucher_brand_concerned", array(
							"{category}"=>$brandName,
							"{title}"=>$ticketRow["ticket_title"]
					));
						
				}
			}
			$noticeData = array(
					"message"=>$message,
					"start_time"=>$ticketRow["start_time"],
					"end_time"=>$ticketRow["end_time"]
			);
			$this->_db->update('oto_pre_notice', $noticeData , array("type"=>$type,"opentype"=>$opentype,"from_id"=>$from_id,"notice_type"=>3));
		}else if( $type == "commodity" ){
			//新品
			if($ticketRow['is_free'] == 1) {//app免费
				$ticketRow['selling_price'] = 0;
			} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] > 0) {//app价格
				$ticketRow['selling_price'] = $ticketRow['app_price'];
			}
			$message = Model_Api_Message::getInstance()->configMsg("commodity_view_new", array(
					"{title}"=>$ticketRow["ticket_title"],
					"{price}"=>$ticketRow["selling_price"]
			));
			$noticeData = array(
					"message"=>$message
			);
			$this->_db->update('oto_pre_notice',$noticeData,array("type"=>$type,"opentype"=>$opentype,"from_id"=>$from_id,"notice_type"=>1));
			//关注店铺的新品
			$shopName = $this->getShopName($ticketRow["shop_id"]);
			$message = Model_Api_Message::getInstance()->configMsg("commodity_shop_concerned", array(
					"{category}"=>$shopName,
					"{title}"=>$ticketRow["ticket_title"]
			));
			$noticeData = array(
					"message"=>$message
			);
			$this->_db->update('oto_pre_notice',$noticeData,array("type"=>$type,"opentype"=>$opentype,"from_id"=>$from_id,"notice_type"=>3));
		}
	}
	
	/**
	 * 新增团购商品
	 * @param unknown_type $getData
	 * @param unknown_type $ticketType
	 * @return unknown|boolean
	 */
	public function addTuanTicket($getData, & $userInfo, $city = 'sh') {		
		$ticket_id = $getData['tid'];
		
		$user_id = $userInfo['user_id'];
		$user_name = $userInfo['user_name'];
		
		$shop_id = intval($getData['sid']);
		$shopRow = $this->getShopFieldById($shop_id);
		
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'buygood');
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$activity_id = intval($getData['activity_id']);
		if(!$activity_id && $getData['activity_name']) {
			$activity_name = Custom_String::HtmlReplace($getData['activity_name'], 2);
			$activity_id = Model_Home_Suser::getInstance()->getActivityIdByName($activity_name, $user_id);
		}
		$ticket_summary = Custom_String::HtmlReplace($getData['ticket_summary'], 1);
		
		$par_value = $getData['p_value'];
		$selling_price = $getData['s_value'] ? $getData['s_value'] : 0;
		$app_price = $getData['a_price'] ? $getData['a_price'] : 0;
		
		$start_time = strtotime($getData['sdate']);
		$end_time = strtotime($getData['edate']);
		$valid_stime =  strtotime($getData['stime']);
		$valid_etime =  strtotime($getData['etime']);
		
		$content = Custom_String::cleanHtml($getData['content']);
		$wap_content = strip_tags(trim($getData['wap_content']));
		
		$file_img_large = $getData['file_img_large'] ? $getData['file_img_large'] : '';
		$file_img_small = $getData['file_img_small'] ? $getData['file_img_small'] : '';
		
		$total = intval($getData['total']);
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		$limit_count = $getData['climit'] ? intval($getData['climit']) : 0;
		$limit_unit = $getData['unit'] ? $getData['unit'] : 'Activity';
		$CanWeb = $getData['CanWeb'] ? $getData['CanWeb'] : 0; 
		$CanWap = $getData['CanWap'] ? $getData['CanWap'] : 0;
		$CanApp = $getData['CanApp'] ? $getData['CanApp'] : 0;
		$UserNameLimit = $getData['UserNameLimit'] ? $getData['UserNameLimit'] : 0;
		$MobileLimit = $getData['MobileLimit'] ? $getData['MobileLimit'] : 0;
	
	
		$param = array(
				'ticket_title' => $ticket_title,
				'ticket_type' => $ticket_type,
				'ticket_sort' => $ticket_sort,
				'activity_id' => $activity_id,
				'ticket_summary' => $ticket_summary,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'brand_id' => $shopRow['brand_id'],
				'store_id' => $shopRow['store_id'],
				'region_id' => $shopRow['region_id'],
				'circle_id' => $shopRow['circle_id'],
				'shop_id' => $shop_id,
				'shop_name' => $shopRow['shop_name'],
				'par_value' => $par_value,
				'selling_price' => $selling_price,
				'app_price' => $app_price,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'valid_stime' => $valid_stime,
				'valid_etime' => $valid_etime,
				'content' => $content,
				'wap_content' => $wap_content,
				'total' => $total,
				'is_auth' => $is_auth,
				'is_show' => $is_show,
				'limit_count' => $limit_count,
				'limit_unit' => $limit_unit,
				'city' => $city
		);
		//编辑团购商品
		if($ticket_id) {
			$param = array_merge($param, array('updated' => REQUEST_TIME));
			$this->_db->update('oto_ticket', $param, array('ticket_id' => $ticket_id));
			$ticketInfo = $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_info', '*', '', true);
			$skuInfo = unserialize($ticketInfo['sku_info']);
			if($getData['dataStr'] && $getData['dataRetStr']) {
				$skuInfo = $this->formatSku($getData);
			}
			$params = array(
						'category_id' => $skuInfo['category_id'],
						'category_name' => $skuInfo['category_name'],
						'sku_info' => serialize($skuInfo),
						'can_web' => $CanWeb,
						'can_wap' => $CanWap,
						'can_app' => $CanApp,
						'user_name_limit' => $UserNameLimit,
						'mobile_limit' => $MobileLimit
				);
			
			if($file_img_large) {
				$params = array_merge($params, array('file_img_large' => $file_img_large));
			}
			
			if($file_img_small) {
				$params = array_merge($params, array('file_img_small' => $file_img_small));
			}
			$this->_db->update('oto_ticket_info', $params, array('ticket_id' => $ticket_id));
			
			//有图片的话，记录券图片库
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
				if(is_array($img_attachment) && $imgCount > 0) {
					//先删除和券关联的图片
					$this->_db->delete('oto_ticket_img', array('ticket_id' => $ticket_id), 0);
					//重新添加券图片关联
					foreach ($img_attachment as $key => $imgUrl) {
						$img_url = str_replace($GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/740/', '', $imgUrl);
						if($this->addTicketImg($img_url, $ticket_id, $user_id)) {
						}
					}
				}
			}
			
			//团购商品店铺关联
			$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
			$shopCount = count($shopArray);
			if(is_array($shopArray) && $shopCount > 0) {
				//先删除之前的店铺关联
				$this->_db->delete('oto_ticket_shop', array('ticket_id' => $ticket_id), 0);
				$sql_shop_str = '';
				foreach($shopArray as $sid) {
					//主店铺不能再被关联咯
					if($sid && $shop_id != $sid) {
						$sql_shop_str .= "('{$ticket_id}', '{$sid}'),";
					}
				}
					
				if($sql_shop_str) {
					$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
					$this->_db->query($sql_shop);
				}
			} else {
				//$this->_db->delete('oto_ticket_shop', array('ticket_id' => $ticket_id), 0);
			}
			//不远程同步
			$this->_db->update('oto_ticket', array('ticket_status' => 0), array('ticket_id' => $ticket_id));
			return array('status' => 100);
			
			//程同步
			$resultArr = $this->syncAudit('buygood', $ticket_id, $this->_ad_city);
			if($resultArr['status'] == 200) {
				$this->_db->update('oto_ticket', array('ticket_status' => 0), array('ticket_id' => $ticket_id));
				return array('status' => 100);
			}
		} 
		//新增团购商品
		else {
			$param = array_merge($param, array('created' => REQUEST_TIME));
			$insert_ticket_id = $this->_db->insert('oto_ticket', $param);
			$skuInfo = $this->formatSku($getData);
			
			if($insert_ticket_id) {
				$params = array(
						'ticket_id' => 	$insert_ticket_id,
						'category_id' => $skuInfo['category_id'],
						'category_name' => $skuInfo['category_name'],
						'file_img_large' => $file_img_large,
						'file_img_small' => $file_img_small,
						'sku_info' => serialize($skuInfo),
						'can_web' => $CanWeb,
						'can_wap' => $CanWap,
						'can_app' => $CanApp,
						'user_name_limit' => $UserNameLimit,
						'mobile_limit' => $MobileLimit
				);
					
				$this->_db->insert('oto_ticket_info', $params);
			}
			
			//有图片的话，记录券图片库
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
				if(is_array($img_attachment) && $imgCount > 0) {
					foreach ($img_attachment as $key => $imgUrl) {
						$img_url = str_replace($GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/ticket/740/', '', $imgUrl);
						if(Model_Home_Suser::getInstance()->updateTicketImg($img_url, $insert_ticket_id, $user_id)) {
						}
					}
				}
			}
			
			//店铺关联
			$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
			$shopCount = count($shopArray);
			if(is_array($shopArray) && $shopCount > 0) {
				$sql_shop_str = '';
				foreach($shopArray as $sid) {
					//主店铺不能再被关联咯
					if($sid && $shop_id != $sid) {
						$sql_shop_str .= "('{$insert_ticket_id}', '{$sid}'),";
					}
				}
			
				if($sql_shop_str) {
					$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
					$this->_db->query($sql_shop);
				}
			}
			
			if($insert_ticket_id) {
				//不远程同步
				return array('status' => 100, 'insert_ticket_id' => $insert_ticket_id);
				//远程同步
				$resultArr = $this->syncAudit('buygood', $insert_ticket_id, $this->_ad_city);
				if($resultArr['status'] == 100) {
					$this->_db->update('oto_ticket', array('ticket_uuid' => $resultArr['data']['ticket_uuid'], 'ticket_status' => 1), array('ticket_id' => $insert_ticket_id));
					return array('status' => 100, 'insert_ticket_id' => $insert_ticket_id);
				}
			}
		}
	}
	
	/**
	 * 新增商城商品
	 * @param unknown_type $getData
	 * @param unknown_type $ticketType
	 * @return unknown|boolean
	 */
	public function addCommodityTicket($getData, & $userInfo, $city = 'sh') {
		$ticket_id = $getData['tid'];
	
		$user_id = $userInfo['user_id'];
		$user_name = $userInfo['user_name'];
	
		$shop_id = intval($getData['sid']);
		$shopRow = $this->getShopFieldById($shop_id);
	
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$ticket_class = intval($getData['ticket_class']);
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$selling_points = Custom_String::HtmlReplace($getData['selling_points'], 1);
		$ticket_summary = Custom_String::HtmlReplace($getData['ticket_summary'], 1);
	
		$par_value = $getData['p_value'];
		$selling_price = $getData['s_value'] ? $getData['s_value'] : 0;
		$app_price = $getData['a_price'] ? $getData['a_price'] : 0;
		
		if( empty($getData['start_time']) ){
			$start_time = REQUEST_TIME;
		}else{
			$start_time = strtotime( $getData['start_time'] );
		}
		
		if( empty($getData['end_time']) ){
			$end_time = strtotime( $GLOBALS['GLOBAL_CONF']['COMMODITY_DEFAULT_END_TIME'] );
		}else{
			$end_time = strtotime( $getData['end_time'] );
		}
	
		$content = Custom_String::cleanHtml($getData['content']);
		$wap_content = strip_tags(trim($getData['wap_content']));
	
		$total = intval($getData['total']);
		$free_shipping = intval($getData['free_shipping']);
		$is_free = intval($getData['is_free']);
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		
		
		$limit_count = $getData['climit'] ? intval($getData['climit']) : 0;
		$limit_unit = $getData['unit'] ? $getData['unit'] : 'Activity';
		$CanWeb = $getData['CanWeb'] ? $getData['CanWeb'] : 0;
		$CanWap = $getData['CanWap'] ? $getData['CanWap'] : 0;
		$CanApp = $getData['CanApp'] ? $getData['CanApp'] : 0;
		$UserNameLimit = $getData['UserNameLimit'] ? $getData['UserNameLimit'] : 0;
		$MobileLimit = $getData['MobileLimit'] ? $getData['MobileLimit'] : 0;
	
	
		$param = array(
				'ticket_title' => $ticket_title,
				'selling_points' => $selling_points,
				'ticket_type' => $ticket_type,
				'ticket_class' => $ticket_class,
				'ticket_sort' => $ticket_sort,
				'ticket_summary' => $ticket_summary,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'brand_id' => $shopRow['brand_id'],
				'store_id' => $shopRow['store_id'],
				'region_id' => $shopRow['region_id'],
				'circle_id' => $shopRow['circle_id'],
				'shop_id' => $shop_id,
				'shop_name' => $shopRow['shop_name'],
				'par_value' => $par_value,
				'selling_price' => $selling_price,
				'app_price' => $app_price,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'content' => $content,
				'wap_content' => $wap_content,
				'total' => $total,
				'free_shipping' => $free_shipping,
				'is_free' => $is_free,
				'is_auth' => $is_auth,
				'is_show' => $is_show,
				'limit_count' => $limit_count,
				'limit_unit' => $limit_unit,
				'city' => $city
		);
		//编辑团购商品
		if($ticket_id) {
			$param = array_merge($param, array('updated' => REQUEST_TIME));
			$this->_db->update('oto_ticket', $param, array('ticket_id' => $ticket_id));
			
			$ticketInfo = $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_info', '*', '', true);
			$skuInfo = unserialize($ticketInfo['sku_info']);
			if($getData['dataStr'] && $getData['dataRetStr']) {
				$skuInfo = $this->formatSku($getData);
			}
			
			$params = array(
					'category_id' => $skuInfo['category_id'] ? $skuInfo['category_id'] : 0,
					'category_name' => $skuInfo['category_id'] ? $skuInfo['category_name'] : '',
					'sku_info' => $skuInfo['category_id'] ? serialize($skuInfo) : '',
					'can_web' => $CanWeb,
					'can_wap' => $CanWap,
					'can_app' => $CanApp,
					'user_name_limit' => $UserNameLimit,
					'mobile_limit' => $MobileLimit
			);
			
			$this->_db->update('oto_ticket_info', $params, array('ticket_id' => $ticket_id));

			//商品关联图片处理
			preg_match_all( "/<img.*?src=[\\\'| \\\"](http:\/\/.*\/api\/good\/get\-special\-img\-thumb\/iid\/[1-9][0-9]*\/type\/ticket\/w\/740)[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
			$img_attachment = array();
			if(!empty($matches[1]))
			{
				foreach ($matches[1] as $img_url){
					$img_attachment[] = str_replace(
							array(
									$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/good/get-special-img-thumb/iid/',
									'/type/ticket/w/740'
							), '', $img_url);
				}
				$img_attachment_ids = '';
				$img_attachment = array_unique($img_attachment);
				$img_attachment_ids = implode(',', $img_attachment);
				if(!empty($img_attachment_ids)) {
					//先断开图片和券的关联
					$this->_db->update('oto_ticket_img', array('ticket_id' => 0), array('ticket_id' => $ticket_id), 0);
					//接着把图片和券关联上
					$sql = "select * from `oto_ticket_img` where `id` in ({$img_attachment_ids})";
					$imgArr = $this->_db->fetchAll($sql);
					foreach($imgArr as & $imgRow) {
						if($imgRow['ticket_id'] == 0) {
							$this->_db->update('oto_ticket_img', array('ticket_id' => $ticket_id), array('id' => $imgRow['id']));
						} elseif ($imgRow['ticket_id'] == $ticket_id) {
			
						} else {
							if(!$this->checkTicketImg($ticket_id, $imgRow['user_id'], $shop_id, $imgRow['img_url'])) {
								$param = array(
										'ticket_id'  => $ticket_id,
										'user_id'  	 => $imgRow['user_id'],
										'shop_id' 	 => $shop_id,
										'img_url'  	 => $imgRow['img_url'],
										'created' 	 => REQUEST_TIME
								);
								$sql = $this->insertSql('oto_ticket_img', $param);
								$this->_db->query($sql);
							}
						}
					}
				}
			}
						
			$this->updateTicketPreNotice($ticket_id, "commodity", "commodity_view");
			
			//团购商品店铺关联
			$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
			$shopCount = count($shopArray);
			if(is_array($shopArray) && $shopCount > 0) {
				//先删除之前的店铺关联
				$this->_db->delete('oto_ticket_shop', array('ticket_id' => $ticket_id), 0);
				$sql_shop_str = '';
				foreach($shopArray as $sid) {
					//主店铺不能再被关联咯
					if($sid && $shop_id != $sid) {
						$sql_shop_str .= "('{$ticket_id}', '{$sid}'),";
					}
				}
					
				if($sql_shop_str) {
					$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
					$this->_db->query($sql_shop);
				}
			}
			//不远程同步
			$this->_db->update('oto_ticket', array('ticket_status' => 0), array('ticket_id' => $ticket_id));
			return array('status' => 100, 'insert_ticket_id' => $ticket_id);
				
			//远程同步
			$resultArr = $this->syncAudit('commodity', $ticket_id, $this->_ad_city);
			if($resultArr['status'] == 200) {
				$this->_db->update('oto_ticket', array('ticket_status' => 0), array('ticket_id' => $ticket_id));
				return array('status' => 100, 'insert_ticket_id' => $ticket_id);
			}
		}
		//新增团购商品
		else {
			$param = array_merge($param, array('updated' => REQUEST_TIME, 'created' => REQUEST_TIME));
			$insert_ticket_id = $this->_db->insert('oto_ticket', $param);
			if($insert_ticket_id) {
				if($getData['dataStr'] && $getData['dataRetStr']) {
					$skuInfo = $this->formatSku($getData);
				}
				
				$params = array(
						'ticket_id' => 	$insert_ticket_id,
						'category_id' => $skuInfo['category_id'] ? $skuInfo['category_id'] : 0,
						'category_name' => $skuInfo['category_id'] ? $skuInfo['category_name'] : '',
						'sku_info' => $skuInfo['category_id'] ? serialize($skuInfo) : '',
						'can_web' => $CanWeb,
						'can_wap' => $CanWap,
						'can_app' => $CanApp,
						'user_name_limit' => $UserNameLimit,
						'mobile_limit' => $MobileLimit
				);
				
				$this->_db->insert('oto_ticket_info', $params);
			}
			//券关联图片处理
			preg_match_all( "/<img.*?src=[\\\'| \\\"](http:\/\/.*\/api\/good\/get\-special\-img\-thumb\/iid\/[1-9][0-9]*\/type\/ticket\/w\/740)[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
			$img_attachment = array();
			if(!empty($matches[1]))
			{
				foreach ($matches[1] as $img_url){
					$img_attachment[] = str_replace(
							array(
									$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/good/get-special-img-thumb/iid/',
									'/type/ticket/w/740'
							), '', $img_url);
				}
				$img_attachment_ids = '';
				$img_attachment = array_unique($img_attachment);
				$img_attachment_ids = implode(',', $img_attachment);
				if(!empty($img_attachment_ids)) {
					$sql = "select * from `oto_ticket_img` where `id` in ({$img_attachment_ids})";
					$imgArr = $this->_db->fetchAll($sql);
					foreach($imgArr as & $imgRow) {
						if($imgRow['ticket_id'] == 0) {
							$this->_db->update('oto_ticket_img', array('ticket_id' => $insert_ticket_id), array('id' => $imgRow['id']));
						} elseif ($imgRow['ticket_id'] == $insert_ticket_id) {
			
						} else {
							if(!$this->checkTicketImg($insert_ticket_id, $imgRow['user_id'], $shop_id, $imgRow['img_url'])) {
								$param = array(
										'ticket_id'  => $insert_ticket_id,
										'user_id'  	 => $imgRow['user_id'],
										'shop_id' 	 => $shop_id,
										'img_url'  	 => $imgRow['img_url'],
										'created' 	 => REQUEST_TIME
								);
								$sql = $this->insertSql('oto_ticket_img', $param);
								$this->_db->query($sql);
							}
						}
					}
				}
			}								
			//店铺关联
			$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
			$shopCount = count($shopArray);
			if(is_array($shopArray) && $shopCount > 0) {
				$sql_shop_str = '';
				foreach($shopArray as $sid) {
					//主店铺不能再被关联咯
					if($sid && $shop_id != $sid) {
						$sql_shop_str .= "('{$insert_ticket_id}', '{$sid}'),";
					}
				}
					
				if($sql_shop_str) {
					$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
					$this->_db->query($sql_shop);
				}
			}
				
			if($insert_ticket_id) {
				//不远程同步
				return array('status' => 100, 'insert_ticket_id' => $insert_ticket_id);
				//远程同步
				$resultArr = $this->syncAudit('commodity', $insert_ticket_id, $this->_ad_city);
				if($resultArr['status'] == 100) {
					$this->_db->update('oto_ticket', array('ticket_uuid' => $resultArr['data']['ticket_uuid'], 'ticket_status' => 1), array('ticket_id' => $insert_ticket_id));
					return array('status' => 100, 'insert_ticket_id' => $insert_ticket_id);
				}
			}
		}
	}

	/**
	 * 新增，编辑一元众筹
	 * @param unknown_type $getData
	 * @param unknown_type $ticketType
	 * @return unknown|boolean
	 */
	public function addEditCrowdfundingTicket($getData, & $userInfo, $city = 'sh') {
		$ticket_id = $getData['tid'];
	
		$user_id = $userInfo['user_id'];
		$user_name = $userInfo['user_name'];
	
		$shop_id = intval($getData['sid']);
		$shopRow = $this->getShopFieldById($shop_id);
	
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'crowdfunding');
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$ticket_summary = Custom_String::HtmlReplace($getData['ticket_summary'], 1);
		$par_value = $getData['p_value'];
		$selling_price = $getData['s_value'] ? $getData['s_value'] : 0;
		$start_time = strtotime($getData['sdate']);
		$end_time = strtotime($getData['edate']);
		$valid_stime =  strtotime($getData['stime']);
		$valid_etime =  strtotime($getData['etime']);
		$lottery_time =  strtotime($getData['lottery_time']);
		$cover_img = $getData['cover_img'];
		$wap_content = strip_tags(trim($getData['wap_content']));
		$total = intval($getData['total']);
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		$limit_count = $getData['climit'] ? intval($getData['climit']) : 0;
		$limit_unit = $getData['unit'] ? $getData['unit'] : 'Activity';
		$CanWeb = $getData['CanWeb'] ? $getData['CanWeb'] : 0;
		$CanWap = $getData['CanWap'] ? $getData['CanWap'] : 0;
		$CanApp = $getData['CanApp'] ? $getData['CanApp'] : 0;
		$UserNameLimit = $getData['UserNameLimit'] ? $getData['UserNameLimit'] : 0;
		$MobileLimit = $getData['MobileLimit'] ? $getData['MobileLimit'] : 0;
		$expiration_minute = $getData['expiration_minute'] ? $getData['expiration_minute'] : 10;
		$love_number = $getData['love_number'] ? $getData['love_number'] : 0;
		$no_winning = $getData['no_winning'];
		$winning_user_name = $getData['winning_user_name'];
		$count_times = $getData['count_times'] ? intval($getData['count_times']) : 1;
		$buy_discount = intval($getData['buy_discount']);
		
		$param = array(
				'ticket_title' => $ticket_title,
				'ticket_type' => $ticket_type,
				'ticket_sort' => $ticket_sort,
				'ticket_summary' => $ticket_summary,
				'user_id' => $user_id,
				'user_name' => $user_name,
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
				'wap_content' => $wap_content,
				'total' => $total,
				'is_auth' => $is_auth,
				'is_show' => $is_show,
				'limit_count' => $limit_count,
				'limit_unit' => $limit_unit,
				'city' => $city
		);
		
		if(!empty($cover_img)) {
			$param = array_merge(array('cover_img' => $cover_img), $param);
		}
		
		//编辑一元众筹
		if($ticket_id) {
			$param = array_merge($param, array('updated' => REQUEST_TIME));
			$this->_db->update('oto_ticket', $param, array('ticket_id' => $ticket_id));
			$ticketInfo = $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_info', '*', '', true);
			$params = array(
					'can_web' => $CanWeb,
					'can_wap' => $CanWap,
					'can_app' => $CanApp,
					'user_name_limit' => $UserNameLimit,
					'mobile_limit' => $MobileLimit,
					'lottery_time' => $lottery_time,
					'expiration_minute' => $expiration_minute,
					'love_number' => $love_number,
					'no_winning' => $no_winning,
					'winning_user_name' => $winning_user_name,
					'count_times' => $count_times,
					'buy_discount' => $buy_discount,
			);
			$this->_db->update('oto_ticket_info', $params, array('ticket_id' => $ticket_id));
			//一元众筹与店铺关联
			$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
			$shopCount = count($shopArray);
			if(is_array($shopArray) && $shopCount > 0) {
				//先删除之前的店铺关联
				$this->_db->delete('oto_ticket_shop', array('ticket_id' => $ticket_id), 0);
				$sql_shop_str = '';
				foreach($shopArray as $sid) {
					//主店铺不能再被关联咯
					if($sid && $shop_id != $sid) {
						$sql_shop_str .= "('{$ticket_id}', '{$sid}'),";
					}
				}
					
				if($sql_shop_str) {
					$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
					$this->_db->query($sql_shop);
				}
			}
			//不远程同步
			$this->_db->update('oto_ticket', array('ticket_status' => 0), array('ticket_id' => $ticket_id));
			return array('status' => 100, 'insert_ticket_id' => $ticket_id);
		}
		//新增一元众筹
		else {
			$param = array_merge($param, array('updated' => REQUEST_TIME, 'created' => REQUEST_TIME));
			$insert_ticket_id = $this->_db->insert('oto_ticket', $param);
			if($insert_ticket_id) {
				$params = array(
						'ticket_id' => 	$insert_ticket_id,
						'can_web' => $CanWeb,
						'can_wap' => $CanWap,
						'can_app' => $CanApp,
						'user_name_limit' => $UserNameLimit,
						'mobile_limit' => $MobileLimit,
						'lottery_time' => $lottery_time,
						'expiration_minute' => $expiration_minute,
						'love_number' => $love_number,
						'no_winning' => $no_winning,
						'winning_user_name' => $winning_user_name,
						'count_times' => $count_times,
						'buy_discount' => $buy_discount,
				);
	
				$this->_db->insert('oto_ticket_info', $params);
			}
			//店铺关联
			$shopArray = trim($getData['sids'], ',') ? explode(',', trim($getData['sids'], ',')) : array();
			$shopCount = count($shopArray);
			if(is_array($shopArray) && $shopCount > 0) {
				$sql_shop_str = '';
				foreach($shopArray as $sid) {
					//主店铺不能再被关联咯
					if($sid && $shop_id != $sid) {
						$sql_shop_str .= "('{$insert_ticket_id}', '{$sid}'),";
					}
				}
				if($sql_shop_str) {
					$sql_shop = 'insert into `oto_ticket_shop` (`ticket_id`, `shop_id`) values ' . substr($sql_shop_str, 0, -1);
					$this->_db->query($sql_shop);
				}
			}
	
			if($insert_ticket_id) {
				//不远程同步
				return array('status' => 100, 'insert_ticket_id' => $insert_ticket_id);
			}
		}
	}

	/**
	 * 新增，编辑快来抢（秒杀）
	 * @param unknown_type $getData
	 * @param unknown_type $userInfo
	 * @return unknown|boolean
	 */
	public function addEditSpikeTicket($getData, & $userInfo, $city = 'sh') {
		$ticket_id = $getData['tid'];
	
		$user_id = $userInfo['user_id'];
		$user_name = $userInfo['user_name'];
	
		$shop_id = intval($getData['sid']);
		$shopRow = $this->getShopFieldById($shop_id);
	
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'spike');
		$ticket_sort = intval($getData['ticket_sort']);
		$ticket_title = Custom_String::HtmlReplace($getData['ticket_title'], 1);
		$ticket_summary = Custom_String::HtmlReplace($getData['ticket_summary'], 1);
		$par_value = $getData['p_value'];
		$selling_price = $getData['s_value'] ? $getData['s_value'] : 0;
		$start_time = strtotime($getData['sdate']);
		$end_time = strtotime($getData['edate']);
		$valid_stime =  strtotime($getData['stime']);
		$valid_etime =  strtotime($getData['etime']);
		$cover_img = $getData['cover_img'];
		$content = Custom_String::cleanHtml($getData['content']);
		$wap_content = strip_tags(trim($getData['wap_content']));
		$total = intval($getData['total']);
		$is_auth = intval($getData['is_auth']);
		$is_show = intval($getData['is_show']);
		$limit_count = $getData['climit'] ? intval($getData['climit']) : 0;
		$limit_unit = $getData['unit'] ? $getData['unit'] : 'Activity';
		$CanWeb = $getData['CanWeb'] ? $getData['CanWeb'] : 0;
		$CanWap = $getData['CanWap'] ? $getData['CanWap'] : 0;
		$CanApp = $getData['CanApp'] ? $getData['CanApp'] : 0;
		$UserNameLimit = $getData['UserNameLimit'] ? $getData['UserNameLimit'] : 0;
		$MobileLimit = $getData['MobileLimit'] ? $getData['MobileLimit'] : 0;
		$expiration_minute = $getData['expiration_minute'] ? $getData['expiration_minute'] : 10;
		$love_number = $getData['love_number'] ? $getData['love_number'] : 0;
		
		$param = array(
				'ticket_title' => $ticket_title,
				'ticket_type' => $ticket_type,
				'ticket_sort' => $ticket_sort,
				'ticket_summary' => $ticket_summary,
				'user_id' => $user_id,
				'user_name' => $user_name,
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
				'content' => $content,
				'wap_content' => $wap_content,
				'total' => $total,
				'is_auth' => $is_auth,
				'is_show' => $is_show,
				'limit_count' => $limit_count,
				'limit_unit' => $limit_unit,
				'city' => $city
		);
		
		if(!empty($cover_img)) {
			$param = array_merge(array('cover_img' => $cover_img), $param);
		}
	
		//编辑快来抢（秒杀）
		if($ticket_id) {
			$param = array_merge($param, array('updated' => REQUEST_TIME));
			$this->_db->update('oto_ticket', $param, array('ticket_id' => $ticket_id));
			$ticketInfo = $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_info', '*', '', true);
			$params = array(
					'can_web' => $CanWeb,
					'can_wap' => $CanWap,
					'can_app' => $CanApp,
					'user_name_limit' => $UserNameLimit,
					'mobile_limit' => $MobileLimit,
					'expiration_minute' => $expiration_minute,
					'love_number' => $love_number
			);
			$this->_db->update('oto_ticket_info', $params, array('ticket_id' => $ticket_id));
			//内容图片处理
			$this->contentPictureAddEdit($content, $ticket_id, $shop_id, 'edit');
			//不远程同步
			$this->_db->update('oto_ticket', array('ticket_status' => 0), array('ticket_id' => $ticket_id));
			return array('status' => 100, 'insert_ticket_id' => $ticket_id);
		}
		//新增快来抢（秒杀）
		else {
			$param = array_merge($param, array('updated' => REQUEST_TIME, 'created' => REQUEST_TIME));
			$insert_ticket_id = $this->_db->insert('oto_ticket', $param);
			if($insert_ticket_id) {
				$params = array(
						'ticket_id' => 	$insert_ticket_id,
						'can_web' => $CanWeb,
						'can_wap' => $CanWap,
						'can_app' => $CanApp,
						'user_name_limit' => $UserNameLimit
				);
	
				$this->_db->insert('oto_ticket_info', $params);
				//内容图片处理
				$this->contentPictureAddEdit($content, $ticket_id, $shop_id, 'add');
				//不远程同步
				return array('status' => 100, 'insert_ticket_id' => $insert_ticket_id);
			}
		}
	}
	
	public function addTicketImg($img_url, $ticket_id, $user_id, $shop_id) {
		return $this->_db->insert('oto_ticket_img',
				array(
						'ticket_id' => $ticket_id,
						'user_id' => $user_id,
						'shop_id' => $shop_id,
						'img_url' => $img_url,
						'created' => REQUEST_TIME
				)
		);
	}
	
	public function audit($data) {
		$tid = intval($data['tid']);
		$audit_type = intval($data['audit_type']);
		$reason1 = trim($data['reason1']);
		$reason2 = trim($data['reason2']);
		$mark = $data['mark'];
		$rebates = $data['rebates'];
		$user_id = intval($data['user_id']);
		$city = $data['city'] ? $data['city'] : $this->_ad_city;
		 
		if($audit_type == 2) {
			switch ($reason1) {
				case 1:
					$reason = '虚假信息';
					break;
				case 2:
					$reason = '恶意广告';
					break;
				case 3:
					$reason = '敏感内容';
					break;
				case 4:
					$reason = $reason2;
					break;
			}
		} elseif($audit_type == 1) {
			$reason = '审核通过';
		}

		//单位定义
		
		$unitArray = array(
					'场' => 'Activity',
					'小时' => 'Hour',
					'天' => 'Day',
					'周' => 'Week',
					'自然周' => 'Weekly',
					'月' => 'Month',
					'自然月' => 'Monthly' 
				);
		if($audit_type == 1) {
			//获取券详情信息
			$ticketRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
			$shopRow = $this->getShopFieldById($ticketRow['shop_id']);
			//远程优惠券同步
			if($mark == 'coupon') {
				$syncAuditResultArray = $this->syncAudit('coupon', $tid, $city);
				switch ($syncAuditResultArray['status']) {
					case 100:
						$updateArr = array(
							'ticket_uuid' => $syncAuditResultArray['data']['ticket_uuid'],
							'ticket_status' => '1',
							'audit_person' => $user_id,
							'audit_time' => REQUEST_TIME,
							'reason' => $reason
						);
						break;
					case 101:
						return false;
						break;
					case 200:
						$updateArr = array('ticket_status' => '1', 'reason' => $reason);
						break;
					case 201:
						return false;
						break;
				}
			} 
			//远程现金券同步
			elseif($mark == 'voucher') {
				$syncAuditResultArray = $this->syncAudit('voucher', $tid, $city, $rebates);
				switch ($syncAuditResultArray['status']) {
					case 100:
						$updateArr = array(
							'ticket_uuid' => $syncAuditResultArray['data']['ticket_uuid'], 
							'ticket_status' => '1', 
							'audit_person' => $user_id,
							'audit_time' => REQUEST_TIME,
							'reason' => $reason, 
							'rebates' => $rebates
						);
						break;
					case 101:
						return false;
						break;
					case 200:
						$updateArr = array(
							'ticket_status' => '1',
							'audit_person' => $user_id,
							'audit_time' => REQUEST_TIME,
							'reason' => $reason, 
							'rebates' => $rebates
						);
						break;
					case 201:
						Custom_Common::showMsg('<span style="color:red">审核失败，请稍后再试<br>或者联系技术人员</span>', 'back');
						break;
					case 202:
						return false;
						break;
				}
			}//远程自定义买单券同步
			elseif($mark == 'selfpay') {
				$syncAuditResultArray = $this->syncAudit('selfpay', $tid, $city, $rebates);
				switch ($syncAuditResultArray['status']) {
					case 100:
						$updateArr = array(
							'ticket_uuid' => $syncAuditResultArray['data']['ticket_uuid'], 
							'ticket_status' => '1',
							'audit_person' => $user_id,
							'audit_time' => REQUEST_TIME,
							'reason' => $reason, 
							'rebates' => $rebates
						);
						break;
					case 101:
						return false;
						break;
					case 200:
						$updateArr = array(
							'ticket_status' => '1', 
							'audit_person' => $user_id,
							'audit_time' => REQUEST_TIME,
							'reason' => $reason, 
							'rebates' => $rebates
						);
						break;
					case 201:
						Custom_Common::showMsg('<span style="color:red">审核失败，请稍后再试<br>或者联系技术人员</span>', 'back');
						break;
					case 202:
						return false;
						break;
				}
			}

			
			return $this->_db->update($this->_table, $updateArr, "`ticket_id` = '{$tid}'");
		} elseif ($audit_type == 2) {
			return $this->_db->update(
					$this->_table, 
					array(
							'ticket_status' => '-1', 
							'reason' => $reason,
							'audit_person' => $user_id,
							'audit_time' => REQUEST_TIME
						), 
					"`ticket_id` = '{$tid}'"
			);
		}
		return false;
	}
	/**
	 * 现金券/团购商品 同步
	 * @param unknown_type $mark
	 * @param unknown_type $tid
	 */
	public function syncAudit($mark, $tid, $city = 'sh', $rebates = 0) {
		//获取券详情信息
		$ticketRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', '*', '', true);
		$userRow = $this->getUserByUserId($ticketRow['user_id'], 'uuid');
		//获取店铺信息
		$shopRow = $this->getShopFieldById($ticketRow['shop_id']);
		//远程同步现金券
		if($mark == 'voucher') {
			$activityRow = $this->select("`activity_id` = '{$ticketRow['activity_id']}'", 'oto_activity', '*', '', true);
			$param1 = $param = array();
			if($ticketRow['ticket_uuid'])
			{
				$param1 = array(
						'ID' => $ticketRow['ticket_uuid']
				);
			}
			$appPrice = $ticketRow['app_price'];
			if($appPrice < 0 || $ticketRow['is_free'] == 1) {
				$appPrice = -1;
			}
		
			if($ticketRow['ticket_class'] == 1) {
				$ProductTypeLabel = 'Market';
			} else if($ticketRow['ticket_class'] == 2) {
				$ProductTypeLabel = 'Brand';
			} else if($ticketRow['ticket_class'] == 3) {
				$ProductTypeLabel = 'Sale';
			}
			
			$param = array(
					'Name' => $ticketRow['ticket_title'],
					'Price' => $ticketRow['selling_price'], //售价
					'OriginalPrice' => $ticketRow['par_value'],//原价（面值）
					'AppPrice' => $appPrice,
					'EventCommonId' => $activityRow['activity_id'],
					'EventName' => $activityRow['activity_name'],
					'LimitCount' => $ticketRow['limit_count'],
					'LimitUnit' => $ticketRow['limit_unit'],
					'Amount' => $ticketRow['total'],
					'StartDate' => datex($ticketRow['start_time'], 'Y-m-d H:i:s'),
					'EndDate' => datex($ticketRow['end_time'], 'Y-m-d H:i:s'),
					'UseStartDate' => datex($ticketRow['valid_stime'], 'Y-m-d H:i:s'),
					'UseEndDate' => datex($ticketRow['valid_etime'], 'Y-m-d H:i:s'),
					'PromoUrl' => $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/home/ticket/show/tid/' . $tid,
					'PromoImglist' => $this->getTicketImgByTicketId($tid),
					'Remark' => $ticketRow['ticket_summary'],
					'MerchantCommonID' => $ticketRow['shop_id'],
					'MerchantName' => $ticketRow['shop_name'],
					'MerchantAddress' => $shopRow['shop_address'],
					'Image' => $ticketRow['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $ticketRow['cover_img'] : '' ,
					'isShow' => $ticketRow['is_show'],
					'CommonID' => $tid,
					'Status' => $ticketRow['is_auth'],
					'Code' => $ticketRow['is_sale'] == 1 ? $ticketRow['sale_code'] : '',
					'CanShare' => $ticketRow['can_share'],
					'City' => $city,
					'IsTuan' => 0,
					'ProductTypeLabel' => $ProductTypeLabel,
					'UserId' => $userRow['uuid'],
					'RebateValue' => round($rebates, 1),
					'RebateUnit' => 0,
			);
			$param = array_merge($param1, $param);
			$authResArray = Custom_AuthTicket::createTickets($param, $ProductTypeLabel);
			if(!$ticketRow['ticket_uuid']) { //券新建
				if($authResArray['code'] == 1) {
					$updateArr = array('ticket_uuid' => $authResArray['message']);
					return array('status' => 100, 'data' => $updateArr);
				} else {
					return array('status' => 101);
				}
			} else { //券编辑
				if($authResArray['code'] == -1) {
					return array('status' => 201);
				}elseif($authResArray['code'] == 2) {
					return array('status' => 200);
				} else {
					return array('status' => 202);
				}
			}
		} 
		//远程同步自助买单券
		else if($mark == 'selfpay') {
			$activityRow = $this->select("`activity_id` = '{$ticketRow['activity_id']}'", 'oto_activity', '*', '', true);
			$param1 = $param = array();
			if($ticketRow['ticket_uuid'])
			{
				$param1 = array(
						'ID' => $ticketRow['ticket_uuid']
				);
			}
		
			if($ticketRow['ticket_class'] == 1) {
				$ProductTypeLabel = 'Market';
			} else if($ticketRow['ticket_class'] == 2) {
				$ProductTypeLabel = 'Brand';
			} else if($ticketRow['ticket_class'] == 3) {
				$ProductTypeLabel = 'Sale';
			}
			
			$param = array(
					'Name' => $ticketRow['ticket_title'],
					'Price' => 0, //售价
					'OriginalPrice' => 0,//原价（面值）
					'AppPrice' => -1,
					'EventCommonId' => $activityRow['activity_id'],
					'EventName' => $activityRow['activity_name'],
					'LimitCount' => $ticketRow['limit_count'],
					'LimitUnit' => $ticketRow['limit_unit'],
					'Amount' => $ticketRow['total'],
					'StartDate' => datex($ticketRow['start_time'], 'Y-m-d H:i:s'),
					'EndDate' => datex($ticketRow['end_time'], 'Y-m-d H:i:s'),
					'UseStartDate' => datex($ticketRow['valid_stime'], 'Y-m-d H:i:s'),
					'UseEndDate' => datex($ticketRow['valid_etime'], 'Y-m-d H:i:s'),
					'PromoUrl' => $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/home/ticket/show/tid/' . $tid,
					'PromoImglist' => $this->getTicketImgByTicketId($tid),
					'Remark' => $ticketRow['ticket_summary'],
					'MerchantCommonID' => $ticketRow['shop_id'],
					'MerchantName' => $ticketRow['shop_name'],
					'MerchantAddress' => $shopRow['shop_address'],
					'Image' => $ticketRow['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $ticketRow['cover_img'] : '' ,
					'isShow' => $ticketRow['is_show'],
					'CommonID' => $tid,
					'Status' => $ticketRow['is_auth'],
					'Code' => $ticketRow['is_sale'] == 1 ? $ticketRow['sale_code'] : '',
					'City' => $city,
					'CanCustomPrice' => 1, //自定义买单券
					'ProductTypeLabel' => $ProductTypeLabel,
					'UserId' => $userRow['uuid'],
					'RebateValue' => round($rebates, 1),
					'RebateUnit' => 1,
			);
			$param = array_merge($param1, $param);
			$authResArray = Custom_AuthTicket::createTickets($param, $ProductTypeLabel);
			if(!$ticketRow['ticket_uuid']) { //券新建
				if($authResArray['code'] == 1) {
					$updateArr = array('ticket_uuid' => $authResArray['message']);
					return array('status' => 100, 'data' => $updateArr);
				} else {
					return array('status' => 101);
				}
			} else { //券编辑
				if($authResArray['code'] == -1) {
					return array('status' => 201);
				}elseif($authResArray['code'] == 2) {
					return array('status' => 200);
				} else {
					return array('status' => 202);
				}
			}
		}
		//团购商品
		elseif($mark == 'buygood') {
			$activityRow = $this->select("`activity_id` = '{$ticketRow['activity_id']}'", 'oto_activity', '*', '', true);
			$param1 = $param = array();
			if($ticketRow['ticket_uuid'])
			{
				$param1 = array(
						'ID' => $ticketRow['ticket_uuid']
				);
			}
			$appPrice = $ticketRow['app_price'];
			if($appPrice < 0) {
				$appPrice = -1;
			}
			$skuRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_info', '*', '', true);
			$skuInfo = unserialize($skuRow['sku_info']);
			$skuParams = array(
						'Props' => $skuInfo['Props'],
						'SkuProps' => $skuInfo['SkuProps']
					);
			
			$imgLargeInfo = getimagesize(ROOT_PATH.'web/data/ticket/' . $skuRow['file_img_large']);
			$imgSmallInfo = getimagesize(ROOT_PATH.'web/data/ticket/' . $skuRow['file_img_small']);
			$imagesArray = array(
						array(
								'Url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $skuRow['file_img_large'],
								'Width' => $imgLargeInfo['0'],
								'Height' => $imgLargeInfo['1'],
								'Name' => '640*400'
						),
						array(
								'Url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/ticket/' . $skuRow['file_img_small'],
								'Width' => $imgSmallInfo['0'],
								'Height' => $imgSmallInfo['1'],
								'Name' => '240*240'
						)
					);
			$param = array(
					'Name' => $ticketRow['ticket_title'],
					'Price' => $ticketRow['selling_price'], //售价
					'OriginalPrice' => $ticketRow['par_value'],//原价（面值）
					'AppPrice' => $appPrice,
					'EventCommonId' => $activityRow['activity_id'],
					'EventName' => $activityRow['activity_name'],
					'LimitCount' => $ticketRow['limit_count'],
					'LimitUnit' => $ticketRow['limit_unit'],
					'Amount' => $ticketRow['total'],
					'StartDate' => datex($ticketRow['start_time'], 'Y-m-d H:i:s'),
					'EndDate' => datex($ticketRow['end_time'], 'Y-m-d H:i:s'),
					'UseStartDate' => datex($ticketRow['valid_stime'], 'Y-m-d H:i:s'),
					'UseEndDate' => datex($ticketRow['valid_etime'], 'Y-m-d H:i:s'),
					'PromoUrl' => $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/home/ticket/show/tid/' . $tid,
					'PromoImglist' => $this->getTicketImgByTicketId($tid),
					'Remark' => $ticketRow['ticket_summary'],
					'MerchantCommonID' => $ticketRow['shop_id'],
					'MerchantName' => $ticketRow['shop_name'],
					'MerchantAddress' => $shopRow['shop_address'],
					'Image' => $ticketRow['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $ticketRow['cover_img'] : '' ,
					'Images' => json_encode($imagesArray),
					'isShow' => $ticketRow['is_show'],
					'CommonID' => $tid,
					'Status' => $ticketRow['is_auth'],
					'Code' => $ticketRow['is_sale'] == 1 ? $ticketRow['sale_code'] : '',
					'City' => $city,
					'IsTuan' => 1,
					'StoreId' => $ticketRow['ticket_sort'],
					'CategoryId' => $skuRow['category_id'],
					'IsMiao' => 0,
					'skus' => json_encode($skuParams),
					'UserNameLimit' => $skuRow['user_name_limit'],
					'MobileLimit' => $skuRow['mobile_limit'],
					'CanWeb' => $skuRow['can_web'],
					'CanWap' => $skuRow['can_wap'],
					'CanApp' => $skuRow['can_app'],
					'ProductTypeLabel' => 'Tuan',
					'UserId' => $userRow['uuid'],
			);
			
			$param = array_merge($param1, $param);
			$authResArray = Custom_AuthTicket::createTickets($param, 'Tuan');
			if(!$ticketRow['ticket_uuid']) { //券新建
				if($authResArray['code'] == 1) {
					$updateArr = array('ticket_uuid' => $authResArray['message']);
					return array('status' => 100, 'data' => $updateArr);
				} else {
					return array('status' => 101);
				}
			} else { //券编辑
				if($authResArray['code'] == -1) {
					return array('status' => 201);
				}elseif($authResArray['code'] == 2) {
					return array('status' => 200);
				} else {
					return array('status' => 202);
				}
			}
		}
		//商城商品
		elseif($mark == 'commodity') {
			$param1 = $param = array();
			if($ticketRow['ticket_uuid'])
			{
				$param1 = array(
						'ID' => $ticketRow['ticket_uuid']
				);
			}

			$skuRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_info', '*', '', true);
			if($skuRow['sku_info']) {
				$skuInfo = unserialize($skuRow['sku_info']);
				$skuParams = array(
						'Props' => $skuInfo['Props'],
						'SkuProps' => $skuInfo['SkuProps']
				);
			}

			$tmpArr = array(
						'1' => '店铺商品',
						'2' => '特卖商品'
					);
			
			$param = array(
					'ProductTypeLabel'	=> 'Commodity',
					'Name' 				=>	$ticketRow['ticket_title'],                         		//标题  Selling Points 
					'SellingPoints' 	=>	$ticketRow['selling_points'],                         		//标题  Selling Points
					'Price' 			=>	$ticketRow['selling_price'],                        		//售价
					'OriginalPrice' 	=>	$ticketRow['par_value'],     								//原价（面值）
					'AppPrice' 			=> 	$ticketRow['is_free'] == 1 ? -1 : $ticketRow['app_price'],	//APP售价
					'LimitCount' 		=> 	$ticketRow['limit_count'],									//限购数量
					'LimitUnit' 		=> 	$ticketRow['limit_unit'],									//限购单位
					'Amount' 			=> 	$ticketRow['total'],										//库存
					'Remark' 			=> 	$ticketRow['ticket_summary'],								//简介
					'MerchantCommonID' 	=> 	$ticketRow['shop_id'],										//店铺ID
					'MerchantName' 		=> 	$ticketRow['shop_name'],									//店铺名称
					'MerchantAddress' 	=> 	$shopRow['shop_address'],									//店铺地址
					'isShow' 			=> 	$ticketRow['is_show'],										//是否显示
					'FreeShipping' 		=> 	$ticketRow['free_shipping'],								//是否包邮
					'CommonID' 			=> 	$tid,														//商品ID
					'Status' 			=> 	$ticketRow['is_auth'],										//上下架
					'City' 				=> 	$city,														//城市
					'StoreId' 			=> 	$ticketRow['ticket_sort'],									//商品分类
					'CategoryId' 		=> 	$skuRow['category_id'],										//SKU分类
					'skus' 				=> 	$skuRow['sku_info'] ? json_encode($skuParams) : '',			//SKU明细
					'UserNameLimit' 	=> 	$skuRow['user_name_limit'],									//用户限制
					'MobileLimit' 		=> 	$skuRow['mobile_limit'],									//手机限制
					'CanWeb' 			=> 	$skuRow['can_web'],											//Web端支持
					'CanWap' 			=> 	$skuRow['can_wap'],											//Wap端支持
					'CanApp' 			=> 	$skuRow['can_app'],											//App端支持
					'UserId' 			=> 	$userRow['uuid'],											//用户UUID
					'SubClassValue'		=>	$ticketRow['ticket_class'],
					'SubClassName'		=> 	urlencode($tmpArr[$ticketRow['ticket_class']]),
			);
				
			$param = array_merge($param1, $param);
			$authResArray = Custom_AuthTicket::createTickets($param, 'Commodity');
			if(!$ticketRow['ticket_uuid']) { //商品新建
				if($authResArray['code'] == 1) {
					$updateArr = array('ticket_uuid' => $authResArray['message'], 'shop_id' => $ticketRow['shop_id'], 'brand_id' => $ticketRow['brand_id']);
					return array('status' => 100, 'data' => $updateArr);
				} else {
					return array('status' => 101);
				}
			} else { //商品编辑
				if($authResArray['code'] == -1) {
					return array('status' => 201);
				}elseif($authResArray['code'] == 2) {
					$updateArr = array('shop_id' => $ticketRow['shop_id'], 'brand_id' => $ticketRow['brand_id']);
					return array('status' => 200, 'data' => $updateArr);
				} else {
					return array('status' => 202);
				}
			}
		}
		//一元众筹
		elseif($mark == 'crowdfunding') {
			$param1 = $param = array();
			if($ticketRow['ticket_uuid'])
			{
				$param1 = array(
						'ID' => $ticketRow['ticket_uuid']
				);
			}
		
			$skuRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_info', '*', '', true);
			$param = array(
					'ProductTypeLabel'	=> 'Crowdfunding',
					'Name' 				=>	$ticketRow['ticket_title'],                         		//标题
					'Price' 			=>	$ticketRow['selling_price'],                        		//售价（众筹价）
					'OriginalPrice' 	=>	$ticketRow['par_value'],     								//原价（面值）
					'LimitCount' 		=> 	$ticketRow['limit_count'],									//限购数量
					'LimitUnit' 		=> 	$ticketRow['limit_unit'],									//限购单位
					'Amount' 			=> 	$ticketRow['total'],										//库存
					'StartDate'			=> 	datex($ticketRow['start_time'], 'Y-m-d H:i:s'),				//销售开始时间
					'EndDate' 			=> 	datex($ticketRow['end_time'], 'Y-m-d H:i:s'),				//销售结束时间
					'UseStartDate' 		=> 	datex($ticketRow['valid_stime'], 'Y-m-d H:i:s'),			//使用开始时间
					'UseEndDate' 		=> 	datex($ticketRow['valid_etime'], 'Y-m-d H:i:s'),			//使用结束时间
					'Remark' 			=> 	$ticketRow['ticket_summary'],								//简介
					'MerchantCommonID' 	=> 	$ticketRow['shop_id'],										//店铺ID
					'MerchantName' 		=> 	$ticketRow['shop_name'],									//店铺名称
					'MerchantAddress' 	=> 	$shopRow['shop_address'],									//店铺地址
					'isShow' 			=> 	$ticketRow['is_show'],										//是否显示
					'CommonID' 			=> 	$tid,														//商品ID
					'Status' 			=> 	$ticketRow['is_auth'],										//上下架
					'City' 				=> 	$city,														//城市
					'StoreId' 			=> 	$ticketRow['ticket_sort'],									//商品分类
					'UserNameLimit' 	=> 	$skuRow['user_name_limit'],									//用户限制
					'MobileLimit' 		=> 	$skuRow['mobile_limit'],									//手机限制
					'CanWeb' 			=> 	$skuRow['can_web'],											//Web端支持
					'CanWap' 			=> 	$skuRow['can_wap'],											//Wap端支持
					'CanApp' 			=> 	$skuRow['can_app'],											//App端支持
					'UserId' 			=> 	$userRow['uuid'],											//用户UUID
					'PayTimeOut' 		=> 	$skuRow['expiration_minute'],								//支付失效时间，单位：分钟
					'BuyCountTimes'		=>	$skuRow['count_times'],										//中奖倍率(一份发几个中奖号码)	
					'BuyDiscount'		=> 	$skuRow['buy_discount'],									//购买优惠					
				);
		
			$param = array_merge($param1, $param);
			$authResArray = Custom_AuthTicket::createTickets($param, 'Crowdfunding');
			if(!$ticketRow['ticket_uuid']) { //商品新建
				if($authResArray['code'] == 1) {
					$updateArr = array('ticket_uuid' => $authResArray['message'], 'shop_id' => $ticketRow['shop_id'], 'brand_id' => $ticketRow['brand_id']);
					return array('status' => 100, 'data' => $updateArr);
				} else {
					return array('status' => 101);
				}
			} else { //商品编辑
				if($authResArray['code'] == -1) {
					return array('status' => 201);
				}elseif($authResArray['code'] == 2) {
					$updateArr = array('shop_id' => $ticketRow['shop_id'], 'brand_id' => $ticketRow['brand_id']);
					return array('status' => 200, 'data' => $updateArr);
				} else {
					return array('status' => 202);
				}
			}
		}
		//快来抢（秒杀）
		elseif($mark == 'spike') {
			$param1 = $param = array();
			if($ticketRow['ticket_uuid'])
			{
				$param1 = array(
						'ID' => $ticketRow['ticket_uuid']
				);
			}
		
			$skuRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket_info', '*', '', true);
			$param = array(
					'ProductTypeLabel'	=> 'Miao',
					'Name' 				=>	$ticketRow['ticket_title'],                         		//标题
					'Price' 			=>	$ticketRow['selling_price'],                        		//售价（秒杀价）
					'OriginalPrice' 	=>	$ticketRow['par_value'],     								//原价（面值）
					'LimitCount' 		=> 	$ticketRow['limit_count'],									//限购数量
					'LimitUnit' 		=> 	$ticketRow['limit_unit'],									//限购单位
					'Amount' 			=> 	$ticketRow['total'],										//库存
					'StartDate'			=> 	datex($ticketRow['start_time'], 'Y-m-d H:i:s'),				//销售开始时间
					'EndDate' 			=> 	datex($ticketRow['end_time'], 'Y-m-d H:i:s'),				//销售结束时间
					'UseStartDate' 		=> 	datex($ticketRow['valid_stime'], 'Y-m-d H:i:s'),			//使用开始时间
					'UseEndDate' 		=> 	datex($ticketRow['valid_etime'], 'Y-m-d H:i:s'),			//使用结束时间
					'Remark' 			=> 	$ticketRow['ticket_summary'],								//简介
					'MerchantCommonID' 	=> 	$ticketRow['shop_id'],										//店铺ID
					'MerchantName' 		=> 	$ticketRow['shop_name'],									//店铺名称
					'MerchantAddress' 	=> 	$shopRow['shop_address'],									//店铺地址
					'isShow' 			=> 	$ticketRow['is_show'],										//是否显示
					'CommonID' 			=> 	$tid,														//商品ID
					'Status' 			=> 	$ticketRow['is_auth'],										//上下架
					'City' 				=> 	$city,														//城市
					'StoreId' 			=> 	$ticketRow['ticket_sort'],									//商品分类
					'UserNameLimit' 	=> 	$skuRow['user_name_limit'],									//用户限制
					'MobileLimit' 		=> 	$skuRow['mobile_limit'],									//手机限制
					'CanWeb' 			=> 	$skuRow['can_web'],											//Web端支持
					'CanWap' 			=> 	$skuRow['can_wap'],											//Wap端支持
					'CanApp' 			=> 	$skuRow['can_app'],											//App端支持
					'UserId' 			=> 	$userRow['uuid'],											//用户UUID
					'PayTimeOut' 		=> 	$skuRow['expiration_minute']								//未支付订单库存释放时间
			);
		
			$param = array_merge($param1, $param);
			$authResArray = Custom_AuthTicket::createTickets($param, 'Miao');
			if(!$ticketRow['ticket_uuid']) { //商品新建
				if($authResArray['code'] == 1) {
					$updateArr = array('ticket_uuid' => $authResArray['message'], 'shop_id' => $ticketRow['shop_id'], 'brand_id' => $ticketRow['brand_id']);
					return array('status' => 100, 'data' => $updateArr);
				} else {
					return array('status' => 101);
				}
			} else { //商品编辑
				if($authResArray['code'] == -1) {
					return array('status' => 201);
				}elseif($authResArray['code'] == 2) {
					$updateArr = array('shop_id' => $ticketRow['shop_id'], 'brand_id' => $ticketRow['brand_id']);
					return array('status' => 200, 'data' => $updateArr);
				} else {
					return array('status' => 202);
				}
			}
		}
		//优惠券
		elseif($mark == 'coupon') {
				$param1 = $param = array();
				if($ticketRow['ticket_uuid'])
				{
					$param1 = array('ID' => $ticketRow['ticket_uuid']);
				}
				$param = array(
						'Name' => $ticketRow['ticket_title'],
						'Price' => $ticketRow['par_value'],
						'Amount' => $ticketRow['total'],
						'StartDate' => datex($ticketRow['start_time'], 'Y-m-d H:i:s'),
						'EndDate' => datex($ticketRow['end_time'], 'Y-m-d H:i:s'),
						'UseStartDate' => datex($ticketRow['valid_stime'], 'Y-m-d H:i:s'),
						'UseEndDate' => datex($ticketRow['valid_etime'], 'Y-m-d H:i:s'),
						'PromoUrl' => $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/home/ticket/show/tid/' . $tid,
						'PromoImglist' => $this->getTicketImgByTicketId($tid),
						'Remark' => $ticketRow['ticket_summary'],
						'MerchantCommonID' => $ticketRow['shop_id'],
						'MerchantName' => $ticketRow['shop_name'],
						'isShow' => $ticketRow['is_show'],
						'CommonID' => $tid,
						'Status' => $ticketRow['is_auth'],
						'Code' => $ticketRow['is_sale'] == 1 ? $ticketRow['sale_code'] : '',
						'City' => $city
				);
				$param = array_merge($param1, $param);
				$authResArray = Custom_AuthTicket::createTickets($param);
				if(!$ticketRow['ticket_uuid']) { //券新建
					if($authResArray['code'] == 1) {					
						$updateArr = array('ticket_uuid' => $authResArray['message']);	
						return array('status' => 100, 'data' => $updateArr);			
					} else {
						return array('status' => 101);
					}				
				} else { //券编辑
					if($authResArray['code'] == 2) {
						return array('status' => 200);
					} else {
						return array('status' => 201);
					}				
				}
			}
	}
	
	public function recommend($getData) {
		$arr = array(
				'come_from_id' => $getData['id'],
				'come_from_type' => 2,
				'title' => $getData['title'],
				'summary' => $getData['summary'],
				'pos_id' => $getData['pos_id'],
				'www_url' => '/home/ticket/show/tid/' . $getData['id'],
				'img_url' => $getData['img_url'],
				'created' => REQUEST_TIME,
				'pmark' => 'voucher',
				'cmark' => 'voucher_view',
				'city' => $this->_ad_city
		);
		return $this->_db->insert('oto_recommend', $arr);
	}
	
	public function checkRecommend($come_from_id, $pos_id) {
		return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '2' and `pos_id` = '{$pos_id}' limit 1") == 1;
	}	
		
 	public function addActivity($activity_name, $user_name) {
 		$user_id = $this->getUserIdByUserName($user_name);
 		
 		$sql = "select 1 from `oto_activity` where `activity_name` = '{$activity_name}' and `user_id` = '{$user_id}' limit 1";
 		if($this->_db->fetchOne($sql) == 1) {
 			return true;	
 		}
 		
 		$arr = array(
 				'user_id' =>  $user_id,
 				'activity_name' => $activity_name,
 				'created'  => REQUEST_TIME
 				);
 		$queryResult = $this->_db->insert('oto_activity', $arr);
 		return $queryResult;
 	}
 	
 	public function check_user($user_name) {
 		$userInfo = $this->select("`user_name` = '{$user_name}'", 'oto_user', 'user_id, user_type', '', true);
 		if ($userInfo['user_type'] == 2) {
 			$conditions = "`user_id` = '{$userInfo['user_id']}' AND `shop_status` <> '-1'";
 			return $this->_db->fetchOne("select 1 from oto_shop where $conditions limit 1") == 1;
 		} 
 		return false;
 	}
 	
 	public function check_user_shop($user_name) {
 		$userInfo = $this->select("`user_name` = '{$user_name}'", 'oto_user', 'user_id, user_type', '', true);
 		if ($userInfo['user_type'] == 2) {
 			$sql = "select 1 from
		 			oto_user_shop_competence CO
		 			left join oto_shop SH on CO.shop_id = SH.shop_id
		 			where CO.user_id = '{$userInfo['user_id']}' and SH.shop_status <> '-1' limit 1";
 			return $this->_db->fetchOne($sql) == 1;
 		}
 		return false; 		
 	}
 	
 	public function getShopListByBusUser($user_name, $shop_name = null) {
 		if(!empty($shop_name)) {
 			$where .= " and SH.shop_name like '%" . trim($shop_name) . "%'";
 		}
 		$userInfo = $this->select("`user_name` = '{$user_name}'", 'oto_user', 'user_id, user_type', '', true);
 			
 		$sql = "select SH.shop_id, SH.shop_name from
		 		oto_user_shop_competence CO
		 		left join oto_shop SH on CO.shop_id = SH.shop_id
		 		where CO.user_id = '{$userInfo['user_id']}' {$where} and SH.shop_status <> '-1' and CO.city = '{$this->_ad_city}'";
 		 		 		
 		return $this->_db->fetchAll($sql);
 	}
	
 	public function getTicketImgByTicketId($ticket_id) {
 		$sql = "select img_url from `oto_ticket_img` where `ticket_id` = '{$ticket_id}'";// order by `is_first` desc, `created` asc limit 1
 		return $this->_db->fetchCol($sql);
 	}
 	
 	public function ajax_module_edit($getData){
 		$column = $getData['column'];
 		$id = $getData['id'];
 		$value = $getData['value'];
 		return $this->_db->update($this->_table,array($column => $value), "`ticket_id` = $id");

 	}
 	
 	public function img_ajax_edit($getData){
 		$column = $getData['column'];
 		$id = $getData['id'];
 		$value = $getData['value'];
 		return $this->_db->update('oto_ticket_wap_img',array($column => $value), "`id` = $id");
 	}
 
 	/**
 	 * 获取券关联店铺名称
 	 * @param unknown_type $ticket_id
 	 */
 	public function getRelationShopByTicketId($ticket_id) {
 		$sql = "select SH.shop_id, SH.shop_name from
		 		oto_ticket_shop TT
		 		left join oto_shop SH on TT.shop_id = SH.shop_id
		 		where TT.ticket_id = '{$ticket_id}'
		 		order by SH.sequence asc, SH.shop_id asc";
		 		$shopArray = $this->_db->fetchAll($sql);
 		return $shopArray ? $shopArray : array();
 	}
 	
 	public function formatSku(& $getData) {
 		$skuInfo = $skuNameAlias = array();
 		if($getData['dataStr']) {
 			$dataStrArray = explode('&&', trim($getData['dataStr']));
 			list($category_id, $category_name) = explode(',',array_shift($dataStrArray));
 			$skuInfo['category_id'] = $category_id;
 			$skuInfo['category_name'] = $category_name;
 			foreach ($dataStrArray as $skuStr) {
 				$dataSkuArray = explode('^^', $skuStr);
 				foreach($dataSkuArray as $skuKey => $skuPropStr) {
 					$skuPropStrArray = explode(';', $skuPropStr);
 					$prop_value_id = array_shift($skuPropStrArray);
 					list($PropValueId, $PropValueName) = explode(',', $skuPropStrArray[0]);
 					$skuInfo['Prop'][$prop_value_id][]= array(
 							'PropId' => $prop_value_id,
 							'PropValueId' => $PropValueId,
 							'PropValueName' => $PropValueName
 					);
 					$skuInfo['PropKeyValue']["{$prop_value_id}:{$PropValueId}"]= $PropValueName;
 					$skuInfo['Props'][] =  array(
 							'PropId' => $prop_value_id,
 							'PropValueId' => $PropValueId,
 							'NameAlias' => $PropValueName
 					);
 					
 					$skuNameAlias[$PropValueName] = $PropValueId;
 				}
 			}
 		}
 		
 		
 		
 		$TmpProps = array_values($skuInfo['Prop']);
 		$TmpPropKeyValue = array_flip($skuInfo['PropKeyValue']);
 		$dataRetStrArray = explode(';', $getData['dataRetStr']);
		
 		foreach($dataRetStrArray as $dataRetStrKey => $dataRetStrItem) {
 			$dataRetStrItemArray = explode(',', $dataRetStrItem);
 			$dataRetStrItemArraySlice = array_slice($dataRetStrItemArray, 0, -3);
 			$sliceLen = count($dataRetStrItemArraySlice);
 			
 			foreach ($dataRetStrItemArray as $retStrKey => $retStrValue) {
 				if($retStrKey < $sliceLen) {
 					$skuInfo['PropStr'][$retStrKey]['Unique']= 1;
 				} else {
 					$skuInfo['PropStr'][$retStrKey]['Unique']= 0;
 				}
 				$skuInfo['PropStr'][$retStrKey]['Value'][] = $retStrValue;
 			}
 			
 			foreach($dataRetStrItemArraySlice as $SliceKey => $SliceValue) {
 				foreach($TmpProps[$SliceKey] as $SkuItem) {
 					if($SliceValue == $SkuItem['PropValueName']) {
 						//if($dataRetStrItemArray[$sliceLen + 2] && $dataRetStrItemArray[$sliceLen + 1] && $dataRetStrItemArray[$sliceLen]) {
 							$propStr = '';
 							for($i=0; $i<$sliceLen; $i++) {
 								$propStr .= $TmpPropKeyValue[$dataRetStrItemArray[$i]] . ';';
 							}
 							
 							$skuInfo['SkuProps'][$dataRetStrKey] = array(
	 								'Amount' => $dataRetStrItemArray[$sliceLen + 2],
	 								'Price' => $dataRetStrItemArray[$sliceLen],
	 								'AppPrice' => $dataRetStrItemArray[$sliceLen + 1],
	 								'Props' => substr($propStr,0, -1),
	 						);
 							
 							
 						//}
 					}
 				}
 			}
 		}
 		
 		foreach ($skuInfo['SkuProps'] as $skuKey => $skuItem) {
 			$skuInfo['skuPrice'][$skuItem['Props']] = array('web' => $skuItem['Price'], 'app' => $skuItem['AppPrice']);
 		}
 		
 		$propStrStr = '';
 		foreach($skuInfo['PropStr'] as $strKey => $strItem) {
 			$propTmpStr = '';
 			if($strItem['Unique'] == 1) {
 				$strItemArray = array_unique($strItem['Value']);
 			} else {
 				$strItemArray = $strItem['Value'];
 			}
 			foreach($strItemArray as $Value) {
 				$propTmpStr .= '"'.$Value.'"' . ',';
 			}
 			$propTmpStr = '[' . substr($propTmpStr, 0, -1) . ']';
 			$propStrStr .= $propTmpStr . ',';
 		}
 		$skuInfo['PropStrStr'] = '[' . substr($propStrStr, 0, -1) . ']';
 		return $skuInfo;	
 	}
 	
 	public function getWapImg($ticket_id) {
 		return $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_wap_img', '*', 'sequence asc, created asc');
 	}
 	
 	public function getImg($ticket_id) {
 		return $this->select("`ticket_id` = '{$ticket_id}'", 'oto_ticket_img', '*', 'is_first desc, created asc');
 	}
 	
 	public function wapUploadImg($wap_img_url, $ticket_id = 0, $shop_id = 0, $user_id = 0) {
 		$sql = $sqlstr = '';
 		$shop_id = intval($shop_id);
 		if(!empty($wap_img_url)) {
 			if(is_array($wap_img_url)) {
	 			foreach($wap_img_url as $img_url) {
	 				if($img_url) {
	 					$sqlstr .= "('{$ticket_id}', '{$user_id}', '{$shop_id}', '{$img_url}', '". REQUEST_TIME ."'), ";
	 				}
	 			}
 			} else {
 				$sqlstr .= "('{$ticket_id}', '{$user_id}', '{$shop_id}', '{$wap_img_url}', '". REQUEST_TIME ."'), ";
 			}
 			
 			if($sqlstr) {
 				$sql = "insert into `oto_ticket_wap_img` (`ticket_id`, `user_id`, `shop_id`, `img_url`, `created`) values " . substr($sqlstr, 0, -2);
 				$query = $this->_db->query($sql);
 				if($query) {
 					$insertId = $this->_db->lastInsertId();
 					return $insertId ? $insertId : 0;
 				}
 			}
 		}
 		
 		return 0;
 	}
 	/**
 	 * 内容图片处理
 	 * @param unknown_type $content
 	 * @param unknown_type $ticket_id
 	 * @param unknown_type $shop_id
 	 * @param unknown_type $action
 	 */
 	public function contentPictureAddEdit($content, $ticket_id, $shop_id, $action = 'add') {
 		$matches = array();
 		preg_match_all( "/<img.*?src=[\\\'| \\\"](http:\/\/.*\/api\/good\/get\-special\-img\-thumb\/iid\/[1-9][0-9]*\/type\/ticket\/w\/740)[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
 		$img_attachment = array();
 		if(!empty($matches[1]))
 		{
 			foreach ($matches[1] as $img_url){
 				$img_attachment[] = str_replace(
 						array(
 								$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/good/get-special-img-thumb/iid/',
 								'/type/ticket/w/740'
 						), '', $img_url);
 			}
 			$img_attachment_ids = '';
 			$img_attachment = array_unique($img_attachment);
 			$img_attachment_ids = implode(',', $img_attachment);
 			if(!empty($img_attachment_ids)) {
 				//编辑先断开图片和券的关联
 				if($action == 'edit') {
 					$this->_db->update('oto_ticket_img', array('ticket_id' => 0), array('ticket_id' => $ticket_id), 0);
 				}
 				//接着把图片和券关联上
 				$sql = "select * from `oto_ticket_img` where `id` in ({$img_attachment_ids})";
 				$imgArr = $this->_db->fetchAll($sql);
 				foreach($imgArr as & $imgRow) {
 					if($imgRow['ticket_id'] == 0) {
 						$this->_db->update('oto_ticket_img', array('ticket_id' => $ticket_id), array('id' => $imgRow['id']));
 					} elseif ($imgRow['ticket_id'] == $ticket_id) {
 		
 					} else {
 						if(!$this->checkTicketImg($ticket_id, $imgRow['user_id'], $shop_id, $imgRow['img_url'])) {
 							$param = array(
 									'ticket_id'  => $ticket_id,
 									'user_id'  	 => $imgRow['user_id'],
 									'shop_id' 	 => $shop_id,
 									'img_url'  	 => $imgRow['img_url'],
 									'created' 	 => REQUEST_TIME
 							);
 							$sql = $this->insertSql('oto_ticket_img', $param);
 							$this->_db->query($sql);
 						}
 					}
 				}
 			}
 		}		 		
 	}
}
