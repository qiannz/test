<?PHP
/**
 * 库与服务器的对应关系(可找出某库在哪台服务器上)
 * 以及各库相关账号密码信息
 * 注：每增加一个数据库，都需修改本文件并增加一组配置
 */

$dbconf = array();
$dbconf['PERSISTENT_CONTENT'] = false; // 持久性连接

$dbconf['superbuy']['num'] = 1; // 分几个库，默认不填或1，则表示不分库，最大16
$dbconf['superbuy']['cfg']['master']['host'] = '127.0.0.1';
$dbconf['superbuy']['cfg']['master']['user'] = 'root';
$dbconf['superbuy']['cfg']['master']['pass'] = 'root';

/*
$dbconf['discount_line']['cfg']['master']['host'] = '127.0.0.1';
$dbconf['discount_line']['cfg']['master']['user'] = 'root';
$dbconf['discount_line']['cfg']['master']['pass'] = '';

$dbconf['message']['cfg']['master']['host'] = '127.0.0.1';
$dbconf['message']['cfg']['master']['user'] = 'root';
$dbconf['message']['cfg']['master']['pass'] = '';

*/
$GLOBALS['dbconf'] = $dbconf;