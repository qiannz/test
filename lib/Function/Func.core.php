<?php

/**
 * 核心函数库（修改请谨慎）
 *
 */

/**
 * 自动加载类库
 *
 * @param string $className
 */
function __autoload($className){
    $classPath = $className;
    if(strpos($className, '_') !== false){
        $classPath = str_replace('_', DIRECTORY_SEPARATOR, $className);
    }
    require_once $classPath . '.php';
    if(!class_exists($className, false)){
        trigger_error('<br />Unable to load class: ' . $className, E_USER_WARNING);
        exit();
    }
}

/**
 * 载入模型 Model
 *
 * @param string $modelName
 * @return object
 */
function Model($modelName, $forceLocal = true){
    if($forceLocal || !isRemoteModel($modelName)){ // 载入本地 model
        $model = getSingleton('Model_' . $modelName);
        if(isDebug()){
            @$GLOBALS['LOADED_MODELS_LOCAL'][$modelName]++;
        }
    }else{ // 载入远程 model
        $model = Core_RPC_Client::loadModel($modelName);
        if(isDebug()){
            @$GLOBALS['LOADED_MODELS_REMOTE'][$modelName]++;
        }
    }
    return $model;
}

/**
 * 判断某模型是否远程 model
 *
 * @param string $modelName
 * @return bool
 */
function isRemoteModel($modelName){
    return (isset($GLOBALS['RPC_ModelConf'][$modelName]) && !empty($GLOBALS['RPC_ModelConf'][$modelName]));
}

/**
 * 单例模式
 *
 * @param string $className
 * @param string $globalVar
 * @return object
 */
$GLOBALS['SINGLETON'] = array();
function getSingleton($className, $globalVar = 'default'){
    if(isset($GLOBALS['SINGLETON'][$globalVar][$className])){
        $classObj = $GLOBALS['SINGLETON'][$globalVar][$className];
        if(is_object($classObj)){
            return $classObj;
        }
    }
    $classObj = new $className();
    $GLOBALS['SINGLETON'][$globalVar][$className] = $classObj;
    return $classObj;
}

/**
 * 包含模板
 *
 * @param string $path
 * @return string
 */
function template($path = ''){
    if(substr($path, -4, 4) != '.php'){
        $path .= '.php';
    }
    return ROOT_PATH . 'tpls' . DIRECTORY_SEPARATOR . $path;
}

/**
 * 区块模板
 *
 * @param string $path
 * @param array $extraParams 附加参数
 * @return string
 */
function blockTemplate($path, array $extraParams = array()){
    if($extraParams){
        extract($extraParams);
    }
    unset($extraParams);
    include template($path);
}

/**
 * 301跳转
 *
 * @param string $url
 * @param bool $is301
 * @return void
 */
function header301($url, $is301 = false){
    if($is301){ // 301 永久重定向
        header('HTTP/1.1 301 Moved Permanently');
    }
    header('Location: ' . $url);
    exit();
}

/**
 * 将对字符串/数组的特殊字符进行转义
 *
 * @param mixed $string
 * @return mixed
 */
function saddslashes($string){
    if(is_array($string)){
        foreach($string as $key=>$val){
            $string[$key] = saddslashes($val);
        }
    }else{
        $string = addslashes($string);
    }
    return $string;
}

/**
 * 递归方式的对变量中的特殊字符去除转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashes_deep($value)
{
	if (empty($value))
	{
		return $value;
	}
	else
	{
		return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes(urldecode($value));
	}
}

/**
 * 组合控制器地址
 *
 * @param string $module
 * @param string $controller
 * @param string $action
 * @return string
 */
function xurl($module, $controller, $action){
    //  return 'index.php?m=' . $module . '&c=' . $controller . '&a=' . $action . '&';
    return '/' . $module . '/' . $controller . '/' . $action . '/?';
}

/**
 * 整形化
 *
 * @param bigint $num
 * @return bigint
 */
function xintval($num){
    return preg_match('/^\-?[0-9]+$/', $num) ? $num : 0;
}

/**
 * 逗号连接
 *
 * @param array $array
 * @return string
 */
function ximplode($array){
    return empty($array) ? 0 : '\'' . implode("','", is_array($array) ? $array : array($array)) . '\'';
}

/**
 * 是否调试模式
 *
 * @return bool
 */
function isDebug(){
    return (defined('IS_DEBUG') && IS_DEBUG);
}
/**
 * 获取文件后缀名
 * Enter description here ...
 * @param $filename
 */
function file_ext($filename)
{
    return trim(substr(strrchr($filename, '.'), 1, 10));
}
/**
 * 判断是否登录
 * Enter description here ...
 */
function is_login(& $db){
	$module = strtolower(Core_Router::getModule());
	$controller = strtolower(Core_Router::getController());
	$action = strtolower(Core_Router::getAction());
	
	if(!isset($_COOKIE['_ad_id']) && $action != 'login' && $action != 'captcha' && $action != 'upload')
	{
		Custom_Common::jumpto('/admin/index/login');
	}
	
	
	if(!empty($_COOKIE['_ad_id']) && $action != 'login' && $action != 'logout' && $action != 'captcha' && $action != 'upload')
	{
		$row = $db->fetchRow("select id, userid, is_disabled, logintime, loginip from `oto_admin` where `id` = '" . deBase64($_COOKIE['_ad_id']) . "'");
	    if($row['userid'] == deBase64($_COOKIE['_ad_userid']) && $row['is_disabled'] == 0)
	    {
			$db->update(oto_admin, array('logintime' => REQUEST_TIME, 'loginip' => CLIENT_IP),"id = '{$row['id']}'");
		} 
		else
		{
			Custom_Common::jumpto('/admin/index/login');
		}
		
	    if(!is_file(ROOT_PATH.'var'.DIRECTORY_SEPARATOR.'manager'.DIRECTORY_SEPARATOR.$row['userid'].'.php')){
	    	Custom_Common::jumpto('/admin/index/login');
	    }
	    $menu = @include(ROOT_PATH.'var'.DIRECTORY_SEPARATOR.'manager'.DIRECTORY_SEPARATOR.$row['userid'].'.php');
	    
	    $nowPath = '/'.$module.'/'.$controller;
	    $allow = false;
	    foreach ($menu as $key => $module){
	    	if(!empty($module['children'])){
	            foreach ($module['children'] as $skey => $children) {
		            if(strpos($children['url'], $nowPath) !== false){
			            $allow = true;
						return array($key,$skey);
			            break;
		            }
	            }
	    	}
	    }
	    
	    if(!$allow){
	         Custom_Common::jumpto('/admin/index/logout');   	
	    }
	}
	
}
