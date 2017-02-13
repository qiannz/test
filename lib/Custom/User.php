<?php
class Custom_User {
	//修改最后登录IP和时间
	private static function user_latest_login($user_id, $log_ip, $last_time){
		$db = Core_DB::get('mp_nanjing');
		$sql = "update `nj_user` set `login_ip` = '{$log_ip}', `login_time` = '".$last_time."' where `id` = '{$user_id}' limit 1";
		$db->query($sql);
		return true;
	}
	
	private static function user_cookie($user_id, $user_name, $cookie_time){
		setcookie("NJMP[user_id]", $user_id, $cookie_time,  $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, true);
		setcookie("NJMP[username]", $user_name, $cookie_time, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, true);		
	}
	
	private static function user_cookie_empty(){
		$db = Core_DB::get('mp_nanjing');
		$time = time() - 3600;
		setcookie("NJMP[user_id]",  '', $time, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
		setcookie("NJMP[username]", '', $time, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);		 
		Core_Session::getInstance($db, 'nj_sessions', 'nj_sessions_data')->destroy_session();				
	}
	
	//登录设置
	public static function user_session($user_id, $user_name, $email, $remember = null){
		$log_ip = Custom_Client::getUserIp();
		$time = time();
		$cookie_time = $time + 3600 * 24 * 15;
				
		$_SESSION['user_id']   = $user_id;
		$_SESSION['user_name'] = $user_name;
		$_SESSION['email']     = $email;
		//15天内自动登录		
		if($remember){
			self::user_cookie($user_id, $user_name, $cookie_time);
		}
		//最后登录时间
		self::user_latest_login($user_id, $log_ip, $time);		
		return true;
	}
	
	//用户注册
	public static function user_register($username, $password, $email, $access_token, $mobile = null){
		$errorArr = array();
		if((!preg_match("/^[A-Za-z]/", $username)) && (!preg_match("/^[\x{4e00}-\x{9fa5}]/u", $username))){
			$errorArr['username'] = '用户名必须以中文或者字母开头';
		}elseif(!Model_Member_Index::getInstance()->verify_userName($username)){
			$errorArr['username'] = '用户名长度错误，(2-15)个字符';
		}elseif(!Model_Member_Index::getInstance()->isRepeat($username)){
			$errorArr['username'] = '用户名重复，请换一个再试';
		}
			
		if(!preg_match("/^[A-Za-z0-9]+$/", $password)){
			$errorArr['password'] = '密码可由字母、数字组成';
		}elseif(!Model_Member_Index::getInstance()->verify_password($password)){
			$errorArr['password'] = '密码长度错误，(6-30)个字符';
		}
			
		if(!preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email)){
			$errorArr['email'] = '请输入正确的邮箱地址';
		}elseif(!Model_Member_Index::getInstance()->isEmail($email)){
			$errorArr['email'] = '电子邮箱重复，请换一个';
		}
			
		if(!empty($mobile)){
			if(!preg_match("/^[0-9]{11}$/", $mobile)){
				$errorArr['mobile'] = '请输入正确的手机号码';
			}elseif(!Model_Member_Index::getInstance()->isMobile($mobile)){
				$errorArr['mobile'] = '手机号码重复，请换一个';
			}
		}
		if(empty($errorArr)){
			//主站同步
			$clientResult = Custom_AuthLogin::register_user($username, $password);
			if($clientResult['RegisterUserBySubStationResult'] == 1){
				$postData = array(
					'username' => $username,
					'uid' => $clientResult['userID'],
					'password' => $password,
					'email' => $email,
					'mobile' => $mobile		
				);
				$result = Model_Member_Index::getInstance()->userRegister($postData, $access_token);
				if($result){
					Custom_Common::sendjson('ok', '绑定成功');
				}
			}else{
				Custom_Common::sendjson('-1', '主站验证不通过，请稍候再试');
			}
		}else{
			Custom_Common::sendjson('-2', $errorArr);
		}
	}
	
	public static function user_auto_register($username, $access_token, $access_type){		
		$password = Custom_Common::random(8, false);
		//递归获取不重复用户名
		$username = self::get_the_user_name($username, $access_type);
		if($username){
			$clientResult = Custom_AuthLogin::register_user($username, $password);
			if($clientResult['RegisterUserBySubStationResult'] == 1){
				$postData = array(
						'username' => $username,
						'uid' => $clientResult['userID'],
						'email' => '',
						'mobile' => ''
				);
				$result = Model_Member_Index::getInstance()->userRegister($postData, $access_token);
				if($result){
					return true;
				}				
			}
		}
		return false;
	}
	
	private static function get_the_user_name($user_name, $access_type, $num = 0){
		if($num >= 20){
			return false;
		}
		$result = Custom_AuthLogin::register_check_user($user_name);
		if($result != 0){
			$user_name = $access_type.'_'.Custom_Common::random(6);
			$num++;
			self::get_the_user_name($user_name, $access_type, $num);
		}
		return $user_name;
	}
	
	public static function user_login($username, $password, $access_token, $remeber = null){
		$errorArr = array();
		if (empty($username))
		{
			self::user_cookie_empty();
		}
		
		if((!preg_match("/^[A-Za-z]/", $username)) && (!preg_match("/^[\x{4e00}-\x{9fa5}]/u", $username))){
			$errorArr['username'] = '用户名必须以中文或者字母开头';
		}elseif(!Model_Member_Index::getInstance()->verify_userName($username)){
			$errorArr['username'] = '用户名长度错误，(2-15)个字符';
		}
			
		if(empty($password)){
			$errorArr['password'] = '密码不能为空';
		}
		
		if(empty($errorArr)){
			$clientResult = Custom_AuthLogin::user_login($username, $password);
			if($clientResult['LoginUserBySubStationResult'] == 1){
				$result = self::synchronization_login($username, $password, $clientResult['userID'], $access_token);
				if($result){
					Custom_Common::sendjson('ok', '绑定成功');
				}else{
					Custom_Common::sendjson('-1', '绑定失败');
				}
			}else{
				Custom_Common::sendjson('-2', '用户名或者密码错误');
			}			
		}else{
			Custom_Common::sendjson('-3', $errorArr);
		}
	}
	
	private static function synchronization_login($username, $password, $uid, $access_token,  $remeber = null){
		$db = Core_DB::get('mp_nanjing');
		$sql = "select `id`, `user_name`, `email`, `status`  from `nj_user` WHERE `user_name`='{$username}' and `uid` = '{$uid}'  limit 1";
		$row = $db->fetchRow($sql);
		if(!empty($row) && $row['status'] == 0){
			$db->update('nj_user', $access_token, "`id` = ". $row['id']);
			self::user_session($row['id'], $row['user_name'], $row['email'], $remeber);
			return true;
		}
		
		//用户主站存在  AND 分站不存在   则分站新增
		if(empty($row)){
			$time = time();
			$ip = Custom_Client::getUserIp();
			$user = array(
					'user_name' => $username,
					'uid' => $uid,
					'register_ip' => $ip,
					'register_time' => $time,
					'login_ip' => $ip,
					'login_time' => $time,
					'last_pull_time' => $time,
					'come_from' => 3 //主站标记
			);
			if(!empty($access_token) && is_array($access_token)){
				$user = array_merge($user, $access_token);
			}
			$insert_id = $db->insert('nj_user', $user);
			Model_Member_Index::getInstance()->userInfo($insert_id);
			self::user_session($insert_id, $username, '', $remeber);
			return true;
		}
		return false;
	}
}