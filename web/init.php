<?php
/**
 * 框架初始化
 *
 */
//开取DEBUG 密码字串
define('DEBUG_OPEN', 'buy');
// 设置时区
date_default_timezone_set('PRC');

// 性能测试 - 程序开始执行时间、消耗内存
$GLOBALS['__starttime'] = $GLOBALS['__runtime'] = microtime(true);
$GLOBALS['__runmsg'] = $GLOBALS['__queries'] = array();
$GLOBALS['__memoryuse'] = memory_get_usage();

define('ROOT_PATH', str_replace('\\', '/', dirname(dirname(__FILE__)) . '/'));
define('RESOURCE_PATH', ROOT_PATH . 'resource' . '/');
define('VAR_PATH', ROOT_PATH . 'var' . '/');
define('LOG_PATH', VAR_PATH . 'logs' . '/');
define('WEB_ROOT', ROOT_PATH . 'web/');

// ROUTER 规则
define('MODULE_DIR', 'app/Controller/');
define('CONTROLLER_DIR', '/');
define('CONTROLLER_PREFIX', 'Controller_');
define('CONTROLLER_OFFSET', '_');

// 设置缺省包含目录
set_include_path('.' . PATH_SEPARATOR . ROOT_PATH . 'lib' . PATH_SEPARATOR . ROOT_PATH . 'app');

// 全局变量
require_once ROOT_PATH . 'etc/app.config.php';

//来源
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/');
//本源
define('HTTP_URI', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//时间
define('REQUEST_TIME', $_SERVER['REQUEST_TIME']);

// 全局函数
require_once ROOT_PATH . 'lib/Function/Func.core.php';
require_once ROOT_PATH . 'lib/Function/Func.app.php';
require_once ROOT_PATH . 'lib/Third/360_safe3.php';
//IP
define('CLIENT_IP', Custom_Client::getUserIp());

// GPC 过滤
if(!get_magic_quotes_gpc()){
    $_GET = saddslashes($_GET);
    $_POST = saddslashes($_POST);
    $_COOKIE = saddslashes($_COOKIE);
    $_REQUEST = saddslashes($_REQUEST);
}

/* 判断是否支持gzip模式 */
if (function_exists('ob_gzhandler'))
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}