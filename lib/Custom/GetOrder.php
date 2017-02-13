<?php 
/**
 * 获取特卖场次列表
 */
class Custom_GetOrder{
	/**
	 * 跟主站同步验证
	 * @return SoapClient
	 */
	private static function login_auth()
	{
		ini_set("soap.wsdl_cache_enabled", "0");
		$client = new SoapClient($GLOBALS['GLOBAL_CONF']['Order_Http_Uri']);
		$auth =array(
				'UserName' => $GLOBALS['GLOBAL_CONF']['Order_User'], 
				'Password' => $GLOBALS['GLOBAL_CONF']['Order_Pwd'],
				'Type' => 'temai',
				'City' => 'nj',
				'Status' => 0
				);
	
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://api.mplife.com/');
	
		$header =  new SoapHeader('http://api.mplife.com/', "Auths", $authvalues, false);
	
		$client->__setSoapHeaders(array($header));
		return $client;
	}
	//获取场次列表
	public static function get_order(){	
		$orderArr = $resultArray = array();
		$object = self::login_auth()->GetOrder();
		$resultArray = objectToArray($object);
		if(empty($resultArray['errorMsg'])){
			foreach(json_decode($resultArray['GetOrderResult']) as $key => $result){		
				$orderArr[$key]['OrderName'] = $result->OrderName;
				$orderArr[$key]['OrderTitle'] = $result->OrderTitle;
				//$orderArr[$key]['StartDate'] = $result->StartDate;
				//$orderArr[$key]['EndDate'] = $result->EndDate;
			}
		}else{
			if(isDebug()){
				die($resultArray['errorMsg']);
			}
		}
		return $orderArr;
	}
	
	public static function get_order_today(){
		//$object = self::login_auth()->GetOrderToday();		
	}
}