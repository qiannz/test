<?php
class Controller_Admin_Index extends Controller_Admin_Abstract {
	//初始化
	public function __construct()
	{
		parent::__construct();
	}
	
	//首页加载
	public function indexAction() {
		$back_nav = $menu = $this->_get_menu($this->_userInfo);
		unset($back_nav['dashboard']);
		$this->_tpl->assign('menu', $menu);
		$this->_tpl->assign('back_nav', $back_nav);
		$this->_tpl->assign('menu_json', json_encode($menu));
		
		$this->_tpl->assign('city_options', $this->_city_options);		
		$this->_tpl->assign('city_selected', $this->_ad_city);
		$this->_tpl->display('admin/index.php');
	}
	//欢迎页面
	public function welcomeAction() {
		$welcome = array(
			'PHP_OS' => PHP_OS,
			'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],
			'PHP_VERSION' => PHP_VERSION,
			'MYSQL_VERSION' => $this->_db->version()
		);
		$this->_tpl->assign('welcome', $welcome);   
		$this->_tpl->display('admin/welcome.php');
	}
	
	public function changeCityAction() {
		$city = $this->_http->get('city');
		cookie('_ad_city', $city, 0, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
		exit('ok');
	}

	public function logoutAction() {
		if($_COOKIE['_ad_id']) {
			$row = $this->select("`id` = '".deBase64($_COOKIE['_ad_id'])."'", 'oto_admin', 'userid, pwd, logintime, loginip', '', true);
			if(file_exists(ROOT_PATH.'var'.DIRECTORY_SEPARATOR.'manager'.DIRECTORY_SEPARATOR.$row['userid'].'.php')){
				unlink(ROOT_PATH.'var'.DIRECTORY_SEPARATOR.'manager'.DIRECTORY_SEPARATOR.$row['userid'].'.php');
			}
		}		
		cookie('_ad_id', '', 1, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
		cookie('_ad_userid', '', 1, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
		cookie('oto_captcha',   '', 1, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
		cookie('_ad_city', '', 1, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
		Custom_Common::jumpto('/admin/index/login', 1);
	}
	
	//登录
	public function loginAction() {
		if($this->_http->isPost()) {
			if (!empty($_COOKIE['oto_captcha']))
			{
				$img = Third_Captcha::getInstance(RESOURCE_PATH.'captcha/'); 					
				if (!$this->_http->getPost('captcha') || !$img->check_word($this->_http->getPost('captcha')))
				{
					Custom_Common::showMsg('非常抱歉，在处理您的请求时出现了一些错误，如下：<br>验证码错误。', 'back');
				}
			}
			$userid = $this->_http->getPost('uname') ? trim($this->_http->getPost('uname')) : '';
			$pwd = $this->_http->getPost('psw') ? trim($this->_http->getPost('psw')) : '';
	
			/* 检查密码是否正确 */
			$row = $this->select("`userid` = '{$userid}' and `pwd` = '".md5($pwd)."' and `is_disabled` = '0'", 'oto_admin', 'id, userid, pwd, role_id', '', true);
			if (!empty($row))
			{				
				// 更新最后登录时间和IP
				$this->_db->update(oto_admin, array('logintime' => time(), 'loginip' => CLIENT_IP),"id='{$row['id']}'");
				if ($this->_http->getPost('remember'))
				{
					$time = time() + 3600 * 24 * 365;
					cookie('_ad_id', enBase64($row['id']), $time, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
					cookie('_ad_userid', enBase64($row['userid']), $time, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
				}
				else
				{
					cookie('_ad_id', enBase64($row['id']), 0, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
					cookie('_ad_userid', enBase64($row['userid']), 0, '/', $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain']);
				}
				Model_Admin_Purview::getInstance()->setPermissions($row);
				Custom_Common::jumpto('/admin');	
			}
			else
			{
				Custom_Common::showMsg('非常抱歉，在处理您的请求时出现了一些错误，如下：<br>您输入的帐号信息不正确。', 'back');
			}		
	
		}
        $this->_tpl->assign('random_number', time());
        $this->_tpl->display('admin/login.php');
	}
	
    //验证码
    public function captchaAction() {
        $img = Third_Captcha::getInstance(RESOURCE_PATH.'captcha/');	     
        //@ob_clean(); //清除之前出现的多余输入
        $img->generate_image();
    }
    
    //获取操作模块
    private function _get_menu() {
    	if(!is_file(ROOT_PATH.'var/manager/'.$this->_userInfo['userid'].'.php')){
    		Custom_Common::jumpto('/admin/index/login');
    	}
    	$menu = @include(ROOT_PATH.'var/manager/'.$this->_userInfo['userid'].'.php');
    	return $menu;
    }
       
    //更新缓存
    public function clearCacheAction() {
        if($this->_mem->flush()) {
            logLog('clear.log', $this->_userInfo['userid'].' 清空缓存');
            exit(json_encode(array('msg' => '缓存更新成功')));
         }
        /*
        $leterArr = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
        foreach($leterArr as $leter) {
        	$dir = VAR_PATH . 'file_cache/' . $leter;
        	if (is_dir($dir)) {
        		if ($dh = opendir($dir)) {
        			while (($file = readdir($dh)) !== false) {
        				unlink($dir . '/' . $file);
        			}
        			closedir($dh);
        		}
        	}
        		 
        	 
        }
        exit(json_encode(array('msg' => '缓存更新成功')));
        */
    }    

}