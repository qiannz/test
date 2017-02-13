<?php
class Model_Admin_Privateletter extends Base{
	private static $_instance;
	protected $_tbl_name;
	protected $_sql;
	protected $_user_id;
	protected $_where;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
	public function setWhere( $getData ){
		$where = "";
		if(!empty($getData)){
			if(isset($getData['content']) && !empty($getData['content'])){
				$where .= " and `message` like '%".Custom_String::HtmlReplace($getData['content'],3)."%'";
			}
			
			if( isset($getData['type']) && $getData['type']){
				$where .= " and `type` = '{$getData['type']}'";
			}
			
			if(isset($getData['field_value']) && !empty($getData['field_value'])){
				$user_id = $this->_db->fetchOne("select `user_id` from `oto_user` where `user_name` = '{$getData['field_value']}'");
				$where .= " and `user_id` = '{$user_id}'";
			}
				
		}
		$this->_where .= $where;
	}
	
	public function getLetterList( $page, $pagesize = PAGESIZE){
		if( $page < 1 ){
			$page = 1;
		}
		$start = ($page-1)*$pagesize;
		$sql = "SELECT * FROM `oto_pre_notice_backup` WHERE 1=1 {$this->_where} ORDER BY `created` DESC LIMIT {$start},{$pagesize}";
		$result = $this->_db->fetchAll($sql);	
		foreach ( $result as &$row ){
			$row["to_user_name"] = "";
			if( $row["user_id"] ){
				$userInfo = $this->getUserByUserId($row["user_id"]);
				if( !empty( $userInfo ) ){
					$row["to_user_name"] = $userInfo["user_name"];
				}
			}
			$row["send_user_name"] = "";
			if( $row["charter_user_id"] ){
				$userInfo = $this->getUserByUserId($row["charter_user_id"]);
				if( !empty( $userInfo ) ){
					$row["send_user_name"] = $userInfo["user_name"];
				}
			}
		}
		return empty($result)?array():$result;
	}
	
	public function getCount(){
		return $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_pre_notice_backup` WHERE 1=1 {$this->_where}");
	}
	
	public function letterAdd($postData){
		$time = REQUEST_TIME;
		$type = 'system';
		$openType = $postData['cmark']?$postData['cmark']:'';
		$from_id = intval( $postData['come_from_id'] );
		$content = mysql_escape_string( trim($postData["content"]) );
		$is_push = intval($postData['is_push']);
		$start_time = !empty($postData['stime'])?$postData['stime']:0;
		$end_time = 0;
		if( $start_time > 0 ){
			$start_time = strtotime($start_time);
			$end_time = strtotime('+1 hour',$start_time);
		}
    	switch ($postData['send_type']){
    		case 1:
    			$user_name = trim($postData['user_name']);
                $user_name = str_replace(array("\r","\r\n"), "\n", $user_name);
                $user_name = explode("\n", $user_name);
                $sql = "select `user_id` from `oto_user` where `user_name` ". $this->db_create_in($user_name);
                $resultArr = $this->_db->fetchCol($sql);
                $uids = "";
                $param = array(
                		'uids' => $resultArr,
						'message' => $content,
                		'notice_type'=>3,
                		'is_push'=> $is_push,
                		'city' => $this->_city
				);
                Model_Api_Message::getInstance()->addPreNotice($type, $openType, $from_id , $param);
                return true;
                break;
    		case 2:
    			$param = array(
    					'message'=>$content,
    					'notice_type'=>3,
    					'is_push'=>$is_push,
    					'start_time'=>$start_time,
    					'end_time'=>$end_time,
    					'city' => $this->_city
    			);
    			Model_Api_Message::getInstance()->addPreNotice($type, $openType, $from_id , $param);
    			return true;
    			break;
    	}
    	return false;
	}
	
	// 系统通知列表
	public function getSystemList($getData, $page, $pagesize = PAGESIZE){
		$where = '';
		if(isset($getData['content']) && !empty($getData['content'])){
			$where .= " and `message` like '%".$getData['content']."%'";
		}
		$start = ($page - 1) * $pagesize;
		$sql = "SELECT * FROM `oto_pre_notice` WHERE 1=1 $where ORDER BY CREATED DESC";
		$systems = $this->_db->limitQuery($sql, $start, $pagesize);
		
		foreach($systems as & $row) {
			if( $row["user_id"] ) {
				$userInfo = $this->getUserByUserId($row["user_id"]);
				if( !empty( $userInfo ) ){
					$row["user_name"] = $userInfo["user_name"];
				}
			}
		}
		return $systems?$systems:array();
	}
	
	//获取通知数量
	public function getSystemCount($getData){
		$where = '';
		if(isset($getData['content']) && !empty($getData['content'])){
			$where .= " and `message` like '%".$getData['content']."%'";
		}
		return $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_pre_notice` WHERE 1=1 $where");
	}
	
	// ajax编辑
	public function ajax_module_edit($getData){
		$id = $getData['id'];
		$value = $getData['value'];
		$result = $this->_db->update('oto_pre_notice',array("message" => $value), "`message_id` = $id");
		if($result){
			echo json_encode(true);
		}
	}
}