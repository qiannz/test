<?php
class Model_Api_Index extends Base
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
	 * 获取券明细
	 * @param unknown_type $ticket_id
	 * @param unknown_type $ticket_uuid
	 * @return unknown
	 */
	public function getTicktRow($ticket_id, $ticket_uuid) {
		$ticketRow = array();
		$ticketRow = $this->select(
				"`ticket_id` = '{$ticket_id}' and `ticket_uuid` = '{$ticket_uuid}'",
				'oto_ticket',
				'ticket_id, ticket_title, ticket_type, user_id, user_name, shop_id, shop_name, valid_stime, valid_etime, ticket_status, is_auth, rebates',
				'',
				true
		);
		return $ticketRow;
	}
	/**
	 * 开始执行返利
	 * @param unknown_type $userRow
	 * @param unknown_type $ticketRow
	 * @param unknown_type $captcha
	 * @param unknown_type $ip
	 */
	public function startedRebates(& $userClerkRow, &$userClientRow, & $ticketRow, $ticket_id, $shop_id, $captcha, $ip, $OrderNo) {
		$resultArray = array();
		$where = " and `type` = '1'";
		$where .= " and `user_id` = '{$userClerkRow['user_id']}'";
		
		if($ticketRow['ticket_id']) {
			$where .= " and `ticket_id` = '{$ticketRow['ticket_id']}'";
		}
		
		if($ticketRow['shop_id']) {
			$where .= " and `shop_id` = '{$ticketRow['shop_id']}'";
		}
		
		$where .= " and `captcha` = '{$captcha}'";
		
		$sql = "select 1 from `oto_task_clerk_coupon` where 1 {$where} limit 1";
		if($this->_db->fetchOne($sql) == 1) {
			$resultArray['msg'] = 'fail';
			$resultArray['res'] = 304;
			$resultArray['extra'] = '重复返利';
		} else {
			
			//判断是否自定义买单
			$mark = $this->getTicketSortById($ticketRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
			//是自定买单，设置了返利金额百分比，自定义金额大于0
			if( $mark == 'selfpay' && ( $ticketRow['rebates'] > 0 ) && ( $ticketRow['OrderPrice'] > 0 ) ) {
				//计算返利金额
				$award = ( intval($ticketRow['rebates']) * $ticketRow['OrderPrice'] ) / 100 ;
				$award = sprintf("%.1f", $award);
				
				if($award > 0) {
					//插入返利日志表
					$query = $this->_db->insert('oto_task_clerk_coupon', array(
							'type' => 1,
							'user_id' => $userClerkRow['user_id'],
							'user_name' => $userClerkRow['user_name'],
							'ticket_id' => $ticketRow['ticket_id'],
							'shop_id' => $ticketRow['shop_id'],
							'award' => $award,
							'source_user_id' => $userClientRow['user_id'],
							'captcha' => $captcha,
							'ip' => $ip,
							'order_no' => $OrderNo ? $OrderNo : '',
							'created' => REQUEST_TIME
					));
					if($query) {
						$resultArray['msg'] = 'success';
						$resultArray['res'] = 100;
						$resultArray['extra'] = '返利成功';
						//插入返利表
						$logArr = array(
								'user_id' => $userClerkRow['user_id'],
								'award'   => $award,
								'task_type'    => 11,//自定义买单返利
								'day_date' => date('Y-m-d'),
								'created' => REQUEST_TIME
						);
						$this->_db->insert('oto_task_log' , $logArr );
					} else {
						$resultArray['msg'] = 'fail';
						$resultArray['res'] = 305;
						$resultArray['extra'] = '数据插入错误';
					}					
				} else {
					$resultArray['msg'] = 'fail';
					$resultArray['res'] = 400;
					$resultArray['extra'] = '自助买单返利低于0.1元';
				}
				
				return $resultArray;
				
			}
			//>>判断需不需要返利分成
			//店铺明细
			$shopRow = $this->getShopFieldById($shop_id);
			//判断返利分成用户的身份是否发生变化，是否还是店主的身份
			$is_owner= $this->_db->fetchOne("select 1 from `oto_user_shop_competence` where `user_id` = '{$shopRow['divided_user_id']}' and `shop_id` = '{$shop_id}' and `user_type` = '2' limit 1");
			
			//返利分成用户是否发生变化 ， 返利用户是营业员，店主跟被返利用户不是同一个用户
			if($is_owner == 1 && $shopRow['is_divided'] == 1 && $userClerkRow['user_type'] == 3 && $userClerkRow['user_id'] != $shopRow['divided_user_id']) {
				//分成返利
				$dividedRebates = intval($ticketRow['rebates'] * $shopRow['divided_ratio'] / 100);
				//分成后的剩余返利
				$surplusRebates = $ticketRow['rebates'] - $dividedRebates;

				
				//执行分成返利
				if($dividedRebates) {
					$ownerRow = $this->getUserByUserId($shopRow['divided_user_id']);
					$query = $this->_db->insert('oto_task_clerk_coupon', array(
							'type' => 2,
							'user_id' => $ownerRow['user_id'],
							'user_name' => $ownerRow['user_name'],
							'ticket_id' => $ticketRow['ticket_id'],
							'shop_id' => $ticketRow['shop_id'],
							'award' => $dividedRebates,
							'source_user_id' => $userClientRow['user_id'],
							'captcha' => $captcha,
							'details' => '来自' . $userClerkRow['user_name'] . '营业员返利分成',
							'ip' => $ip,
							'created' => REQUEST_TIME
					));	
					
					if($query) {
							$this->_db->insert('oto_task_log' , array(
							'user_id' => $ownerRow['user_id'],
							'award'   => $dividedRebates,
							'task_type'    => 10, //分成返利
							'day_date' => date('Y-m-d'),
							'created' => REQUEST_TIME
						) );
					}				
				}
				
				//执行分成后的返利
				if($surplusRebates) {
					$query = $this->_db->insert('oto_task_clerk_coupon', array(
							'type' => 1,
							'user_id' => $userClerkRow['user_id'],
							'user_name' => $userClerkRow['user_name'],
							'ticket_id' => $ticketRow['ticket_id'],
							'shop_id' => $ticketRow['shop_id'],
							'award' => $surplusRebates,
							'source_user_id' => $userClientRow['user_id'],
							'captcha' => $captcha,
							'details' => '',
							'ip' => $ip,
							'created' => REQUEST_TIME
					));
					
					if($query) {
						$this->_db->insert('oto_task_log' , array(
								'user_id' => $userClerkRow['user_id'],
								'award'   => $surplusRebates,
								'task_type'    => 9, //推荐返利
								'day_date' => date('Y-m-d'),
								'created' => REQUEST_TIME
						) );
					}					
				}
				
				$resultArray['msg'] = 'success';
				$resultArray['res'] = 100;
				$resultArray['extra'] = '返利成功';
				return $resultArray;				
			}
			//<<判断需不需要返利分成
			
			
			//插入返利日志表
			$query = $this->_db->insert('oto_task_clerk_coupon', array(
						'type' => 1,
						'user_id' => $userClerkRow['user_id'],
						'user_name' => $userClerkRow['user_name'],
						'ticket_id' => $ticketRow['ticket_id'],
						'shop_id' => $ticketRow['shop_id'],
						'award' => $ticketRow['rebates'],
						'source_user_id' => $userClientRow['user_id'],
						'captcha' => $captcha,
						'ip' => $ip,
						'order_no' => $OrderNo ? $OrderNo : '',
						'created' => REQUEST_TIME
					));
			if($query) {
				$resultArray['msg'] = 'success';
				$resultArray['res'] = 100;
				$resultArray['extra'] = '返利成功';
				//插入返利表
				$logArr = array(
						'user_id' => $userClerkRow['user_id'],
						'award'   => $ticketRow['rebates'],
						'task_type'    => 9,//推荐返利
						'day_date' => date('Y-m-d'),
						'created' => REQUEST_TIME
				);
				$this->_db->insert('oto_task_log' , $logArr );
			} else {
				$resultArray['msg'] = 'fail';
				$resultArray['res'] = 305;
				$resultArray['extra'] = '数据插入错误';
			}
		}
		return $resultArray;
	}
	/**
	 * 游惠返利
	 * @param unknown_type $getData
	 */
	public function startSelfPayRebates($getData) {
		$resultArray = array();
		$where = "`type` = '1'";   //返利类型（直接返利）
		$captcha = $getData['captcha']; //验证码
		$shop_id = $getData['shop_id']; //店铺ID
		$ticket_id = $getData['ticket_id']; //返利ticket_id
		$ticket_uuid = $getData['ticket_uuid']; //返利ticket_uuid
		$cguid = $getData['cguid'];//营业员用UID
		$uguid = $getData['uguid'];//购买用户UID
		$OrderNo = $getData['OrderNo']; //订单号
		$OrderPrice = $getData['OrderPrice'];//客单价格
		$order_type = $getData['order_type'];//订单来源  WEB, WAP, APP
		$ip = $getData['ip'] ? $getData['ip'] : CLIENT_IP;
		
		//APP自动返利【TO店长】
		if($order_type && $order_type == 'APP') {
			//店铺详情
			$shopRow = $this->getShopFieldById($shop_id);
			//获取店长
			$shopManagerRow = $this->select_one("`shop_id` = '{$shop_id}' and `user_type` = '2'", 'oto_user_shop_commodity');
			//判断是否设置自动返利
			if($shopRow['is_selfpay'] == 0) {
				_sexit('店铺未勾选自动返利', 303.5);
			}
			
			if(!$shopManagerRow['user_id']) {
				_sexit('店长不存在，返利终止', 303.4);
			}
			
			$userInfoRow = $this->getWebUserId($shopManagerRow['user_id']);
			$to_user_id = $shopManagerRow['user_id'];
			$to_user_name = $userInfoRow['user_name'];
			$to_uuid = $userInfoRow['uuid'];
		} 
		else 
		{
			$cguidUserInfo = array();
			if($cguid && $cguid != '00000000-0000-0000-0000-000000000000') {	
				//返利目标户信息
				$cguidUserInfo = $this->getWebUserId($cguid);
			}
			//如果返利目标创建失败
			if(empty($cguidUserInfo['user_id'])) {
				_sexit('返利对象不存在', 301.2);
			}
			
			$to_user_id = $cguidUserInfo['user_id'];
			$to_user_name = $cguidUserInfo['user_name'];
			$to_uuid = $userInfoRow['uuid'];
		}

		//现金券或者游惠信息
		$ticketRow = $this->getTicktRow($ticket_id, $ticket_uuid);
		if(empty($ticketRow)) {
			_sexit('不存在这个现金券或者游惠', 303.2);
		} else {
			//返利金额判断
			if(!$ticketRow['rebates']) {
				_sexit('未设置返利参数值', 303.1);
			}
		}
		//购买用户信息
		$userClerkRow = $this->getWebUserId($uguid);
		if(!$userClerkRow['user_id']) {
			_sexit('购买用户ID获取失败', 303.6);
		} else {
			$from_user_id = $userClerkRow['user_id'];
		}
		
		//计算返利金额
		$award = ( intval($ticketRow['rebates']) * $OrderPrice ) / 100 ;
		$award = sprintf("%.1f", $award);
		if( !$award || $award >= $OrderPrice) {
			_sexit('游惠返利金额不正确', 303.3);
		}
		
		if( $award > 0 && $award < 0.1 ) {
			_sexit('游惠返利金额太小', 303.3);
		}
		
		//判断是否已经返利
		$where .= " and `user_id` = '{$to_user_id}' and `ticket_id` = '{$ticket_id}' and `shop_id` = '{$shop_id}' and `captcha` = '{$captcha}'";
		$sql = "select 1 from `oto_task_clerk_coupon` where {$where} limit 1";
		if($this->_db->fetchOne($sql) == 1) {
			$resultArray['msg'] = '重复返利';
			$resultArray['res'] = 304;
		} 
		//正常返利执行
		else 
		{		
			//插入返利日志表
			$query = $this->_db->insert('oto_task_clerk_coupon', array(
					'type' => 1,
					'user_id' => $to_user_id,
					'user_name' => $to_user_name,
					'ticket_id' => $ticket_id,
					'shop_id' => $shop_id,
					'award' => $award,
					'order_price' => $OrderPrice,
					'source_user_id' => $from_user_id,
					'captcha' => $captcha,
					'ip' => $ip,
					'order_no' => $OrderNo ? $OrderNo : '',
					'created' => REQUEST_TIME
			));
			
			if($query) {
				$resultArray['msg'] = 'success';
				$resultArray['res'] = 100;
				$resultArray['extra'] = array('to_uuid' => $to_uuid);
				//插入返利表
				$logArr = array(
						'user_id' => $to_user_id,
						'award'   => $award,
						'task_type'    => 11,//游惠返利
						'day_date' => date('Y-m-d'),
						'created' => REQUEST_TIME
				);
				$this->_db->insert('oto_task_log' , $logArr );
			} else {
				$resultArray['msg'] = '返利失败';
				$resultArray['res'] = 300;
			}
		}
		
		//返利结果日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'rebate/result/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($resultArray, true), $logPath);
		
		return $resultArray;		
	}
	/**
	 * 
	 * @param unknown_type $userClerkRow 营业员用户
	 * @param unknown_type $userClientRow 购买用户
	 * @param unknown_type $ticket_id 券ID
	 * @param unknown_type $shop_id 店铺ID
	 * @param unknown_type $captcha 验证码
	 * @param unknown_type $ip 
	 * @param unknown_type $award 返利金额
	 * @param unknown_type $task_type 任务类型
	 * @return multitype:string number
	 */
	public function specialStartedRebates(& $userClerkRow, & $userClientRow, $ticket_id, $shop_id, $captcha, $ip, $award, $task_type) {
		$resultArray = array();
		$where = "";
		$where .= " and `user_id` = '{$userClerkRow['user_id']}'";
		$where .= " and `captcha` = '{$captcha}'";
			
		$sql = "select 1 from `oto_task_clerk_coupon` where 1 {$where} limit 1";
		if($this->_db->fetchOne($sql) == 1) {
			$resultArray['msg'] = 'fail';
			$resultArray['res'] = 304;
			$resultArray['extra'] = '重复返利';
		} else {
			//插入返利日志表
			/*
			$query = $this->_db->insert('oto_task_clerk_coupon', array(
					'user_id' => $userClerkRow['user_id'],
					'user_name' => $userClerkRow['user_name'],
					'ticket_id' => intval($ticket_id),
					'shop_id' => intval($shop_id),
					'award' => $award,
					'source_user_id' => $userClientRow['user_id'],
					'captcha' => $captcha,
					'ip' => $ip,
					'created' => REQUEST_TIME
			));
			*/
			$sql = $this->insertSql('oto_task_clerk_coupon', array(
					'user_id' => $userClerkRow['user_id'],
					'user_name' => $userClerkRow['user_name'],
					'ticket_id' => intval($ticket_id),
					'shop_id' => intval($shop_id),
					'award' => $award,
					'source_user_id' => $userClientRow['user_id'],
					'captcha' => $captcha,
					'ip' => $ip,
					'created' => REQUEST_TIME
			));
			$query = $this->_db->query($sql);
			
			if($query) {
				$resultArray['msg'] = 'success';
				$resultArray['res'] = 100;
				$resultArray['extra'] = '返利成功';
				//插入返利表
				$logArr = array(
						'user_id' => $userClerkRow['user_id'],
						'award'   => $award,
						'task_type'    => $task_type,
						'day_date' => date('Y-m-d'),
						'created' => REQUEST_TIME
				);
				$sql = $this->insertSql('oto_task_log', $logArr);
				$this->_db->query($sql);
				//$this->_db->insert('oto_task_log' , $logArr );
			} else {
				$resultArray['msg'] = 'fail';
				$resultArray['res'] = 305;
				$resultArray['extra'] = '数据插入错误';
			}
		}
		return $resultArray;
	}	
	
	public function existCoupon($ticket_id, $ticket_uuid) {
		return $this->_db->fetchOne("select 1 from `oto_ticket` where `ticket_id` = '{$ticket_id}' and `ticket_uuid` = '{$ticket_uuid}' limit 1") == 1;
	}
	
	public function syncTicket($getData) {
		$sale_code = $getData['code'] ? $getData['code'] : '';
		$is_sale = $sale_code ? 1 : 0;
		if($getData['CanCustomPrice'] == 1) {
			$selling_price = 0;
			$par_value = 0;
			$app_price = -1;
		} else {
			$selling_price = $getData['price'];
			$par_value = $getData['originalPrice'] ? $getData['originalPrice'] : 0;
			$app_price = $getData['appPrice'] < 0 ? '-1' : $getData['appPrice'];
		}
		
		$param = array(
					'ticket_title' => Custom_String::HtmlReplace($getData['name']),
					'ticket_summary' => Custom_String::HtmlReplace($getData['remark']),
					'selling_price' => $selling_price,
					'par_value' => $par_value,
					'app_price' => $app_price,
					'start_time' => strtotime($getData['startDate']),
					'end_time' => strtotime($getData['endDate']),
					'valid_stime' => strtotime($getData['useStartDate']),
					'valid_etime' => strtotime($getData['useEndDate']),
					'total' => intval($getData['amount']),
					'limit_count' => intval($getData['limitCount']),
					'limit_unit' => $getData['limitUnit'],
					'is_auth' => intval($getData['status']),
					'is_show' => intval($getData['isShow']),
					'can_share' => intval($getData['CanShare']),
					'is_sale' => $is_sale,
					'sale_code' => $sale_code
				);
		
		$updateResult = $this->_db->update('oto_ticket', $param, array(
					'ticket_id' => $getData['tid'],
					'ticket_uuid' => $getData['guid']
				));
		if($updateResult) {
			$this->_db->update('oto_ticket_info', 
					array(
						'expiration_minute' => $getData['PayTimeOut'], 		//支付失效时间，单位：分钟
						'count_times' => $getData['BuyCountTimes'], 		//中奖倍率(一份发几个中奖号码)，
						'buy_discount' => $getData['BuyDiscount'], 			//购买优惠
					), 
					array(
						'ticket_id' => $getData['tid']
					)
			);
			return true;
		}
		return false;
	}
	
	public function newUserByUuid($uuid) {
		$userRow = $this->select("`uuid` = '{$uuid}'", 'oto_user', 'user_id, uuid, user_name, user_type, user_status', '', true);
		$authUserRow = Custom_AuthLogin::get_user_by_uuid($uuid);
		if($authUserRow['GetUserInfosResult'] == 1) {			
			$userRow['GroupTitle'] = $authUserRow['userInfo']['GroupTitle'];
			$userRow['UserSex'] = $authUserRow['userInfo']['UserSex'];
			$userRow['CityTitle'] = $authUserRow['userInfo']['CityTitle'];
			$userRow['MP'] = $authUserRow['userInfo']['MP'];
			$userRow['Avatar50'] = $authUserRow['userInfo']['userField']['Avatar50'];
			$userRow['Avatar30'] = $authUserRow['userInfo']['userField']['Avatar30'];
			$user_name = $authUserRow['userInfo']['UserName'];
			if(!$userRow['user_id']) {
				$sql = "insert ignore into `oto_user` (`uuid`, `user_name`, `created`) values ('{$uuid}', '{$user_name}', '".REQUEST_TIME."')";
				$this->_db->query($sql);
				$insert_id = $this->_db->lastInsertId();
				if($insert_id > 1) {
					$userRow['uuid'] = $uuid;
					$userRow['user_id'] = $insert_id;
					$userRow['user_name'] = $user_name;
					$userRow['user_type'] = 1;
					$userRow['user_status'] = 0;
				}
			}
			return $userRow;
		}
		return false;
	}
	
	public function discountNotice() {
		//找出第二天未发送推送通知的记录
		$date = datex(strtotime("+1 day"),"Y-m-d");
		$start = strtotime($date." 00:00:00");
		$end   = strtotime($date." 23:59:59");
		$sel_sql = "SELECT * FROM `discount_notice`
					WHERE `stime`>={$start} AND `stime`<={$end} AND `is_send` = 0
					ORDER BY `stime` ASC
					LIMIT 100";
		//修改状态为正在处理
		$noticeList = $this->_db->fetchAll($sel_sql);
		if( empty($noticeList) ){
			return;
		}
		$nids = "";
		foreach ($noticeList as $row){
			$nids .= $row["id"].",";
		}
		$nids = trim($nids,",");
		$sql = "UPDATE `discount_notice` SET `is_send` = 1 WHERE `id` IN ({$nids})";
		$flag = $this->_db->query($sql);
		if( !$flag ){
			return;
		}
		foreach( $noticeList as $row ) {
			$message = "你关注的折扣信息:{$row['title']}，明天".datex("H:i")."开始啦";
			$data = array(
				'user_id' => $row['user_id'],
				'frid' => $row["discount_id"],
				'message' => $message,
				'type' => 'system',
				'opentype'=>'discount_view',
				'notice_type' => 3
			);
			Model_Api_Message::getInstance()->sendNotice($data);
		}
	}
	
	//一元购活动 OR 秒杀活动通知
	public function activityNotice(){		
		$time = REQUEST_TIME;
		$max_time = $time + 1200;
		$min_time = $time + 600;
		
		$sql = "SELECT * 
				FROM `oto_ticket_prompt`
				WHERE `is_send` = '0' AND (`start_time` between {$min_time} AND {$max_time})
				limit 100";
		//修改状态为1
		$noticeList = $this->_db->fetchAll( $sql );
		if( empty($noticeList) ){
			return;
		}
		$nids = "";
		foreach ($noticeList as $row){
			$nids .= $row["prompt_id"].",";
		}
		$nids = trim($nids,",");
		$sql = "UPDATE `oto_ticket_prompt` SET `is_send` = 1 WHERE `prompt_id` IN ({$nids})";
		$flag = $this->_db->query($sql);
		if( !$flag ){
			return;
		}
		$ticketsort = $this->getTicketSortById(0, 'ticketsort');
		foreach( $noticeList as $row ) {
			$sort_mark = "";
			if( $row["ticket_type"] > 0 ){
				$sort_mark = $ticketsort[$row['ticket_type']]['sort_detail_mark'];
			}
			$opentype = "";
			$www_url = "";
			if( $sort_mark == "spike" ){//根据分类显示通知内容
				$message = "你关注的秒杀活动:{$row['ticket_title']}，马上就要开始啦";
				$opentype = "activity_come_and_grab_view";
				$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL']."/active/comeandgrap";
			}else{
				$message = "你关注的一元众筹活动:{$row['ticket_title']}，马上就要开始啦";
				$opentype = "activity_one_yuan_purchase_view";
				$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL']."/active/oneyuanpurchase";
			}
			
			$data = array(
					'user_id' => $row['user_id'],
					'message' => $message,
					'frid' => $row["ticket_id"],
					'type' => 'system',
					'opentype'=> $opentype,
					'www_url'=> $www_url,
					'notice_type' => 3
			);
			
			Model_Api_Message::getInstance()->sendNotice($data);
		}
	}
	
	public function sendPreNotice(){
		//移除历史遗留的已处理的消息
		$sel_sql = " SELECT * FROM `oto_pre_notice` WHERE `is_handle`='1' AND `user_id`<>'0'";
		$oldList = $this->_db->fetchAll($sel_sql);
		$this->handleSrchRes($oldList);
		//找出未处理的通知（券未过期的）
		$sel_sql = "
					SELECT *
					FROM `oto_pre_notice`
					WHERE `is_handle`= '0' AND (`type`='system' OR `opentype`!='voucher_view')
					UNION
					SELECT *
					FROM `oto_pre_notice`
					WHERE `is_handle`= '0' AND `type`='voucher' AND `opentype`='voucher_view' AND `start_time`<'".REQUEST_TIME."' AND `end_time`>'".REQUEST_TIME."'
					LIMIT 50";
		$noticeList = $this->_db->fetchAssoc($sel_sql);
		if( empty($noticeList) ){
			return;
		}
		$configArray = @include VAR_PATH . 'config/config.php';
		$power = $configArray['APP_PUSH_NOTICE'];
		foreach ( $noticeList as $row ) {
			if( $row["type"] == "system" && $row["start_time"] > 0 && $row["end_time"] > 0 ){
				if( $row["start_time"] > REQUEST_TIME ){
					unset($noticeList[$row["message_id"]]);
					continue;
				}
			}
			if( $row["is_push"] == 1 && $power == 1 ) {
				$retCode = Model_Api_Message::getInstance()->pushPreNotice($row);
				if( $retCode == 200  ){
					$sql = "UPDATE `oto_pre_notice` SET `is_handle` = 0 WHERE `message_id` = '{$row["message_id"]}'";
					$this->_db->query($sql);
					unset($noticeList[$row["message_id"]]);
					continue;
				}
			}
			if( $row["user_id"] > 0 ) {
				Model_Api_Message::getInstance()->sendPersonalNotice($row["user_id"], array($row));
			}
		}
		
		$this->handleSrchRes($noticeList);
	}
	//处理搜索出来的接口
	public function handleSrchRes( $noticeList ){
		//更新预放通知信息
		$upt_ids = $insert_sql = $del_ids = "";
		foreach ( $noticeList as $row ) {
			if( $row["user_id"] == 0 ) {//群发(OR后台添加的通知,先滤掉)不在删除操作
				$upt_ids .= $row["message_id"].",";
			} else {
				$row["message"] = mysql_escape_string($row["message"]);
				$insert_sql .= "('".implode("','", array_values($row))."'),";
				$del_ids .= $row["message_id"].",";
			}
		}
		if( $insert_sql ) {//更新点对点信息到备份表并删除原始记录
			$insert_sql = trim($insert_sql,",");
			$insert_sql = "INSERT INTO `oto_pre_notice_backup`(`".implode("`,`", array_keys($row))."`) VALUES {$insert_sql} ON DUPLICATE KEY UPDATE `created`=VALUES(`created`);";
			$this->_db->query( $insert_sql );
			$del_ids = trim($del_ids,",");
			$this->_db->query( "DELETE FROM `oto_pre_notice` WHERE `message_id` IN ({$del_ids})");
		}
		if( $upt_ids ) {//更新群发信息为已处理状态
			$upt_ids = trim($upt_ids,",");
			$sql = "UPDATE `oto_pre_notice` SET `is_handle` = 1 WHERE `message_id` IN ({$upt_ids})";
			$this->_db->query($sql);
		}
	}
}