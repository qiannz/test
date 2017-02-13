<?php
class Controller_Api_Abstract extends Base {
    
    public function __construct() {
        parent::__construct();
        $this->_city = !$this->_http->get('city') ? $this->_city : strval($this->_http->get('city'));
    }

    /**
     * APP加密验证
     * @param unknown_type $getData
     */
    protected function auth($getData) {
        $encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
        Third_Des::$key = '34npzntC';
        if (!$getData['ssid'] || $getData['ssid'] != Third_Des::encrypt($encryptString)) {
                exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
        }
    }
    /**
     * 全字段加密
     * @param unknown_type $getData
     */
    protected function authAll($getData) {
    	$SignData = $getData['SignData'];
    	unset($getData['m']);
    	unset($getData['c']);
    	unset($getData['act']);
    	unset($getData['api']);
    	unset($getData['SignData']);
    	 
    	ksort($getData);
    	$md5String = '';
    	foreach ($getData as $k => $v) {
    		$md5String .= "{$k}=".urldecode($v)."&";
    	}
    	 
    	$md5String = substr($md5String, 0, -1);
    	$desString = 'sign=' . md5($md5String) . '&time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
    	 
    	Third_Des::$key = '34npzntC';
    	if( !$SignData || Third_Des::encrypt($desString) != $SignData) {
    		exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
    	}
    }
    /**
     * .NET加密验证
     * @param unknown_type $getData
     */
    protected function authZeroNet($getData) {
    
    	$SignData = $getData['SignData'];
    	unset($getData['m']);
    	unset($getData['c']);
    	unset($getData['act']);
    	unset($getData['api']);
    	unset($getData['SignData']);
    
    	$param = array();
    
    	foreach($getData as $key => $value) {
    		$param[strtolower($key)] = $value;
    	}
    	ksort($param);
    
    	//$stringA = http_build_query($param);
    	$stringA = '';
    	foreach ($param as $k => $v) {
    		$stringA .= "{$k}=".urldecode($v)."&";
    	}
    	$stringA = substr($stringA, 0, -1);
    
    	$stringSignTemp = $stringA . '&key=8F0EC841B756454CB09C705E96BF6776';
    	$stringSignTempMd5 = strtoupper(md5($stringSignTemp));
    
    	//logLog('rebateAuthSync.log', $stringSignTemp . var_export($param, true) . ' | ' . $stringSignTempMd5 . ' | ' . $SignData);
    
    	if($stringSignTempMd5 != $SignData) {
    		exit(json_encode($this->returnArr(0, '', 300, '验证失败！')));
    	}
    
    }
}