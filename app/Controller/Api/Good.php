<?php 
class Controller_Api_Good extends Controller_Api_Abstract {
	
	private $width;
	private $height;
	private $_model;
	
	public function __construct() {
		parent::__construct();
		//图片宽度限制
		$this->width = array(240, 280 , 400 , 640 , 160 , 740,200);
		//图片高度限制
		$this->height = array(240, 300, 400, 480 , 160 , 740);
		
		$this->_model = Model_Api_Good::getInstance();
	}
	/**
	 * 图片自动缩略（当前只支持商城商品）
	 */
	public function getImgThumbFirstAction() {
		//商品ID
		$tid = intval($this->_http->get('tid'));
		//图片宽度
		$w = intval($this->_http->get('w'));
		//图片高度
		$h = intval($this->_http->get('h'));
		
		//图片不满足宽度或者高度要求，则显示默认图片
		if(!in_array($w, $this->width) || ($h && !in_array($h, $this->height))) {
			exit(json_encode($this->returnArr(0, array(), 500, '图片尺寸限制错误')));
		}
	
		$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($tid);
		$wapImgRow = current($wapImgData);
		if(empty($wapImgRow)) {
			exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
		}
		
		//图片生成
		$this->_model->imgThumb($wapImgRow['img_url'], 'commodity', $w, $h);
	}
	/**
	 * 图片自动缩略（通用）
	 */
	public function getImgThumbAction() {
		//wap图片ID
		$iid = intval($this->_http->get('iid'));
		//富文本编辑框里面的图片id
		$id = intval($this->_http->get('id'));
		//图片宽度
		$w = intval($this->_http->get('w'));
		//图片高度
		$h = intval($this->_http->get('h'));
		//商品类型：现金券（voucher），团购（buygood）， 自定义买单 （selfpay），商城商品（commodity），一元众筹（crowdfunding），快来抢（spike）
		$type = $this->_http->get('type');
		if( !$iid && !$id ){
			exit(json_encode($this->returnArr(0, array(), 101, '图片ID不能为空')));
		}
		if(!in_array($type, array('ticket','coupon', 'voucher', 'buygood', 'selfpay', 'commodity', 'crowdfunding', 'spike', 'discount','special','good'))) {
			exit(json_encode($this->returnArr(0, array(), 101, '商品类型错误')));
		}
		
		switch ($type) {
			case 'ticket':
				$folder = 'ticket';
				break;
			case 'commodity':
				$folder = 'commodity';
				break;
			case 'discount':
				$folder = 'discount';
				break;
			case 'special':
				$folder = 'special';
				break;
			case 'good':
				$folder = 'good/original';
				break;
			case 'crowdfunding':
				$folder = 'crowdfunding';
				break;
			default:
				$folder = 'ticketwap';
				break;
		}
		
		//图片不满足宽度或者高度要求，则显示默认图片
		if(!in_array($w, $this->width) || ($h && !in_array($h, $this->height))) {
			exit(json_encode($this->returnArr(0, array(), 500, '图片尺寸限制错误')));
		}
		
		if( in_array($type, array('ticket','coupon', 'voucher', 'buygood', 'selfpay', 'commodity', 'crowdfunding', 'spike')) ) {
			if( $iid ){
				$wapImgRow = $this->select("`id` = '{$iid}'", 'oto_ticket_wap_img', '*', '', true);
			}else{
				$wapImgRow = $this->select("`id` = '{$id}'", 'oto_ticket_img', '*', '', true);
			}
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		} elseif( $type == 'discount') {
			if( $iid ) {
				$wapImgRow = $this->select("`id` = '{$iid}'", 'discount_wap_img', '*', '', true);
			} else {
				$wapImgRow = $this->select("`id` = '{$id}'", 'discount_img', '*', '', true);
			}
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		}elseif( $type == 'special') {
			if( $iid ){
				$wapImgRow = $this->select("`id` = '{$iid}'", 'special_wap_img', '*', '', true);
			}else{
				$wapImgRow = $this->select("`id` = '{$id}'", 'special_img', '*', '', true);
			}
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		}elseif( $type == 'good') {
			$wapImgRow = $this->select("`good_img_id` = '{$iid}'", 'oto_good_img', '*', '', true);
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		}
	
		//图片生成
		$this->_model->imgThumb($wapImgRow['img_url'], $folder, $w, $h);
	}
	
