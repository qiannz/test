<?php

/**
 * 路由分发基类
 *
 */

class Core_Router {
    /**
     * 是否开启路由重写
     * 例子：
     * 开：/album/friend/list/ 或 /album/friend/?action=list
     * 关：/?m=album&c=friend&action=list
     *
     * @var bool
     */
    protected static $_rewrite = 0;
    
    /**
     * 模块名
     *
     * @var string
     */
    protected static $_module;
    
    /**
     * 控制器名
     *
     * @var string
     */
    protected static $_controller;
    
    /**
     * 方法名
     *
     * @var string
     */
    protected static $_action;
    
    /**
     * 开始分发
     */
    public static function dispatch($autoSetMCA = true){
        // 设置并获取 module,controller,action
        if($autoSetMCA){
            self::setMCA();
        } 
        $fileName = ROOT_PATH . MODULE_DIR . self::$_module . CONTROLLER_DIR . self::$_controller . '.php';
        if(!is_file($fileName) || !file_exists($fileName)){
            Custom_Common::showMsg('你输入的网址可能不正确，或者该网页不存在');
        }
        
        require_once $fileName;
        $className = CONTROLLER_PREFIX . self::$_module . CONTROLLER_OFFSET . self::$_controller;
        $controllerObj = new $className();
        $actionDo = self::$_action . 'Action';
                
        if(!method_exists($controllerObj, $actionDo)){
            //$actionDo = 'indexAction';
            Custom_Common::showMsg('你输入的网址可能不正确，或者该网页动作不存在');
        }
        $controllerObj->actionName = self::$_action;
        $controllerObj->$actionDo();
    }
    
    /**
     * 设置并获取 module,controller,action
     */
    public static function setMCA(){
        if(self::$_rewrite){ // 是否开启路由重写机制
            $uris = self::getUris();
            $_module = isset($uris[0]) ? $uris[0] : '';
            $_controller = isset($uris[1]) ? $uris[1] : '';
            $_action = isset($uris[2]) ? $uris[2] : '';
        }else{ // 未开启路由重写，则用 get 传参获取
            $_module = self::getParam('m');
            $_controller = self::getParam('c');
            $_action = self::getParam('act');
            if($_module == $GLOBALS['GLOBAL_CONF']['Default_Manager_Module_Path']){
            	$_con = self::getParam('con'); 
            	if($_con){
            		self::setConditions($_con);
            	}
            }else{
            	$uris = self::getPathInfo();
            	if($_action) {
            		self::setFrontConditions($_module, $_controller, $_action, $uris);
            	}
            }
        }
        self::$_module = $_module ? ucfirst(self::ucwords($_module)) : ucfirst(self::getDefault('Module'));
        self::$_controller = $_controller ? ucfirst(self::ucwords($_controller)) : ucfirst(self::getDefault('Controller'));
        self::$_action = $_action ? self::ucwords($_action) : self::getDefault('Action');
    }
    
    public static function setConditions($con){
    	$con = strip_tags(trim(trim($con), '/'));
    	$cons = explode('/', $con);
    	foreach ($cons as $conItem){
    		//list($key, $value) = explode(':', $conItem);
    		$posFirst = stripos($conItem, ':');
    		if($posFirst !== false) {
    			$key = substr($conItem, 0, $posFirst);
    			$value = substr($conItem, $posFirst + 1);
    			$_REQUEST[$key] = $_GET[$key] = $value;
    		}    		
    		
    	}
    }
    
    public static function setFrontConditions($m, $c, $a, $uris){
    	$url = str_replace("/$m/$c/$a", '', $uris);
    	$url = trim($url, '/');
    	if($url) {
	    	$urlArr = explode('/', $url);
	    	$count = count($urlArr);
	    	for($i = 0; $i < $count; $i++) {
	    		$_REQUEST[$urlArr[$i]] = $_GET[$urlArr[$i]] = $urlArr[$i+1];
	    		$i++;
	    	}	    	
    	}
    }
    /**
     * 根据 URL/URI 获取 PATH_INFO 数组
     *
     * @return array
     */
    public static function getUris(){
        $pathInfo = self::getPathInfo();
        $uri = strip_tags(trim(trim($pathInfo), '/'));
        $uris = explode('/', $uri);
        return $uris;
    }
    
    /**
     * 获取全局变量 PATH_INFO
     *
     * @return string
     */
    public static function getPathInfo(){
        $pathInfo = (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : getenv('PATH_INFO');
        if(!$pathInfo){
            $queryPos = strpos($_SERVER['REQUEST_URI'], '?');
            if($queryPos === false){
                $pathInfo = $_SERVER['REQUEST_URI'];
            }else{
                $pathInfo = substr($_SERVER['REQUEST_URI'], 0, $queryPos);
            }
        }
        return $pathInfo;
    }
    
    /**
     * 将全小写的 abc-xyz 转换为 abcXyz 驼峰形式
     *
     * @param string $key
     * @return string
     */
    public static function ucwords($key){
        $key = strtolower($key);
        if(strpos($key, '-') !== false){
            $key = str_replace('-', ' ', $key);
            $key = preg_replace('/[^a-z0-9\s]/', '', $key);
            $key = str_replace(' ', '', ucwords($key));
            $key = strtolower($key[0]) . substr($key, 1);
        }else{
            $key = preg_replace('/[^a-z0-9]/', '', $key); // 只允许小写字母和数字
        }
        return $key;
    }
    
    /**
     * 获取默认分发器配置
     *
     * @param string $mca Module/Controller/Action
     * @return string
     */
    public static function getDefault($mca){
        if(isset($GLOBALS['GLOBAL_CONF']['Default_' . $mca]) && $GLOBALS['GLOBAL_CONF']['Default_' . $mca]){
            return $GLOBALS['GLOBAL_CONF']['Default_' . $mca];
        }
        return 'index';
    }
    
    /**
     * 获取模块名
     */
    public static function getModule(){
        return self::$_module;
    }
    
    /**
     * 获取控制器名
     */
    public static function getController(){
        return self::$_controller;
    }
    
    /**
     * 获取方法名
     */
    public static function getAction(){
        return self::$_action;
    }
    
    /**
     * 获取URL参数
     *
     * @param string $paramName
     * @return mixed
     */
    public static function getParam($paramName){
        return isset($_GET[$paramName]) ? strip_tags(trim($_GET[$paramName])) : '';
    }
    
    /**
     * 转发到另一个 Action
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    public static function forward($module, $controller, $action){
        self::$_module = $module;
        self::$_controller = $controller;
        self::$_action = $action;
        
        self::dispatch(false);
        exit();
    }
}