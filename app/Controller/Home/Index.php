<?php
class Controller_Home_Index extends Controller_Home_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Index::getInstance();
	}
	
	public function listAction() {
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
	
		//轮播大图
		$imgLargeList = $this->_model->getRecommendListByIdentifier('index_img_large', $this->_city, 4);
		$this->_tpl->assign('imgLargeList', $imgLargeList);

		//热门店铺
		$topShopList = $this->_model->getRecommendListByIdentifier('index_top_shop', $this->_city, 8);
		$this->_tpl->assign('topShopList', $topShopList);
		//超值精选
		$valuePickList = $this->_model->getRecommendListByIdentifier('index_value_pick', $this->_city, 5);
		$this->_tpl->assign('valuePickList', $valuePickList);
		
		//获取分类商品推荐
		$recommendClassificationGoodList = $this->_model->getRecommendClassificationGood('recommend_goods', $this->_city);
		$this->_tpl->assign('recommendClassificationGoodList', $recommendClassificationGoodList);
		
		//>> start 获取分类品牌推荐
		//品牌.女装
		$recommendBrandsWomList = $this->_model->getRecommendListByIdentifier('recommend_brands_wom', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsWomList', $recommendBrandsWomList);
		//品牌.女鞋
		$recommendBrandsShoesList = $this->_model->getRecommendListByIdentifier('recommend_brands_shoes', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsShoesList', $recommendBrandsShoesList);
		//品牌.内衣
		$recommendBrandsUnderwearList = $this->_model->getRecommendListByIdentifier('recommend_brands_underwear', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsUnderwearList', $recommendBrandsUnderwearList);
		//品牌.男装
		$recommendBrandsMenList = $this->_model->getRecommendListByIdentifier('recommend_brands_men', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsMenList', $recommendBrandsMenList);
		//品牌.配饰
		$recommendBrandsAccessoriesList = $this->_model->getRecommendListByIdentifier('recommend_brands_accessories', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsAccessoriesList', $recommendBrandsAccessoriesList);
		//品牌.母婴
		$recommendBrandsMaternalChildList = $this->_model->getRecommendListByIdentifier('recommend_brands_maternal_child', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsMaternalChildList', $recommendBrandsMaternalChildList);
		//品牌.床品
		$recommendBrandsBeddingList = $this->_model->getRecommendListByIdentifier('recommend_brands_bedding', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsBeddingList', $recommendBrandsBeddingList);
		//<< end 获取分类品牌推荐
				
		$this->_tpl->display('home/index20140422.php');
	}

	public function indexAction() {
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		//轮播大图
		$imgLargeList = $this->_model->getRecommendListByIdentifier('index_img_large', $this->_city, 4);
		$this->_tpl->assign('imgLargeList', $imgLargeList);
		
		
		//热门店铺
		$topShopList = $this->_model->getRecommendListByIdentifier('index_top_shop', $this->_city, 8);
		$this->_tpl->assign('topShopList', $topShopList);
		
		// >> 品牌
		
		//1. 品牌分类
		$brandStore = $this->getStore(0, true, false, $this->_city);
		$this->_tpl->assign('brandStore', $brandStore);

		// 2. 品牌首页推荐大图
		$indexBrandList = $this->_model->getRecommendListByIdentifier('index_brand', $this->_city, 5);
		$this->_tpl->assign('indexBrandList', $indexBrandList);
		// 3. 品牌首页推荐LOGO图
		$indexBrandLogoList = $this->_model->getRecommendListByIdentifier('index_brand_logo', $this->_city, 12);
		$this->_tpl->assign('indexBrandLogoList', $indexBrandLogoList);
		
		// >> 商场
		
		// 1.获取行政区
		$region = $this->getRegion(0, false, $this->_city);
		$this->_tpl->assign('region', $region);
		
		// 2.商场首页推荐大图
		$indexMarketList = $this->_model->getRecommendListByIdentifier('index_market', $this->_city, 5);
		$this->_tpl->assign('indexMarketList', $indexMarketList);
		
		// 2.商场首页推荐LOGO图
		$indexMarketLogoList = $this->_model->getRecommendListByIdentifier('index_market_logo', $this->_city, 12);
		$this->_tpl->assign('indexMarketLogoList', $indexMarketLogoList);
		
		
		//超值精选
		$valuePickList = $this->_model->getRecommendListByIdentifier('index_value_pick', $this->_city, 5);
		$this->_tpl->assign('valuePickList', $valuePickList);
		
		//获取分类商品推荐
		$recommendClassificationGoodList = $this->_model->getRecommendClassificationGood('recommend_goods', $this->_city);
		$this->_tpl->assign('recommendClassificationGoodList', $recommendClassificationGoodList);
		
		//>> start 获取分类品牌推荐
		//品牌.女装
		$recommendBrandsWomList = $this->_model->getRecommendListByIdentifier('recommend_brands_wom', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsWomList', $recommendBrandsWomList);
		//品牌.女鞋
		$recommendBrandsShoesList = $this->_model->getRecommendListByIdentifier('recommend_brands_shoes', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsShoesList', $recommendBrandsShoesList);
		//品牌.内衣
		$recommendBrandsUnderwearList = $this->_model->getRecommendListByIdentifier('recommend_brands_underwear', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsUnderwearList', $recommendBrandsUnderwearList);
		//品牌.男装
		$recommendBrandsMenList = $this->_model->getRecommendListByIdentifier('recommend_brands_men', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsMenList', $recommendBrandsMenList);
		//品牌.配饰
		$recommendBrandsAccessoriesList = $this->_model->getRecommendListByIdentifier('recommend_brands_accessories', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsAccessoriesList', $recommendBrandsAccessoriesList);
		//品牌.母婴
		$recommendBrandsMaternalChildList = $this->_model->getRecommendListByIdentifier('recommend_brands_maternal_child', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsMaternalChildList', $recommendBrandsMaternalChildList);
		//品牌.床品
		$recommendBrandsBeddingList = $this->_model->getRecommendListByIdentifier('recommend_brands_bedding', $this->_city, 9);
		$this->_tpl->assign('recommendBrandsBeddingList', $recommendBrandsBeddingList);
		
		$this->_tpl->display('home/index20141201.php');
	}
	
    /**
     *街友爱分享
     */
    public function shareAction(){
    	//统计总共发奖金额
    	$amountAwards = $this->_model->getAmountAwards();
    	//获取第一张轮播图
        $imageRow = array_shift($this->_model->getRecommendListByIdentifier('index_img_large', $this->_city, 1));
        //获取最新8条商品
        $shareGoodArray = $this->_model->getRecommendListByIdentifier('index_value_pick', $this->_city, 8);

        $this->_tpl->assign('good' ,$shareGoodArray);
        $this->_tpl->assign('amountAwards' , $amountAwards);
        $this->_tpl->assign('imageRow' , $imageRow);
        $this->_tpl->display('home/share.php');
    }
	/**
	 * 喜欢/关注
	 */
	public function concernAction() {
		$errorMsg = array();
		$gid = $this->_http->get('gid');
		if(!$this->_user_id) {
			$errorMsg = array('Code' => 200, 'Num' => 0, 'Message' => '必须登录才能添加关注！');
		} else {			
			$affectNumber = $this->addConcern($this->_userInfo['user_name'], $gid);
			$this->updateQuantityLoveGoodByUserId($this->_userInfo['user_id']);
			if ($affectNumber) {
				$errorMsg = array('Code' => 100, 'Num' => $affectNumber, 'Message' => '恭喜，你成功新增关注！');
			} else {
				$errorMsg = array('Code' => 300, 'Num' => 0, 'Message' => '抱歉，网络问题，请稍候再试！');
			}
		}
		exit(json_encode($errorMsg));
	}
	/**
	 * 收藏
	 */
	public function favoriteAction() {
		$errorMsg = array();
		$gid = $this->_http->get('gid');
		if(!$this->_user_id) {
			$errorMsg = array('Code' => 200, 'Num' => 0, 'Message' => '必须登录才能添加收藏！');
		} else {
			$affectNumber = $this->addFavorite($this->_userInfo['user_name'], $gid);
			$this->updateQuantityFavGoodByUserId($this->_userInfo['user_id']);
			if ($affectNumber) {
				$errorMsg = array('Code' => 100, 'Num' => $affectNumber, 'Message' => '恭喜，你成功新增收藏！');
			} else {
				$errorMsg = array('Code' => 300, 'Num' => 0, 'Message' => '抱歉，网络问题，请稍候再试！');
			}
		}
		exit(json_encode($errorMsg));		
	}	
}