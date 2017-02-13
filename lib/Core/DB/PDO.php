<?php

/**
 * PDO 数据库操作基类
 *
 */

class Core_DB_PDO {
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
    protected $_db = NULL;
    
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
     * 构造函数，初始化配置
     *
     * @param array $writeConf
     * @param array $readConf
     * @param bool $forceMaster
     * @param bool $persistent
     */
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
     * @return PDO Object
     */
    protected function _getDbWriteConn($forceConnect = false){
        // 强制重连，首先需断开已连的连接
        if($forceConnect){
            $this->_disconnectWrite();
        }
        
        // 判断是否已经连接
        if($this->_writeDbConn && is_object($this->_writeDbConn)){
            return $this->_writeDbConn;
        }
        
        $db = $this->_connect($this->writeConf);
        if(!$db || !is_object($db)){
            return false;
        }
        
        $this->_writeDbConn = $db;
        return $this->_writeDbConn;
    }
    
    /**
     * 获取从库的“读”数据连接
     *
     * @param bool $forceConnect 是否强制重连
     * @return PDO Object
     */
    protected function _getDbReadConn($forceConnect = false){
        // 强制重连，首先需断开已连的连接
        if($forceConnect){
            $this->_disconnectRead();
        }
        
        // 判断是否已经连接
        if($this->_readDbConn && is_object($this->_readDbConn)){
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
            $db = $this->_connect($this->readConf);
            if($db && is_object($db)){
                $this->_readDbConn = $db;
                return $this->_readDbConn;
            }
        }
    }
    
    /**
     * 从 host 中识别端口号
     *
     * @param string $host
     * @return string
     */
    protected function _setHostPort($host){
        if(strpos($host, ':') !== false){
            list($host, $port) = explode(':', $host);
            return $host . ';port=' . $port;
        }
        return $host;
    }
    
    /**
     * 连接数据库
     *
     * @param array $conf
     * @param bool $isPconnect
     * @return PDO Object
     */
    protected function _connect($conf = array(), $isPconnect = false){
        try{
            $dsn = 'mysql:dbname=' . $conf['path'] . ';host=' . $this->_setHostPort($conf['host']);
            if(PHP_VERSION < '5.3'){
                $params = array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8');
                if($this->persistent || $isPconnect){
                    $params[PDO::ATTR_PERSISTENT] = true;
                }
                $db = new PDO($dsn, $conf['user'], $conf['pass'], $params);
            }else{
                $params = array();
                if($this->persistent || $isPconnect){
                    $params[PDO::ATTR_PERSISTENT] = true;
                }
                $db = new PDO($dsn, $conf['user'], $conf['pass'], $params);
                $db->exec("SET NAMES UTF8");
            }
            $db->dsn = $conf;
        }catch(PDOException $e){
            Core_Exception::exceptionProcess($e, $dsn . ' Connection Failed', $this->_errorlevel);
            return false;
        }
        return $db;
    }
    
    /**
     * 释放数据库连接
     */
    public function disconnect(){
        $this->_disconnectWrite(); // 释放写连接
        $this->_disconnectRead(); // 释放读连接
        if(isset($this->_dbConn) && is_object($this->_dbConn)){ // 释放临时连接
            $this->_dbConn = NULL;
        }
    }
    
    /**
     * 释放数据库“写”连接
     */
    protected function _disconnectWrite(){
        if(isset($this->_writeDbConn) && is_object($this->_writeDbConn)){
            $this->_writeDbConn = NULL;
        }
    }
    