	/**
	 * 图片自动缩略（通用）
	 */
	public function getSpecialImgThumbAction() {
		$wapImgRow = array();
		//图片ID
		$iid = intval($this->_http->get('iid'));
		//图片宽度
		$w = intval($this->_http->get('w'));
		//图片高度
		$h = intval($this->_http->get('h'));
		//商品类型：专题（special）
		$type = $this->_http->get('type');
		
		if(!in_array($type, array('ticket', 'special', 'spike','discount'))) {
			exit(json_encode($this->returnArr(0, array(), 101, '商品类型错误')));
		}
	
		switch ($type) {
			case 'ticket':
				$folder = 'ticket';
				break;
			case 'special':
				$folder = 'special';
				break;
			case 'spike':
				$folder = 'spike';
				break;
			case 'discount':
				$folder = 'discount';
				break;
		}
		//图片不满足宽度或者高度要求，则显示默认图片
		if(!in_array($w, $this->width) || ($h && !in_array($h, $this->height))) {
			exit(json_encode($this->returnArr(0, array(), 500, '图片尺寸限制错误')));
		}
		//现金券，商城商品
		if( $type == 'ticket') {
			$wapImgRow = $this->select("`id` = '{$iid}'", 'oto_ticket_img', '*', '', true);
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		}
		//专题
		elseif( $type == 'special') {
			$wapImgRow = $this->select("`id` = '{$iid}'", 'special_img', '*', '', true);
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		}
		//折扣
		elseif( $type == 'discount' ){
			$wapImgRow = $this->select("`id` = '{$iid}'", 'discount_img', '*', '', true);
			if(empty($wapImgRow)) {
				exit(json_encode($this->returnArr(0, array(), 501, '图片不存在')));
			}
		}
		
		if($wapImgRow && $folder) {
			//图片生成
			$this->_model->specialImgThumb($wapImgRow['img_url'], $folder, $w, $h);
		}
	}
	
	/**
	 * 获取 现金券|团购|商城商品|一元众筹|快来抢，WAP详情描述和图片
	 */
	public function getTicketWapInfoAction() {
		$data = array();
		//商品ID
		$tid = intval($this->_http->get('tid'));
		//商品详情
		$goodRow = Model_Home_Ticket::getInstance()->getTicketRow($tid);
		
		if(empty($goodRow)) {
			exit(json_encode($this->returnArr(0, array(), 102, '商品ID错误')));
		}
		//商品类型：现金券（voucher），团购（buygood）， 自定义买单 （selfpay），商城商品（commodity）
		$type = $this->getTicketSortById($goodRow['ticket_type'], 'ticketsort', 'sort_detail_mark');
		if(!in_array($type, array('coupon', 'voucher', 'buygood', 'selfpay', 'commodity', 'crowdfunding', 'spike'))) {
			exit(json_encode($this->returnArr(0, array(), 101, '商品类型错误')));
		}
		
		$data['content'] = $goodRow['wap_content'];
		$data['cover_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $goodRow['cover_img'];
		//商品图片
		$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($tid);
		if($wapImgData) {
			foreach ($wapImgData as $wapImgItem) {
				$data['imgList'][] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/api/good/get-img-thumb/iid/{$wapImgItem['id']}/w/640/type/{$type}";
			}
		}
		
		$shopInfo = Model_Home_Good::getInstance()->getShop($goodRow['shop_id']);
		$shopList = Model_Home_Ticket::getInstance()->getAssociatedShops($tid, $shopInfo);
		$data['shopList'] = $shopList;
		
		exit(json_encode($this->returnArr(0, $data)));
	}
	
	public function getShopInfoAction() {
		//店铺ID
		$sid = intval($this->_http->get('sid'));
		if($sid) {
			$shopInfo = Model_Home_Good::getInstance()->getShop($sid);
			exit(json_encode($this->returnArr(0, $shopInfo)));
		}	
	}
	
	public function getWeixinKeyAction() {
		$url = $this->_http->get('request_url');
		if(!$url) {
			_sexit('fail', 300);
		}
		
		$weixinKeyArr = Model_Active_Oneyuanpurchase::getInstance()->getWeixinKey($url);
		_sexit('sucess', 100, $weixinKeyArr);
	}
}