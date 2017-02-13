<?php
class Model_Admin_Appversion extends Base {

	private static $_instance;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	public function getCount() {
		return $this->_db->fetchOne("select count(*) from app_version where 1=1".$this->_where);
	}
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from app_version where 1=1 ".$this->_where." order by created desc";
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	public function setWhere($getData) {
		$where = '';
		if($getData['phone_type']){
			$type = trim($getData['phone_type']);
			$where .= "  and type = '{$type}'";
		}
		
		if($getData['is_update']){
			$where .= "  and is_update = '{$getData['is_update']}'";
		}
		$this->_where = $where;
	}
	
	public function upAppVersion($postData){
		$arr  = array(
				'type'     => trim($postData['phone_type']),
				'version' => trim($postData['version']),
				'url' => trim($postData['url']),
				'is_update'     => intval($postData['is_update']),
				'allow_wallet_show' => intval($postData['allow_wallet_show']),
				'content'     => trim($postData['content']),
				'channel'     => trim($postData['channel'])
		);
		if(!$postData['id']){
			$arr = array_merge($arr, array('created' => REQUEST_TIME));
			$insert_id = $this->_db->insert('app_version' , $arr);
			return $insert_id?$insert_id:false;
		}else{
			$where = array('id' =>$postData['id']);
			$affected_rows = $this->_db->update('app_version' , $arr , $where);
			return $affected_rows?$affected_rows:false;
		}
	
	}
	
	public function del($id){
		$delResult = $this->_db->delete('app_version', "`id` = $id");
		return $delResult;
	}
	
	public function check_version($id, $version, $phone_type) {
		if ($phone_type == 'ios') {
			$conditions = "`type` = '{$phone_type}' and `version` = '{$version}'";
			$id && $conditions .= " AND `id` <> $id";
			return $this->_db->fetchOne("select count(*) from app_version where $conditions");
		}
		return false;
	}
	
	public function check_version_android($id, $version, $phone_type, $channel) {
		$conditions = "`type` = '{$phone_type}' and `version` = '{$version}' and channel = '{$channel}'";
		$id && $conditions .= " AND `id` <> $id";
		return $this->_db->fetchOne("select count(*) from app_version where $conditions");
	}
	
}