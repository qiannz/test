<?php
class Controller_Home_Test extends Controller_Home_Abstract {
	
	public function indexAction() {		
		/*
		require_once ROOT_PATH . 'lib/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		
		
		$inputFileName = WEB_ROOT . 'data/2016.xlsx';
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		
		
		echo '<hr />';
		
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		print_r($sheetData);
		*/
		//print_r(Custom_Send::sendMessage('15000692900', '您的验证码是：1121'));
		//echo $token = Custom_AuthLogin::getUrlToken('15000692900', '1454567356');
		
		//$data = Custom_AuthTicket::getOrderListToUser(array('mobile' => '15000692900'));
		//print_r($data);
		
		/*
		$data = Custom_AuthTicket::getOrderInfoToShop(
			array(
				'shop_id' => 3071,
				'order_no' => 'WAP1126442835'
			)
		);		
		print_r($data);
		*/
		//print_r(Custom_AuthLogin::get_user_by_mobile('13816016273'));
		//print_r(Custom_AuthTicket::getMerchantStatInfoToUser(array('shop_id' => 4348)));
		//print_r(Custom_AuthLogin::get_user_by_uuid('99191036-304c-423b-b407-990dd86635a6'));
		//$userArr = array_unique($userArr);
		//print_r($userArr);exit;
		/*
		foreach($userArr as $uuid) {
			print_r($this->getWebUserId($uuid));
		}
		
		//Third_Des::$key = '34npzntC';
		//echo Third_Des::decrypt('cV3iZw1SzEehG/e3Oj039NLJJBuuKHbGrSeN89c2IqgP0a5opAQvPp9yYR0jp+or/2fc3CjUUPs=');
		//print_r(Custom_AuthLogin::get_user_by_mobile('13262219995')); //13524660526    18516153088
		//print_r(Custom_AuthTicket::getUserTicketOne('qiannz', 'WAP1334321532'));
		//print_r(Custom_AuthTicket::getTicketDetailByTicketUuid('9116cbf7-e36e-4566-b5f0-bb14d25f75d1'));				
		print_r(Custom_AuthLogin::get_user_by_uuid('5af156c0-f3c0-4b5a-b313-6e540f1aaffe'));
		//$ticketObj = Custom_AuthTicket::get_ticket_details_by_guid('a6d1824e-2f26-4947-9e53-17be31b049df');
		
		//echo $ticketObj->data->Avtivities[0]->ProductStock;
		//Custom_Send::sendMobileMessage('13524239755','恭喜您13524239755获得第十届婴童节满50减50元优惠券，请于婴童节活动期间至现场收银台凭此短信使用，转发无效。');
		/*
		$data = array (
			  'frid' => '41',
			  'qst' => 'Hello',
			  'tid' => '11',
			  'time' => '1444788264',
			  'type' => 'voucher',
			  'uname' => 'CANDY1014',
			  'uuid' => '4e89f77d-2907-43d3-ba68-36a2f783760d',
			);
		
		print_r(Core_Http::sendRequest('http://local.buy.mplife.com/api/message/append-personal-message', $data, 'CURL-POST'));
		
		*/
	}
	
