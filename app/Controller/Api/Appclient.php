<?php
/**
 * 商城营业员模块
 * @author Qiannz
 *
 */
class Controller_Api_Appclient extends Controller_Api_Abstract {

    private $_model;
    private $_pagesize = 20;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Api_App::getInstance();
    }
    /**
     * 商城营业员端加密
     */
    protected function auth($getData) {
    	$SignData = $getData['SignData'];
    	unset($getData['m']);
    	unset($getData['c']);
    	unset($getData['act']);
    	unset($getData['SignData']);
    	
    	ksort($getData);
    	$md5String = '';
    	foreach ($getData as $k => $v) {
    		$md5String .= "{$k}=".urldecode($v)."&";
    	}
    	
    	$md5String = substr($md5String, 0, -1);    	
    	$desString = 'sign=' . md5($md5String) . '&time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
    	
    	Third_Des::$key = '34npzntC';
    	if( !$SignData || Third_Des::encrypt($desString) != $SignData) {
    		exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
    	}
    }
    /**
     * 营业员手机登录
     */
    public function loginAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$mobile = $getData['mobile'];
    	if(!preg_match('/^1[2-9][0-9]{9}$/', $mobile)) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请输入正确的手机号码')));
    	}
    	
    	$code = $getData['code'];
    	if(!$code){
			exit(json_encode($this->returnArr(0, array(), 300, '请输入验证码')));
		}
	
		$mianUserInfoRow = Custom_AuthLogin::get_user_by_mobile($mobile);
		if($mianUserInfoRow['GetUserInfosResult'] == 1) {
			$mobileRow = $this->getUserByUuid($mianUserInfoRow['userInfo']['UserId']);
			$shop_commodity_user_info = $this->select("`user_id` = '{$mobileRow['user_id']}' and `user_type` <> '3'", 'oto_user_shop_commodity', '*', '', true);
			$shop_commodity_user_info['shop_name'] = $this->getShopFieldById($shop_commodity_user_info['shop_id'], 'shop_name');			
			$shop_commodity_user_info['user_name'] = $mianUserInfoRow['userInfo']['UserName'];
			$shop_commodity_user_info['avatar'] = $mianUserInfoRow['userInfo']['userField']['Avatar50'];
			$shop_commodity_user_info['uuid'] = $mianUserInfoRow['userInfo']['userField']['UserId'];
			if(!empty($shop_commodity_user_info)) {
				//测试账号
				if($mobile == '13262219995' && $code == '123456') {
					exit(json_encode($this->returnArr(1, $shop_commodity_user_info)));
				}
				
				if($mobileRow['code'] == $code) {
					//清空验证码
					$this->_db->update('oto_user', array('code' => ''), array('user_id' => $mobileRow['user_id']));
					//返回登录结果
					exit(json_encode($this->returnArr(1, $shop_commodity_user_info)));
				} else {
					exit(json_encode($this->returnArr(0, array(), 300, '验证码错误')));
				}
			} else {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不是店员')));
			}
		} else {
			exit(json_encode($this->returnArr(0, array(), 300, '手机用户不存在')));
		}
    }
    /**
     * 发送验证码
     */
    public function sendCodeAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$mobile = $getData['mobile'];
    	if(!preg_match('/^1[2-9][0-9]{9}$/', $mobile)) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请输入正确的手机号码')));
    	}

    	$mianUserInfoRow = Custom_AuthLogin::get_user_by_mobile($mobile);
    	    	
    	if($mianUserInfoRow['GetUserInfosResult'] == 1) {
    		$mobileRow = $this->getUserByUuid($mianUserInfoRow['userInfo']['UserId']);
    		$shop_commodity_user_info = $this->select("`user_id` = '{$mobileRow['user_id']}'", 'oto_user_shop_commodity', '*', '', true);
    		if(!empty($shop_commodity_user_info)) {
    			$code = Custom_Common::random(4);
    			$content = "您的验证码为：" .$code;
    			$resultMes = Custom_Send::sendMessageNew($mobile,$content);
    			if($resultMes['SendSmsResult'] == 1){
    				$this->_db->update('oto_user', array('code' => $code), array('user_id' => $mobileRow['user_id']));
    				exit(json_encode($this->returnArr(1, array())));
    			} else {
    				exit(json_encode($this->returnArr(0, array(), 300, '验证码发送失败')));
    			}   			
    		} else {
    			exit(json_encode($this->returnArr(0, array(), 300, '用户不是店员')));
    		}
    	} else {
    		exit(json_encode($this->returnArr(0, array(), 300, '手机用户不存在')));
    	}
    }
    /**
     * 营业员首页
     */
    public function clerkHomeAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$data = $this->_model->getClerkHome($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 店长首页
     */
    public function managerHomeAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	 
    	$data = $this->_model->getManagerHome($getData, $this->_city);
    	 
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取店铺图片
     */
    public function getShopPhotoAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$data = $this->_model->getShopPhoto($getData);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 图片上传
     */
    public function imgUploadAction() {
    	$getData = $this->_http->getParams();
    	
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['img']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '图片不能为空')));
    	}
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	 
		$param = array(
					'img' => $getData['img'],
					'user_id' => $getData['uid'],
					'shop_id' => $getData['sid'],
					'folder' => 'commodity',
				);
		
		$insert_id = Custom_Upload::commodityImgageUpload($param);
		
    	exit(json_encode($this->returnArr(1, array('id' => $insert_id))));
    }
    /**
     * 图片删除
     */
    public function imgDelAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['id']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '图片ID不能为空')));
    	}
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	//断开商品图片关联
    	$result = $this->_db->update(
	    			'oto_ticket_wap_img', 
	    			array('ticket_id' => '0'), 
	    			array(
	    					'id' => intval($getData['id']), 
	    					'shop_id' => intval($getData['sid']),
	    					'user_id' => intval($getData['uid'])
	    					)
	    			);

    	if($result) {
    		exit(json_encode($this->returnArr(1, array())));
    	} else {
    		exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
    	}
    }
    /**
     * 商品上传
     */
    public function goodUploadAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	if(!$getData['title']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品标题不能为空')));
    	}
    	 
    	if(!$getData['d_price']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '售价不能为空')));
    	}
    	
    	if(!$getData['o_price']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '原价不能为空')));
    	}
    	
    	if($getData['d_price'] > $getData['o_price']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '折扣价格不能大于原价')));
    	}
    	
    	if(!$getData['tid'] && empty($getData['iids'])) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请上传图片')));
    	}
    	
    	$insert_id = $this->_model->goodUpload($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, array('id' => $insert_id))));
    }
    /**
     * 商品列表
     */
    public function goodListAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$data = $this->_model->getGoodList($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    /**
     * 商品明细
     */
    public function goodDetailAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
      	 
    	$data = $this->_model->getGoodDetail($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 商品审核
     */
    public function goodVerifyAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	if(!$getData['type'] || !in_array($getData['type'], array(1, 2))) {
    		exit(json_encode($this->returnArr(0, array(), 300, '审核类型不正确')));
    	}
    	
    	$param = array(
    				'tid' => intval($getData['tid']),
    				'audit_type' => $getData['type'],
    				'user_id' => intval($getData['uid']),
    				'ticket_sort' => intval($getData['sortid']),
    				'reason1' => 4,
    				'reason2' => '审核拒绝',
    				'city' => $this->_city
    			);
    	
    	$result = Model_Admin_Commodity::getInstance()->audit($param);
    	
    	if($result) {
    		exit(json_encode($this->returnArr(1, array())));
    	} else {
    		exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
    	}
    }
    /**
     * 商品删除（就是不显示的意思）
     */
    public function goodDelAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	//商品在审核不通过的情况下，可以删除（即不显示）
    	$result = $this->_db->update('oto_ticket', array('is_show' => '0'), array('ticket_id' => intval($getData['tid']), 'ticket_status' => '-1'));
    	
    	if($result) {
    		exit(json_encode($this->returnArr(1, array())));
    	} else {
    		exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
    	}
    }
    /**
     * 获取店铺咨询列表
     */
    public function infoListAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	  	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$data = $this->_model->getInfoList($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 咨询明细
     */
    public function infoShowAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '咨询ID不能为空')));
    	}
    	
    	if(!$getData['frid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	 
    	$data = $this->_model->getInfoShow($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 咨询回复
     */
    public function infoReplyAction() {
    	
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	 
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '咨询ID不能为空')));
    	}
    	 
    	if(!$getData['frid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	if(!$getData['qst']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '回复内容不能为空')));
    	}
    	
    	$userRow = $this->getUserByUserId($getData['uid']);
    	
    	$userInfo = $this->getWebUserId($userRow['uuid']);
    	
    	if(!$getData['type']) {
    		$getData['type'] = 'commodity';
    	}
    	
    	$result = Model_Api_Message::getInstance()->replyPersonalMessage($getData, $userInfo);
    	 
    	if($result) {
    		exit(json_encode($this->returnArr(1, array())));
    	} else {
    		exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
    	}
    }
    /**
     * 获取店铺未回复咨询数量
     */
    public function getShopNoReplyNumAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}

    	$num = $this->_model->getShopNoReplyNum($getData, $this->_city);
    	 
    	exit(json_encode($this->returnArr(1, array('unanswered_advisory_number' => $num))));
    }
    
    /**
     * 商户订单列表
     */
    public function getOrderListToShopAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	//分页
    	$page = !$getData['page'] || intval($getData['page']) < 0 ? 1 : intval($getData['page']);
    	
    	$data = $this->_model->getOrderListToShop($getData, $page);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 商户订单统计
     */
    public function getMerchantStatInfoAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if( !$getData['sid'] ) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	 
    	$data = $this->_model->getMerchantStatInfo($getData);
    	
    	exit(json_encode($this->returnArr(1, $data)));    	
    }
    /**
     * 商户订单详情
     */
    public function getOrderInfoToShopAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	
    	$data = $this->_model->getOrderInfoToShop($getData);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 订单发货
     */
    public function setOrderDeliverAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	
    	$data = $this->_model->setOrderDeliver($getData);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 商户检查验证码
     */
    public function checkVcodeShopAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	
    	$data = $this->_model->checkVCodeShop($getData);
    	exit(json_encode($this->returnArr(1, $data)));    	
    }
    /**
     * 商户使用验证码
     */
    public function useVcodeToShopAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	 
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	 
    	$data = $this->_model->useVCodeToShop($getData);
    	exit(json_encode($this->returnArr(1, $data)));    	
    }
    /**
     * 店长收益页面
     */
    public function managerProfitMainAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	
    	$data = $this->_model->getManagerProfitMain($getData);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    /**
     * 收益查询(查看更多)
     */
    public function managerProfitListAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	if( isset($getData["sdate"]) && $getData["sdate"] ){
    		$getData["sdate"] .= " 00:00:00";
    		$getData["stime"] = strtotime( $getData["sdate"] );
    	}
    	if( isset($getData["edate"]) && $getData["edate"] ){
    		$getData["edate"] .= " 23:59:59";
    		$getData["etime"] = strtotime( $getData["edate"] );
    	}
    	$data = $this->_model->getProfitList($getData);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    /**
     * 收益查询
     */
    public function managerProfitDetailAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if( !$getData['uid'] ) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	if( !$getData['sdate'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '开始日期不能为空')));
    	}
    	if( !$getData['edate'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '结束日期不能为空')));
    	}
    	$getData['sdate'] .= " 00:00:00";
    	$getData['stime'] = strtotime( $getData["sdate"] );
    	$getData['edate'] .= " 23:59:59";
    	$getData['etime'] = strtotime( $getData["edate"] );
    	$data = $this->_model->getManagerProfitDetail($getData);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    /**
     * 余额
     */
    public function managerRemainProfitAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	$shopUserIds = $this->_model->getShopUserIds($getData['sid']);
    	$remain_profit = 0;
    	if( !empty($shopUserIds) ){
    		$remain_profit = $this->_model->getShopRemainProfit(implode(",", $shopUserIds));
    	}
    	exit(json_encode($this->returnArr(1, array("remain_profit"=>$remain_profit))));
    }
    
    
    /**
     * 提现
     */
    public function managerExtractAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}	
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	$shopManagerRow = $this->select_one("`shop_id` = '{$getData['sid']}' and `user_id` = '{$getData['uid']}'", 'oto_user_shop_commodity');
    	if( $shopManagerRow["user_type"]!=2 ){
    		exit(json_encode($this->returnArr(0, array(), 300, '登录用户不是该店店长')));
    	}
    	$shopUserIds = $this->_model->getShopUserIds($getData['sid']);
    	$remain_profit = $this->_model->getShopRemainProfit(implode(",", $shopUserIds));
    	$errMsg = '';
    	if(!preg_match('/^-?\d+$/', $getData['cardMoney'])) {
    		$errMsg .= '提取金额必须是整数';
    	}
    	if($getData['cardMoney'] > $remain_profit) {
    		$errMsg .= '提取金额超过最大提现金额';
    	}
    	if(empty($getData['cardRealName'])) {
    		$errMsg .= '开户人姓名不能为空';
    	}
    	if(empty($getData['cardNum'])) {
    		$errMsg .= '储蓄卡号不能为空';
    	}
    	if( $errMsg ){
    		exit(json_encode($this->returnArr(0, array(), 300, $errMsg)));
    	}
    	$getData['type'] = 'bank';
    	$userInfo = $this->getUserByUserId($getData['uid']);
    	$res = Model_Home_User::getInstance()->addTaskMoney($getData, $userInfo);
    	if( $res ){
    		exit(json_encode($this->returnArr(1, array())));
    	}else{
    		exit(json_encode($this->returnArr(0, array() , 300 , '提现失败')));
    	}
    }
    
    /**
     * 提现历史记录
     */
    public function managerWithdrawListAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	if(!$getData['uid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
    	}
    	if( !$getData['sid'] ){
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺id不能为空')));
    	}
    	$data = $this->_model->getWithdrawList($getData);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
 }