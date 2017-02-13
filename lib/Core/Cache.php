<?php

/**
 * 缓存类适配器（工厂）
 *
 */

class Core_Cache {
    protected static $_cacheObjs = array();
    
    public static function factory($class){
        $class = ucfirst($class);
        
        // 兼容 windows 环境下没有 pecl-memcached 客户端扩展的情况
        if(substr($class, 0, 8) == 'Memcache' && !extension_loaded('memcached')){
            $class = 'Memcache';
        }
        if(isset(self::$_cacheObjs[$class]) && is_object(self::$_cacheObjs[$class])){
            return self::$_cacheObjs[$class];
        }
        
        $fileName = ROOT_PATH . 'lib/Core/Cache/' . $class . '.php';
        
        if(!is_file($fileName) || !file_exists($fileName)){
            trigger_error('<br />Unable to load cache class: ' . $class, E_USER_WARNING);
            exit();
        }
        
        include_once $fileName;
        $className = 'Core_Cache_' . $class;
        self::$_cacheObjs[$class] = new $className();
        return self::$_cacheObjs[$class];
    }

}