	public function syncDiscountAction(){
		exit();
		$pageSize = 100;
		$dl_db = Core_DB::get('discount_line', null, true);
		$limit = " LIMIT 0 , {$pageSize} ";
		$sql = "SELECT *
				FROM `discounts_content` 
				WHERE `is_sync` = 0 AND `status` <> '-2' AND `is_del` = 1 ORDER BY `dis_id` ASC $limit";
		$res = $dl_db->fetchAll($sql);
		if( empty( $res ) ){
			echo "没有需要处理的了~~~";exit();			
		}
		$dids = array();
		foreach ( $res as $row){
			$dids[] = $row["dis_id"];
		}
		$dl_db->query("UPDATE `discounts_content` SET `is_sync` = 1 WHERE `dis_id` in (".implode(",", $dids).")");
		$city = 'sh';
		foreach ( $res as $row){
			$lng = $lat = 0;
			$lngLatString = $this->getLatitudeAndLongitudeFormamap($row["address"], $city);
			if($lngLatString) {
				list($lng, $lat) = explode(',', $lngLatString);
			}
			if( !trim($row["user_name"]) ){
				continue;
			}
			$user_id = $this->getUserIdByUserName($row["user_name"]);
			if( intval($user_id) == 0 ){
				continue;
			}
			$content = strip_tags($row["content"]);
			$content = str_replace("　", ' ', $content);
    		$content = preg_replace("/[\r\n\t ]{1,}/", ' ', $content);
			$content = mysql_escape_string($content);
			$data = array(
					"title"=>mysql_escape_string($row["title"]),
					"user_id"=>$user_id,
					"user_name"=>$row["user_name"],
					"stime"=>$row["start_time"],
					"etime"=>$row["end_time"],
					"address"=>mysql_escape_string($row["address"]),
					"lng"=>$lng,
					"lat"=>$lat,
					"category_id"=>$this->getCategoryIdByUUid($row["category_id"]),
					"discount_start"=>$row["discount_num1"],
					"discount_end"=>$row["discount_num2"],
					"promotion"=>mysql_escape_string($row["promotions"]),
					"market_id"=>$this->getMarketIdByName($row["market"]),
					"discount_status"=>($row["status"]==1)?1:0,
					"wap_content"=> $content,
					"linker"=>mysql_escape_string($row["contact_man"]),
					"telephone"=>mysql_escape_string($row["contact_phone"]),
					"city"=>$city,
					"created"=>$row["created"],
					"updated"=>$row["created"]
					);
			$last_insert_id = $this->_db->insert("discount_content",$data);
			if( !$last_insert_id ){
				continue;
			}
			
			//获取图片
			$sql = "SELECT `dis_id`,`user_id`,`img_url`,`created` FROM `discounts_img` WHERE `dis_id` = {$row["dis_id"]}";
			echo $sql."<br/>";
			$imgs = $dl_db->fetchAll($sql);
			$img_sql = "";
			if( !empty($imgs) ){
				// 原始图路径
				$imgOrigPath = ROOT_PATH.'../DiscountReport/web/data/line/original/';
				// 拷贝图路径
				$imgBuyCopyPath = ROOT_PATH.'web/data/buy/discount/';
				$buyDir = $imgBuyCopyPath.date('Y').'/'.date('m').'/'.date('d').'/';
				foreach ( $imgs as $imgRow){
					$filePath = $imgOrigPath.$imgRow["img_url"];
					echo $filePath."<br/>";
					if( file_exists($filePath) ){
						$baseName = basename($filePath);
						$this->imageCopy($buyDir, $baseName, $filePath);
						$img_url = str_replace($imgBuyCopyPath, '', $buyDir.$baseName);
						$img_sql .= " ('{$last_insert_id}','{$imgRow["user_id"]}','{$img_url}',".REQUEST_TIME."),";
					}
				}
				if( $img_sql ){
					$img_sql = substr($img_sql, 0 , -1);
					$img_sql = "INSERT INTO `discount_wap_img`(`discount_id`,`user_id`,`img_url`,`created`) VALUES ".$img_sql;
					echo $img_sql."<br/>";
					$this->_db->query($img_sql);
				}
			}
			
			if( $row["brand_name"] )
				$this->copyDiscountBrand($last_insert_id, $row["brand_name"]);
			$dl_db->query("UPDATE `discounts_content` SET `is_sync` = 2 WHERE `dis_id` = {$row["dis_id"]} ");
		}
		
		$url = $GLOBALS['GLOBAL_CONF']["SITE_URL"]."/home/test/sync-discount/t/" . REQUEST_TIME;
		echo "<script type=\"text/javascript\">setTimeout(function(){window.location = '{$url}';}, 1000)</script>";
		//Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']["SITE_URL"]."/home/test/sync-discount", 3);
	}
	
	PUBLIC function imageCopy($dir, $baseName, $tmpFileName) {
		if(create_folders($dir)) {
			copy($tmpFileName, $dir.$baseName);
		}
	}
	
