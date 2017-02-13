<?php

/**
 * memcached客户端基本封装
 *
 */
class Core_Cache_Memcached {
    
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
    protected $_servers = array();
    
    /**
     * 是否长连接
     *
     * @var boolean
     */
    protected $_persistent = false;
    
    /**
     * 构造函数
     *
     * @param boolean $persistent 暂未实现
     * @return void
     */
    public function __construct($persistent = false){
        // 是否长连接
        $this->_persistent = $persistent;
        //
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
        
        // 根据是否持久连接决定创建持久化对象或普通对象
        // TODO 持久化ID唯一，并传入mc的构造函数
        $this->_cache = $this->_persistent ? new Memcached() : new Memcached();
        
        $servers = array();
        foreach($this->_servers as $mhost){
            $mArr = explode(':', $mhost);
            $host = isset($mArr[0]) ? $mArr[0] : '';
            $port = isset($mArr[1]) ? $mArr[1] : '11211';
            $weight = isset($mArr[2]) ? $mArr[2] : '';
            if(!$host){
                continue;
            }
            if($weight){
                $servers[] = array($host, $port, $weight);
            }else{
                $servers[] = array($host, $port);
            }
        }
        
        // 开启大值自动压缩
        $this->_cache->setOption(Memcached::OPT_COMPRESSION, true);
        
        ////开启一致性哈希
        $this->_cache->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        
        //开启ketama算法兼容，注意，打开本算法时，sub_hash会使用KETAMA默认的MD5
        $this->_cache->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        
        //设置哈希算法，当不使用KETAMA算法时，这个设置才生效，有几种可选择，不过如果启用ketama，这个选项是没用的
        //$mc->setOption(Memcached::OPT_HASH , Memcached::HASH_MD5 );
        // 开启已连接socket的无延迟特性（在某些环境可能会带来速度上的提升）。
        $this->_cache->setOption(Memcached::OPT_TCP_NODELAY, true);
        
        // 开启异步I/O。这将使得存储函数传输速度最大化。
        $this->_cache->setOption(Memcached::OPT_NO_BLOCK, true);
        
        if(!count($this->_cache->getServerList())){
            return $this->_cache->addServers($servers);
        }
        
        return true;
    }
    
    /**
     * 序列化KEY
     *
     * @param string $key
     * @return string
     */
    public function generateKey($key){
        return md5($key);
    }
    
    /**
     * 检索单个元素
     *
     * @param string $key
     * @param callback $cache_cb
     * @param boolean $in_cas
     * @return mixed
     */
    public function get($key, $cache_cb = NULL, $in_cas = false){
        if(!$this->_enabled){
            return false;
        }
        $key = $this->generateKey($key);
        
        if($this->connect()){
            $rs = $in_cas ? $this->_cache->get($key, $cache_cb, $cas_token) : $this->_cache->get($key, $cache_cb);
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                // NOTE 如果 $this->_cache->set('key', false) 不好判断
                //                if($rs === false) {
                //                    $rs = 'false';
                //                }
                return $in_cas ? array($rs, $cas_token) : $rs;
            }
        }
        return false;
    }
    
    /**
     * 存储单个元素
     *
     * @param string $key
     * @param string $value
     * @param int $exp 默认永不过期
     * @return boolean
     */
    public function set($key, $value, $exp = 0){
        if($this->connect()){
            $key = $this->generateKey($key);
            $exp = $exp !== NULL ? $exp : $this->_lefttime;
            $rs = $this->_cache->set($key, $value, $exp);
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                return $rs;
            }
        }
        return false;
    }
    
    /**
     * 删除某个缓存
     *
     * @param string $key
     * @param int $time 默认立即删除
     * @return boolean
     */
    public function delete($key, $time = 0){
        if($this->connect()){
            $key = $this->generateKey($key);
            $rs = $this->_cache->delete($key, $time);
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                return $rs;
            }elseif($this->_cache->getResultCode() == Memcached::RES_NOTFOUND){
                // NOTE 找不到要删除的值，如果业务模块不需要判断这个，可直接和上面返回一样的结果
                return -1;
            }
        }
        return false;
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
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                return $rs;
            }
        }
        return false;
    }
    
    /**
     * 自减（注意：只能操作无符号数，不能为负数）
     *
     * @param string $key
     * @param int $offset
     * @return int|false
     */
    public function decrement($key, $offset = 1){
        if($this->connect()){
            $key = $this->generateKey($key);
            $rs = $this->_cache->decrement($key, $offset);
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                return $rs;
            }
        }
        return false;
    }
    
    /**
     * 检索多个元素
     *
     * @param array $keys
     * @param boolean $in_cas
     * @param int $flags
     * @return array
     */
    public function getMulti($keys, $in_cas = false, $flags = 0){
        if(!$this->_enabled){
            return false;
        }
        
        if($this->connect()){
            foreach((array) $keys as $i=>$key){
                // keys数组中的值必须都为字符串型
                $key = $this->generateKey($key);
                $keys[$i] = (string) $key;
            }
            
            if($flags){
                if($in_cas){
                    $rs = $this->_cache->getMulti($keys, $cas_tokens, Memcached::GET_PRESERVE_ORDER);
                }else{
                    $null = null;
                    $rs = $this->_cache->getMulti($keys, $null, Memcached::GET_PRESERVE_ORDER);
                }
            
     //$rs = $in_cas ? $this->_cache->getMulti($keys, $cas_tokens, Memcached::GET_PRESERVE_ORDER) : $this->_cache->getMulti($keys, null, Memcached::GET_PRESERVE_ORDER);
            }else{
                $rs = $in_cas ? $this->_cache->getMulti($keys, $cas_tokens) : $this->_cache->getMulti($keys);
            }
            
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                return $in_cas ? array($rs, $cas_tokens) : $rs;
            }
        }
        return array();
    }
    
    /**
     * 存储多个元素
     *
     * @param array $items
     * @param int $exp 默认永不过期
     * @return boolean
     */
    public function setMulti($items, $exp = 0){
        if($this->connect()){
            $exp = $exp !== NULL ? $exp : $this->_lefttime;
            $this->_cache->setMulti($items, $exp);
            if($this->_cache->getResultCode() == Memcached::RES_SUCCESS){
                return true;
            }
        }
        return false;
    }
    
    /**
     * 获取服务器池的统计信息
     *
     * @return array|false
     */
    public function getStats(){
        if($this->connect()){
            // 据手册所说，经实验，服务器池中有不可用服务器时，返回false
            return $this->_cache->getStats();
        }
        return false;
    }
    
    /**
     * 关闭连接
     */
    public function close(){
        // 兼容Memcache
    }
    
    /**
     * 暂时仅封装常用方法，如使用其它方法将通过魔术方法调用
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments){
        if($this->connect()){
            return call_user_func_array(array($this->_cache, $name), $arguments);
        }
        return false;
    }

}
