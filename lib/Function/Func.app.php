<?php

/**
 * 应用函数（修改请谨慎）
 *
 */

/**
 * 用于 AJAX 响应输出 JSON
 *
 * @param string $msg
 * @param string $resultType success|error|warnings|tips
 * @param string $extra
 */
function _exit($msg, $resultType = '', $extra = '') {
    exit(json_encode(array('msg'=>$msg, 'res'=>$resultType, 'extra'=>$extra)));
}

function _sexit($msg, $result = '', $extra = '') {
	if(isset($_GET['jsoncallback']) && !empty($_GET['jsoncallback'])) {
		exit($_GET['jsoncallback'] .'('.json_encode(array('msg'=>$msg, 'res'=>$result, 'extra' => $extra)).')');
	} else {
		exit(json_encode(array('msg'=>$msg, 'res'=>$result, 'extra' => $extra)));
	}
}
/**
 * 格式化时间戳
 *
 * @param int $timestamp
 * @param string $format
 * @return string
 */
function datex($timestamp, $format = 'Y-m-d H:i') {
    return date($format, $timestamp);
}

/**
 * 获取用户组名称
 * @param $params
 * @return 组名称
 */
function insert_groupName($params) {
	$db = Core_DB::get('superbuy', null, true);
	if($params['gid']){
		return $db->fetchOne("select `g_name` from `group` where `gid` = ". intval($params['gid']));
	}else{
		return '';
	}
}

/**
 * 全站 meta
 * @param $params  参数
 * @return meta
 */
function insert_siteMeta($params) {
	include_once RESOURCE_PATH . 'meta/config.php';
	
	$module = Core_Router::getModule();
	$controller = Core_Router::getController();
	$action = Core_Router::getAction();
	
	$key = $module . '|' . $controller . '|' . $action;
	$meta = $GLOBALS['metaConfig'][$key];
	
	switch ($key){
		//商品列表页
		case 'Home|Good|list':
				$title = str_replace(array('{region}','{circle}','{brand}','{store}'), array($params['region'],$params['circle'],$params['brand'],$params['store']), $meta[0]);
				$keywords = str_replace(array('{region}','{circle}','{brand}','{store}'), array($params['region'],$params['circle'],$params['brand'],$params['store']), $meta[1]);
				$description = str_replace(array('{region}','{circle}','{brand}','{store}'), array($params['region'],$params['circle'],$params['brand'],$params['store']), $meta[2]);
			break;
		//店铺详情页
		case 'Home|Shop|show':
			$title = str_replace(array('{shop}'), array($params['shop']), $meta[0]);
			$keywords = str_replace(array('{shop}'), array($params['shop']), $meta[1]);
			$description = str_replace(array('{shop}','{region}','{circle}','{brand}','{store}'), array($params['shop'],$params['region'],$params['circle'],$params['brand'],$params['store']), $meta[2]);
			break;
		//店铺WAP详情页
		case 'Home|Good|wap':
		//商品详情页
		case 'Home|Good|show':
			$title = str_replace(array('{good}','{shop}'), array($params['good'],$params['shop']), $meta[0]);
			$keywords = str_replace(array('{good}','{shop}','{brand}'), array($params['good'],$params['shop'],$params['brand']), $meta[1]);
			$description = str_replace(array('{good}','{shop}'), array($params['good'],$params['shop']), $meta[2]);
			break;
		//优惠券详情页
		case 'Home|Ticket|show':
		case 'Home|Ticket|wap':
		case 'Home|Ticket|wapShow':
			$title = str_replace(array('{ticket}','{shop}'), array($params['ticket'],$params['shop']), $meta[0]);
			$keywords = str_replace(array('{ticket}','{shop}','{brand}'), array($params['ticket'],$params['shop'],$params['brand']), $meta[1]);
			$description = str_replace(array('{ticket}','{shop}'), array($params['ticket'],$params['shop']), $meta[2]);
			break;
		//优惠券适用商品页
		case 'Home|Good|more':
			$title = str_replace(array('{ticket}','{shop}'), array($params['ticket'],$params['shop']), $meta[0]);
			$keywords = str_replace(array('{ticket}'), array($params['ticket']), $meta[1]);
			$description = str_replace(array('{ticket}'), array($params['ticket']), $meta[2]);
			break;

			break;
			//品牌详情页
		case 'Home|Brand|show':
			$title = str_replace(array('{brand}'), array($params['brand']), $meta[0]);
			break;
		//搜索结果页
		case 'Home|Search|list':
			$title = str_replace(array('{keyword}'), array($params['keyword']), $meta[0]);
			break;
		//商场详情页VIEW
		case 'Home|Market|show':
			$title = str_replace(array('{market}'), array($params['market']), $meta[0]);
			break;
		//品牌首页
		case 'Home|Brand|list':
		//品牌大全
		case 'Home|Brand|all':
		//商场首页
		case 'Home|Market|list':
		case 'Home|Good|add':			
		case 'Home|Circle|show':		
		case 'Home|Suser|myGood':	
		case 'Home|Suser|goodEdit':
		case 'Home|Suser|add':
		case 'Home|Suser|shopEdit':
		case 'Home|Suser|couponList':
		case 'Home|Suser|couponEdit':
		case 'Home|Suser|addCoupon':
		case 'Home|Suser|valid':
			$title = $meta[0];
			break;
		default:
			$title = $meta[0];
			$keywords = $meta[1];
			$description = $meta[2];
			break;
	}
	$metaStr = '';
	if($title){
		$metaStr .= '<title>'.$title.'</title>'."\r\n";
	}
	
	if($keywords){
		$metaStr .= '<meta name="keywords" content="'.$keywords.'" />'."\r\n";
	}
	
	if($description){
		$metaStr .= '<meta name="description" content="'.$description.'" />';
	}
	
	return $metaStr;
}
/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder) {
	$reval = false;

	if (!file_exists($folder))
	{
		/* 如果目录不存在则尝试创建该目录 */
		@umask(0);

		/* 将目录路径拆分成数组 */
		preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

		/* 如果第一个字符为/则当作物理路径处理 */
		$base = ($atmp[0][0] == '/') ? '/' : '';

		/* 遍历包含路径信息的数组 */
		foreach ($atmp[1] AS $val)
		{
			if ('' != $val)
			{
				$base .= $val;

				if ('..' == $val || '.' == $val)
				{
					/* 如果目录为.或者..则直接补/继续下一个循环 */
					$base .= '/';

					continue;
				}
			}
			else
			{
				continue;
			}

			$base .= '/';

			if (!file_exists($base))
			{
				/* 尝试创建目录，如果创建失败则继续循环 */
				if (@mkdir(rtrim($base, '/'), 0777))
				{
					@chmod($base, 0777);
					$reval = true;
				}
			}
		}
	}
	else
	{
		/* 路径已经存在。返回该路径是不是一个目录 */
		$reval = is_dir($folder);
	}

	clearstatcache();

	return $reval;
}

