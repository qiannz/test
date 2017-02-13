<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 上午10:14
 */

class Model_Admin_Details extends Base {
    private static $_instance;
    protected $_where ;


    public static function getInstance() {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();
    }

    public function setWhere($getData){
        $where = '';
        if($getData['user_name']){
            $user_id = $this->getUserIdByUserName(trim($getData['user_name']));
            $user_id = $user_id ? $user_id : 0 ;
            $where = " and user_id = '{$user_id}'";
        }
        $this->_where = $where;
    }

    public function getDetailsList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        $sql = "select * from oto_task_log  where task_type != 5 ".$this->_where." order by log_id desc";
        $result  = $this->_db->limitQuery($sql, $start, $pagesize);
        foreach($result as &$row){
            $username = $this->_db->fetchOne("select user_name from oto_user where user_id = '{$row['user_id']}'");
            $row['user_name'] = $username;
        }
        return $result?$result:array();
    }

    public function getCount() {
        return $this->_db->fetchOne("select count(log_id) from oto_task_log  where task_type != 5".$this->_where);
    }

}