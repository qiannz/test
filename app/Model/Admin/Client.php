<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 上午9:38
 */

class Model_Admin_Client extends Base {
    private static $_instance;
    protected $_where;
    protected $_monday;

    public static function getInstance() {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();
        $this->_where = '';
        $this->_monday = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"))));
    }

    public function setWhere($getData) {
        $where ='';
        if($getData['user_name']){
            $username = trim($getData['user_name']);
            $where .= "  and user_name = '{$getData['user_name']}'";
        }
        if (($getData['start_time']) && $getData['end_time']){
            $created_starat = strtotime($getData['start_time']);
            $created_end    = strtotime($getData['end_time']);
            $where .= "  and created >= '{$created_starat}'";
            $where .= "  and created <= '{$created_end}'";
        }
        $this->_where = $where;
    }

    public function getClientList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        if(!$this->_where){
            $sql = "select * from oto_task_client  where created >='{$this->_monday}' order by created desc";
        }else{
            $sql = "select * from oto_task_client  where 1=1" . $this->_where . " order by created desc";
        }
        $actives = $this->_db->limitQuery($sql, $start, $pagesize);
        return $actives?$actives:array();
    }

    public function getCount() {
        if(!$this->_where){
            return $this->_db->fetchOne("select count(*) from oto_task_client  where created >= '{$this->_monday}'");
        }else{
            return $this->_db->fetchOne("select count(*) from oto_task_client  where 1=1". $this->_where ."");
        }
    }

    public function getStatistics(){
        if(!$this->_where){
            $total_amount  = $this->_db->fetchRow("select count(*) as used_num ,count(distinct user_id) as num, sum(award) as total_amount from oto_task_client where created >= '{$this->_monday}'");
        }else{
            $total_amount   = $this->_db->fetchRow("select count(*) as used_num ,count(distinct user_id) as num, sum(award) as total_amount from oto_task_client  where 1=1 ".$this->_where ."");
        }
        return $total_amount;
    }

}