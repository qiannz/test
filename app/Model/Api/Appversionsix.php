<?php
class Model_Api_Appversionsix extends Base
{
	private static $_instance;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_key = Core_Router::getModule() . '_' . Core_Router::getController(). '_' . Core_Router::getAction() . '_';
	}
	
	/**
	 * 首页
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getHomeList( $getData , $city ){
		$data =array();
		$data["unread_message_number"] = '0';
		if ( $getData['uuid'] ){
			$userInfo = $this->getWebUserId($getData['uuid']);
			$data["unread_message_number"] = Model_Api_Message::getInstance()->getMyPersionUnReadMessageNum($userInfo);
		}
		//推荐广告
		$data["app_home_six_banner"]   = Model_Api_App::getInstance()->getListByMark($city, 'app_home_version_six', 'app_home_six_banner' , false);
		//大图标
		$app_home_six_icon = Model_Api_App::getInstance()->getListByMark($city, 'app_home_version_six', 'app_home_six_icon', 4);
		$hour = date('G');
		$i = 0;
		foreach($app_home_six_icon as & $six_icon_row) {
			$i++;
			$six_icon_row['img_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . "/images/congra/{$hour}/{$i}.png";
		}		
		$data["app_home_six_icon"]     = $app_home_six_icon;
		$activity = array();
		//一元众筹
		$oneyuanpurchase = Model_Api_App::getInstance()->getListByMark($city, 'app_home_version_six', 'app_home_six_oneyuanpurchase', 1);
		if(!empty($oneyuanpurchase)){$activity[] = $oneyuanpurchase[0];}
		//快来抢
		$comeandgrap = Model_Api_App::getInstance()->getListByMark($city, 'app_home_version_six', 'app_home_six_comeandgrap', 1);
		if(!empty($comeandgrap)){$activity[] = $comeandgrap[0];}
		//拍卖会
		$auction = Model_Api_App::getInstance()->getListByMark($city, 'app_home_version_six', 'app_home_six_auction', 1);
		if(!empty($auction)){$activity[] = $auction[0];}
		//活动（一元众筹，快来抢，拍卖会）
		$data["app_home_six_activity"] = $activity;
		//达人说
		$data["app_home_six_daren"]    = Model_Api_App::getInstance()->getListByMark($city, 'app_home_version_six', 'app_home_six_daren', 2);
		$getData["page"] = 1;
		//猜你喜欢
		$data["app_home_six_love"]     = $this->getLoveList( $getData , $city );
		//折扣由近到远
		$data["app_home_six_discount"] = Model_Api_Discount::getInstance()->getDiscounts(array("lat"=>$getData["lat"],
																								"lng"=>$getData["lng"],
																								"page"=>1,
																						 		), $city, 10);
		return $data;
	}
	
	/**
	 * 获取首页活动信息
	 * @param unknown_type $city
	 */
	public function getHomeActivity( $city ){
		$largeImgRow = getimagesize(ROOT_PATH . 'web/images/activity/oneyuanpurchase_large.jpg');
		$middleImgRow = getimagesize(ROOT_PATH . 'web/images/activity/oneyuanpurchase_middle.jpg');
		$smallImgRow = getimagesize(ROOT_PATH . 'web/images/activity/oneyuanpurchase_small.jpg');
		$activity =array(
					  	array(
					  		'img' => array(
						  				'large' => array('img_url'=>$GLOBALS['GLOBAL_CONF']['SITE_URL'].'/images/activity/oneyuanpurchase_large.jpg',
						  								 'width'=>$largeImgRow[0],
						  						   		 'height'=>$largeImgRow[1]
						  							),
						  				'middle'=> array('img_url'=>$GLOBALS['GLOBAL_CONF']['SITE_URL'].'/images/activity/oneyuanpurchase_middle.jpg',
						  								 'width'=>$middleImgRow[0],
						  						         'height'=>$middleImgRow[1]
						  							),
						  				'small' => array('img_url'=>$GLOBALS['GLOBAL_CONF']['SITE_URL'].'/images/activity/oneyuanpurchase_small.jpg',
						  						         'width'=>$smallImgRow[0],
						  						         'height'=>$smallImgRow[1]
						  							),
					  				),
					  		'title'=>'1元购',
					  		'summary'=>'把握机会1元精选',
					  		'www_url'=>$GLOBALS['GLOBAL_CONF']['SITE_URL'].'/active/oneyuanpurchase',
					  		'timeout'=>'',
					  		'type'=>'one_yuan_purchase',	
					  	)
					);
		//$recommend = $this->getRecommandMore("app_home_six_activity_recommend", "voucher", 2 , 1 , $city );
		$data = array(
						'activity_num'=>count($activity),
						'activity_list'=>$activity,
				      	'recommend'=>array());
		return $data;
	}
	
	/**
	 * 猜你喜欢列表//券 1、推荐的图 2、现金券头图 6个 //商品 1、推荐的图 2、找店铺对应的品牌的头图商品的图 4个
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getLoveList( $getData , $city ){
		$page = intval($getData["page"]);
		if( $page < 1 ){
			$page = 1;
		}
		$recVoucher = $this->getRecommandMore("app_home_six_love", "voucher", 6 , $page , $city );
		$recBuygood = $this->getRecommandMore("app_home_six_love", "commodity", 4 , $page , $city );
		$recOneYuanPurchase = array();
		$recComeAndGrap = array();
		if( $page == 1 ){
			$recOneYuanPurchase = $this->getRecommandMore("app_home_six_love", "crowdfunding" , 0 , 0 , $city);
			$recComeAndGrap = $this->getRecommandMore("app_home_six_love", "spike" , 0 , 0 , $city);
		}
		$data = array_merge($recVoucher , $recBuygood , $recOneYuanPurchase , $recComeAndGrap );
		if(!empty($data)) shuffle($data);
		return $data;
	}
	
	/**
	 * 获取推荐的现金券或商城商品
	 * @param unknown_type $mark
	 * @param unknown_type $sort_detail_mark
	 * @param unknown_type $pageSize
	 * @param unknown_type $page
	 * @param unknown_type $city
	 */
	public function getRecommandMore( $mark , $sort_detail_mark , $pageSize , $page = 1, $city = 'sh' ){
		$ticketType = Model_Api_App::getInstance()->getTicketSortById(0,'ticketsort',$sort_detail_mark);
		$time = REQUEST_TIME;
		$pos_id = 	Model_Api_App::getInstance()->getPosIdByMark($city, 'app_home_version_six', $mark);
		
		$where = " and `B`.`is_auth` = '1' and `B`.`is_show` = '1' and `B`.`ticket_status` = '1'";
		if( "voucher" == $sort_detail_mark ){
			$where .= " and `B`.`start_time` < '{$time}' and `B`.`end_time` > '{$time}'";
		}else if( "crowdfunding" == $sort_detail_mark || "commodity" == $sort_detail_mark || "spike" == $sort_detail_mark ){
			$where .= " and `B`.`end_time` > '{$time}'";
		}
		
		$sql = "select `A`.*, `B`.`ticket_type`, `B`.`par_value`, `B`.`selling_price`,`B`.`end_time`,`B`.`ticket_id`,`B`.`ticket_uuid`,`B`.`brand_id`,`B`.`cover_img`
				from `oto_recommend` as `A`
				left join `oto_ticket` as `B` on `A`.`come_from_id` = `B`.`ticket_id`
				where `A`.`pos_id`='{$pos_id}' and `B`.`ticket_type`='{$ticketType}' {$where} 
				order by `A`.`sequence` asc, `A`.`created` desc";
		if( "crowdfunding" == $sort_detail_mark || "spike" == $sort_detail_mark ){
			$data = $this->_db->fetchAll($sql);
		}else{
			$start = ($page - 1) * $pageSize;
			$data = $this->_db->limitQuery($sql, $start, $pageSize);
		}
		
		foreach ($data as &$row){
			if($row['img_url']) {
				$img_tmp = $row['img_url'];
				$www_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $img_tmp;
				$dir_url = ROOT_PATH . 'web/data/recommend/' . $img_tmp;
			}else if( "voucher" == $sort_detail_mark || "crowdfunding" == $sort_detail_mark || "spike" == $sort_detail_mark ){
				$www_url = $row['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $row['cover_img'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/app/default_ticket.png';
				$dir_url = $row['cover_img'] ? ROOT_PATH . 'web/data/cover/' . $row['cover_img'] : ROOT_PATH . 'web/data/app/default_ticket.png';
			}else if( "commodity" == $sort_detail_mark ){
				$brandInfo = $this->_db->fetchRow("select * from oto_brand where brand_id = '{$row['brand_id']}'");
				$www_url = $brandInfo['brand_figure'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandInfo['brand_figure'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/wap/img_brand_default.png';
				$dir_url = $brandInfo['brand_figure'] ? ROOT_PATH . 'web/data/brand/' . $brandInfo['brand_figure'] : ROOT_PATH . 'web/images/wap/img_brand_default.png' ;
			}
			unset( $row['cover_img'] );
			list($row["width"],$row["height"]) = getimagesize($dir_url);
			$row["img_url"] = $www_url;
		}
		return $data;
	}
	/**
	 * 达人说列表
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getDarenList($getData, $city, $page_size = PAGESIZE) {
		$data = array();
		$page = !$getData['page'] || intval($getData['page']) < 1 ? 1 : intval($getData['page']);
		$start = ($page - 1) * $page_size;
		$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, 'app_home_version_six', 'app_home_six_daren');
		$sql = "select * from `oto_recommend` where `pos_id` = '{$pos_id}' order by sequence asc, created desc";
		$data = $this->_db->limitQuery($sql, $start, $page_size);
		foreach($data as & $row) {
			if($row['img_url']) {
				$img_tmp = $row['img_url'];
				$row['img_url'] =  $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $img_tmp;
				list($row['width'], $row['height']) = getimagesize(ROOT_PATH . 'web/data/recommend/' . $img_tmp);
			} else {
				$row['img_url'] = '';
				$row['width'] = $row['height'] = 0;
			}
		}
		return $data ? $data : array();		
	}
}