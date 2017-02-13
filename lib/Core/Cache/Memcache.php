<?php

/**
 * Memcache 封装操作基类
 *
 */

class Core_Cache_Memcache {
    /**
     * 缓存时间
     *
     * @var int
     */
    protected $_lefttime = 1800;
    
    /**
     * 是否允许缓存
     *
     * @var boolean
     */
    protected $_enabled = true;
    
    /**
     * memcache对象
     *
     * @var object
     */
    protected $_cache = NULL;
    
    /**
     * 服务器信息
     *
     * @var array
     */
    protected $_servers;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct(){
        // 是否允许缓存
        $this->_enabled = $GLOBALS['GLOBAL_CONF']['Mem_Cache_Enabled'];
        
        // 服务器信息
        $this->_servers = $GLOBALS['GLOBAL_CONF']['Mem_Cache_Servers'];
        
        // 禁用缓存 - 调试模式
        if(isset($_REQUEST['nocache']) && $_REQUEST['nocache'] == 'yes'){
            $this->_enabled = false;
        }
    }
    
    /**
     * 单例模式
     *
     * @return Memcached Object
     */
    protected static $_instance = NULL;
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 连接memcache服务器
     *
     * @return boolean
     */
    public function connect(){
        if(!empty($this->_cache) && is_object($this->_cache)){
            return true;
        }
        $this->_cache = new Memcache(); // PECL 扩展
        foreach($this->_servers as $mhost){
            $mArr = explode(':', $mhost);
            $host = isset($mArr[0]) ? $mArr[0] : '';
            $port = isset($mArr[1]) ? $mArr[1] : '11211';
            $weight = isset($mArr[2]) ? $mArr[2] : '';
            if(!$host){
                continue;
            }
            if($weight){
                $this->_cache->addServer($host, $port, false, $weight);
            }else{
                $this->_cache->addServer($host, $port, false);
            }
        }
        $this->_cache->setCompressThreshold(10000, 0.2); // 开启大值自动压缩: 0.2表示20%压缩率
        return true;
    }
    
    /**
     * 读取
     *
     * @param string $key
     * @return object
     */
    public function get($key){
        if(!$this->_enabled){
            return false;
        }
        if($this->connect()){
            $key = $this->generateKey($key);
            return $this->_cache->get($key);
        }
        return false;
    }
    
    /**
     * 写入
     *
     * @param string $key
     * @param string $value
     * @param int $exp 默认永不过期
     * @return boolean
     */
    public function set($key, $value, $exp = 0){
        if($this->connect()){
            $key = $this->generateKey($key);
            $exp = $exp > 0 ? $exp : $this->_lefttime;
            return $this->_cache->set($key, $value, MEMCACHE_COMPRESSED, $exp);
        }
        return false;
    }
    
    /**
     * 根据Tag进行缓存
     *
     * @param string $key
     * @param string $value
     * @param string $tag
     * @param int $exp
     * @return boolean
     */
    public function setByTag($key, $value, $tag, $exp = 0){
        if($this->connect()){
            $key = $this->generateKey($key);
            $exp = $exp !== NULL ? $exp : $this->_lefttime;
            $this->_cache->set($key, $value, MEMCACHE_COMPRESSED, $exp);
            
            $tag = $this->generateKey($tag);
            $keys = $this->_cache->get($tag);
            if(!empty($keys) && is_array($keys)){
                $keys[] = $key;
                $keys = array_unique($keys);
            }else{
                $keys = array($key);
            }
            return $this->_cache->set($tag, $keys, MEMCACHE_COMPRESSED, $exp);
        }
        return false;
    }
    
    /**
     * 删除某个缓存
     *
     * @param string $key
     * @param int $time
     * @return boolean
     */
    public function delete($key, $time = 0){
        if($this->connect()){
            $key = $this->generateKey($key);
            return $this->_cache->delete($key, $time);
        }
        return false;
    }
    
    /**
     * 删除整个Tag标记的缓存
     *
     * @param string $tag
     * @return boolean
     */
    public function deleteByTag($tag){
        if($this->connect()){
            $tag = $this->generateKey($tag);
            $keys = $this->_cache->get($tag);
            if(!empty($keys) && is_array($keys)){
                foreach($keys as $key){
                    $this->_cache->delete($key);
                }
            }
            return $this->_cache->delete($tag);
        }
        return false;
    }
    
    /**
     * 清空缓存
     *
     * @return boolean
     */
    public function flush(){
        if($this->connect()){
            return $this->_cache->flush();
        }
        return false;
    }
    
    /**
     * 获取服务器统计信息
     *
     * @return array
     */
    public function getStats(){
        if($this->connect()){
            return $this->_cache->getStats();
        }
        return false;
    }
    
    /**
     * 获取缓存服务器池中所有服务器统计信息
     *
     * @return array
     */
    public function getExtendedStats(){
        if($this->connect()){
            return $this->_cache->getExtendedStats();
        }
        return false;
    }
    
    /**
     * 用于获取一个服务器的在线/离线状态
     *
     * @return array
     */
    public function getServerStatus($host, $port = ''){
        if($this->connect()){
            return $this->_cache->getServerStatus($host, $port);
        }
        return false;
    }
    
    /**
     * 关闭连接
     */
    public function close(){
        if(!empty($this->_cache) && is_object($this->_cache)){
            $this->_cache->close();
            $this->_cache = NULL;
        }
    }
    
    /**
     * 析构函数
     */
    public function __destruct(){
        $this->close();
    }
    
    /**
     * 序列化KEY
     *
     * @param string $key
     * @return string
     */
    public function generateKey($key){
        return 'LOCAL:' . md5($key);
    }
    
    /**
     * 自增（注意：只能操作无符号数，不能为负数）
     *
     * @param string $key
     * @param int $step 步长
     * @param int $initExpiredTime 初次设置的过期时间
     * @return int|false
     */
    public function increment($key, $step = 1, $initExpiredTime = 0){
        if($this->connect()){
            $rs = $this->_cache->increment($this->generateKey($key), $step);
            if($rs === false){
                if($this->set($key, $step, $initExpiredTime)){
                    return $step;
                }
                return false;
            }
            return $rs;
        }
        return false;
    }
    
    /**
     * 自减（注意：只能操作无符号数，不能为负数）
     *
     * @param string $key
     * @param int $step
     * @return decr
     */
    public function decrement($key, $step = 1){
        if($this->connect()){
            $key = $this->generateKey($key);
            return $this->_cache->decrement($key, $step);
        }
        return false;
    }
    
    /**
     * 批量获取
     *
     * @param array $key
     * @return array
     */
    public function getMulti($arrKey = array()){
        $arrResult = array();
        if($this->connect()){
            foreach($arrKey as $key=>$var){
                $arrResult[$key] = $this->get($var);
            }
            return $arrResult;
        }
        return false;
    }
}