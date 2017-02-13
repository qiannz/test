<?php
class Controller_Home_Good extends Controller_Home_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Good::getInstance();
	}
	/**
	 * 商品分类列表
	 */
	public function listAction() {
		
		$sidArray = $this->_http->has('sid') ? explode('_', $this->_http->get('sid')) : array(0,0,0,0,0,0);
		
		$store_id 	= intval($sidArray[0]); 
		$brand_id 	= intval($sidArray[1]);
		$region_id 	= intval($sidArray[2]);
		$circle_id 	= intval($sidArray[3]);		
		$market_id 	= intval($sidArray[4]);
		$shop_id 	= intval($sidArray[5]);
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
			$circleList = $this->_model->getCircle($this->_city);
		}
		$this->_tpl->assign('circleList', $circleList);
		//获取推荐商场
		$marketList = $this->_model->getMarketCommend($region_id);
		$this->_tpl->assign('marketList', $marketList);
		//选中商场的情况下，获取商场对应的折扣信息
		if(!empty($market_id)) {
			$marketDiscountRow = $this->_model->getMarketDiscountMessage($market_id);
			$this->_tpl->assign('marketDiscountRow', $marketDiscountRow);
		}
		//获取对应店铺
		if($region_id && $circle_id) {
			$shopList = $this->getShop($region_id, $circle_id, $this->_city);
			$this->_tpl->assign('shopList', $shopList);
		}
		
		$this->_tpl->assign('store_id', $store_id);
		if($store_id) {
			$this->_tpl->assign('store', $this->getStore($store_id, true, false, $this->_city));
		}
		$this->_tpl->assign('brand_id', $brand_id);
		if($brand_id) {
			$this->_tpl->assign('brand', $this->getBrand($brand_id,false,true, $this->_city));
		}
		
		$this->_tpl->assign('region_id', $region_id);
		if($region_id) {
			$this->_tpl->assign('region', $this->getRegion($region_id, true, $this->_city));
		}
		$this->_tpl->assign('circle_id', $circle_id);
		if($circle_id) {
			$this->_tpl->assign('circle', $this->getCircleByCircleId($circle_id, true, $this->_city));
		}
		$this->_tpl->assign('shop_id', $shop_id);
		$this->_tpl->assign('market_id', $market_id);
		$this->_tpl->assign('order', $order);
		
		$this->_tpl->display('good/list.php');
	}
	/**
	 * 商品分类瀑布流展示
	 */
	public function ajaxAction() {		
		$goodArray = $this->_model->getAjaxGoodList(
			intval($this->_http->get('seid')),
			intval($this->_http->get('bdid')),
			intval($this->_http->get('rnid')),
			intval($this->_http->get('ceid')),
			intval($this->_http->get('mkid')),
			intval($this->_http->get('spid')),
			intval($this->_http->get('page')),
			intval($this->_http->get('order'))
		);
		
		exit(json_encode($goodArray));
	}
	/**
	 * 商品详情显示
	 */
	public function showAction() {
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);

		// 获取商品ID
		$gid = intval($this->_http->get('gid'));
		// 获取排序
		$order = $this->_http->get('order') ? intval($this->_http->get('order')) : 1;
		
		if (!$gid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		
		// 获取商品详情
		$goods = $this->_model->getGoods($gid, $order);
		
		if(empty($goods['img'])) Custom_Common::jumpto('/404/404.html');
		if($goods['is_del'] == 1) {
			Custom_Common::showMsg(
					'抱歉，你访问的商品已被删除！',
					'',
					array(
						'list' => '返回商品列表页'
					)
				);
		}
		// 商铺信息
		$shopInfo = $this->_model->getShop($goods['shop_id']);
		//热门商品
		$goodShowHotList = Model_Home_Index::getInstance()->getRecommendListByIdentifier('good_show_hot', $this->_city, 4);
		$this->_tpl->assign('goodShowHotList', $goodShowHotList);
		//猜你喜欢
		$goodShowGuessList = Model_Home_Index::getInstance()->getRecommendListByIdentifier('good_show_guess', $this->_city, 6);
		$this->_tpl->assign('goodShowGuessList', $goodShowGuessList);
		//点击数加1
		$this->addclick($gid);
		
		$this->_tpl->assign('goods', $goods);
		$this->_tpl->assign('shopInfo', $shopInfo);
		$this->_tpl->display('good/show.php');
	}
	/**
	 * 根据券ID 获取相关商品
	 */
	public function moreAction() {
		$tid = $this->_http->get('tid');
		$tid = intval($tid);
		
		if (!$tid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		$ticketRow = Model_Home_Ticket::getInstance()->getTicktRow($tid);
		$this->_tpl->assign('ticketRow', $ticketRow);
		
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->display('good/more.php');		
	}
	
	public function ajaxByTidAction() {
		$tid = intval($this->_http->get('tid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$goodTicketArray = Model_Home_Ticket::getInstance()->getGoodListByTicketId($tid, 0, $page);
		exit(json_encode($goodTicketArray));
	}
	
	public function uploadAction() {
		if($this->_http->isPost()){			
			$user_id = $this->getUserIdByUserName($this->_http->getPost('user_name'));
			$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'good';
			$primary_id = $this->_http->has('primary_id') ? intval($this->_http->get('primary_id')) : 0;
			$picStr = Custom_Upload::imageUpload($_FILES, $user_id, $folder, $primary_id);
			if($picStr){
				list($aid, $img_url) = explode('|', $picStr);
				$picArr = array(
					'error' => 0,
					'data' => array(
						array(
							'aid' => $aid,
							'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'].$img_url,
							'gid' => $primary_id
						)
					)
				);
				exit(json_encode($picArr));
			}
		}
	}
	
	public function addAction() {
		if(!$this->_user_id) {
			Custom_Common::jumpto('http://passport.mplife.com/login.aspx?sourceurl=' . HTTP_URI);
		}
		$this->_model->whether_to_allow(CLIENT_IP, $this->_userInfo);
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$getData['user_name'] = $this->_userInfo['user_name'];
			$getData['user_id'] = $this->_userInfo['user_id'];
			$errMsg = '';
			if(empty($getData['good_name'])) {
				$errMsg .= '商品标题不能为空'."\r\n";
			}
			
			if(!empty($getData['good_name']) && mb_strlen($getData['good_name'], 'utf8') > 30) {
				$errMsg .= '商品标题最多30个字符，汉字算一个字符'."\r\n";
			}
			
			if(empty($getData['dis_price'])) {
				$errMsg .= '商品现价不能为空'."\r\n";
			}
				
			if(!empty($getData['dis_price']) && !is_numeric($getData['dis_price'])) {
				$errMsg .= '商品现价要为整数'."\r\n";
			}
			
			if(empty($getData['region_id'])) {
				$errMsg .= '请选择店铺所在的区'."\r\n";
			}
				
			if(empty($getData['circle_id'])) {
				$errMsg .= '请选择店铺所在的商圈'."\r\n";
			}
			
			if(empty($getData['shop_id'])) {								
				if(empty($getData['shop_name'])) {
					$errMsg .= '请输入店铺名称'."\r\n";
				}
				
				if(!empty($getData['shop_name']) && Model_Home_Shop::getInstance()->repeatShop(Custom_String::HtmlReplace(trim($getData['shop_name']), -1))) {
					$errMsg .= '店铺名称重复'."\r\n";
				}
				
				if(empty($getData['address'])) {
					$errMsg .= '请输入店铺地址'."\r\n";
				}								
			}
				
			if(empty($getData['img'])) {
				$errMsg .= '商品照片不能为空'."\r\n";
			}
			
			if($errMsg == '') {
				if($this->_http->submitCheckRefresh()) {
					$shop_id = $this->_model->submitGood($getData, $this->_city);
					$this->updateUser(CLIENT_IP, $this->_userInfo['user_id']);
					$this->updateQuantityTotalGoodByUserId($this->_userInfo['user_id']);
					Custom_Common::showMsg(
						'恭喜，你的新商品添加成功！',
						'',
						array(
							'add' => '继续新增',
							'add/rid/'.intval($getData['region_id']).'/cid/'.intval($getData['circle_id']).'/sid/' . $shop_id => '继续在该店铺新增商品',
							'list' => '返回商品列表页'
						)
					);
				} else {
					Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/good/list');
				}
			}else {
				exit(nl2br($errMsg));
			}
		}
				
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		//热门商品
		$goodShowHotList = Model_Home_Index::getInstance()->getRecommendListByIdentifier('good_show_hot', 4);
		$this->_tpl->assign('goodShowHotList', $goodShowHotList);
		
		$this->_tpl->assign('formhash', $this->_http->formHashRefresh());
		
		$rid = intval($this->_http->get('rid'));		
		$cid = intval($this->_http->get('cid'));
		$sid = intval($this->_http->get('sid'));
		
		if($rid && $cid && $sid) {
			$this->_tpl->assign('rid', $rid);
			$this->_tpl->assign('cid', $cid);
			$this->_tpl->assign('sid', $sid);
			
			$regionArray = $this->getRegion(0, true, $this->_city);
			$circleArray = $this->getCircleByRegionId($rid, false, true, $this->_city);
			$shopArray = $this->getShop($rid, $cid, $this->_city);
			
			$this->_tpl->assign('regionArray', $regionArray);
			$this->_tpl->assign('circleArray', $circleArray);
			$this->_tpl->assign('shopArray', $shopArray);
		}
		
		$this->_tpl->display('good/upload.php');
	}
	/**
	 * 获取区域
	 */
	public function getRegionAction() {
		$regionListArray = array();
		$i = 0;
		$regionArray = $this->getRegion(0, true, $this->_city);
		foreach($regionArray as $key => $value) {
			$regionListArray[$i]['id'] = $key;
			$regionListArray[$i]['name'] = $value;
			$i++;
		}
		exit(json_encode($regionListArray));
	}
	
	public function getCircleAction() {
		$region_id = $this->_http->get('region_id');
		if($region_id) {
			exit(json_encode($this->getCircleByRegionId($region_id, false, true, $this->_city)));
		} else {
			exit(json_encode(array()));	
		}		
	}
	
	public function getShopAction() {
		$region_id = $this->_http->get('region_id');
		$circle_id = $this->_http->get('circle_id');
		exit(json_encode($this->getShop($region_id, $circle_id, $this->_city)));
	}
	
	public function delImgAction() {
		$aid = intval($this->_http->get('aid'));
		$user_id = $this->_db->fetchOne("select user_id from `oto_good_img` where `good_img_id` = '{$aid}' and `good_id` = '0' limit 1");
		if(!$user_id || $user_id != $this->_user_id) {
			echo json_encode(array('status' => ''));
			exit();
		}
		
		$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'good';
		if(Custom_Upload::imageDelete($aid, $folder)) {
			echo json_encode(array('status' => 'ok'));
			exit();
		}
	}
	public function setCoverAction() {
		$aid = intval($this->_http->get('aid'));
		$gid = intval($this->_http->get('gid'));
		if($this->_model->setCover($aid, $gid)) {
			echo json_encode(array('status' => 'ok'));
		}
	}
	

	public function checkShopNameAction() {
		$shop_name = $this->_http->getQuery('sname');
		$shop_name = Custom_String::HtmlReplace(trim($shop_name), -1);
		if(Model_Home_Shop::getInstance()->repeatShop($shop_name)) {
			_exit('店铺名称重复', 300);
		} else {
			_exit('店铺名称输入正确', 100);
		}
	}


    /**
     * wap页面
     */

    public function wapAction(){
        $gid = intval($this->_http->get('gid'));
        if (!$gid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
        
        // 获取商品详情
        $goods = $this->_model->getGoods($gid, 1, false, false, false);
        
        if(empty($goods['img'])) Custom_Common::jumpto('/404/404.html');
        if($goods['is_del'] == 1) {
        	Custom_Common::showMsg(
        	'抱歉，你访问的商品已被删除！',
        	'',
        	array(
        	'list' => '返回商品列表页'
        			)
        	);
        }
        
        // 商铺信息
        $shopInfo = $this->_model->getShop($goods['shop_id']);
        
        $goods['user'] = $this->getWebUserId($goods['user_name']);
       	
        $this->_tpl->assign('goods',$goods);
        $this->_tpl->assign('shopInfo', $shopInfo);
        $this->_tpl->display('good/wap.php');
    }
}