function create_folders($dir){
	return is_dir($dir) or (create_folders(dirname($dir)) and mkdir($dir,0777));
}

/**
 * 清除字符串多余的空白，多个只保留一个，去除换行符 
 * @param  $content 字符串内容
 * @return string
 */
function remove_word_newline($content = NULL) {
	if($content){
		$content = trim(strip_tags($content));
		$content = str_replace(array("\r", "\n", "\r\n"), '', $content);
		$content = preg_replace("/([\s ]{2,})/", "\\1", $content);
		$content = preg_replace("/((&nbsp;){2,})/", "\\2", $content);
	}
	return $content;
}
/**
 * 判断远程文件是否存在
 * @param unknown_type $file_url
 */
function url_file_exists($file_url) {
	$ch = curl_init();
	$timeout = 10;
	curl_setopt ($ch, CURLOPT_URL, $file_url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	
	$contents = curl_exec($ch);
	if (preg_match("/404/", $contents)){
		return false;
	}else{
		return true;
	}
}

function get_avatar($uid, $size = '', $type = '') {
	if($size){
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'small';
	}	
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$avatar_dir = ROOT_PATH.'web/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/';
	if(!is_dir($avatar_dir)){
		make_dir($avatar_dir);
	}
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

function get_avatar_real($uid) {
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$avatar_dir = ROOT_PATH.'web/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/';
	if(!is_dir($avatar_dir)){
		make_dir($avatar_dir);
	}
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_real'."_avatar";
}
/**
 * 获取文件大小
 * @param unknown_type $url
 * @return string|number
 */
function getFileSize($url) {
	if(function_exists('file_get_contents')){
		return strlen(file_get_contents($url));
	}
	$url = parse_url($url);	
	$fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$error);
	if($fp){
		fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n");
		fputs($fp,"Host:$url[host]\r\n\r\n");
		while(!feof($fp)){
			$tmp = fgets($fp);
			if(trim($tmp) == ''){
				break;
			}else if(preg_match('/Content-Length:(.*)/si',$tmp,$arr)){
				return trim($arr[1]);
			}
		}
		return 0;
	}else{
		return 0;
	}
}

/**
 * 执行时间日志
 * @param unknown_type $debugFileName
 */
function executionTimeLog($debugFileName) {
	$degbugDir = ROOT_PATH.'var/logs/'.date('Y').'/'.date('m').'/'.date('d').'/';
	if(!is_dir($degbugDir)){
		make_dir($degbugDir);
	}
	$startTime = $GLOBALS['__starttime'];
	$endTime =  microtime(true);
	file_put_contents($degbugDir.$debugFileName, date('Y-m-d H:i:s')."： ".round(($endTime - $startTime) * 1000,1).' ms'."\r\n", FILE_APPEND);
}

function logLog($log_name, $log_content, $log_path = null) {
	if(is_null($log_path)){
		$log_path = ROOT_PATH.'var/logs/';
	} else {
		if(substr($log_path, -1) != '/') {
			$log_path .= '/';
		}
		make_dir($log_path);
	}
	
	file_put_contents($log_path.$log_name, date('Y-m-d H:i:s')."： ". $log_content ."\r\n", FILE_APPEND);	
}
/**
 * 文件夹删除
 * @param unknown_type $directory 路径
 * @param unknown_type $empty 是否删除文件夹本身  默认删除
 * @return boolean
 */
function deleteAll($directory, $empty = false) {
	if(substr($directory,-1) == "/") {
		$directory = substr($directory,0,-1);
	}

	if(!file_exists($directory) || !is_dir($directory)) {
		return false;
	} elseif(!is_readable($directory)) {
		return false;
	} else {
		$directoryHandle = opendir($directory);
		while(($file = @readdir($directoryHandle)) !== false) {
			if($file != '.' && $file != '..') {
				$path = $directory . "/" . $file;

				if(is_dir($path)) {
					deleteAll($path);
				} else {
					unlink($path);
				}
			}
		}

		closedir($directoryHandle);

		if($empty == false) {
			if(!rmdir($directory)) {
				return false;
			}
		}
		return true;
	}
}

function cookie($cookie_name, $cookie_value, $cookie_time = 0, $cookie_path = '/', $cookie_domain = '', $secure = false, $httponly = false) {
	$cookie_path = !$cookie_path ? $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'] : $cookie_path;
	$cookie_domain = !$cookie_domain ? $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'] : $cookie_domain;
	setcookie(
		$cookie_name, 
		$cookie_value, 
		$cookie_time, 
		$cookie_path, 
		$cookie_domain, 
		$secure, 
		$httponly
	);	
}

//数组转对象
function arrayToObject($e) {
	if( gettype($e)!='array' ) return;
	foreach($e as $k=>$v){
		if( gettype($v)=='array' || getType($v)=='object' ) {
			$e[$k]=(object)arrayToObject($v);
		}
	}
	return (object)$e;
}
//对象转数组
function objectToArray($e) {
	$e=(array)$e;
	foreach($e as $k=>$v){
		if( gettype($v)=='resource' ) return;
		if( gettype($v)=='object' || gettype($v)=='array' ) {
			$e[$k]=(array)objectToArray($v);
		}
	}
	return $e;
}
//删除多余的斜杠
function cancelSlash($s) {
	$ns = preg_replace('/(.*?)(?:\/(?!\w)|$)/', '$1', $s);
	return $ns;
}

function enBase64($enstring) {
	return base64_encode($GLOBALS['GLOBAL_CONF']['hash_code'].$enstring);
}

function deBase64($destring) {
	return str_replace($GLOBALS['GLOBAL_CONF']['hash_code'], '', base64_decode($destring));
}


/**
 *计算某个经纬度的周围某段距离的正方形的四个点
 *
 *@param lng float 经度
 *@param lat float 纬度
 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
 *@return array 正方形的四个点的经纬度坐标
*/
function returnSquarePoint($lng, $lat,$distance = 0.5){
	$earth_radius = 6371; //地球半径，平均半径为6371km
	$dlng =  2 * asin(sin($distance / (2 * $earth_radius)) / cos(deg2rad($lat)));
	$dlng = rad2deg($dlng);
	 
	$dlat = $distance/$earth_radius;
	$dlat = rad2deg($dlat);
	 
	return array(
			'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
			'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
			'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
			'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
	);
	//使用此函数计算得到结果后，带入sql查询。
	//$squares = returnSquarePoint($lng, $lat);
	//$info_sql = "select id,locateinfo,lat,lng from `lbs_info` where lat<>0 and lat>{$squares['right-bottom']['lat']} and lat<{$squares['left-top']['lat']} and lng>{$squares['left-top']['lng']} and lng<{$squares['right-bottom']['lng']} ";
}


/**
 *  @desc 根据两点间的经纬度计算距离
 *  @param float $lat 纬度值
 *  @param float $lng 经度值
 *  返回值单位：米
 */
function getDistance($lat1, $lng1, $lat2, $lng2)
{
	$earthRadius = 6367000; //approximate radius of earth in meters

	/*
	 Convert these degrees to radians
	to work with the formula
	*/

	$lat1 = ($lat1 * pi() ) / 180;
	$lng1 = ($lng1 * pi() ) / 180;

	$lat2 = ($lat2 * pi() ) / 180;
	$lng2 = ($lng2 * pi() ) / 180;

	/*
	 Using the
	Haversine formula

	http://en.wikipedia.org/wiki/Haversine_formula

	calculate the distance
	*/

	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  
	$stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	$calculatedDistance = $earthRadius * $stepTwo;

	return round($calculatedDistance);
}



/**
 * 复制文件
 * @param unknown_type $path 源文件/文件夹
 * @param unknown_type $dest 目标文件/文件夹
 * @return boolean
 */
function copy_r( $path, $dest ) {
	if( is_dir($path) )
	{
		@mkdir( $dest );
		$objects = scandir($path);
		if( sizeof($objects) > 0 )
		{
			foreach( $objects as $file )
			{
				if( $file == "." || $file == ".." )
					continue;
				// go on
				if( is_dir( $path.DS.$file ) )
				{
					copy_r( $path.DS.$file, $dest.DS.$file );
				}
				else
				{
					copy( $path.DS.$file, $dest.DS.$file );
				}
			}
		}
		return true;
	}
	elseif( is_file($path) )
	{
		return copy($path, $dest);
	}
	else
	{
		return false;
	}
}

function convertUTF8($str)
{
	if(empty($str)) return '';
	return  iconv('gb2312', 'utf-8', $str);
}

function convToUtf8($str)
{
	if( mb_detect_encoding($str,"UTF-8, ISO-8859-1, GBK")!="UTF-8" )
	{
		return  iconv("gbk","utf-8",$str);
	}
	else
	{
		return $str;
	}
}

function specialHtmlConversion($str) {
	return str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $str);
}

