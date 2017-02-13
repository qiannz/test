<?php
class Controller_Home_Post extends Controller_Home_Abstract {
	
	public function __construct() {
		parent::__construct();
	}
	
	protected function authAll($getData) {
		$SignData = $getData['SignData'];
		unset($getData['m']);
		unset($getData['c']);
		unset($getData['act']);
		unset($getData['api']);
		unset($getData['SignData']);
	
		ksort($getData);
		$md5String = '';
		foreach ($getData as $k => $v) {
			$md5String .= "{$k}=".urldecode($v)."&";
		}
		$md5String = substr($md5String, 0, -1);
		$desString = 'sign=' . md5($md5String) . '&time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
	
		Third_Des::$key = '34npzntC';
		return Third_Des::encrypt($desString);
	}
	
	function auth($getData) {
		$encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
		Third_Des::$key = '34npzntC';
		return Third_Des::encrypt($encryptString);
	}
	
	//http://local.buy.mplife.com/api/discount/discount-add/title/1233/stime/2015-08-28/etime/2015-09-01/address/%E4%B8%8A%E6%B5%B7%E5%B8%82%E9%97%B8%E5%8C%97%E5%8C%BA%E5%A4%A9%E7%9B%AE%E4%B8%AD%E8%B7%AF344%E5%8F%B7/uuid/1b1a3484-7928-4972-a3f4-1e2d45e33b7c/uname/leofan05?debug=buy
	public function indexAction() {
		print_r($this->sendMessageTwo('18321969821', '您的验证码为：1388'));
		exit();
		$data = array(
// 				'uuid'=>'dc7891ba-9ba7-4d98-902c-f17ac3a40f24',
// 				'uname'=>'amyduan',
				'mobile'=>'18321969821'
				);
		$SignData = $this->auth($data);
		$data["ssid"] = $SignData;
		
		$SignData = $this->authAll($data);
		$data["SignData"] = $SignData;
		$res = Core_Http::sendRequest("http://api.mpshop.com/api/appclient/send-code",
				$data,
				'CURL-POST'
		);
		//print_r($res);
		print_r(json_decode($res,1));
	}
	
	public function sendMessageTwo($phone, $message, $time = null){
    	// 短信通道
    	$client = new SoapClient('http://webservice.mplife.com/smswebservice/SmsHelper.asmx?wsdl');
    	$auth = array('UserName' => 'crowdfunding', 'Password' => 'F6049729E87B4C42C42C35E7C4938371D099E3F4D598AD04');
    	$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://api.mplife.com/');
    	$header =  new SoapHeader('http://api.mplife.com/', "Auths", $authvalues, false);
    	$client->__setSoapHeaders(array($header));
    	$param = array(
    			'phones'      => $phone,
    			'content'     => $message,
    			'sendtime'    => $time,
    	);
    	$call_val = $client->SendSms($param);
    	$arr = objectToArray($call_val);
    	return $arr;
    }
	
	private function verify($getData) {
		Third_Des::$key = 'IN0xMmwV';
		$ssid = $getData['sid'];
		$encrypt_uuid = $getData['uuid'];
		$time = $getData['time'];
	
		$decrypt_ssid =  Third_Des::encrypt($encrypt_uuid . '|'. $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'] . '|' . $time);
		$decrypt_ssid = urldecode($decrypt_ssid);
		return $decrypt_ssid;
	}
	
	public function syncDiscountAction(){
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
}