<?php
class Model_Admin_Rebate extends Base {
	private static $_instance;
	protected $_where;
    protected $_rebetaWhere;
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function __construct() {
        parent::__construct();
        $this->_where = '';
        $this->_rebetaWhere ='';
    }

    public function setWhere($getData) {
        $where ='';
        if (($getData['start_time']) && ($getData['end_time'])){
            $stime = strtotime($getData['start_time'].' '.'00:00:00');
            $etime = strtotime($getData['end_time'].' '.'23:59:59');
            $where .= "  and created >= '{$stime}'";
            $where .= "  and created <= '{$etime}'";
        }
        if($getData['username']){
            $where .= "  and user_name = '{$getData['username']}'";
        }
        if($getData['shopname']){
            $shop_name = trim($getData['shopname']);
            $shop_id = $this->getShopId($shop_name);
            $where .= "  and shop_id = '{$shop_id}'";
        }
        if($getData['order_no']){
        	$order_no = trim($getData['order_no']);
        	$where .= "  and order_no = '{$order_no}'";
        }
        if($getData['captcha']){
        	$captcha = trim($getData['captcha']);
        	$where .= "  and captcha = '{$captcha}'";
        }
        if($getData['award_start'] && $getData['award_end']){
            $where .= "  and award >= '{$getData['award_start']}'";
            $where .= "  and award <= '{$getData['award_end']}'";
        }
        if($getData['rebateNum_start'] || $getData['rebateNum_end']){
            if($getData['rebateNum_start'] && $getData['rebateNum_end']){
                $Arr['rebateNum_start'] = $getData['rebateNum_start'];
                $Arr['rebateNum_end'] = $getData['rebateNum_end'];
            }elseif($getData['rebateNum_start']){
                $Arr['rebateNum_start'] = $getData['rebateNum_start'];
            }elseif($getData['rebateNum_end']){
                $Arr['rebateNum_end'] = $getData['rebateNum_end'];
            }
        }
        $this->_rebetaWhere = $Arr;
        $this->_where = $where;
    }

    public function geRebetaCount(){
    	$sql = '';
        if($this->_rebetaWhere['rebateNum_start'] && $this->_rebetaWhere['rebateNum_end']){
        	$userArray = $this->_db->fetchCol("select user_id from oto_task_clerk_coupon where 1=1 ".$this->_where."group by user_id having count(*) >= '{$this->_rebetaWhere['rebateNum_start']}' and count(*) <= '{$this->_rebetaWhere['rebateNum_end']}'");
            $sql = "select count(*) as num , shop_id , user_name, user_id ,created
                    from oto_task_clerk_coupon
                    where " . $this->db_create_in($userArray, 'user_id') . $this->_where." group by user_id";
        }
        /*
        elseif($this->_rebetaWhere['rebateNum_start']){
        	$userArray = $this->_db->fetchCol("select user_id from oto_task_clerk_coupon where 1=1 ".$this->_where."group by user_id having count(*) >= '{$this->_rebetaWhere['rebateNum_start']}'");
            $sql = "select count(*) as num , shop_id , user_name, user_id ,created
                    from oto_task_clerk_coupon
                    where " . $this->db_create_in($userArray, 'user_id') .$this->_where." group by user_id";
        }elseif($this->_rebetaWhere['rebateNum_end']){
        	$userArray = $this->_db->fetchCol("select user_id from oto_task_clerk_coupon where 1=1 ".$this->_where."group by user_id having count(*) <= '{$this->_rebetaWhere['rebateNum_end']}'");
            $sql = "select count(*) as num , shop_id , user_name, user_id ,created
                    from oto_task_clerk_coupon
                    where " . $this->db_create_in($userArray, 'user_id') . $this->_where." group by user_id";
        }
        */
        return $sql;
    }

    public function getRebateList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        
        $sql = $this->geRebetaCount();
        if(!$sql) {
            $sql = "select * from oto_task_clerk_coupon  where 1=1" . $this->_where . " order by `created` desc";
        }
        $result = $this->_db->limitQuery($sql, $start, $pagesize);
        return $result?$result:array();
    }

    public function getCount() {
        if($this->_rebetaWhere['rebateNum_start'] && $this->_rebetaWhere['rebateNum_end']){
            $result  = $this->_db->fetchAll($this->geRebetaCount());
            return count($result);
        }
        /*
        elseif($this->_rebetaWhere['rebateNum_start']){
            $result  = $this->_db->fetchAll($this->geRebetaCount());
            return count($result);
        }elseif($this->_rebetaWhere['rebateNum_end']){
            $result  = $this->_db->fetchAll($this->geRebetaCount());
            return count($result);
        }
        */
        else{
            return $this->_db->fetchOne("select count(*) from oto_task_clerk_coupon where 1=1". $this->_where ."");
        }
    }

    public function getShopId($shop_name){
        return $this->_db->fetchOne("select shop_id from oto_shop where shop_name = '{$shop_name}'");
    }

    public  function getDetailList($parameterKey, $parameterVal,$page,$pagesize=PAGESIZE){
        $start = ($page - 1) * $pagesize;
        if($parameterKey =='user_id'){
             $sql = "select * from oto_task_clerk_coupon where user_id = '{$parameterVal}'".$this->_where."order by created desc";
        }elseif($parameterKey =='id'){
             $sql = "select * from oto_task_clerk_coupon where id = '{$parameterVal}'".$this->_where."order by created desc";
        }
        $result = $this->_db->limitQuery($sql, $start, $pagesize);
        return $result?$result:array();
    }

    public function getDetailCount($parameterKey,$parameterVal){
        if($parameterKey =='user_id'){
            return $this->_db->fetchOne("select count(*) from oto_task_clerk_coupon where  user_id = '{$parameterVal}'".$this->_where);
        }elseif($parameterKey =='id'){
            return $this->_db->fetchOne("select count(*) from oto_task_clerk_coupon where id = '{$parameterVal}'".$this->_where);
        }
    }
}