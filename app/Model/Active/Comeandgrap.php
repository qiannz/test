<?php
class Model_Active_Comeandgrap extends Base {
	
	private static $_instance;
	protected $_table = 'oto_ticket';
	protected $_sort_detail_mark = 'spike';
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * 
	 * @param unknown_type $type 0：正在进行  1：今日即将开抢的   2：明日开枪的
	 * @param unknown_type $city
	 */
	public function getActivityList( $user_id , $type , $city ){
		$ticketType = Model_Home_Suser::getInstance()->getTicketIdByTicketUnique($this->_sort_detail_mark);
		$where = "WHERE `city`='{$city}' AND `ticket_type` = '{$ticketType}' AND `ticket_status`=1 AND `is_auth`=1 AND `is_show`=1 ";
		if( 0 == $type ){
			$order = " ORDER BY `sequence` ASC, `end_time` ASC";
			$where .= " AND `start_time`<='".REQUEST_TIME."' AND `end_time`>'".REQUEST_TIME."'";
		}else if( 1 == $type ){
			$order = " ORDER BY `sequence` ASC, `start_time` ASC";
			$endTime = strtotime(datex(REQUEST_TIME,"Y-m-d")." 23:59:59");
			$where .= " AND `start_time`>'".REQUEST_TIME."' AND `start_time`<='{$endTime}'";
		}else if( 2 == $type ){
			$order = " ORDER BY `sequence` ASC, `start_time` ASC";
			$date = datex(strtotime("+1 day"),"Y-m-d");
			$minStartTime = strtotime($date." 00:00:00");
			$maxStartTime = strtotime($date." 23:59:59");
			$where .= " AND `start_time`>='".$minStartTime."' AND `start_time`<='{$maxStartTime}'";
		}
		$sql = "SELECT `ticket_id`,`ticket_uuid`,`ticket_title`,`cover_img`,`par_value`,`selling_price`,`start_time`,`end_time`,`total`
				FROM `".$this->_table."`
				{$where}
				{$order}";
		$data = $this->_db->fetchAll( $sql );
		$list = array();
		foreach( $data as $row ){
			$row["selling_price"] = round($row["selling_price"],2);
			$row["cover_img"] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/'.$row["cover_img"];
			$row["timeout"] = $row["prompted_num"] = '0';
			$row["discount"] = round(($row['selling_price'] / $row['par_value']) * 10 , 1);
			if( $type == 0 ){//正在进行
				$row["timeout"] = $row["end_time"] - REQUEST_TIME;
			}else if( $type == 1 || $type == 2 ){
				$row["timeout"] = $row["start_time"] - REQUEST_TIME;
				$row["prompted_num"] = $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_ticket_prompt` WHERE `ticket_id`='{$row["ticket_id"]}'");
				$ticketInfo = $this->select("`ticket_id` = '{$row["ticket_id"]}'", 'oto_ticket_info', '*', '', true);
				$row["prompted_num"] += $ticketInfo["love_number"];
			}
			$row["is_notice"] = 0;
			if( $user_id ){
				$row["is_notice"] = (int) Model_Active_Oneyuanpurchase::getInstance()->isNotice($row["ticket_id"], $user_id);
			}
			if( $type == 1 ){
				$list[$row["start_time"]][] = $row;
			}else{
				$list[] = $row;
			}
		}
		return $list;
	}
}