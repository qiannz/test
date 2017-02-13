<?php
/**
 * 同步验证
 *
 */
class Custom_AuthLogin {   
	/**
	 * 跟主站同步验证
	 * @return SoapClient
	 */
	private static function login_auth()
	{
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$client = new SoapClient($GLOBALS['GLOBAL_CONF']['Auth_Http_Uri']);
	
		$auth =array('UserName'=>$GLOBALS['GLOBAL_CONF']['Auth_User'], 'Password'=>$GLOBALS['GLOBAL_CONF']['Auth_Pwd']);
	
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://tempuri.org/');
	
		$header =  new SoapHeader('http://tempuri.org/', "Auths", $authvalues, false);
	
		$client->__setSoapHeaders(array($header));
		return $client;
	}
	/**
	 * 彩信头验证
	 * @return SoapClient
	 */
	private static function login_mms_auth()
	{
		ini_set("soap.wsdl_cache_enabled", "0");
	
		$client = new SoapClient($GLOBALS['GLOBAL_CONF']['Mms_Http_Uri']);
	
		$auth =array('UserName'=>$GLOBALS['GLOBAL_CONF']['Mms_User'], 'Password'=>$GLOBALS['GLOBAL_CONF']['Mms_Pwd']);
	
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://tempuri.org/');
	
		$header =  new SoapHeader('http://tempuri.org/', "Auths", $authvalues, false);
	
		$client->__setSoapHeaders(array($header));
		return $client;
	}
	/**
	 * 消息系统
	 */
	private static function login_sms_auth() {
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$client = new SoapClient('http://api.push.mplife.com/WCFService');
		
		$auth =array('UserName'=>$GLOBALS['GLOBAL_CONF']['Mms_User'], 'Password'=>$GLOBALS['GLOBAL_CONF']['Mms_Pwd']);
		
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://tempuri.org/');
		
		$header =  new SoapHeader('http://tempuri.org/', "Auths", $authvalues, false);
		
		$client->__setSoapHeaders(array($header));
		return $client;		
	}
	/**
	 * 与主站同步登录
	 * @param unknown_type $userName
	 * @param unknown_type $passWord
	 * @return int -99(身份不合法);1(成功);2(传参不正确);3(密码格式错误);-1(用户不存在);-2(密码错误)
	 */
	public static function user_login($userName, $passWord){
        $param = array(
        		'userName' => $userName,
        		'passWord' => Third_Des::encrypt($passWord),
        );
		$clientObject = self::login_auth()->LoginUserBySubStation($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;	
	}		
	/**
	 * 验证主站用户名是否重复
	 * @param unknown_type $user_name
	 * return int RegisterCheckUserBySubStationResult：-99(身份验证失败);1(已存在的用户);-1(用户名规则不规范);0(不存在的用户);
	 */
	public static function register_check_user($user_name){
		$param = array(
			'userName' => $user_name
		);	
		$clientObject = self::login_auth()->RegisterCheckUserBySubStation($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult['RegisterCheckUserBySubStationResult'];
	}
    /**
     * 同步主站用户名密码
     * @param unknown_type $user_name
     * @param unknown_type $pass_word
     * @return array 
     * 		   RegisterUserBySubStationResult: -99(身份验证失败);1(成功);2(传参不正确);-1(账号已存在);
     * 		   userID 同步ID
     */
	public static function register_user($user_name, $pass_word, $source = '南京WEB接口'){
		$param = array(
				'userName'    => $user_name,
				'passWord'    => Third_Des::encrypt($pass_word),
				'ip'          => Custom_Client::getUserIp(),
				'source'      => $source,
				'province'    => '320000',
				'city'	      => '320100',
		);
		$clientObject = self::login_auth()->RegisterUserBySubStation($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;	
	}
	/**
	 * 同步主站密码修改
	 * @param unknown_type $uid
	 * @param unknown_type $pass_word
	 * @return int RetrievePasswordBySubStationResult：-99(身份验证失败);1(成功);-1(失败);2(传参错误);3(密码格式错误);4(用户不存在);5(此用户不是南京APP用户)
	 */
	public static function retrieve_password($uid, $pass_word){
		$param = array(
				'userID'      => $uid,
				'passWord'    => Third_Des::encrypt($pass_word),
		);
		$clientObject = self::login_auth()->RetrievePasswordBySubStation($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult['RetrievePasswordBySubStationResult'];		
	}
	/**
	 * 根据用户名获取用户详细信息
	 * @param unknown_type $user_name
	 * @return Ambigous <void, array>
	 */
	public static function get_user_info($user_name) {
		$user_name = convToUtf8($user_name);
		$param = array(
				'userName' => $user_name
		);
		$clientObject = self::login_auth()->GetUserInfo($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;
	}
	
	public static function get_user_by_uuid($uuid) {
		$param = array(
				'type' => 'UserID',
				'keyword' => $uuid
		);
		$clientObject = self::login_auth()->GetUserInfos($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;
	}
	
	public static function get_user_by_mobile($mobile) {
		$param = array(
				'type' => 'Mobile',
				'keyword' => $mobile
		);
		$clientObject = self::login_auth()->GetUserInfos($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;
	}
	
	public static function sync_user($uuid, $mobile, $real_name) {
		$params = array(
				'UserId' => $uuid,
				'Mobile' => $mobile,
				'RealName' => $real_name
		);
		$clientObject = self::login_auth()->UpdateUserForMpshop(array('param' =>json_encode($params)));
		$clientResult = objectToArray($clientObject);
		/*
		 * 	-99：身份验证不通过
			-98：服务器异常
			-1：参数错误
			-2：会员账号不存在
			-3：手机号码已经被其他用户绑定
			1：成功
			返回KEY [UpdateUserForMpshopResult]
		 */
		$filerName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'sync/user/' . date('Y') . '/' .date('m') . '/';
		logLog($filerName, var_export($clientResult, true), $logPath);
		
		return $clientResult;
	}
	
	/**
	 * 改变用户身份类型
	 * @param unknown_type $guid
	 * @param unknown_type $utype
	 */
	public static function changeUserType($guid, $utype) {
		$param = array(
				'userID' => $guid,
				'type' => $utype
		);
		$clientObject = self::login_auth()->ChangeUserType($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;	
	}
	
	/**
	 * 发彩信
	 * @param unknown_type $mobile 接收方手机号码
	 * @param unknown_type $orderid 订单号
	 * @param unknown_type $numiid 何券
	 * @param unknown_type $apply_type
	 * @return Array ( [resultid] => 1 [resultmessage] => (二维码提供商返回状态码：00, 返回消息：成功！) ) 
	 * 
	 */
	public static function send_mms($mobile, $orderid, $numiid, $apply_type = '02') {
		$jsonData = array(
			'applyContent' =>array(
				'apply_type' => $apply_type,
				'apply_msg' => array(
						'numiid' => $numiid,
						'recemobile' => $mobile,
						'orderid' => $orderid,
						'apptypename' => 'superbuy'
				)
			)
		);
		$param = array(
				'applyContent' => json_encode($jsonData)
		);
		
		$clientObject = self::login_mms_auth()->DoCertificate($param);
		$clientResult = objectToArray($clientObject);
		$clientResultArray = objectToArray(json_decode(urldecode($clientResult['DoCertificateResult'])));
		return $clientResultArray;	
	}
	/**
	 * 用户绑定手机
	 * @param unknown_type $uuid
	 * @param unknown_type $mobile
	 * @return Ambigous <void, array>
	 */
	public static function bindMobile($uuid, $mobile) {
		$param = array(
				'userId' => $uuid,
				'mobile' => $mobile
		);
		$clientObject = self::login_auth()->MS_BindUserMobile($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult;		
	}
	/**
	 * 消息系统通知发送
	 * @param unknown_type $param
	 */
	public static function send_sms($param) {
		$filerName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'notice/send/' . date('Y') . '/' .date('m') . '/';
		logLog($filerName, var_export($param, true), $logPath);
		
		$url = 'http://api.push.mplife.com/WCFService/postmessage';
		
		$postData = array(
				'reqmsg' => json_encode($param)
				);
		
		logLog(date('Ymd'). '_post.log', var_export($postData, true), $logPath);
		$sendResult = Core_Http::sendRequest($url, $postData, 'CURL-POST');
		
		return $sendResult;
	}
	
	public static function getUrlToken($mobile, $time) {
		Third_Des::$key = $time;		
		$token = urlencode(Third_Des::encrypt($mobile));
		return $token;
	}
}