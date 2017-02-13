<?php
class Model_Api_User extends Base
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
	
	/**
	 * 获取好友动态
	 * @param unknown_type $getData
	 * @param unknown_type $pageSize
	 * @param unknown_type $is_cache
	 */
	public function getFriendsDynamicList( $getData , $pageSize , $is_cache = false ){
		$user_id = intval($getData["uid"]);
		$login_user_id = intval($getData["login_user_id"]);
		$page = intval($getData["page"]);
		if( $page<1 ) $page=1;
		$key = $this->_key.$user_id."_".$page;
		$data = $this->getData( $key );
		if(!$is_cache || empty( $data )) {
			$start = ($page-1)*$pageSize;
			if( $user_id == $login_user_id ){
				$user_id_arr = $this->_db->fetchCol("select `to_user_id` from `oto_user_concerned` WHERE `from_user_id` = '{$user_id}'");
				$user_id_arr[] = $user_id;
				$sql = "SELECT A.`id` AS `dynamic_id`,A.*,B.`uuid`,B.`user_name`
						FROM `oto_user_dynamic` AS A
						LEFT JOIN `oto_user` AS B ON B.`user_id` = A.`user_id`
						WHERE A.`user_id` IN (".implode(",", $user_id_arr).")
						ORDER BY `created` DESC
						LIMIT {$start},{$pageSize}";
			}else{
				$sql = "SELECT A.`id` AS `dynamic_id`,A.*,B.`uuid`,B.`user_name`
						FROM `oto_user_dynamic` AS A
						LEFT JOIN `oto_user` AS B ON B.`user_id` = A.`user_id`
						WHERE A.`user_id`='{$user_id}'
						ORDER BY `created` DESC
						LIMIT {$start},{$pageSize}";
			}
			
			$dynamics = $this->_db->fetchAll( $sql );
			$list = array();
			foreach( $dynamics as $key => $row ){
				//获取动态记录所需要的数据
				$from_id = $row["from_id"];
				$row["avatar"] = "";
				if($row["uuid"]) {
					$avatarRow = $this->getUserAvatarByUuid ($row["uuid"] );
					if(!empty( $avatarRow ) && $avatarRow['Avatar50'] ) {
						$row["avatar"] = $avatarRow ['Avatar50'];
					}
				}
				switch ($row["type"]){
					case '1'://商品 
						$ticketRow = $this->_db->fetchRow("SELECT * FROM `oto_ticket` WHERE `ticket_id`='{$from_id}'");
						if( $ticketRow["ticket_status"]==1 && $ticketRow["is_auth"]==1 && $ticketRow["is_show"]==1 ){
							$wapImgList = Model_Admin_Ticket::getInstance()->getWapImg($from_id);
							$from_data = $this->getWapImgList($wapImgList, 'commodity');
							$row["from_data"] = $from_data;
							$row["is_like"] = 0;//否
							//获取按赞人列表
							$row["like_peoples"] = $this->getLikePeopleList($row["dynamic_id"]);
							unset($row["ip"],$row["favorite_id"],$row["id"]);
							$list[] = $row;
						}
						break;
					case '2'://店铺
						$from_data = $this->_db->fetchRow("SELECT `shop_name`,`shop_img`,`shop_address`,`shop_status` FROM `oto_shop` WHERE `shop_id`='{$from_id}'");
						if( $from_data["shop_status"]!=-1 ){
							$from_data["shop_img"] = $from_data["shop_img"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/shop/'. $from_data["shop_img"]:$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/data/default_shop.png';
							$row["from_data"] = $from_data;
							$row["is_like"] = 0;//否
							//获取按赞人列表
							$row["like_peoples"] = $this->getLikePeopleList($row["dynamic_id"]);
							unset($row["ip"],$row["favorite_id"],$row["id"]);
							$list[] = $row;
						}
						break;
					case '3'://商场
						$from_data = $this->_db->fetchRow("SELECT `market_name`,`region_id`,`head_img`,`market_address`,`lng`,`lat` FROM `oto_market` WHERE `market_id`='{$from_id}'");
						if( !empty($from_data) ){
							$region_name = $this->getRegion($from_data["region_id"]);
							$from_data["region_name"] = $region_name?$region_name:"";
							$from_data["head_img"] = $from_data["head_img"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/market/'.$from_data["head_img"]:$GLOBALS['GLOBAL_CONF']['SITE_URL'].'/images/wap/market_default_head.png';
							$row["from_data"] = $from_data;
							$row["is_like"] = 0;//否
							//获取按赞人列表
							$row["like_peoples"] = $this->getLikePeopleList($row["dynamic_id"]);
							unset($row["ip"],$row["favorite_id"],$row["id"]);
							$list[] = $row;
						}
						break;
					case '4'://品牌
						$from_data = $this->_db->fetchRow("SELECT `brand_name_zh`,`brand_name_en`,`brand_head` FROM `oto_brand` WHERE `brand_id`='{$from_id}'");
						if( !empty($from_data) ){
							$from_data["brand_head"] = $from_data["brand_head"]?$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/brand/'.$from_data["brand_head"]:$GLOBALS['GLOBAL_CONF']['SITE_URL'].'/images/wap/img_brand_default.png';
							$row["from_data"] = $from_data;
							$row["is_like"] = 0;//否
							//获取按赞人列表
							$row["like_peoples"] = $this->getLikePeopleList($row["dynamic_id"]);
							unset($row["ip"],$row["favorite_id"],$row["id"]);
							$list[] = $row;
						}
						break;
					case '5'://收藏折扣
					case '6'://发布折扣
					case '7'://浏览折扣
						$discountRow = $this->_db->fetchRow("SELECT * FROM `discount_content` WHERE`discount_id`='{$from_id}'");
						if( $discountRow["discount_status"]!=-1 && $discountRow["is_del"]==0 ){
							$wapImgList = $this->select ( "`discount_id` = '{$from_id}'", 'discount_wap_img', '*', 'sequence asc, created asc' );
							$from_data = $this->getWapImgList($wapImgList, 'discount');
							$row["from_data"] = $from_data;
							$row["is_like"] = 0;//否
							//获取按赞人列表
							$row["like_peoples"] = $this->getLikePeopleList($row["dynamic_id"]);
							unset($row["ip"],$row["favorite_id"],$row["id"]);
							$list[] = $row;
						}
						break;
				}
			}
			$user = array();
			if( $page==1 ){
				$user = $this->getUserByUserId($user_id,"user_name, uuid, fans_number, concerned_user_number");
				$user["avatar"] = "";
				if($user["uuid"]) {
					$avatarRow = $this->getUserAvatarByUuid ($user["uuid"] );
					if(!empty( $avatarRow ) && $avatarRow['Avatar50'] ) {
						$user["avatar"] = $avatarRow ['Avatar50'];
					}
				}
				$user["fans_number"] = $this->getFansNum($user_id);
				$user["concerned_user_number"] = $this->getConcenedUsersNum($user_id);
				$user["is_concern"] = 0;
			}
			$data = array("list"=>$list,"user"=>$user);
			$this->setData($key, $data);
		}
		foreach( $data["list"] as &$row ){
			//格式化时间
			$row["created"] = Custom_Time::getTime4($row["created"]);
			//检查该用户是否按赞
			if( $login_user_id )
				$row["is_like"] = $this->isLike($row["dynamic_id"], $login_user_id);
		}
		if( !empty($data["user"]) ){
			if( $user_id==$login_user_id  ){
				$data["user"]["is_concern"] = -1;
			}else if( 1==$this->_db->fetchOne("SELECT 1 FROM `oto_user_concerned` WHERE `from_user_id`='{$login_user_id}' AND `to_user_id`='{$user_id}'") ){
				$data["user"]["is_concern"] = 1;
			}
		}
		return $data;
	}
	
	/**
	 * 获取粉丝数量
	 * @param unknown_type $user_id
	 */
	public function getFansNum($user_id){
		return $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_user_concerned` WHERE `to_user_id`='{$user_id}'");
	}
	
	/**
	 * 获取用户关注数
	 * @param unknown_type $user_id
	 */
	public function getConcenedUsersNum($user_id){
		return $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_user_concerned` WHERE `from_user_id`='{$user_id}'");
	}
	
	/**
	 * 改变动态按赞数量
	 * @param unknown_type $chat_id
	 */
	public function updtDynamicPraiseQuantity( $dynamic_id ){
		$count = $this->_db->fetchOne("SELECT count(*) FROM `oto_user_dynamic_like` WHERE `dynamic_id` = {$dynamic_id}");
		return $this->_db->update('oto_user_dynamic', array('like'=>$count) , "`id` = '{$dynamic_id}'");
	}
	
	/**
	 * 是否对该动态按赞
	 * @param unknown_type $dynamic_id 动态id
	 * @param unknown_type $user_id 登录用户id
	 */
	private function isLike( $dynamic_id , $user_id ){
		return $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_user_dynamic_like` WHERE `dynamic_id`='{$dynamic_id}' AND `user_id`='{$user_id}'");
	}
	
	/**
	 * 根据动态id获取对该动态按赞的人员列表
	 * @param int $dynamic_id 动态id
	 */
	private function getLikePeopleList( $dynamic_id ){
		$sql = "SELECT B.`user_id`,B.`user_name` 
				FROM `oto_user_dynamic_like` AS A 
				LEFT JOIN `oto_user` AS B ON B.`user_id`=A.`user_id` 
				WHERE `dynamic_id`='{$dynamic_id}'";
		return $this->_db->fetchAll($sql);
	}
	
	/**
	 * 获取各种尺寸的图片列表
	 * @param array $imgList 图片列表
	 * @param string $folder 文件夹
	 * @param array $img_w 需要获取的图片尺寸
	 */
	private function getWapImgList( $imgList , $folder , $img_w = array(640,400,240) ){
		$originalWapImgArr = $thumbWapImgArr = array();
		$i = 0;
		foreach ($imgList as $wapImgItem){
			$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight($wapImgItem['img_url'], $folder);
			if( empty($widthHeightRow['width']) || empty($widthHeightRow['height']) ){
				continue;
			}
			$originalWapImgArr[] = array(
					'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'] . "/buy/discount/" . $wapImgItem['img_url'],
					'width' => $widthHeightRow['width'],
					'height' => $widthHeightRow['height']
			);
			foreach( $img_w as $w ){
				$widthHeightRow = array();
				$widthHeightRow = Model_Api_App::getInstance()->getImageWidthHeight( $wapImgItem['img_url'], $folder, $w );
				$thumbWapImgArr[$i]['W'.$w] = array (
						'img_url' => $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/iid/{$wapImgItem['id']}/w/{$w}/type/{$folder}",
						'width' => empty($widthHeightRow['width'])?0:$widthHeightRow['width'],
						'height' => empty($widthHeightRow['height'])?0:$widthHeightRow['height']
						);
			}
			$i++;
		}
		return array("original_wap_img"=>$originalWapImgArr,"thumb_wap_img"=>$thumbWapImgArr);
	}
	
	/**
	 * 修改粉丝数量
	 * @param unknown_type $user_id
	 */
	public function updateFansNumberByUid($user_id){
		$sql = "SELECT COUNT(*) FROM `oto_user_concerned` WHERE `to_user_id` = {$user_id}";
		$fansNumber = $this->_db->fetchOne($sql);
		$this->_db->update('oto_user', array('fans_number'=>$fansNumber), array("user_id"=>$user_id));
	}
	
	/**
	 * 修改关注用户数量
	 * @param unknown_type $user_id
	 */
	public function updateConcernedNumberByUid($user_id){
		$sql = "SELECT COUNT(*) FROM `oto_user_concerned` WHERE `from_user_id` = {$user_id}";
		$concernedNumber = $this->_db->fetchOne($sql);
		$this->_db->update('oto_user', array('concerned_user_number'=>$concernedNumber), array("user_id"=>$user_id));
	}
	
	/**
	 * 用户粉丝列表
	 * @param unknown_type $getData
	 * @param unknown_type $pageSize
	 * @param unknown_type $is_cache
	 */
	public function getFansList( $getData , $pageSize , $is_cache = false ){
		$login_user_id = intval($getData["login_user_id"]);
		$user_id = intval($getData["uid"]);
		$page = intval($getData["page"]);
		if($page<1) $page=1;
		$start = ($page-1)*$pageSize;
		$key = $this->_key.$user_id."_".$page;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$sql = "SELECT B.`user_id`,B.`uuid`,B.`user_name` FROM `oto_user_concerned` AS A 
					LEFT JOIN `oto_user` AS B 
					ON B.`user_id`=A.`from_user_id` 
					WHERE A.`to_user_id`='{$user_id}' ORDER BY A.`created` DESC LIMIT {$start},{$pageSize}";
			$users = $this->_db->fetchAll($sql);
			foreach( $users AS &$row ){
				$row["avatar"] = "";
				if($row["uuid"]) {
					$avatarRow = $this->getUserAvatarByUuid ($row["uuid"] );
					if(!empty( $avatarRow ) && $avatarRow['Avatar50'] ) {
						$row["avatar"] = $avatarRow ['Avatar50'];
					}
				}
				$row["is_concern"] = "0";
			}
			$fans_number = $this->_db->fetchOne("SELECT `fans_number` FROM `oto_user` WHERE `user_id`='{$user_id}'");
			$data = array("fans_number"=>$fans_number,"list"=>$users);
			$this->setData($key, $data);
		}
		foreach( $data["list"] as &$row ){
			if( $login_user_id==$row["user_id"]  ){
				$row["is_concern"] = "-1";
			}else if( 1==$this->_db->fetchOne("SELECT 1 FROM `oto_user_concerned` WHERE `from_user_id`='{$login_user_id}' AND `to_user_id`='{$row["user_id"]}'") ){
				$row["is_concern"] = "1";
			}
		}
		return $data;
	}
	
	/**
	 * 获取用户关注列表
	 * @param unknown_type $getData
	 * @param unknown_type $pageSize
	 * @param unknown_type $is_cache
	 */
	public function getConernedList( $getData , $pageSize , $is_cache = false ){
		$login_user_id = intval($getData["login_user_id"]);
		$user_id = intval($getData["uid"]);
		$page = intval($getData["page"]);
		if($page<1) $page=1;
		$start = ($page-1)*$pageSize;
		$key = $this->_key.$user_id."_".$page;
		$data = $this->getData($key);
		if( !$is_cache || empty($data) ){
			$sql = "SELECT B.`user_id`,B.`uuid`,B.`user_name` FROM `oto_user_concerned` AS A
			LEFT JOIN `oto_user` AS B
			ON B.`user_id`=A.`to_user_id`
			WHERE A.`from_user_id`='{$user_id}' LIMIT {$start},{$pageSize}";
			$user = $this->_db->fetchAll($sql);
			foreach( $user AS &$row ){
				$row["avatar"] = "";
				if($row["uuid"]) {
					$avatarRow = $this->getUserAvatarByUuid ($row["uuid"] );
					if(!empty( $avatarRow ) && $avatarRow['Avatar50'] ) {
						$row["avatar"] = $avatarRow ['Avatar50'];
					}
				}
				$row["is_concern"] = "0";
			}
			$concerned_user_number = $this->getConcenedUsersNum($user_id);
			$data = array("concerned_user_number"=>$concerned_user_number,"list"=>$user);
			$this->setData($key, $data);
		}
		foreach( $data["list"] as &$row ){
			if( $login_user_id==$row["user_id"]  ){
				$row["is_concern"] = "-1";
			}else if( 1==$this->_db->fetchOne("SELECT 1 FROM `oto_user_concerned` WHERE `from_user_id`='{$login_user_id}' AND `to_user_id`='{$row["user_id"]}'") ){
				$row["is_concern"] = "1";
			}
		}
		return $data;
	}
	/**
	 * 获取用户权限
	 * @param unknown_type $getData
	 */
	public function getUserRight($getData) {
		$data = array();
		$uuid = $getData['uuid'];
		$userInfo = $this->getWebUserId($uuid);
		$userRow = $this->select_one("`user_id` = '{$userInfo['user_id']}'", 'oto_user_shop_commodity');
		if(empty($userRow) || $userRow['user_type'] == 3) {
			_sexit('用户不是营业员，也不是店长', 300);
		}
		
		$data['right'] = $this->select_one("`user_id` = '{$userInfo['user_id']}'", 'oto_user_right');
		$sql = "select * from `oto_ticket_shop` where `shop_id` = '{$userRow['shop_id']}'";
		$data['ticket'] = $this->_db->fetchCol($sql);
		$data['shop'] = $this->select_one("`shop_id` = '{$userRow['shop_id']}'", 'oto_shop', 'shop_id, shop_name');
		return $data;
	}
}