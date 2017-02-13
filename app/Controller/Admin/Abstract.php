<?php

/**
 * 后台控制器基类
 *
 */

class Controller_Admin_Abstract extends Base {
	protected $_userInfo;
	protected $_module;
	protected $_controller;
	protected $_action;
	protected $_city_options;
	protected $pmodule;
	protected $cmodule;
	
	public function __construct()
	{
		parent::__construct();
		list($this->pmodule,$this->cmodule) = is_login($this->_db);
		//用户信息
		if(!empty($_COOKIE['_ad_id']))
		{
			$this->_userInfo = $this->select("`id` = '".deBase64($_COOKIE['_ad_id'])."'", 'oto_admin', '*', '', true);
			$this->_tpl->assign('user', $this->_userInfo);
			$this->_tpl->assign('_ad_city', $this->_ad_city);
		}
		
		$this->_module = strtolower(Core_Router::getModule());
		$this->_controller = strtolower(Core_Router::getController());
		$this->_action = strtolower(preg_replace('/([A-Z])/','-\1', Core_Router::getAction()));
		$this->_tpl->assign('_ACTION', $this->_action);
		$GLOBALS['GLOBAL_CONF']['_M'] = $this->_module;
		$GLOBALS['GLOBAL_CONF']['_C'] = $this->_controller;
		$GLOBALS['GLOBAL_CONF']['_A'] = $this->_action;
		$GLOBALS['GLOBAL_CONF']['FORM_ACTION'] = '/'.$this->_module.'/'.$this->_controller.'/'.$this->_action;

		$this->_city_options = array(
				'sh' => '上海市',
				'nj' => '南京市',
				'nb' => '宁波市'
				);
		
		$this->_tpl->assign('_CONF', $GLOBALS['GLOBAL_CONF']);
	}
	
	public function getPageStr($getData) {
		$page_str = '';
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:".urlencode($value)."/";
			}
		}
		return $page_str;		
	}
}