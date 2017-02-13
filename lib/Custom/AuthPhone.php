<?php
/**
 * Enter description here ...
 *
 * @author ellison
 * @copyright 2011-2012 mplife.com
 * @file: file_name 2015 2013-6-7 ����02:47:05 ellison
 * @history 2013-6-7  ::  ellison  ::  Create File
 * @version
 */
class Custom_AuthPhone {   
    
    public static function isPhoneCodeExists($mobile, $code, $act_id)
    {
		$sql = "select `code` from `act_mobile_check` where `mobile` = '{$mobile}' and `act_id` = '{$act_id}' order by created desc limit 1";
		$phone_code = Core_DB::get('superbuy')->fetchOne($sql);
		if($phone_code && $phone_code == $code){
			return true;
		}else{
			return false;
		}	
    }
    
	public static function sendPhoneMessage($mobile, $randPwd, $content, $act_id){
	    $arr = array(
	    		'act_id' =>  $act_id,
				'mobile'  => $mobile,
				'code'    => $randPwd,
	    		'ip' => CLIENT_IP,
		        'created' => REQUEST_TIME
		);
		$resultMes = Custom_Send::sendMobileMessage($mobile,$content);
		if($resultMes['SendSmsResult'] == 1){
			if(Core_DB::get('superbuy')->insert('act_mobile_check', $arr)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function isPhoneRepeatedly($mobile, $type) {
	    $time = strtotime(date('Y-m-d'));
		$ip = Custom_Client::getUserIp();
		$sql = "select count(id) from `act_mobile_check` where `mobile` = '{$mobile}' and `type` = '{$type}' and `ip` = '{$ip}' and `created` > '".$time."'";
		$num = Core_DB::get('superbuy')->fetchOne($sql);
		if($num >= 5){
			return false;
		}else{
			return true;
		}
	}
	
	public static function isOverdue($mobile, $type) {
	    $sql = "select `created` from `act_mobile_check` where `mobile` = '{$mobile}' and `type` = '{$type}' order by `created` desc limit 1";
	    $find_time = Core_DB::get('superbuy')->fetchOne($sql);
	    return $find_time;
	}
}