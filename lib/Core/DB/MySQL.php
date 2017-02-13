<?php

/**
 * MySQL 原生数据库操作基类
 *
 */

class Core_DB_MySQL {
    /**
     * 错误级别
     *
     * @var int
     */
    protected $_errorlevel = 9;
    
    /**
     * 存放主库的连接
     *
     * @var object
     */
    protected $_writeDbConn = NULL;
    
    /**
     * 存放从库的连接
     *
     * @var object
     */
    protected $_readDbConn = NULL;
    
    /**
     * 存放当前DB连接
     *
     * @var object
     */
    protected $_dbConn = NULL;
    
    /**
     * 主库连接配置信息
     *
     * @var array
     */
    public $writeConf = array();
    
    /**
     * 从库连接配置信息
     *
     * @var object
     */
    public $readConf = array();
    
    /**
     * 是否进行长连接
     *
     * @var bool
     */
    public $persistent = false;
    
    /**
     * 是否强制连接主库
     *
     * @var bool
     */
    public $forceMaster = false;
    
    /**
     * 存放最后一次查询的 SQL
     *
     * @var array
     */
    public $_lastSql = array('sql'=>'', 'params'=>'');
    
    /**
     * 存放当前连接的配置信息
     *
     * @var array
     */
    protected $_currentConf;
    
    /**
     * 构造函数，初始化配置
     *
     * @param array $writeConf
     * @param array $readConf
     * @param bool $forceMaster
     * @param bool $persistent
     */
    protected $_version;
    
    public function __construct($writeConf, $readConf, $forceMaster, $persistent){
        $this->writeConf = $writeConf;
        $this->readConf = $readConf;
        $this->forceMaster = $forceMaster;
        $this->persistent = $persistent;
    }
    
    /**
     * 获取主库的“写”数据连接
     *
     * @param bool $forceConnect 是否强制重连
     * @return MySQL Conn Link
     */
    protected function _getDbWriteConn($forceConnect = false){
        // 强制重连，首先需断开已连的连接
        if($forceConnect){
            $this->_disconnectWrite();
        }
        
        // 判断是否已经连接
        if($this->_writeDbConn && is_resource($this->_writeDbConn)){
            return $this->_writeDbConn;
        }
        
        $dbConn = $this->_connect($this->writeConf);
        if(!$dbConn || !is_resource($dbConn)){
            return false;
        }
        
        $this->_writeDbConn = $dbConn;
        return $this->_writeDbConn;
    }
    
    /**
     * 获取从库的“读”数据连接
     *
     * @param bool $forceConnect 是否强制重连
     * @return MySQL Conn Link
     */
    protected function _getDbReadConn($forceConnect = false){
        // 强制重连，首先需断开已连的连接
        if($forceConnect){
            $this->_disconnectRead();
        }
        
        // 判断是否已经连接
        if($this->_readDbConn && is_resource($this->_readDbConn)){
            return $this->_readDbConn;
        }
        
        // 没有从库配置则直接连主库
        $arrHost = explode('|', $this->readConf['host']);
        if(!is_array($arrHost) || empty($arrHost)){
            return $this->_getDbWriteConn();
        }
        
        // 乱序随机选择从库
        shuffle($arrHost);
        foreach($arrHost as $host){
            $this->readConf['host'] = $host;
            $dbConn = $this->_connect($this->readConf);
            if($dbConn && is_resource($dbConn)){
                $this->_readDbConn = $dbConn;
                return $this->_readDbConn;
            }
        }
    }
    
    /**
     * 连接数据库
     *
     * @param array $conf
     * @param bool $isPconnect
     * @return MySQL Object
     */
    protected function _connect($conf = array(), $isPconnect = false){
        try{
            $func = ($this->persistent || $isPconnect) ? 'mysql_pconnect' : 'mysql_connect';
            $dbConn = $func($conf['host'], $conf['user'], $conf['pass']);
            $this->_version  = mysql_get_server_info($dbConn);
            mysql_select_db($conf['path'], $dbConn);
            mysql_set_charset('UTF8', $dbConn); // 等价于 mysql_query('SET NAMES UTF8');
            $this->_currentConf = $conf;
        }catch(Exception $e){
            Core_Exception::exceptionProcess($e, implode('|', $conf) . ' Connection Failed', $this->_errorlevel);
            return false;
        }
        return $dbConn;
    }
    
    /**
     * 释放数据库连接
     */
    public function disconnect(){
        $this->_disconnectWrite(); // 释放写连接
        $this->_disconnectRead(); // 释放读连接
        if(isset($this->_dbConn) && is_resource($this->_dbConn)){ // 释放临时连接
            $this->_dbConn = NULL;
        }
    }
    
