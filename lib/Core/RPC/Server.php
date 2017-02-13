<?php

/**
 * PHPRPC_Server 实例类
 *
 */

class Core_RPC_Server {
    /**
     * 启动 API Server
     *
     * @param string $classPath   类名，例如 Biz_Shop
     * @param string $classPrefix 类名前缀，例如 Api_Mobile_ , Api_Oss_
     * @return void
     */
    public static function dispatch($classPath, $classPrefix = ''){
        if(!$classPath){
            exit('Empty/Invalid ClassPath');
        }
        
        // 重新处理单词首字母大写
        $classPath = $classPrefix . str_replace(' ', '_', ucwords(str_replace('_', ' ', $classPath)));
        
        // 类名放入全局变量用于写RPC接口调用日志 -- lujun 2011.11.21
        $GLOBALS['RPC_ClassPath'] = $classPath;
        
        require_once self::getClassFile($classPath); // 必须先引入，定义类结构，否则会出现 __PHP_Incomplete_Class
        

        // 启用 Session
        // session_start();
        

        // 把实例对象存至 session 中，保证本次会话请求、发布的是同一个对象
        /*
        if (isset($_SESSION[$classPath])) {
            $objApi = $_SESSION[$classPath];
        } else {
            $objApi = new $classPath();
            $_SESSION[$classPath] = $objApi;
        }
        */
        
        $objApi = new $classPath();
        
        // 获取该实例中的所有定义、开放的方法
        $methods = get_class_methods($objApi);
        $methods = array_merge($methods, $objApi->getQuickOpenMethods());
        
        // 删除构造函数、析构函数等无用函数
        foreach($methods as $key=>$method){
            if(in_array($method, array('__construct', '__destruct', 'getInstance'))){
                unset($methods[$key]);
            }
        }
        
        // 启动 RPC Server
        require_once ROOT_PATH . 'lib/Third/phprpc/phprpc_server.php';
        $server = new PHPRPC_Server();
        
        // 参数选项，可缺省
        $server->setCharset('UTF-8');
        $server->setDebugMode(isDebug()); #debug
        $server->setEnableGZIP(false);
        
        $server->add($methods, $objApi);
        $server->start();
    }
    
    /**
     * 转换类名为具体文件路径，以便 include/require
     *
     * @param string $classPath
     * @return string
     */
    public static function getClassFile($classPath){
        if(strpos($classPath, '_') !== false){
            $classPath = str_replace('_', DIRECTORY_SEPARATOR, $classPath);
        }
        $fileName = ROOT_PATH . 'app/' . $classPath . '.php';
        if(!file_exists($fileName) || !is_file($fileName)){
            exit('Access Denied - Cannot find Api_Class');
        }
        return $fileName;
    }
}