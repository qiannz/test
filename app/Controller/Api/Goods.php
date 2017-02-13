<?php
class Controller_Api_Goods extends Base {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Goods::getInstance();
		$this->_city = !$this->_http->get('city') ? $this->_city : strval($this->_http->get('city'));
	}

	// 宝贝列表最热页面
	public function hotAction() {
		$postData = $this->_http->getPost();		
		$page = max(1, intval($postData['page']));
		$hots = $this->_model->getHot($this->_city, $page);
		exit(json_encode($hots));
	}
	
	// 宝贝列表最新页面
	public function newAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$newest = $this->_model->getNew($page);
		$pos_id = $this->getPosId('phone_head', $this->_city);
		$newest['head'] = $this->_model->getPhoneHead($pos_id, $this->_city);
		$newest['store'] = $this->_model->getAppStore($this->_city);
		exit(json_encode($newest));
	}	
	
	// 按分类搜索宝贝列表
	public function storeGoodAction() {
		$postData = $this->_http->getPost();
		$storeId = intval($postData['store_id']);
		$page = max(1, intval($postData['page']));
		$goodList = $this->_model->getGoodListByStore($storeId, $this->_city, $page);
		exit(json_encode($goodList));
	}
	
	// 宝贝列表身边页面
	public function nearAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$errorMsg = '';
		$post_lng = $postData['lng'];
		$post_lat = $postData['lat'];
		if (empty($post_lng) || empty($post_lat)) {
			$errorMsg .= '无法获取坐标|';
		}
		$distance = $postData['distance'];
		if (empty($distance)) {
			$errorMsg .= '请选择距离半径';
		}
		if (!empty($errorMsg)) {
			exit(json_encode($this->returnArr(0, '', 300, $errorMsg)));
		}
		$nears = $this->_model->getNear($this->_city, $post_lng, $post_lat, $distance, $page);
		exit(json_encode($nears));
	}
	
	// 宝贝身边行政区
	public function regionAction() {
		$region = $this->getRegion(0, true, $this->_city);
		$data = array();
		foreach ($region as $key => $value) {
			$data[] = array(
					'id' => $key,
					'name' => $value
				);
		}
		$arr = $this->returnArr(count($data),$data);
		exit(json_encode($arr));
	}
	
	// 宝贝身边商圈（热门）
	public function circleAction() {
		$postData = $this->_http->getPost();
		$rid = intval($postData['region_id']);
		if ($rid == 0) {
			$circle = $this->getCircleByRegionId(0, true, true, $this->_city);
		}else{
			$circle = $this->getCircleByRegionId($rid, false, true, $this->_city);
		}
		$arr = $this->returnArr(count($circle),$circle);
		exit(json_encode($arr));
	}
	
	// 商圈内的宝贝
	public function circleGoodAction() {
		$postData = $this->_http->getPost();
		$cid = intval($postData['circle_id']);
		if (empty($cid)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择商圈')));
		}
		$page = max(1, intval($postData['page']));
		$goods = $this->_model->getCircleGood($cid, $this->_city, $page);
		exit(json_encode($goods));
	}
	
	// 搜索宝贝
	public function searchGoodAction() {
		$postData = $this->_http->getPost();
		$gname = urldecode(trim($postData['gname']));
		if (empty($gname)) {
			exit(json_encode($this->returnArr(0, '', 300, '请填写宝贝名称')));
		}
		$page = max(1, intval($postData['page']));
		$goods = $this->_model->searchGood($gname, $this->_city, $page);
		$this->_model->setKeywords($gname, 1, $this->_city);
		exit(json_encode($goods));
	}
	
	// 搜索店铺下的宝贝
	public function searchShopAction() {
		$postData = $this->_http->getPost();
		$sname = urldecode(trim($postData['sname']));
		if (empty($sname)) {
			exit(json_encode($this->returnArr(0, '', 300, '请填写店铺名称')));
		}
		$page = max(1, intval($postData['page']));
		$goods = $this->_model->searchShop($sname, $this->_city, $page);
		$this->_model->setKeywords($sname, 2, $this->_city);
		exit(json_encode($goods));
	}
	
	// 宝贝详情显示接口(作废)
	public function detailAction() {
		$postData = $this->_http->getPost();
		$type = $this->_http->get('type'); 
		$gid = $this->_http->get('gid');
		$uuid = $postData['uuid'];
		$uname = urldecode($postData['uname']); 
		$lng = $this->_http->get('lng');
		$lat = $this->_http->get('lat');
		$errorMsg = '';
		if (empty($gid)){
			$errorMsg .= '请选择宝贝';
		}
		if(!empty($errorMsg)) {
			exit(json_encode($this->returnArr(0, '', 300, $errorMsg)));
		}
		$goods = $this->_model->detail($gid, $type, $this->_city,  $lng, $lat, $uuid, $uname);
		$goods['result']['coupon'] = $this->_model->detailCoupon($gid);
		$this->addclick($gid);
		exit(json_encode($goods));
	}
	
	// 宝贝详情接口new（新版）
	public function goodDetailAction() {
		$postData = $this->_http->getParams();
		$gid = $postData['gid'];
		$uuid = $postData['uuid'];
		$uname = urldecode($postData['uname']);
		$page = max(1, intval($postData['page']));
		$pagesize = !$postData['pagesize'] ? PAGESIZE : intval($postData['pagesize']);
 
		if (empty($gid)){
			exit(json_encode($this->returnArr(0, '', 300, '请选择宝贝')));				
		}
				
		$goods = $this->_model->goodView($gid, $this->_city, $uuid, $uname, $page, $pagesize);
		$goods['result']['coupon'] = $this->_model->detailCoupon($gid);
		$this->addclick($gid);
		exit(json_encode($goods));
	}
	
	// 券列表 (作废)
	public function couponListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$couponList = $this->_model->getCouponList($this->_city, $page);
		exit(json_encode($couponList));
	}
	
	// new券列表(作废)
	public function couponListNewAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$couponList =  $this->_model->getClist($page);
		exit(json_encode($couponList));
	}
	
	// 获取券剩余数量 (非缓存)
	public function surplusAction() {
		$postData = $this->_http->getPost();
		$data =  $this->_model->getCouponSurplus($postData);
		exit(json_encode($this->returnArr(1, $data)));
	}
	
	// 2013-08-13 优惠券列表（新版）(作废)
	public function couponAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$couponList =  $this->_model->getCouponListNew($postData, $page);
		echo json_encode($couponList);
		exit();
	}
	
	
	// 2013-09-01  (现金券, 优惠券列表 独立) (作废)
	public function ticketListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$couponList =  $this->_model->getTicketList($postData, $page);
		echo json_encode($couponList);
		exit();
	}
	
	/**
	 * 优惠券首页入口
	 * 2013-09-12  (优惠券分类列表)
	 */ 
	public function ticketClassAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$couponList =  $this->_model->getTickeClasstList($postData, $this->_city, $page);
		exit(json_encode($couponList));
	}
	
	/**
	 * 我的优惠券 
	 */
	public function myCouponAction() {
		$postData = $this->_http->getParams();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$orderStatus = !$postData['orderStatus'] ? -1 : intval($postData['orderStatus']);
		$is_tuan = !$postData['is_tuan'] ? 0 : intval($postData['is_tuan']);
		$this->isLogin($uuid, $uname);
		//$this->auth($postData);
		
		$page = max(1, intval($postData['page']));
		$pagesize = !$postData['pagesize'] ? 100 : intval($postData['pagesize']);
		
		$ticketList =  $this->_model->getMyCouponList($uuid, $uname, $is_tuan, $orderStatus, $page, $pagesize); // 优惠券
		exit(json_encode($ticketList));
	}
	/**
	 * 我的优惠券 => 根据用户名和订单号获取某个优惠券明细 
	 */
	public function myCouponOneAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$this->auth($postData);	
		$ticketRow =  $this->_model->getMyCouponOne($postData);
		exit(json_encode($ticketRow));
	}
	/**
	 * 我的优惠券 （免费券）(作废)
	 */
	public function myTicketCouponAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$this->isLogin($uuid, $uname);
		$this->auth($postData);
		$uid = $this->checkUid($uuid, $uname);
		$page = max(1, intval($postData['page']));
		$couponList = $this->_model->getMyTicketCoupon($uid, $uname, $page); // 免费券
		echo json_encode($couponList);
		exit();
	}

	/**
	 * 我的优惠券 （现金券）(作废)
	 */
	public function myTicketVoucherAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$this->isLogin($uuid, $uname);
		$this->auth($postData);
		$uid = $this->checkUid($uuid, $uname);
		$page = max(1, intval($postData['page']));
		$voucherList = $this->_model->getMyTicketVoucher($uuid, $uname, $page); // 现金券
		echo json_encode($voucherList);
		exit();
	}

	
	// 券详情接口
	public function couponDetailAction() {
		$tid = $this->_http->get('tid');
		if (empty($tid)){
			echo json_encode($this->returnArr(0, '', 300, '请选择优惠券'));
			exit();
		}
		$coupon =  Model_Home_Ticket::getInstance()->getTicktRow($tid);
		echo json_encode($this->returnArr(1, $coupon));
		exit();
	}
	
	// 商圈搜索宝贝的详情页(作废)
	public function circleGoodViewAction() {
		$postData = $this->_http->getPost();
		$gid = $this->_http->get('gid');
		$cid = $this->_http->get('cid');
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$uname = urldecode($uname);
		$errorMsg = '';
		if (empty($gid)){
			$errorMsg .= '请选择宝贝|';
		}
		if (empty($cid)){
			$errorMsg .= '请选择商圈';
		}
		if(!empty($errorMsg)) {
			echo json_encode($this->returnArr(0, '', 300, $errorMsg));
			exit();
		}
		$goods = $this->_model->circleGoodView($gid, $cid, $uuid, $uname);
		$this->_model->addclick($gid);
		echo json_encode($goods);
		exit();
	}
	
	// 收藏夹接口
	public function favAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uid'];
		$uname = $postData['uname'];
		$this->isLogin($uuid, $uname);
		$page = max(1, intval($postData['page']));
		$fav_goods = $this->_model->getFavGood($uuid, $uname, $this->_city, $page);
		exit(json_encode($fav_goods));
	}
	
	// 我发布的
	public function publishAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uid'];
		$uname = $postData['uname'];
		$this->isLogin($uuid, $uname);
		$page = max(1, intval($postData['page']));
		$publish_goods = $this->_model->getPublishGood($uuid, $uname, $this->_city, $page);
		exit(json_encode($publish_goods));
	}
	
	/**
	 * 添加宝贝 （优化接口 ）
	 * 目前临时方法，上线后改名
	 */ 
	public function addGoodTmpAction() {
		$postData = $this->_http->getPost();
		//$this->auth($postData);
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$sid = $postData['sid'];
		$sname = Custom_String::HtmlReplace(urldecode(trim($postData['sname'])), 1);
		$shop_address = Custom_String::HtmlReplace(urldecode(trim($postData['saddress'])), 1);
		$postData['user_id'] = $uid;
		if (empty($postData['gname'])) {
			exit(json_encode($this->returnArr(0, '', 300, '请填写宝贝名称')));
		}
		
		if (empty($postData['dprice'])) {
			exit(json_encode($this->returnArr(0, '', 300, '请填写宝贝价格')));
		}
		
		if (empty($postData['sname'])) {
			exit(json_encode($this->returnArr(0, '', 300, '请填写店铺名称')));
		}
		
		if (empty($postData['shop_address'])) {
			exit(json_encode($this->returnArr(0, '', 300, '请填写店铺地址')));
		}
		
		$lng = $lat = 0;
		$lngLatString = $this->getLatitudeAndLongitudeFormamap($shop_address);
		if($lngLatString) {
			list($lng, $lat) = explode(',', $lngLatString);
		}
		$postData['lng'] = $lng;
		$postData['lat'] = $lat;
		// 创建店铺过程  
		if (!$sid) {
			$shop_id = $this->_db->fetchOne("select shop_id from oto_shop where `shop_name` = '{$sname}' and `city` = '{$this->_city}'");
			if (!$shop_id) {
				$insertId = $this->_model->addShop($postData, $this->_city);
				$postData['sid'] = $insertId;
			}else{
				$postData['sid'] = $shop_id;
			}
		}
		$result = $this->_model->addGood($postData, $this->_city);
		if ($result) {
			$this->updateQuantityTotalGoodByUserId($uid);
			exit(json_encode($this->returnArr(1, '', 100, '添加成功')));
		} else {
			exit(json_encode($this->returnArr(0, '', 300, '添加失败')));
		}
	}
	
	// 添加宝贝接口
	public function addAction() {	
		$postData = $this->_http->getPost();
		//$this->auth($postData);
		$this->isLogin($postData['uuid'], $postData['uname']);
		$user_id = $this->checkUid($postData['uuid'], $postData['uname']);
		$postData['user_id'] = $user_id;
		$errorMsg = '';
		$good_name = trim($postData['gname']);
		$shop_id   = $postData['sid'];
		$dis_price = $postData['dprice'];
		$ip = $postData['ip'];
		if (empty($good_name)) {
			$errorMsg .= '请填写宝贝名称|';
		}	
		if (empty($shop_id)) {
			$errorMsg .= '请选择店铺|';
		}
		if (empty($dis_price)) {
			$errorMsg .= '请填写宝贝价格';
		}
		if (!empty($errorMsg)) {
			exit(json_encode($this->returnArr(0, '', 300, $errorMsg)));
		}
		$result = $this->_model->addGood($postData, $this->_city);
		if ($result) {
			$this->updateQuantityTotalGoodByUserId($user_id);
			exit(json_encode($this->returnArr(1, '', 100, '添加成功')));
		} else {
			exit(json_encode($this->returnArr(0, '', 300, '添加失败')));
		}
	}
	
	// 修改宝贝
	public function editAction() {
		$postData = $this->_http->getPost();
		//$this->auth($postData);
		$this->isLogin($postData['uuid'], $postData['uname']);
		$errorMsg = '';
		$shop_id   = $postData['sid'];
		$dis_price = $postData['dprice'];
		$ip = $postData['ip'];
		
		if (empty($postData['gid']) || empty($postData['gname'])) {
			$errorMsg .= '请填写宝贝名称|';
		}		
		if (empty($postData['sid'])) {
			$errorMsg .= '请选择店铺|';
		}
		if (empty($postData['dprice'])) {
			$errorMsg .= '请填写宝贝价格';
		}
		
		if (!empty($errorMsg)) {
			exit(json_encode($this->returnArr(0, '', 300, $errorMsg)));
		}
		
		$result = $this->_model->editGood($postData);
		
		if ($result) {
			exit(json_encode($this->returnArr(1, '', 100, '编辑成功')));
		} else {
			exit(json_encode($this->returnArr(0, '', 300, '编辑失败')));
		}
	}
	
	// 修改宝贝时 上传单图
	public function uploadAction() {
		$postData = $this->_http->getPost();
		$this->auth($postData);
		$img = $postData['img'];
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$gid = $postData['gid'];
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		if (empty($gid)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择宝贝')));
		}
		if (empty($img)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择上传图片')));
		}
		$result = $this->_model->uploadImg($img, $uid, $gid);
		exit(json_encode($result));
	}
	
	// 进入添加页面所需返回的店铺地址接口
	public function getShopNameAction() {
		$postData = $this->_http->getPost();
		$lat = $postData['lat'];  // 纬度
		$lng = $postData['lng'];  // 经度
		if (empty($lat) || empty($lng)) {
			exit(json_encode($this->returnArr(0, '', 300, '没有经纬度坐标')));
		}
		$shop_info = $this->getShopNameByLngLat($lng, $lat, 2, $this->_city);
		exit(json_encode($this->returnArr(count($shop_info), $shop_info)));
	}
	
	// 根据地址获取店铺经纬度  查询店铺列表
	public function getShopNameByAddressAction() {
		$postData = $this->_http->getPost();
		$shop_address = $postData['shop_address'];
		if (empty($shop_address)) {
			exit(json_encode($this->returnArr(0, '', 300, '请输入店铺地址')));
		}
		$lng = $lat = 0;
		$lngLatString = $this->getLatitudeAndLongitudeFormamap($shop_address);
		if($lngLatString) {
			list($lng, $lat) = explode(',', $lngLatString);
		}
		$shop_info = $this->getShopNameByLngLat($lng, $lat, 2, $this->_city);
		exit(json_encode($this->returnArr(count($shop_info), $shop_info)));
	}
	
	// 添加商铺
	public function addShopAction() {
		$postData = $this->_http->getPost();
		$errorMsg = '';
		$this->isLogin($postData['uuid'], $postData['uname']);
		$sname = Custom_String::HtmlReplace(urldecode(trim($postData['sname'])), 1);  // 店铺名称
		$saddress = Custom_String::HtmlReplace(urldecode(trim($postData['saddress'])), 1);  // 店铺地址
		$lat = $postData['lat'];
		$lng = $postData['lng'];
		
		if (empty($postData['sname'])) {
			$errorMsg .= '请填写店铺名称|';
		}
		if (empty($postData['saddress'])) {
			$errorMsg .= '请填写店铺地址';
		}
		if (!empty($errorMsg)) {
			exit(json_encode($this->returnArr(0, '', 300, $errorMsg)));
		}
		
		$insertId = $this->_model->addShop($postData, $this->_city);
		
		if ($insertId) {
			$data = array(
					'shop_id'      => $insertId,
					'shop_name'    => stripslashes_deep($sname),
					'shop_address' => stripslashes_deep($saddress),
					);
			exit(json_encode($this->returnArr(1, $data))); 
		} else {
			exit(json_encode($this->returnArr(0, '', 300, '添加商铺失败')));
		}
	}
	
	// 根据经纬度获取地址 高德地图
	public function getAddressAction() {
		$postData = $this->_http->getPost();
		$lat = $postData['lat'];  // 维度
		$lng = $postData['lng'];  // 经度
		if (empty($lat) || empty($lng)) {
			exit(json_encode($this->returnArr(0, '', 300, '没有经纬度坐标')));
		}
		$shop_address = $this->getAddressBylnglatFormamap($lng, $lat);
		exit(json_encode($this->returnArr(1, array('address' => $shop_address))));
	}
	
	// 宝贝喜欢加接口
	public function likeAction() {
		$postData = $this->_http->getPost();
		$gid = $postData['gid'];
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isLike = $this->_model->isLikeFav($uid, $gid, 'oto_good_concerned');
		if (empty($gid)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择喜欢的宝贝')));
		}
		if ($isLike) {
			exit(json_encode($this->returnArr(0, '', 100, '您已经对这个宝贝加过喜欢了哦')));
		}
		$result = $this->_model->addLike($postData);
		$this->updateQuantityLoveGoodByUserId($uid);
		exit(json_encode($result));
	}
	
	// 宝贝收藏接口
	public function addFavAction() {
		$postData = $this->_http->getPost();
		$gid = $postData['gid'];
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isLikeFav($uid, $gid, 'oto_good_favorite');
		if (empty($gid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择要收藏的宝贝')));
		}
		if ($isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您已经收藏了这个宝贝')));
		}
		$result = $this->_model->addFav($postData);
		$this->updateQuantityFavGoodByUserId($uid);
		exit(json_encode($result));
	}

	/**
	 * 店铺收藏 接口
	 */ 
	public function shopFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$sid = $postData['shop_id'];
		$ip = $postData['ip'];
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isShopFav($uid, $sid, 'oto_shop_favorite');
		if (empty($sid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择要收藏的店铺')));
		}
		if ($isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您已经收藏了这个店铺')));
		}
		$result = $this->_model->addShopFav($sid, $uid, $ip);
		$this->updateQuantityFavShopByUserId($uid);
		exit(json_encode($result));
	}
	
	/**
	 * 用户收藏的店铺列表
	 */
	public function shopFavListAction() {
		$postData = $this->_http->getPost();
		$lat = $postData['lat'];
		$lng = $postData['lng'];
 		$page = max(1, intval($postData['page']));
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		
		$result = $this->_model->getShopFav($lat, $lng, $uid, $this->_city, $page);
		exit(json_encode($result));
	}
	
	/**
	 * 用户收藏的品牌列表
	 */
	public function brandFavListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
 		$this->isLogin($postData['uuid'], $postData['uname']);
 		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$result = $this->_model->getFavBrandList($uid, $this->_city, $page);
		exit(json_encode($result));
	}
	
	/**
	 * 取消店铺收藏
	 */ 
	public function delShopFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$sid = $postData['shop_id'];
		$ip = $postData['ip'];
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isShopFav($uid, $sid, 'oto_shop_favorite');
		if (empty($sid)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择要收藏的店铺')));
		}
		if (!$isFav) {
			exit(json_encode($this->returnArr(0, '', 201, '您还没有收藏这个店铺')));
		}
		$result = $this->_model->delFavShop($sid, $uid, $ip);
		exit(json_encode($result));
	}
	
	/**
	 * 品牌收藏 接口
	 */ 
	public function brandFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$bid = $postData['brand_id'];
		$ip = $postData['ip'];
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isBrandFav($uid, $bid, 'oto_brand_favorite');
		if (empty($bid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择要收藏的品牌')));
		}
		if ($isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您已经收藏了这个品牌')));
		}
		$result = $this->_model->addBrandFav($bid, $uid, $ip);
		$this->updateQuantityFavBrandByUserId($uid);
		exit(json_encode($result));
	}
	
	/**
	 * 取消品牌收藏
	 */ 
	public function delBrandFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$bid = $postData['brand_id'];
		$ip = $postData['ip'];
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isBrandFav($uid, $bid, 'oto_brand_favorite');
		if (empty($bid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择要收藏的品牌')));
		}
		if (!$isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您还没有收藏这个品牌')));
		}
		$result = $this->_model->delFavBrand($bid, $uid, $ip);
		$this->updateQuantityFavBrandByUserId($uid);
		exit(json_encode($result));
	}
	
	
	// 取消收藏接口
	public function cancelFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isLikeFav($uid, $postData['gid'], 'oto_good_favorite');
		if (empty($postData['gid'])) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择要收藏的宝贝')));
		}		
		if (!$isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您还没有收藏这个宝贝')));
		}
		$fav_goods = $this->_model->cancelFavGood($postData, $uid);
		exit(json_encode($fav_goods));	
	}
	
	// 删除宝贝图片（详情内）
	public function delImgAction() {
		$postData = $this->_http->getPost();
		$this->auth($postData);
		$goodImgId = $postData['goodImgId'];
		$gid = $postData['gid'];
		$this->isLogin($postData['uuid'], $postData['uname']);
		if (empty($goodImgId)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择要删除的图片')));
		}
		$result = $this->_model->delImg($goodImgId, $gid);
		exit(json_encode($result));
	}
	
	// 获取头部banner
	public function getHeadAction() {
		$pos_id = $this->getPosId('phone_head', $this->_city);
		$result = $this->_model->getPhoneHead($pos_id, $this->_city);
		exit(json_encode($result));
	}
	
	
	public function favViewAction() {
		$uuid = $this->_http->get('uuid');
		$uname = urldecode($this->_http->get('uname'));
		$gid = $this->_http->get('gid');
		$this->isLogin($uuid, $uname);
		if (empty($gid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择宝贝')));
		}		
		$goods = $this->_model->favView($uuid, $uname, $gid);
		exit(json_encode($goods));		
	}
	
	// 我发布的详情
	public function publishViewAction() {
		$uuid = $this->_http->get('uuid');
		$uname = $this->_http->get('uname');
		$gid = $this->_http->get('gid');
		$this->isLogin($uuid, $uname);
		if (empty($gid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择宝贝')));
		}
		$goods = $this->_model->pulishView($uuid, $uname, $gid, $this->_city);
		exit(json_encode($goods));
	}
	
	// 宝贝名字搜索宝贝详情
	public function goodSearchViewAction() {
		$postData = $this->_http->getPost();
	    if (empty($postData['gid'])) {
	    	exit(json_encode($this->returnArr(0, array(), 300, '请选择宝贝')));
	    }  
	    if (empty($postData['gname'])) {
	    	exit(json_encode($this->returnArr(0, array(), 300, '搜索内容不能为空')));
	    }
	    $goods = $this->_model->goodSearchView($postData, $this->_city);
	    exit(json_encode($goods));
	}
	
	// 根据店铺名字搜索到宝贝的详情
	public function shopSearchViewAction() {
		$postData = $this->_http->getPost();
		$sname = urldecode($this->_http->get('sname'));
		
		$gid = $this->_http->get('gid');
		if (empty($postData['gid'])) {
			echo json_encode($this->returnArr(0, array(), 300, '请选择宝贝'));
			exit();
		}
		if (empty($postData['sname'])) {
			echo json_encode($this->returnArr(0, array(), 300, '搜索内容不能为空'));
			exit();
		}
		$goods = $this->_model->shopSearchView($postData, $gid, $sname);
		echo json_encode($goods);
		exit();
	}

	// 领取优惠券 (作废)
	public function applyAction() {
		$postData = $this->_http->getPost();
		$tid = intval($postData['tid']);
		$phone = $postData['phone'];
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];

		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		
		$errorMsg = '';
		// 判断手机号码是否存在， 券ID 是否合法
		if(!$tid) {
			$errorMsg .= '该优惠券不存在|';
		}
		
		if (!preg_match('/^1[3-9][0-9]{9}$/', $phone)) {
			$errorMsg .= '请填写正确的手机号码|';
		}
		
		if (!empty($errorMsg)) {
			echo json_encode($this->returnArr(0, array(), 300, $errorMsg));
			exit();
		}
		//领券逻辑开始
		$this->_model->applyTicket($tid, $phone, $uuid, $uname);
	}
	
	// 我的优惠券统计数量, 商品收藏统计数量, 上传的商品统计数量（作废）
	public function myNumAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		// 获取网页端用户Id
		
		$myNum = $this->_model->getMyNum($uuid, $uname);
		exit(json_encode($myNum));
	}
	
	/**
	 * 雷达扫描模块一 (作废)
	 * 根据搜索条件 获取 店铺，品牌，优惠券，宝贝 个数
	 */
	public function scanAction() {
		$postData = $this->_http->getPost();
		$scanData = $this->_model->getScan($postData);
		exit(json_encode($scanData));
	}

	/**
	 * 雷达扫描模块二 
	 * 根据搜索条件 获取 店铺，品牌，优惠券，宝贝 列表
	 * 
	 * 根据参数 type 区分各列表
	 */
	public function scanListAction() {
		$postData = $this->_http->getParams();
		$page = max(1, intval($postData['page']));
		$scanListData = $this->_model->getScanList($postData, $this->_city, $page);
		exit(json_encode($scanListData));
	}
	
	/**
	 * 店铺详情页(作废)
	 * 显示店铺品牌icon、店铺名称、地理位置、关联的优惠券和店内商品
	 * @param $shop_id
	 */
	public function shopViewAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$uid = $this->checkUid($uuid, $uname);
		$sid = $postData['shop_id'];
	    if (empty($sid)) {
	    	exit(json_encode($this->returnArr(0, array(), 300, '请选择店铺')));
	    } 
		$shopView = $this->_model->getShopView($sid, $uid, 7);
		exit(json_encode($shopView));
	}
	
	/**
	 * 店铺详情页(作废)
	 * 显示店铺品牌icon、店铺名称、地理位置、关联的优惠券和店内商品
	 * @param $shop_id
	 * 版本跟新 宝贝详情显示11个
	 */
	public function shopViewNewAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$uid = $this->checkUid($uuid, $uname);
		$sid = $postData['shop_id'];
		if (empty($sid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择店铺')));
		}
		$shopView = $this->_model->getShopView($sid, $uid, 11);
		exit(json_encode($shopView));
	}
	
	public function shopDetailAction() {
		$postData = $this->_http->getParams();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		$uid = $this->checkUid($uuid, $uname);
		$sid = $postData['shop_id'];
		$page = max(1, intval($postData['page']));
		if (empty($sid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择店铺')));
		}
		$pagesize = !$postData['pagesize'] ? PAGESIZE : intval($postData['pagesize']);
		
		$shopView = $this->_model->shopView($postData, $sid, $uid, $page, $pagesize, $this->_city);
		
		exit(json_encode($shopView));
	}
	
	/**
	 * 宝贝详情页详情页(作废)
	 * @param $good_id
	 */
	public function scanGoodViewAction() {
		$postData = $this->_http->getPost();
		$gid = $this->_http->get('gid');
		$goodView = $this->_model->getGoodView($gid, $postData);
		$goodView['result']['coupon'] = $this->_model->detailCoupon($gid);
		$this->addclick($gid);
		exit(json_encode($goodView));
	}
	
	/**
	 * 品牌详情页详情页
	 * @param $brand_id
	 * @param $lng
	 * @param $lat
	 */
	public function brandViewAction() {
		$postData = $this->_http->getPost();
 		$bid = $postData['brand_id'];
 		$uuid = $postData['uuid'];
 		$uname = $postData['uname'];
 		$uid = $this->checkUid($uuid, $uname);
		$page = max(1, intval($postData['page']));
		if (empty($bid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择品牌')));
		}
		$brandView = $this->_model->getBrandView($uid, $bid, $postData, $this->_city, $page);
		exit(json_encode($brandView));
	}
	
	/**
	 * 通过雷达扫描进入的品牌详情
	 */
	public function brandViewScanAction() {
		$postData = $this->_http->getPost();
		$bid = $postData['brand_id'];
		$page = max(1, intval($postData['page']));
		if (empty($bid)) {
			exit(json_encode($this->returnArr(0, '', 300, '请选择品牌')));
		}
		$brandView = $this->_model->getBrandViewScan($bid, $postData, $this->_city, $page);
		exit(json_encode($brandView));
	}
	
	/**
	 * 由首页进入的品牌详情
	 */
	public function brandViewHomeAction() {
		$postData = $this->_http->getParams();
		$bid = intval($postData['brand_id']);
		$page = max(1, intval($postData['page']));
		if (empty($bid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择品牌')));
		}
		$brandView = $this->_model->getBrandViewHome($bid, $postData, $this->_city, $page);
		
		exit(json_encode($brandView));
	}
	
	
	/**
	 * 宝贝详情单页 通用接口
	 * @param $gid
	 */
	public function goodViewAction() {
		$postData = $this->_http->getParams();
		$gid = $postData['good_id'];
		$page = max(1, intval($postData['page']));
		if (empty($gid)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择宝贝')));
		}
		$pagesize = !$postData['pagesize'] ? PAGESIZE : intval($postData['pagesize']);
		
		$goodDetail = $this->_model->getGoodOneDetail($gid, $postData, $this->_city,  $page, $pagesize);
		$goodDetail['result']['coupon'] = $this->_model->detailCoupon($gid, $this->_city);
		$this->addclick($gid);
		
		exit(json_encode($goodDetail));
	}
	
	/**
	 * 所有品牌列表
	 * @return 1. 热门品牌
	 * 		   2. 非热门品牌  按照收藏数，排序字段  来进行排序	
	 */
	public function brandListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$data = $this->_model->getBrandList($this->_city, $page);
		exit(json_encode($data));
	}
	
	/**
	 * 搜索品牌
	 * @return 根据关键字查询后的品牌列表， 按照收藏数，排序字段  来进行排序	
	 */
	public function searchBrandListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$key = urldecode(trim($postData['key']));
		$data = $this->_model->getBrandByKey($key, $this->_city, $page);
		exit(json_encode($data));
	}
	
	
	/**
	 * 2014-08-20 品牌列表新接口
	 */
	public function brandAction() {
		$postData = $this->_http->getPost();
		$data = $this->_model->getBrandListNew($this->_city);
		exit(json_encode($data));
	}
	
	/**
	 * 所有店铺列表
	 * @return 1. 存在经纬度  按照距离排序   根据收藏数排序
	 * 		   2. 不存在经纬度 根据收藏数排序	
	 */
	public function shopListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$data = $this->_model->getShopList($postData, $this->_city, $page);
		exit(json_encode($data));
	}
	
	/**
	 * 搜索店铺1
	 * @return 根据关键字查询后的店铺列表
	 *         1. 存在经纬度  按照距离排序   根据收藏数排序
	 * 		   2. 不存在经纬度 根据收藏数排序
	 */
	public function searchShopListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$key = urldecode(trim($postData['key']));
		$data = $this->_model->getShopByKey($key, $postData, $this->_city, $page);
		$this->_model->setKeywords($key, 2, $this->_city);
		exit(json_encode($data));
	}
	
	/**
	 * 搜索店铺2
	 * 根据关键字 返回商铺名称 地址 和ID
	 */
	public function searchShopByNameAction() {
		$postData = $this->_http->getPost();
		$sname = urldecode(trim($postData['sname']));
		$data = $this->_model->getShopByName($sname, $this->_city);
		$this->_model->setKeywords($sname, 2, $this->_city);
		exit(json_encode($data));
	}
	
	/**
	 * 我的任务列表
	 */
	public function myTaskAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		
   		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		
		//任务结束时间
		$task_end_time = $GLOBALS['GLOBAL_CONF']['TASK_END_TIME'];
		
		//天天向上-今日上传商品数量（已审核）
		$myTodayUploads =  Model_Home_User::getInstance()->getMyTodayUploads($uid);
		
		//十全大补-我当前完成度
		$myTenDays = Model_Home_User::getInstance()->getMyTenDays($uid, $myTodayUploads);
		
		//畅游迪拜-我上传的商品数（已审核）
		$myTotalUploads = Model_Home_User::getInstance()->getMyTotalUploads($uid);
		
		//畅游迪拜-最高上传商品数（已审核）
		$maxUploads = Model_Home_User::getInstance()->getMaxUploads();
		if (empty($maxUploads)) {
			$maxUploads = '0';
		}
		
		//街友最划算-刮刮卡数量
		$myClientEffectiveNum = Model_Home_User::getInstance()->getMyClientEffectiveNum($uid);
		
		//我的奖金
		$myBonus = Model_Home_User::getInstance()->getMyBonus($uid);

		//店员最划算-刮刮卡数量
		$userInfo = $this->_model->getLocalUserInfo($uid);
		if($userInfo['user_type'] == 3) {
			$myClerkEffectiveNum = Model_Home_User::getInstance()->getMyClerkEffectiveNum($uid);
		} else {
			$myClerkEffectiveNum = '0';
		}
		
		$data = array(
					'task_end_time'         => $task_end_time,
					'myEveryDayUploads'     => $myTodayUploads,
					'myTenDay'              => $myTenDays,
					'myDibaiUploads'        => $myTotalUploads,
					'maxDibaiuploads'       => $maxUploads,
					'myClientEffectiveNum'  => $myClientEffectiveNum,
					'myClerkEffectiveNum'   => $myClerkEffectiveNum,
					'myBonus'               => $myBonus
				);
		$arr = $this->returnArr(1,$data);
		exit(json_encode($arr));
	}
	
	/**
	 * 我的获奖历史 
	 */
	public function myWinAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		$myWinData = $this->_model->getMyWin($uid, $page);
		exit(json_encode($myWinData));
	}
	
	/**
	 * 街友刮奖
	 */
	public function clientScratchAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		$userInfo = array();
		$userInfo['user_id'] = $uid;
		$userInfo['user_name'] = $uname;
		//街友最划算-开始刮奖
		$clientResultMsgArray = Model_Home_User::getInstance()->clientScratchStart($userInfo);
		switch ($clientResultMsgArray['msg']) {
			case 'success' :
				$message = '恭喜你， 刮奖成功，还剩'. $clientResultMsgArray['over'] .'次刮奖机会';
				break;
			case 'failure' :
				$message = '刮奖失败';
				break;
			case 'exceed' :
				$message = '今日奖励已发完';
				break;				
			case 'emptyPrize' :
				$message = '谢谢参与';
				break;			
		}
		Custom_Log::logLog($uid, $clientResultMsgArray, 'client');
		echo json_encode($this->returnArr(1, '', $clientResultMsgArray['res'], $message));
		exit();
	}
	
	/**
	 * 店员刮奖
	 */
	public function clerkScratchAction() {
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		$userInfo = array();
		$userInfo['user_id'] = $uid;
		$userInfo['user_name'] = $uname;
		
		//店员最划算-开始刮奖
		$clerkResultMsgArray = Model_Home_User::getInstance()->clerkScratchStart($userInfo);
		switch ($clerkResultMsgArray['msg']) {
			case 'success' :
				$message = '恭喜你， 刮奖成功，还剩'. $clerkResultMsgArray['over'] .'次刮奖机会';
				break;
			case 'failure' :
				$message = '刮奖失败';
				break;
			case 'exceed' :
				$message = '今日奖励已发完';
				break;
			case 'emptyPrize' :
				$message = '谢谢参与';
				break;
		}
		Custom_Log::logLog($uid, $clerkResultMsgArray, 'clerk');
		echo json_encode($this->returnArr(1, '', $clerkResultMsgArray['res'], $message));
		exit();
	}
	
	/**
	 * 申请提现
	 */
	public function appExtractAction() {
		$postData = $this->_http->getPost();
 		$uuid = $postData['uuid'];
 		$uname = $postData['uname'];
		// 判断该用户是否登录
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		if(!$uid) {
			echo json_encode($this->returnArr(1, '', 300, '请先登录'));
			exit();
		}
		//我的奖金
		$myBonus = Model_Home_User::getInstance()->getMyBonus($uid);
		$errMsg = '';
		if(!preg_match('/^[0-9]+$/', $postData['money'])) {
			$errMsg .= '提取金额只能是整数<br>';
		}
		if(intval($postData['money']) > $myBonus) {
			$errMsg .= '提取金额错误<br>';
		}
		if(empty($postData['realName'])) {
			$errMsg .= '真实姓名不能为空<br>';
		}
		if(empty($postData['paypal'])) {
			$errMsg .= '淘宝账号不能为空<br>';
		}
		if($postData['paypal'] != $postData['repaypal']) {
			$errMsg .= '两次淘宝账号不一致';
		}
		if ($errMsg == '') {
			$userInfo = $this->_model->getLocalUserInfo($uid);
			$insertResult = Model_Home_User::getInstance()->addTaskMoney($postData, $userInfo);
			if($insertResult) {
				echo json_encode($this->returnArr(1, '', 100, '提交成功，请耐心等候审核。'));
				exit();
			} else {
				echo json_encode($this->returnArr(1, '', 300, '提交失败。'));
				exit();
			}
		} else {
			echo json_encode($this->returnArr(0, '', 300, $errMsg));
			exit();
		}
	}
	
	/**
	 * 提现历史
	 */
	public function myTaskExtractAction() {		
		$postData = $this->_http->getPost();
		$uuid = $postData['uuid'];
		$uname = $postData['uname'];
		//验证是否登录状态
		$this->isLogin($uuid, $uname);
		$uid = $this->checkUid($uuid, $uname);
		$page = max(1, intval($postData['page']));
		$userInfo = array();
		$userInfo['user_id'] = $uid;
		$taskMoney = Model_Home_User::getInstance()->getTaskMoney($userInfo, $page);
		$taskMoneyArr = $taskMoney['data'];
		foreach ($taskMoneyArr as &$row) {
			if ($row['operat_status'] == 1) {
				$row['status'] = '申请中';
			} elseif ($row['operat_status'] == 2) {
				if ($row['operat_result'] == -1) {
					$row['status'] = '提现失败';
				} elseif ($row['operat_result'] == 1) {
					$row['status'] = '提现成功';
				}
			}
			$row['app_time'] = date('Y-n-d', $row['app_time']);
		}
		echo json_encode($this->returnArr(count($taskMoneyArr), $taskMoneyArr));
		exit();
	}
	
	// 获取热门词
	public function getHotWordAction() {
		$postData = $this->_http->getPost();
		$type = intval($postData['type']);
		$hotWord = $this->_model->getHotKey($type, $this->_city);
		exit(json_encode($hotWord));
	}
		
	// 营业员所在店铺下的现金券的明细
	public function shopCouponRowAction() {
		$postData = $this->_http->getPost();
		$ticket_uuid = $postData['product_id'];
		if (!$ticket_uuid) {
			exit(json_encode($this->returnArr(0, '', 101, '请选择要查询的店铺')));
		}
		$couponList = Custom_AuthTicket::getShopCouponRow($ticket_uuid);
		if ($couponList['code'] == 1) {
			$rs = $couponList['message']['Result'];
			exit(json_encode($this->returnArr(1, $rs)));
		} else {
			exit(json_encode($this->returnArr(0, '', 102, '数据异常，请重新查询')));
		}
	}
	
	public function shopCouponDetailAction() {
		$postData = $this->_http->getPost();
		$ticket_uuid = $postData['product_id'];
		
		if (!$ticket_uuid) {
			exit(json_encode($this->returnArr(0, '', 101, '请选择要查询的店铺')));
		}
		
		$couponDetail =$this->_model->getShopCouponDetail($ticket_uuid, $postData, $this->_city);
		
		if ($couponDetail) {
			exit(json_encode($this->returnArr(1, $couponDetail, 100)));
		} else {
			exit(json_encode($this->returnArr(0, array(), 201, 'no coupon')));
		}
	}
	
	
	// 超级精选
	public function pickAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$data = $this->_model->getRecommendListByIdentifier('index_value_pick', $this->_city , $page);
		$data['store'] = $this->_model->getAppStore($this->_city);
		exit(json_encode($data));
	}
	
	/**
	 * 商场模块
	 * 1.商场列表
	 * 2.根据商场名查询
	 * 3.根据距离，商区查询
	 * 4.详情
	 */
	
	//  商场列表
	public function marketListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$markList = $this->_model->getMarket($postData, $this->_city, $page);
		exit(json_encode($markList));
	}
	
	// 根据商场名查询
	public function searchMarketByNameAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$markList = $this->_model->getMarketByName($postData, $this->_city, $page);
		exit(json_encode($markList));	
	}
	
	// 根据距离，商区查询 
	public function searchMarketAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$markList = $this->_model->getMarketBySearch($postData, $this->_city, $page);
		exit(json_encode($markList));
	}
	
	// 商场详情
	public function marketViewAction() {
		$postData = $this->_http->getParams();
		$market_id = intval($postData['market_id']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		if (empty($market_id)) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择商场')));
		}
		$marketView = $this->_model->getMarketView($market_id, $uid, $this->_city);
		
		exit(json_encode($marketView));
	}
	
	// 商场收藏接口
	public function marketFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$market_id = $postData['market_id'];
		if (empty($market_id)) {
			exit(json_encode($this->returnArr(0, array(), 101, '请选择要收藏的商场')));
		}
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isMarketFav($uid, $market_id, 'oto_market_favorite');		
		if ($isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您已经收藏了这个商场')));
		}
		$result = $this->_model->addMarketFav($market_id, $uid, $postData['ip']);
		$this->updateQuantityFavMarketByUserId($uid);
		exit(json_encode($result));
	}
	
	
	//  取消商场收藏
	public function delMarketFavAction() {
		$postData = $this->_http->getPost();
		$this->isLogin($postData['uuid'], $postData['uname']);
		$market_id = $postData['market_id'];		
		if (empty($market_id)) {
			exit(json_encode($this->returnArr(0, array(), 101, '请选择要取消收藏的商场')));
		}
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$isFav = $this->_model->isMarketFav($uid, $market_id, 'oto_market_favorite');
		if (!$isFav) {
			exit(json_encode($this->returnArr(0, array(), 201, '您还没有收藏这个商场')));
		}
		$result = $this->_model->delFavMarket($market_id, $uid, $postData['ip']);
		$this->updateQuantityFavMarketByUserId($uid);
		exit(json_encode($result));
	}
	
	
	// 我收藏的商场列表
	public function marketFavListAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$this->isLogin($postData['uuid'], $postData['uname']);
		$uid = $this->checkUid($postData['uuid'], $postData['uname']);
		$result = $this->_model->favMarketList($uid, $postData, $this->_city, $page);
		exit(json_encode($result));
	}
	
	// 2014-09-25  品牌列表（按分类显示）
	public function brandStoreListAction () {
		$brandStoreList = $this->_model->getBrandStoreList($this->_city);
		exit(json_encode($brandStoreList));
	}
	
	// 2014-09-26 根据分类ID查询品牌列表
	public function brandListByStoreAction () {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$store_id = intval($postData['store_id']);
		if (!$store_id) {
			exit(json_encode($this->returnArr(0, array(), 101, '请选品牌分类')));
		}
		
		$brandStoreList = $this->_model->getBrandByStore($store_id, $postData, $this->_city, $page);
		exit(json_encode($brandStoreList));
	}
	
	/**
	 * 2014-08-20  品牌名搜索新接口
	 */
	public function brandSearchAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$bname = urldecode(trim($postData['brand_name']));
		$data = $this->_model->getBrandListByName($bname, $this->_city, $page);
		exit(json_encode($data));
	}
	
	// 2014-09-26 商场首页新版
	public function marketIndexAction() {
		$postData = $this->_http->getPost();
		$marketList = $this->_model->getMarketIndex($postData, $this->_city);
		exit(json_encode($marketList));
	}	
	
	// 根据商圈ID得到商场列表
	public function marketCircleAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$circle_id = intval($postData['circle_id']);
		if (!$circle_id) {
			exit(json_encode($this->returnArr(0, array(), 101, '请选热门商区')));
		}
		$marketList = $this->_model->getMarketByCircle($circle_id, $postData, $this->_city, $page);
		exit(json_encode($marketList));
	}
	
	// 全部推荐商场列表
	public function recomMarketAction() {
		$postData = $this->_http->getPost();
		$page = max(1, intval($postData['page']));
		$markets = $this->_model->getAllRecomMarket($postData, $this->_city, $page);
		exit(json_encode($markets));
	}
	/**
	 * 订单管理
	 */
	public function orderManageAction() {
		$postData = $this->_http->getPost();
		//验证
		$this->auth($postData);
		//判断登录状态
		$this->isLogin($postData['uuid'], $postData['uname']);
		//执行	
		$orderResult = $this->_model->orderManagement($postData);
		exit(json_encode($orderResult));		
	}
	/**
	 * 获取团购首页
	 */
	public function getTuanIndexAction() {
		$getData = $this->_http->getPost();
		//验证
		$this->authTwo($getData);

		$resArr = array();
		//banner
		$app_banner = Model_Home_Index::getInstance()->getRecommendListByIdentifier('buygood_app_banner', $this->_city, 5);
		$resArr['app_banner'] = array_values($app_banner);
		foreach($resArr['app_banner'] as & $bannerRow) {
			$ticketRow = Model_Home_Ticket::getInstance()->getTicketRow($bannerRow['come_from_id']);
			$bannerRow['ticket_uuid'] = $ticketRow['ticket_uuid'];
		}
		
		//APP分类
		$resArr['app_store'] = $this->_model->getAppStore($this->_city);
		//秒杀
		$resArr['app_spike'] = Model_Home_Ticket::getInstance()->getTuanRecommend('buygood_spike', $this->_city, 5);
		foreach ($resArr['app_spike'] as $key => $item) {
			$ticketSurplus = Custom_AuthTicket::get_ticket_details_by_guid($item['ticket_uuid']);
			$resArr['app_spike'][$key]['surplus'] = $ticketSurplus->data->Avtivities[0]->ProductStock; // 剩余
			$resArr['app_spike'][$key]['total'] = $ticketSurplus->data->Avtivities[0]->ProductNum; // 总数
			$resArr['app_spike'][$key]['has_led'] = $ticketSurplus->data->Avtivities[0]->ProductDisplaySale; // 售出
		}		
		//推荐
		$resArr['app_recommend'] = Model_Home_Ticket::getInstance()->getTuanRecommend('buygood_hot', $this->_city, 50);
		
		foreach ($resArr['app_recommend'] as $key => $item) {
			$ticketSurplus = Custom_AuthTicket::get_ticket_details_by_guid($item['ticket_uuid']);
			$resArr['app_recommend'][$key]['surplus'] = $ticketSurplus->data->Avtivities[0]->ProductStock; // 剩余
			$resArr['app_recommend'][$key]['total'] = $ticketSurplus->data->Avtivities[0]->ProductNum; // 总数
			$resArr['app_recommend'][$key]['has_led'] = $ticketSurplus->data->Avtivities[0]->ProductDisplaySale; // 售出		
		}
		
		exit(json_encode($this->returnArr(1, $resArr)));
	}
	/**
	 * 团购详情页
	 */
	public function getTuanShowAction() {
		$getData = $this->_http->getParams();
		//验证
		$this->authTwo($getData);
		if (!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择团购商品')));
		}
		
		$tuanShow = array();
		//团购明细
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($getData['tid']);
		$tuanShow['detail'] = Model_Home_Ticket::getInstance()->getTicktRow($ticketRow['ticket_id']);
		$ticketSurplus = Custom_AuthTicket::get_ticket_details_by_guid($tuanShow['detail']['ticket_uuid']);
		$tuanShow['detail']['surplus'] = $ticketSurplus->data->Avtivities[0]->ProductStock; // 剩余
		$tuanShow['detail']['total'] = $ticketSurplus->data->Avtivities[0]->ProductNum; // 总数
		$tuanShow['detail']['has_led'] = $ticketSurplus->data->Avtivities[0]->ProductDisplaySale; // 售出
		if($tuanShow['detail']['app_price'] < 0) {
			$tuanShow['detail']['selling_price'] = 0;
		} elseif($tuanShow['detail']['app_price'] > 0) {
			$tuanShow['detail']['selling_price'] = $tuanShow['detail']['app_price'];
		}
		//团购消费提示
		$configArray = @include VAR_PATH . 'config/config.php';
		$tuanShow['detail']['tips'] = $configArray['CONSUMER_TIPS'] ? $configArray['CONSUMER_TIPS'] : '';
		//推荐
		$tuanShow['recommend'] = Model_Home_Ticket::getInstance()->getTuanRecommend('buygood_hot', $this->_city, 20);
		//团购关联店铺
		$tuanShow['associated_shops'] = Model_Home_Ticket::getInstance()->getAssociatedShops($getData['tid'], Model_Home_Good::getInstance()->getShop($tuanShow['detail']['shop_id']));
		
		exit(json_encode($this->returnArr(1, $tuanShow)));
	}
	/**
	 * 团购列表页
	 */
	public function getTuanListAction() {
		$getData = $this->_http->getParams();
		//验证
		$this->authTwo($getData);
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$storeid = !$getData['storeid'] ? 0 : intval($getData['storeid']);
		$dtype = !$getData['dtype'] ? 'time' : strval($getData['dtype']);
		$dsort = !$getData['dsort'] ? 'desc' : strval($getData['dsort']);
				
		$tuanList = array();
		//APP分类
		$tuanList['app_store'] = $this->_model->getAppStore($this->_city);
		//推荐
		$tuanList['show_list'] = Model_Home_Ticket::getInstance()->getTuanInfo($storeid, $dtype, $dsort, $this->_city, $page);
				
		exit(json_encode($this->returnArr(1, $tuanList)));
	}
	
	// 接口验证公共函数
	public function auth($postData) {
		//logLog('auth.log', var_export($postData, true));
		$ssid = $postData['ssid'];
		if ($postData['img']) {
			unset($postData['img']);
		}		
		unset($postData['ssid']);
		unset($postData['uname']);
		ksort($postData);
		
		foreach ($postData as $key => $value) {
			$http_query_string .= "{$key}={$value}&";
		}

		$http_query_string = substr($http_query_string, 0, -1) . '&key=' . $GLOBALS['GLOBAL_CONF']['Auth_Key'];
		if ($ssid != md5($http_query_string)) {
			exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
		}
	}
	
	private function authTwo($getData) {
		$encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
		Third_Des::$key = '34npzntC';
		if (!$getData['ssid'] || $getData['ssid'] != Third_Des::encrypt($encryptString)) {
			exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
		}
	}	
}