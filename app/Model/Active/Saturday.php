<?php
class Model_Active_Saturday extends Base {
	
	private static $_instance;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function getInviteNum($user_id){
        $sql = "select count(log_id) from oto_task_log where task_type = '7' and user_id = '{$user_id}'" ;
        return $this->_db->fetchOne($sql);
    }
	
}