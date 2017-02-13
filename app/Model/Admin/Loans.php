<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-17
 * Time: 下午4:21
 */

class Model_Admin_Loans extends Base {
    private static $_instance;
    protected  $_where ='';
    protected  $_monday ='';

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
            $where .= "  and user_name = '{$getData['user_name']}'";
        }
        if (($getData['start_time'])&& ($getData['end_time']) ){
            $starttime = strtotime($getData['start_time']);
            $where .= "  and loans_time >= '{$starttime}'";
            $endtime = strtotime($getData['end_time']);
            $where .= "  and loans_time <= '{$endtime}'";
        }
        $this->_where = $where;
    }

    public function getLoansList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        if(!$this->_where){
            $sql = "select * from oto_task_money  where operat_status = 2 order by loans_time desc";
        }else{
            $sql = "select * from oto_task_money  where  operat_status = 2" . $this->_where . " order by loans_time desc";
        }
        $actives = $this->_db->limitQuery($sql, $start, $pagesize);
        return $actives?$actives:array();
    }

    public function getCount() {
        if(!$this->_where){
            return $this->_db->fetchOne("select count(*) from oto_task_money  where operat_status = 2");
        }else{
            return $this->_db->fetchOne("select count(*) from oto_task_money  where operat_status = 2". $this->_where ."");
        }
    }

    public function getStatistics(){
        if(!$this->_where){
            $total_amount = $this->_db->fetchRow("select sum(amount) as total_amount from oto_task_money where operat_status = 2 and  operat_result = 1");
            $num = $this->_db->fetchAll("select distinct user_id as num from oto_task_money where operat_status = 2  and  operat_result = 1");
        }else{
            $total_amount = $this->_db->fetchRow("select sum(amount) as total_amount from oto_task_money where operat_status = 2 and  operat_result = 1 ".$this->_where);
            $num = $this->_db->fetchAll("select distinct user_id as num from oto_task_money where operat_status = 2 and operat_result = 1 ".$this->_where);
        }
        $result['total_amount'] = $total_amount['total_amount'];
        $result['num'] = count($num);
        return $result ;
    }


    public function getAdminUser($id){
        return $this->_db->fetchOne("select userid from oto_admin where id = '{$id}'");
    }


}
