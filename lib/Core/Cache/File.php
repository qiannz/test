<?php

/**
 * File 缓存封装类
 *
 */

class Core_Cache_File {
	/**
	 * 是否允许缓存
	 *
	 * @var boolean
	 */
	protected $_enabled = true;	
    /**
     * 缓存目录
     *
     * @var string
     * @access private
     */
    private $dir;
    
    /**
     * 构造器
     *
     * @access public
     */
    
    private static $_instance = NULL;
    
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();
        }
        return self::$_instance;        
    }
    
    public function __construct(){
    	// 是否允许缓存
    	$this->_enabled = $GLOBALS['GLOBAL_CONF']['File_Cache_Enabled']; 
    	$this->dir = ROOT_PATH . 'var/file_cache/';
    	if(!is_dir($this->dir)){
    		mkdir($this->dir, true);
    		chmod($this->dir, 0777);
    	}
    	if(!is_writable($this->dir)){
    		throw new Core_Exception('缓存文件夹' . $this->dir . '不可写');
    	}   	
    }
    
    /**
     * 设置一个缓存变量
     *
     * @param String $key    缓存Key
     * @param mixed $value   缓存内容
     * @param int $expire    缓存时间(秒)
     * @return boolean       是否缓存成功
     * @access public
     * @abstract
     */
    public function set($key, $value, $expire = 60){
    	$dir = $this->dir . substr(md5($key), 0,1) . DS;
    	
    	if(!is_dir($dir)){
    		mkdir($dir, true);
    		chmod($dir, 0777);
    	}
    	if(!is_writable($dir)){
    		throw new Core_Exception('缓存文件夹' . $dir . '不可写');
    	}
    	   	
        $file = $dir . md5($key) . '.cache';
        
        if(file_put_contents($file, serialize($value), LOCK_EX)){
            @touch($file, time() + $expire);
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 获取一个已经缓存的变量
     *
     * @param String $key  缓存Key
     * @return mixed       缓存内容
     * @access public
     */
    public function get($key){    	
    	if(!$this->_enabled){
    		return false;
    	}
    	$dir = $this->dir . substr(md5($key), 0,1) . DS;
        $file = $dir . md5($key) . '.cache';       
        if(is_file($file)){
            if(time() <= filemtime($file)){
                return unserialize(file_get_contents($file));
            }else{
                @unlink($file);
                //删除缓存
                return false;
            }
        }else{
            //没有找到缓存
            return false;
        }
    }
    
    /**
     * 删除一个已经缓存的变量
     *
     * @param  $key
     * @return boolean       是否删除成功
     * @access public
     */
    public function del($key){
        $file = $this->dir . substr(md5($key), 0,1). DS . md5($key) . '.cache';
        return @unlink($file);
    }
    
    /**
     * 删除全部缓存变量
     *
     * @return boolean       是否删除成功
     * @access public
     */
    public function delAll(){
    	if(deleteAll($this->dir, true)) {
        	return true;
    	}
    }
    
    /**
     * 检测是否存在对应的缓存
     *
     * @param string $key   缓存Key
     * @return boolean      是否存在key
     * @access public
     */
    public function has($key){
        return (is_file($this->dir . substr(md5($key), 0,1). DS . md5($key) . '.cache') === NULL ? false : true);
    }
}