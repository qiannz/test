<?php
class Controller_Home_Abstract extends Base {
	protected $_userInfo;
	protected $_user_id;
	protected $_module;
	protected $_controller;
	protected $_action;
		
	public function __construct()
	{
		parent::__construct();	
		$this->_userInfo = $this->isWebLogin();
		if($this->_userInfo) {
			$this->_user_id = $this->_userInfo['user_id'];
			//当前用户不是普通会员时，他管理的店铺数量
			if ($this->_userInfo['user_type'] == 2 || $this->_userInfo['user_type'] == 3) {
				$this->_userInfo['shopNum'] = Model_Home_Suser::getInstance()->getPermissionShopByUserId($this->_userInfo['user_id']);
			}
			$this->_tpl->assign('user', $this->_userInfo);
		}
		$this->_module = strtolower(Core_Router::getModule());
		$this->_controller = strtolower(Core_Router::getController());
		$this->_action = strtolower(preg_replace('/([A-Z])/','-\1', Core_Router::getAction()));
		$this->_tpl->assign('_ACTION', $this->_action);
		$GLOBALS['GLOBAL_CONF']['_M'] = $this->_module;
		$GLOBALS['GLOBAL_CONF']['_C'] = $this->_controller;
		$GLOBALS['GLOBAL_CONF']['_A'] = $this->_action;
		$GLOBALS['GLOBAL_CONF']['FORM_ACTION'] = '/'.$this->_module.'/'.$this->_controller.'/'.$this->_action;
		 
		$this->_tpl->assign('http_uri', HTTP_URI);
		$this->_tpl->assign('_CONF', $GLOBALS['GLOBAL_CONF']);
	}
	/**
	 * .NET加密
	 * @param unknown_type $getData
	 */
	public function auth($getData) {
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
	
		$stringA = '';
		foreach ($param as $k => $v) {
			$stringA .= "{$k}=".urldecode($v)."&";
		}
		$stringA = substr($stringA, 0, -1);
	
		$stringSignTemp = $stringA . '&key=8F0EC841B756454CB09C705E96BF6776';
		$stringSignTempMd5 = strtoupper(md5($stringSignTemp));
		if($stringSignTempMd5 != $SignData) {
			_sexit('fail', 500, '验证失败');
		}
	}
}