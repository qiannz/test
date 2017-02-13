<?php
class Model_Api_Deals extends Base
{
	private static $_instance;
	private $_table = 'oto_deals';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getSpecialList($getData, $city) {
		//特卖状态 1：进行中 2：即将开始 3：已结束
		
		$type = intval($getData['type']);
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$pagesize = !$getData['pagesize'] ? PAGESIZE : intval($getData['pagesize']);
		
		$key = "get_special_list_{$city}_{$type}_{$page}";
		$data = $this->getData($key);
		
		
		if(empty($data)) {
			$where = "`city` = '{$city}'";
			$start = ($page - 1) * $pagesize;
			switch($type) {
				case 1:
					$where .= " and `start_time` < '" . REQUEST_TIME . "' and `end_time` > '". REQUEST_TIME."'";
					break;
				case 2:
					$where .= " and `start_time` > '" . REQUEST_TIME . "'";
					break;
				case 3:
					$where .= " and `end_time` < '" . REQUEST_TIME . "'";
					break;
			}
			
			$sql = "select * from `{$this->_table}` where {$where} order by `sequence` asc, `created` desc limit {$start}, {$pagesize}";			
			$data = $this->_db->fetchAll($sql);
			$this->setData($key, $data);
		}
		
		return $data;
	}
}