    /**
     * 释放数据库“写”连接
     */
    protected function _disconnectWrite(){
        if(isset($this->_writeDbConn) && is_resource($this->_writeDbConn)){
            mysql_close($this->_writeDbConn);
            $this->_writeDbConn = NULL;
        }
    }
    
    /**
     * 释放数据库“读”连接
     */
    protected function _disconnectRead(){
        if(isset($this->_readDbConn) && is_resource($this->_readDbConn)){
            mysql_close($this->_readDbConn);
            $this->_readDbConn = NULL;
        }
    }
    
    /**
     * 选择数据库连接
     *
     * @param bool $forceMaster 是否强制连接主库
     * @return void
     */
    protected function _getChoiceDbConnect($forceMaster = false){
        $forceMaster = ($forceMaster || $this->forceMaster);
        if($forceMaster){
            $this->_dbConn = $this->_getDbWriteConn();
        }else{
            $this->_dbConn = $this->_getDbReadConn();
        }
    }
    
    /**
     * 写日志
     *
     * @param string $sql
     * @param array $params
     * @param decimal $time 本次消耗时间
     */
    protected function _writeLog($sql, $params, $time){
        $host = $this->_currentConf['host'];
        $dbname = $this->_currentConf['path'];
        Core_DB::writeSqlLog($host, $dbname, $sql, $params, $time);
    }
    
    /**
     * 执行操作的底层接口
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return MySQL Statement
     */
    protected function _autoExecute($sql, $params = array(), $forceMaster = false){
        try{
            if($params){ #关闭传参形式，规范SQL
                exit('MySQL parameters for SQL is not supported in this framework.');
            }
            
            $this->_lastSql = array('sql'=>$sql, 'params'=>$params);
            
            $sqlstarttime = microtime(true);
            
            $this->_getChoiceDbConnect($forceMaster);
            
            if(!$this->_dbConn){
                exit('DB connection lost.');
            }
            
            $query = mysql_query($sql, $this->_dbConn);
            $errno = mysql_errno($this->_dbConn);
            if($errno){
                // 错误补救处理机制
                $query = $this->_errorRetrieve($errno, $sql, $forceMaster);
                if(!$query){ // 补救失败，则跳出
                    return false;
                }
            }
            
            $sqlendtime = microtime(true);
            $this->_writeLog($sql, $params, $sqlendtime - $sqlstarttime);
            
            if(isDebug()){ #debug explain sql
                $sqltime = sprintf('%.4f', ($sqlendtime - $sqlstarttime));
                $explain = array();
                if(preg_match("/^(select )/i", $sql)){
                    $explain = mysql_fetch_assoc(mysql_query('EXPLAIN ' . $sql, $this->_dbConn));
                }
                $GLOBALS['__queries'][] = array('sql'=>$sql, 'time'=>$sqltime, 'explain'=>$explain);
            }
            
            return $query;
        
        }catch(Exception $e){
            Core_Exception::exceptionProcess($e, $this->getLogSql($sql, $params), $this->_errorlevel);
            exit();
        }
    }
    
    /**
     * 错误补救机制
     *
     * @param int $errno
     * @param string $sql
     * @param bool $forceMaster
     * @return bool/query
     */
    protected function _errorRetrieve($errno, $sql, $forceMaster){
        switch($errno){
            case 1146: // 切换库失败，则重新选择库后再执行 -- #todo silver 临时解决方案：
                

                mysql_select_db($this->_currentConf['path'], $this->_dbConn);
                $query = mysql_query($sql, $this->_dbConn);
                if($errno = mysql_errno($this->_dbConn)){
                    throw new Core_Exception('code:[' . $errno . '] ' . mysql_error($this->_dbConn));
                    return false;
                }
                return $query;
                
                break;
            case 2006: // MySQL server has gone away
                

                $this->reActivate(); // 重新激活和获取连接，重新给 $this->_writeDbConn 和 $this->_readDbConn 赋值
                $this->_getChoiceDbConnect($forceMaster); // 重新给 $this->_dbConn 赋值
                

                $query = mysql_query($sql, $this->_dbConn);
                if($errno = mysql_errno($this->_dbConn)){
                    throw new Core_Exception('code:[' . $errno . '] ' . mysql_error($this->_dbConn));
                    return false;
                }
                return $query;
                
                break;
            default:
                throw new Core_Exception('code:[' . $errno . '] ' . mysql_error($this->_dbConn));
                return false;
        }
    }
    