    /**
     * 释放数据库“读”连接
     */
    protected function _disconnectRead(){
        if(isset($this->_readDbConn) && is_object($this->_readDbConn)){
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
            $this->_db = $this->_getDbWriteConn();
        }else{
            $this->_db = $this->_getDbReadConn();
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
        $host = $this->_db->dsn['host'];
        $dbname = $this->_db->dsn['path'];
        Core_DB::writeSqlLog($host, $dbname, $sql, $params, $time);
    }
    
    /**
     * 执行操作的底层接口
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster 是否强制连接主库
     * @return PDO Statement
     */
    protected function _autoExecute($sql, $params = array(), $forceMaster = false){
        try{
            $this->_lastSql = array('sql'=>$sql, 'params'=>$params);
            $sqlstarttime = microtime(true);
            
            $this->_getChoiceDbConnect($forceMaster);
            if(!$this->_db){
                exit('DB connection lost.');
            }
            
            // 预编译 SQL
            $stmt = $this->_db->prepare($sql);
            if(!$stmt){
                throw new Core_Exception('code:[' . $this->_db->errorCode() . '] ' . implode(',', $this->_db->errorInfo()));
                return false;
            }
            
            // 二级参数兼容
            if(!$params){
                $params = array();
            }elseif(!is_array($params)){
                $params = array($params);
            }
            
            // 执行 SQL
            if(!$stmt->execute($params)){
                $stmt = $this->_errorRetrieve($stmt, $sql, $params, $forceMaster); // 错误处理、补救机制
                if($stmt->errorCode() > 0){
                    throw new Core_Exception('code:[' . $stmt->errorCode() . '] ' . implode(',', $stmt->errorInfo()));
                    return false;
                }
            }
            
            $sqlendtime = microtime(true);
            $this->_writeLog($sql, $params, $sqlendtime - $sqlstarttime);
            
            return $stmt;
        
        }catch(Exception $e){
            Core_Exception::exceptionProcess($e, $this->getLogSql($sql, $params), $this->_errorlevel);
            exit();
        }
    }
    
    /**
     * 错误补救机制
     *
     * @param PDO Statement $stmt
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster
     * @return bool/query
     */
    protected function _errorRetrieve($stmt, $sql, $params = array(), $forceMaster = false){
        $errorInfo = $stmt->errorInfo();
        if($errorInfo[0] == 'HY000' && $errorInfo[1] == 2006){ // MySQL server has gone away
            

            $this->reActivate(); // 重新激活和获取连接，重新给 $this->_writeDbConn 和 $this->_readDbConn 赋值
            $this->_getChoiceDbConnect($forceMaster); // 重新给 $this->_db 赋值
            

            // 这里 prepare + execute 不能简写为 query($sql)，因为 query() 的返回值 stmt 没有 errorCode 属性
            $stmt = $this->_db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }
        return $stmt;
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
            $stmt = $this->_autoExecute($sql, $params, $forceMaster);
            if($stmt){
                $rows = $stmt->rowCount();
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
        $stmt = $this->_autoExecute($sql, $params, $forceMaster);
        if($stmt){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->_autoExecute($sql, $params, $forceMaster);
        if($stmt){
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
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
        $stmt = $this->_autoExecute($sql, $params, $forceMaster);
        if($stmt){
            $data = array();
            while($row = $stmt->fetch(PDO::FETCH_NUM)){
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
        $stmt = $this->_autoExecute($sql, $params, $forceMaster);
        if($stmt){
            $data = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
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
        $stmt = $this->_autoExecute($sql, $params, $forceMaster);
        if($stmt){
            return $stmt->fetchColumn();
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
        $stmt = $this->_autoExecute($sql, $params, $forceMaster);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return array();
    }
    
    /**
     * 删除
     *
     * @param string $table
     * @param array/string $whereArr
     * @param int $limit
     * @param array $params
     * @return int
     */
    public function delete($table, $whereArr, $limit = 1, $params = array()){
        $whereSql = Core_DB::getWhereSQL($whereArr);
        $sql = 'DELETE FROM `' . $table . '`' . ' WHERE ' . $whereSql . ($limit > 0 ? (' LIMIT ' . $limit) : '');
        return $this->query($sql, $params);
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
            //          $insertvaluesql .= $comma . '\'' . $value . '\'';
            $insertvaluesql .= $comma . '?';
            $comma = ', ';
        }
        
        $method = $replace ? 'REPLACE' : 'INSERT';
        $sql = $method . ' INTO `' . $table . '`' . '(' . $insertkeysql . ') ' . 'VALUES (' . $insertvaluesql . ')';
        
        $setArr = array_map('stripslashes', $setArr);
        $stmt = $this->_autoExecute($sql, array_values($setArr), true);
        if(!$stmt){
            return 0;
        }
        
        $insertId = $this->lastInsertId();
        return $insertId ? $insertId : true;
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
            //          $setSql .= $comma . '`' . $key . '`' . ' = \'' . $value . '\'';
            $setSql .= $comma . '`' . $key . '`' . ' = ?';
            $comma = ', ';
        }
        
        $whereSql = Core_DB::getWhereSQL($whereArr);
        $sql = 'UPDATE `' . $table . '`' . ' SET ' . $setSql . ' WHERE ' . $whereSql . ($limit > 0 ? (' LIMIT ' . $limit) : '');
        
        $setArr = array_map('stripslashes', $setArr);
        return $this->query($sql, array_values($setArr));
    }
    
    /**
     * 批量插入记录
     *
     * @param string $table
     * @param array $setArrs
     * @return int
     */
    public function insertBatch($table, $setArrs){
        $params = array();
        $insertkeysqlGot = false;
        if(!$setArrs || !is_array($setArrs)){
            return false;
        }
        
        $insertkeysql = $insertvaluesql = $comma = '';
        foreach($setArrs as $setArr){
            
            $insertvaluesqlNode = $commaNode = '';
            $setArr = array_map('stripslashes', $setArr);
            
            foreach($setArr as $key=>$value){
                if(!$insertkeysqlGot){
                    $insertkeysql .= $commaNode . '`' . $key . '`';
                }
                
                //              $insertvaluesqlNode .= $commaNode . '\'' . $value . '\'';
                $insertvaluesqlNode .= $commaNode . '?';
                $params[] = $value;
                
                $commaNode = ', ';
            }
            $insertvaluesql .= $comma . '(' . $insertvaluesqlNode . ')';
            $insertkeysqlGot = true;
            $comma = ', ';
        }
        
        $sql = 'INSERT INTO `' . $table . '` (' . $insertkeysql . ') VALUES ' . $insertvaluesql;
        return $this->query($sql, $params);
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
        return $this->_db->lastInsertId();
    }
    
    /**
     * 事务开始
     */
    public function beginTransaction(){
        $this->_getChoiceDbConnect(true);
        $this->_db->beginTransaction();
    }
    
    /**
     * 事务提交
     */
    public function commit(){
        $this->_getChoiceDbConnect(true);
        $this->_db->commit();
    }
    
    /**
     * 事务回滚
     */
    public function rollBack(){
        $this->_getChoiceDbConnect(true);
        $this->_db->rollBack();
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
        $host = $this->_db->dsn['host'];
        $dbname = $this->_db->dsn['path'];
        
        $return = '';
        $return .= $sql . (empty($params) ? '' : ' [' . implode(',', $params) . ']');
        if($params){
            $return .= "\nrealSql:" . Core_DB::replaceHolder($sql, $params);
        }
        return $return . "\nhost: " . $host . ', dbname: ' . $dbname;
    }
    
    /**
     * 执行 SQL 并返回 PDO Statement
     *
     * @param string $sql
     * @param array $params
     * @param bool $forceMaster
     * @return PDO Statement
     */
    public function execute($sql, $params = array(), $forceMaster = true){
        try{
            $stmt = $this->_autoExecute($sql, $params, $forceMaster);
            return $stmt ? $stmt : false;
        }catch(Exception $e){
            Core_Exception::exceptionProcess($e, $this->getLogSql($sql, $params), $this->_errorlevel);
            return false;
        }
    }
    
    /**
     * PDO fetch method
     *
     * @param PDO Statement $stmt
     * @return arary
     */
    public function fetchArray($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 字段自增
     *
     * @param string $table
     * @param string $field
     * @param int $value
     * @param array/string $whereArr
     * @return int
     */
    public function incr($table, $field, $value, $whereArr, $limit = 1){
        $whereSql = Core_DB::getWhereSQL($whereArr);
        $limit = $limit > 0 ? 'LIMIT ' . $limit : '';
        $value = $value > 0 ? ' + ' . $value : $value;
        return $this->query("UPDATE `{$table}` SET `{$field}` = `{$field}` {$value} WHERE {$whereSql} {$limit}");
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
        if($this->_writeDbConn && is_object($this->_writeDbConn)){
            $result['write'] = 0;
            if($this->_ping($this->_writeDbConn) == -1){
                $this->_getDbWriteConn(true);
                $result['write'] = 1;
            }
        }
        
        // 重连“读”连接
        if($this->_readDbConn && is_object($this->_readDbConn)){
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
        if(!$conn || !is_object($conn)){
            return -99;
        }
        $stmt = $conn->prepare('SELECT 1');
        if(!$stmt){
            $errInfo = $conn->errorInfo();
        }else{
            $stmt->execute();
            $errInfo = $stmt->errorInfo();
        }
        if($errInfo[0] == 'HY000'){
            if($errInfo[1] == 2006){ // MySQL server has gone away
                return -1;
            }
            return -9;
        }
        return 1;
    }
}