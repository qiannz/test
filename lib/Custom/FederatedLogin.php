<?php 
define( "WB_AKEY" , '3145608762' );
define( "WB_SKEY" , 'd15d2193bdee0c4649463119ce0c4e75' );
define( "WB_CALLBACK_URL" , 'http://www.mpning.com/?action=weibo-callback' );

define( "QQ_APPID" , '100398344' );
define( "QQ_KEY" , 'a4e10051822040c829b36ede99abbf5f' );
define( "QQ_CALLBACK_URL" , 'http://www.mpning.com/?action=qq-callback' );

define( 'DEBUG_MODE', false );

class Custom_FederatedLogin {
	
	private static $weibo_auth = null;
	private static $weibo_client = null;
	private $config = array();
	
	private static function weibo_auth(){
		if(self::$weibo_auth === null){
			require_once ROOT_PATH. 'lib/Third/Sina.php';			
			self::$weibo_auth = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
			self::$weibo_auth->set_debug( DEBUG_MODE );
		}
		return self::$weibo_auth;
	}
	
	private static function weibo_client($access_token){
		if(self::$weibo_client === null){
			require_once ROOT_PATH. 'lib/Third/Sina.php';
			self::$weibo_client = new SaeTClientV2( WB_AKEY , WB_SKEY, $access_token );
			self::$weibo_client->set_debug( DEBUG_MODE );
		}
		return self::$weibo_client;
	}
	
	
	private static function qq_obj(){
		if(self::$qq === null){
			require_once ROOT_PATH. 'lib/Third/Qq.php';			
			self::$qq = new Third_Qq();
			self::$qq->set_debug( DEBUG_MODE );
		}
		return self::$weibo;
	}
	
	public static function weibo_url($session_id){		
		$code_url = self::weibo_auth()->getAuthorizeURL( WB_CALLBACK_URL , 'code', $session_id );	
		return $code_url;
	}
	
	public static function get_sina_access_token($code){
		$keys = array();
		$keys['code'] = $code;
		$keys['redirect_uri'] = WB_CALLBACK_URL;
		$token = self::weibo_auth()->getAccessToken( 'code', $keys ) ;
		return $token;
	}
	
	public static function get_sina_user_info($access_token){
		$uid_get = self::weibo_client($access_token)->get_uid();
		$uid = $uid_get['uid'];
		$user_message = self::weibo_client()->show_user_by_id($uid);
		return $user_message;		
	}
	
	public static function get_qq_user_info($access_token, $openid){
		$get_user_info = "https://graph.qq.com/user/get_user_info?"
				. "access_token=" . $access_token
				. "&oauth_consumer_key=" . QQ_APPID
				. "&openid=" . $openid
				. "&format=json";
		
		$info = get_url_contents($get_user_info);
		return json_decode($info, true);
	}
	
	public static function getqq_url($session_id){
		$qq_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
				. QQ_APPID . "&redirect_uri=" . urlencode(QQ_CALLBACK_URL) 
				. "&state=" . $session_id;
		return $qq_url;
	}
	
	public static function get_qq_token($code){
		$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
				. "client_id=" . QQ_APPID. "&redirect_uri=" . urlencode(QQ_CALLBACK_URL)
				. "&client_secret=" . QQ_KEY . "&code=" . $code;
		$response = get_url_contents($token_url);
		if (strpos($response, "callback") !== false)
		{
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg = json_decode($response);
		}
	
		$params = array();
		parse_str($response, $params);
		return $params["access_token"];

	}
	
	public static function get_qq_openid($access_token){
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $access_token;
		
		$str  = get_url_contents($graph_url);
		if (strpos($str, "callback") !== false)
		{
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}
		
		$user = json_decode($str);
		return $user->openid;		
	}
	
}

if (!function_exists("get_url_contents"))
{
	function get_url_contents($url)
	{
		if (ini_get("allow_url_fopen") == "1"){
			return file_get_contents("$url");
		}

		$ch = curl_init();
		//CURLOPT_HTTPHEADER  用来设置http头字段的数组，相当于html的<head></head>中的内容设置
		//curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded','Connection: close' ,'Cache-Control: no-cache' ,'Accept-Language: zh-cn'));
		//CURLOPT_TIMEOUT  响应时间设置
		//curl_setopt ($ch, CURLOPT_TIMEOUT, 20);
		//CURLOPT_USERAGENT  在HTTP请求中包含一个"User-Agent: "头的字符串(用来设置用户浏览器)
		//curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
		//CURLOPT_HEADER  启用时会将头文件的信息作为数据流输出(true,false)
		curl_setopt ($ch, CURLOPT_HEADER,0);
		//CURLOPT_FOLLOWLOCATION  启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
		//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
		//CURLOPT_RETURNTRANSFER  (这个很重要)将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		//CURLOPT_POST 启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
		//curl_setopt($ch, CURLOPT_POST, 0);
		//CURLOPT_URL  需要获取的URL地址，也可以在curl_init()函数中设置
		curl_setopt ($ch, CURLOPT_URL,$url);
		// CURLOPT_SSL_VERIFYPEER  禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//CURLOPT_SSL_VERIFYHOST    1 检查服务器SSL证书中是否存在一个公用名(common name)。译者注：公用名(Common Name)一般来讲就是填写你将要申请SSL证书的域名 (domain)或子域名(sub domain)。2 检查公用名是否存在，并且是否与提供的主机名匹配。
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
		//CURLOPT_HTTPGET   用get方式获取参数
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		$res  = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
}