<?php
class Model_Admin_App extends Base
{
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getAppList() {
	    return $this->_db->fetchOne("select config_value from oto_config where config_key = 'APP_DAY_SURPRISE'");
	}
	
	public function edit($postData) {		
		$every_day_surprise = $postData['every_day_surprise'];
		$daySurprise = $this->getAppList();
		
		$daySurpriseArray = unserialize($daySurprise);
		$daySurpriseArray[$this->_ad_city] =  $every_day_surprise;
	    
	    //每日惊喜
	    $this->_db->update('oto_config', array('config_value' => serialize($daySurpriseArray), 'updated' => REQUEST_TIME), "config_key = 'APP_DAY_SURPRISE'");	     
	    return true;
	}
	
	/**
	 * 将数列反序列化后的数组写入config 文件夹
	 */
	
	public function unserializeConfig() {
		$config = array();
		$appInfo = $this->_db->fetchAll("select config_key, config_value from oto_config ");
		foreach($appInfo as $key => $appItem) {
			if (substr($appItem['config_value'], 0,2) == 'a:') {
				$config[$appItem['config_key']][] = unserialize($appItem['config_value']);
			} else {
				$config[$appItem['config_key']] = $appItem['config_value'];
			}
		}
		return $config;
	}	
}