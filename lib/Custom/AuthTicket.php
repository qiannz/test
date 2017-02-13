<?php
/**
 * 同步验证
 *
 */
class Custom_AuthTicket {
	private static $ticketTypeLabel = 'MERCHANT';
	/**
	 * 跟主站同步验证
	 * @return SoapClient
	 */
	private static function login_auth()
	{
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$client = new SoapClient($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Url']);
		$auth =array(
				'UserName'=>$GLOBALS['GLOBAL_CONF']['Ticket_Auth_User'], 
				'Password'=>$GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'],
				'RequestTime' => REQUEST_TIME
				);
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://webservice.mplife.com/cmsdataprovider');
	
		$header =  new SoapHeader('http://webservice.mplife.com/cmsdataprovider', "Auths", $authvalues, false);
	
		$client->__setSoapHeaders(array($header));
		return $client;
	}
	
	private static function auth($signData)
	{	
		ini_set("soap.wsdl_cache_enabled", "0");
		$client = new SoapClient($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Url']);
		$auth =array(
				'UserName'=>$GLOBALS['GLOBAL_CONF']['Ticket_Auth_User'],
				'Password'=>$GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'],
				'SignData' =>  $signData,
				'RequestTime' => REQUEST_TIME
		);
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://webservice.mplife.com/cmsdataprovider');
	
		$header =  new SoapHeader('http://webservice.mplife.com/cmsdataprovider', "Auths", $authvalues, false);
	
		$client->__setSoapHeaders(array($header));
		return $client;
	}
	
	private static function authHead($signData, $request_url = 'http://superbuy.mplife.com/Interface/SuperBuyWebService.asmx?wsdl')
	{
		ini_set("soap.wsdl_cache_enabled", "0");
		$client = new SoapClient($request_url);
		$auth =array(
				'UserName'=>$GLOBALS['GLOBAL_CONF']['Ticket_Auth_User'],
				'Password'=>$GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'],
				'IsApp' => true,
				'SignData' =>  $signData,
				'RequestTime' => REQUEST_TIME
		);
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://tempuri.org/');
	
		$header =  new SoapHeader('http://tempuri.org/', "Auths", $authvalues, false);
	
		$client->__setSoapHeaders(array($header));
		return $client;
	}	
	/**
	 * 提交优惠券 / 编辑优惠券
	 * @param unknown_type $param
	 */
	public static function createTickets($param, $ticket_type = 'MERCHANT') {
		
		$ticketParamJson =json_encode($param);
		$paramArray = array(
					'ticketTypeLabel' => $ticket_type,
					'merchantTypeLabel' => 'MPBUY',
					'ticketJsonData' => $ticketParamJson,
					'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], $ticket_type . '|MPBUY|' . REQUEST_TIME)
				);
		
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::login_auth()->CreateTickets($paramArray);
		
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'createTickets/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($clientObject, true) . var_export($param, true), $logPath);
		
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['CreateTicketsResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 领取优惠券
	 * @param unknown_type $user_name
	 * @param unknown_type $ticket_uuid
	 * @param unknown_type $phone
	 */
	public static function getCouponTickets($user_uuid, $user_name, $ticket_uuid, $phone) {
		$param = array(
					'ticketTypeLabel' => self::$ticketTypeLabel,
					'ticketTokenJsonData' => json_encode(array(
						'UserMobile' => $phone,
						'UserLoginName' => $user_name,
						'UserLoginID' => $user_uuid,
						'TicketID' => $ticket_uuid,
					)),
					'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], self::$ticketTypeLabel . '|' . REQUEST_TIME)
				);
		$clientObject = self::login_auth()->GetTicket($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetTicketTokenResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;			
	}
	/**
	 * 使用优惠券
	 * @param unknown_type $ticket_uuid
	 * @param unknown_type $phone
	 * @return Ambigous <void, array>
	 */
	public static function useTicket($ticketModel, $ticket_type = 'MERCHANT') {
		$param = array(
				'ticketTypeLabel' => $ticket_type,
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($ticketModel),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], $ticket_type . '|MPBUY|' . REQUEST_TIME)
		);
		$clientObject = self::login_auth()->VerifyTickets($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['VerifyTicketsResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	
	/**
	 * 验证现金券
	 * @param unknown_type $ticket_uuid
	 * @param unknown_type $phone
	 * @return Ambigous <void, array>
	 */
	public static function vaildVoucherTicket($ticketModel, $ticket_type = 'COUPON') {
		$param = array(
				'ticketTypeLabel' => $ticket_type,
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($ticketModel),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], $ticket_type . '|MPBUY|' . REQUEST_TIME)
		);
		$clientObject = self::login_auth()->VerifyTickets($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['VerifyTicketsResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 获取用户现金券列表
	 * @param unknown_type $user_name
	 */
	public static function getUserTicketList($uuid, $orderStatus = -1, $is_tuan = 0, $page = null, $pagesize = PAGESIZE) {		
		if(!is_null($page) && !is_null($pagesize)) {
			$jsonDataArray = array(
					'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
					'UserLoginID' => $uuid,
					'OrderStatus' => $orderStatus,
					'IsTuan' => $is_tuan,
			);			
		} else {
			$jsonDataArray = array(
					'UserLoginID' => $uuid,
					'OrderStatus' => $orderStatus,
					'IsTuan' => $is_tuan,
			);
		}
		
		$param = array(
				'ticketTypeLabel' => 'COUPON',
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($jsonDataArray),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], 'COUPON|MPBUY|' . REQUEST_TIME)
		);
		$clientObject = self::login_auth()->GetUserTicketList($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetUserTicketListResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	
	/**
	 * 获取用户单张现金券明细
	 * @param unknown_type $user_name
	 */
	public static function getUserTicketOne($user_name, $OrderNo, $ticket_type = 'COUPON') {
		$jsonDataArray = array(
				'UserLoginName' => $user_name,
				'OrderNo' => $OrderNo
		);
		$param = array(
				'ticketTypeLabel' => $ticket_type,
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($jsonDataArray),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], $ticket_type . '|MPBUY|' . REQUEST_TIME)
		);
		
		$clientObject = self::login_auth()->GetUserTicketList($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetUserTicketListResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}	
	/**
	 * 根据验证码查询，相关现金券信息
	 */
	public static function getTicketVerifyList($shop_id, $captcha, $ticket_type = 'COUPON') {		
		$jsonDataArray = array(
					//'MerchantCommonID' => $shop_id,
					'Code' => $captcha
				);
		$param = array(
				'ticketTypeLabel' => $ticket_type,
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($jsonDataArray),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], $ticket_type . '|MPBUY|' . REQUEST_TIME)
		);
		$clientObject = self::login_auth()->GetTicketVerifyList($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetTicketVerifyListResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	} 
	/**
	 * 获取有效券的列表
	 * @return Ambigous <void, array>
	 */
	public static function get_ticket_valid_list() {
		$param = array(
				'ticketTypeLabel' => self::$ticketTypeLabel,
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Report_Auth_Password'],self::$ticketTypeLabel),
		);
		$clientObject = self::login_auth()->GetTicketValidList($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetTicketValidListResult']);
		$clientResultArray = objectToArray($clientResultJson);
		return $clientResultArray;
	}
	/**
	 * 获取商户账户记录
	 * @param unknown_type $shop_id
	 * @param unknown_type $stime
	 * @param unknown_type $etime
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 * @param unknown_type $ticket_type
	 */
	public static function getAccountBookList($shop_id, $stime = 0, $etime = 0, $page = 1, $pagesize = PAGESIZE) {
		$jsonDataArray = array(
				'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
				'MerchantCommonID' => $shop_id,
				'PostStartTimeStamp' => $stime,
				'PostEndTimeStamp' => $etime
		);
		$param = array(
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($jsonDataArray),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], 'MPBUY|' . REQUEST_TIME)
		);
		$clientObject = self::login_auth()->GetAccountBookList($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetAccountBookListResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}

	/**
	 * 获取券使用统计
	 * @param unknown_type $shop_id
	 */
	public static function getMerchantStat($shop_id) {
		$jsonDataArray = array(
				'MerchantCommonID' => $shop_id
		);
		$param = array(
				'merchantTypeLabel' => 'MPBUY',
				'jsonData' => json_encode($jsonDataArray),
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], 'MPBUY|' . REQUEST_TIME)
		);
		$clientObject = self::login_auth()->GetMerchantStat($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetMerchantStatResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 同步商场列表
	 */
	public static function getMarketPlaceList($pagesize) {
		$jsonDataArray = array(
				'Method' => 'MarketPlaceList',
				'Data' => json_encode(array(
							'Paging' => array('PageSize' => $pagesize)
						))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)				
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 根据商场ID 获取对应的折扣信息
	 */
	public static function getDiscountsAssociated($ID) {
		$jsonDataArray = array(
				'Method' => 'ArticleListByMarketPlace',
				'Data' => json_encode(array(
						'ID' => $ID,
						'ImageSize' => '180*135'
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	
	/**
	 * 根据根据经纬度 获取对应的折扣信息
	 */
	public static function getArticleListByDistance($lng, $lat, $radius, $page = 1, $pagesize = PAGESIZE) {
		$jsonDataArray = array(
				'Method' => 'ArticleListByDistance',
				'Data' => json_encode(array(
						'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
						'Longitude' => $lng,
						'Latitude' => $lat,
						'Radius' => $radius
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 根据商户ID（店铺ID）获取对应的现金券列表
	 * @param unknown_type $shop_id
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public static function getShopCouponList($shop_id, $page = 1, $pagesize = PAGESIZE) {
		$jsonDataArray = array(
				'Method' => 'CouponList',
				'Data' => json_encode(array(
						'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
						'MerchantCommonID' => $shop_id,
						'Status' => 3
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}

	/**
	 * 获取团购列表
	 * @param unknown_type $shop_id
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public static function getTuanList($sid, $dtype, $dsort, $city, $page = 1, $pagesize = PAGESIZE) {
		switch ($dtype) {
			case 'time':
				if($dsort == 'desc') {
					$sort = 10;
				} elseif($dsort == 'asc') {
					$sort = 9;
				}
				brak;
			case 'sales':
				if($dsort == 'desc') {
					$sort = 2;	
				} elseif($dsort == 'asc') {
					$sort = 1;
				}
				brak;
			case 'price':
				if($dsort == 'desc') {
					$sort = 6;
				} elseif($dsort == 'asc') {
					$sort = 5;
				}
				brak;
			case 'iprice'://APP价格
				if($dsort == 'desc') {
					$sort = 8;
				} elseif($dsort == 'asc') {
					$sort = 7;
				}
				brak;
		}
		$jsonDataArray = array(
				'Method' => 'ProductList',
				'Data' => json_encode(array(
						'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
						'City' => $city,
						'IsTuan' => 1,
						'StoreId' => $sid,
						'Sort' => $sort,
						'Status' => 2 //未过期的商品
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}	
	/**
	 * 根据商户ID（店铺ID）获取对应的现金券列表
	 * @param unknown_type $shop_id
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public static function getShopCouponRow($ticket_uuid) {
		$jsonDataArray = array(
				'Method' => 'CouponList',
				'Data' => json_encode(array(
						'ProductID' => $ticket_uuid
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	
	private static function get_token() {
		$guid = uuid();
		$param = array(
				'ticketTypeLabel' => self::$ticketTypeLabel,
				'commonID' => $guid,
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Report_Auth_Password'],self::$ticketTypeLabel.'|'.$guid),
		);
		$backObject = self::login_auth()->GetTicketAccessToken($param);
		$backObjectJsonArray = objectToArray($backObject);
		$backObjectArray = array_shift(objectToArray(json_decode($backObjectJsonArray['GetTicketAccessTokenResult'])));		
		return $backObjectArray;
	}
	
	public static function get_ticket($userName, $userMobile, $userTicketID) {
		$token = self::get_token();
		if($token['code'] == 1) {
			$param = array(
					'ticketTypeLabel' => self::$ticketTypeLabel,
					'token' => $token['message'],
					'userName' => $userName,
					'userMobile' => $userMobile,
					'userTicketID' => $userTicketID,
					'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Report_Auth_Password'],self::$ticketTypeLabel.'|'.$token['message'].'|'.$userName.'|'.$userMobile.'|'.$userTicketID),
			);
			$backObject = self::login_auth()->GetTicket($param);
			$backObjectJsonArray = objectToArray($backObject);
			$backObjectArray = array_shift(objectToArray(json_decode($backObjectJsonArray['GetTicketResult'])));
			return $backObjectArray;
		}
	}
	
	public static function get_ticket_notification($mobile, $ticketID) {
		$param = array(
				'mobile' => $mobile,
				'ticketID' => $ticketID,
				'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Report_Auth_Password'],$mobile.'|'.$ticketID),
		);
		$backObject = self::login_auth()->GetTicketNotification($param);
		$backObjectJsonArray = objectToArray($backObject);
		$backObjectArray = array_shift(objectToArray(json_decode($backObjectJsonArray['GetTicketNotificationResult'])));
		return $backObjectArray;
	}
	
	public static function get_ticket_details_by_guid($guid) {
		$param = array(
					'action' => 'GetOneProduct',
					'activityid' => $guid,
					'jsoncallback' => 'back'
				);
		$request_url = $GLOBALS['GLOBAL_CONF']['Ticket_Detail_Url'] . '?' . http_build_query($param);
		$jsonString = file_get_contents($request_url);
 		$jsonString = substr($jsonString, 5, -1);
 		return json_decode($jsonString);
	}
	
	public static function getTicketDetailByTicketUuid($ticket_uuid) {
		$param = array(
						'action' => 'GetProductInfo',
						'productId' => $ticket_uuid,
						'jsoncallback' => 'back'
				);
		$request_url = $GLOBALS['GLOBAL_CONF']['Ticket_Detail_Url'] . '?' . http_build_query($param);
		$jsonString = file_get_contents($request_url);
		$jsonString = substr($jsonString, 5, -1);
		return json_decode($jsonString);
	}
	/**
	 * 预约白名单新增 0a7f0ee1-67a2-48ac-b2b7-06bb0cc9705d
	 */
	public static function productReservationAdd($ProductID, $mobile, $Source = 'test') {
		$jsonDataArray = array(
				'Method' => 'ProductReservationAdd',
				'Data' => json_encode(array(
						'ProductID' => $ProductID,
						'Mobile' => $mobile,
						'Source' => $Source
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	
	/**
	 * 预约白名单列表 
	 */
	public static function productReservationList($ProductID, $mobile = null) {
		
		if(is_null($mobile)) {
			$jsonDataArray = array(
					'Method' => 'ProductReservationList',
					'Data' => json_encode(array(
							'ProductID' => $ProductID
					))
			);			
		} else {
			$jsonDataArray = array(
					'Method' => 'ProductReservationList',
					'Data' => json_encode(array(
							'ProductID' => $ProductID,
							'Mobile' => $mobile
					))
			);
		}
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 获取短链接
	 * @param unknown_type $url
	 */
	public static function getTinyUrlList($url) {
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$param = array(
					'longUrls' => $url,
					'signData' => MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], $url)
				);
		$clientObject = self::auth($signData)->GetTinyUrlList($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetTinyUrlListResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
		
	}
	/**
	 * 会员订单处理
	 * @param unknown_type $type
	 * @param unknown_type $uuid
	 * @param unknown_type $AppType
	 */
	public static function orderProcessing($uuid, $OrderNo, $type, $AppType = -1) {
		switch ($type) {
			//退款
			case 'Refund':
					$jsonDataArray = array(
							'Method' => 'OrderApplyRefund',
							'Data' => json_encode(array(
									'OrderNo' => $OrderNo,
									'UserId'  => $uuid
							))
					);		
				break;
				//取消订单
			case 'CancelOrder':
					$jsonDataArray = array(
							'Method' => 'OrderApplyCancel',
							'Data' => json_encode(array(
									'OrderNo' => $OrderNo,
									'UserId'  => $uuid
							))
					);				
				break;
				//继续支付
			case 'ContinueToPay':
					$jsonDataArray = array(
							'Method' => 'OrderContiunePay',
							'Data' => json_encode(array(
									'OrderNo' => $OrderNo,
									'UserId'  => $uuid,
									'AppType' => $AppType
							))
					);				
				break;
		}
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	/**
	 * 后台订单处理
	 * @param unknown_type $method
	 * @param unknown_type $orderNo
	 * @return mixed
	 */
	public static function changeOrder($method, $orderNo, $userName, $expressCompany, $expressNumber) {
		
		switch ($method) {
			//发货
			case 'delivery':
				$jsonDataArray = array(
					'Method' => 'OrderDeliver',
					'Data' => json_encode(array(
						'OrderNo' => $orderNo,
						'Deliverer' => $userName,
						'DeliverInfo' => json_encode(array('ExpressCompany' => $expressCompany, 'ExpressNumber' => $expressNumber))
					))
				);
				break;

		}
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	/**
	 * 获取抵用券列表
	 * @param unknown_type $mobile
	 * @param unknown_type $uuid
	 * @param unknown_type $status
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 * @return mixed
	 */
	public static function getVoucherList($mobile, $uuid, $status = -999, $type = 0, $page = 1, $pagesize = PAGESIZE) {
		$jsonDataArray = array(
				'Method' => '10',
				'Data' => json_encode(array(
						'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
						'Mobile' => !$mobile ? '' : $mobile,
						'UserId' => !$uuid ? '' : $uuid,
						'Status' => $status,
						'Type' => $type
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 抵用券绑定
	 * @param unknown_type $mobile
	 * @param unknown_type $uuid
	 * @param unknown_type $code
	 * @param unknown_type $version
	 */
	public static function bindVoucher($mobile, $uuid, $code, $version) {
		$jsonDataArray = array(
				'Method' => '12',
				'Data' => json_encode(array(
						'App' => 'Mpbuy',
						'Remark' => $version,
						'UserId' => $uuid,
						'Mobile' => $mobile,
						'Code' => $code
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::auth($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}

	/**
	 * 同步优惠券至某一手机用户
	 * @param unknown_type $mobile 		手机号码
	 * @param unknown_type $ticket_id 	券ID
	 */
	public static function quizAddVoucher($mobile,  $ticket_id) {
		$jsonDataArray = array(
				'Mobile' => $mobile,
				'TicketId' => $ticket_id
		);
	
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
	
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->QuizAddVoucher($param);
	
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'QuizAddVoucher/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($clientObject, true), $logPath);
	
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	
	/**
	 * 同步抵用券至某一手机用户
	 * @param unknown_type $mobile 		手机号码
	 * @param unknown_type $ticket_id 	券ID
	 */
	public static function tempAddCoupon($jsonDataArray) {
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->TempAddCoupon($param);
	
		//日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'TempAddCoupon/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, var_export($param, true) . var_export($clientObject, true), $logPath);
	
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['TempAddCouponResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 一元众筹抽奖
	 * @param unknown_type $mobile
	 * @param unknown_type $code
	 */
	public static function lotteryWinning($ProductId, $code) {
		$jsonDataArray = array(
				'Method' => '251',
				'Data' => json_encode(array(
						'ProductId' => $ProductId,
						'Code' => $code,
						'GetTime' => datex(REQUEST_TIME, 'Y-m-d H:i:s')
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}	
	/**
	 * 一元众筹订单列表
	 * @param unknown_type $UserId
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public static function lotteryList($UserId, $page = 1, $pagesize = PAGESIZE) {
		$jsonDataArray = array(
				'Method' => '252',
				'Data' => json_encode(array(
						//'Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize),
						'UserId' => $UserId
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 用户订单列表
	 * @param unknown_type $param
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public static function getOrderListToUser($param, $page = 1, $pagesize = PAGESIZE) {
		$jsonDataArray = array(
				'Method' => 'OrderListToUser',
				'Data' => json_encode(array(
						'Paging' 		=> 		array('PageIndex' => $page, 'PageSize' => $pagesize),
						'Mobile' 		=> 		!$param['mobile'] ? '' : $param['mobile'],
						'UserId' 		=> 		!$param['uuid'] ? '' : $param['uuid'],
						'OrderStatus' 	=> 		!$param['order_status'] ? 0 : $param['order_status'],
						'OrderTimeSlot' => 		!$param['order_time_slot'] ? 0 : $param['order_time_slot']
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 店铺订单列表
	 * @param unknown_type $param
	 * @param unknown_type $page
	 * @param unknown_type $pagesize
	 */
	public static function getOrderListToShop($param, $page = 1, $pagesize = PAGESIZE){
		$jsonDataArray = array(
				'Method' => 'OrderListToShop',
				'Data' => json_encode(array(
						'Paging' 				=> 		array('PageIndex' => $page, 'PageSize' => $pagesize),
						'MerchantCommonId' 		=> 		$param['shop_id'], //店铺ID
						'ReceiptStatus' 		=> 		$param['receipt_status'],
						'OrderStatus' 			=> 		$param['order_status'],
						'DisplayStatus'			=> 		$param['display_status'],
						'OrderTimeSlot' 		=> 		$param['order_time_slot']
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 商户订单统计
	 * @param unknown_type $param
	 */
	public static function getMerchantStatInfo($param) {
		$jsonDataArray = array(
				'Method' => 'MerchantStatInfo',
				'Data' => json_encode(array(
						'MerchantCommonId' 		=> 		$param['shop_id'], //店铺ID
						'MerchantUserId' 		=> 		$param['uuid'], //用户UUID
						'OrderTimeSlot' 		=> 		$param['order_time_slot'],
						'OrderStartTime'		=>		$param['order_start_time'], //订单开始时间 Y-m-d H:i:s(order_time_slot为-1时生效)
						'OrderEndTime'			=>		$param['order_end_time'] //订单结束时间 Y-m-d H:i:s(order_time_slot为-1时生效)
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	/**
	 * 店铺游惠统计
	 * @param unknown_type $param
	 */
	public static function getMerchantStatInfoToUser($param) {
		$jsonDataArray = array(
				'Method' => 'MerchantStatInfoToUser',
				'Data' => json_encode(array(
						'MerchantCommonId' 		=> 		$param['shop_id'], //店铺ID
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 商户订单详情
	 * @param unknown_type $param
	 * @return mixed
	 */
	public static function getOrderInfoToShop($param) {
		$tmpArr = array(
					'MerchantCommonId' 		=> 		$param['shop_id'], //店铺ID
					'OrderNo' 		=> 		$param['order_no'], 
					'OrderId' 		=> 		$param['order_id']
				);
		
		if(empty($tmpArr['OrderId'])) {
			unset($tmpArr['OrderId']);
		}
		
		$jsonDataArray = array(
				'Method' => 'OrderInfoToShop',
				'Data' => json_encode($tmpArr)
		);
		
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	/**
	 * 商户订单发货
	 * @param unknown_type $param
	 * @return mixed
	 */
	public static function setOrderDeliver($param) {
		$jsonDataArray = array(
				'Method' => 'OrderDeliver',
				'Data' => json_encode(array(
				    "MerchantCommonId"	=> $param['shop_id'], //String，必填
				    "OrderNo"		=>  $param['order_no'], //String，必填
				    "Deliverer"		=>  $param['user_name'], //String，必填
				    "DelivererId"	=>  $param['uuid'], //String，必填
				    "ExpressCompany" => $param['express_company'], //String，非必填
				    "ExpressNumber"	=> $param['express_number'],//String，非必填
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;
	}
	/**
	 * 商户检查验证码
	 * @param unknown_type $param
	 */
	public static function checkVCodeShop($param) {
		$jsonDataArray = array(
				'Method' => 'CheckVCode',
				'Data' => json_encode(array(
						"MerchantCommonId"	=> $param['shop_id'], //String，必填
						"VCode"		=>  $param['code'], //String，必填
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
	/**
	 * 商户使用验证码
	 * @param unknown_type $param
	 */
	public static function useVCodeToShop($param) {
		$jsonDataArray = array(
				'Method' => 'UseVCodeToShop',
				'Data' => json_encode(array(
						"MerchantCommonId"	=> $param['shop_id'], //String，必填
						"VCode"		=>  $param['code'], //String，必填
						'Verifier' => $param['user_name'],
						'VerifierId' => $param['uuid'],
						'AppPlatform' => $param['version']
				))
		);
		$param = array(
				'jsonData' => json_encode($jsonDataArray)
		);
		$signData = MD5sign($GLOBALS['GLOBAL_CONF']['Ticket_Auth_Password'], REQUEST_TIME);
		$clientObject = self::authHead($signData)->GetResult($param);
		$clientResult = objectToArray($clientObject);
		$clientResultJson = json_decode($clientResult['GetResultResult']);
		$clientResultArray = array_shift(objectToArray($clientResultJson));
		return $clientResultArray;		
	}
}
