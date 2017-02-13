<?php

/**
 * 模板引擎基类
 *
 */

class Core_Template {
    /**
     * 变量存储器
     *
     * @var array
     */
    private $_storeVars;
    private $_isGlobalVars;
    
    private static $_instance = NULL;
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 获取所有变量
     *
     * @return array
     */
    public function getVars(){
        return $this->_storeVars;
    }
    
    /**
     * 清除所有变量
     */
    public function clearVars(){
        $this->_storeVars = NULL;
        $this->_isGlobalVars = NULL;
    }
    
    /**
     * Assign 传参
     *
     * @param string/array $spec
     * @param mixed $value
     * @param bool $isGlobal 是否全局变量
     */
    public function assign($key, $value = NULL, $isGlobal = false){
        // 封装数组传参形式
        if(is_array($key)){
            if(empty($key)){
                return false;
            }
            foreach($key as $k=>$v){
                $this->_storeVars[$k] = $v;
                if($isGlobal){ // 是否全局变量
                    $this->_isGlobalVars[$k] = true;
                }
            }
            return true;
        }
        
        // 常规的变量传参形式
        $this->_storeVars[$key] = $value;
        if($isGlobal){ // 是否全局变量
            $this->_isGlobalVars[$key] = 1;
        }
    }
    
    /**
     * 渲染输出模板
     *
     * @param string $script
     * @param bool $return 是否仅返回（不输出到屏幕）
     * @return void/string
     */
    public function display($script = NULL, $return = false){
        // 从存储器中遍历传参
        if($this->_storeVars && is_array($this->_storeVars)){
            extract($this->_storeVars);
        }
        
        // 传出全局变量
        if($this->_isGlobalVars && is_array($this->_isGlobalVars)){
            foreach($this->_isGlobalVars as $key=>$value){
                $GLOBALS[$key] = $this->_storeVars[$key];
            }
        }
        
        // 清理变量、节约内存
        $this->clearVars();
        
        // 渲染模板页面
        if($return){
            ob_start();
            include template($script);
            $return = ob_get_contents();
            ob_end_clean();
            return $return;
        }else{
            include template($script);
        }
    }
    
    /**
     * 返回输出内容（不输出到屏幕）
     *
     * @param string $script
     * @return string
     */
    public function fetch($script){
        return $this->display($script, true);
    }
}