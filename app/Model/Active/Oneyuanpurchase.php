<?php
class Model_Active_Oneyuanpurchase extends Base {
	
	private static $_instance;
	protected $_table = 'oto_ticket';
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 获取活动列表
	 * @param unknown_type $user_id 登录用户id
	 * @param unknown_type $type 类型 -1：已结束 0：正在进行 1：未开始
	 * @param unknown_type $city 城市
	 */
	public function getActivityList( $user_id , $type , $city='sh', $page = 1 , $pageSize=10){
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique('crowdfunding');
		$where = "WHERE `city`='{$city}' AND ticket_type = '{$ticketType}'  AND `ticket_status`=1 AND `is_auth`=1 AND `is_show`=1";
		$order = " ORDER BY `sequence` asc, `end_time` DESC";
		if( $type == -1 ){//已结束
			$where .= " AND `end_time`<=".REQUEST_TIME;
			$order = " ORDER BY `end_time` DESC";
		}else if( $type == 0 ){//正在进行中的
			$where .= " AND `start_time`<".REQUEST_TIME." AND `end_time`>".REQUEST_TIME;
		}else if( $type == 1 ){//未开始的
			$where .= " AND `start_time`>=".REQUEST_TIME;
			$order = " ORDER BY `sequence` asc, `start_time` DESC";
		}
		$sql = "SELECT `ticket_id`,`ticket_uuid`,`ticket_title`,`cover_img`,`par_value`,`selling_price`,`start_time`,`end_time`,`total`
				FROM `".$this->_table."`
				{$where} 
				{$order}";
		if( -1==$type ){
			$start = ($page-1)*$pageSize;
			$sql .= " LIMIT {$start},{$pageSize}";
		}
		
		$data = $this->_db->fetchAll( $sql );
		foreach($data as & $row) {
			$row["selling_price"] = round($row["selling_price"]);
			$row["cover_img"] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/'.$row["cover_img"];
			$row["attended_num"] = 0;
			$row["timeout"] = $row["prompted_num"] = $row["is_attend"]='0';
			if( $type == 0 ){
				$row["timeout"] = $row["end_time"] - REQUEST_TIME;
			}else if( $type == 1 ){
				$row["timeout"] = $row["start_time"] - REQUEST_TIME;
				$row["prompted_num"] = $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_ticket_prompt` WHERE `ticket_id`='{$row["ticket_id"]}'");
				$ticketInfo = $this->select("`ticket_id` = '{$row["ticket_id"]}'", 'oto_ticket_info', '*', '', true);
				$row["prompted_num"] += $ticketInfo["love_number"];
			}else if( $type == -1){
				$lottery_row = $this->_db->fetchRow("SELECT * FROM `oto_ticket_info` WHERE `ticket_id`='{$row["ticket_id"]}'");
				$row['lottery_action'] = $lottery_row['lottery_action'];
				$lottery_time = $lottery_row["lottery_time"];
				$row["lottery_date"] = date("Y-m-d H:i:s",$lottery_time);
				$row["timeout"] = 0;
				$row["lottery_user"] = array();
				if( $lottery_time - REQUEST_TIME > 0 ){
					$row["timeout"] = $lottery_time - REQUEST_TIME;
				}else{
					if( $lottery_row["lottery_uuid"] ) {
						$row["lottery_user"] = $this->getWebUserId($lottery_row["lottery_uuid"],true);
						$row["lottery_user"]["mobile"] = preg_replace('/(1\d{1,2})\d\d\d\d(\d{3,4})/', '$1****$2', $lottery_row["lottery_mobile"]);
						$row["lottery_user"]["order_no"] = $lottery_row["lottery_code"];
					} else {
						$mobile = $lottery_row['no_winning'] ? $lottery_row['no_winning'] : '18916972605';
						$mianUserInfoRow = Custom_AuthLogin::get_user_by_mobile($mobile);
						if($mianUserInfoRow['GetUserInfosResult'] == 1) {
							$row["lottery_user"] = $this->getWebUserId($mianUserInfoRow['userInfo']['UserId'],true);
						} else {
							$row["lottery_user"]['user_name'] = $lottery_row['winning_user_name'] ? $lottery_row['winning_user_name'] : 'mob_' . substr($mobile, 4);
						}
						$row["lottery_user"]['mobile'] = preg_replace('/(1\d{1,2})\d\d\d\d(\d{3,4})/', '$1****$2', $mobile);
						$row["lottery_user"]["order_no"] = $lottery_row["lottery_code"];
					}
				}
			}
			$row["is_notice"] = 0;
 			if( $user_id ){
 				$row["is_notice"] = (int) $this->isNotice($row["ticket_id"], $user_id);
 			}
		}
		return $data;
	}
	
	/**
	 * 是否提醒
	 * @param unknown_type $ticket_id 活动 id
	 * @param unknown_type $user_id 用户id
	 * @return Ambigous <string, unknown>
	 */
	public function isNotice( $ticket_id ,  $user_id ){
		$num = $this->_db->fetchOne("SELECT 1 FROM `oto_ticket_prompt` WHERE `ticket_id`='{$ticket_id}' AND `user_id`='{$user_id}' limit 1");
		return $num == 1 ;
	}
	
	/**
	 * 用户添加提醒
	 * @param unknown_type $ticket_id 活动id
	 * @param unknown_type $user_id 用户id
	 */
	public function addNotice( $ticket_id , $user_id ){
		$tickRow = $this->_db->fetchRow("SELECT `start_time`,`ticket_title`,`ticket_type` FROM `oto_ticket` WHERE `ticket_id` = '{$ticket_id}'");
		$sql = "INSERT INTO `oto_ticket_prompt`(`ticket_id`,`user_id`,`start_time`,`ticket_title`,`ticket_type`,`created`) VALUES('{$ticket_id}','{$user_id}','{$tickRow["start_time"]}','{$tickRow["ticket_title"]}','{$tickRow["ticket_type"]}','".REQUEST_TIME."') ON DUPLICATE KEY UPDATE `created`='".REQUEST_TIME."'";
		return $this->_db->query( $sql );
	}
	
	/**
	 * 用户取消提醒
	 * @param unknown_type $ticket_id 活动id
	 * @param unknown_type $user_id 用户id
	 */
	public function cancelNotice( $ticket_id , $user_id ){
		$sql = "DELETE FROM `oto_ticket_prompt` WHERE `ticket_id`='{$ticket_id}' AND `user_id`='{$user_id}'";
		return $this->_db->query( $sql );
	}
	
	/**
	 * 获取活动详情
	 * @param unknown_type $ticket_id 活动id
	 * @param unknown_type $user_id 用户id
	 */
	public function getActivityDetail( $ticket_id , $user_id ){
		$sql = "SELECT `ticket_id`,`ticket_uuid`,`ticket_title`,`ticket_summary`,`wap_content`,`cover_img`,`par_value`,`selling_price`,`start_time`,`end_time`,`limit_count`,`shop_id`
				FROM `".$this->_table."`
				WHERE `ticket_id`='{$ticket_id}'";
		$ticketRow = $this->_db->fetchRow( $sql );
		if( empty($ticketRow) ){
			return false;
		}
		
		$ticketRow["cover_img"] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/'.$ticketRow["cover_img"];
		//参加人数+阀值
		$ticketObj = Custom_AuthTicket::get_ticket_details_by_guid($ticketRow['ticket_uuid']);
		$ticketRow["attended_num"] = $ticketObj->data->Avtivities[0]->ProductStock;
		
		$ticketRow["date"] = date("Y.m.d",$ticketRow["start_time"])."-".date("m.d",$ticketRow["end_time"]);
		
		$shop = $this->_db->fetchRow("SELECT `shop_id`,`shop_name`,`shop_address`,`lat`,`lng` FROM `oto_shop` WHERE `shop_id`='{$ticketRow["shop_id"]}'");
		$ticketRow["shop"] = $shop;
		
		//商品原始图片
		$imgList = Model_Admin_Ticket::getInstance()->getWapImg($ticket_id);
		$wap_img_list = array();
		foreach ($imgList as &$imgItem) {
			$wap_img_list[] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/commodity/" . $imgItem['img_url'];
		}
		$ticketRow["wap_img_list"] = $wap_img_list;
		
		return $ticketRow;
		
	}

	public function getWeixinKey($share_url, $appkey = 'quiz', $secretKey = '3497eca83ec541c5b74ccfc2598fc28') {
		$stamp = REQUEST_TIME;
		$signData = strtoupper(md5("appkey=quiz&stamp={$stamp}&key={$secretKey}"));
		$param = array(
				'act' => 'GetJsApiData',
				'appkey' => $appkey,
				'stamp' => $stamp,
				'url' => $share_url,
				'signData' => $signData
		);
	
		$keyResult = Core_Http::sendRequest('http://pay.mplife.com/provider/weixin/GetHandler.ashx', $param, 'CURL-POST');
		$keyResultArray = objectToArray(json_decode($keyResult));
		$keyResultArray = array_shift($keyResultArray);
		return $keyResultArray;
	}
	
	public function getWeixinUser($back_url, $appkey = 'quiz', $secretKey = '3497eca83ec541c5b74ccfc2598fc28') {
		$stamp = REQUEST_TIME;
		$param = array(
				'Act' => 'GetUserInfo',
				'AppKey' => $appkey,
				'Stamp' => $stamp,
				'UserId' => '00000000-0000-0000-0000-000000000000',
				'CallbackUrl' => $back_url,
		);
		ksort($param);
	
		$str = '';
	
		foreach($param as $key => $value) {
			$str .= "{$key}={$value}&";
		}
	
		$str = substr($str, 0, -1);
	
		$str .= $secretKey;
		$signData = md5($str);
		$param['Token'] = $signData;
	
		$url = 'http://Pay.Mplife.Com/Provider/Weixin/Authorize.aspx?';
		foreach($param as $key => $value) {
			$url .= "{$key}=".urlencode($value) . "&";
		}
	
		return substr($url, 0, -1);
	}
	
	public function parseLinkMsg( $output ){
		$user_id = 0;
		if( !empty($output['uuid']) ){
			$clientResult = Custom_AuthLogin::get_user_by_uuid($output['uuid']);
			$userInfo = $this->getWebUserId($output['uuid'],true);
			if( !empty($clientResult["userInfo"]["Mobile"]) && !empty($userInfo['user_id']) ){
				$user_id = $userInfo['user_id'];
				cookie('ONEYUANPURCHASE_USER_ID', Third_Des::encrypt($user_id), 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			}else{
				cookie('ONEYUANPURCHASE_USER_ID', '', 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			}
		}
		return $user_id;
	}
}