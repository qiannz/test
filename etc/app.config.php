<?php
define('IN_BUY', true);
define('PAGESIZE', 10);
define('DS', DIRECTORY_SEPARATOR);
define('DEFINED_USER_NAME', 'leofan05'); // 默认后台新增商品对应用户名

//任务中奖触发上限
$GLOBAL_CONF['TASK_WINNING_UPPER_TRIGGER'] = 20;
//十全大补任务连续天数
$GLOBAL_CONF['TASK_WINNING_DAYS'] = 10;
// 分发器默认配置
$GLOBAL_CONF['Default_Module'] = 'home'; // 模块
$GLOBAL_CONF['Default_Controller'] = 'index'; // 控制器
$GLOBAL_CONF['Default_Action'] = 'index'; // 方法

$GLOBAL_CONF['Default_Manager_Module_Path'] = 'admin'; // 后台Manager_Module_Path
//后台TITLE
$GLOBAL_CONF['ADMIN_PAGE_TITLE'] = '名品导购后台管理系统';
//前端WWW地址
$GLOBAL_CONF['SITE_URL'] = 'http://local.buy.com';
$GLOBAL_CONF['MPSHOP_SITE_URL'] = 'http://local.buy.com';
//图片URL地址
$GLOBAL_CONF['IMG_URL'] = 'http://local.buy.com/data'; //http://img5.mplife.com
//主站URL地址
$GLOBAL_CONF['MAIN_SITE_URL'] = 'http://passport.mplife.com';
//COOKIE配置
$GLOBAL_CONF['COOKIE']['cookie_path'] = '/';
$GLOBAL_CONF['COOKIE']['cookie_admin_path'] = '/admin/';
$GLOBAL_CONF['COOKIE']['cookie_domain'] = '.buy.com';
$GLOBAL_CONF['COOKIE']['myshop_cookie_domain'] = '.buy.com';
$GLOBAL_CONF['COOKIE']['cookie_secure'] = false;

//密码混淆字符串
$GLOBAL_CONF['hash_code'] = 'superbuy20131024';

//缓存时间
$GLOBAL_CONF['Cache_Life_Time'] = 600; // 单位：秒
// Memcache 配置
$GLOBAL_CONF['Mem_Cache_Servers'][] = '192.168.120.204:12000'; // 参数的依次为IP:端口:轮询权重
$GLOBAL_CONF['Mem_Cache_Enabled'] = true;
//文件缓存
$GLOBAL_CONF['File_Cache_Enabled'] = true;


// 全局配置文件
$GLOBAL_CONF['JS_PATH'] = $GLOBAL_CONF['SITE_URL'];
$GLOBAL_CONF['CSS_PATH'] = $GLOBAL_CONF['SITE_URL'];
$GLOBAL_CONF['IMG_PATH'] = '';

// JS、CSS的版本号
$GLOBAL_CONF['WEB_VERSION'] = '2013102406';

// MySQL Slow Query Time
$GLOBAL_CONF['MySQL_Slow_Query_Time'] = 0.1;

//短信帐号密码
$GLOBAL_CONF['SMS_User'] = 'report';
$GLOBAL_CONF['SMS_Pwd'] = '344B497EDF794EC048708D6B26596E93';

//根据地址获取经纬度
$GLOBAL_CONF['Get_Latitude_And_Longitude'] = 'http://api.map.baidu.com/geocoder';

//高德地图根据经纬度获取地址URL
$GLOBAL_CONF['Get_Address_Bylnglat_Formamap'] = 'http://restapi.amap.com/v3/geocode/regeo';
//高德地图根据地址获取经纬度URL
$GLOBAL_CONF['Get_Latitude_And_Longitude_Formamap'] = 'http://restapi.amap.com/v3/geocode/geo';

//与主站同步验证会员地址,验证 用户名和密码,查询用户信息
$GLOBAL_CONF['Auth_Http_Uri'] = 'http://passport.mplife.com/webservices/MPUserService.asmx?wsdl';
$GLOBAL_CONF['Auth_User'] = 'mpuserinfo';
$GLOBAL_CONF['Auth_Pwd'] = '175F88ED196184E1422E92892362B172';

//彩信
$GLOBAL_CONF['Mms_Http_Uri'] = 'http://webservice.mplife.com/qrcode/qrcodeservices.asmx?wsdl';
$GLOBAL_CONF['Mms_User'] = 'mpqrcodewebservice';
$GLOBAL_CONF['Mms_Pwd'] = '175F88ED196184E1422E92892362B172';

//IOS 接口验证公钥
$GLOBAL_CONF['Auth_Key'] = 'BBBFB38A';
//名品街接口公钥
$GLOBAL_CONF['My_User_Auth_Key'] = 'buy@mplife@2014^by#from$superbuy';
//券同步，验证，领取
$GLOBAL_CONF['Ticket_Auth_Url'] = 'http://webservice.mplife.com/CmsDataProvider/Get.asmx?wsdl';
$GLOBAL_CONF['Ticket_Auth_User'] = 'o2o';
$GLOBAL_CONF['Ticket_Auth_Password'] = '31b23f221123244efd493b2316f0c9';
//现金券明细查询地址URL
$GLOBAL_CONF['Ticket_Detail_Url'] = 'http://superbuy.mplife.com/interface/SuperBuyHandler.ashx';
//名品街任务活动的开始时间/结束时间
$GLOBAL_CONF['TASK_START_TIME'] = '2014-04-1';
$GLOBAL_CONF['TASK_END_TIME'] = '2014-08-12';
//名品街(街友爱分享)任务中奖虚加金额
$GLOBAL_CONF['TASK_SUM_MONEY'] = 50000;
//名品街(畅游迪拜)上传商品虚加数量
$GLOBAL_CONF['TASK_SUM_GOOD'] = 500;
//名品街商城商品默认结束时间2036-12-31 23:59:59
$GLOBAL_CONF['COMMODITY_DEFAULT_END_TIME'] = '2036-12-31 23:59:59';

$GLOBALS['GLOBAL_CONF'] = $GLOBAL_CONF;
