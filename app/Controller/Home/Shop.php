<?php
class Controller_Home_Shop extends Controller_Home_Abstract {	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Shop::getInstance();
	}
	
	public function showAction() {
		$sid = intval($this->_http->get('sid'));
		$f = intval($this->_http->get('f'));
		$order = !$this->_http->get('order') ? 1 : intval($this->_http->get('order'));
		if (!$sid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		
		$shopRow = $this->getShopFieldById($sid, 'shop_id, shop_pid, shop_name,brand_id,region_id,circle_id,store_id,is_flag,is_enable');
		
		//判断店铺是否存在
		if(empty($shopRow)) {
			Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		}
		//判断店铺是否被合并
		if ($shopRow['shop_pid'] != 0) {
			Custom_Common::jumpto('/home/shop/show/sid/' . $shopRow['shop_pid'], 3);
		}
		
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		//旗舰店
		if(($shopRow['is_flag'] == 1 && $shopRow['is_enable'] == 1) || ($f && $shopRow['is_flag'] == 1 && $shopRow['is_enable'] == 0 && $f == 1)) {
			$flagRow = $coupon = array();
			
			if($shopRow['brand_id']) {
				//获取品牌图片
				$brand_logo = $this->_model->getBrandById($shopRow['brand_id'], 'brand_logo');
				$flagRow['brand_logo'] = $brand_logo ?  $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brand_logo : '';
			}
			//获取店铺关注人数
			$flagRow['favorite_num'] = $this->_model->getUserNumByShopId($sid);
			//判断当前登录用户是否已关注当前店铺
			if($this->_user_id) {
				$flagRow['is_favorite'] = $this->_model->hadFavoriteShop($sid, $this->_user_id);
			}
			//根据店铺ID获取所有相关推荐
			$flagRow['flagPicRecommend'] = $this->_model->getFlagShopAllRecommend($sid);			
			
			$f = $shopRow['is_enable'] == 1 ? 0 : $f;
			
			$coupon['shop_info'] = $shopRow;
			$this->_tpl->assign('shop_id', $sid);
			$this->_tpl->assign('order', $order);
			$this->_tpl->assign('coupon', $coupon);
			$this->_tpl->assign('region', $this->getRegion($coupon['shop_info']['region_id'], true, $this->_city));
			$this->_tpl->assign('circle', $this->getCircleByCircleId($coupon['shop_info']['circle_id'], true, $this->_city));
			$this->_tpl->assign('store', $this->getStore($coupon['shop_info']['store_id'], true, false, $this->_city));
			$this->_tpl->assign('flagRow', $flagRow);
			$this->_tpl->assign('f', $f);
			$this->_tpl->display('shop/flagShow.php');			
		} 
		//普通店
		else {		
			// 该店铺下的优惠券
			$coupon = $this->_model->getCoupon($sid);
	
			$this->_tpl->assign('coupon', $coupon);
			$this->_tpl->assign('region', $this->getRegion($coupon['shop_info']['region_id'], true, $this->_city));
			$this->_tpl->assign('circle', $this->getCircleByCircleId($coupon['shop_info']['circle_id'], true, $this->_city));
			$this->_tpl->assign('store', $this->getStore($coupon['shop_info']['store_id'], true, false, $this->_city));
			$this->_tpl->assign('shop_id', $sid);
			$this->_tpl->assign('order', $order);
			$this->_tpl->display('shop/show.php');
		}
	}
	
	/**
	 * 商品分类瀑布流展示
	 */
	public function ajaxAction() {
		$goodArray = $this->_model->getAjaxGoodList(
				intval($this->_http->get('sid')),
				intval($this->_http->get('page')),
				intval($this->_http->get('order'))
		);
		exit(json_encode($goodArray));
	}
	
	public function isVeriyAction() {
		$ty = $this->_http->get('ty');
		$ty = intval($ty);
		
		if($ty == 1) {
			$sid = intval($this->_http->get('sid'));
			$isShopApp = $this->_model->unique(1, $this->_user_id, $sid);
			if($isShopApp) {
				exit(json_encode(array('status' => 300, 'msg' => '你已经认领过本店铺，请耐心等待审核！')));
			}
		} elseif ($ty == 2) {
			if($this->_userInfo['user_type'] == 2) {
				exit(json_encode(array('status' => 200, 'msg' => '已经是商户啦')));
			}
			$isUserApp = $this->_model->unique(2, $this->_user_id);
			if($isUserApp) {
				exit(json_encode(array('status' => 300, 'msg' => '你已经申请过，正在审核中...，请耐心等待！')));
			}
		}
		exit(json_encode(array('status' => 100, 'msg' => '验证成功')));
	}

	/**
	 * 店铺认领  - 重复性 判断
	 */
	public function shopClaimAction() {
		$sid = intval($this->_http->get('sid'));
		if($sid) {
			if($this->_model->unique_app($sid, $this->_user_id)) {
				exit(json_encode(array('status' => 300, 'msg' => '你已经认领过本店铺，请耐心等待审核！')));
			}
			
			if($this->_model->unique_app($sid)) {
				exit(json_encode(array('status' => 400, 'msg' => '本店铺已经有人认领，请换一个店铺！')));
			}
			exit(json_encode(array('status' => 100, 'msg' => '')));
		}
	}
	
	public function addVeriyAction() {
		if($this->_http->isPost()) {
			$ty = intval($this->_http->get('ty'));
			
			$getData = $this->_http->getPost();		
			$snapArray = array();
			foreach ($_FILES['fileToUpload'] as $key => $item) {
				$snapArray[0][$key] = $item[0];
				$snapArray[1][$key] = $item[1];
			}			
			$id_img = Custom_Upload::singleImgUpload($snapArray[0], 'verify');
			$bus_img = Custom_Upload::singleImgUpload($snapArray[1], 'verify');
			$getData['id_img'] = $id_img;
			$getData['bus_img'] = $bus_img;
			
			$rs = $this->_model->setAudit($this->_userInfo, $getData);			
			if ($rs) {
				exit(json_encode(array('status' => 100, 'msg' => $ty == 1 ? '认领提交成功，请等待审核！' : '商户申请成功，请等待审核')));
			} else {
				exit(json_encode(array('status' => 300, 'msg' => '系统忙，请稍候再试' )));
			}
		}
		$type = intval($this->_http->get('type'));
		$sid = intval($this->_http->get('sid'));
		$this->_tpl->assign('type', $type);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->display('shop/add_veriy.php');
	}
	/**
	 * 启用旗舰店
	 */
	public function flagOpenAction() {
		$sid = intval($this->_http->get('sid'));
		if($sid && $this->_user_id) {
			$openResult = $this->_model->flagOpen($sid, $this->_user_id);
			if($openResult) {
				_exit('success', 100);
			}
		}		
		_exit('启用失败', 300);
	}
	/**
	 * 店铺收藏/关注
	 */
	public function favoriteAction() {
		$errorMsg = array();
		$sid = $this->_http->get('sid');
		if(!$this->_user_id) {
			$errorMsg = array('Code' => 200, 'Num' => 0, 'Message' => '必须登录才能添加关注！');
		} else {
			$affectNumber = $this->addShopFavorite($this->_user_id, $sid);
			$this->updateQuantityFavShopByUserId($this->_user_id);
			if ($affectNumber) {
				$errorMsg = array('Code' => 100, 'Num' => $affectNumber, 'Message' => '恭喜，你成功新增关注！');
			} else {
				$errorMsg = array('Code' => 300, 'Num' => 0, 'Message' => '抱歉，网络问题，请稍候再试！');
			}
		}
		exit(json_encode($errorMsg));
	}
	
	/**
	 * 店铺券，游惠列表
	 */
	public function shopTicketsAction(){
		$getData = $this->_http->getParams();
		$data = $this->_model->getShopTickets($getData);
		print_r($data);
	}
}