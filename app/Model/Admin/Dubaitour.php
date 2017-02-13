<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-15
 * Time: 下午3:46
 */

class Model_Admin_Dubaitour extends Base {
    private static $_instance;
    protected $_table = 'oto_task_every_day';

    public static function getInstance() {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();
    }

    public function getdubaitourList() {
        $task_start_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']);
        $task_end_time   = strtotime($GLOBALS['GLOBAL_CONF']['TASK_END_TIME']);
        $sql ="select
	           count(A.good_id) as `effective_upload`,
	           A.user_name,
	           (select sum(award) from oto_task_log where task_type != 5 and user_id = A.user_id) as awardSum
               from `oto_good` A
               where A.`good_status` = '1' and A.`is_del` = '0' and  A.`created` > '{$task_start_time}' and A.`created` < '{$task_end_time}'
               group by A.`user_id`
               order by `effective_upload` desc, A.`user_name` asc
               limit 0,20";

        $result = $this->_db->fetchAll($sql);
        return $result?$result:array();
    }

}