<?php 
class Controller_Error_Msg extends Controller_Home_Abstract {
    private static $_instance = NULL;
    public static function getInstance(){
        if(self::$_instance === NULL){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	//报错
    public function showMsgAction($message, $redirect = null, $links = null){
    	$this->_tpl->assign('message', $message);
    	if($redirect){
    		$this->_tpl->assign('redirect', $redirect);
    	}
    	if(!empty($links) && is_array($links)){
    		$this->_tpl->assign('links', $links);
    	} 

    	if($this->_module == $GLOBALS['GLOBAL_CONF']['Default_Manager_Module_Path']){
    		$this->_tpl->display('_common/admin_msg.php');
    	}else{
    		$this->_tpl->display('_common/front_msg.php');
    	}
    	exit();
    }
}