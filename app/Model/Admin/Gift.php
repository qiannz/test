<?php
class Model_Admin_Gift extends Base {
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
	}

    public function addToKenKey($addArr){
        $insert_id = $this->_db->insert('oto_app_welcome_gift',$addArr);
        return $insert_id?$insert_id:'';
    }
    
    public function edit($postData) {
    	$gift_id = isset($postData['gift_id'])?intval($postData['gift_id']):0;
    	$remark   = trim($postData['remark']);
    	$gift_value = trim($postData['gift_value']);
    	$gift_content = trim($postData['gift_content']);
    	$arr = array(
    			'remark'	     => $remark,
    			'gift_value'    => $gift_value,
    			'gift_content'  => $gift_content
    	);
    	$affected_rows = $this->_db->update('oto_app_welcome_gift', $arr,"`gift_id` = $gift_id");
    	return $affected_rows?$affected_rows:false;
    }

    public function getKenKeyList($page, $pagesize = PAGESIZE){
        $start = ($page - 1) * $pagesize;
        $sql = "select * from oto_app_welcome_gift";
        $data = $this->_db->limitQuery($sql, $start, $pagesize);
        return $data ? $data : array();
    }

    public function getCount() {
        return $this->_db->fetchOne("select count(*) from oto_app_welcome_gift");
    }

    //更改验证码状态 是否启用
    public function changeKenkeyStutas($arr, $gift_id){
        $result = $this->_db->update('oto_app_welcome_gift',$arr , "`gift_id` = $gift_id");
        return $result;
    }
    
    public function changePrizeStutas($arr, $gift_id){
    	$result = $this->_db->update('oto_app_welcome_prize',$arr , "`id` = $gift_id");
    	return $result;
    }

    public function getList($page, $pagesize = PAGESIZE){
        $start = ($page - 1) * $pagesize;
        $sql = "select * from oto_app_welcome_record where 1=1" .$this->_where. " order by created desc";
        $data = $this->_db->limitQuery($sql, $start, $pagesize);
        foreach ($data as & $row) {
        	$MobileNumber = substr($row['mobile'], 0, 7);
        	$row['area'] = $this->select("`MobileNumber` = '{$MobileNumber}'", "phone_section", '*', '', true);
        	// 奖品内容
        	$row['gift_value'] = $this->_db->fetchOne("select prize_name from oto_app_welcome_prize where id = '{$row['award_type']}'");
        }
        return $data ? $data : array();
    }
    
    public function getPrizeListCount() {
    	return $this->_db->fetchOne("select count(*) from oto_app_welcome_prize");
    }
    
    public function getPrizeList($page, $pagesize = PAGESIZE) {
    	$start = ($page - 1) * $pagesize;
    	$sql = "select * from oto_app_welcome_prize order by created desc";
    	$data = $this->_db->limitQuery($sql, $start, $pagesize);
    	return $data ? $data : array();
    }
    
    public function modiPrize($postData){
    	$prize_id = isset($postData['id'])?intval($postData['id']):0;
    	$prize_name = trim($postData['prize_name']);
    	$prize_content = trim($postData['prize_content']);
    	
    	$arr = array(
    			'prize_name' => $prize_name,
    			'prize_content' => $prize_content,
    			);
    	
    	if ($prize_id == 0) {
    		$arr['created'] = REQUEST_TIME;
    		$insert_id = $this->_db->insert('oto_app_welcome_prize',$arr);
    		return $insert_id?$insert_id:false;
    	} else {
    		$arr['updated'] = REQUEST_TIME;
    		$affected_rows = $this->_db->update('oto_app_welcome_prize', $arr,"`id` = $prize_id");
    		return $affected_rows?$affected_rows:false;
    	}
    }

    public function getListCount() {
        return $this->_db->fetchOne("select count(*) from oto_app_welcome_record where 1=1 ". $this->_where);
    }

    public function paymentPrizes($record_id){
        return $this->_db->update('oto_app_welcome_record' ,array('is_award'=> 1) ,"`gift_record_id` = $record_id");
    }

    public function setWhere($getData) {
        $where ='';
        if($getData['captcha']){
            $where .= "  and captcha = '{$getData['captcha']}'";
        }
        if($getData['mobile']){
            $where .= "  and mobile = '{$getData['mobile']}'";
        }
        if($getData['is_award']){
            if($getData['is_award'] == 2){
                $getData['is_award'] = 1;
            }elseif($getData['is_award'] == 1){
                $getData['is_award'] = 0;
            }
            $where .= "  and is_award = '{$getData['is_award']}'";
        }
        if($getData['award_type']){
            if($getData['award_type'] == 1){
                $getData['award_type'] = 0;
            }elseif($getData['award_type'] == 2){
                $getData['award_type'] = 1;
            }elseif($getData['award_type'] == 3){
                $getData['award_type'] = 2;
            }
            $where .= "  and award_type = '{$getData['award_type']}'";
        }
        
        if($getData['type']) {
        	$where .= " and `type` = '{$getData['type']}'";
        }
        
        if($getData['gift_id']){
            $where .= " and gift_id = '{$getData['gift_id']}'";
        }
        if (($getData['start_time']) && ($getData['end_time'])){
            $stime = strtotime($getData['start_time']."00:00:00");
            $etime = strtotime($getData['end_time']."23:59:59");
            $where .= "  and created >= '{$stime}'";
            $where .= "  and created <= '{$etime}'";
        }
        $this->_where = $where;
    }

    //统计各部门总数
    public function totelByGiftId($gift_id){
        return $this->_db->fetchOne ("select count(gift_record_id) from oto_app_welcome_record where gift_id = '{$gift_id}'");
    }

    //查询验证码是否已经存在
    public function verificaCaptcha($captcha){
        return $this->_db->fetchOne ("select 1 from oto_app_welcome_gift where captcha = '{$captcha}' ");
    }
    
    //查询奖品名是否已经存在
    public function verificaPrize($prize_name, $prize_id){
    	return $this->_db->fetchOne ("select 1 from oto_app_welcome_prize where prize_name = '{$prize_name}' and id <> '{$prize_id}' ");
    }

    //根据用户绑定ID查询验证码
    public function QueryCaptcha($record_id){
        return $this->_db->fetchOne("select captcha from oto_app_welcome_record where gift_record_id = '{$record_id}'");
    }
}