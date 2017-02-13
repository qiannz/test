<?php
/**
 * 快来抢
 */
class Controller_Active_Comeandgrap extends Base {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Comeandgrap::getInstance();
		Third_Des::$key = '34npzntC';
		
		$comeandgrapArr = Model_Api_App::getInstance()->getListByMark($this->_city, 'app_home_version_six', 'app_home_six_comeandgrap', 1);
		$comeandgrapRow = $comeandgrapArr[0];
		
		$this->_tpl->assign('title', $comeandgrapRow['title']);
		$this->_tpl->assign('version', REQUEST_TIME);
		$this->_tpl->assign('desc', $comeandgrapRow['summary']);
		$this->_tpl->assign('share_img_url', $comeandgrapRow['img_url']);
		$this->_tpl->assign('share_url', $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/comeandgrap');
		
		//手机端分享 header
		header("MplifeShareWeixinTitle : ".urlencode($comeandgrapRow['title']));
		header("MplifeShareWeixinDesc : ". urlencode($comeandgrapRow['summary']));
		header("MplifeShareWeixinUrl : " . $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/comeandgrap');
		header("MplifeShareWeixinImageUrl : {$comeandgrapRow['img_url']}");
		
		$this->_platform = !$this->_http->get('platform') ? $this->_platform : strval($this->_http->get('platform'));
	}
	
	public function indexAction() {
		$output = array();
		$msg = $this->_http->get('msg');
		$http_build_query_string = Third_Des::decrypt($msg);
		parse_str($http_build_query_string, $output);
		$user_id = 0;
		if( !empty($output) ){
			$city = empty($output["city"]) ? $this->_city : $output["city"];
			cookie('ONEYUANPURCHASE_CITY', $city, 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			if(empty($output['uuid'])) {
				cookie('ONEYUANPURCHASE_USER_ID', '', 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			} else {
				$user_id = Model_Active_Oneyuanpurchase::getInstance()->parseLinkMsg($output);
			}
			if(empty($_COOKIE['_platform'])) {
				cookie('_platform', $this->_platform, 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			}
		}else{
			$user_id = (int) Third_Des::decrypt($_COOKIE['ONEYUANPURCHASE_USER_ID']);
			$city = $_COOKIE["ONEYUANPURCHASE_CITY"] ? $_COOKIE["ONEYUANPURCHASE_CITY"] : $this->_city;
		}
		//顶部栏位
		$top_banner = array();
		//当天进行中的快来抢
		$activities_in = $this->_model->getActivityList($user_id, 0, $city);
		$weekArray = array("日","一","二","三","四","五","六");
		$week = datex(REQUEST_TIME,"w");
		if( !empty($activities_in) ){
			$top_banner[] = array("week"=>$weekArray[$week],
									"text"=>"正在抢购中");
		}
		//当天即将进行的快来抢
		$activities_will = $this->_model->getActivityList($user_id, 1, $city);
		foreach ( array_keys($activities_will) as $startTime ){
			$time = date("H:i",$startTime);
			$top_banner[] = array("week"=>$weekArray[$week],
					"text"=>"{$time}点开抢");
		}
		//明日的快来抢
		$activities_tomorrow = $this->_model->getActivityList($user_id, 2, $city);
		if( !empty($activities_tomorrow) ){
			if( $week == 6 ){ 
				$week = 0; 
			}else{
				$week++;
			}
			$top_banner[] = array("week"=>$weekArray[$week],
					"text"=>"明日开抢");
		}
		//微信相关授权信息
		$weixinKeyArr = Model_Active_Oneyuanpurchase::getInstance()->getWeixinKey($GLOBALS['GLOBAL_CONF']['SITE_URL'] . $_SERVER['REQUEST_URI']);
		$this->_tpl->assign('weixinKeyArr', $weixinKeyArr);
		$this->_tpl->assign("top_banner",$top_banner);
		$this->_tpl->assign("activities_in",$activities_in);
		$this->_tpl->assign("activities_will",$activities_will);
		$this->_tpl->assign("activities_tomorrow",$activities_tomorrow);
		$this->_tpl->display("active/comeandgrap/index.php");		
	}
	
	//订单列表
	public function orderListAction(){
		$user_id = (int) Third_Des::decrypt($_COOKIE['ONEYUANPURCHASE_USER_ID']);
		if( $user_id ){
			$user = $this->getUserByUserId($user_id,'uuid');
			if( $user['uuid'] ){
				$userInfo = $this->getWebUserId($user['uuid']);
				if( $userInfo["Mobile"] ){
					$token = Custom_AuthLogin::getUrlToken($userInfo["Mobile"], REQUEST_TIME);
				}
			}
		}
		$order_url = "/active/oneyuanpurchase/login?jumpfrom=/active/comeandgrap/order-list";
		if( $token ){
			$order_url = "http://superbuy.mplife.com/Wap/Pay/MyOrder.aspx?token={$token}&stamp=".REQUEST_TIME;
		}
		Custom_Common::jumpto($order_url);
	}
}