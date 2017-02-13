<?php
class Controller_Active_Saturday extends Controller_Home_Abstract {
	
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Saturday::getInstance();
	}
	
	public function indexAction() {
		//$this->_tpl->display('active/saturday/index2.php');
		$this->_tpl->display('active/saturday/index_20150202.php');
		exit();
		
        $rid = $this->_http->get('rid');
        $recommend_url = $rid ? 'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b&rid='.$rid :'http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=bad427e2-351b-4830-993b-517d2ea25f8b' ;
        $this->_tpl->assign('recommend_url',$recommend_url);

        $url = $this->_userInfo? $url = $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/active/saturday?rid='.$this->_userInfo['uuid'] : '' ;
        $this->_tpl->assign('url',$url);

        $imgStr = $this->_userInfo? $this->getQrcode($url,$this->_userInfo['uuid']) : '';
        $this->_tpl->assign('imgstr',$imgStr);

        $InviteNum = $this->_model->getInviteNum($this->_userInfo['user_id']);
        $this->_tpl->assign('invitenum',$InviteNum);

        $this->_tpl->assign('http_uri',HTTP_URI);
        $this->_tpl->assign('user_id' , $this->_userInfo['user_id']);
        $this->_tpl->assign('user_name',$this->_userInfo['user_name']);
		$this->_tpl->display('active/saturday/index.php');
	}

    public function nyAction(){
        $this->_tpl->display('active/saturday/ny.php');
    }

    public function wapAction(){
        //$this->_tpl->display('active/saturday/wap.php');
        $this->_tpl->display('active/saturday/wap_20150130.php');
    }

    public function wapNyAction(){
        $this->_tpl->display('active/saturday/wap_ny.php');
    }

    private  function getQrcode($content , $uuid) {
        $folder = substr(md5($uuid), 0, 1);
        $fileName = ROOT_PATH . 'web/data/code/' . $folder . '/' . $uuid . '.png';
        $imgStr = Custom_Image::twoCode($content , $fileName);
        return $imgStr;
    }
}