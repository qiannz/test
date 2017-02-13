<?php
class Custom_AuthSku {
	
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
	/**
	 * 获取分类列表
	 * @param unknown_type $categoryParentId
	 * @return mixed
	 */
	public static function getCategoryList($categoryParentId = -1, $status = 1) {
		$jsonDataArray = array(
				'Method' => 'CategoryList',
				'Data' => json_encode(array(
						'CategoryParentId' => $categoryParentId,
						'Status' => $status
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
	 * 根据分类ID 获取SKU属性阵列
	 * @param unknown_type $categoryId
	 * @param unknown_type $isSale
	 * @param unknown_type $status
	 * @param unknown_type $valueStatus
	 * @return mixed
	 */
	public static function getPropList($categoryId, $isSale = 1, $status = 1, $valueStatus = 1) {
		$jsonDataArray = array(
				'Method' => 'PropList',
				'Data' => json_encode(array(
						'CategoryId' => $categoryId,
						'IsSale' => $isSale,
						'Status' => $status,
						'ValueStatus' => $valueStatus
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
	 * 根据商品ID 获取相关属性
	 * @param unknown_type $productId
	 * @param unknown_type $pvs
	 * @param unknown_type $isSku
	 * @return mixed
	 */
	public static function getProductPropValueList($productId, $pvs = '', $isSku = 1) {
		$jsonDataArray = array(
				'Method' => 'ProductPropValueList',
				'Data' => json_encode(array(
						'ProductId' => $productId,
						'Pvs' => $pvs,
						'IsSku' => $isSku
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
	 * 获取团购订单列表
	 * @param unknown_type $orderData
	 * @return mixed
	 */
	public static function getOrderList($orderData, $page = 1, $pagesize = PAGESIZE) {
		/**					
			'Mobile' => $orderData['mobile'] ? $orderData['mobile'] : '',
			'OrderStatus' => !$orderData['order_status'] ? '-1' : $orderData['order_status'],
			'UserName' => $orderData['user_name'] ? $orderData['user_name'] : '',
			'UserId' => $orderData['user_id'] ? $orderData['user_id'] : '00000000-0000-0000-0000-000000000000',
			'OrderNo' => $orderData['order_no'] ? $orderData['order_no'] : '',
			'OrderStartTime' => $orderData['order_start_time'] ? $orderData['order_start_time'] : '',
			'OrderEndTime' => $orderData['order_end_time'] ? $orderData['order_end_time'] : '',
			'ProductName' => $orderData['product_name'] ? $orderData['product_name'] : '',
			'ReceiptStatus' => !$orderData['receipt_status'] ? '-1' : $orderData['receipt_status'],
			'IsTuan' => 1,
			'MerchantCommonId' => $orderData['merchant_common_id'],
		 */
		$orderData = array_merge(array('Paging' => array('PageIndex' => $page, 'PageSize' => $pagesize)), $orderData);
		$jsonDataArray = array(
				'Method' => 'OrderList',
				'Data' => json_encode($orderData)
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
}