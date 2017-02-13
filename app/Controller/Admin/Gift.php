<?php
class Controller_Admin_Gift extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Gift::getInstance();
	}
	
	public function listAction() {
        $page = $this->_http->get('page');
        $gift_id = $this->_http->get('gift_id');
        $page = !$page ? 1 : intval($page);
        $page_str = '';
        $getData = $this->_http->getParams();

        foreach($getData as $key => $value) {
            if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
                $page_str .= "{$key}:{$value}/";
            }
        }

        $page_info = $this->_get_page($page);

        $this->_model->setWhere($getData);
        $data = $this->_model->getList($page);
        $page_info['item_count'] = $this->_model->getListCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }

        $this->_format_page($page_info);
        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('gift_id', $gift_id);
        $this->_tpl->assign('data', $data);
        $this->_tpl->assign('request', stripslashes_deep($_REQUEST));

		$this->_tpl->display('admin/gift.php');
	}

    public function giftListAction(){
        $page = $this->_http->get('page');
        $page = !$page ? 1 : intval($page);
        $page_str = '';
        $getData = $this->_http->getParams();

        foreach($getData as $key => $value) {
            if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
                $page_str .= "{$key}:{$value}/";
            }
        }

        $page_info = $this->_get_page($page);
        $data = $this->_model->getKenKeyList($page);
        foreach($data as &$val){
            $val['total'] = $this->_model->totelByGiftId($val['gift_id']);
        }
        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }

        $this->_format_page($page_info);
        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('data', $data);

        $this->_tpl->display('admin/gift_list.php');
    }


    public function disableAction(){
        $gift_id = $this->_http->get('gift_id')?$this->_http->get('gift_id'):'';
        $_switch = $this->_http->get('_switch')?$this->_http->get('_switch'):'';
        if($_switch == 'on'){
            $Arr = array('is_enable'=> 1);
            $result = $this->_model->changeKenkeyStutas($Arr,$gift_id);
            if($result){
                $Msg = array('status'=>100);
            }
        }else{
            $Arr = array('is_enable'=> 0);
            $result = $this->_model->changeKenkeyStutas($Arr,$gift_id);
            if($result){
                $Msg = array('status'=>100);
            }
        }
        exit(json_encode($Msg));
    }
    
    
    public function disablePrizeAction(){
    	$gift_id = $this->_http->get('id')?$this->_http->get('id'):'';
    	$_switch = $this->_http->get('_switch')?$this->_http->get('_switch'):'';
    	if($_switch == 'on'){
    		$Arr = array('is_enable'=> 1);
    		$result = $this->_model->changePrizeStutas($Arr,$gift_id);
    		if($result){
    			$Msg = array('status'=>100);
    		}
    	}else{
    		$Arr = array('is_enable'=> 0);
    		$result = $this->_model->changePrizeStutas($Arr,$gift_id);
    		if($result){
    			$Msg = array('status'=>100);
    		}
    	}
    	exit(json_encode($Msg));
    }
    

    public function addAction(){
        if($this->_http->isPost()){
            $postData = $this->_http->getPost();
            $postData['created'] = REQUEST_TIME;
            $result = $this->_model->addToKenKey($postData);
            if($result){
                Custom_Common::showMsg(
                    '验证码添加成功',
                    'back',
                    array('add' => '继续添加验证码' , 'gift-list' => '奖品管理','list' => '领奖列表')
                );
            }else{
                Custom_Common::showMsg(
                    '验证码添加失败',
                    'back'
                );
            }
        }
        $this->_tpl->display('admin/gift_add.php');
    }
    
    public function editAction() {
    	$gift_id = $this->_http->get('gift_id');
    	if($this->_http->isPost()){
    		$postData = $this->_http->getPost();
    		$result = $this->_model->edit($postData);
    		if($result){
    			Custom_Common::showMsg(
    			'奖品编辑成功',
    			'back',
    			array('edit/gift_id:' . $postData['gift_id'] => '重新编辑奖品' , 'gift-list' => '奖品管理','list' => '领奖列表')
    			);
    		}else{
    			Custom_Common::showMsg(
    			'奖品编辑失败',
    			'back'
    			);
    		}
    		
    	}
    	
    	$giftRow = $this->select("`gift_id` = {$gift_id}", "oto_app_welcome_gift", '*', '', true);
    	$this->_tpl->assign('giftRow', $giftRow);
    	$this->_tpl->display('admin/gift_add.php');
    }

    public function paymentPrizesAction(){
        $record_id = $this->_http->get('record_id')?intval($this->_http->get('record_id')):'';
        if($record_id){
            $result  = $this->_model->paymentPrizes($record_id);
            $captcha = $this->_model->QueryCaptcha($record_id);
            if($result){
                Custom_Log::log($this->_userInfo['id'], "确认发奖 该记录id:{$record_id} ,验证码为:{$captcha} ,发奖时间:".date('Y-m-d H:i:s',REQUEST_TIME)."", $this->pmodule, $this->cmodule, 'awards');
                $msg = array('msg' =>'success' , 'status' => 100);
            }
        }
        exit(json_encode($msg));
    }


    public function batchAuditAction(){
        $ids = $this->_http->get('id');
        $idArray = explode(',', $ids);
        // 审核通过所选商品
        $sql  = "update oto_app_welcome_record set is_award = 1 where " . $this->db_create_in($idArray, 'gift_record_id') ."";
        $result = $this->_db->query($sql);
        if ($result) {
            foreach($idArray as $val){
                $captcha = $this->_model->QueryCaptcha($val);
                Custom_Log::log($this->_userInfo['id'], "确认发奖 该记录id:{$val} ,验证码为:{$captcha} ,发奖时间:".date('Y-m-d H:i:s',REQUEST_TIME)."", $this->pmodule, $this->cmodule, 'awards');
            }
           exit(json_encode(array('msg'=>100)));
        } else {
            exit(json_encode(array('msg'=>101)));
        }
    }

    public function verificaAction(){
        $captcha = $this->_http->get('captcha');
        if($captcha){
            $result  = $this->_model->verificaCaptcha($captcha);
            if($result){
                exit(json_encode(false));
            }
        }
        exit(json_encode(true));
    }
    
    public function exportAction() {
    	set_time_limit(0);
    	
    	$captcha = $this->_http->get('captcha');
    	$mobile = $this->_http->get('mobile');
    	$is_award = $this->_http->get('is_award');
    	$award_type = $this->_http->get('type');
    	$stime= $this->_http->get('stime');
    	$etime = $this->_http->get('etime');
    	
    	$where = '1';
    	if($captcha) {
    		$where .= " and `captcha` = '{$captcha}'";
    	}
    	
    	if($mobile) {
    		$where .= " and `mobile` = '{$mobile}'";
    	}
    	
    	if($is_award) {
    		$where .= $is_award == 1 ? " and `is_award` = '0'" : " and `is_award` = '1'";
    	}
    	
    	if($award_type) {
    		if($award_type == 1) {
    			$where .= " and `award_type` = '0'";
    		} elseif($award_type == 2) {
    			$where .= " and `award_type` = '1'";
    		} elseif($award_type == 3) {
    			$where .= " and `award_type` = '2'";
    		}
     	}
    	
    	if($stime && $etime) {
    		$stime = strtotime($stime);
    		$etime = strtotime($etime . " 23:59:59");
    		$where .= " and `created` >= '{$stime}' and `created` <= '{$etime}'";
    	}
    	
    	$detailsArray = array();
    	$details = $this->select($where, 'oto_app_welcome_record', '*', '`created` desc');
    	
    	foreach($details as $key => $item) {
    		$detailsArray[$key]['mobile'] = $item['mobile'];
    		$MobileNumber = substr($item['mobile'], 0, 7);
    		$row = $this->select("`MobileNumber` = '{$MobileNumber}'", "phone_section", '*', '', true);
    		$detailsArray[$key]['Mobile_attribution'] = $row['MobileArea'].'(' . $row['MobileType'] . ')';    		
    		$detailsArray[$key]['tokenkey'] = $item['tokenkey'];
    		$detailsArray[$key]['captcha'] = $item['captcha'];
    		$detailsArray[$key]['is_award'] = $item['is_award'] == 0 ? '未发放':'已发放';
    		if($item['award_type'] == 0) {
    			$detailsArray[$key]['award_type'] = '自主选择';
    		} elseif($item['award_type'] == 1) {
    			$detailsArray[$key]['award_type'] = '10元现金券';
    		} elseif($item['award_type'] == 2) {
    			$detailsArray[$key]['award_type'] = '5元花费';
    		}
    		$detailsArray[$key]['created'] = date('Y-m-d H:i:s',$item['created']);
    	}
    	
    	$detailsArray = array_merge(array(array('手机号码', '手机归属地','tokenkey','验证码','发放状态', '奖品类型', '绑定时间')), $detailsArray);
    	$excelObject = Third_Excel::getInstance('UTF-8', false, '使用明细');
    	$excelObject->addArray($detailsArray);
    	$excelObject->generateXML('gift');    	
    	
    }
    
    /**
     * 奖品列表
     */
    public function prizeListAction() {
    	$page = $this->_http->get('page');
    	$page = !$page ? 1 : intval($page);
    	$page_str = '';
    	$getData = $this->_http->getParams();
    	
    	foreach($getData as $key => $value) {
    		if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
    			$page_str .= "{$key}:{$value}/";
    		}
    	}
    	$page_info = $this->_get_page($page);
    	
    	$data = $this->_model->getPrizeList($page);
    	$page_info['item_count'] = $this->_model->getPrizeListCount();
    	if($page_str){
    		$page_info['page_str'] = $page_str;
    	}
    	
    	$this->_format_page($page_info);
    	$this->_tpl->assign('page_info', $page_info);
    	$this->_tpl->assign('page', $page);
    	$this->_tpl->assign('data', $data);
    	$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
    	
    	$this->_tpl->display('admin/prize_list.php');
    }
    
    /**
     * 新增奖品
     */
    public function prizeAddAction() {
    	if($this->_http->isPost()){
    		$postData = $this->_http->getPost();
    		$result = $this->_model->modiPrize($postData);
    		if($result){
    			Custom_Common::showMsg(
    			'奖品添加成功',
    			'back',
    			array('prize-add' => '继续添加奖品' , 'prize-list' => '奖品列表','list' => '领奖列表')
    			);
    		}else{
    			Custom_Common::showMsg(
    			'奖品添加失败',
    			'back'
    			);
    		}
    	}
    	$this->_tpl->display('admin/prize_add.php');
    }
    
    /**
     * 编辑奖品
     */
    public function prizeEditAction() {
    	$id = $this->_http->get('id');
    	if($this->_http->isPost()){
    		$postData = $this->_http->getPost();
    		$result = $this->_model->modiPrize($postData);
    		if($result){
    			Custom_Common::showMsg(
    			'奖品编辑成功',
    			'back',
    			array('prize-edit/id:' . $postData['id'] => '重新编辑奖品' , 'prize-list' => '奖品管理','list' => '领奖列表')
    			);
    		}else{
    			Custom_Common::showMsg(
    			'奖品编辑失败',
    			'back'
    			);
    		}
    	
    	}
    	$prizeRow = $this->select("`id` = {$id}", "oto_app_welcome_prize", '*', '', true);
    	$this->_tpl->assign('prizeRow', $prizeRow);
    	$this->_tpl->assign('pid', $id);
    	$this->_tpl->display('admin/prize_add.php');
    }
    
    public function verificaPrizeAction(){
    	$prize_name = trim($this->_http->get('prize_name'));
    	$prize_id = trim($this->_http->get('p_id'));
    	if($prize_name){
    		$result  = $this->_model->verificaPrize($prize_name, $prize_id);
    		if($result){
    			exit(json_encode(false));
    		}
    	}
    	exit(json_encode(true));
    }
    
}