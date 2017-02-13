<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-7-1
 * Time: 下午1:55
 */

class Controller_Home_Brand extends Controller_Home_Abstract {
    private $_model;


    public function __construct() {
        parent::__construct();
        $this->_model = Model_Home_Brand::getInstance();
    }

    /**
     * 品牌详情
     */
    public function showAction() {
        $bid = $this->_http->get('bid');
        $order = !$this->_http->get('order') ? 1 : intval($this->_http->get('order'));
        $bid = intval($bid);
        if (!$bid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
         
        //获取分类导航
        $navList = $this->getNavList();
        $this->_tpl->assign('navList', $navList);
         
        // 品牌详情
        $brandRow = $this->_model->getBrandDetail($bid, $this->_city);
        if(empty($brandRow) || !empty($brandRow) && $brandRow['is_enable'] == 0) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);

        if($brandRow['brand_name_zh']) {
        	$brand_name = $brandRow['brand_name_zh'];
        }
        	
        if($brandRow['brand_name_en']) {
        	$brand_name .= "（{$brandRow['brand_name_en']}）";
        }
 
        $brandRow['brand_name'] = $brand_name;
        // 品牌关注数
        $followNum = $this->_model->getUserNumByBrandId($bid);
        $brandRow['favorite_num'] = $followNum;
        if($this->_user_id){
        	$brandRow['follow'] = $this->_model->hadFavoriteBrand($this->_user_id, $bid);
        }
        
        $brandRow['brand_profile'] = Custom_String::cutString($brandRow['brand_profile'], 120);
        
        
        // 旗下店铺
        $shop = $this->_model->getShopInfo($bid, $this->_city);
        $this->_tpl->assign('shop', $shop);
         
        // 品牌优惠券
        $coupon = $this->_model->getCouponByBrand($bid, $this->_city);
        $this->_tpl->assign('coupon', $coupon);
         
        $this->_tpl->assign('brandRow', $brandRow);
        $this->_tpl->assign('order',$order);
        $this->_tpl->assign('brand_id',$bid);
        $this->_tpl->display('brand/view.php');

    }

    public function favoriteAction() {
    	$errorMsg = array();
    	$bid = $this->_http->get('bid');
    	if(!$this->_user_id) {
    		$errorMsg = array('Code' => 200, 'Num' => 0, 'Message' => '必须登录才能添加关注！');
    	} else {
    		$affectNumber = $this->addBrandFavorite($this->_user_id, $bid);
    		$this->updateQuantityFavBrandByUserId($this->_user_id);
    		if ($affectNumber) {
    			$errorMsg = array('Code' => 100, 'Num' => $affectNumber, 'Message' => '恭喜，你成功新增关注！');
    		} else {
    			$errorMsg = array('Code' => 300, 'Num' => 0, 'Message' => '抱歉，网络问题，请稍候再试！');
    		}
    	}
    	exit(json_encode($errorMsg));
    }
    /**
     * 商品分类瀑布流展示
     */
    public function ajaxAction() {
        $goodArray = $this->_model->getAjaxGoodList(
            intval($this->_http->get('bid')),
            intval($this->_http->get('page')),
            intval($this->_http->get('order'))
        );
        exit(json_encode($goodArray));
    }
    
    /**
     * 详情页wap版
     */
    public function wapAction() {
    	$bid = intval($this->_http->get('bid'));
    	if (!$bid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
    	$brandDetail = $this->_model->getBrandDetail($bid, $this->_city);
    	$shopInfo = $this->_model->getShopInfo($bid, $this->_city);

    	$this->_tpl->assign('shopInfo',$shopInfo);
    	$this->_tpl->assign('brandDetail',$brandDetail);
    	$this->_tpl->display('brand/view_wap.php');
    }
    
    /**
     * 网站 首页
     */
    public function listAction() {
    	//获取分类导航
    	$navList = $this->getNavList();
    	$this->_tpl->assign('navList', $navList);
    	
    	// 推荐大图 （首页 1 ）
    	$recommBrandBid = Model_Home_Index::getInstance()->getRecommendListByIdentifier('brand_recom_big', $this->_city, 1);
    	foreach ($recommBrandBid as &$row) {
    		$brand_logo = $this->_db->fetchOne("select brand_icon from oto_brand where brand_id = '{$row['come_from_id']}' limit 1");
    		$row['brand_logo'] = $brand_logo ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brand_logo : '/images/blank.png';
    		$row['ticket'] = $this->_model->getCouponByBrand($row['come_from_id'], $this->_city);
    	}
    	$this->_tpl->assign('recommBrandBid', $recommBrandBid);

    	
    	// 推荐小图 （首页 4）
    	$recommBrandSmall = Model_Home_Index::getInstance()->getRecommendListByIdentifier('brand_recom_small', $this->_city, 4);
    	foreach ($recommBrandSmall as &$row1) {
    		$brand_info = $this->_db->fetchRow("select brand_name_zh, brand_name_en, brand_logo from oto_brand where brand_id = '{$row1['come_from_id']}' limit 1");
    		$row1['brand_logo'] = $brand_info['brand_logo'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brand_info['brand_logo'] : '/images/blank.png';
    		if($brand_info['brand_name_zh'] && !$brand_info['brand_name_en']) {
    			$row1['brand_name'] = $brand_info['brand_name_zh'];
    		} elseif(!$brand_info['brand_name_zh'] && $brand_info['brand_name_en']) {
    			$row1['brand_name'] = $brand_info['brand_name_en'];
    		} else {
    			$row1['brand_name'] = $brand_info['brand_name_zh'];
    		}
    		$row1['ticket'] = $this->_model->getCouponByBrand($row1['come_from_id'], $this->_city);
    	}
    	$this->_tpl->assign('recommBrandSmall', $recommBrandSmall);
    	
    	// 获取品牌分类 以及分类下的 品牌 10
    	$brand = $this->_model->getBrand($this->_city);
    	$this->_tpl->assign('brand', $brand);
    	
    	$this->_tpl->display('brand/index.php');
    }
      
    /**
     * 网站 品牌大全
     */
	public function allAction() {
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		// 获取品牌分类 以及分类下的所有品牌名
		$brand = $this->_model->getAllBrand($this->_city);
		$this->_tpl->assign('brand', $brand);
		
		$this->_tpl->display('brand/all.php');
	}
}