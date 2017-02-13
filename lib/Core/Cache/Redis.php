<?php

class Core_Cache_Redis {
    private static $_redisObjs;
    private $_redisConf;
    private $_dbName;
    
    const DB_DEFAULT = 0;
    const DB_FEED = 0;
    const DB_APP = 2;
    const DB_MQ = 3;
    const DB_USER = 4;
    const DB_TEST = 5;
    const DB_NEWEST_MSG = 6;
    const DB_BIZ = 7;
    const DB_CRAD_REG = 8; // Q卡激活
    const DB_CRAD_ADDRESS = 10; // Q卡实物下发地址
    const DB_GROUP = 11; // 圈子
    const DB_CONTACT = 12;
    const DB_VCODE = 13; // 虚拟码
    

    /**
     * 加载 redis 配置
     *
     * @param int $dbName
     * @return void
     */
    public function __construct($dbName = 0){
        $this->_dbName = $dbName > 0 ? $dbName : self::DB_DEFAULT;
        
        switch($this->_dbName){
            case self::DB_FEED:
                $this->_redisConf = $GLOBALS['GLOBAL_CONF']['Redis']['Feed'];
                break;
            case self::DB_MQ:
                $this->_redisConf = $GLOBALS['GLOBAL_CONF']['Redis']['MQ'];
                break;
            default:
                $this->_redisConf = $GLOBALS['GLOBAL_CONF']['Redis']['Others'];
        }
        
        if(!$this->_redisConf){
            exit('Invalid Redis Configuration');
        }
    }
    
    public static $_instances = array();
    public static function getInstance($dbName = 0){
        if(!isset(self::$_instances[$dbName]) || self::$_instances[$dbName] === NULL){
            self::$_instances[$dbName] = new self($dbName);
        }
        return self::$_instances[$dbName];
    }
    
    /**
     * 关闭 redis 连接
     *
     * @return void
     */
    public function __destruct(){
        if(isset(self::$_redisObjs[$this->_dbName]) && is_object(self::$_redisObjs[$this->_dbName])){
            
            $redisObj = self::$_redisObjs[$this->_dbName];
            if(method_exists($redisObj, 'close')){
                $redisObj->close();
            }elseif(method_exists($redisObj, 'quit')){
                $redisObj->quit();
            }
            
            unset(self::$_redisObjs[$this->_dbName]);
        }
    }
    
    /**
     * 建立 redis 连接
     *
     * @return redis conn object
     */
    private function _connect(){
        $redisObj = isset(self::$_redisObjs[$this->_dbName]) ? self::$_redisObjs[$this->_dbName] : NULL;
        if($redisObj === NULL || !is_object($redisObj)){
            
            if(count($this->_redisConf) == 1){
                $redisClass = class_exists('Redis', false) ? 'Redis' : 'Third_Redis';
                $redisObj = new $redisClass();
                $redisObj->connect($this->_redisConf[0]['host'], $this->_redisConf[0]['port']);
            }else{
                $redisObj = new Third_RedisCluster($this->_redisConf);
            }
            
            if($this->_dbName > 0){
                $redisObj->select($this->_dbName);
            }
            
            self::$_redisObjs[$this->_dbName] = $redisObj;
        }
        return self::$_redisObjs[$this->_dbName];
    }
    
    /**
     * 调用魔术方法
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     */
    public function __call($method, $args){
        $redisObj = $this->_connect();
        if(!$redisObj){
            return false;
        }
        return call_user_func_array(array($redisObj, $method), $args);
    }
}