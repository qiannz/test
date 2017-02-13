<?php

/**
 * Mongodb 基本操作类----临时
 *
 */

define("HOST", '10.66.201.36');
define('PORT', '27017');

/**
 * 使用
 * 注意时间，数量等内容，如果要进行大于小于查询的请强转成INT
 *
 *$db = new Core_Mongo('sns_credit_log');
   return $db->insert('sns_credit_log', $setArr);

   $db = new Core_Mongo('sns_credit_log');
    return $db->find('xl_credit_log', $where, $pageSize, array('_id' => -1), $skip);

    反回数量
    $db = new Core_Mongo('sns_credit_log');
    return $db->find('xl_credit_log', $where, '', array(), '', true);

    $db = new Core_Mongo('xl_user_create');
    return $db->update('xl_user_create', array('uid' => $userId), array('uid' => $userId, "credits" => $num), true);
 *
 */

class Core_Mongo {
    /**
     * Mongo对像数组
     *
     * @var array
     */
    private static $_MongoObjs;
    
    /**
     * Mongo 配置信息
     *
     * @var array
     */
    private $_MongoConf;
    
    /**
     * 服务器名字
     *
     * @var string
     */
    public $_host;
    
    /**
     * 服务器商品
     *
     * @var int
     */
    public $_port;
    
    /**
     * 操作DB
     *
     * @var string
     */
    public $dbName;
    
    public $_mongoObj;
    
    public function __construct($dbName){
        $this->dbName = $dbName;
        $this->_host = HOST;
        $this->_port = PORT;
    }
    
    /**
     * 连接mongo
     *
     * @param unknown_type $host
     * @param unknown_type $port
     * @return unknown
     */
    public function _connect($host, $port, $connect = true, $username = '', $password = '', $timeout = '')//$replicaSet
{
        $options = array();
        if(false == $connect){
            $options['connect'] = $connect;
        }
        if($username && $password){
            $options['username'] = $username;
            $options['password'] = $password;
        }
        if(!empty($timeout)){
            $options['timeout'] = $timeout;
        }
        //return new Mongo('mongodb://' . $host . ':' . $port, $options);
        return new Mongo($GLOBALS['GLOBAL_CONF']['MongoDB'], $options);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getDb(){
        /*if($forceMaster) {
            $this->_db = $this->_connect($this->_host, $this->_port);
        }*/
        
        if(!is_object($this->_mongoObj)){
            $this->_mongoObj = $this->_connect($this->_host, $this->_port);
        }
        $dbName = $this->dbName;
        return $this->_mongoObj->$dbName;
    }
    
    /**
     * 查找/查找反回count
     *
     * @param unknown_type $collection
     * @param unknown_type $where
     * @param unknown_type $limit
     * @param unknown_type $sort
     * @param unknown_type $skip
     * @param unknown_type $count
     * @return unknown
     */
    public function find($collection, $where, $limit = '', $sort = array(), $skip = '', $count = ''){
        $db = $this->getDb();
        $collection = $db->$collection;
        if(empty($where)){
            $where = '';
        }
        $cursor = $collection->find($where);
        if(strlen(trim($limit)) > 0){
            $cursor = $cursor->limit(intval($limit));
        }
        if(strlen(trim($skip)) > 0){
            $cursor = $cursor->skip(intval($skip));
        }
        if(!empty($sort)){
            $cursor = $cursor->sort($sort);
        }
        if(!empty($count)){
            return $cursor->count();
        }
        $array = iterator_to_array($cursor);
        return $array;
    }
    
    /**
     * 插入数据
     *
     * @param string $collection
     * @param array $setArr
     * @param bool $safe
     * @param bool $fsync
     * @param int $timeOut
     * @return string mongo默认自增ID
     */
    public function insert($collection, $setArr, $safe = false, $fsync = false, $timeOut = ''){
        $db = $this->getDb();
        $collection = $db->$collection;
        $options = array();
        if(true == $safe){
            $options['safe'] = true;
        }
        if(true == $fsync){
            $options['safe'] = true;
        }
        if($timeOut > intval($timeOut)){
            $options['timeout'] = intval($timeOut);
        }
        $tmp = $collection->insert($setArr, $options);
        if(true == $tmp){
            $id = '$id';
            return $setArr['_id']->$id;
        }
        return false;
    }
    
    /**
     * 更新
     *
     * @param unknown_type $collection
     * @param unknown_type $where
     * @param unknown_type $setArr
     * @param unknown_type $upsert
     * @param unknown_type $multiple
     * @param unknown_type $safe
     * @param unknown_type $fsync
     * @param unknown_type $timeOut
     * @return unknown
     */
    public function update($collection, $where, $setArr, $upsert = false, $multiple = false, $safe = false, $fsync = false, $timeOut = ''){
        $db = $this->getDb();
        $collection = $db->selectCollection($collection);
        $options = array();
        if(true == $safe){
            $options['safe'] = true;
        }
        if(true == $fsync){
            $options['safe'] = true;
        }
        if(true == $upsert){
            $options['upsert'] = true;
        }
        if(true == $multiple){
            $options['multiple'] = true;
        }
        if($timeOut > intval($timeOut)){
            $options['timeout'] = intval($timeOut);
        }
        return $collection->Update($where, $setArr, $options);
    }

}