<?php
/**
 * 商城商品模块
 * @author Qiannz
 *
 */
class Controller_Api_Appgood extends Controller_Api_Abstract {

    private $_model;
    private $_pagesize = 20;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Api_App::getInstance();
    }
    /**
     * 新品首页
     */
    public function newAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = $this->_model->getNewCommodity($getData, $this->_city, true);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取更多新品
     */
    public function newMoreAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	$page = intval($getData['page'])>1 ? intval($getData['page']) : 1;
    	
    	$data = $this->_model->getNewCommodityMore(
    			array(
    					'page' => $page,
    					'pagesize' => $this->_pagesize,
    					'uuid' => $getData['uuid'],
    					'uname' => $getData['uname'],
    					'shop_id' => intval($getData['sid']),
    					'lng'  => $getData['lng'],
    					'lat'  => $getData['lat'],
    					'w' => 240,
    					'city' => $this->_city
    				)
    			);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 名品购首页2015-12-28
     */
    public function newVersionTwoAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    
    	$data = $this->_model->getCommodityNewVersionTwo($getData, $this->_city, true);
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    /**
     * 距离手机用户最近的店铺列表
     */
    public function distanceAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = $this->_model->getCommodityDistance($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取商城商品分类
     */
    public function getStoreAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$data = $this->_model->getStoreList($this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取商城品牌分类
     */
    public function getBrandAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	$data = $this->_model->getCommodityBrandList($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取品牌下的店铺列表
     */
    public function getBrandShopAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	if(!$getData['bid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '品牌ID不能为空')));
    	}
    	$data = $this->_model->getCommodityShopList($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取商城商品详情，店铺简介，同店铺商品
     */
    public function commodityDetailAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	$data = $this->_model->getCommodityDetail($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取商城商品详情，同店铺更多商品
     */
    public function commodityDetailMoreAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$page = intval($getData['page']) >= 2 ? intval($getData['page']) : 2;
    	
    	$getData['shop_id'] = intval($getData['sid']);
    	$getData['page'] = $page; //分页
    	$getData['pagesize'] = $this->_pagesize;//分页大小
    	$getData['w'] = 240; //图片缩略
    	$getData['city'] = $this->_city; //所在城市
    	
    	$data = $this->_model->getNewCommodityMore($getData);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取店铺详情页
     */
    public function commodityShopDetailAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$data = $this->_model->getCommodityShopDetail($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 获取商城商品详情
     */
    public function detailAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['tid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	$data = $this->_model->getCommodityDetail($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    
    /**
     * 搜索商城商品
     */
    public function commoditySearchAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$page = !$getData['page'] ? 1 : intval($getData['page']);
    	
    	$getData['page'] = $page; //分页
    	$getData['pagesize'] = $this->_pagesize;//分页大小
    	$getData['w'] = 240; //图片缩略
    	$getData['city'] = $this->_city; //所在城市
    	$data = $this->_model->getNewCommodityMore($getData);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 店铺粉丝
     */
    public function shopFansAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['sid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	$data = $this->_model->getShopFans($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 用户加关注
     */
    public function userFollowAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	 
    	if(!$getData['fuuid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '我的用户UUID不能为空')));
    	}
    	
    	if(!$getData['tuuid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '好友用户UUID不能为空')));
    	}
		
    	$data = $this->_model->addUserConcerned($getData, $this->_city);
    	 
    	exit(json_encode($this->returnArr(1, array('message' => $data['message']), $data['result'])));
    }
    /**
     * 用户取消关注
     */
    public function cancelFollowAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	if(!$getData['fuuid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '我的用户UUID不能为空')));
    	}
    	 
    	if(!$getData['tuuid']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '好友用户UUID不能为空')));
    	}
    	
    	$data = $this->_model->cancelUserConcerned($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, array('message' => $data['message']), $data['result'])));
    }
    
    /**
     * 新增店铺收藏
     */
    public function shopFavAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$shop_id = intval($getData['sid']);
    	
    	if(!$shop_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	if(!$getData['uuid'] || !$getData['uname']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
    	}
    	
    	$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
    	if(!$user_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
    	}	
    	
    	$isFav = Model_Api_Goods::getInstance()->isShopFav($user_id, $shop_id, 'oto_shop_favorite');

    	if ($isFav) {
    		exit(json_encode($this->returnArr(0, array(), 300, '您已经收藏了这个店铺')));
    	}
    	//新增店铺收藏
    	$result = Model_Api_Goods::getInstance()->addShopFav($shop_id, $user_id, CLIENT_IP);
    	//改变用户收藏店铺数量
    	$this->updateQuantityFavShopByUserId($user_id);
    	
    	exit(json_encode($this->returnArr(1, array())));
    }
    
    /**
     * 取消店铺收藏
     */
    public function delShopFavAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$shop_id = intval($getData['sid']);
    	
    	if(!$shop_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '店铺ID不能为空')));
    	}
    	
    	if(!$getData['uuid'] || !$getData['uname']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
    	}
    	
    	$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
    	if(!$user_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
    	}	
    	
    	$isFav = Model_Api_Goods::getInstance()->isShopFav($user_id, $shop_id, 'oto_shop_favorite');

    	if (!$isFav) {
    		exit(json_encode($this->returnArr(0, array(), 300, '您还没有收藏这个店铺')));
    	}
    	//删除店铺收藏
    	$result = Model_Api_Goods::getInstance()->delFavShop($shop_id, $user_id, CLIENT_IP);
    	//改变用户收藏店铺数量
    	$this->updateQuantityFavShopByUserId($user_id);
    	
    	exit(json_encode($this->returnArr(1, array())));
    }
	/**
	 * 商城商品收藏
	 */
    public function addCommodityFavAction() {
        $getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$ticket_id = intval($getData['tid']);
    	
    	if(!$ticket_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	if(!$getData['uuid'] || !$getData['uname']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
    	}
    	
    	$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
    	if(!$user_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
    	}
    	
    	$isFav = $this->_model->isFavorite('oto_ticket_favorite', $ticket_id, $user_id);
    	if ($isFav) {
    		exit(json_encode($this->returnArr(0, array(), 300, '您已经收藏了这个宝贝')));
    	}
    	
    	$result = $this->_model->addFavorite('oto_ticket_favorite', $ticket_id, $user_id);
    	//改变用户收藏商品数量
    	$this->_model->updateQuantityFavByUserId('oto_ticket_favorite', $user_id);
    	if( $result ){
    		//同步到oto_user_dynamic
			$title = $this->_db->fetchOne("SELECT `ticket_title` FROM `oto_ticket` WHERE `ticket_id`='{$ticket_id}'");
			$this->syncFavoriteDynamic(array('user_id' => $user_id, 'from_id' => $ticket_id, 'summary' => $title,'type'=>1,'favorite_id'=>$result,'created'=>REQUEST_TIME));
    	}
    	exit(json_encode($this->returnArr(1, array())));
    }
    /**
     * 获取我的商城商品收藏
     */
    public function myCommodityFavAction() {
    	$getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);

    	if(!$getData['uuid'] || !$getData['uname']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
    	}
    	 
    	$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
    	if(!$user_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
    	}
	 
    	$getData['user_id'] = $user_id;
    	$getData['pagesize'] = $this->_pagesize;
    	
    	$data = $this->_model->getFavCommodity($getData, $this->_city);
    	
    	exit(json_encode($this->returnArr(1, $data)));
    }
    /**
     * 取消商城商品收藏
     */
    public function delCommodityFavAction() {
        $getData = $this->_http->getParams();
    	//传输加密验证
    	$this->auth($getData);
    	
    	$ticket_id = intval($getData['tid']);
    	
    	if(!$ticket_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '商品ID不能为空')));
    	}
    	
    	if(!$getData['uuid'] || !$getData['uname']) {
    		exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
    	}
    	
    	$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
    	if(!$user_id) {
    		exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
    	}
    	
    	$isFav = $this->_model->isFavorite('oto_ticket_favorite', $ticket_id, $user_id);
    	if (!$isFav) {
    		exit(json_encode($this->returnArr(0, array(), 300, '您还没有收藏了这个宝贝')));
    	}
    	
    	$result = $this->_model->delFavorite('oto_ticket_favorite', $ticket_id, $user_id);
    	//改变用户收藏商品数量
    	$this->_model->updateQuantityFavByUserId('oto_ticket_favorite', $user_id);
    	if( $result ){
    		//删除oto_user_dynamic中相应的记录
    		$this->removeFavoriteDynamic($user_id,$ticket_id,1);
    	}
    	exit(json_encode($this->returnArr(1, array())));
    }
    
}