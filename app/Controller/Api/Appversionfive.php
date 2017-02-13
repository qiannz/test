<?php
class Controller_Api_Appversionfive extends Controller_Api_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Api_App::getInstance();
    }
    /**
     * 商场首页
     */
    public function marketAction() {
    	$getData = $this->_http->getParams();
    	
    	//传输加密验证
    	$this->auth($getData);
    	 
    	$data = $this->_model->getMarket($getData,  $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 商场列表
     */
    public function marketListAction() {
    	$getData = $this->_http->getParams();
    	 
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = $this->_model->getMarketList($getData,  $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 商场详情
     */
    public function marketDetailAction() {
    	$getData = $this->_http->getParams();
    	
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!isset($getData['mid']) || empty($getData['mid'])) {
    		exit(json_encode($this->returnArr(0, '', 101, '商场ID必传')));
    	}
    	
    	$data = $this->_model->getMarketDetail($getData,  $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 商场收藏
     */
	public function marketFavAction() {
		$getData = $this->_http->getParams();
		
		//传输加密验证
		$this->auth($getData);
		
		$this->isLogin($getData['uuid'], $getData['uname']);
		
		if (!isset($getData['mid']) || empty($getData['mid'])) {
			exit(json_encode($this->returnArr(0, array(), 101, '请选择要收藏的商场')));
		}
		
		$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
		
		$result = $this->_model->addMarketFav($getData['mid'], $user_id);
		
		$this->updateQuantityFavMarketByUserId($user_id);
		
		if($result) {
			exit(json_encode($this->returnArr(1, array())));
		}
	}
	/**
	 * 商场搜索
	 */
	public function marketSearchAction() {
		$getData = $this->_http->getParams();
		
		//传输加密验证
		$this->auth($getData);
				
		$data = $this->_model->getMoreRecommendMarket($getData, $this->_city);
		
		exit(json_encode($this->returnArr(1, $data)));
	}
    /**
     * 商场首页，更多商场推荐
     */
    public function marketRecommendMoreAction() {
    	$getData = $this->_http->getParams();
    
    	//传输加密验证
    	$this->auth($getData);
    	     	
    	$data = $this->_model->getMoreRecommendMarket($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 品牌首页
     */
    public function brandAction() {
    	$getData = $this->_http->getParams();
    	 
    	//传输加密验证
    	$this->auth($getData);
    	 
    	$data = $this->_model->getBrand($getData,  $this->_city);
    	 
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 品牌列表
     */
    public function brandListAction() {
    	$getData = $this->_http->getParams();
    	
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = $this->_model->getBrandList($getData,  $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 品牌首页，更多品牌推荐
     */
    public function brandRecommendMoreAction() {
    	$getData = $this->_http->getParams();
    
    	//传输加密验证
    	$this->auth($getData);
    	 
    	$page = !$getData['page'] ? 2 : intval($getData['page']);
    	$data = $this->_model->getBrandRecommendMore('app_brand_version_four', 'app_band_recommend', $page, $this->_city);
    	 
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 品牌详情页
     */
    public function brandDetailAction() {
    	$getData = $this->_http->getParams();
    	 
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!isset($getData['bid']) || empty($getData['bid'])) {
    		exit(json_encode($this->returnArr(0, '', 101, '品牌ID必传')));
    	}
    	 
    	$data = $this->_model->getBrandDetail($getData,  $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));    	
    }
    /**
     * 品牌收藏
     */
    public function brandFavAction() {
    	$getData = $this->_http->getParams();
    
    	//传输加密验证
    	$this->auth($getData);
    
    	$this->isLogin($getData['uuid'], $getData['uname']);
    	
    	if (!isset($getData['bid']) || empty($getData['bid'])) {
    		exit(json_encode($this->returnArr(0, array(), 101, '请选择要收藏的品牌')));
    	}
    
    	$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
    
    	$result = $this->_model->addBrandFav($getData['bid'], $user_id);
    	
    	$this->updateQuantityFavBrandByUserId($user_id);
    	
    	if($result) {
    		exit(json_encode($this->returnArr(1, array())));
    	}
    }
	/**
	 * 现金券列表
	 */
    public function ticketListAction() {
    	$getData = $this->_http->getParams();
    	
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = $this->_model->getTicketList($getData,  $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 现金券列表2015-11-10
     */
    public function voucherAllAction() {
    	$getData = $this->_http->getParams();
    	 
    	//传输加密验证
    	$this->auth($getData);
    	 
    	$data = $this->_model->getVoucherTicketAllList($getData,  $this->_city);
    	 
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 现金券详情
     */
    public function voucherDetailAction(){
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->authAll($getData);
    	
    	if ( !$getData['product_id'] && !$getData['tid'] ) {
    		exit(json_encode($this->returnArr(0, '', 101, '券UUID、券ID都为空')));
    	}
    	$data = $this->_model->getVoucherDetail($getData , $this->_city );
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 现金券列表
     */
    public function voucherTicketListAction() {
    	$getData = $this->_http->getParams();
    	 
    	//传输加密验证
    	$this->auth($getData);
    	 
    	$data = $this->_model->getVoucherTicketList($getData,  $this->_city, true);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }    
    /**
     * 获取商品分类
     */
    public function getCategoryAction() {
    	$getData = $this->_http->getParams();
    	 
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = Model_Api_Goods::getInstance()->getAppStore($this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
}