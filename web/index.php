<?php
/**
 * 框架入口
 *
 */
header('Content-type: text/html; charset=UTF-8');
require_once dirname(__FILE__) . '/init.php';
// 调试、错误信息开关
if((isset($_REQUEST['debug']) && $_REQUEST['debug'] == DEBUG_OPEN) || (isset($_COOKIE['debug']) && $_COOKIE['debug'] == DEBUG_OPEN)){
    define('IS_DEBUG', true);
    ini_set('display_errors', 1);
    error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
    if(!isset($_COOKIE['debug'])){
        cookie('debug', DEBUG_OPEN, 0, '/',$GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
    }
}else{
    define('IS_DEBUG', false);
    ini_set('display_errors', 0);
    error_reporting(0);
}

#debug
if(IS_DEBUG){
    require_once ROOT_PATH . 'lib/Third/FirePHP/FirePHP.class.php';
    require_once ROOT_PATH . 'lib/Third/FirePHP/Fb.php';
    include_once ROOT_PATH . 'lib/Function/Func.debug.php';
}

// 路由分发
Core_Router::dispatch();