	/**
	 * 拷贝wap图片
	 * @param unknown_type $last_insert_id
	 * @param unknown_type $user_id
	 * @param unknown_type $dis_id
	 * @param unknown_type $dl_db
	 */
	private function copyWapImg( $last_insert_id , $user_id , $dis_id , &$dl_db ){
		$sql = " SELECT `img_url` , `created`  
				FROM `discounts_img` WHERE `dis_id` = {$dis_id}";
		echo $sql;
		$img_list = $dl_db->fetchAll($sql);
		foreach( $img_list as $imgRow ){
			//拷贝图片到指定目录
			$imgRow["discount_id"] = $last_insert_id;
			$imgRow["user_id"] = $user_id;
			$this->_db->insert("discount_img",$imgRow);
		}
	}
	
	/**
	 * 获取category id
	 * @param unknown_type $uid
	 */
	private function getCategoryIdByUUid( $uuid ){
		$sortId = $this->_db->fetchOne("SELECT `sort_id` from `oto_sort` WHERE `sort_unique` = 'discountcategory'");
		return $this->_db->fetchOne("SELECT `sort_detail_id` FROM `oto_sort_detail` WHERE `sort_id`='{$sortId}' AND  `sort_detail_mark` = '{$uuid}'");
	}
	
	
	/**
	 * 获取market id 
	 * @param unknown_type $name
	 */
	private function getMarketIdByName( $name ){
		return $this->_db->fetchOne("SELECT `market_id` FROM `oto_market` WHERE `market_name` = '{$name}'");
	}
	
	/**
	 * 拷贝折扣与品牌的关系
	 * @param unknown_type $last_insert_id 折扣id
	 * @param unknown_type $bnames 多个品牌名称
	 */
	private function copyDiscountBrand( $last_insert_id , $bnames ){
		$bnames = str_replace("，", ",", $bnames);
		$bnameArr = explode(",", $bnames);
		$where = "";
		foreach( $bnameArr as $bname ){
			if( $bname )
				$where .= " `brand_name_zh` LIKE '%{$bname}%' OR `brand_name_en` LIKE '%{$bname}%' OR";
		}
		if( $where ){
			$where = substr($where, 0 , -2);
			$sql = " SELECT `brand_id` FROM `oto_brand` WHERE {$where} ";
			$bidsRes = $this->_db->fetchCol($sql);
			$insertSql = "";
			foreach( $bidsRes as $bid ){
				$insertSql .= " ('{$last_insert_id}','{$bid}'),";
			}
			if( $insertSql ){
				$insertSql = "INSERT INTO `discount_brand`(`discount_id`,`brand_id`) VALUES ".$insertSql;
				$insertSql = substr($insertSql, 0 , -1);
				$this->_db->query($insertSql);
			}
		}
	}
	
	
	/**
	 * 获取指定类型的宣传标语
	 * @param unknown_type $type 类型
	 */
	private function getSlogansByType( $type ){
		$sql = "SELECT `name` FROM `oto_slogan` WHERE `is_del`='0' AND `category`='{$type}'";
		return $this->_db->fetchCol($sql);
	}

	/**
	 * 根据不同类型的sql取出数据
	 * @param unknown_type $sql 获取数据的sql语句
	 * @param unknown_type $type 类型 1：商品，2：店铺，3：商场，4：品牌，5：收藏折扣，6：发布折扣，7：取消折扣
	 */
	private function syncToDynamic( $sql , $type ){
		echo $sql."<br/>";
		$data = $this->_db->fetchAll($sql);
		if( empty( $data ) ){
			echo "没有需要处理的了~~~";exit();
		}
		$slogans = $this->getSlogansByType($type);
		if( count($slogans) == 0 ){
			echo "<span style='color:red'>抱歉，没有该类型所需的宣传标语，请添加宣传标语</span>";exit();
		}
		$insert_sql = '';
		foreach( $data as $row ){
			$indx = array_rand($slogans);
			$slogan = $slogans[$indx];
			$title = '';
			if( $type == 4 ){//品牌 
				$title = trim($row["brand_name_zh"])?$row["brand_name_zh"]:$row["brand_name_en"];
			}else{
				$title = $row["title"];
			}
			$summary = str_replace('{name}', $title , $slogan);
			$summary = mysql_escape_string($summary);
			$insert_sql .= "('{$row["user_id"]}','{$row["from_id"]}','{$summary}','{$type}','{$row["created"]}','{$row["favorite_id"]}'),";
		}
		if( $insert_sql ){
			$insert_sql = "INSERT INTO `oto_user_dynamic`(`user_id`,`from_id`,`summary`,`type`,`created`,`favorite_id`) VALUES ".substr($insert_sql, 0 , -1);
			$insert_sql .= " ON DUPLICATE KEY UPDATE `summary` = values(`summary`), `created`= values(`created`) , `favorite_id`=values(`favorite_id`) ";
			echo $insert_sql."<br/>";
			$this->_db->query($insert_sql);
		}
	}
	
