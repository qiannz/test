<?php
/**
 * 刮奖（针对名品购商品的促销活动）
 * @author qiannz
 *
 */

class Controller_Active_Scratch extends Base {
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Scratch::getInstance();
		Third_Des::$key = "34npzntC";
		$shareArr = array(
					'title' => '名品购专题页分享',
					'desc' => '名品购开馆大酬宾，首单立减100元现金',
					'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/active/scratch/share.jpg',
					'www_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/scratch'
				);
		
		$this->_tpl->assign('version', '2016012709');
		$this->_tpl->assign('share', $shareArr);
		
		//手机端分享 header
		header("MplifeShareWeixinTitle : " . urlencode($shareArr['title']));
		header("MplifeShareWeixinDesc : " . urlencode($shareArr['desc']));
		header("MplifeShareWeixinUrl : " . $shareArr['www_url']);
		header("MplifeShareWeixinImageUrl : " . $shareArr['img_url']);		
	}
	
	public function indexAction() {
		$output = array();
		$msg = $this->_http->get('msg');
		$http_build_query_string = Third_Des::decrypt($msg);
		parse_str($http_build_query_string, $output);
		$user_id = 0;
		if( !empty($output) ) {
			$city = empty($output["city"]) ? $this->_city : $output["city"];
			cookie('ONEYUANPURCHASE_CITY', $city);
			if(empty($output['uuid'])) {
				cookie('ONEYUANPURCHASE_USER_ID', '');
			} else {
				$userInfo = $this->getWebUserId($output['uuid'], true);
				if( !empty($userInfo["Mobile"]) && !empty($userInfo['user_id']) ){
					$user_id = $userInfo['user_id'];
					cookie('ONEYUANPURCHASE_USER_ID', Third_Des::encrypt($user_id));
				}else{
					cookie('ONEYUANPURCHASE_USER_ID', '');
				}
			}
		} else {
			$user_id = (int) Third_Des::decrypt($_COOKIE['ONEYUANPURCHASE_USER_ID']);
			$userRow = $this->getUserByUserId($user_id);
			$userInfo = $this->getWebUserId($userRow['uuid'], true);
			$city = $_COOKIE["ONEYUANPURCHASE_CITY"] ? $_COOKIE["ONEYUANPURCHASE_CITY"] : $this->_city;
		}
		
		//微信相关授权信息
		$weixinKeyArr = Model_Active_Oneyuanpurchase::getInstance()->getWeixinKey($GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/active/scratch');
		$this->_tpl->assign('weixinKeyArr', $weixinKeyArr);
		
		if(empty($user_id)) {
			Custom_Common::jumpto("/active/oneyuanpurchase/login?jumpfrom=/active/scratch");
		}
		
		$mobileRow = $this->_model->getMobileRow($user_id);
		if(empty($mobileRow)) {
			$is_award = rand(0, 1);
			//如果超出最大限额，则默认为不中奖
			if($this->_model->getAwardNum() > 50) {
				$is_award = 0;
			}
			
			$mobileRow = array(
						'user_id' => $userInfo['user_id'],
						'user_name' =>  $userInfo['user_name'],
						'mobile'	=> $userInfo['Mobile'],
						'is_award' => $is_award,
						'ip' => CLIENT_IP,
						'created' => REQUEST_TIME
					);
			$this->_model->inertMobileRow($mobileRow);
		}
		
		//最新中奖者
		$latestRow = $this->_model->getLatestRow();
		//最新的2个名品购商品
		$latestGoodArr = Model_Api_App::getInstance()->getNewCommodityMore(
						array(
							'page' => 1,
							'pagesize' => 2,
							'w' => 240,
							'city' => $city
						)
					);
		$time = REQUEST_TIME;
		$token = Custom_AuthLogin::getUrlToken($mobileRow['mobile'], $time);
		
		foreach ($latestGoodArr['data'] as & $row) {
			$row['buy_url'] = "http://superbuy.mplife.com/wap/pay/payorder.aspx?token={$token}&stamp={$time}&productID={$row["ticket_uuid"]}&amount=1&app=mpbuy&platform={$this->_platform}";
		}
		
		$this->_tpl->assign('mobileRow', $mobileRow);
		$this->_tpl->assign('latestRow', $latestRow);
		$this->_tpl->assign('latestGoodArr', $latestGoodArr);
		$this->_tpl->display('active/scratch/index.php');
	}	
	
	public function sendMessageAction() {
		$user_id = (int) Third_Des::decrypt($_COOKIE['ONEYUANPURCHASE_USER_ID']);
		if($user_id) {
			$mobileRow = $this->_model->getMobileRow($user_id);
			//如果已中奖，但是尚未发券的话就补发
			if($mobileRow['is_award'] == 1 && $mobileRow['is_sync'] == 0) {
				//发送奖品
				$param = array(
						'AppName' => 'buy20160127', //请求应用名称
						'AppRemark' => '名品购减100活动券', //请求应用描述
						'CouponType' => 1,
						'Mobile' => $mobileRow['mobile'],
						'SetId' => '58629c5a-42f3-4943-b5cb-07e3fd42c4a5'
				);
				$resultAddCoupon = Custom_AuthTicket::tempAddCoupon($param);
				if($resultAddCoupon['code'] == 1) {
					$this->_model->updateSendStatus($mobileRow['user_id']);
					//发送短信
					$this->_model->sendMessage($mobileRow['mobile'], $resultAddCoupon['message']['0']['Code']);
				}
			}			
		}
	}
}