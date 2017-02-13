<?php
/**
 * 一元众筹
 */
class Controller_Active_Oneyuanpurchase extends Base {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Oneyuanpurchase::getInstance();
		Third_Des::$key = "34npzntC";
		
		$oneyuanpurchaseArr = Model_Api_App::getInstance()->getListByMark($this->_city, 'app_home_version_six', 'app_home_six_oneyuanpurchase', 1);		
		$oneyuanpurchaseRow = $oneyuanpurchaseArr[0];
		
		$this->_tpl->assign('title', $oneyuanpurchaseRow['title']);
		$this->_tpl->assign('version', REQUEST_TIME);
		$this->_tpl->assign('desc', $oneyuanpurchaseRow['summary']);
		$this->_tpl->assign('share_img_url', $oneyuanpurchaseRow['img_url']);
		
		//手机端分享 header
		header("MplifeShareWeixinTitle : ".urlencode($oneyuanpurchaseRow['title']));
		header("MplifeShareWeixinDesc : ". urlencode($oneyuanpurchaseRow['summary']));
		header("MplifeShareWeixinUrl : " . $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/oneyuanpurchase');
		header("MplifeShareWeixinImageUrl : {$oneyuanpurchaseRow['img_url']}");
		
		$this->_platform = !$this->_http->get('platform') ? $this->_platform : strval($this->_http->get('platform'));
	}
	//活动列表页
	public function indexAction() {
		$output = array();
		$msg = $this->_http->get('msg');
		$http_build_query_string = Third_Des::decrypt($msg);
		parse_str($http_build_query_string, $output);
		$user_id = 0;
		if( !empty($output) ) {
			$city = empty($output["city"]) ? $this->_city : $output["city"];
			cookie('ONEYUANPURCHASE_CITY', $city, 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
 			if(empty($output['uuid'])) {
				cookie('ONEYUANPURCHASE_USER_ID', '', 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
 			} else {
				$user_id = $this->_model->parseLinkMsg($output);
			}
			
			if(empty($_COOKIE['_platform'])) {
				cookie('_platform', $this->_platform, 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			}
		} else {
			$user_id = (int) Third_Des::decrypt($_COOKIE['ONEYUANPURCHASE_USER_ID']);
			$city = $_COOKIE["ONEYUANPURCHASE_CITY"] ? $_COOKIE["ONEYUANPURCHASE_CITY"] : $this->_city;
		}
		$recommend_list = Model_Api_App::getInstance()->getListByMark($city, 'activity', 'activity_oneyuanpurchase_banner' , 1);
		$activities_in  = $this->_model->getActivityList($user_id, 0 , $city);//活动中
		$unbegin_activities = $this->_model->getActivityList($user_id, 1 , $city);//未开始
		$ended_activities = $this->_model->getActivityList($user_id, -1 , $city , 1 , 3);//已结束

		
		//微信相关授权信息
		$weixinKeyArr = $this->_model->getWeixinKey($GLOBALS['GLOBAL_CONF']['SITE_URL'] . $_SERVER['REQUEST_URI']);
		$this->_tpl->assign('weixinKeyArr', $weixinKeyArr);
		$this->_tpl->assign("recommend_list",$recommend_list);
		$this->_tpl->assign("activities_in",$activities_in);
		$this->_tpl->assign("unbegin_activities",$unbegin_activities);
		$this->_tpl->assign("ended_activities",$ended_activities);
		$this->_tpl->assign('site_url', $GLOBALS['GLOBAL_CONF']['SITE_URL']);
		$this->_tpl->display("active/oneyuanpurchase/index.php");
	}
	
	//往期活动(更多)
	public function activityMoreAction(){
		$page = $this->_http->get("page");
		$page = intval($page);
		if( $page<1 ) $page=1;
		$user_id = $_COOKIE["ONEYUANPURCHASE_USER_ID"];
		$user_id = Third_Des::decrypt($user_id);
		$city = $_COOKIE["ONEYUANPURCHASE_CITY"]?$_COOKIE["ONEYUANPURCHASE_CITY"]:$this->_city;
		$data = $this->_model->getActivityList(intval($user_id), -1 , $city , $page , 3 );
		echo json_encode($data);
	} 
	
	//获取活动的参与人数
	public function ticketSoldNumAction(){
		$tuuid = $this->_http->getPost('tuuid');
		$ticketObj = Custom_AuthTicket::getTicketDetailByTicketUuid($tuuid);
		$threshold = $ticketObj->data->ProductDisplaySale;
		$surplus = $ticketObj->data->ProductStock;
		echo json_encode(array("hadsold"=>intval($threshold),"surplus"=>intval($surplus)));
	}
	
	//通知我
	public function noticeMeAction(){
		$user_id = $_COOKIE["ONEYUANPURCHASE_USER_ID"];
		$user_id = Third_Des::decrypt($user_id);
		if( !intval($user_id) ){
			_exit("未登录",101);
		}
		$ticket_id = $this->_http->getPost("ticket_id");
		if( empty($ticket_id) ){
			_exit("参数错误",103);
		}
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($ticket_id);
		if( empty($ticketRow) ){
			_exit("活动不存在",103);
		}
		if( Model_Active_Oneyuanpurchase::getInstance()->isNotice($ticket_id, $user_id) ){
			_exit("已添加提醒",103);
		}
		if( $ticketRow["start_time"] - REQUEST_TIME < 10*60 ){
			_exit("距离活动开始时间不到十分钟，不能添加提醒~",103);
		}
		$res = Model_Active_Oneyuanpurchase::getInstance()->addNotice($ticket_id, $user_id);
		if( $res > 0 ){
			_exit("添加提醒成功",100);
		}else{
			_exit("添加提醒失败",103);
		}
	}
	
	//取消通知
	public function cancelNoticeAction(){
		$user_id = $_COOKIE["ONEYUANPURCHASE_USER_ID"];
		$user_id = Third_Des::decrypt($user_id);
		if( !intval($user_id) ){
			_exit("未登录",101);
		}
		$ticket_id = $this->_http->getPost("ticket_id");
		if( empty($ticket_id) ){
			_exit("参数错误",103);
		}
		if( !$this->_model->isNotice($ticket_id, $user_id) ){
			_exit("未添加提醒",103);
		}
		$res = $this->_model->cancelNotice($ticket_id, $user_id);
		if( $res > 0 ){
			_exit("取消提醒成功",100);
		}else{
			_exit("取消提醒失败",103);
		}
	}
	
	protected function auth($getData) {
	
		$SignData = $getData['SignData'];
		unset($getData['m']);
		unset($getData['c']);
		unset($getData['act']);
		unset($getData['api']);
		unset($getData['SignData']);
	
		$param = array();
	
		foreach($getData as $key => $value) {
			$param[strtolower($key)] = $value;
		}
		ksort($param);
	
		//$stringA = http_build_query($param);
		$stringA = '';
		foreach ($param as $k => $v) {
			$stringA .= "{$k}=".urldecode($v)."&";
		}
		$stringA = substr($stringA, 0, -1);
	
		$stringSignTemp = $stringA . '&key=8F0EC841B756454CB09C705E96BF6776';
		$stringSignTempMd5 = strtoupper(md5($stringSignTemp));
	
		//logLog('rebateAuthSync.log', $stringSignTemp . var_export($param, true) . ' | ' . $stringSignTempMd5 . ' | ' . $SignData);
	
		if($stringSignTempMd5 != $SignData) {
			return false;
		}
		return true;
	}
	
	//订单列表
	public function orderListAction(){
		$cookie_user_id = $_COOKIE["ONEYUANPURCHASE_USER_ID"];
		$cookie_user_id = Third_Des::decrypt($cookie_user_id);
		if( !intval($cookie_user_id) ){
			Custom_Common::jumpto('/active/oneyuanpurchase/login?jumpfrom=/active/oneyuanpurchase/order-list');
		}
		$userRow = $this->getUserByUserId($cookie_user_id,"uuid");
		$clientResult = Custom_AuthTicket::lotteryList($userRow["uuid"]);
		$lotteryList = array();
		if( 1==$clientResult["Code"] && !empty($clientResult["Result"]["Result"]) ){
			$lotteryList = $clientResult["Result"]["Result"];
			foreach ($lotteryList as &$row){
				$ticketRow = $this->_db->fetchRow("SELECT * FROM `oto_ticket` WHERE `ticket_uuid`='{$row["ProductId"]}'");
				$row["ticket_id"] = $ticketRow["ticket_id"];
				$row["status"] = "未中奖";
				$row["class"] = "target_02";
				if( count($row["WinningCodes"]) > 0 ){
					foreach( $row["OrderCodes"] as $orderRow ){
						if($row["WinningCodes"][0]["Code"] == $orderRow["Code"] ){
							$row["status"] = "已中奖";
							$row["class"] = "target_01";
							break;
						}
					}
				}else{
					$row["status"] = "正在进行";
				}
				$row["cover_img"] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/'.$ticketRow["cover_img"];
				$row["OrderTime"] = date("Y-m-d H:i:s",strtotime($row["OrderTime"]) );
			}	
		}
		$this->_tpl->assign("lottery_list", $lotteryList);
		$this->_tpl->display('active/oneyuanpurchase/order.php');
	}
	
	//登录页面
	public function loginAction(){
		$jumpfrom = $this->_http->getParam("jumpfrom");
		$platform = $this->_http->getParam("platform");
		$tuid = $this->_http->getParam("tuid");
		$cookie_user_id = (int) Third_Des::decrypt($_COOKIE["ONEYUANPURCHASE_USER_ID"]);
		if( $cookie_user_id ){
			$uuid = $this->_db->fetchOne("SELECT `uuid` FROM `oto_user` WHERE `user_id`='{$cookie_user_id}'");
        	$clientResult = Custom_AuthLogin::get_user_by_uuid($uuid);
        	if( !empty($clientResult["userInfo"]["Mobile"]) ){
        		if( $jumpfrom == 'buy_page' ){
        			if( $tuid ){
        				$time = REQUEST_TIME;
        				$token = Custom_AuthLogin::getUrlToken($clientResult["userInfo"]["Mobile"], $time);
        				$url = "http://superbuy.mplife.com/wap/pay/payorder.aspx?token={$token}&stamp={$time}&productID={$tuid}&amount=1&app=mpbuy&platform={$this->_platform}";
        				Custom_Common::jumpto($url);
        			}
        		}else{
        			Custom_Common::jumpto($jumpfrom);
        		}
			}else{
				cookie('ONEYUANPURCHASE_USER_ID', '', 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			}
		}
		$this->_tpl->assign("tuid",$tuid);
		$this->_tpl->assign("jumpfrom",$jumpfrom);
		$this->_tpl->assign("platform",$platform);
		$this->_tpl->display("active/oneyuanpurchase/login.php");
	}
	
	//发送验证码
	public function sendCodeAction(){
		$mobile = $this->_http->getPost('mobile');
	
		if(!preg_match('/^1[2-9][0-9]{9}$/', $mobile)) {
			_exit('fail', 101);
		}
	
		$code = Custom_Common::random(4);
		$content = "您的验证码为：" .$code;
		$resultMes = Custom_Send::sendMessageNew($mobile,$content);
		if($resultMes['SendSmsResult'] == 1){
			 session_start();
			 $_SESSION['mobile']=$mobile;
             $_SESSION['code']=Third_Des::encrypt($code);
			_exit('success', 100);
		}else {
			_exit('fail', 300);
		}
	}
	
	//验证验证码
	public function verifyMobileCodeAction(){
		$mobile = trim($this->_http->getPost('mobile'));
		$code   = trim($this->_http->getPost('code'));
		$jumpfrom = trim($this->_http->getPost('jumpfrom'));
		if(!$mobile || !preg_match( '/^1[2-9][0-9]{9}$/' , $mobile )){
			_exit('fail', 101); //手机错误
		}
	
		if(!$code){
			_exit('fail', 102); //验证码不存在
		}
		session_start();
		if( !empty($_SESSION['mobile']) && !empty($_SESSION['code']) ){
			$sess_code = Third_Des::decrypt($_SESSION['code']);
			if( $mobile == $_SESSION['mobile'] && $code == $sess_code ){
				$_SESSION['code'] = "";
				$clientResult = Custom_AuthLogin::get_user_by_mobile($mobile);
				if( !empty($clientResult) && !empty($clientResult["userInfo"]) && !empty($clientResult["userInfo"]["UserId"]) ){
					$userRow = $this->getWebUserId($clientResult["userInfo"]["UserId"]);
					if( !empty($userRow) ){
						cookie('ONEYUANPURCHASE_USER_ID', Third_Des::encrypt($userRow["user_id"]), 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
						$extra = '';
						if( $jumpfrom  == "buy_page" ){
							$plateform = trim($this->_http->getPost('platform'));
							$ticket_uuid = trim($this->_http->getPost('tuid'));
							$time = REQUEST_TIME;
							$token = Custom_AuthLogin::getUrlToken($clientResult["userInfo"]["Mobile"], $time);
							$extra = "http://superbuy.mplife.com/wap/pay/payorder.aspx?token={$token}&stamp={$time}&productID={$ticket_uuid}&amount=1&app=mpbuy&platform={$plateform}";
						}else if( $jumpfrom ){
							$extra = $jumpfrom;
						}
						_exit('success', 100 , $extra);
					}
					_exit('fail', 105); //错误
				}
			}
			_exit('fail', 104); //验证码错误
		}else{
			_exit('fail',103);
		}
	}
	
}