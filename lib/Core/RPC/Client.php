<?php

/**
 * PHPRPC_Client 实例类
 *
 */

require_once ROOT_PATH . 'lib/Third/phprpc/phprpc_client.php';

class Core_RPC_Client {
    // 单例
    private static $_client;
    
    // 已载入的模型
    private static $_loadedModels = array();
    
    /**
     * 获取 RPC_Client 单例
     *
     * @param string $url
     * @return RPC_Client Object
     */
    public static function get($url){
        if(self::$_client === NULL){
            $client = new PHPRPC_Client();
            $client->setProxy(NULL);
            $client->setTimeout(5);
            self::$_client = $client;
        }
        self::$_client->useService($url);
        return self::$_client;
    }
    
    /**
     * RPC 载入模型 Example: Core_RPC_Client::loadModel('Space');
     *
     * @param string $modelName
     * @return RPC_Client
     */
    public static function loadModel($modelName){
        if(isset(self::$_loadedModels[$modelName])){
            $model = self::$_loadedModels[$modelName];
            if($model instanceof PHPRPC_Client){
                return $model;
            }
        }
        
        // 远程 API 地址
        $url = $GLOBALS['RPC_ModelConf'][$modelName] . '/' . 'api.php?p=' . $modelName;
        self::$_loadedModels[$modelName] = clone (self::get($url));
        
        return self::$_loadedModels[$modelName];
    }

}