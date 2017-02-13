<?php
require_once ROOT_PATH . 'lib/Third/Smarty/Smarty.class.php';
class Third_Template
{
    private static $_instance = NULL;
    protected static $_tplObjs;
     
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();
        }
        return self::$_instance;        
    }
    
    function get()
    {
        self::$_tplObjs = new Smarty;
        self::$_tplObjs->left_delimiter = '{{';
        self::$_tplObjs->right_delimiter = '}}';
        self::$_tplObjs ->template_dir   = ROOT_PATH . 'tpls';
        self::$_tplObjs ->cache_dir      = ROOT_PATH . 'var'.DIRECTORY_SEPARATOR.'caches';
        self::$_tplObjs ->compile_dir    = ROOT_PATH . 'var'.DIRECTORY_SEPARATOR.'compiled';
        if(!is_dir(self::$_tplObjs->compile_dir)) mkdir(self::$_tplObjs ->compile_dir, 0777, true);
        self::$_tplObjs ->force_compile = true;
        //self::$_tplObjs->compile_check = true;
        self::$_tplObjs ->debugging = false;
        self::$_tplObjs ->caching = false;
        //$smarty->cache_lifetime = 120;
        return self::$_tplObjs;
    }
}