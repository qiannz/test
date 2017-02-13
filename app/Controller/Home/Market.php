<?php
class Controller_Home_Market extends Controller_Home_Abstract {
	private $_model;


	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Market::getInstance();
	}
	
	public function wapAction() {
		$mid = intval($this->_http->get('mid'));
		if (!$mid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		
		$marketInfo = $this->_model->getMarketRow($mid, $this->_city);
		$shop_band = $this->_model->getShopBrand($mid, $this->_city);

		$this->_tpl->assign('marketInfo',$marketInfo);
		$this->_tpl->assign('shopBand',$shop_band);
		$this->_tpl->display('market/view_wap.php');
	}
	
	// 网站商场首页
	public function listAction() {
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		// 获取热门商圈 9个 及该商圈下的商场
		$hotCircle = $this->_model->getHotCircle($this->_city);
		$this->_tpl->assign('hotCircle', $hotCircle);
		$this->_tpl->assign('marketbycircle', json_encode($hotCircle));
		
		// 初始值  第一个热门商圈
		$firstCid = array_slice($hotCircle, 0, 1);
		$firstCid = $firstCid[0]['id'];
		$this->_tpl->assign('firstCid', $firstCid);
		
		// 获取推荐商场 6个
		$recommMarket = Model_Home_Index::getInstance()->getRecommendListByIdentifier('market_recom', $this->_city, 6);
		$this->_tpl->assign('recommMarket', $recommMarket);
		
		// 行政区内所有的商场
		$regionMarket = $this->_model->getMarketByRid($this->_city);
		$this->_tpl->assign('regionMarket', $regionMarket);

		$this->_tpl->display('market/index.php');
	}
	
	// 商场详情
	public function showAction() {
		$mid = intval($this->_http->get('mid'));
		if (!$mid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		// 商场详情
		$marketRow = $this->_model->getMarketRow($mid, $this->_city);
		
		// 商场关注数
		$followNum = $this->_model->getUserNumByMarketId($mid);
		$marketRow['favorite_num'] = $followNum;
		if($this->_user_id){
			$marketRow['follow'] = $this->_model->hadFavoriteMarket($this->_user_id, $mid);
		}
		
		// 该商场的优惠券
		$coupon = $this->_model->getCouponByMarketId($mid, $this->_city);
		$this->_tpl->assign('coupon', $coupon);
		
		// 商场店铺
		$shop = $this->_model->getShopByMarketId($mid, $this->_city);
		$this->_tpl->assign('shop', $shop);
		
		$this->_tpl->assign('market_id', $mid);
		$this->_tpl->assign('marketRow', $marketRow);
		$this->_tpl->display('market/view.php');
		
	}

	public function favoriteAction() {
		$errorMsg = array();
		$mid = $this->_http->get('mid');
		if(!$this->_user_id) {
			$errorMsg = array('Code' => 200, 'Num' => 0, 'Message' => '必须登录才能添加关注！');
		} else {
			$affectNumber = $this->addMarketFavorite($this->_user_id, $mid);
			$this->updateQuantityFavMarketByUserId($this->_user_id);
			if ($affectNumber) {
				$errorMsg = array('Code' => 100, 'Num' => $affectNumber, 'Message' => '恭喜，你成功新增关注！');
			} else {
				$errorMsg = array('Code' => 300, 'Num' => 0, 'Message' => '抱歉，网络问题，请稍候再试！');
			}
		}
		exit(json_encode($errorMsg));
	}
	
	// ajax异步获取商场 （根据商圈ID）
	public function getMarketAction() {
		$cid = $this->_http->get('cid');
		$markets = $this->_model->getMarketByCid($cid, $this->_city);
		exit(json_encode($markets));
	}
}