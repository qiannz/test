<?php
class Model_Admin_Log extends Base {
	private static $_instance;

	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	public function getCount($type = '', $id = '') {
		if( $type != '' ){
			$where .= " and `type` = '{$type}'";
		}
		if( $id ){
			$where .= " and `from_id` = '{$id}'";
		}
		return $this->_db->fetchOne("select count(log_id) from oto_log_log where 1=1". $where . $this->_where);
	}
	
	public function getLogList($page, $pagesize = PAGESIZE, $type, $id) {
		if( $type != '' ){
			$where .= " and `type` = '{$type}'";
		}
		if( $id ){
			$where .= " and `from_id` = '{$id}'";
		}
		$start = ($page - 1) * $pagesize;
		$sql = "select * from oto_log_log where 1=1" . $where . $this->_where .  " order by created desc";
		$logs = $this->_db->limitQuery($sql, $start, $pagesize);
		$adminArray = $this->_db->fetchPairs("select id, userid from oto_admin");
		$menu = @include(ROOT_PATH.'var'.DIRECTORY_SEPARATOR.'manager'.DIRECTORY_SEPARATOR.deBase64($_COOKIE['_ad_userid']).'.php');
		foreach($logs as & $logItem) {
			$logItem['admin_user_name'] = $adminArray[$logItem['admin_id']];
			$pmark = $logItem['pmodule'];
			$cmark = $logItem['cmodule'];
			$logItem['pmodule'] = $menu[$pmark]['text'];
			$logItem['cmodule'] = $menu[$pmark]['children'][$cmark]['text'];
		}
		
		return $logs?$logs:array();
	}
	
	public function setWhere($getData) {
		$where = '';
		
		if($getData['pmodule']){
			$where .= "  and pmodule = '{$getData['pmodule']}'";
		}
		
		if($getData['cmodule']){
			$where .= "  and cmodule = '{$getData['cmodule']}'";
		}
		
		if($getData['activity']){
			$where .= "  and activity = '{$getData['activity']}'";
		}
		
		if($getData['field_value']){
			$where .= "  and operat_info LIKE '%{$getData['field_value']}%'";
		}		
		$this->_where = $where;
	}
	
	public function getLogInfo($type , $from_id){
		$sql = "SELECT `pmodule`,`cmodule` 
				FROM `oto_log_log` 
				WHERE `type`='{$type}' AND `from_id`='{$from_id}' LIMIT 1";
		return $this->_db->fetchRow($sql);
	}
	
	public function getPModel() {
		$sql = "select b.`mark`,b.`m_name` 
				from (
					select pmodule 
					from oto_log_log 
					where pmodule <> '' 
					group by pmodule
				) as a
				left join `module` as b ON a.`pmodule` = b.`mark`";
		return $this->_db->fetchAll($sql);
	}
	
	public function getCModel($pmodule) {
		$sql = "select b.`mark`,b.`m_name`
				from (
					select cmodule 
					from oto_log_log 
					where pmodule = '{$pmodule}' and cmodule <> '' 
					group by cmodule
				) as a
				left join `module` as b ON a.`cmodule` = b.`mark`";
		return $this->_db->fetchAll($sql);
	}
}