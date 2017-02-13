<?php
class Model_Admin_Active extends Base {
	
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getCount() {
		return $this->_db->fetchOne("select count(*) from act_active where 1=1". $this->_where . " order by created desc");
	}
	
	public function getActiveList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from act_active where 1=1" . $this->_where . " order by created desc";
		$actives = $this->_db->limitQuery($sql, $start, $pagesize);
		return $actives?$actives:array();
	}
	
	public function setWhere($getData) {
		$where = '';
		if($getData['act_name']){
			$where .= "  and act_name like '%{$getData['act_name']}%'";
		}
		$this->_where = $where;
	}
	
	public function actModi($postData) {
		$act_id = isset($postData['act_id'])?intval($postData['act_id']):0;
		$act_name = $postData['act_name'];
		$act_mart = $postData['act_mart'];
		$act_content = $postData['act_content'];
		$start_time = strtotime($postData['start_time']);
		$end_time = strtotime($postData['end_time']);
		$share_num = intval($postData['share_num']);
		$win_num = intval($postData['win_num']);

		$arr = array(
				'act_name'      => $act_name,
				'act_mart'      => $act_mart,
				'act_content'   => $act_content,
				'start_time'    => $start_time,
				'end_time'      => $end_time,
				'share_num'     => $share_num,
				'win_num'       => $win_num,
		);
		if ($act_id == 0) { // 新增
			$arr['created'] = time();
			$insert_id = $this->_db->insert('act_active', $arr);
			return $insert_id?$insert_id:false;
		} else { // 编辑
			$affected_rows = $this->_db->update('act_active', $arr,"`act_id` = $act_id");
			return $affected_rows?$affected_rows:false;
		}
	}
	
	public function del($act_id) {
		$delResult = $this->_db->delete('act_active', "`act_id` = $act_id");
		return $delResult;
	}
	
	public function getAttendCount($act_id) {
		return $this->_db->fetchOne("select count(*) from act_mobile where act_id = '{$act_id}'" . $this->_where_attend . " order by created desc");
	}
	
	public function getAttendList($page, $act_id, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from act_mobile where act_id = '{$act_id}' " . $this->_where_attend . " order by created desc";
		$attends = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($attends as $key => $attend) {
			$attends[$key]['shareNum'] = $this->_db->fetchOne("select count(id) from `act_share` where `mobile` = '{$attend['phone']}'");
		}
		return $attends?$attends:array();
	}
	
	public function setAttendWhere($getData) {
		$where_attend = '';
		if($getData['mobile']){
			$where_attend .= "  and phone = '{$getData['mobile']}'";
		}
		$this->_where_attend = $where_attend;
	}
	
	public function getShareCount($act_id, $mobile) {
		return $this->_db->fetchOne("select count(*) from act_share where act_id = '{$act_id}' and mobile = '{$mobile}'" . $this->_where_share . " order by created desc");
	}
	
	public function getShareList($page, $act_id, $mobile, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from act_share where act_id = '{$act_id}' and mobile = '{$mobile}' " . $this->_where_share . " order by created desc";
		$shares = $this->_db->limitQuery($sql, $start, $pagesize);
		return $shares?$shares:array();
	}
	
	public function setShareWhere($getData) {
		$where_share = '';
		if($getData['nick_name']){
			$where_share .= "  and nick_name like '%{$getData['nick_name']}%'";
		}
		
		if($getData['customer']){
			$where_share .= "  and customer_phone = '{$getData['customer']}'";
		}
		
		if($getData['ip']){
			$where_share .= "  and ip = '{$getData['ip']}'";
		}
		
		$this->_where_share = $where_share;
	}
	
	public function check_mart($act_mart, $act_id) {
		$conditions = "`act_mart` = '{$act_mart}'";
		$act_id && $conditions .= " AND `act_id` <> $act_id";
		return $this->_db->fetchOne("select count(*) from act_active where $conditions");
	}

	/**
	 * 获取50元中奖名单
	 */
	public function getFiftyWinning($act_id) {
		$activeRow = $this->select("`act_id` = '{$act_id}'", 'act_active', 'act_mart, share_num,  win_num', '', true);
		$sql = "select count(id) as num, mobile from act_share where act_id = '{$act_id}' GROUP BY mobile HAVING num >= {$activeRow['share_num']}";
		$mobileAll = $this->_db->fetchAll($sql);
		$phoneArray = array();
		foreach ($mobileAll as $mobileItem) {
			$sql = "select count(*) from (
				select * from act_share where mobile = '{$mobileItem['mobile']}' GROUP BY nick_name
			) A";
			$countNum = $this->_db->fetchOne($sql);
			if($countNum >= 30) {
				$phoneArray[] = $mobileItem['mobile'];
			}
		}
		return $phoneArray;
	}
	
	public function getTenWinning($act_id, $mobile) {
		$sql = "select count(id) from act_share where act_id = '{$act_id}' and customer_phone = '$mobile' and `had_received` = '0'";
		$num = $this->_db->fetchOne($sql);
		return $num ? $num : 0;		
	}


}