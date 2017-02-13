<?php

/**
 * MemcacheDB 封装操作基类
 *
 */

class Core_Cache_Memcachedb extends Core_Cache_Memcached {
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct(){
        // 服务器信息
        $this->_servers = $GLOBALS['GLOBAL_CONF']['Mem_Cache_DB_Servers'];
    }
    
    /**
     * 写入
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value){
        return parent::set($key, value, 0);
    }
    
    /**
     * 根据Tag进行缓存
     *
     * @param string $key
     * @param string $value
     * @param string $tag
     * @return boolean
     */
    public function setTag($key, $value, $tag){
        return parent::set($key, $value, $tag, 0);
    }
    
    /**
     * 更新键值对
     *
     * @param string $key
     * @param array $arrayData
     * @return boolean
     */
    public function merge($key, $arrayData){
        if($this->connect()){
            $result = $this->get($key);
            if(!is_array($result)){
                return false;
            }
            if(!is_array($arrayData)){
                $arrayData = array($arrayData);
            }
            return (bool) $this->set($key, array_merge($result, $arrayData));
        }
        return false;
    }
}