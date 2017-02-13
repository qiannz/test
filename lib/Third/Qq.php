<?php
/**
 * QQ 联合登录
 * @author qiannz
	$config['appid']    = '100352073';
	$config['appkey']   = '3e19ba0026d6231765e35066415ba8f9';
	$config['callback'] = 'http://open.mpning.com/aa.php';
	$o_qq = Oauth_qq::getInstance($config);
	 
	$o_qq->login();
	$o_qq->callback();
	$o_qq->get_openid();
	$o_qq->get_user_info();
*/
class Third_Qq
{
	private static $_instance;
	private $config = array();
	private $debug = false;

	
	private function __construct($config)
	{
		$this->Third_Qq($config);
	}
	
	public static function getInstance($config)
	{
		if(!isset(self::$_instance))
		{
		  self::$_instance = new self($config);
		}
		return self::$_instance;
	}
	
	private function Third_Qq($config)
	{
		$this->config = $config;
		$_SESSION["appid"]    = $this->config['appid'];
		$_SESSION["appkey"]   = $this->config['appkey'];
		$_SESSION["callback"] = $this->config['callback'];
		$_SESSION["scope"] = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
	}
	
	function login()
	{
		$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
		$qq_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
				. $_SESSION["appid"] . "&redirect_uri=" . urlencode($_SESSION["callback"]) 
				. "&state=" . $_SESSION['state'] 
				. "&scope=".$_SESSION["scope"];
		return $qq_url;
	}
	
	function callback()
	{		
		if($_REQUEST['state'] == $_SESSION['state']) //csrf
		{
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
				. "client_id=" . $_SESSION["appid"]. "&redirect_uri=" . urlencode($_SESSION["callback"])
				. "&client_secret=" . $_SESSION["appkey"]. "&code=" . $_REQUEST["code"];
		  	$response = get_url_contents($token_url);
			if (strpos($response, "callback") !== false)
			{
				$lpos = strpos($response, "(");
				$rpos = strrpos($response, ")");
				$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
				$msg = json_decode($response);
				if (isset($msg->error) && $this->debug)
				{
					echo "<h3>error:</h3>" . $msg->error;
					echo "<h3>msg  :</h3>" . $msg->error_description;
					exit;
				}
			}
	
			$params = array();
			parse_str($response, $params);
	
			$_SESSION["access_token"] = $params["access_token"];
			
			return $_SESSION["access_token"];
	
		}
		else
		{
			//echo("The state does not match. You may be a victim of CSRF.");
		}
	}
	
	function get_openid()
	{
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $_SESSION['access_token'];
		
		$str  = get_url_contents($graph_url);
		if (strpos($str, "callback") !== false)
		{
		  $lpos = strpos($str, "(");
		  $rpos = strrpos($str, ")");
		  $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}
		
		$user = json_decode($str);
		if (isset($user->error) && $this->debug)
		{
		  echo "<h3>error:</h3>" . $user->error;
		  echo "<h3>msg  :</h3>" . $user->error_description;
		  exit;
		}
		
		//set openid to session
		return $_SESSION["openid"] = $user->openid;
	}
	
	function get_user_info()
	{
		$get_user_info = "https://graph.qq.com/user/get_user_info?" 
				. "access_token=" . $_SESSION['access_token'] 
				. "&oauth_consumer_key=" . $_SESSION["appid"] 
				. "&openid=" . $_SESSION["openid"] 
				. "&format=json";
		
		$info = get_url_contents($get_user_info);
		$arr = json_decode($info, true);
		
		return $arr;
	}
	
	public function __clone()
	{
		trigger_error('Clone is not allow' ,E_USER_ERROR);
	}
	
}
	
/* 公用函数 */
if (!function_exists("do_post"))
{
	function do_post($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);
		
		curl_close($ch);
		return $ret;
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