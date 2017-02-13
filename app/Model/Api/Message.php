<?php
class Model_Api_Message extends Base
{
	private static $_instance;
	private $_key;
	private $_type;
	private $_dDayTime;
	
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
		$this->_type = array('user', 'good', 'brand', 'shop', 'market', 'voucher', 'buygood', 'system', 'commodity' , 'shopping' , 'consult' , 'discount');
		//个人信息显示最多30天以内
		$this->_dDayTime = strtotime('-30 day');
	}
	
	public function getNoticeList($getData, $is_cache = false) {
		$ticket_id = intval($getData['tid']);
		$key = $this->_key . 'get_api_notice_list_' . $ticket_id;
		$data = $this->getData($key);
		if(empty($data) || !$is_cache) {
			$sql = "select * from `oto_ticket_notice` where `ticket_id` = '{$ticket_id}' and `is_del` = '0' order by notice_id asc";
			$data = $this->_db->fetchAll($sql);
			$this->setData($key, $data);
		}
		return $data;
	}
	
	public function addNotice($getData, & $userInfo) {
		$ip = !$getData['ip'] ? CLIENT_IP : $getData['ip'];
		$param = array(
					'ticket_id' => intval($getData['tid']),
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
					'avator' => $userInfo['Avatar50'],
					'content' => Custom_String::HtmlReplace(urldecode($getData['content']), 1),
					'ip' => $ip,
					'created' => REQUEST_TIME
				);
		//重置缓存
		$this->_file->del($this->_key . 'get_api_notice_list_' . intval($getData['tid']));
		return $this->_db->insert('oto_ticket_notice', $param);
	}
	
	public function delNotice($getData, & $userInfo) {
		$ticket_id = intval($getData['tid']);
		$notice_id = intval($getData['nid']);
		//重置缓存
		$this->_file->del($this->_key . 'get_api_notice_list_' . $ticket_id);
		return $this->_db->update('oto_ticket_notice', array('is_del' => 1), array('ticket_id' => $ticket_id, 'user_id' => $userInfo['user_id'], 'notice_id' => $notice_id));
	}
	
	public function getPersonalMessage($getData, & $userInfo) {
		$from_id = intval($getData['frid']);
		$type = $getData['type'];
		$tid = $getData['tid'];
		
		$sql = "select * 
				from `oto_message_post` 
				where `from_id` = '{$from_id}' and `type` = '{$type}' and `tid` = '{$tid}' and `is_del` = '0' 
				order by pid asc";
		$data = $this->_db->fetchAll($sql);
		foreach ($data as & $row) {
			if($row['user_id'] == $userInfo['user_id']) {
				$row['is_me'] = 1;
			} else {
				$row['is_me'] = 0;
			}
			$row["created_format"] = datex($row['created'],'Y-m-d H:i:s');
		}
		return $data ? $data : array();
	}
	
	public function addNewPersonalMessage($getData, & $userInfo) {
		$ip = !$getData['ip'] ? CLIENT_IP : $getData['ip'];
		$from_id = intval($getData['frid']);
		$type = $getData['type'];
		$question = Custom_String::HtmlReplace(urldecode($getData['qst']), 1);
		$shop_id = $this->getShopIdByGoodType($type, $from_id);
		$threadParam = array(
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
					'avator' => $userInfo['Avatar50'],
					'question' => $question,
					'type' => $type,
					'shop_id' => $shop_id,
					'from_id' => $from_id,
					'ip' => $ip,
					'created' => REQUEST_TIME,
					'updated' => REQUEST_TIME
				);
		$tid = $this->_db->insert('oto_message_thread', $threadParam);
		
		if($tid) {
			$postParam = array(
						'tid' => $tid,
						'user_id' => $userInfo['user_id'],
						'user_name' => $userInfo['user_name'],
						'avator' => $userInfo['Avatar50'],
						'question' => $question,
						'type' => $type,
						'first' => 1,
						'from_id' => $from_id,
						'shop_id' => $shop_id,
						'ip' => $ip,
						'created' => REQUEST_TIME
					);
			$this->_db->insert('oto_message_post', $postParam);
			return $tid;
		}
		
		return false;
	}
	
	public function appendPersonalMessage($getData, & $userInfo) {
		$ip = !$getData['ip'] ? CLIENT_IP : $getData['ip'];
		$from_id = intval($getData['frid']);
		$tid = intval($getData['tid']);
		$type = $getData['type'];
		$question = Custom_String::HtmlReplace(urldecode($getData['qst']), 1);
		$shop_id = $this->getShopIdByGoodType($type, $from_id);
		
		if(!$tid) {
			return false;	
		}
		
		$postParam = array(
				'tid' => $tid,
				'user_id' => $userInfo['user_id'],
				'user_name' => $userInfo['user_name'],
				'avator' => $userInfo['Avatar50'],
				'question' => $question,
				'type' => $type,
				'from_id' => $from_id,
				'shop_id' => $shop_id,
				'ip' => $ip,
				'created' => REQUEST_TIME
		);
		$pid = $this->_db->insert('oto_message_post', $postParam);
		
		if($pid) {
			$threadParam = array(
						'floors' => $this->_db->fetchOne("select count(pid) from `oto_message_post` where `tid` = '{$tid}'"),
						'repler' => '',
						'reply_time' => '0',
						'updated' => REQUEST_TIME
					);
			
			return $this->_db->update('oto_message_thread', $threadParam, array('tid' => $tid, 'from_id' => $from_id));
		}
		
		return false;		
	}
	
	public function getClerkNoticeList($getData, & $userInfo) {
		$from_id = intval($getData['frid']);
		$type = $getData['type'];
		$rtype = !$getData['rtype'] ? 0 : intval($getData['rtype']);
		
		switch ($rtype) {
			//全部
			case 0:
				$sql = "select * from `oto_message_thread` 
						where `from_id` = '{$from_id}' and `type` = '{$type}' and `is_del` = '0'
						order by `updated` desc";
				break;
			//未回复
			case 1:
				$sql = "select * from `oto_message_thread`
						where `from_id` = '{$from_id}' and `type` = '{$type}' and `reply_time` < `updated` and `is_del` = '0'
						order by `updated` desc";
				break;
			//已回复
			case 2:
				$sql = "select * from `oto_message_thread`
						where `from_id` = '{$from_id}' and `type` = '{$type}' and `reply_time` > `updated` and `is_del` = '0'
						order by `updated` desc";
				break;
		}
		
		if($sql) {
			$clerkNoticeArray = $this->_db->fetchAll($sql);
			foreach($clerkNoticeArray as & $item) {
				$userRow = $this->getUserByUserId($item['user_id'], 'uuid');
				$item['uuid'] = $userRow['uuid'];
				$item['createdx'] = datex($item['created'], 'Y-m-d H:i:s');
			}
		}
		
		return $clerkNoticeArray ? $clerkNoticeArray : array();
	}
	
	public function getMyPersonalMessage($getData, & $userInfo, $pagesize = PAGESIZE) {
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$start = ($page - 1) * $pagesize;
		$table = $this->getNoticeTableNameByUserId($userInfo['user_id']);
		$db = Core_DB::get('message', null, true);
		
		//置为已读状态
		if($page == 1) {
			$db->update($table, array('is_read' => 1), array('user_id' => $userInfo['user_id'], 'created >' => $this->_dDayTime), 0);
		}
		//返回用户信息
		$sql = "select * from `{$table}` where `user_id` = '{$userInfo['user_id']}' and `created` > '{$this->_dDayTime}' order by `created` desc";
		$data = $db->limitQuery($sql, $start, $pagesize);
		foreach ($data as & $myPersonalRow) {
			if(!$myPersonalRow['charter_user_id']) {
				$myPersonalRow['charter_member'] = '名品街';
				$myPersonalRow['charter_member_avator'] = 'http://buy.mplife.com/data/app/default_avatar.png';
			}
		}
		return $data ? $data : array();
	}
	
	public function getMyPersionUnReadMessageNum(& $userInfo) {
		$table = $this->getNoticeTableNameByUserId($userInfo['user_id']);
		$db = Core_DB::get('message', null, true);
		return $db->fetchOne("select count(id) from `{$table}` where `user_id` = '{$userInfo['user_id']}' and `is_read` = '0' and `created` > '{$this->_dDayTime}'");
	}
	
	public function getMyPersionUnReadMessageNumByType(& $userInfo , $noticeType , $city ){
		$table = $this->getNoticeTableNameByUserId($userInfo['user_id']);
		$db = Core_DB::get('message', null, true);
		if( $noticeType == 1  ){//活动
			$sql = "SELECT COUNT(*) 
					FROM `{$table}` AS A 
					LEFT JOIN `oto_ticket` AS B 
					ON A.type = 'voucher' AND B.ticket_id = A.from_id
					WHERE A.`city`='{$city}' AND A.`user_id`='{$userInfo['user_id']}' AND `notice_type`='{$noticeType}' AND A.`is_read`=0 
					AND ((A.type='voucher' AND B.end_time>'".REQUEST_TIME."') OR A.type!='voucher' )";
			return $db->fetchOne($sql);
		}else if( $noticeType == 2 ){//私信
			$sql = "SELECT COUNT(*) FROM (
										SELECT count(id) FROM `{$table}` 
										WHERE `city`='{$city}' AND `user_id` = '{$userInfo['user_id']}' AND `notice_type`='{$noticeType}' AND `is_read`=0  
										GROUP BY `opentype`,`from_id`,`charter_user_id`
								) AS A";
			return $db->fetchOne($sql);
		}else if( $noticeType == 3 ){//通知
			$sql = "SELECT COUNT(*) 
					FROM `{$table}` AS A
					LEFT JOIN `oto_ticket` AS B
					ON A.type = 'voucher' AND B.ticket_id = A.from_id
					WHERE A.`city`='{$city}' AND A.`user_id` = '{$userInfo['user_id']}' AND A.`notice_type`='{$noticeType}' AND A.`is_read`=0
					AND ((A.type='voucher' AND B.end_time>'".REQUEST_TIME."') OR A.type!='voucher' ) ";
			return $db->fetchOne($sql);
		}
	}
	
	public function getMyPersionUnReadMessageListByType( $getData, & $userInfo, $city , $pagesize = PAGESIZE ){
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$start = ($page - 1) * $pagesize;
		$table = $this->getNoticeTableNameByUserId($userInfo['user_id']);
		$db = Core_DB::get('message', null, true);
		$noticeType = $getData["notice-type"];
		if( $noticeType == 1  ) {//活动
			$sql = "SELECT A.*
					FROM `{$table}` AS A
					LEFT JOIN `oto_ticket` AS B
					ON A.type = 'voucher' AND B.ticket_id = A.from_id
					WHERE A.`city`='{$city}' AND A.`user_id`='{$userInfo['user_id']}' AND `notice_type`='{$noticeType}' 
					AND ((A.type='voucher' AND B.end_time>'".REQUEST_TIME."') OR A.type!='voucher' ) 
					ORDER BY A.`is_read` ASC,A.`created` DESC";
		} else if ( $noticeType == 2 ){//私信
			$sql = "SELECT A.* FROM (
						SELECT * FROM `{$table}` 
						WHERE `city`='{$city}' AND `user_id` = '{$userInfo['user_id']}' AND `notice_type`='{$noticeType}' ORDER BY `created` DESC 
					) AS A 
					GROUP BY A.`opentype`,A.`from_id`,A.`charter_user_id` 
					ORDER BY A.`is_read` ASC,A.`created` DESC";
		} else { //通知
			$noticeType = 3;
			$sql = "SELECT A.* 
					FROM `{$table}` AS A
					LEFT JOIN `oto_ticket` AS B
					ON A.type = 'voucher' AND B.ticket_id = A.from_id
					WHERE A.`city`='{$city}' AND A.`user_id` = '{$userInfo['user_id']}' AND A.`notice_type`='{$noticeType}'
					AND ((A.type='voucher' AND B.end_time>'".REQUEST_TIME."') OR A.type!='voucher' ) 
					ORDER BY A.`is_read` ASC,A.`created` DESC";
		}
		$list = $db->limitQuery($sql, $start, $pagesize);
		foreach ( $list as &$row){
			$row["num"] = 0;
			if( $noticeType == 2 ){
				$sql = "SELECT COUNT(*) 
						FROM `{$table}` 
						WHERE `city`='{$city}' AND `user_id`='{$row["user_id"]}' AND `notice_type`='{$noticeType}' AND `is_read`=0 AND `opentype`='{$row["opentype"]}' AND `from_id`='{$row["from_id"]}' AND `charter_user_id`='{$row["charter_user_id"]}'";
				$row["num"] = $db->fetchOne($sql);
			}
			if(!$row['charter_user_id']) {
				$row['charter_member'] = '名品街';
				$row['charter_member_avator'] = 'http://buy.mplife.com/data/app/default_avatar.png';
			}
			$row['www_url'] = "";
			switch ( $row['opentype'] ){
				case 'sale_view':
					$row['www_url'] = "http://promo.mplife.com/mp/{$row["from_id"]}/wap.html";
					break;
				case 'activity_come_and_grab':
				case 'activity_come_and_grab_view':
					$row['www_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL']."/active/comeandgrap";
					break;
				case 'activity_one_yuan_purchase':
				case 'activity_one_yuan_purchase_view':
					$row['www_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL']."/active/oneyuanpurchase";
					break;
			}
			$row["created"] = Custom_Time::getTime4( $row["created"] );
		}
		return $list ? $list : array();
	}
	
	
	public function updateReadValue( $id , $user_id , $notice_type = 0 ){
		$db = Core_DB::get('message', null, true);
		$table = $this->getNoticeTableNameByUserId($user_id);
		if( $id > 0 && $notice_type == 0 ){//指定消息置为已读
			$noticeRow = $db->fetchRow("SELECT * FROM `{$table}` WHERE `id`='{$id}'");
			if( $noticeRow["notice_type"] == 2 ){
				$where = array("user_id"=>$user_id,
						"opentype"=>$noticeRow["opentype"],
						"notice_type"=>$noticeRow["notice_type"],
						"from_id"=>$noticeRow["from_id"],
						"charter_user_id"=>$noticeRow["charter_user_id"]);
				$db->update($table,array("is_read"=>1),$where,0);
			}else{
				$db->update($table,array("is_read"=>1),array("id"=>$id));
			}
		}else if( $notice_type > 0 ){//指定类型的消息置为已读
			$where = array("user_id"=>$user_id,
							"notice_type"=>$notice_type);
			$db->update($table,array("is_read"=>1),$where,0);
		}else if( $notice_type == 0 ){//全部置为已读
			$where = array("user_id"=>$user_id);
			$db->update($table,array("is_read"=>1),$where,0);
		}
	}
	
	
	public function replyPersonalMessage($getData, & $userInfo) {
		$ip = !$getData['ip'] ? CLIENT_IP : $getData['ip'];
		$from_id = intval($getData['frid']);
		$tid = intval($getData['tid']);
		$type = $getData['type'];
		$question = Custom_String::HtmlReplace(urldecode($getData['qst']), 1);
		$shop_id = $this->getShopIdByGoodType($type, $from_id);
		$postParam = array(
				'tid' => $tid,
				'user_id' => $userInfo['user_id'],
				'user_name' => $userInfo['user_name'],
				'avator' => $userInfo['Avatar50'],
				'question' => $question,
				'type' => $type,
				'position' => 'R',
				'from_id' => $from_id,
				'shop_id' => $shop_id,
				'ip' => $ip,
				'created' => REQUEST_TIME
		);
		$pid = $this->_db->insert('oto_message_post', $postParam);
		
		if($pid) {
			$threadParam = array(
					'floors' => $this->_db->fetchOne("select count(pid) from `oto_message_post` where `tid` = '{$tid}'"),
					'repler' => $userInfo['user_name'],
					'reply_time' => REQUEST_TIME,
					'updated' => REQUEST_TIME
			);
				
			$this->_db->update('oto_message_thread', $threadParam, array('tid' => $tid, 'from_id' => $from_id));
			
			//放到预放通知里
			$openType = "";
			switch ($type) {
				case 'voucher':
					$openType = "voucher_advisory";
					break;
				case 'buygood':
					$openType = "nine_buy_advisory";
					break;
				case 'commodity':
					$openType = "commodity_advisory";
					break;
			}
			$param = array(
					"tid"=>$getData["tid"],
					"charter_user_id"=>$userInfo['user_id'],
					"charter_member"=>$userInfo['user_name'],
					"charter_member_avator"=>$userInfo['Avatar50'],
					"message_type"=>"reply"
					);
			$this->addPreNotice($type, $openType, $from_id , $param);
			
			return true;
		}
		
		return false;		
	}
	/**
	 * 发送通知
	 */
	public function sendNotice($data, $userInfo = null, $messageType = null) {
		$errMsg = array();	
		//通知日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/sendMessage/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($data, true) . var_export($userInfo, true), $logPath);

		$type = $data['type'];
		if(!$type || !in_array($type, $this->_type)) {
			$errMsg[] = 'type:通知类型错误';
		}
		//通知内容不能为空
		$message = Custom_String::HtmlReplace(urldecode($data['message']), 1);
		if(!$message) {
			$errMsg[] = 'message:通知内容为空';
		} elseif (mb_strlen($message, 'utf8') > 500) {
			$errMsg[] = 'message:通知内容限制500个字符，汉字算一个字符';
		}
		//来源ID
		$from_id = $data['frid'] ? intval($data['frid']) : 0;
		//标记为商户通知
		$is_auth = $data['is_auth'] ? intval($data['is_auth']) : 0;
		//来源城市
		$city = !$data['city'] ? $this->_city : $data['city'];
		
		if($type == 'system') {
			//发送给谁不能为空
			$user_id = $data['user_id'];
			if(!$user_id) {
				$errMsg[] = '通知用户ID不能为空';
			}		
			//检测订单号是否存在
			if( "home_order" == $data["opentype"] ) {
				$order_no = $data['order_no'];
				if(!$order_no) {
					$errMsg[] = 'order_no:订单号为空';
				}
			}		
		} else { 
			if(!$userInfo['user_id']) {
				$errMsg[] = 'user_id:通知发起人用户ID为空';
			}
			
			if(!$userInfo['user_name']) {
				$errMsg[] = '通知发起人用户名为空';
			}
			
			if(!$userInfo['Avatar50']) {
				$errMsg[] = 'avator:通知发起人头像为空';
			}
			
			if(!$from_id) {
				$errMsg[] = 'frid:通知来源ID为空';
			}
		}
				
		
		switch($type) {
			case 'user':
			case 'good':
			case 'brand':
				
				break;
			case 'system':
					$param = array(
							'message' => $message,
							'type' => $type,
							'opentype' => $data["opentype"] ? $data["opentype"] : '',
							'notice_type'=> $data["notice_type"],
							'charter_user_id' => 0,
							'charter_member' => '',
							'charter_member_avator' => '',
							'from_id' => $from_id,
							'order_no' => $order_no ? $order_no : '',
							'is_auth' => $is_auth,
							'city'  => $city,
							'created' => REQUEST_TIME
					);
					$openparam = array(
									'orderno' => $order_no ? $order_no : '' ,
									'frid' => $from_id
							);
					if( $data["opentype"] == "activity_come_and_grab_view" || $data["opentype"] == "activity_one_yuan_purchase_view"){
						$openparam['wwwurl'] = $data["www_url"] ? $data["www_url"] : '';
					}
					$sendParam = array(
							'opentype' => $data["opentype"],
							'openparam' => $openparam
					);
					$this->sendNoticeOne($user_id, $param, $sendParam, $errMsg);
				

				break;
			//现金券
			case 'voucher':
				if(!is_null($messageType) && $messageType == 'advisory') {
					$users = $this->getUserListByTicketId($from_id);
				} elseif(!is_null($messageType) && $messageType == 'reply') {
					$users = $this->getThreadUserId('voucher', $data['tid'], $from_id);
				}
				
				if(empty($users)) {
					$errMsg[] = '通知用户ID不能为空';
				}
				
				$param = array(
						'message' => $message,
						'type' => $type,
						'opentype' => $data["opentype"],
						'notice_type'=>$data['notice_type'],
						'charter_user_id' => $userInfo['user_id'],
						'charter_member' => $userInfo['user_name'],
						'charter_member_avator' => $userInfo['Avatar50'],
						'from_id' => $from_id,
						'is_auth' => $is_auth,
						'city'  => $city,
						'created' => REQUEST_TIME
				);
				
				$sendParam = array(
						'opentype' => $data["opentype"],
						'openparam' => array(
							'frid' => $from_id
						)
				);
				if(is_array($users)) {
					foreach($users as $user_id) {
						$this->sendNoticeOne($user_id, $param, $sendParam, $errMsg);
					}
				} else {
					$this->sendNoticeOne($users, $param, $sendParam, $errMsg);
				}
				break;
			//团购商品			
			case 'buygood':
				if(!is_null($messageType) && $messageType == 'advisory') {
					$users = $this->getUserListByTicketId($from_id);
				} elseif(!is_null($messageType) && $messageType == 'reply') {
					$users = $this->getThreadUserId('buygood', $data['tid'], $from_id);
				}
				
				if(!$users) {
					$errMsg[] = 'user_id:通知对象ID为空';
				}
				
				$param = array(
						'message' => $message,
						'type' => $type,
						'opentype' => $data["opentype"],
						'notice_type'=>$data['notice_type'],
						'charter_user_id' => $userInfo['user_id'],
						'charter_member' => $userInfo['user_name'],
						'charter_member_avator' => $userInfo['Avatar50'],
						'from_id' => $from_id,
						'is_auth' => $is_auth,
						'city'  => $city,
						'created' => REQUEST_TIME
				);
				
				$sendParam = array(
						'opentype' => $data["opentype"],
						'openparam' => array(
							'frid' => $from_id
						)
				);
				
				if(is_array($users)) {
					foreach($users as $user_id) {
						$this->sendNoticeOne($user_id, $param, $sendParam, $errMsg);
					}
				} else {
					$this->sendNoticeOne($users, $param, $sendParam, $errMsg);
				}
				
				break;
			//商城商品
			case 'commodity':
				if( "commodity_advisory" == $data["opentype"] ){
					$users = $this->getThreadUserId('commodity', $data['tid'], $from_id);
				}else if( "commodity_view" == $data["opentype"] ){
					$users = $userInfo;
				}
				
				if(!$users) {
					$errMsg[] = 'user_id:通知对象ID为空';
				}
			
				$param = array(
						'message' => $message,
						'type' => $type,
						'opentype' => $data["opentype"],
						'notice_type'=>$data['notice_type'],
						'charter_user_id' => $userInfo['user_id'],
						'charter_member' => $userInfo['user_name'],
						'charter_member_avator' => $userInfo['Avatar50'],
						'from_id' => $from_id,
						'is_auth' => $is_auth,
						'city'  => $city,
						'created' => REQUEST_TIME
				);
				
				$sendParam = array(
						'opentype' => $data["opentype"],
						'openparam' => array(
							'frid' => $from_id
						)
				);
				$this->sendNoticeOne($users['user_id'], $param, $sendParam, $errMsg);
				break;
			//折扣
			case 'discount':
				$user_id = $data["user_id"];
				if(!$user_id) {
					$errMsg[] = 'user_id:通知对象ID为空';
				}
				
				$param = array(
						'message' => $message,
						'type' => $type,
						'opentype' => $data["opentype"],
						'notice_type'=>$data['notice_type'],
						'charter_user_id' => $userInfo['user_id'],
						'charter_member' => $userInfo['user_name'],
						'charter_member_avator' => $userInfo['Avatar50'],
						'from_id' => $from_id,
						'is_auth' => $is_auth,
						'city'  => $city,
						'created' => REQUEST_TIME
				);
				
				$sendParam = array(
						'opentype' => $data["opentype"],
						'openparam' => array(
							'frid' => $from_id,
							'touid' => $userInfo['user_id']
						)
				);
				
				$this->sendNoticeOne($user_id, $param, $sendParam, $errMsg);
				break;
		}

	}
	
	//发送通知到指定用户
	private function sendNoticeOne($user_id, $param, $sendParam, $errMsg) {
		//通知错误日志
		if(!empty($errMsg)) {
			$fileErrorName = date('Ymd'). '.log';
			$logErrorPath = LOG_PATH . 'message/error/' . date('Y') . '/' .date('m') . '/';
			logLog($fileErrorName, var_export($errMsg, true), $logErrorPath);
		} 
		//通知入库
		else 
		{
			//>>发送提示
			Third_Des::$key = 'BBBFB38A';
			$message = $param['message'];
			$time = datex(REQUEST_TIME, 'Y-m-d H:i:s');
			
			
 			$openparam = array(
 				'tid' => $param['from_id']
			);
			
			$userRow = $this->getUserByUserId($user_id, 'uuid');
			$apply_message = array(
					'type' => 'ALL',//IOS,Android,ALL
					'sendtype' => 'Listcast',  // Broadcast : 广播   Listcast：列播
					'param' => array(
							'userid' => $userRow['uuid'],
							'title' => $message,
							'text' => $message,
							'desc' => $message,
							'openapp' => 'mpbuy',
							'opentype' => $sendParam['opentype'],
							'openparam' => json_encode($sendParam['openparam'])
					)
			);
			$apply_message_json = json_encode($apply_message);
			$apply_key = Third_Des::encrypt(md5($apply_message_json) . '|' . $time);
			
			$paramSend = array(
					'apply_type' => 'notify',
					'apply_key' => $apply_key,
					'apply_timestamp' => $time,
					'apply_message' => $apply_message
			);
			
			$sendSmsResult = Custom_AuthLogin::send_sms($paramSend);
			//日志
			$fileName = date('Ymd'). '.log';
			$logPath = LOG_PATH . 'message/sendResult/' . date('Y') . '/' .date('m') . '/';
			logLog($fileName, var_export($paramSend, true).var_export($sendSmsResult, true), $logPath);
			//<<
			
			$db = Core_DB::get('message', null, true);
			$param['user_id'] = $user_id;
			$param['opentype'] = $sendParam['opentype'];
			$sql = $this->insertSql($this->getNoticeTableNameByUserId($user_id), $param);
			return $db->query($sql);
		}
	}
	
	
	
	public function insertSql($table, $setArr){
		$insertkeysql = $insertvaluesql = $comma = '';
		foreach($setArr as $key=>$value){
			$insertkeysql .= $comma . '`' . $key . '`';
			$insertvaluesql .= $comma . '\'' . $value . '\'';
			$comma = ', ';
		}
		$sql = 'insert ignore into `' . $table . '`' . '(' . $insertkeysql . ') ' . 'VALUES (' . $insertvaluesql . ')';
		return $sql;
	}
	/**
	 * 获取发送通知表名
	 * @param unknown_type $user_id
	 */
	public function getNoticeTableNameByUserId($user_id) {
		//$table = 'oto_personal_notice';
		$table = 'oto_personal_notice_' . substr(md5($user_id), 0, 1);
		return $table;
	}
	/**
	 * 根据 现金券ID/团购ID 获取关联营业员和商户
	 * @param unknown_type $ticket_id
	 */
	public function getUserListByTicketId($ticket_id) {
		$sql = "select `OSC`.`user_id` 
				from `oto_ticket` as `OT`
				left join `oto_user_shop_competence` as `OSC` on `OT`.`shop_id` = `OSC`.`shop_id`
				where `OSC`.`user_type` <> '1' and `OT`.`ticket_id` = '{$ticket_id}'";
		$userArray = $this->_db->fetchCol($sql);
		return $userArray ? array_unique($userArray) : array();
	}
	
	public function getThreadTid($getData, & $userInfo) {
		$from_id = $getData['frid'];
		$type = $getData['type'];
		$user_id = $userInfo['user_id'];
		
		$where = "`user_id` = '{$user_id}' and `type` = '{$type}' and `from_id` = '{$from_id}'";
		$threadRow = $this->select($where, 'oto_message_thread', 'tid', '', true);
		
		return $threadRow['tid'] ? $threadRow['tid'] : 0; 
	}
	
	public function getThreadUserId($type, $tid, $from_id) {
	
		$where = "`tid` = '{$tid}' and `type` = '{$type}' and `from_id` = '{$from_id}'";
		$threadRow = $this->select($where, 'oto_message_thread', 'user_id', '', true);
	
		return $threadRow ? $threadRow : array();
	}
	/**
	 * 根据
	 * @param unknown_type $type
	 * @param unknown_type $ticket_id
	 */
	public function getShopIdByGoodType($type, $ticket_id) {
		$shop_id = 0;
		switch ($type) {
			case 'voucher':
			case 'buygood':
			case 'commodity':
				$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($ticket_id);
				$shop_id = $ticketRow['shop_id'];
				break;
		}
		
		return $shop_id;
	}
	
	public function syncTicketInfoToMsgDb( $ticket_id , $start_time , $end_time ){
		if( !$ticket_id || !$start_time || !$end_time ){
			return;
		}
		$msg_db = Core_DB::get('message', null, true);
		$msg_db->query("INSERT INTO `oto_ticket`(`ticket_id`,`start_time`,`end_time`) VALUES ('{$ticket_id}','{$start_time}','{$end_time}') ON DUPLICATE KEY UPDATE `start_time`='{$start_time}',`end_time`='{$end_time}'");
	}
	
	/**
	 * 添加预放通知
	 * @param unknown_type $noticeData
	 */
	public function addPreNotice( $type , $openType , $from_id ,  $param = array() ){
		if( $openType == "voucher_view" || $openType == "commodity_view"){
			$sql = "SELECT 1 FROM `oto_pre_notice` WHERE `type`='{$type}' AND `opentype` ='{$openType}' AND `notice_type`= '1' AND `from_id` = '{$from_id}' ";
			if( 1 ==  $this->_db->fetchOne($sql) ){
				return;
			}
		}
		switch ($type){
			case "voucher"://现金券：品牌券，商场券，特卖券
				$this->addVoucherNotice( $type , $openType , $from_id , $param);
				break;
			case "commodity"://商城商品
				$this->addCommodityNotice($type, $openType, $from_id, $param);
				break;
			case "buygood":
				$this->addBuygoodNotice($type, $openType, $from_id, $param);
				break;
			case "discount"://折扣
				$this->addDiscountNotice($type, $openType, $from_id, $param);
				break;
			case "system":
				$this->addSystemNotice($type, $openType, $from_id, $param);
				break;
		}
	}
	
	/**
	 * 添加券相关的通知
	 * @param unknown_type $type
	 * @param unknown_type $openType
	 * @param unknown_type $from_id
	 * @param unknown_type $param
	 */
	public function addVoucherNotice( $type , $openType , $from_id , $param ){
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($from_id);
		if( $openType == "voucher_view"){
			if( $ticketRow["ticket_status"] == 1 && $ticketRow["is_auth"] == 1 && $ticketRow["is_show"] == 1 ){
				$this->syncTicketInfoToMsgDb($from_id, $ticketRow["start_time"], $ticketRow["end_time"]);
				$noticeData = array(
						"user_id"=>0,
						"from_id"=>$from_id,
						"type"=>$type,
						"opentype"=>$openType,
						"start_time"=>$ticketRow["start_time"],
						"end_time"=>$ticketRow["end_time"],
						"city"=>$ticketRow["city"],
						"notice_type"=>0,
						"is_push"=>0,
						"message"=>"",
						"created"=>REQUEST_TIME
				);
				//新券
				$ticket_class = "";
				if( 1 == $ticketRow["ticket_class"] ){
					$ticket_class = "商场券";
				}elseif( 2 == $ticketRow["ticket_class"] ){
					$ticket_class = "品牌券";
				}elseif( 3 == $ticketRow["ticket_class"] ){
					$ticket_class = "特卖券";
				}
				$message = $this->configMsg("voucher_view_new", array(
						"{category}"=>$ticket_class,
						"{title}"=>$ticketRow["ticket_title"],
						"{time}"=>datex($ticketRow["start_time"],"Y.m.d")."-".datex($ticketRow["end_time"],"m.d")
				));
				$noticeData["notice_type"]=1;
				$noticeData["message"]=$message;
				$this->_db->insert('oto_pre_notice', $noticeData);
				$shopInfo = $this->_db->fetchRow("SELECT * FROM `oto_shop` WHERE `shop_id`='{$ticketRow["shop_id"]}'");
				if( $ticketRow["ticket_class"] == 1 ){//商场
					if( $shopInfo["market_id"] ){//关注的商场的商场券
						$marketName = $this->_db->fetchOne("SELECT `market_name` FROM `oto_market` WHERE `market_id`='{$shopInfo["market_id"]}'");
						$noticeData["message"] = $this->configMsg("voucher_market_concerned", array(
								"{category}"=>$marketName,
								"{title}"=>$ticketRow["ticket_title"]
						));
						$noticeData["notice_type"] = 3;
						$noticeData["is_push"] = 1;
						$uids = $this->_db->fetchCol("SELECT `user_id` FROM `oto_market_favorite` WHERE `market_id`='{$shopInfo["market_id"]}'");
						$sql = "";
						foreach ( $uids as $uid ){
							$noticeData["user_id"] = $uid;
							$sql .=" ('".implode("','", array_values($noticeData) )."'),";
						}
						if( $sql ){
							$sql = trim($sql,",");
							$sql = "INSERT INTO `oto_pre_notice`(`".implode("`,`", array_keys($noticeData))."`) VALUES ".$sql;
							$this->_db->query($sql);
						}
					}
				}else if($ticketRow["ticket_class"] == 2){//品牌
					if( $shopInfo["brand_id"] ){//关注的品牌的品牌券
						$brandName = $this->getBrand($shopInfo["brand_id"]);
						$noticeData["message"] = $this->configMsg("voucher_brand_concerned", array(
								"{category}"=>$brandName,
								"{title}"=>$ticketRow["ticket_title"]
						));
						$noticeData["notice_type"] = 3;
						$noticeData["is_push"] = 1;
						$uids = $this->_db->fetchCol("SELECT `user_id` FROM `oto_brand_favorite` WHERE `brand_id`='{$shopInfo["brand_id"]}'");
						$sql = "";
						foreach ( $uids as $uid ){
							$noticeData["user_id"] = $uid;
							$sql .=" ('".implode("','", array_values($noticeData) )."'),";
						}
						if( $sql ){
							$sql = trim($sql,",");
							$sql = "INSERT INTO `oto_pre_notice`(`".implode("`,`", array_keys($noticeData))."`) VALUES ".$sql;
							$this->_db->query($sql);
						}
					}
				}
			}
		}else if( $openType == "voucher_advisory" ){//现金券咨询
			if(isset($param["message_type"]) && $param["message_type"] == 'advisory') {
				$users = $this->getUserListByTicketId($from_id);
				$message = $this->configMsg("voucher_advisory", array(
						"{uname}"=>$param["charter_member"],
						"{title}"=>$ticketRow["ticket_title"]
				));
			} elseif(isset($param["message_type"]) && $param["message_type"] == 'reply') {
				$users = $this->getThreadUserId($type, $param['tid'], $from_id);
				$message = $this->configMsg("voucher_advisory_reply", array(
						"{title}"=>$ticketRow["ticket_title"]
				));
			}
			$noticeData = array(
					"from_id"=>$from_id,
					"type"=>$type,
					"opentype"=>$openType,
					"notice_type"=>2,
					"start_time"=>$ticketRow["start_time"],
					"end_time"=>$ticketRow["end_time"],
					"message" => $message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"city"=>$ticketRow["city"],
					"is_push"=>1,
					"created"=>REQUEST_TIME
			);
			if(is_array($users)) {
				foreach($users as $user_id) {
					$noticeData["user_id"] = $user_id;
					$this->_db->insert('oto_pre_notice',$noticeData);
				}
			} else if($users>0) {
				$noticeData["user_id"] = $users;
				$this->_db->insert('oto_pre_notice',$noticeData);
			}
		}
	}
	/**
	 * 添加商城商品通知
	 * @param unknown_type $type
	 * @param unknown_type $openType
	 * @param unknown_type $from_id
	 * @param unknown_type $param
	 */
	public function addCommodityNotice( $type , $openType , $from_id , $param ){
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($from_id);
		$shopName = $this->getShopName($ticketRow["shop_id"]);
		if( $openType == "commodity_view"){//商城商品详情
			if( $ticketRow["ticket_status"] == 1 && $ticketRow["is_auth"] == 1 && $ticketRow["is_show"] == 1  ){
				$noticeData = array(
						"user_id"=>0,
						"from_id"=>$from_id,
						"type"=>$type,
						"opentype"=>$openType,
						"start_time"=>$ticketRow["start_time"],
						"end_time"=>$ticketRow["end_time"],
						"notice_type"=>0,
						"message"=>"",
						"is_push"=>0,
						"city"=>$ticketRow["city"],
						"created"=>REQUEST_TIME
				);
				
				//新品
// 				if($ticketRow['is_free'] == 1) {//app免费
// 					$ticketRow['selling_price'] = 0;
// 				} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] > 0) {//app价格
// 					$ticketRow['selling_price'] = $ticketRow['app_price'];
// 				}
// 				$noticeData["message"] = $this->configMsg("commodity_view_new", array(
// 							"{title}"=>$ticketRow["ticket_title"],
// 							"{price}"=>$ticketRow["selling_price"]
// 				));
// 				$noticeData["notice_type"] = 1;
// 				$this->_db->insert('oto_pre_notice',$noticeData);
					
				//关注店铺的新品
				$noticeData["message"] = $this->configMsg("commodity_shop_concerned", array(
						"{category}"=>$shopName,
						"{title}"=>$ticketRow["ticket_title"]
				));
				$noticeData["notice_type"] = 3;
				$noticeData["is_push"] = 1;
				$uids = $this->_db->fetchCol("SELECT DISTINCT(`user_id`) FROM `oto_shop_favorite` WHERE `shop_id`='{$ticketRow["shop_id"]}'");
				$sql = "";
				foreach ( $uids as $uid ){
					$noticeData["user_id"] = $uid;
					$sql .=" ('".implode("','", array_values($noticeData) )."'),";
				}
				if( $sql ){
					$sql = trim($sql,",");
					$sql = "INSERT INTO `oto_pre_notice`(`".implode("`,`", array_keys($noticeData))."`) VALUES ".$sql;
					$this->_db->query($sql);
				}
			}
		}else if( $openType =="commodity_advisory" ){//商城商品咨询
			$users = $this->getThreadUserId('commodity', $param['tid'], $from_id);
			$message = $this->configMsg("commodity_advisory", array(
					"{title}"=>$ticketRow["ticket_title"],
					"{shop}"=>$shopName
			));
			$noticeData = array(
					"from_id"=>$from_id,
					"type"=>$type,
					"opentype"=>$openType,
					"notice_type"=>2,
					"message" => $message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"city"=>$ticketRow["city"],
					"is_push"=>1,
					"created"=>REQUEST_TIME
			);
			if(is_array($users)) {
				foreach($users as $user_id) {
					$noticeData["user_id"] = $user_id;
					$this->_db->insert('oto_pre_notice',$noticeData);
				}
			} else if($users>0) {
				$noticeData["user_id"] = $users;
				$this->_db->insert('oto_pre_notice',$noticeData);
			}
		}
	}
	/**
	 * 添加团购商品通知
	 * @param unknown_type $type
	 * @param unknown_type $openType
	 * @param unknown_type $from_id
	 * @param unknown_type $param
	 */
	public function addBuygoodNotice(  $type , $openType , $from_id , $param ){
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($from_id);
		if( $openType == "nine_buy_advisory" ){
			if(isset($param["message_type"]) && $param["message_type"] == 'advisory') {
				$users = $this->getUserListByTicketId($from_id);
				$message = $this->configMsg("nine_buy_advisory", array(
						"{uname}" => $param['charter_member']
				));
			} elseif(isset($param["message_type"]) && $param["message_type"] == 'reply') {
				$users = $this->getThreadUserId($type, $param['tid'], $from_id);
				$message = $this->configMsg("nine_buy_advisory_reply", array(
						"{uname}" => $param['charter_member']
				));
			}
			
			$noticeData = array(
					"from_id"=>$from_id,
					"type"=>$type,
					"opentype"=>$openType,
					"notice_type"=>2,
					"message" => $message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"city"=>$ticketRow["city"],
					"is_push"=>1,
					"created"=>REQUEST_TIME
			);
			if(is_array($users)) {
				foreach($users as $user_id) {
					$noticeData["user_id"] = $user_id;
					$this->_db->insert('oto_pre_notice',$noticeData);
				}
			} else {
				$noticeData["user_id"] = $users;
				$this->_db->insert('oto_pre_notice',$noticeData);
			}
		}
	}
	/**
	 * 添加折扣相关通知
	 * @param unknown_type $type
	 * @param unknown_type $openType
	 * @param unknown_type $from_id
	 * @param unknown_type $param
	 */
	public function addDiscountNotice( $type , $openType , $from_id , $param ){
		if( $openType == "special_group_chat" ){//专题群聊，获取相关专题详情
			$discountRow = $this->_db->fetchRow("SELECT *,0 as user_id FROM `special_content` WHERE `special_id`='{$from_id}'");
		}else{
			$discountRow = $this->_db->fetchRow("SELECT * FROM `discount_content` WHERE `discount_id`='{$from_id}'");
		}
		if( $openType == "discount_view" ){//折扣详情
			$uids = $this->_db->fetchCol("SELECT `from_user_id` FROM `oto_user_concerned` WHERE `to_user_id`='{$discountRow["user_id"]}'");
			$message = $this->configMsg("discount_user_concerned", array("{uname}"=>$param["charter_member"],"{title}"=>$discountRow["title"]));
			$noticeData = array(
					"user_id"=>0,
					"from_id"=>$from_id,
					"type"=>$type,
					"opentype"=>$openType,
					"notice_type"=>3,
					"message"=>$message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"city"=>$discountRow["city"],
					"created"=>REQUEST_TIME
			);
			$sql = "";
			foreach( $uids as $uid ){
				$noticeData['user_id'] = $uid;
				$sql .=" ('".implode("','", array_values($noticeData) )."'),";
			}
			if( $sql ){
				$sql = trim($sql,",");
				$sql = "INSERT INTO `oto_pre_notice`(`".implode("`,`", array_keys($noticeData))."`) VALUES ".$sql;
				$this->_db->query($sql);
			}
				
		}else if( $openType == "discount_about_shopping" ){//折扣约逛
			$message = $this->configMsg("discount_about_shopping", array(
					"{uname}"=>$param["charter_member"]
			));
			$noticeData = array(
					"user_id"=>$param["to_user_id"],
					"from_id"=>$from_id,
					"type"=>$type,
					"opentype"=>$openType,
					"notice_type"=>2,
					"message"=>$message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"city"=>$discountRow["city"],
					"is_push"=>1,
					"created"=>REQUEST_TIME
			);
			$this->_db->insert('oto_pre_notice',$noticeData);
		}else if( $openType == "discount_advisory" ){//折扣咨询
			if( $discountRow["user_id"] == $param["charter_user_id"] ){//折扣发布者回复
				$message = $this->configMsg("discount_advisory_reply", array(
						"{uname}"=>$param["charter_member"]
				));
			}else{//用户咨询
				$message = $this->configMsg("discount_advisory", array(
						"{uname}"=>$param["charter_member"]
				));
			}
			$noticeData = array(
					"user_id"=>$param["to_user_id"],
					"from_id"=>$from_id,
					"type"=>$type,
					"opentype"=>$openType,
					"notice_type"=>2,
					"message"=>$message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"city"=>$discountRow["city"],
					"is_push"=>1,
					"created"=>REQUEST_TIME
			);
			$this->_db->insert('oto_pre_notice',$noticeData);
		}else if( $openType == "discount_group_chat" ||
				$openType == "special_group_chat" ){//折扣|专题群聊
			//获取所有参与的用户
			$sql = "SELECT DISTINCT(`user_id`) 
					FROM `oto_group_chat` 
					WHERE `did`='{$from_id}' AND `user_id`<>'{$param["charter_user_id"]}' 
					ORDER BY `created` ASC";
			$uids = $this->_db->fetchCol($sql);
			$message = $this->configMsg($openType, array(
					"{action}"=>"参与",
					"{title}"=>$discountRow["title"]
			));
			$noticeData = array(
					'user_id'=>0,
					'from_id'=>$from_id,
					'type'=>$type,
					'opentype'=>$openType,
					'notice_type'=>2,
					'message'=>$message,
					'charter_user_id'=>intval($param["charter_user_id"]),
					'charter_member'=>$param["charter_member"],
					'charter_member_avator'=>$param["charter_member_avator"],
					'city'=>$discountRow["city"],
					'created'=>REQUEST_TIME
			);
			$sql = "";
			foreach( $uids as $uid ){
				$noticeData['user_id'] = $uid;
				$sql .=" ('".implode("','", array_values($noticeData) )."'),";
			}
			if( $sql ){
				$sql = trim($sql,",");
				$sql = "INSERT INTO `oto_pre_notice`(`".implode("`,`", array_keys($noticeData))."`) VALUES ".$sql;
				$this->_db->query($sql);
			}
		}
	}
	/**
	 * 添加系统相关通知
	 * @param unknown_type $type
	 * @param unknown_type $openType
	 * @param unknown_type $from_id
	 * @param unknown_type $param
	 */
	public function addSystemNotice( $type , $openType , $from_id , $param ){
		if( $openType == "home_fans_list" ){
			$message = $this->configMsg("user_conserned", array(
					"{uname}"=>$param["charter_member"],
					"{time}"=>datex(REQUEST_TIME,"y-m-d H:i:s")
			));
			$noticeData = array(
					"user_id"=>$param["to_user_id"],
					"from_id"=>$from_id,
					'type'=>$type,
					'opentype'=>$openType,
					'notice_type'=>3,
					'message'=>$message,
					"charter_user_id"=>intval($param["charter_user_id"]),
					"charter_member"=>$param["charter_member"],
					"charter_member_avator"=>$param["charter_member_avator"],
					"is_push"=>1,
					"city"=>$this->_city,
					"created"=>REQUEST_TIME
			);
			$this->_db->insert('oto_pre_notice',$noticeData);
		}else{//接口调用or后台添加的通知
			if( isset($param["uids"]) ){
				$uids = $param["uids"];
				if( !is_array($uids) && $uids > 0 ){
					$uids = array($uids);
				}
				$noticeData = array(
						"user_id"=>0,
						"from_id"=>$from_id,
						"type"=>$type,
						"opentype"=>$openType,
						"notice_type"=>3,
						"message"=>$param["message"],
						"start_time"=>isset($param["start_time"])?$param["start_time"]:0,
						"end_time"=>isset($param["end_time"])?$param["end_time"]:0,
						"city"=>$param["city"],
						"is_push"=>$param["is_push"],
						"created"=>REQUEST_TIME
				);
				$sql = "";
				foreach ( $uids as $uid ){
					$noticeData["user_id"] = $uid;
					$sql .=" ('".implode("','", array_values($noticeData) )."'),";
				}
				if( $sql ){
					$sql = trim($sql,",");
					$sql = "INSERT INTO `oto_pre_notice`(`".implode("`,`", array_keys($noticeData))."`) VALUES ".$sql;
					$this->_db->query($sql);
				}
			}else{
				$noticeData = array(
						"from_id"=>$from_id,
						"type"=>$type,
						"opentype"=>$openType,
						"notice_type"=>3,
						"message"=>$param["message"],
						"start_time"=>isset($param["start_time"])?$param["start_time"]:0,
						"end_time"=>isset($param["end_time"])?$param["end_time"]:0,
						"city"=>$param["city"],
						"is_push"=>$param["is_push"],
						"created"=>REQUEST_TIME
				);
				$this->_db->insert("oto_pre_notice",$noticeData);
			}
		}
	}
	
	/**
	 * 修改预放通知 （针对现金券）
	 * @param unknown_type $from_id
	 * @param unknown_type $data
	 */
	public function updatePreNoticeInfo( $from_id , $type , $open_type ) {		
		if( $type == "voucher" ) {//券修改
			$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($from_id);
			
			$this->syncTicketInfoToMsgDb($from_id, $ticketRow["start_time"], $ticketRow["end_time"]);
			
			$ticket_class = "voucher_view_new";
			if( 1 == $ticketRow["ticket_class"] ){
				$ticket_class = "商场券";
			}elseif( 2 == $ticketRow["ticket_class"] ){
				$ticket_class = "品牌券";
			}elseif( 3 == $ticketRow["ticket_class"] ){
				$ticket_class = "特卖券";
			}
			$message = $this->configMsg("voucher_view_new", array(
						"{category}"=>$ticket_class,
						"{title}"=>$ticketRow["ticket_title"],
						"{time}"=>datex($ticketRow["start_time"],"Y.m.d")."-".datex($ticketRow["end_time"],"m.d")
					));
			$noticeData = array(
					"message"=>$message,
					"start_time"=>$ticketRow["start_time"],
					"end_time"=>$ticketRow["end_time"]
			);
			$this->_db->update('oto_pre_notice', $noticeData , array("type"=>$type,"opentype"=>$open_type,"from_id"=>$from_id,"notice_type"=>1));
			
			$shopInfo = $this->_db->fetchRow("SELECT * FROM `oto_shop` WHERE `shop_id`='{$ticketRow["shop_id"]}'");
			if( $ticketRow["ticket_class"] == 1 ){//商场
				if( $shopInfo["market_id"] ){//关注的商场的商场券
					$marketName = $this->_db->fetchOne("SELECT `market_name` FROM `oto_market` WHERE `market_id`='{$shopInfo["market_id"]}'");
					$message = $this->configMsg("voucher_market_concerned", array(
							"{category}"=>$marketName,
							"{title}"=>$ticketRow["ticket_title"]
					));
				}
			}else if($ticketRow["ticket_class"] == 2){//品牌
				if( $shopInfo["brand_id"] ){//关注的品牌的品牌券
					$brandName = $this->getBrand($shopInfo["brand_id"]);
					$message = $this->configMsg("voucher_brand_concerned", array(
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
			$this->_db->update('oto_pre_notice', $noticeData , array("type"=>$type,"opentype"=>$open_type,"from_id"=>$from_id,"notice_type"=>3));
		}else if( $type == "commodity" ){//商城商品修改
			$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($from_id);
			//新品
			if($ticketRow['is_free'] == 1) {//app免费
				$ticketRow['selling_price'] = 0;
			} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] > 0) {//app价格
				$ticketRow['selling_price'] = $ticketRow['app_price'];
			}
			$message = $this->configMsg("commodity_view_new", array(
					"{title}"=>$ticketRow["ticket_title"],
					"{price}"=>$ticketRow["selling_price"]
			));
			$noticeData = array(
					"message"=>$message
					);
			$this->_db->update('oto_pre_notice',$noticeData,array("type"=>$type,"opentype"=>$open_type,"from_id"=>$from_id,"notice_type"=>1));
			//关注店铺的新品
			$shopName = $this->getShopName($ticketRow["shop_id"]);
			$message = $this->configMsg("commodity_shop_concerned", array(
					"{category}"=>$shopName,
					"{title}"=>$ticketRow["ticket_title"]
			));
			$noticeData = array(
					"message"=>$message
					);
			$this->_db->update('oto_pre_notice',$noticeData,array("type"=>$type,"opentype"=>$open_type,"from_id"=>$from_id,"notice_type"=>3));
		}
	}
	
	/**
	 * 关联预放通知到用户
	 * @param unknown_type $user_id
	 */
	public function updateUserPreNotice( $to_user_id , $city = 'sh') {
		$msg_db = Core_DB::get('message', null, true);
		$msg_tbl = $this->getNoticeTableNameByUserId($to_user_id);
		//发送预放的现金券
		$sel_sql = "SELECT * FROM `oto_pre_notice` 
					WHERE `city`='{$city}' AND `type`='voucher' AND `opentype`='voucher_view' AND `user_id`='0' AND `is_handle`='1' 
					AND `start_time`<'".REQUEST_TIME."' AND `end_time`>'".REQUEST_TIME."'";
		$noticeRes = $this->_db->fetchAll($sel_sql);
		if( $noticeRes ){
			$this->sendPersonalNotice($to_user_id , $noticeRes);
		}
		
		//发送预放的其他消息
		$last_update_time = $this->_db->fetchOne("SELECT `last_update_time` FROM `oto_pre_notice_user` WHERE `user_id`='{$to_user_id}'");
		$where = " WHERE `city`='{$city}' AND (`type`='system' OR `opentype`<>'voucher_view') AND `user_id`='0' AND `is_handle`=1 ";
		if( $last_update_time && $last_update_time > $this->_dDayTime  ){
			$where .= " AND `created` >'".$last_update_time."'";
		}else{
			$where .= " AND `created` >'".$this->_dDayTime."'";
		}
		$sel_sql = "SELECT * FROM `oto_pre_notice` {$where} ";
		$noticeRes = $this->_db->fetchAll($sel_sql);
		if($noticeRes) {
			$this->sendPersonalNotice($to_user_id , $noticeRes);
			$this->_db->query("INSERT INTO `oto_pre_notice_user`(`user_id`,`last_update_time`)VALUES('{$to_user_id}','".REQUEST_TIME."') ON DUPLICATE KEY UPDATE `last_update_time`='".REQUEST_TIME."'");
		}
	}
	
	/**
	 * 更新到通知列表
	 * @param unknown_type $to_user_id
	 * @param unknown_type $noticeArr
	 */
	public function sendPersonalNotice( $to_user_id , $noticeArr ){
		$sql = "";
		$msg_db = Core_DB::get('message', null, true);
		$tbl_name = $this->getNoticeTableNameByUserId($to_user_id);
		foreach( $noticeArr as $row ){
				if( $row["opentype"] == "voucher_view" && $row["notice_type"]=="1" ){
					$sel_sql = "SELECT 1 FROM `{$tbl_name}` 
							WHERE `opentype`= 'voucher_view' AND `notice_type`='1' AND `from_id`='{$row["from_id"]}'";
					$flag = $msg_db->fetchOne($sel_sql);
					if( $flag == 1 ){
						continue;
					}
				}
				$param = array(
						'user_id' => $to_user_id,
						'message' => mysql_escape_string( $row["message"] ),
						'type' => $row["type"],
						'opentype' => $row["opentype"],
						'notice_type'=>$row["notice_type"],
						'charter_user_id' => $row["charter_user_id"],
						'charter_member' => $row["charter_member"],
						'charter_member_avator' => $row["charter_member_avator"],
						'from_id' => intval($row["from_id"]),
						'city' => $row["city"],
						'created' => $row["created"]
				);
				$sql .=" ('".implode("','", array_values($param) )."'),";
		}
		if( $sql ){
			$sql = trim($sql,",");
			$sql = "INSERT INTO `{$tbl_name}`(`".implode("`,`", array_keys($param) )."`) VALUES {$sql}";
			return $msg_db->query($sql);
		}
		return true;
	}
	
	/**
	 * 发送推送通知
	 * @param unknown_type $param
	 */
	public function pushPreNotice( $param ){
		//通知日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/sendPreNotice/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($param, true), $logPath);
		
		Third_Des::$key = 'BBBFB38A';
		$time = datex(REQUEST_TIME, 'Y-m-d H:i:s');
		$user_id = intval( $param["user_id"] );
		$sendType = 'Listcast';
		
		$user_uuid = '';
		$openparam = array();
		if( $user_id == 0 ){
			$sendType = 'Broadcast';
		}else{
			$userRow = $this->getUserByUserId($user_id, 'uuid');
			$user_uuid = empty($userRow["uuid"])?'':$userRow["uuid"];
		}
		
		switch ( $param['opentype'] ){
			case 'home_order':
				$openparam = array(
					'orderno' => $param["order_no"],
					'city' => $param["city"]
				);
				break;
			case 'discount_about_shopping':
			case 'discount_advisory':
				$openparam = array(
					'frid' => $param["from_id"],
					'touid' => $param["charter_user_id"],
					'city' => $param["city"]
				);
				break;
			case 'activity_one_yuan_purchase':
			case 'activity_one_yuan_purchase_view':
				$openparam = array(
						'frid' => '',
						'wwwurl' => $GLOBALS['GLOBAL_CONF']['SITE_URL']."/active/oneyuanpurchase",
						'city' => $param["city"]
				);
				break;
			case 'activity_come_and_grab':
			case 'activity_come_and_grab_view':
				$openparam = array(
					'frid' => '',
					'wwwurl' => $GLOBALS['GLOBAL_CONF']['SITE_URL']."/active/comeandgrap",
					'city' => $param["city"]
					);
				break;
			default:
				$openparam = array(
					'frid' => empty($param["from_id"])?'':$param["from_id"],
					'city' => $param["city"]
				);
				break;
		}
		
		$apply_message = array(
				'type' => 'ALL',//IOS,Android,ALL
				'sendtype' => $sendType,  // Broadcast : 广播   Listcast：列播
				'param' => array(
						'userid' => $user_uuid,
						'title' => $param["message"],
						'text' => $param["message"],
						'desc' => $param["message"],
						'openapp' => 'mpbuy',
						'opentype' => empty($param['opentype'])?'system':$param['opentype'],
						'openparam' => json_encode($openparam)
				)
		);
		$apply_message_json = json_encode($apply_message);
		$apply_key = Third_Des::encrypt(md5($apply_message_json) . '|' . $time);
			
		$paramSend = array(
				'apply_type' => 'notify',
				'apply_key' => $apply_key,
				'apply_timestamp' => $time,
				'apply_message' => $apply_message
		);
			
		$sendSmsResult = Custom_AuthLogin::send_sms($paramSend);
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'message/sendResult/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($paramSend, true).var_export($sendSmsResult, true), $logPath);
		//<<
		$sendSmsResult = json_decode($sendSmsResult,1);
		$result = $sendSmsResult["PostMessageResult"];
		$result = str_replace("'", "\"", $result);
		$result = json_decode($result,1);
		return intval($result["returncode"]);
	}
	
	
	//更新message内容
	public function configMsg( $key , $param ){
		$msgArr = @include VAR_PATH . 'config/message.php';
		if( array_key_exists($key, $msgArr) ){
			$msg = $msgArr[$key];
			$msg = str_replace(array_keys($param), array_values($param), $msg);
			return mysql_escape_string($msg);
		}
		return "";
	}	
}