	//同步按赞数据
	public function syncDynamicAction(){
		$type = $this->_http->get('type');
		$pageSize = 100;
		switch ($type){
			case '1'://同步oto_ticket_favorite到oto_user_dynamic表
				$commodityId = $this->getTicketSortById(0,'ticketsort','commodity');
				$sql = "SELECT A.`favorite_id`,A.`user_id`,A.`ticket_id` AS from_id,A.`created`,B.`ticket_title` AS title
						FROM (
							SELECT * FROM `oto_ticket_favorite`
							WHERE `favorite_id`
							NOT IN (
								SELECT `favorite_id`
								FROM `oto_user_dynamic`
								WHERE `type`='{$type}'
							)
						) AS A
						LEFT JOIN `oto_ticket` AS B ON B.`ticket_id` = A.`ticket_id`
						WHERE B.`ticket_title`<>'' AND B.`ticket_type` = {$commodityId}
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			case '2'://同步oto_shop_favorite到oto_user_dynamic表
				$sql = "SELECT A.`favorite_id`,A.`user_id`,A.`shop_id` AS from_id,A.`created`,B.`shop_name` AS title
						FROM (
							SELECT * FROM `oto_shop_favorite`
							WHERE `favorite_id`
							NOT IN (
								SELECT `favorite_id`
								FROM `oto_user_dynamic`
								WHERE `type`='{$type}'
							)
						) AS A
						LEFT JOIN `oto_shop` AS B ON B.`shop_id` = A.`shop_id`
						WHERE B.`shop_name`<>''
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			case '3'://同步oto_market_favorite到oto_user_dynamic表
				$sql = "SELECT A.`favorite_id`,A.`user_id`,A.`market_id` AS from_id,A.`created`,B.`market_name` AS title
						FROM (
							SELECT * FROM `oto_market_favorite`
							WHERE `favorite_id`
							NOT IN (
								SELECT `favorite_id`
								FROM `oto_user_dynamic`
								WHERE `type`='{$type}'
							)
							) AS A
						LEFT JOIN `oto_market` AS B ON B.`market_id` = A.`market_id`
						WHERE B.`market_name`<>''
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			case '4'://同步oto_brand_favorite到oto_user_dynamic表
				$sql = "SELECT A.`favorite_id`,A.`user_id`,A.`brand_id` AS from_id,A.`created`,B.`brand_name_zh`,B.`brand_name_en`
						FROM (
							SELECT * FROM `oto_brand_favorite`
							WHERE `favorite_id`
							NOT IN (
								SELECT `favorite_id`
								FROM `oto_user_dynamic`
								WHERE `type`='{$type}'
							)
						) AS A
						LEFT JOIN `oto_brand` AS B ON B.`brand_id` = A.`brand_id`
						WHERE B.`brand_name_zh`<>'' OR B.`brand_name_en`<>''
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			case '5'://同步discount_favorite到oto_user_dynamic表
				$sql = "SELECT A.`favorite_id`,A.`user_id`,A.`discount_id` AS from_id,A.`created`,B.`title`
						FROM (
							SELECT * FROM `discount_favorite`
							WHERE `favorite_id`
							NOT IN (
								SELECT `favorite_id`
								FROM `oto_user_dynamic`
								WHERE `type`='{$type}'
							)
						) AS A
						LEFT JOIN `discount_content` AS B ON B.`discount_id` = A.`discount_id`
						WHERE B.`title`<>''
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			case '6'://同步discount_content到oto_user_dynamic表
				$sql = "SELECT `discount_id` AS favorite_id,`user_id`,`discount_id` AS from_id,`created`,`title`
						FROM `discount_content`
						WHERE `discount_id`
						NOT IN (
							SELECT `favorite_id`
							FROM `oto_user_dynamic`
							WHERE `type`='{$type}'
						)
						AND `title`<>''
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			case '7'://同步discount_visit到oto_user_dynamic表
				$sql = "SELECT A.`id` AS favorite_id,A.`user_id`,A.`discount_id` AS from_id,A.`created`,B.`title`
						FROM (
							SELECT * FROM (
								SELECT * FROM ( 
										SELECT * FROM `discount_visit` ORDER BY `created` DESC 
								) AS C GROUP BY C.discount_id,C.user_id 
							) AS D
							WHERE D.`id`
							NOT IN (
								SELECT `favorite_id`
								FROM `oto_user_dynamic`
								WHERE `type`='{$type}'
							)
						) AS A
						LEFT JOIN `discount_content` AS B ON B.`discount_id` = A.`discount_id`
						WHERE B.`title`<>''
						LIMIT 0,{$pageSize}";
				$this->syncToDynamic($sql, $type);
				break;
			default:
				echo "<span style='color:red'>抱歉，type值不对</span>";
				exit();
		}
		$url = $GLOBALS['GLOBAL_CONF']["SITE_URL"]."/home/test/sync-dynamic/type/{$type}/t/" . REQUEST_TIME;
		echo "<script type=\"text/javascript\">setTimeout(function(){window.location = '{$url}';}, 1000)</script>";
	}
	public function syncUserConcernedNumberAction(){
		$pageSize = 100;
		$page = intval($this->_http->get('page'));
		if( $page < 1 ){
			$page = 1;
		}
		$start = ($page-1)*$pageSize;
		$sql = "SELECT * FROM `oto_user` LIMIT {$start},{$pageSize}";
		$result = $this->_db->fetchAll( $sql );
		if( count($result) ==0){
			echo "没有需要处理的了";exit();
		}
		foreach( $result as $row ){
			$concernedNumber = $this->_db->fetchOne("SELECT COUNT(*) FROM `oto_user_concerned` WHERE `from_user_id`='{$row["user_id"]}'"); 
			$this->_db->query("UPDATE `oto_user` SET `concerned_user_number` = '{$concernedNumber}' WHERE `user_id`='{$row["user_id"]}'");
		}
		$page++;
		$url = $GLOBALS['GLOBAL_CONF']["SITE_URL"]."/home/test/sync-user-concerned-number/page/{$page}/t/" . REQUEST_TIME;
		echo "<script type=\"text/javascript\">setTimeout(function(){window.location = '{$url}';}, 1000)</script>";
	}
	//同步折扣关注数（discount_content 到 discount_concerned_number）
	public function syncDiscountConcernedNumberAction(){
		$pageSize = 100;
		$page = intval($this->_http->get('page'));
		if( $page < 1 ){
			$page = 1;
		}
		echo "page:{$page}<br/>";
		$start = ($page-1)*$pageSize;
		$sql = "SELECT `discount_id`,`concern_number` FROM `discount_content` 
				WHERE `concern_number`>0 LIMIT {$start},{$pageSize}";
		$result = $this->_db->fetchAll( $sql );
		if( count($result) ==0){
			echo "没有需要处理的了";exit();
		}
		$insert_sql = "";
		foreach( $result as $row ){
			$insert_sql .= "('".implode("','", $row)."'),";
		}
		$insert_sql = trim($insert_sql,",");
		$insert_sql = "INSERT INTO `discount_concerned_number` VALUES".$insert_sql." 
						ON DUPLICATE KEY UPDATE `concern_number` = VALUES(`concern_number`)";
		$this->_db->query($insert_sql);
		$page++;
		$url = $GLOBALS['GLOBAL_CONF']["SITE_URL"]."/home/test/sync-discount-concerned-number/page/{$page}/t/" . REQUEST_TIME;
		echo "<script type=\"text/javascript\">setTimeout(function(){window.location = '{$url}';}, 1000)</script>";
	}
}

