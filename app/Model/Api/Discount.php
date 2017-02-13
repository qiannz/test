<?php
class Model_Api_Discount extends Base
{
	private $_key;
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
	
	public function getSearchFilter($getData, $city = 'sh', $is_cache = false) {
		$name = !$getData['name'] ? '' :  $getData["name"];
		$pmark = !$getData['pmark'] ? '' : $getData['pmark'];
		$cmark = !$getData['cmark'] ? '' : $getData['cmark'];
		$key = $this->_key . $city . '_' . $name .'_' . $getData['type'] . 
			  ($pmark?'_'.$pmark:'').($cmark?'_'.$cmark:'');
		$data = $this->getData($key);
		if(!$is_cache || empty($data)) {
			$where = " and `city` = '{$city}'";
			switch ($getData['type']) {
				case 'category' :
					$data = $this->getDiscountCategory($getData);					
					break;
				case 'brand':
					if($pmark && $cmark) {
						$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, $pmark, $cmark);
						$sql = "select B.brand_id as id,B.brand_name_zh,B.brand_name_en
								from `oto_recommend` A
								left join `oto_brand` B on A.come_from_id = B.brand_id
								where A.`pos_id` = '{$pos_id}' and A.`come_from_type` = '3' and A.`city` = '{$city}'
								order by A.sequence asc, A.created desc";
						$data = $this->_db->fetchAll($sql);
						foreach ($data as & $row) {
							$row['name'] = $row['brand_name_zh'] ? $row['brand_name_zh'] : $row['brand_name_en'];
							unset($row['brand_name_zh'], $row['brand_name_en']);
						}
					} else {
						$brandList = $this->getBrand(0,FALSE);
						$data = array();
						foreach ( $brandList as $key => $val ){
							$data[] = array("id"=>$key,"name"=>$val);
						}
					}
					break;
				case 'circle':
					if($pmark && $cmark) {
						$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, $pmark, $cmark);
						$sql = "select B.circle_id AS id,B.circle_name AS name
						from `oto_recommend` A
						left join `oto_circle` B on A.come_from_id = B.circle_id
						where A.`pos_id` = '{$pos_id}' and A.`come_from_type` = '7' and A.`city` = '{$city}'
						order by A.sequence asc, A.created desc";
					} else {
						if( $name ){
							$where .= " AND `circle_name` LIKE '%{$name}%' ";
						}
						$sql = "select circle_id AS id,circle_name AS name from oto_circle WHERE 1 {$where} order by sequence asc, created desc";
					}
					$data = $this->_db->fetchAll($sql);
					break;
				case 'market':
					if($pmark && $cmark) {
						$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, $pmark, $cmark);
						$sql = "select B.`market_id` AS id , B.`market_name` AS name
						from `oto_recommend` A
						left join `oto_market` B on A.come_from_id = B.market_id
						where A.`pos_id` = '{$pos_id}' and A.`come_from_type` = '4' and A.`city` = '{$city}'
						order by A.sequence asc, A.created desc";
					} else {
						if( $name ){
							$where .= " AND `market_name` LIKE '%{$name}%' ";
						}
						$sql = 'SELECT `market_id` AS id, `market_name` AS name FROM `oto_market` WHERE 1 '.$where.' ORDER BY `sequence` ASC ';
					}
					$data = $this->_db->fetchAll($sql);
					break;
				case 'discount_strength':
					//折扣，0：不限 ；1：1-4折；2：4-5折；3：5折以上 ；4：新品上市
					$data = array(
						array("id"=>1,"name"=>"1-4折"),
						array("id"=>2,"name"=>"4-5折"),
						array("id"=>3,"name"=>"5折以上"),
						array("id"=>4,"name"=>"新品上市")							
					);
					break;
			}
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 折扣分类
	 */
	public function getDiscountCategory( $getData ){
		$categoryArray  = $this->getTicketSortById( 0 , 'discountcategory' );
		$categoryArray = array_values($categoryArray);
		$data = array();
		$name = strtolower($getData["name"]);
		$newData = array();
		foreach ( $categoryArray as &$row ){
			if( $name && FALSE === strpos(strtolower($row["sort_detail_name"]),$name) ){
				continue;
			}
			$data[] = array("id"=>$row["sort_detail_id"],"name"=>$row["sort_detail_name"]);
		}
		return $data;
	}

	/**
	 * 获取首页
	 * @param array $getData 请求参数
	 * @param string $city 城市
	 * @param bool $is_cache 是否缓存
	 */
	public function getMain( $getData , $city , $pageSize , $is_cache = false ){
		$key = $this->_key . $city . "_{$getData['lat']}_{$getData['lng']}_{$getData['uuid']}";
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$data["discount_banner"] = Model_Api_App::getInstance()->getListByMark($city, 'discount', 'discount_banner', 3 );
			$data["discount_list"] = $this->getNearbyMore(array(
							'page' => 1,
							'lng' => $getData['lng'],
							'lat' => $getData['lat'],
							'uuid' => empty($getData['uuid'])?'':$getData['uuid'],
							'uname'=> empty($getData['uname'])?'':$getData['uname']
						), $city , $pageSize );
			$this->setData($key, $data);
		}
		return $data;
	}
	
	/**
	 * 获取折扣推荐位内容
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function getRecmmendDiscount( $getData , $city ){
		$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, 'discount', 'discount_recommend');
		
		$sql = "SELECT C.`discount_id`,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime` ,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address` ".
				"FROM `oto_recommend` R ".
				"LEFT JOIN `discount_content` C ON C.`discount_id` = R.`come_from_id` ".
				"WHERE R.`pos_id` = {$pos_id} AND C.city = '{$city}' AND C.`is_del` = 0 AND C.`discount_status` >= 0  AND C.`etime` > ".REQUEST_TIME.
				" LIMIT 3";
		$discount_recommends = $this->_db->fetchAll($sql);
		
		$ids = '';
		foreach ( $discount_recommends as $key => $row ){
			$row["is_main"] = 0;//显示发布用户信息
			$discount_recommends[$key] = $this->getDiscountRow($row, $getData["lat"], $getData["lng"] , 0 ,true);
			$ids .= $row["discount_id"].',';
		}
		$ids = substr($ids, 0 , -1);
		return array($discount_recommends , $ids);
	}
	
	/**
	 * 解析折扣相关参数
	 * @param unknown_type $discount
	 */
	public function parseDiscountInfo( $row , $lat = 0 , $lng = 0 ){
		if( array_key_exists("distance", $row) ){
			$row["distance"] = floor($row["distance"]);
		}else{
			$row["distance"] = -1;//用户没有传经纬度(附近关注)
		}
		if( $lat>0 && $lng>0 && $row["pub_lat"] >0 && $row["pub_lng"]>0 ){
			if( !array_key_exists("pub_distance", $row)  ){
				$row["pub_distance"] = getDistance($lat, $lng, $row["pub_lat"], $row["pub_lng"]);
			}
		}else{
			$row["pub_distance"] = -1;//没有经纬度
		}
		$row["is_start"] = array("status"=>0,"msg"=>"未开始");
		if( $row["etime"] <= REQUEST_TIME ){
			$row["is_start"] = array("status"=>-1,"msg"=>"已结束");
		}else if( $row["stime"]>REQUEST_TIME && $row["stime"]<REQUEST_TIME + 24*60*60 ){//即将开始
			$row["is_start"] = array("status"=>1,"msg"=>"即将开始");
		}else if($row["stime"]<=REQUEST_TIME){//进行中
			$row["is_start"] = array("status"=>2,"msg"=>"进行中");
		}
		$row["user_name"] = "";
		$row["uuid"] = "";
		if( !empty($row["user_id"]) ){
			$userRow = $this->getUserByUserId($row["user_id"] , '`uuid`,`user_name`');
			if( !empty($userRow) ){
				$row["uuid"] = $userRow["uuid"];
				$row["user_name"] = $userRow["user_name"];
			}
		}else if( !empty($row["pub_uid"]) ){
			$userRow = $this->getUserByUserId($row["pub_uid"] , '`uuid`,`user_name`');
			if( !empty($userRow) ){
				$row["uuid"] = $userRow["uuid"];
				$row["user_name"] = $userRow ["user_name"];
			}
			$row["user_id"] = $row["pub_uid"];
		}
		$row ["avatar"] = ""; // 默认头像
		if ($row ["uuid"]) {
			$avatarRow = $this->getUserAvatarByUuid ( $row ["uuid"] );
			if (! empty ( $avatarRow ) && $avatarRow ['Avatar50']) {
				$row ["avatar"] = $avatarRow ['Avatar50'];
			}
		}
		$row["format_date"] = Custom_Time::getTime2 ( $row ["created"] );
		$wapImgList = array();
		$iids = 'iid';
		$wapImgList = $this->select ( "`discount_id` = '{$row["discount_id"]}'", 'discount_wap_img', '`id`,`img_url`,`height`,`width`,`filesize`', 'sequence asc, created asc' );
		if(empty($wapImgList)) {
			$iids = 'id';
			$wapImgList = $this->select ( "`discount_id` = '{$row["discount_id"]}'", 'discount_img', '*', 'sequence asc, created asc' );
		}
		$wapImgArr = array ();
		$i = 0;
		$originalWapImgArr = array();
		foreach ( $wapImgList as $rowWapImg ) {
			$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight($rowWapImg['img_url'], 'discount');
			if( empty($widthHeightRow['width']) || empty($widthHeightRow['height']) ){
				continue;
			}
			$originalWapImgArr[] = array(
					'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/discount/" . $rowWapImg ['img_url'],
					'width' => $widthHeightRow['width'],
					'height' => $widthHeightRow['height']
			);
			$widthHeightRow640 = Model_Api_App::getInstance ()->getImageWidthHeight ( $rowWapImg ['img_url'], 'discount', 640 );
			$wapImgArr[$i]['W640'] = array (
					'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/640/type/discount",
					'width' => empty($widthHeightRow640 ['width'])?0:$widthHeightRow640 ['width'],
					'height' => empty($widthHeightRow640 ['height'])?0:$widthHeightRow640 ['height']
			);
			$widthHeightRow400 = Model_Api_App::getInstance ()->getImageWidthHeight ( $rowWapImg ['img_url'], 'discount', 400 );
			$wapImgArr[$i]['W400'] = array (
					'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/400/type/discount",
					'width' => empty($widthHeightRow400 ['width'])?0:$widthHeightRow400 ['width'],
					'height' => empty($widthHeightRow400 ['height'])?0:$widthHeightRow400 ['height']
			);
			$widthHeightRow240 = Model_Api_App::getInstance ()->getImageWidthHeight ( $rowWapImg ['img_url'], 'discount', 240 );
			$wapImgArr[$i]['W240'] = array (
					'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/240/type/discount",
					'width' => empty($widthHeightRow240 ['width'])?0:$widthHeightRow240 ['width'],
					'height' => empty($widthHeightRow240 ['height'])?0:$widthHeightRow240 ['height']
			);
			$i++;
		}
		$row["thumb_wap_img"] = empty ( $wapImgArr ) ? array () : $wapImgArr;
		$row["original_wap_img"] = empty ( $originalWapImgArr ) ? array () : $originalWapImgArr;
		return $row;
	}
	
	/**
	 * 获取附近关注
	 * 
	 * @param unknown_type $getData
	 *        	请求参数
	 */
	public function getNearbyMore($getData, $city, $pageSize, $is_cache = false) {
		$page = intval($getData['page']);
		if( $page < 1 ){
			$page = 1;
		}
		$lng = $getData['lng'];
		$lat = $getData['lat'];
		$des = !$getData['des'] ? 'nearby' : $getData['des'];
		if( !in_array( $des, array('nearby','hot','stime_asc','stime_desc','etime_asc','etime_desc' ) ) ) {
			$des = 'nearby';
		}
		$user_id = 0;
		if( !empty($getData['uuid']) && !empty($getData['uname']) ){
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
		}
		$key = "{$this->_key}_{$city}_{$user_id}_{$lat}_{$lng}_{$des}_{$page}";
		$data = $this->getData( $key );
		if (!$is_cache || empty($data)) {
			$start = ($page - 1) * $pageSize;
			$dids = '';
			$recDiscounts = array ();
			if (1 == $page) {
				list( $recDiscounts, $dids ) = $this->getRecmmendDiscount( $getData, $city );
			}
			$where = $order = '';
			$where = " WHERE `city` = '{$city}' AND `is_del` = 0 AND `discount_status` >= 0 AND `etime` > " . REQUEST_TIME;
			switch ($des) {
				case 'nearby' :
					if ($lat>0 && $lng>0) {
						$order = ' C.`distance` ASC , C.`created` DESC ';
					} else {
						$order = ' C.`star` DESC, C.`created` DESC ';
					}
					break;
				case 'hot' :
					$order = ' B.`concern_number` DESC ';
					break;
				case 'stime_asc' :
					$order = ' C.`stime` ASC ';
					break;
				case 'stime_desc' :
					$order = ' C.`stime` DESC ';
					break;
				case 'etime_asc' :
					$order = ' C.`etime` ASC ';
					break;
				case 'etime_desc' :
					$order = ' C.`etime` DESC ';
					break;
			}
			if( $lat>0 && $lng>0 ) {
				if ($dids) {
					$where .= " AND `discount_id` NOT IN ({$dids}) ";
				}
				if( $user_id > 0 ){
					$where .= " AND `user_id`<>'{$user_id}' ";
				}
				if( $des == "hot" ){//按关注数排序，需要连接discount_concerned_number表进行处理
					$sql = "SELECT C.`id`,C.`user_id`,C.`lng`,C.`lat`,C.distance,C.`discount_id`,C.`created`,B.`concern_number` 
							FROM (
								SELECT * FROM (
									SELECT *,12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as `distance`
									FROM `discount_visit`
									{$where} ORDER BY distance ASC , created DESC
									) AS D GROUP BY D.discount_id
								) AS C
							LEFT JOIN `discount_concerned_number` AS B ON B.discount_id = C.discount_id
							ORDER BY {$order}";	
				}else{
					$sql = "SELECT C.`id`,C.`user_id`,C.`lng`,C.`lat`,C.distance,C.`discount_id`,C.`created` 
							FROM (
								SELECT *,12756274*asin(Sqrt(power(sin(({$lat}-lat)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(lat*0.0174533)*power(sin(({$lng}-lng)*0.008726646),2))) as `distance` 
								FROM `discount_visit` 
								{$where} ORDER BY distance ASC , created DESC
							) AS C GROUP BY C.discount_id
							ORDER BY {$order}";
				}
				$discountArr = $this->_db->limitQuery ( $sql, $start, $pageSize );
				foreach( $discountArr as $key => $row ) {
					$row["is_main"] = 1; // 关注的用户
					$discountArr[$key] = $this->getDiscountRow($row, $lat, $lng , 1 ,true);
				}
			} else {
				if ($dids) {
					$where .= " AND C.`discount_id` NOT IN ({$dids}) ";
				}
				if( $des == "hot" ){//按关注数排序，需要连接discount_concerned_number表进行处理
					$sql = "SELECT C.`discount_id`,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime` ,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address`,B.`concern_number` 
							FROM `discount_content` AS C
							LEFT JOIN `discount_concerned_number` AS B ON B.discount_id = C.discount_id
							{$where}
							ORDER BY {$order}";
				}else{
					$sql = "SELECT C.`discount_id`,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime` ,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address` 
							FROM `discount_content` AS C 
							{$where} 
							ORDER BY {$order}";
				}
				$discountArr = $this->_db->limitQuery ( $sql, $start, $pageSize );
				foreach( $discountArr as $key => $row ) {
					$row["is_main"] = 0; // 发布的用户
					$discountArr[$key] = $this->getDiscountRow($row, $lat, $lng , 0 ,true);
				}
			}
			$data = array_merge( $recDiscounts, $discountArr );
			$this->setData( $key, $data );
		}
		return $data;
	}
	/**
	 * 获取指定折扣的信息
	 * @param unknown_type $discountRow 折扣信息
	 * @param unknown_type $lat 经纬度
	 * @param unknown_type $lng
	 * @param unknown_type $type 是否需要获取discount的内容，默认为0：代表$discountRow中包含了所需的折扣信息；设为1：需要重新获取折扣内容
	 * @param unknown_type $is_cache 是否缓存
	 */
	public function getDiscountRow( $discountRow , $lat , $lng , $type = 0 , $is_cache = false ){
		$discount_id = $discountRow["discount_id"];
		$key = "list_discount_{$discount_id}";
		$data = $this->getData($key);
		if( empty($data) || !$is_cache ){
			if( $type == 1 ){//为1时，需要获取折扣的详细信息
				$sql = "SELECT `title`,`created`,`user_id` as pub_uid,`stime`,`etime` ,`lng` as pub_lng,`lat` as pub_lat,`address` 
						FROM `discount_content`
						WHERE `discount_id`='{$discount_id}'";
				$data = $this->_db->fetchRow($sql);
			}else{
				$data = $discountRow;
			}
			$wapImgList = array();
			$iids = 'iid';
			$wapImgList = $this->select ( "`discount_id` = '{$discount_id}'", 'discount_wap_img', '`id`,`img_url`,`height`,`width`,`filesize`', 'sequence asc, created asc' );
			if(empty($wapImgList)) {
				$iids = 'id';
				$wapImgList = $this->select ( "`discount_id` = '{$discount_id}'", 'discount_img', '*', 'sequence asc, created asc' );
			}
			$wapImgArr = $originalWapImgArr = array();
			$i = 0;
			foreach ( $wapImgList as $rowWapImg ) {
				$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight($rowWapImg['img_url'], 'discount');
				if( empty($widthHeightRow['width']) || empty($widthHeightRow['height']) ){
					continue;
				}
				$originalWapImgArr[] = array(
						'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/discount/" . $rowWapImg ['img_url'],
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
				);
				$widthHeightRow640 = Model_Api_App::getInstance ()->getImageWidthHeight( $rowWapImg ['img_url'], 'discount', 640 );
				$wapImgArr[$i]['W640'] = array (
						'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/640/type/discount",
						'width' => empty($widthHeightRow640 ['width'])?0:$widthHeightRow640 ['width'],
						'height' => empty($widthHeightRow640 ['height'])?0:$widthHeightRow640 ['height']
						);
				$widthHeightRow400 = Model_Api_App::getInstance ()->getImageWidthHeight( $rowWapImg ['img_url'], 'discount', 400 );
				$wapImgArr[$i]['W400'] = array (
						'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/400/type/discount",
						'width' => empty($widthHeightRow400 ['width'])?0:$widthHeightRow400 ['width'],
						'height' => empty($widthHeightRow400 ['height'])?0:$widthHeightRow400 ['height']
						);
				$widthHeightRow240 = Model_Api_App::getInstance ()->getImageWidthHeight( $rowWapImg ['img_url'], 'discount', 240 );
				$wapImgArr[$i]['W240'] = array (
						'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/{$iids}/{$rowWapImg['id']}/w/240/type/discount",
						'width' => empty($widthHeightRow240 ['width'])?0:$widthHeightRow240 ['width'],
						'height' => empty($widthHeightRow240 ['height'])?0:$widthHeightRow240 ['height']
						);
				$i++;
			}
			$data["thumb_wap_img"] =  $wapImgArr;
			$data["original_wap_img"] = $originalWapImgArr;
			$this->setData($key, $data , 3600);
		}
		$data = array_merge($data,$discountRow);
		if( array_key_exists("distance", $data) ){
			$data["distance"] = floor($data["distance"]);
		}else{
			$data["distance"] = -1;//用户没有传经纬度(附近关注)
		}
		if( $lat>0 && $lng>0 && $data["pub_lat"] >0 && $data["pub_lng"]>0 ){
			if( !array_key_exists("pub_distance", $data)  ){
				$data["pub_distance"] = getDistance($lat, $lng, $data["pub_lat"], $data["pub_lng"]);
			}
		}else{
			$data["pub_distance"] = -1;//没有经纬度
		}
		$data["is_start"] = array("status"=>0,"msg"=>"未开始");
		if( $data["etime"] <= REQUEST_TIME ){
			$data["is_start"] = array("status"=>-1,"msg"=>"已结束");
		}else if( $data["stime"]>REQUEST_TIME && $data["stime"]<REQUEST_TIME + 24*60*60 ){//即将开始
			$data["is_start"] = array("status"=>1,"msg"=>"即将开始");
		}else if($data["stime"]<=REQUEST_TIME){//进行中
			$data["is_start"] = array("status"=>2,"msg"=>"进行中");
		}
		$data["user_name"] = $data["uuid"] = $data ["avatar"] = "";
		//如果有附近关注用户id,去附近关注用户信息，否则取发布折扣用户信息
		$user_id = !empty($data["user_id"])?intval($data["user_id"]):intval($data["pub_uid"]);
		
		if( $user_id ){
			$userRow = $this->getUserByUserId($user_id , '`uuid`,`user_name`');
			$userInfo = $this->getWebUserId($userRow['uuid'], true);
			if($userInfo) {
				$data["uuid"] = $userInfo["uuid"];
				$data["user_name"] = $userInfo["user_name"];
				$data["avatar"] = $userInfo['Avatar50'];
			}
			$data["user_id"] = $user_id;
		}
		
		$data["format_date"] = Custom_Time::getTime2($data ["created"]);
		//如果不是从缓存中获取的折扣信息，并且含有concern_number字段，则不需要再获取concern_number
		if( (empty($data) || !$is_cache) && array_key_exists("concern_number", $data) ){
			$data["concern_number"] = intval($data["concern_number"]);
		}else{
			$data["concern_number"] = $this->getDiscountConcernNumber($discount_id);
		}
		
		return $data;
	}
	
	// 获取折扣列表
	public function getDiscountList($getData, $city, $pageSize, $is_cache = false) {
		$page = intval($getData['page']);
		if( $page < 1 ){
			$page = 1;
		}
		$title = $getData['title'] ; // 折扣名称列表
		$lng = $getData['lng'];
		$lat = $getData['lat'];
		$category_id = intval( $getData['cat_id'] ); //折扣分类ID
		$brand_id =	intval( $getData['bid'] ); //品牌ID
		$circle_id = intval( $getData['cir_id'] );//商圈ID
		$market_id = intval( $getData['mid'] );//商场ID
		$is_valid = intval( $getData["is_valid"] );//是否是有效期的 1：是 0：否
		$user_id = intval( $getData["uid"] );//用户id 获取我的关注
		$discount_type = intval( $getData["type"] );//折扣，0：不限 ；1：1-4折；2：4-5折；3：5折以上 ；4：新品上市
		$des = !$getData['des'] ? '' : $getData['des'];
		$key = $this->_key.$title."_".$lat."_".$lng."_".$category_id."_".$brand_id."_".$circle_id."_".
				$market_id."_".$is_valid."_".$user_id."_".$discount_type."_".$des."_".$page;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$start = ($page - 1)*$pageSize;
			$where = $order = '';
			$order = ' C.`star` DESC , C.`created` DESC ';
			switch ($des) {
				case 'nearby' :
					if( $lat>0 && $lng>0 ){
						$order = ' `pub_distance` ASC , C.`created` DESC ';
					}
					break;
				case 'hot' :
					$order = ' B.`concern_number` DESC , C.`created` DESC ';
					break;
				case 'stime_asc' :
					$order = ' C.`stime` ASC ';
					break;
				case 'stime_desc' :
					$order = ' C.`stime` DESC ';
					break;
				case 'etime_asc' :
					$order = ' C.`etime` ASC ';
					break;
				case 'etime_desc' :
					$order = ' C.`etime` DESC ';
					break;
			}
			if( 4 == $discount_type ){
				$order = " C.`stime` DESC ";
			}
			$where = " WHERE C.`city` = '{$city}' AND C.`is_del` = 0 AND C.`discount_status` >= 0 ";
			if( $user_id ){//我的折扣
				$bids=$mids=$fuids=$cdids = "";
				//获取我关注的品牌,商场,用户,人ID
				$bids = $this->getConcernBrands($user_id);
				$mids = $this->getConcernMarkets($user_id);
				$fuids = $this->getConcernUsers($user_id);
				$cdids = $this->getFavoriteDiscounts($user_id);
				$where .= " AND C.`etime` > ".REQUEST_TIME;
// 	 			if( !$bids && !$mids && !$fuids && !$cdids ){
// 	 				return array("totalRes"=>0,"list"=>array());//未关注任何，跳到新用户
// 	 			}
				$fuids .= $fuids?",".$user_id:$user_id;
				if( $bids ){
					$sub_where .= " C.`discount_id` IN (SELECT `discount_id` FROM `discount_brand` WHERE `brand_id` IN ({$bids}) ) OR ";
				}
				if( $mids ){
					$sub_where .= " C.`market_id` IN ({$mids}) OR ";
				}
				if( $fuids ){
					$sub_where .= " C.`user_id` IN ({$fuids}) OR ";
				}
				if( $cdids ){
					$sub_where .= " C.`discount_id` IN ({$cdids}) OR ";
				}
				if( $sub_where ){
					$sub_where = substr($sub_where, 0 , -3);
					$where .= " AND ({$sub_where})";
				}
			}else{//搜索
				if( $is_valid )//有效期以内的
					$where .= " AND C.`etime` > ".REQUEST_TIME;
				if( $brand_id ){
					$where .= " AND C.`discount_id` IN ( SELECT `discount_id` FROM `discount_brand` WHERE brand_id = {$brand_id} )";
				}else if( $market_id ){
					$where .= " AND C.`market_id` = {$market_id}";
				}
				if( $category_id )
					$where .= " AND C.`category_id` = {$category_id}";
				if( $circle_id )
					$where .= " AND C.`circle_id` = {$circle_id}";
				if( 1 == $discount_type ){
					$where .= " AND C.`discount_start` >= 1 AND C.`discount_end` < 4 ";
				}else if( 2 == $discount_type ){
					$where .= " AND C.`discount_start` >= 4 AND C.`discount_end` < 5 ";
				}else if( 3 == $discount_type ){
					$where .= " AND ( C.`discount_start` >= 5 OR (C.`discount_start` = 0 AND C.`discount_end` =0) )";
				}
				if( $title )
					$where .= " AND C.`title` LIKE '%{$title}%' ";
			}
			if( $lat>0 && $lng>0 ){
				if( $des == "hot" ){//按关注数排序需要join discount_concerned_number表，独立出来
					$sql = "SELECT 12756274*asin(Sqrt(power(sin(({$lat}-C.`lat`)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(C.`lat`*0.0174533)*power(sin(({$lng}-C.`lng`)*0.008726646),2))) as `pub_distance`,
							C.`discount_id`,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime`,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address`,B.`concern_number` 
							FROM `discount_content` AS C 
							LEFT JOIN `discount_concerned_number` AS B ON B.`discount_id`=C.`discount_id`
							{$where} 
							ORDER BY {$order}";
				}else{
					$sql = "SELECT 12756274*asin(Sqrt(power(sin(({$lat}-C.`lat`)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(C.`lat`*0.0174533)*power(sin(({$lng}-C.`lng`)*0.008726646),2))) as `pub_distance`,
							C.`discount_id`,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime`,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address` 
							FROM `discount_content` C {$where} 
							ORDER BY {$order}";
				}
			}else{
				if( $des == "hot" ){//按关注数排序需要join discount_concerned_number表，独立出来
					$sql = "SELECT C.`discount_id` ,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime`,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address`,B.`concern_number`
							FROM `discount_content` C 
							LEFT JOIN `discount_concerned_number` AS B ON B.`discount_id`=C.`discount_id`
							{$where}
							ORDER BY {$order}";
				}else{
					$sql = "SELECT C.`discount_id` ,C.`created`,C.`title`, C.`user_id` as pub_uid, C.`stime`,C.`etime`,C.`lng` as pub_lng,C.`lat` as pub_lat,C.`address` 
							FROM `discount_content` C {$where} 
							ORDER BY {$order}";
				}
			}
			$discountArr = $this->_db->limitQuery($sql, $start, $pageSize);
			foreach ( $discountArr as $key => $row ){
				$row["is_main"] = 0;
				$discountArr[$key] = $this->getDiscountRow($row, $lat, $lng , 0 ,true);
			}
			$data = array();
			$data["totalRes"] = 0;
			if( !$user_id ){
				$totalRec = $this->_db->fetchOne("SELECT count(*) FROM `discount_content` C {$where}");
				$data["totalRes"] = $totalRec;
			}
			$data["list"] = $discountArr;
			$this->setData($key,$data);
		}
		return $data;
	}
	
	public function getDiscounts( $getData , $city , $pageSize , $is_cache ){
		$page = !$getData ['page'] || intval($getData ['page']) < 0 ? 1 : intval($getData['page']);
		$lng = $getData ['lng'];
		$lat = $getData ['lat'];
		$key = $this->_key.$lat."_".$lng."_".$page;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$start = ($page - 1) * $pageSize;
			$where = " WHERE C.`city` = '{$city}' AND C.`discount_status` >= 0 AND C.`is_del` = 0 AND C.`etime` > ".REQUEST_TIME;
			if( $lat>0 && $lng>0 ){
				$sql = "SELECT 12756274*asin(Sqrt(power(sin(({$lat}-C.`lat`)*0.008726646),2) + Cos(36.793*0.0174533)*Cos(C.`lat`*0.0174533)*power(sin(({$lng}-C.`lng`)*0.008726646),2))) as `pub_distance`,".
						"C.`discount_id` AS come_from_id,C.`title`,'discount' as pmark,'discount_view' as cmark ".
						"FROM `discount_content` C {$where} ".
						"ORDER BY `pub_distance` ASC, C.`etime` ASC";
			}else{
				$sql = "SELECT -1 AS pub_distance, C.`discount_id` AS come_from_id ,C.`title`,'discount' as pmark,'discount_view' as cmark  ".
						"FROM `discount_content` C {$where} ".
						"ORDER BY C.`etime` ASC";
			}
			$data = $this->_db->limitQuery($sql, $start, $pageSize);
			$this->setData($key,$data);
		}
		return $data;
	}
	
	/**
	 * 获取我关注的品牌
	 * @param unknown_type $user_id
	 */
	public function getConcernBrands( $user_id ){
		$bidArr = $this->_db->fetchAll("SELECT `brand_id` FROM `oto_brand_favorite` WHERE `user_id` = {$user_id}");
		foreach ( $bidArr as $row ){
			$bids .= $row["brand_id"].",";
		}
		$bids = substr($bids, 0 , -1);
		return $bids;
	}
	
	/**
	 * 获取我关注的商场
	 * @param unknown_type $user_id
	 */
	public function getConcernMarkets( $user_id ){
		//获取我关注的商场
		$midArr = $this->_db->fetchAll("SELECT `market_id` FROM `oto_market_favorite` WHERE `user_id` = {$user_id}");
		foreach ( $midArr as $row ){
			$mids .= $row["market_id"].",";
		}
		$mids = substr($mids, 0 , -1);
		return $mids;
	}
	
	/**
	 * 获取起我关注的人
	 * @param unknown_type $user_id
	 */
	public function getConcernUsers( $user_id ){
		//获取我关注的人
		$fuidArr = $this->_db->fetchAll("SELECT `to_user_id` FROM `oto_user_concerned` WHERE `from_user_id` = {$user_id}");
		$fuids = "";
		foreach ( $fuidArr as $row ){
			$fuids .= $row["to_user_id"].",";
		}
		$fuids = substr($fuids, 0 , -1);
		return $fuids;
	}
	
	/**
	 * 获取我搜藏的折扣
	 * @param unknown_type $user_id
	 */
	public function getFavoriteDiscounts( $user_id ){
		$cdidArr = $this->_db->fetchAll("SELECT `discount_id` FROM `discount_favorite` WHERE `user_id` = {$user_id}");
		foreach ( $cdidArr as $row ){
			$cdids .= $row["discount_id"].",";
		}
		$cdids = substr($cdids, 0 , -1);
		return $cdids;
	}
	
	//获取专题详情
	public function getSpecialContent( $getData , $city , $is_cache = false ){
		$special_id = $getData["sid"];
		$key = $this->_key.$city."_".$special_id;
		$data = $this->getData( $key );
		
		if( !$is_cache || empty($data) ){
			$data = array();
			$special = $this->select("`special_id`={$special_id}","special_content","*" , "" , 1);
			$cover_img = $special["cover_img"] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' .$special["cover_img"]:"";
			$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight($special["cover_img"], 'cover');
			$special["cover_img"] = array(
								'img_url' => $cover_img,
								'width' => $widthHeightRow['width'],
								'height' => $widthHeightRow['height']
							);
			$special["www_url"]=$GLOBALS['GLOBAL_CONF']['SITE_URL']."/home/daren/wap-show/sid/{$special_id}";
			$data["special"] = $special;
			$goods = $this->specialGoodMore(array("page"=>1,"sid"=>$special_id), $city , false);
			$data["goods"] = $goods;
			$chats = $this->chatMore(array("page"=>1,"id"=>$special_id,"type"=>"special","uid"=>intval($getData["user_id"]) ), $city ,5);
			$data["chats"] = $chats;
			$this->setData($key , $data);
		}
		return $data;
	}
	
	//获取折扣详情
	public	function getDiscountContent( $getData , $city , $is_cache = false ){
		$discount_id = intval($getData["did"]);
		$user_id = intval($getData["uid"]);
		$key = $this->_key."_".$discount_id;
		$data = $this->getData( $key );
		if( !$is_cache || empty($data) ){
			$data = array();
			$discountRow = $this->select("`discount_id`={$discount_id}",
										"discount_content",
										"`discount_id`,`title`,`telephone`,`stime`,`etime`,`address`,`lat`,`lng`,`circle_id`,`market_id`,`wap_content`,`content`" , 
										"" , 
										1);
			if( empty($discountRow) ){
				return array();
			}
			$discountRow["wap_content"] = htmlspecialchars_decode($discountRow["wap_content"]);
			$discountRow["date"] = datex($discountRow["stime"],"Y.m.d")." - ".datex($discountRow["etime"],"Y.m.d");
			$discountRow["brand"] = array();
			
			$sql = "SELECT B.`brand_id`,B.`brand_name_zh`,B.`brand_name_en` 
					FROM `discount_brand` A 
					LEFT JOIN `oto_brand` B 
					ON B.`brand_id` = A.`brand_id` 
					WHERE A.`discount_id` = {$discount_id}";
			$brandList = $this->_db->fetchAll( $sql );
			foreach ($brandList as &$row){
				$row["brand_name"] = $row["brand_name_zh"] . (($row["brand_name_zh"]&&$row["brand_name_en"]) ? "(" :"") . $row["brand_name_en"] .(($row["brand_name_zh"]&&$row["brand_name_en"]) ? ")" :"");
				unset( $row["brand_name_zh"] , $row["brand_name_en"]);
			}
			$discountRow["brand"] = $brandList;
			
			$discountRow["circle_name"] = "";
			if( $discountRow["circle_id"] ){
				$circleRow = $this->getCircleByCircleId($discountRow["circle_id"],true,$city);
				if( !empty($circleRow) ){
					$discountRow["circle_name"] = $circleRow["circle_name"];
				}
			}else{
				$discountRow["circle_id"] = '';
			}
			
			$discountRow["market_name"] = "";
			if( $discountRow["market_id"] ){
				$marketRow = $this->select("market_id={$discountRow["market_id"]}","oto_market","`market_name`","",1);
				if( !empty($marketRow) ){
					$discountRow["market_name"] = $marketRow["market_name"];
				}
			}else{
				$discountRow["market_id"] = '';
			}
			
			$wapImgList = $this->select("`discount_id` = '{$discount_id}'", 'discount_wap_img','`id`,`img_url`,`height`,`width`,`filesize`', 'sequence asc, created asc');
			$wapImgArr = array();
			$i = 0;
			foreach($wapImgList as $rowWapImg) {
				$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight($rowWapImg['img_url'], 'discount');
				if( empty($widthHeightRow['width']) || empty($widthHeightRow['height']) ){
					continue;	
				}
				$wapImgArr[$i] = array(
						'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL']."/buy/discount/".$rowWapImg['img_url'] ,
						'width' => $widthHeightRow['width'],
						'height' => $widthHeightRow['height']
						);
				$i++;
			}
			$discountRow["wap_img"] = $wapImgArr;
			$discountRow["is_collect"] = 0;
			$discountRow["is_notice"] = 0;//是否提醒
			$discountRow["wap_url"] = $GLOBALS['GLOBAL_CONF']['SITE_URL']."/home/discount/wap-show/did/".$discount_id;
			$data["discount"] = $discountRow;
			$this->setData($key , $data , 3600);//折扣基本不会有变动，缓存数据保留1小时
		}
		if( $data["discount"]["stime"] - REQUEST_TIME > 24*60*60){
			$data["discount"]["is_notice"] = 1;
		}
		$data["discount"]["concern_number"] = $this->getDiscountConcernNumber($discount_id);
		$data["discount"]["last_concern_name"] = $this->getLastCopncernUserName($discount_id);
		unset($data["discount"]["stime"] , $data["discount"]["etime"]);
		$chats = $this->chatMore(array("page"=>1,"id"=>$discount_id,"type"=>"discount","uid"=>$user_id), $city ,5);
		$data["chats"] = $chats;
		if( $user_id ){
			$sql = "SELECT count(*) FROM `discount_favorite` WHERE `discount_id`={$discount_id} AND `user_id` = {$user_id}";
			if( $this->_db->fetchOne($sql) == 1 ){
				$data["discount"]["is_collect"] = 1;
			}
		}
		$this->discountVisit($getData, $city);
		return $data;
	}
	/**
	 * 获取最后关注的人的名字
	 * @param unknown_type $discount_id
	 */
	private function getLastCopncernUserName( $discount_id ){
		$name = "";
		$res = $this->select("`discount_id` = {$discount_id}" , "discount_concerned" , "`user_id`" , "`updated` DESC" , 1 );
		if( !empty($res) ){
			$userRow = $this->getUserByUserId($res["user_id"],'user_name');
			if( !empty($userRow) ){
				$name = $userRow["user_name"];
			}
		}
		return $name;
	}
	
	//专题商品
	public function specialGoodMore( $getData , $city , $pageSize ){
		$page = !$getData['page'] || intval($getData['page']) < 0 ? 1 : intval($getData['page']);
		$limit = "";
		if( $pageSize ){
			$start = ($page-1)*$pageSize;
			$limit = "LIMIT {$start},{$pageSize}";
		}
		
		$special_id = intval($getData["sid"]);
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'commodity');
		$where = "  AND B.`ticket_type` = '{$ticket_type}' AND B.`ticket_status` = '1' AND B.`is_auth` = '1' AND B.`is_show` = 1 AND B.`end_time`>'".REQUEST_TIME."' ";
		$sql = "select B.`ticket_id`,B.`ticket_uuid`,B.`ticket_title`,B.`ticket_type`,B.`par_value`,B.`selling_price`, B.`free_shipping`
				from `special_good` A
				left join `oto_ticket` B on A.good_id = B.ticket_id
				where B.city = '{$city}' {$where} and A.`special_id`={$special_id} order by created DESC {$limit}";
		$goods = $this->_db->fetchAll( $sql );
		$thumb_img_width = 280;
		foreach( $goods as &$row ){
			$imgList = Model_Api_App::getInstance()->getTicketWapImg($row['ticket_id'],'commodity');
			//第一张缩略图
			if(isset($imgList[0])) {
				$row["first_img"] = $imgList[0];
			}else{
				$row["first_img"] = array('img_url'=>'', 'width'=>0, 'height'=>0);
			}
			//处理商品价格
			if($row['is_free'] == 1) {
				$row['selling_price'] = 0;
			}
			//unset($row['app_price']);
			//折扣
			$row['discount'] = round(($row['selling_price'] / $row['par_value']) * 10 , 1);
			$row['detail_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/home/ticket/wap/tid/'.$row["ticket_id"];
			$row['buy_url'] = 'http://superbuy.mplife.com/Wap/Pay/Buy.aspx?i='.$row["ticket_uuid"];
		}
		return $goods;	
	}
	
	//聊聊 （专题 ， 折扣）
	public function chatMore( $getData , $city ,$pageSize ){
		$page = intval($getData["page"]);
		$user_id = intval($getData["uid"]);
		if( $page < 1 ) $page = 1;
		$start = ($page-1)*$pageSize;
		$id = intval($getData["id"]);
		$type = empty($getData["type"])?"discount":$getData["type"];
		$sql = "SELECT COUNT(*) FROM `oto_group_chat` WHERE `did` = {$id} AND `type` = '{$type}' AND `is_del` = 0";
		$count = $this->_db->fetchOne( $sql );
		$sql = "SELECT * FROM `oto_group_chat` WHERE `did` = {$id} AND `type` = '{$type}' AND `is_del` = 0 ORDER BY `created` DESC LIMIT {$start},{$pageSize}";
		$data = $this->_db->fetchAll( $sql );
		$count -= $start;
		foreach( $data as &$row ){
			$row['floor'] = $count."F";
			$row['created'] = datex($row['created'],"Y-m-d H:i:s");
			$count--;
			$row["is_praise"] = 0;
			if( $user_id ){
				$row["is_praise"] = $this->checkUserIsPraise($user_id, $row["id"]);
			}
		}
		return $data;
	} 
	
	/**
	 * 检查用户是否对某个留言按赞
	 * @param unknown_type $user_id 用户id
	 * @param unknown_type $chat_id 聊天记录id
	 */
	private function checkUserIsPraise( $user_id , $chat_id ){
		$sql = "SELECT COUNT(*) FROM `oto_group_chat_praise` WHERE `user_id` = {$user_id} AND `chat_id` = {$chat_id}";
		return $this->_db->fetchOne( $sql );
	} 
	
	//聊聊（专题，折扣）
	public function chatAdd( $getData , $userInfo ){
		$ip = !$getData['ip'] ? CLIENT_IP : $getData['ip'];
		$type = empty($getData["type"])?"special":$getData["type"];
		$id = intval($getData["id"]);
		//折扣信息
		$question = Custom_String::HtmlReplace($getData['content'],3);
		$postParam = array(
				'type' => $type,
				'did' => $id,
				'user_id' => $userInfo['user_id'],
				'user_name' => $userInfo['user_name'],
				'avatar' => $userInfo['Avatar50'],
				'question' => $question,
				'ip' => $ip,
				'created' => REQUEST_TIME
		);
		$last_id = $this->_db->insert('oto_group_chat', $postParam);
		if( $last_id ){
			$param = array(
					"charter_user_id"=>$userInfo['user_id'],
					"charter_member"=>$userInfo['user_name'],
					"charter_member_avator"=> $userInfo['Avatar50']
					);
			Model_Api_Message::getInstance()->addPreNotice("discount", $type."_group_chat", $id , $param);
		}
		return $last_id;
	}
	
	/**
	 * 折扣图片上传
	 * @param unknown_type $wap_img_url
	 * @param unknown_type $discount_id
	 * @param unknown_type $user_id
	 * @return Ambigous <number, unknown>|number
	 */
	public function wapUploadImg($wap_img_url, $discount_id = 0, $user_id = 0) {
		$sql = $sqlstr = '';
		$shop_id = intval($shop_id);
		if(!empty($wap_img_url)) {
			if(is_array($wap_img_url)) {
				foreach($wap_img_url as $img_url) {
					if($img_url) {
						$sqlstr .= "('{$discount_id}', '{$user_id}', '{$img_url}', '". REQUEST_TIME ."'), ";
					}
				}
			} else {
				$sqlstr .= "('{$discount_id}', '{$user_id}', '{$wap_img_url}', '". REQUEST_TIME ."'), ";
			}
	
			if($sqlstr) {
				$sql = "insert into `discount_wap_img` (`discount_id`, `user_id`, `img_url`, `created`) values " . substr($sqlstr, 0, -2);
				$query = $this->_db->query($sql);
				if($query) {
					$insertId = $this->_db->lastInsertId();
					return $insertId ? $insertId : 0;
				}
			}
		}
			
		return 0;
	}
	
	/**
	 * 折扣新增
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 * @return unknown
	 */
	public function discountAdd( $getData , $city ){
		//图片IDS
		$img_ids = $getData['iids'] ? trim($getData['iids'], ',') : '';
		$user_id = intval($getData['uid']);
		$userRow = $this->getUserByUserId($user_id);
		$lat = trim($getData['lat']);
		$lng = trim($getData['lng']);
		$address = Custom_String::HtmlReplace($getData['address'],-1);
		if( intval($lat)==0 && intval($lng)==0 ){
			$lngLatString = $this->getLatitudeAndLongitudeFormamap($address,$city);
			if($lngLatString) {
				list($lng,$lat) = explode(",", $lngLatString);
			}
		}
		$title = Custom_String::HtmlReplace($getData['title'],-1);
		$param = array(
				'title'		=> 	$title,
				'user_id'	=>	$userRow['user_id'], //用户ID
				'user_name'	=>	$userRow['user_name'], //用户名
				'stime'		=> 		strtotime($getData['stime']),//折扣开始时间
				'etime'		=> 		strtotime($getData['etime']),//折扣结束时间
				'address'	=>		$address, 		//地址
				'lng' 		=>		$lng,
				'lat' 		=> 		$lat,
				'ip'		=>  CLIENT_IP,
				'city'		=>  $city,
				'updated'	=>		REQUEST_TIME,
				'created'   => 		REQUEST_TIME
		);
		$last_insert_id = $this->_db->insert('discount_content', $param);
		if( !$last_insert_id ){
			return false;
		}
		$sql = "SELECT COUNT(*) FROM `discount_content` WHERE `user_id` = {$getData['uid']} AND `is_del` = 0";
		$numbers = $this->_db->fetchOne($sql);
		$this->_db->update("oto_user",array("discount_number"=>$numbers),array("user_id"=>$getData["uid"]) );
		//关联折扣图片
		if($img_ids) {
			$sql = "select * from `discount_wap_img` where `id` in ({$img_ids})";
			$imgArr = $this->_db->fetchAll($sql);
			foreach($imgArr as & $imgRow) {
				if($imgRow['discount_id'] == 0) {
					$this->_db->update('discount_wap_img', array('discount_id' => $last_insert_id), array('id' => $imgRow['id']));
				}
			}
		}
		//同步动态
		$this->syncFavoriteDynamic( array('user_id' => $user_id, 'from_id' => $last_insert_id, 'summary' => $title,'type'=>6, 'favorite_id'=>$last_insert_id,'created' => REQUEST_TIME) );
		
		$userInfo = $this->getUserByUserId($user_id);
		$avatar = $this->getUserAvatar($userInfo["user_name"]);
		Model_Api_Message::getInstance()->addPreNotice("discount", "discount_view", $last_insert_id , array(
			"charter_user_id"=>$user_id,
			"charter_member"=>$userInfo["user_name"],
			"charter_member_avator"=>$avatar
		));
		return $last_insert_id;
	}
	
	
	
	//折扣查看
	public function discountVisit( $getData , $city ){
		$discount_id = intval( $getData['did'] );
		$sql = "UPDATE `discount_content` SET `view_quantity` = `view_quantity` + 1 WHERE `discount_id` = {$getData['did']}";
		$this->_db->query($sql);
		$user_id = $lat = $lng = 0;
		if( $getData['uid'] )
			$user_id = intval($getData['uid']);
		if( $getData['lat'] )
			$lat = $getData['lat'];
		if( $getData['lng'] )
			$lng = $getData['lng'];
		$data = array('discount_id'=>$discount_id,
					  'user_id'=>$user_id,
					  'lat'=>$lat,
					  'lng'=>$lng,
					);
		if( $user_id>0 && $lat>0 && $lng>0 ){
			$time = REQUEST_TIME;
			$row = $this->select("`discount_id`={$discount_id}",
										"discount_content",
										"`title`,`stime`,`etime`,`type_id`,`category_id`,`discount_start`,`discount_end`,`region_id`,`circle_id`,`market_id`,`discount_status`,`is_del`","",1);
			if( !empty($row) ){
				$title = $row["title"];
				unset( $row["title"] );
				$data = array_merge($data , $row , array('ip'=>CLIENT_IP,'created'=>$time,'city'=>$city));
				
				$sql = " SELECT COUNT(*) 
						FROM `discount_visit` 
						WHERE `discount_id` = {$discount_id} AND `user_id` = {$user_id} AND `created` > ".($time - 300);
				$count = $this->_db->fetchOne( $sql );
				if( $count ==0 ){
					$last_insert_id = $this->_db->insert("discount_visit",$data);
					if( $last_insert_id > 0 ){
						//更新用户关注折扣的记录
						$this->addDiscountUserConcerned($user_id, $discount_id, $time);
						//更新折扣关注用户的数量
						$this->updateDiscountConcernNumber($discount_id);
						//更新用户关注折扣的数量
						$this->updateUserConcernNumber($user_id);
						//同步动态
						$param = array('user_id' => $user_id, 
										'from_id' => $discount_id, 
										'summary' => $title,
										'type'=>7, 
										'favorite_id'=>$last_insert_id,
										'created' => REQUEST_TIME);
						$this->syncFavoriteDynamic( $param );
					}
				}
			}
		}
	}
	
	/**
	 * 添加用户关注折扣记录
	 * @param unknown_type $user_id
	 * @param unknown_type $discount_id
	 * @param unknown_type $time
	 */
	public function addDiscountUserConcerned( $user_id , $discount_id , $time ){
		$sql = "INSERT INTO `discount_concerned`(`user_id`,`discount_id`,`created`,`updated`) VALUES ('{$user_id}','{$discount_id}','{$time}','{$time}') ON DUPLICATE KEY UPDATE `updated`=VALUES(`updated`)";
		$this->_db->query($sql);
	}
	
	/**
	 * 修改折扣关注用户数
	 * @param unknown_type $discount_id 折扣id
	 */
	public function updateDiscountConcernNumber( $discount_id ){
		$sql = "SELECT COUNT(*) FROM `discount_concerned` WHERE `discount_id`='{$discount_id}' AND `status`='1'";
		$concern_numbers = $this->_db->fetchOne($sql);
		$insert_sql = "INSERT INTO `discount_concerned_number` 
					   VALUES('{$discount_id}','{$concern_numbers}') 
					   ON DUPLICATE KEY UPDATE `concern_number` = VALUES(`concern_number`)";
		$this->_db->query($insert_sql);
	}
	/**
	 * 获取折扣关注数
	 * @param unknown_type $discount_id 折扣id
	 */
	public function getDiscountConcernNumber( $discount_id ){
		$row = $this->select_one("`discount_id`='{$discount_id}'", 'discount_concerned_number');
		if( empty($row) ){
			return 0;
		}
		return intval($row["concern_number"]);
	}
	
	/**
	 * 修改用户关注的折扣数
	 * @param unknown_type $user_id 用户id
	 */
	public function updateUserConcernNumber( $user_id ){
		$sql = "SELECT COUNT(*) FROM `discount_concerned` WHERE `user_id`='{$user_id}' AND `status`='1'";
		$user_concern_numbers = $this->_db->fetchOne($sql);
		$this->_db->update('oto_user',array("discount_concern_number"=>$user_concern_numbers),array("user_id"=>$user_id));
	}
	
	/**
	 * 改变折扣收藏数量
	 * @param unknown_type $discount_id 折扣id
	 */
	public function updtDiscountFavQuantity($discount_id){
		$count = $this->_db->fetchOne("SELECT count(*) FROM `discount_favorite` WHERE `discount_id` = {$discount_id}");
		return $this->_db->update('discount_content', array('collection_number'=>$count) , "`discount_id` = '{$discount_id}'");
	}
	
	/**
	 * 改变聊聊记录按赞情况
	 * @param unknown_type $chat_id
	 */
	public function updtChatPraiseQuantity( $chat_id ){
		$count = $this->_db->fetchOne("SELECT count(*) FROM `oto_group_chat_praise` WHERE `chat_id` = {$chat_id}");
		return $this->_db->update('oto_group_chat', array('praise_number'=>$count) , "`id` = '{$chat_id}'");
	}
	
	/**
	 * 相关折扣
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function relateDiscount( $getData , $city , $is_cache = false ){
		$discount_id = intval($getData["did"]);
		$bids = $getData["bids"];//多个品牌以逗号隔开
		$bids = trim( $bids , "," );
		$market_id = intval($getData["mid"]);//商场id
		$category_id = intval($getData["cid"]);//分类id
		$lat = $getData["lat"];
		$lng = $getData["lng"];
		$key = $this->_key.$bids."_".$market_id."_".$category_id."_".$lat."_".$lng."_".$city;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$brandDiscount = $marketDiscount = $categoryDiscount = $newDiscount = array();
			$where = " AND B.`city` = '{$city}' AND B.`is_del` = 0 AND B.`discount_id` != {$discount_id} AND B.`discount_status` >= 0  AND B.`etime` > ".REQUEST_TIME;
			$subWhere = "";
			$limit = " LIMIT 5";
			$order = " ORDER BY B.created DESC ";
			if( $bids ){
				$sql = "SELECT B.`discount_id`, B.`created`, B.`title`,
						B.`user_id` as pub_uid, B.`stime`, B.`etime`, B.`lng` as pub_lng,
						B.`lat` as pub_lat, B.`address` FROM (
							SELECT * FROM `discount_brand` WHERE `brand_id` IN ({$bids})
						) AS A
						LEFT JOIN `discount_content` AS B ON B.`discount_id` = A.`discount_id` 
						WHERE 1 {$where} 
						GROUP BY B.`discount_id` {$order}  {$limit}";
				$brandDiscount = $this->_db->fetchAll($sql);
				$dids = "";
				foreach( $brandDiscount as $key => $row ){
					$dids .= $row["discount_id"].",";
					$row["is_main"] = 0;//显示发布用户信息
					$brandDiscount[$key] = $this->getDiscountRow($row, $lat, $lng , 0 ,true);
				}
				if( $dids ){
					$dids = trim($dids,",");
					$subWhere = " AND B.`discount_id` NOT IN ({$dids}) ";
				}
			}
			if( $market_id ){
				$sql = "SELECT B.`discount_id`, B.`created`, B.`title`,
					B.`user_id` as pub_uid, B.`stime`, B.`etime`, B.`lng` as pub_lng,
					B.`lat` as pub_lat, B.`address` 
					FROM `discount_content` B 
					WHERE B.`market_id` = {$market_id} {$where} {$subWhere} {$order} {$limit}";
				$marketDiscount = $this->_db->fetchAll($sql);
				foreach( $marketDiscount as $key => $row ){
					$dids .= $row["discount_id"].",";
					$row["is_main"] = 0;//显示发布用户信息
					$marketDiscount[$key] = $this->getDiscountRow($row, $lat, $lng , 0 ,true);
				}
				if( $dids ){
					$dids = trim($dids,",");
					$subWhere = " AND B.`discount_id` NOT IN ({$dids}) ";
				}
			}
			if( $category_id ){
				$sql = "SELECT B.`discount_id`, B.`created`, B.`title`,
					B.`user_id` as pub_uid, B.`stime`, B.`etime`, B.`lng` as pub_lng,
					B.`lat` as pub_lat,B.`address`
					FROM `discount_content` B 
				WHERE B.`category_id` = {$category_id} {$where} {$subWhere} {$order} {$limit}";
				$categoryDiscount = $this->_db->fetchAll($sql);
				foreach( $categoryDiscount as $key =>$row ){
					$dids .= $row["discount_id"].",";
					$row["is_main"] = 0;//显示发布用户信息
					$categoryDiscount[$key] = $this->getDiscountRow($row, $lat, $lng , 0 ,true);
				}
				if( $dids ){
					$dids = trim($dids,",");
					$subWhere = " AND B.`discount_id` NOT IN ({$dids}) ";
				}
			}
			$sql = "SELECT B.`discount_id`, B.`created`, B.`title`,
					B.`user_id` as pub_uid, B.`stime`, B.`etime`, B.`lng` as pub_lng,
					B.`lat` as pub_lat, B.`address`
					FROM `discount_content` B WHERE 1 {$where} {$subWhere} {$order} {$limit}";
			$newDiscount = $this->_db->fetchAll($sql);
			foreach( $newDiscount as $key => $row ){
				$row["is_main"] = 0;//显示发布用户信息
				$newDiscount[$key] = $this->getDiscountRow($row, $lat, $lng , 0 ,true);
			}
			$data = array_merge($brandDiscount,$marketDiscount,$categoryDiscount,$newDiscount);
			$this->setData($key,$data);
		}
		return $data;
	}
	
	//我们都关注
	public function discountViewUser( $getData , $city , $pageSize , $is_cache = false ){
		$page = intval($getData["page"]);
		if( $page < 1) $page = 1;
		$start = ($page-1)*$pageSize;
		$discount_id = intval($getData["did"]);//折扣id
		$user_id = intval($getData["uid"]);
		$key = $this->_key.$discount_id."_".$page."_".$city;
		$data = $this->getData( $key );
		if( !$is_cache || empty($data) ){//discount_number写错成discount_concern_number
			$sql = "SELECT B.`user_id`,B.`uuid`,B.`user_name`, B.`discount_number` as discount_concern_number 
					FROM `discount_concerned`
					AS A LEFT JOIN `oto_user` AS B ON B.`user_id` = A.`user_id`
					WHERE A.`discount_id` = {$discount_id} AND B.`user_status` = 0 AND B.`is_del` = 0 ".($user_id?" AND B.`user_id`!={$user_id} ":"")."
					LIMIT {$start},{$pageSize}";
			$data = $this->_db->fetchAll($sql);
			foreach( $data as &$row){
				$row ["avatar"] = ""; // 默认头像
				if ($row["uuid"]) {
					$avatarRow = $this->getUserAvatarByUuid( $row["uuid"] );
					if (!empty($avatarRow) && $avatarRow['Avatar50']) {
						$row["avatar"] = $avatarRow['Avatar50'];
					}
				}
				$row["is_follow"] = 0;
			}
			$this->setData($key , $data);
		}
		if( $user_id ){
			$sql = "select `to_user_id` from `oto_user_concerned` where `from_user_id` = '{$user_id}' ";
			$concernUsers = $this->_db->fetchCol($sql);
			foreach ($data as &$row){
				if( in_array($row["user_id"], $concernUsers) ){
					$row["is_follow"] = 1;
				}
			}
		}
		return $data;
	}
	
	//用户关注列表
	public function userViewDiscount( $getData , $city , $pageSize , $is_cache ){
		$user_id = intval($getData["vuid"]);
		$page = intval( $getData["page"] );
		$lat = $getData["lat"];
		$lng = $getData["lng"];
		if( $page < 1) $page = 1;
		$start = ($page-1)*$pageSize;
		$key = $this->_key.$user_id."_".$lat."_".$lng."_".$page."_".$city;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$limit = " LIMIT {$start},{$pageSize}";
			$sql = "SELECT `discount_id` 
					FROM `discount_concerned`  
					WHERE `user_id` = {$user_id} AND `status`=1 
					ORDER BY `updated` DESC 
					{$limit}";
			$data = $this->_db->fetchAll( $sql );
			foreach( $data as $k => $row ){
				$row["is_main"] = 0;
				$data[$k] = $this->getDiscountRow($row,$lat,$lng,1,true);
			}
			$this->setData($key , $data);
		}
		return $data;
	}
	
	//用户发布的折扣列表
	public function userPublishDiscount( $getData , $pageSize , $city , $is_cache=false ){
		$user_id = intval($getData["vuid"]);
		$page = intval( $getData["page"] );
		$lat = $getData["lat"];
		$lng = $getData["lng"];
		if( $page < 1) $page = 1;
		$start = ($page-1)*$pageSize;
		$key = $this->_key.$user_id."_".$lat."_".$lng."_".$page."_".$city;
		$data = $this->getData($key);
		
		if( !$is_cache || empty($data) ){
			$where = " WHERE B.`city` = '{$city}' AND B.`discount_status` >= 0 AND B.`is_del` = 0";
			$order = " ORDER BY B.`created` DESC";
			$limit = " LIMIT {$start},{$pageSize}";
			$sql = "SELECT B.`discount_id`,B.`created`,B.`title`,
					B.`user_id` as pub_uid, B.`stime`,B.`etime`,B.`lng` as pub_lng,
					B.`lat` as pub_lat,B.`address` 
					FROM `discount_content` AS B WHERE B.`user_id` = {$user_id} {$order} {$limit}";
			$data = $this->_db->fetchAll( $sql );
			foreach( $data as $k => $row ){
				$row["is_main"] = 0;
				$data[$k] = $this->getDiscountRow($row,$lat,$lng,0,true);
			}
			$this->setData($key , $data);
		}
		return $data;
	}
	
	//获取折扣的第一个品牌
	public function getDiscountAndFirstBrand($did){
		$discount = $this->select("discount_id={$did}","discount_content","discount_id,title,concern_number","",1);
		$sql = "SELECT B.`brand_id`,B.`brand_logo`
				FROM `discount_brand` A
				LEFT JOIN `oto_brand` B ON B.`brand_id` = A.`brand_id`
				WHERE A.`discount_id` = {$did} AND B.`brand_logo` <> '' LIMIT 1";
		$brandRow = $this->_db->fetchRow($sql);
		$brand = array("brand_id"=>"","brand_logo"=>"");
		if( !empty($brandRow) ){
			$brand = array("brand_id"=>$brandRow["brand_id"],
						   "brand_logo"=>$brandRow["brand_logo"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $brandRow["brand_logo"] : '/images/blank.png');
		}
		return array($discount , $brand);
	}
	
	//获取私聊的信息
	public function getMessageShow( $getData , $city ){
		$frid = intval($getData["frid"]);
		//获取折扣品牌logo(只取1个)
		$discount = $brand = array();
		list($discount,$brand) = $this->getDiscountAndFirstBrand($frid);
		//获取分享者信息（判断用户是否关注此用户）分享者关注的折扣数
		$to_user_id = intval($getData["to_uid"]);
		$user_id = intval($getData["uid"]);
		$userRow = $this->select("user_id={$to_user_id}","oto_user","user_id,uuid,user_name,discount_concern_number","",1);
		if( !empty($userRow) ){
			$userRow["is_follow"] = 0;
			$sql = "select 1 from `oto_user_concerned` where `from_user_id` = '{$user_id}' and `to_user_id` = '{$to_user_id}' limit 1";
			if( 1==$this->_db->fetchOne($sql) ){
				$userRow["is_follow"] = 1;
			}
		}
		//获取message信息
		$type = $getData["type"];
		$sql = "SELECT * FROM `discount_message_post` 
				WHERE ((`user_id` = {$user_id} AND `to_user_id`={$to_user_id}) OR (`user_id` = {$to_user_id} AND `to_user_id`={$user_id})) AND `type`='{$type}' AND `from_id`={$frid} AND `is_del` = 0 
				ORDER BY `pid` ASC";
		$message = $this->_db->fetchAll( $sql );
		foreach ( $message as &$row){
			$row["position"] = "L";
			if( $row["user_id"] == $user_id ){
				$row["position"] = "R";
			}
			$row["format_date"] = datex($row["created"],"Y-m-d H:i:s");
			unset($row["is_del"],$row["ip"],$row["created"]);
		}
		return array("discount"=>$discount,"brand"=>$brand , "user"=>$userRow , "message"=>$message);
	}
	
	//消息添加
	public function messageAdd( $getData , $city ){
		$user_id = intval($getData["uid"]);
		$to_user_id = intval($getData["to_uid"]);
		$type = $getData["type"];
		$from_id = intval($getData["frid"]);
		//获取聊天记录id
		$tid = $this->getMessageTid($from_id, $user_id, $to_user_id, $type);
		$userRow = $this->getUserByUserId($user_id, 'user_id, uuid, user_name, user_type');
		$userRow["Avatar50"] = $this->getUserAvatar($userRow["user_name"]);
    	$param = array(
    			"to_user_id"=>$to_user_id,
    			"charter_user_id"=>$user_id,
    			"charter_member"=>$userRow["user_name"],
    			"charter_member_avator"=>$userRow["Avatar50"]
    			);
		if($tid<=0){//新增
			$newRes = $this->addDiscountMessage($getData,$userRow);
			$getData["tid"] = $newRes;
			if( $newRes ){//给用户发送推送
				if( "shopping" == $type ){
					Model_Api_Message::getInstance()->addPreNotice("discount", "discount_about_shopping", $from_id,$param);
				}else{
					Model_Api_Message::getInstance()->addPreNotice("discount", "discount_advisory", $from_id,$param);
				}
			}
			return $newRes;
		}else{//追加
			$getData["tid"] = $tid;
			$appendRes = $this->appendDiscountMessage($getData,$userRow);
			if( $appendRes ){//给用户发送推送
				if( "shopping" == $type ){
					Model_Api_Message::getInstance()->addPreNotice("discount", "discount_about_shopping", $from_id,$param);
				}else{
					Model_Api_Message::getInstance()->addPreNotice("discount", "discount_advisory", $from_id,$param);
				}
			}
			return $appendRes;
		}
		return false;
	}
	
	//发送通知
	private function sendDiscountNotice( $getData , $userInfo ){
		$user_id = intval($getData["uid"]);
		$to_user_id = intval($getData["to_uid"]);
		$type = $getData["type"];
		$from_id = intval($getData["frid"]);
		$opentype = '';
		switch ($type) {
			case 'shopping':
				$opentype = 'discount_about_shopping';
				$message = "用户‘{$userInfo['user_name']}’,给你发来约逛信息。";
				break;
			case 'consult':
				$opentype = 'discount_advisory';
				$sql = "SELECT `user_id` FROM `discount_content` WHERE `discount_id` = {$from_id}";
				if( $user_id == $this->_db->fetchOne($sql) ){
					$message = "用户‘{$userInfo['user_name']}’,回复了你的消息。";
				}else{
					$message = "用户‘{$userInfo['user_name']}’,给你发来消息。";
				}
				break;
		}
		$data = array('tid' => $getData['tid'],
				'user_id' => $to_user_id,
				'frid' => $from_id,
				'type' => 'discount',
				'message' => $message,
				'opentype' => $opentype,
				'notice_type'=>2
		);
		$chart_user = array('charter_user_id' => $userInfo['user_id'],
				'charter_member' => $userInfo['user_name'],
				'avator' => $userInfo['Avatar50']);
		Model_Api_Message::getInstance()->sendNotice($data,$chart_user);
		return true;
	}
	
	//检查两个用户之间是否已有聊天记录
	public function getMessageTid( $from_id , $user_id , $to_user_id , $type ){
		$sql = "SELECT `tid` FROM `discount_message_thread`
		WHERE `from_id` = {$from_id} AND `type`='{$type}'
		AND ((`user_id`={$user_id} AND `to_user_id` ={$to_user_id}) OR (`user_id`={$to_user_id} AND `to_user_id` = {$user_id} ) ) 
		AND `is_del` = 0 LIMIT 1";
		$tid = $this->_db->fetchOne( $sql );
		return intval($tid);
	}
	
	//新增消息
	public function addDiscountMessage( $getData , $userInfo ){
		$user_id = intval($getData["uid"]);
		$to_user_id = intval($getData["to_uid"]);
		$type = $getData["type"];
		$from_id = intval($getData["frid"]);
		
		$question = Custom_String::HtmlReplace($getData['content'],3);
		$threadParam = array(
				'user_id' => $userInfo['user_id'],
				'user_name' => $userInfo['user_name'],
				'avator' => $userInfo['Avatar50'],
				'question' => $question,
				'type' => $type,
				'from_id' => $from_id,
				'to_user_id' => $to_user_id,
				'ip' => CLIENT_IP,
				'created' => REQUEST_TIME,
				'updated' => REQUEST_TIME
		);
		$tid = $this->_db->insert('discount_message_thread', $threadParam);
		
		if($tid) {
			$postParam = array(
					'tid' => $tid,
					'user_id' => $userInfo['user_id'],
					'user_name' => $userInfo['user_name'],
					'avator' => $userInfo['Avatar50'],
					'question' => $question,
					'type' => $type,
					'first' => 1,
					'from_id' => $from_id,
					'to_user_id' => $to_user_id,
					'ip' => CLIENT_IP,
					'created' => REQUEST_TIME
			);
			$this->_db->insert('discount_message_post', $postParam);
			return $tid;
		}
		return false;
	}
	
	//追加消息
	public function appendDiscountMessage( $getData , $userInfo ){
		$tid = intval( $getData["tid"] );
		$user_id = intval($getData["uid"]);
		$to_user_id = intval($getData["to_uid"]);
		$type = $getData["type"];
		$from_id = intval($getData["frid"]);
		$question = Custom_String::HtmlReplace($getData['content'],3);
		$postParam = array(
				'tid' => $tid,
				'user_id' => $userInfo['user_id'],
				'user_name' => $userInfo['user_name'],
				'avator' => $userInfo['Avatar50'],
				'question' => $question,
				'type' => $type,
				'from_id' => $from_id,
				'to_user_id' => $to_user_id,
				'ip' => CLIENT_IP,
				'created' => REQUEST_TIME
		);
		$pid = $this->_db->insert('discount_message_post', $postParam);
		
		if($pid) {
			$threadParam = array(
					'floors' => $this->_db->fetchOne("select count(pid) from `discount_message_post` where `tid` = '{$tid}'"),
					'repler' => $userInfo['user_name'],
					'reply_time' => REQUEST_TIME,
					'updated' => REQUEST_TIME
					);
		
			$this->_db->update('discount_message_thread', $threadParam, array('tid' => $tid, 'from_id' => $from_id));
			return $pid;
		}
		return false;
	}
	//获取主题
	public function getThreadUserId($type, $tid, $from_id) {
		$where = "`tid` = '{$tid}' and `type` = '{$type}' and `from_id` = '{$from_id}'";
		$threadRow = $this->select($where, 'discount_message_thread', 'user_id', '', true);
	
		return $threadRow ? $threadRow : array();
	}
	//获取热门品牌，商场，用户
	public function getHotList( $getData , $city , $is_cache=false ){
		$type = $getData["type"];
		$page = intval($getData["page"]);
		$pageSize = intval($getData["pagesize"]);
		if($page<1) $page = 1;
		$start = ($page-1)*$pageSize;
		$user_id = intval($getData["uid"]);
		$pmark = $getData["pmark"];
		$cmark = $getData["cmark"];
		$key = $this->_key.$type."_".$user_id."_".$pmark."_".$cmark."_".$page."_".$city;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$data = array();
			$limit = "";
			if( $pageSize >　0 ){
				$limit = " LIMIT {$start},{$pageSize}";
			}
			switch ( $cmark ){
				case 'discount_brand_recommend':
					$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, $pmark, $cmark);
					$bids = $this->getConcernBrands($user_id);
					$sql = "select B.brand_id as id,B.brand_name_zh,B.brand_name_en,B.brand_logo as logo
					from `oto_recommend` A
					left join `oto_brand` B on A.come_from_id = B.brand_id
					where A.`pos_id` = '{$pos_id}' and A.`come_from_type` = '3' and A.`city` = '{$city}' ".($bids ? " and B.`brand_id` NOT IN({$bids})":"").
					" order by A.sequence asc, A.created desc {$limit}";
					$data = $this->_db->fetchAll($sql);
					foreach( $data as &$row ){
						$row["name"] = $row["brand_name_zh"]." ".$row["brand_name_en"];
						$row["logo"] = $row["logo"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $row["logo"] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] .'/images/wap/brand_default_icon.png';
						unset($row["brand_name_zh"],$row["brand_name_en"]);
					}
					break;
				case 'discount_market_recommend':
					$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, $pmark, $cmark);
					$mids = $this->getConcernMarkets($user_id);
					$sql = "select B.`market_id` AS id , B.`market_name` AS name , B.logo_img as logo , B.head_img , B.region_id, B.market_address , B.lng , B.lat
					from `oto_recommend` A
					left join `oto_market` B on A.come_from_id = B.market_id
					where A.`pos_id` = '{$pos_id}' and A.`come_from_type` = '4' and A.`city` = '{$city}' ".($mids ?" and B.`market_id` NOT IN({$mids})":"")
					." order by A.sequence asc, A.created desc {$limit}";
					$data = $this->_db->fetchAll($sql);
					$region_sql = "SELECT `region_id`,`region_name` FROM `oto_region` ";
					$regionObj = $this->_db->fetchPairs($region_sql);
					foreach( $data as &$row ){
						$row["logo"]     = $row["logo"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row["logo"] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] .'/images/wap/market_default_icon.png';
						$row["head_img"] = $row["head_img"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/market/' . $row["head_img"] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] .'/images/wap/market_default_head.png';
						$row["region_name"] = empty($regionObj[$row['region_id']])?"":$regionObj[$row['region_id']];
					}
					break;
				default:
					$uids = $this->getConcernUsers($user_id);
					$uids.= ",".$user_id;
					$uids = trim( $uids , ",");
					$sql  = "SELECT `user_id`,`user_name`,`uuid`,`discount_number`,`phone_number` FROM `oto_user` WHERE ".($uids?"`user_id` NOT IN ({$uids}) AND ":"")." `is_del` = 0 AND `user_status` =0 ORDER BY `fans_number` DESC {$limit}";
					$data = $this->_db->fetchAll($sql);
					$row["avatar"] = "";
					foreach( $data as &$row ){
						$avatarRow = $this->getUserAvatarByUuid( $row["uuid"] );
						if (!empty($avatarRow) && $avatarRow['Avatar50']) {
							$row["avatar"] = $avatarRow['Avatar50'];
						}
					}
					break;
			}
			$this->setData($key,$data);
		}
		return $data;
	}
	
	//热门品牌收藏
	public function addBrandsFav( $bids , $user_id ){
		$bids = trim($bids,",");
		$bidArr = explode(",", $bids);
		foreach($bidArr as $bid){
			$isFavorite = Model_Api_App::getInstance()->isFavorite('oto_brand_favorite', $bid, $user_id);
			if( $isFavorite ){
				continue;
			}
			$result = Model_Api_App::getInstance()->addFavorite('oto_brand_favorite', $bid, $user_id);
			if( $result ){
				$num = Model_Api_App::getInstance()->getFavoriteNum('oto_brand_favorite', $bid);
				$this->_db->update('oto_brand', array('favorite_number' => $num), "brand_id = '{$bid}'");
				//同步到oto_user_dynamic
				$brand = $this->_db->fetchRow("SELECT `brand_name_zh`,`brand_name_en` FROM `oto_brand` WHERE `brand_id`='{$bid}'");
				$this->syncFavoriteDynamic(array('user_id'=>$user_id, 'from_id'=>$bid, 'summary'=>trim($brand["brand_name_zh"])?$brand["brand_name_zh"]:$brand["brand_name_en"],'type'=>4, 'favorite_id'=>$result,'created'=>REQUEST_TIME));
			}
		}
		Model_Api_App::getInstance()->updateQuantityFavByUserId('oto_brand_favorite', $user_id);
		$this->updateUser(CLIENT_IP, $user_id);
		return true;
	}
	
	//热门商场收藏
	public function addMarketsFav( $mids , $user_id ){
		$mids = trim($mids,",");
		$midArr = explode(",", $mids);
		foreach($midArr as $mid){
			$isFavorite = Model_Api_App::getInstance()->isFavorite('oto_market_favorite', $mid, $user_id);
			if( $isFavorite ){
				continue;
			}
			$result = Model_Api_App::getInstance()->addFavorite('oto_market_favorite', $mid, $user_id);
			if( $result ){
				$num = Model_Api_App::getInstance()->getFavoriteNum('oto_market_favorite', $mid);
				$this->_db->update('oto_market', array('favorite_number' => $num), "market_id = '{$mid}'");
				//同步到oto_user_dynamic
				$marketName = $this->_db->fetchOne("SELECT `market_name` FROM `oto_market` WHERE `market_id`='{$mid}'");
				$this->syncFavoriteDynamic(array('user_id' => $user_id, 'from_id' => $mid, 'summary' => $marketName,'type'=>3, 'favorite_id'=>$result,'created' => REQUEST_TIME));	
			}
		}
		Model_Api_App::getInstance()->updateQuantityFavByUserId('oto_market_favorite', $user_id);
		$this->updateUser(CLIENT_IP, $user_id);
		return true;
	}
	
	//热门用户关注
	public function addUsersConcern( $uids , $from_user_id ){
		$uids = trim($uids,",");
		$uidArr = explode(",", $uids);
		foreach($uidArr as $to_user_id){
			if( $to_user_id == $from_user_id ){
				continue;
			}
			$sql = "select 1 from `oto_user_concerned` where `from_user_id` = '{$from_user_id}' and `to_user_id` = '{$to_user_id}' limit 1";
			$hadAttention = $this->_db->fetchOne($sql);
			if($hadAttention == 1) {
				continue;
			}
			$res = $this->_db->insert('oto_user_concerned', array('from_user_id' => $from_user_id, 'to_user_id' => $to_user_id, 'created' => REQUEST_TIME));
			if( $res ){
				$userInfo = $this->getUserByUserId($from_user_id ,"user_id,uuid,user_name, user_type");
				$avatar = $this->getUserAvatar($userInfo["user_name"]);
				Model_Api_Message::getInstance()->addPreNotice("system", "home_fans_list", 0 , array(
					"to_user_id"=> $to_user_id,
					"charter_user_id"=>$from_user_id,
					"charter_member"=>$userInfo["user_name"],
					"charter_member_avator"=>$avatar
				));
				Model_Api_User::getInstance()->updateFansNumberByUid($to_user_id);
				Model_Api_User::getInstance()->updateConcernedNumberByUid($from_user_id);
			}
		}
		return true;
	}
	
	//添加用户提醒
	public function addNoticeMe( $getData , $user_id ){
		$discount_id = intval($getData["did"]);
		$discountRow = $this->select("discount_id={$discount_id}",'discount_content',"title,stime","",1);
		if( empty($discountRow) ){
			return false;
		}
		$sql = "insert into `discount_notice` (`user_id`,`discount_id`,`title`,`stime`,`created`) 
				values ({$user_id},{$discount_id},'{$discountRow["title"]}','{$discountRow["stime"]}',".REQUEST_TIME.") 
				on duplicate key update `created` = `created`";
		$res = $this->_db->query($sql);
		return $res;
	}
}