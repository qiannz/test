<?php
class Controller_Home_Ticket extends Controller_Home_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Ticket::getInstance();
		$this->_partial_city = $this->_http->has('city') ? $this->_http->get('city') : $this->_city;
	}

	public function listAction() {
		$type = !$this->_http->get('type') ? 1 : intval($this->_http->get('type'));
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		//轮播大图
		$imgLargeList = Model_Home_Index::getInstance()->getRecommendListByIdentifier('index_img_large', $this->_city, 4);
		$this->_tpl->assign('imgLargeList', $imgLargeList);
	
		$this->_tpl->assign('type', $type);
		$this->_tpl->display('ticket/new_list.php');
	}
	
	public function overDataAction() {
		$type = !$this->_http->get('type') ? 1 : intval($this->_http->get('type'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$couponInfo = $this->_model->getCouponInfo($type, $page);
		exit(json_encode($couponInfo));
	}
		
	public function listOldAction() {
		$sidArray = $this->_http->has('sid') ? explode('_', $this->_http->get('sid')) : array(0,0,0,0,0);
		
		$store_id 	= intval($sidArray[0]);
		$brand_id 	= intval($sidArray[1]);
		$region_id 	= intval($sidArray[2]);
		$circle_id 	= intval($sidArray[3]);
		$shop_id 	= intval($sidArray[4]);
		$order 		= !$this->_http->get('order') ? 1 : intval($this->_http->get('order'));
		
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		//获取商品分类
		$storeList = $this->getStore(0, true, false, $this->_city);
		$this->_tpl->assign('storeList', $storeList);
		//获取品牌分类
		$brandList = $this->getBrand(0, true, true, $this->_city);
		$this->_tpl->assign('brandList', $brandList);
		//获取区域分类
		$regionList = $this->getRegion(0, true, $this->_city);
		$this->_tpl->assign('regionList', $regionList);
		//获取商圈分类
		if($region_id) {
			$circleList = $this->getCircleByRegionId($region_id, false, true, $this->_city);
		} else {
			$circleList = Model_Home_Good::getCircle($this->_city);
		}
		$this->_tpl->assign('circleList', $circleList);
		//获取对应店铺
		if($region_id && $circle_id) {
			$shopList = $this->getShop($region_id, $circle_id, $this->_city);
			$this->_tpl->assign('shopList', $shopList);
		}
		
		$tickets = $this->_model->getTicketList($store_id,$brand_id,$region_id,$circle_id,$shop_id,$order);
		
		$this->_tpl->assign('store_id', $store_id);
		$this->_tpl->assign('brand_id', $brand_id);
		$this->_tpl->assign('region_id', $region_id);
		$this->_tpl->assign('circle_id', $circle_id);
		$this->_tpl->assign('shop_id', $shop_id);
		$this->_tpl->assign('order', $order);
		
		$this->_tpl->assign('coupon', $tickets);
		$this->_tpl->display('ticket/list.php');		
	}

	public function showAction() {
		$tid = $this->_http->get('tid');
		$tid = intval($tid);
		if (!$tid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
	
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		//获取券信息 及相关店铺和部分相关商品信息
		$ticketRow = $this->_model->getTicktRow($tid);
		if(empty($ticketRow['ticket_id'])) {
			Custom_Common::jumpto('/404/404.html');
		}
	
		if($ticketRow['end_time'] < REQUEST_TIME) {
			$ticketRow['is_end'] = 1;
		}
		$this->_tpl->assign('ticketRow', $ticketRow);
		//热门商品
		$goodShowHotList = Model_Home_Index::getInstance()->getRecommendListByIdentifier('good_show_hot', $this->_city, 4);
		$this->_tpl->assign('goodShowHotList', $goodShowHotList);
		//券对应店铺
		$shopInfo = Model_Home_Good::getInstance()->getShop($ticketRow['shop_id']);
		//本券适用商品
		$goodTicketList = $this->_model->getGoodListByTicketId($tid, 3);
		$this->_tpl->assign('goodTicketList', $goodTicketList);
	
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->assign('shopInfo', $shopInfo);
				
		if($ticketRow['ticket_mark'] == 'buygood') {
			//团购关联店铺
			$shopList = $this->_model->getAssociatedShops($tid, $shopInfo);
			$this->_tpl->assign('shopList', $shopList);
			//团购推荐
			$tuanRecommend = $this->_model->getTuanRecommend('buygood_hot', $this->_city, 4);
			$this->_tpl->assign('tuanRecommend', $tuanRecommend);
			$this->_tpl->display('ticket/show_tuan.php');
		} else {
			$this->_tpl->display('ticket/show.php');
		}
	}
		
	public function wapListAction() {
		$class = intval($this->_http->get('class'));		
		$tickets = $this->_model->getTicketList(0, 0, 0, 0, 0, 1, $class, $this->_partial_city);
		$this->_tpl->assign('coupon', $tickets);
		$this->_tpl->assign('class', $class);
		$this->_tpl->assign('city', $this->_partial_city);
		$this->_tpl->display('ticket/wap_list_new.php');
	}
	
    public function wapAction(){
        $tid = intval($this->_http->get('tid'));
        if (!$tid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);

        $ticketRow = $this->_model->getTicktRow($tid, false);
        if(empty($ticketRow)) {
        	Custom_Common::jumpto('/404/404.html');
        }
        //保存用户传递过来的用户信息
        $output = array();
        $msg = $this->_http->get('msg');
        Third_Des::$key = "34npzntC";
        $http_build_query_string = Third_Des::decrypt($msg);
        parse_str($http_build_query_string, $output);
        $user_id = 0;
        if( !empty($output) ) {
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
        } else {
        	$user_id = (int) Third_Des::decrypt($_COOKIE["ONEYUANPURCHASE_USER_ID"]);
        }
        
        $this->_tpl->assign('tid', $tid);
        $this->_tpl->assign('mark', $ticketRow['ticket_mark']);
        //商城商品
        if($ticketRow['ticket_mark'] == 'commodity') {
        	$data = Model_Api_App::getInstance()->getCommodityDetail(array('tid' => $tid), $this->_city);
        	$this->_tpl->assign('data', $data);
        	$this->_tpl->display('ticket/commodity_show.php');
        } 
        //一元众筹 OR 快来抢
        elseif( 'crowdfunding' == $ticketRow['ticket_mark'] || 'spike' == $ticketRow['ticket_mark'] ) {
        	$ticketRow["buy_url"] = "/active/oneyuanpurchase/login?jumpfrom=buy_page&tuid={$ticketRow['ticket_uuid']}&platform={$this->_platform}";
        	$this->_tpl->assign("jumpfrom","/home/ticket/wap/tid/{$tid}");
        	if( $user_id ){
        		$uuid = $this->_db->fetchOne("SELECT `uuid` FROM `oto_user` WHERE `user_id`='{$user_id}'");
        		$clientResult = Custom_AuthLogin::get_user_by_uuid($uuid);
        		if( !empty($clientResult["userInfo"]["Mobile"]) ){
        			$time = REQUEST_TIME;
        			$token = Custom_AuthLogin::getUrlToken($clientResult["userInfo"]["Mobile"], $time);
        			$ticketRow['buy_url'] = $url = "http://superbuy.mplife.com/wap/pay/payorder.aspx?token={$token}&stamp={$time}&productID={$ticketRow["ticket_uuid"]}&amount=1&app=mpbuy&platform={$this->_platform}";
        		}
        	}
        	//券未开始并且已登录
        	$ticketRow["is_notice"] = 0;
        	if($ticketRow['voucher_status'] == 1) {
        		if($user_id) {
        			$ticketRow['is_notice'] = (int) Model_Active_Oneyuanpurchase::getInstance()->isNotice($ticketRow['ticket_id'], $user_id);
        		}
        	}
        	//实物商品
        	if(empty($ticketRow['valid_stime']) || empty($ticketRow['valid_etime'])) {
        		$data['detail'] = $ticketRow;
        		$data['detail']['firstImg']['img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $ticketRow['cover_img'];
        		 
        		$imgList = Model_Admin_Ticket::getInstance()->getWapImg($tid);
        		
        		$folder = ('crowdfunding' == $ticketRow['ticket_mark'])?"crowdfunding":"ticketwap";
        		foreach ($imgList as & $imgItem) {
        			$widthHeightRow = array();
        			$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight($imgItem['img_url'], $folder);
        			$data['detail']['imgList'][] = array(
        					'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/{$folder}/" . $imgItem['img_url'],
        					'width' => $widthHeightRow['width'],
        					'height' => $widthHeightRow['height']
        			);
        		}
        		$data['shop'] = $this->getShopFieldById($ticketRow['shop_id']);
        		$data['brand'] = Model_Home_Brand::getInstance()->getBrandDetail($ticketRow['brand_id'], $this->_city);
        		$this->_tpl->assign('data', $data);
        		$this->_tpl->display('ticket/commodity_show.php');
        	} 
        	//虚拟商品
        	else {
        		$ticketRow['start_time']   = date('Y.n.j',$ticketRow['start_time']);
        		$ticketRow['end_time']   = date('n.j',$ticketRow['end_time']);
        		$shopInfo = Model_Home_Good::getInstance()->getShop($ticketRow['shop_id']);
        		$this->_tpl->assign('shopInfo', $shopInfo);
        		$this->_tpl->assign('ticketRow', $ticketRow);
        		$this->_tpl->display('ticket/oneyuanpurchase_voucher.php');
        	}
        } 
        //现金券
        else {
        	$from = $this->_http->get('from');
        	$shopInfo = Model_Home_Good::getInstance()->getShop($ticketRow['shop_id']);
        	$this->_tpl->assign('shopInfo', $shopInfo);
        	$ticketRow['valid_stime']   = date('Y.n.j',$ticketRow['valid_stime']);
        	$ticketRow['valid_etime']   = date('n.j',$ticketRow['valid_etime']);
        	if( $from == 'ios' ){
        		if( $ticketRow['is_free'] == 1 || $ticketRow['app_price'] < 0 ) {
        			$ticketRow['selling_price'] = 0;
        		} elseif($ticketRow['is_free'] == 0 && $ticketRow['app_price'] > 0) {
        			$ticketRow['selling_price'] = $ticketRow['app_price'];
        		}
        	}
        	$this->_tpl->assign('ticketRow', $ticketRow);
        	$this->_tpl->assign('come_from', $from);
        	$this->_tpl->display('ticket/show_wap.php');
        }
    }

    public function wapShowAction(){
        $tid = intval($this->_http->get('tid'));
        if (!$tid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);

        $ticketRow = $this->_model->getTicktRow($tid);

        $shopInfo = Model_Home_Good::getInstance()->getShop($ticketRow['shop_id']);
        $this->_tpl->assign('shopInfo', $shopInfo);
        if( $ticketRow["ticket_mark"] == 'crowdfunding' ){
        	$wap_img_list =  Model_Admin_Ticket::getInstance()->getWapImg($ticketRow["ticket_id"]);
			foreach($wap_img_list as &$imgRow){
				$imgRow["img_url"] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/crowdfunding/" .$imgRow["img_url"];
			}
			$ticketRow["wap_img_list"] = $wap_img_list;
        }
        $this->_tpl->assign('ticketRow',$ticketRow);
        $this->_tpl->display('ticket/show_detail_wap.php');
    }

	public function applyTicketAction() {
		$tid = intval($this->_http->get('tid'));
		$phone = $this->_http->get('phone');
		//判断用户是否登录
		if(!$this->_user_id) _exit('抱歉，登录用户才能领取优惠券', 99);
		//判断手机号码是否存在， 券ID 是否合法
		if(!preg_match('/^1[2-9][0-9]{9}$/', $phone) || !$tid) exit('Hacker attacks');
		//领券逻辑开始
		$this->_model->applyTicket($tid, $phone, $this->_userInfo);	
	}
	
	public function applyTicketVoucherAction() {
		$tid = intval($this->_http->get('tid'));
        $is_wap = $this->_http->get('wap');
		//判断用户是否登录
        if(!$is_wap){
		    if(!$this->_user_id) _exit('抱歉，登录用户才能购买现金券', 99);
        }
		if(!$tid) exit('Hacker attacks');
		//购券逻辑开始
		$this->_model->applyTicketVoucher($tid, $this->_userInfo);
	}
	
	public function callBackAction() {
		$tid = intval($this->_http->get('tid'));
		Custom_Common::showMsg(
			'你好，现金券支付成功！',
			'',
			array(
				$GLOBALS['GLOBAL_CONF']['MAIN_SITE_URL'] . '/O2oMyOrder/MyOrder.aspx' => '查看我的订单详情'
			)
		);		
	}
	
	public function getTicketPlusAction() {
		$tid = intval($this->_http->get('tid'));
		$ticketRow = $this->select("`ticket_id` = '{$tid}'", 'oto_ticket', 'total, has_led', '', true);
		_exit('success', 100, $ticketRow['total'] - $ticketRow['has_led']);
	}
	
}
