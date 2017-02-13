<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 上午9:38
 */

class Model_Admin_Withdrawal extends Base {
    private static $_instance;
    protected  $_where   ='';
    protected  $_monday  ='';
    protected  $_pmodule ='';
    protected  $_cmodule ='';

    public static function getInstance() {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();
        $this->_where = '';
        //$this->_monday = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"))));
        $this->_pmodule = '任务管理';
        $this->_cmodule = '提现记录';
    }

    public function setWhere($getData) {
        $where ='';
        if($getData['user_name']){
            $where .= "  and user_name = '{$getData['user_name']}'";
        }
        if ($getData['start_time'] && $getData['end_time']){
            $starttime = strtotime($getData['start_time']);
            $where .= "  and app_time >= '{$starttime}'";
            $endtime = strtotime($getData['end_time']);
            $where .= "  and app_time <= '{$endtime}'";
        }
        $this->_where = $where;
    }

    public function getWithdrawalList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        if(!$this->_where){
            $sql = "select * from oto_task_money  where operat_status = 1 order by app_time desc";
        }else{
            $sql = "select * from oto_task_money  where operat_status = 1" . $this->_where . " order by app_time desc";
        }
        $actives = $this->_db->limitQuery($sql, $start, $pagesize);
        return $actives?$actives:array();
    }

    public function getCount() {
        if(!$this->_where){
            return $this->_db->fetchOne("select count(*) from oto_task_money  where operat_status = 1");
        }else{
            return $this->_db->fetchOne("select count(*) from oto_task_money  where operat_status = 1". $this->_where ."");
        }
    }

    public function getStatistics(){
        if(!$this->_where){
            $total_amount = $this->_db->fetchRow("select sum(amount) as total_amount from oto_task_money where operat_status = 1");
            $num = $this->_db->fetchAll("select distinct user_id as num from oto_task_money where operat_status = 1");
        }else{
            $total_amount = $this->_db->fetchRow("select sum(amount) as total_amount from oto_task_money where operat_status = 1".$this->_where);
            $num = $this->_db->fetchAll("select distinct user_id as num from oto_task_money where operat_status = 1".$this->_where);
        }
        $result['total_amount'] = $total_amount['total_amount'];
        $result['num'] = count($num);
        return $result;
    }


    public function ChangeStatus($getData){
        $id      = $getData['id'];
        $reason  = $getData['selected_value'];
        $operate = $getData['operate'];
        $user_id = $getData['user_id'];
        $amount  = $getData['amount'];
        $admin_id  = $getData['admin_id'];
        if($operate == 1){  //确认转账
            $Arr = array(
                'user_id' => $user_id,
                'award'   => -$amount,
                'task_type'    => 5,
                'created' =>REQUEST_TIME
            );
            $log_id = $this->_db->insert('oto_task_log' , $Arr );
            $result = $this->_db->update('oto_task_money' , array('admin_id'=>$admin_id ,'operat_status'=>2,'operat_result'=>1,'loans_time'=>REQUEST_TIME), array('money_id'=>$id));
            if($log_id && $result){
                Custom_Log::log($admin_id, "放款成功 <b>放款金额 ：{$amount}</b>元整", $this->_pmodule,$this->_cmodule, 'loans');
                return true ;
            }else{
                return false;
            }
        }else if($operate == 2){ //确认取消转账
            $result = $this->_db->update('oto_task_money' , array('admin_id'=>$admin_id,'operat_status'=>2,'operat_result'=>-1,'reason_of_failure'=>$reason,'loans_time'=>REQUEST_TIME), array('money_id'=>$id));{
               if($result){
                   Custom_Log::log($admin_id, "放款失败 <b>失败原因：{$reason}</b>", $this->_pmodule,$this->_cmodule, 'loans');
                   return true;
               }else
                   return false;
            }
        }
    }
}
