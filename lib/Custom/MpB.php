<?php
/**
 * 名品币接口
 *
 * @author ellison
 * @copyright 2011-2012 mplife.com
 * @file: file_name 2015 2013-8-28 ����03:53:32 ellison
 * @history 2013-8-28  ::  ellison  ::  Create File
 * @version
 */
class Custom_MpB {   
    
    public static function MpAuth() {
        ini_set("soap.wsdl_cache_enabled", "0");
        
        $client = new SoapClient($GLOBALS['GLOBAL_CONF']['Mp_Auth_Url']);
        
        $auth =array('UserName'=> $GLOBALS['GLOBAL_CONF']['Mp_Auth_User'], 'Password'=> $GLOBALS['GLOBAL_CONF']['Mp_Auth_Password']);
        
        $authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://tempuri.org/');
        
        $header =  new SoapHeader('http://tempuri.org/', "Auths", $authvalues, false);
        
        $client->__setSoapHeaders(array($header));        
        
        return $client;
    }
    
    /**
     * 
     * 加名品币
     * @param  $userName 用户名
     * @param  $mp 名品币
     * @return 1:成功 0：数据库失败，-1：来源不存在，-2：会员不存在， -3：操作员不存在，-4：账户冻结， -5：账户MP币不足， -6：消费规则不正确
     */
    public static function MpAdd($userName, $mp, $description = '线报奖励') {
        $param = array(
        	'userName' => $userName,
        	'type' => 1,
        	'systemType' => 2,
        	'source' => '折扣线报',
        	'description' => $description,
        	'mp' => $mp,
        	'operatorid' => 'CBF6CC11-3FD8-4E5D-B661-1E13010794F6'
        );
        $clientObject = self::MpAuth()->MPAdditional($param);
        $clientResult = objectToArray($clientObject);
		return $clientResult['MPAdditionalResult'];
        
    }
    
    /**
     * 
     * 减品币
     * @param  $userName 用户名
     * @param  $mp 名品币
     * @return 1:成功 0：数据库失败，-1：来源不存在，-2：会员不存在， -3：操作员不存在，-4：账户冻结， -5：账户MP币不足， -6：消费规则不正确
     */
    public static function MpMinus($userName, $mp, $description = '线报扣除') {
        $param = array(
        	'userName' => $userName,
        	'type' => 1,
        	'systemType' => 2,
        	'source' => '折扣线报',
        	'description' => $description,
        	'paymp' => $mp,
        );
        $clientObject = self::MpAuth()->MPConsume($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult['MPConsumeResult'];
    }	
    
    /**
     * 
     * 查询名品币
     * @param  $userName 用户名
     * @return 名品币数值
     */
    public static function MpQuery($userName) {
        $param = array(
			'userName' => $userName,
        );
        $clientObject = self::MpAuth()->GetMPCurrencyByUserName($param);
		$clientResult = objectToArray($clientObject);
		return $clientResult['GetMPCurrencyByUserNameResult'];
    }
}