    /**
     * 执行一条 SQL （一般针对写操作，如 insert/update/delete）
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return int 影响行数
     */
    public function query($sql, $params = array(), $forceMaster = true){
        try{
            $query = $this->_autoExecute($sql, $params, $forceMaster);
            if($query){
                $rows = mysql_affected_rows($this->_dbConn);
                return $rows > 0 ? $rows : true;
            }else{
                return false;
            }
        }catch(Exception $e){
            Core_Exception::exceptionProcess($e, $this->getLogSql($sql, $params), $this->_errorlevel);
            return false;
        }
    }
    
    /**
     * 获取所有记录
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return array
     */
    public function fetchAll($sql, $params = array(), $forceMaster = false){
        $query = $this->_autoExecute($sql, $params, $forceMaster);
        if($query && mysql_num_rows($query) > 0){
            $data = array();
            while($row = mysql_fetch_assoc($query)){
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
    
    /**
     * 获取第一列
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return array
     */
    public function fetchCol($sql, $params = array(), $forceMaster = false){
        $query = $this->_autoExecute($sql, $params, $forceMaster);
        if($query && mysql_num_rows($query) > 0){
            $data = array();
            while($row = mysql_fetch_row($query)){
                $data[] = $row[0];
            }
            return $data;
        }
        return array();
    }
    
    /**
     * 获取键值对
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return array
     */
    public function fetchPairs($sql, $params = array(), $forceMaster = false){
        $query = $this->_autoExecute($sql, $params, $forceMaster);
        if($query && mysql_num_rows($query) > 0){
            $data = array();
            while($row = mysql_fetch_row($query)){
                $data[$row[0]] = $row[1];
            }
            return $data;
        }
        return array();
    }
    
    /**
     * 获取关联数组
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return array
     */
    public function fetchAssoc($sql, $params = array(), $forceMaster = false){
        $query = $this->_autoExecute($sql, $params, $forceMaster);
        if($query && mysql_num_rows($query) > 0){
            $data = array();
            while($row = mysql_fetch_assoc($query)){
                $key = current($row);
                $data[$key] = $row;
            }
            return $data;
        }
        return array();
    }
    
    /**
     * 获取一个单元格
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return array
     */
    public function fetchOne($sql, $params = array(), $forceMaster = false){
        $query = $this->_autoExecute($sql, $params, $forceMaster);
        if($query && mysql_num_rows($query) > 0){
            return mysql_result($query, 0);
        }
        return NULL;
    }
    
    /**
     * 获取单条记录
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return array
     */
    public function fetchRow($sql, $params = array(), $forceMaster = false){
        $query = $this->_autoExecute($sql, $params, $forceMaster);
        if($query && mysql_num_rows($query) > 0){
            return mysql_fetch_assoc($query);
        }
        return array();
    }
    
    /**
     * 删除
     *
     * @param string $table
     * @param array/string $whereArr
     * @param int $limit
     * @return int
     */
    public function delete($table, $whereArr, $limit = 1){
        $whereSql = Core_DB::getWhereSQL($whereArr);
        $sql = 'DELETE FROM `' . $table . '`' . ' WHERE ' . $whereSql . ($limit > 0 ? (' LIMIT ' . $limit) : '');
        return $this->query($sql);
    }
    
    /**
     * 插入记录
     *
     * @param string $table
     * @param array $setArr
     * @param bool $replace
     * @return int
     */
    public function insert($table, $setArr, $replace = false){
        $insertkeysql = $insertvaluesql = $comma = '';
        foreach($setArr as $key=>$value){
            $insertkeysql .= $comma . '`' . $key . '`';
            $insertvaluesql .= $comma . '\'' . $value . '\'';
            $comma = ', ';
        }
        $method = $replace ? 'REPLACE' : 'INSERT';
        $sql = $method . ' INTO `' . $table . '`' . '(' . $insertkeysql . ') ' . 'VALUES (' . $insertvaluesql . ')';
        $query = $this->_autoExecute($sql, NULL, true);
        if($query){
            $insertId = $this->lastInsertId();
            return $insertId ? $insertId : true;
        }
        return 0;
    }
    
    /**
     * 替换
     *
     * @param string $table
     * @param array $setArr
     * @return int
     */
    public function replace($table, $setArr){
        return $this->insert($table, $setArr, true);
    }
    
    /**
     * 更新记录
     *
     * @param string $table
     * @param array $setArr
     * @param array/string $whereArr
     * @param int $limit
     * @return int
     */
    public function update($table, $setArr, $whereArr, $limit = 1){
        $setSql = $comma = '';
        foreach($setArr as $key=>$value){
            $setSql .= $comma . '`' . $key . '`' . '=\'' . $value . '\'';
            $comma = ', ';
        }
        $whereSql = Core_DB::getWhereSQL($whereArr);
        $sql = 'UPDATE `' . $table . '`' . ' SET ' . $setSql . ' WHERE ' . $whereSql . ($limit > 0 ? (' LIMIT ' . $limit) : '');
        //logLog('sql.log', $sql);
        return $this->query($sql);
    }
    
    /**
     * 批量插入记录
     *
     * @param string $table
     * @param array $setArrs
     * @return int
     */
    public function insertBatch($table, $setArrs, $replace = false){
        $insertkeysqlGot = false;
        if(!$setArrs || !is_array($setArrs)){
            return false;
        }
        $insertkeysql = $insertvaluesql = $comma = '';
        foreach($setArrs as $setArr){
            $insertvaluesqlNode = $commaNode = '';
            foreach($setArr as $key=>$value){
                if(!$insertkeysqlGot){
                    $insertkeysql .= $commaNode . '`' . $key . '`';
                }
                $insertvaluesqlNode .= $commaNode . '\'' . $value . '\'';
                $commaNode = ', ';
            }
            $insertvaluesql .= $comma . '(' . $insertvaluesqlNode . ')';
            $insertkeysqlGot = true;
            $comma = ', ';
        }
        $method = $replace ? 'REPLACE' : 'INSERT';
        //$sql = 'INSERT INTO `' . $table . '` (' . $insertkeysql . ') VALUES ' . $insertvaluesql;
        $sql = $method . ' INTO `' . $table . '` (' . $insertkeysql . ') VALUES ' . $insertvaluesql;
        return $this->query($sql);
    }
    
    /**
     * 分页封装 fetchAll
     *
     * @param string $sql
     * @param int $start
     * @param int $count
     * @param mixed $params
     * @param bool $forceMaster
     * @return array
     */
    public function limitQuery($sql, $start, $pageSize, $fetchMethod = 'fetchAll', $params = array(), $forceMaster = false){
        $start = intval($start);
        if($start < 0){
            return array();
        }
        $pageSize = intval($pageSize);
        if($pageSize > 0){ // pageSize 为0时表示取所有数据
            $sql .= ' LIMIT ' . $pageSize;
            if($start > 0){
                $sql .= ' OFFSET ' . $start;
            }
        }
        return $this->$fetchMethod($sql, $params, $forceMaster);
    }
    
    /**
     * 获取自增ID
     *
     * @return lastInsertId
     */
    public function lastInsertId(){
        return mysql_result(mysql_query('SELECT last_insert_id()', $this->_dbConn), 0);
    
     //      return mysql_insert_id($this->_dbConn); // 不能用，当字段是 bigint 时返回的数据不对
    }
    
    /**
     * 事务开始
     */
    public function beginTransaction(){
        $this->_getChoiceDbConnect(true);
        mysql_query('BEGIN', $this->_dbConn);
    }
    
    /**
     * 事务提交
     */
    public function commit(){
        $this->_getChoiceDbConnect(true);
        mysql_query('COMMIT', $this->_dbConn);
    }
    
    /**
     * 事务回滚
     */
    public function rollBack(){
        $this->_getChoiceDbConnect(true);
        mysql_query('ROLLBACK', $this->_dbConn);
    }
    
    /**
     * 把带参数的 SQL 的转换为可记录的 Log
     *
     * @param string $sql
     * @param array $params
     * @return strig
     */
    public function getLogSql($sql = '', $params = array()){
        if(!$sql){
            $sql = $this->_lastSql['sql'];
            $params = $this->_lastSql['params'];
        }
        $host = $this->_currentConf['host'];
        $dbname = $this->_currentConf['path'];
        $return = $sql . (empty($params) ? '' : ' [' . implode(',', $params) . ']');
        return $return . "\nhost: " . $host . ', dbname: ' . $dbname;
    }
    
    /**
     * 执行 SQL 并返回 MySQL Statement
     *
     * @param string $sql
     * @param bool $forceMaster
     * @return MySQL Statement
     */
    public function execute($sql, $forceMaster = true){
        try{
            $query = $this->_autoExecute($sql, array(), $forceMaster);
            return $query ? $query : false;
        }catch(Exception $e){
            Core_Exception::exceptionProcess($e, $this->getLogSql($sql), $this->_errorlevel);
            return false;
        }
    }
    
    /**
     * MySQL fetch method
     *
     * @param MySQL Statement $query
     * @return arary
     */
    public function fetchArray($query){
        return mysql_fetch_assoc($query);
    }
    
    /**
     * 字段自增
     *
     * @param string $table
     * @param string $field
     * @param int $value 正数表示自增，负数表示自减
     * @param array/string $whereArr
     * @return int
     */
    public function incr($table, $field, $value, $whereArr, $limit = 1){
        $whereSql = Core_DB::getWhereSQL($whereArr);
        $limit = $limit > 0 ? 'LIMIT ' . $limit : '';
        $value = $value > 0 ? ' + ' . $value : $value;
        return $this->query("UPDATE `{$table}` SET `{$field}` = `{$field}` + '{$value}' WHERE {$whereSql} {$limit}");
    }
    
    /**
     * 字段自减
     *
     * @param string $table
     * @param string $field
     * @param int $value
     * @param array/string $whereArr
     * @return int
     */
    public function decr($table, $field, $value, $whereArr, $limit = 1){
        return $this->incr($table, $field, -$value, $whereArr, $limit);
    }
    
    /**
     * 检查一个表是否存在
     *
     * @param string $table
     * @return bool
     */
    public function checkTableExists($table){
        return (bool) $this->fetchRow("SHOW TABLES LIKE '{$table}'");
    }
    
    /**
     * 重新激活DB连接
     * 解决8小时内如果没有执行一条SQL，则连接超时问题（my.cnf => wait_timeout = 60）
     *
     * @return array('master' => 0/1, 'slave' => 0/1)
     */
    public function reActivate(){
        $result = array();
        
        // 重连“写”连接
        if($this->_writeDbConn && is_resource($this->_writeDbConn)){
            $result['write'] = 0;
            if($this->_ping($this->_writeDbConn) == -1){
                $this->_getDbWriteConn(true);
                $result['write'] = 1;
            }
        }
        
        // 重连“读”连接
        if($this->_readDbConn && is_resource($this->_readDbConn)){
            $result['read'] = 0;
            if($this->_ping($this->_readDbConn) == -1){
                $this->_getDbReadConn(true);
                $result['read'] = 1;
            }
        }
        
        return $result;
    }
    /**
     * 检测 MySQL server has gone away
     *
     * @param object $conn
     * @return int
     */
    protected function _ping($conn){
        if(!$conn || !is_resource($conn)){
            return -99;
        }
        if(!mysql_ping($conn)){
            return -1; // MySQL server has gone away
        }
        return 1;
    }
    
    public function version()
    {
        return $this->_version;
    }
    
    function autoReplace($table, $field_values, $update_values, $where = '')
    {
        $field_descs = $this->fetchAll('DESC ' . $table);

        $primary_keys = array();
        foreach ($field_descs AS $value)
        {
            $field_names[] = $value['Field'];
            if ($value['Key'] == 'PRI')
            {
                $primary_keys[] = $value['Field'];
            }
        }

        $fields = $values = array();
        foreach ($field_names AS $value)
        {
            if (array_key_exists($value, $field_values) == true)
            {
                $fields[] = $value;
                $values[] = "'" . $field_values[$value] . "'";
            }
        }

        $sets = array();
        foreach ($update_values AS $key => $value)
        {
            if (array_key_exists($key, $field_values) == true)
            {
                if (is_int($value) || is_float($value))
                {
                    $sets[] = $key . ' = ' . $key . ' + ' . $value;
                }
                else
                {
                    $sets[] = $key . " = '" . $value . "'";
                }
            }
        }

        $sql = '';
        if (empty($primary_keys))
        {
            if (!empty($fields))
            {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        }
        else
        {
            if ($this->version() >= '4.1')
            {
                if (!empty($fields))
                {
                    $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                    if (!empty($sets))
                    {
                        $sql .=  'ON DUPLICATE KEY UPDATE ' . implode(', ', $sets);
                    }
                }
            }
            else
            {
                if (empty($where))
                {
                    $where = array();
                    foreach ($primary_keys AS $value)
                    {
                        if (is_numeric($value))
                        {
                            $where[] = $value . ' = ' . $field_values[$value];
                        }
                        else
                        {
                            $where[] = $value . " = '" . $field_values[$value] . "'";
                        }
                    }
                    $where = implode(' AND ', $where);
                }

                if ($where && (!empty($sets) || !empty($fields)))
                {
                    if (intval($this->fetchOne("SELECT COUNT(*) FROM $table WHERE $where")) > 0)
                    {
                        if (!empty($sets))
                        {
                            $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
                        }
                    }
                    else
                    {
                        if (!empty($fields))
                        {
                            $sql = 'REPLACE INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                        }
                    }
                }
            }
        }

        if ($sql)
        {
            return $this->query($sql);
        }
        else
        {
            return false;
        }
    }
}