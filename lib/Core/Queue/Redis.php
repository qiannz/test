<?php

/**
 * 队列基类 (Redis List)
 *
 */

class Core_Queue_Redis {
    private $_redis;
    private $_listName;
    private $_setName;
    private $_setSuffix = ':SET';
    
    public function __construct(){
        $this->_redis = new Core_Cache_Redis(Core_Cache_Redis::DB_MQ);
    }
    
    private static $_instance;
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 加工处理队列名
     *
     * @param string $name
     */
    public function initMQName($name){
        $this->_listName = 'Queue:' . strtoupper($name);
        $this->_setName = 'Queue:' . strtoupper($name) . $this->_setSuffix;
    }
    
    /**
     * 向队列尾部追加一个元素
     *
     * @param string $name
     * @param string $value
     * @param bool $checkRepeat 检测如果重复则不进队列
     *
     * @return bool
     */
    public function push($name, $value, $checkRepeat = false){
        if(empty($name) || empty($value)){
            return false;
        }
        $this->initMQName($name);
        
        // 检测重复
        if($checkRepeat && $this->_redis->sismember($this->_setName, $value)){
            return false;
        }
        
        $this->_redis->rpush($this->_listName, $value);
        
        // 再存一遍集合的目的即为了检测重复
        if($checkRepeat){
            $this->_redis->sadd($this->_setName, $value);
        }
        
        return true;
    }
    
    /**
     * 取出队列头部的第一个元素
     *
     * @param string $name
     * @return bool
     */
    public function pop($name){
        if(empty($name)){
            return false;
        }
        $this->initMQName($name);
        $value = $this->_redis->lpop($this->_listName);
        if(!empty($value)){
            $this->_redis->srem($this->_setName, $value);
        }
        return $value;
    }
    
    /**
     * 返回队列长度
     *
     * @param string $name
     * @return bool
     */
    public function count($name){
        if(empty($name)){
            return 0;
        }
        $this->initMQName($name);
        return $this->_redis->scard($this->_setName);
    }
    
    /**
     * 清空某队列
     *
     * @param string $name
     * @return bool
     */
    public function clear($name){
        if(empty($name)){
            return false;
        }
        $this->initMQName($name);
        $this->_redis->del($this->_listName);
        $this->_redis->del($this->_setName);
        return true;
    }
    
    /**
     * 获取一个队列的所有元素
     *
     * @param string $name
     * @param int $start
     * @param int $end
     * @return array
     */
    public function get($name, $start = 0, $end = -1){
        if(empty($name)){
            return array();
        }
        $this->initMQName($name);
        return $this->_redis->lrange($this->_listName, $start, $end);
    }
    
    /**
     * 获取所有队列名
     *
     * @return array
     */
    public function getAll(){
        return $this->_redis->keys('Queue:*');
    }
    
    /**
     * 清空、删除所有队列
     *
     * @return bool
     */
    public function clearAll(){
        $list = $this->getAll();
        if(!$list){
            return true;
        }
        foreach($list as $qname){
            $this->_redis->del($qname);
        }
        return true;
    }
    
    /**
     * 获取一个自增数（最大 2147483647）
     *
     * @return int
     */
    public function getIncr(){
        return $this->_redis->incr('Queue:Var:Incr');
    }
}

/*
Example :
push    : Core_Queue_Redis::getInstance()->push('队列名', $value);
pop     : Core_Queue_Redis::getInstance()->pop('队列名');
*/