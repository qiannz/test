<?php
class Model_Admin_Config extends Base
{
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getCount() {
		$sql = "select count(*) from ( select count(*) from oto_config where 1=1 " . $this->_where . " group by config_key order by created desc ) A";
		return $this->_db->fetchOne($sql);
	}
	
	public function getConfigList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from oto_config where 1=1" . $this->_where . " group by config_key order by created desc";
		$configs = $this->_db->limitQuery($sql, $start, $pagesize);
		return $configs ? $configs : array();
	}
	
	public function setWhere($getData) {
		$where = '';
		
		if($getData['config_ex']){
			$where .= "  and config_ex like '%{$getData['config_ex']}%' ";
		}
		$this->_where = $where;
	}
	
	public function getConfigValue($configKey) {
		$sql = "select * from oto_config where config_key = '{$configKey}' order by created desc";
		$configValues = $this->_db->fetchAll($sql);
		return $configValues ? $configValues : array();
	}
	
	public function getConfigRow($id) {
		return $this->select("config_id = '{$id}'", 'oto_config' ,'*', '', true);
	}
	
	public function addOne($getData) {
		$id = !$getData['id']?0:$getData['id'];
		$config_key = trim($getData['config_key']);
		$config_value = trim($getData['config_value']);
		$config_ex = trim($getData['config_ex']);
		$arr = array(
					'config_key'    => $config_key, 
					'config_value'  => $config_value,
					'config_ex'     => $config_ex
				);
		if ($id == 0) {
			$arr['created'] = REQUEST_TIME;
			$insert_id = $this->_db->insert('oto_config', $arr);
			return $insert_id ? $insert_id : false;
		} else {
			if (array_key_exists('config_value', $getData)) { // 1. 单例情况
				$arr['updated'] = REQUEST_TIME;
				$affected_rows = $this->_db->update('oto_config', $arr,"`config_id` = $id");
				return $affected_rows?$affected_rows:false;
				
			} else { // 2.多例情况
				$newConfigVaule = array();
				unset($getData['id']);unset($getData['config_key']);unset($getData['config_ex']);
				$newConfigVaule = $getData;		
				$updateArr = array(
								'config_key'    => $config_key,
								'config_value'  => serialize($newConfigVaule),
								'config_ex'     => $config_ex,
								'updated'       => REQUEST_TIME
						);		
				$affected_rows = $this->_db->update('oto_config', $updateArr,"`config_id` = $id");
				return $affected_rows?$affected_rows:false;
			}
		}
		return false;
	}
	
	public function addMore($postData) {
		$config_key = trim($postData['config_key']);
		$config_ex = trim($postData['config_ex']);
		$k = $postData['k'];
		$v = $postData['v'];
		$config_value = array_combine($k, $v);
		$arr = array(
				'config_key'    => $config_key,
				'config_value'  => serialize($config_value),
				'config_ex'     => $config_ex,
				'created'       => REQUEST_TIME
		);
		$insert_id = $this->_db->insert('oto_config', $arr);
		return $insert_id ? $insert_id : false;
	}
	
	public function del($id) {
		$delResult = $this->_db->delete('oto_config', "`config_id` = '{$id}'");
		return $delResult;
	}
	
}