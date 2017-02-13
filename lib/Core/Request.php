<?php

/**
 * Zend_Controller_Request_Http
 *
 */
class Core_Request {
    private static $_instance = NULL;
    
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();            
        }
        return self::$_instance;
    }
    
    /**
     * Access values contained in the superglobals as public members
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
     *
     * @see http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     * @param string $key
     * @return mixed
     */
    public function __get($key){
        switch(TRUE){
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case ($key == 'REQUEST_URI'):
                return $this->getRequestUri();
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return NULL;
        }
    }
    
    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key){
        switch(TRUE){
            case isset($_GET[$key]):
                return TRUE;
            case isset($_POST[$key]):
                return TRUE;
            case isset($_COOKIE[$key]):
                return TRUE;
            case isset($_SERVER[$key]):
                return TRUE;
            case isset($_ENV[$key]):
                return TRUE;
            default:
                return FALSE;
        }
    }
    
    /**
     * Alias to __get
     *
     * @param string $key
     * @return mixed
     */
    public function get($key){
        return $this->__get($key);
    }
    
    /**
     * Alias to __isset()
     *
     * @param string $key
     * @return boolean
     */
    public function has($key){
        return $this->__isset($key);
    }
    
    /**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns NULL if key does not exist
     */
    public function getQuery($key = NULL, $default = NULL){
        if(NULL === $key){
            return $_GET;
        }
        
        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }
    
    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns NULL if key does not exist
     */
    public function getPost($key = NULL, $default = NULL){
        if(NULL === $key){
            return $_POST;
        }
        
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }
    
    /**
     * Retrieve a member of the $_COOKIE superglobal
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns NULL if key does not exist
     */
    public function getCookie($key = NULL, $default = NULL){
        if(NULL === $key){
            return $_COOKIE;
        }
        
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }
    
    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns NULL if key does not exist
     */
    public function getServer($key = NULL, $default = NULL){
        if(NULL === $key){
            return $_SERVER;
        }
        
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }
    
    /**
     * Retrieve a member of the $_ENV superglobal
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns NULL if key does not exist
     */
    public function getEnv($key = NULL, $default = NULL){
        if(NULL === $key){
            return $_ENV;
        }
        
        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }
    
    /**
     * Retrieve a parameter
     *
     * Retrieves a parameter from the instance. Priority is in the order of
     * userland parameters (see {@link setParam()}), $_GET, $_POST. If a
     * parameter matching the $key is not found, NULL is returned.
     *
     * @param mixed $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = NULL){
        if(isset($_GET[$key])){
            return $_GET[$key];
        }elseif(isset($_POST[$key])){
            return $_POST[$key];
        }
        return $default;
    }
    
    /**
     * Retrieve an array of parameters
     *
     * Retrieves a merged array of parameters, with precedence of userland
     * params (see {@link setParam()}), $_GET, $POST (i.e., values in the
     * userland params will take precedence over all others).
     *
     * @return array
     */
    public function getParams(){
        $result = array();
        if(isset($_GET) && is_array($_GET)){
            $result += $_GET;
        }
        if(isset($_POST) && is_array($_POST)){
            $result += $_POST;
        }
        return $result;
    }
    
    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost(){
        return ('POST' == $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet(){
        return ('GET' == $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut(){
        return ('PUT' == $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete(){
        return ('DELETE' == $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead(){
        return ('HEAD' == $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions(){
        return ('OPTIONS' == $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Return the raw body of the request, if present
     *
     * @return string|FALSE Raw body, or FALSE if not present
     */
    public function getRawBody(){
        $body = file_get_contents('php://input');
        
        if(strlen(trim($body)) > 0){
            return $body;
        }
        
        return FALSE;
    }
    
    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     * @return string|FALSE HTTP header value, or FALSE if not found
     * @throws Core_Exception
     */
    public function getHeader($header){
        if(empty($header)){
            throw new Core_Exception('An HTTP header name is required');
        }
        
        // Try to get it from the $_SERVER array first
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if(!empty($_SERVER[$temp])){
            return $_SERVER[$temp];
        }
        
        // This seems to be the only way to get the Authorization header on
        // Apache
        if(function_exists('apache_request_headers')){
            $headers = apache_request_headers();
            if(!empty($headers[$header])){
                return $headers[$header];
            }
        }
        
        return FALSE;
    }
    
    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest(){
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }
    
    /**
     * Is this a Flash request?
     *
     * @return bool
     */
    public function isFlashRequest(){
        return ($this->getHeader('USER_AGENT') == 'Shockwave Flash');
    }
    
    /**
     * get HTTP user agent
     *
     * @return string
     */
    public function getUserAgent(){
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    
    /**
     * get HTTP referer
     *
     * @return string
     */
    public function getHttpReferer(){
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
    
    /**
     * Aja header output
     *
     * @return void
     */
    public function ajaxHeader(){
        header('Content-type:text/html;charset=utf-8');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', FALSE);
        header('Pragma: no-cache');
    }
    
    /**
     * 检测表单 POST 提交
     *
     * @return bool
     */
    public function submitCheck($var){
        if(!$this->isPost() || !$this->getPost($var)){
            return false;
        }
        if((empty($_SERVER['HTTP_REFERER']) || preg_replace('/https?:\/\/([^\:\/]+).*/i', '\\1', $_SERVER['HTTP_REFERER']) == preg_replace('/([^\:]+).*/', '\\1', $_SERVER['HTTP_HOST'])) && $this->getPost('formhash') == $this->formHash()){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 表单验证码
     *
     * @return string
     */
    public function formHash($specialAdd = ''){
        $authKey = md5('RANDOM_SILVER_CODE_AJSDLJLJOZX');
        return substr(md5(substr(time(), 0, -5) . session_id() . $authKey . $specialAdd), 8, 8);
    }
    
	/**
	 * 表单验证码防刷新
	 * @return string
	 */
    public function formHashRefresh() {
    	session_start();
    	$formhash = md5( uniqid() );
    	$_SESSION[$formhash] = 1;
    	return $formhash;
    }
    /**
     * 检测防刷新表单 POST 提交
     * @param unknown_type $formhash
     * @return boolean
     */
    public function submitCheckRefresh() {
    	session_start();
    	if(!$this->isPost() || !$this->getPost('formhash')){
    		return false;
    	}
    	if($_SESSION[$this->getPost('formhash')] == 1){
    		$_SESSION[$this->getPost('formhash')] = 2;
    		return true;
    	}else{
    		return false;
    	}
    }
}