function _isset($a) {
	if(isset($a)) {
		return 1;
	}
	return 0;
}

//MD5方式签名
function MD5sign($okey,$odata)
{
	$signdata=hmac("",$odata);
	return hmac($okey,$signdata);
}

function hmac ($key, $data)
{
	//$key = iconv('gb2312', 'utf-8', $key);
	//$data = iconv('gb2312', 'utf-8', $data);
	$b = 64;
	if (strlen($key) > $b) {
		$key = pack("H*",md5($key));
	}
	$key = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad ;
	$k_opad = $key ^ $opad;
	return md5($k_opad . pack("H*",md5($k_ipad . $data)));
}

function  uuid( $prefix  =  '' )
{
	$chars  = md5(uniqid(mt_rand(), true));
	$uuid   =  substr ( $chars ,0,8) .  '-' ;
	$uuid  .=  substr ( $chars ,8,4) .  '-' ;
	$uuid  .=  substr ( $chars ,12,4) .  '-' ;
	$uuid  .=  substr ( $chars ,16,4) .  '-' ;
	$uuid  .=  substr ( $chars ,20,12);
	return   $prefix  .  $uuid ;
}

function getExtensionName($img_name)
{
	if(($pos=strrpos($img_name,'.'))!==false)
		return (string)substr($img_name,$pos+1);
	else
		return '';
}