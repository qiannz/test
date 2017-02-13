<?php 
class Model_Admin_Purview extends Base {

    private static $_instance = NULL;
	protected $_table = 'group_module';
	private $_where = '';
	private $_order = '';
	private $moduleAll;
	
    public static function getInstance()
    {
        if (self::$_instance === NULL) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->moduleAll = array();
    }
      
    public function getModuleAll($pid = 0){
    	$moduleArr = array();
    	$sql = "select * from `module` where `pid` = {$pid} order by `sequence` asc, `mid` asc";
    	$moduleArr = $this->_db->fetchAll($sql);
    	foreach ($moduleArr as $key => $module){
    		foreach ($module as $ckey => $value){
    			$this->moduleAll[$key][$ckey] = $value;
    			if(($ckey == 'mid') && ($this->_db->fetchOne("select count(*) from `module` where `pid` = {$value}") > 0)){
		    		$this->moduleAll[$key]['children'] = $this->getChildAll($value);			
    			}   			
    		}   		
    	}    	
    	return $this->moduleAll;
    }
    
	public function getChildAll($pid){
    	$childArr = array();
    	$sql = "select * from `module` where `pid` = {$pid} order by `sequence` asc, `mid` asc";
    	$childArr = $this->_db->fetchAll($sql);
    	foreach ($childArr as $key => $child){
    		foreach ($child as $ckey => $value){
    			$childArr[$key][$ckey] = $value;  			
    		}  		   		
    	}   	
    	return $childArr;
    }

    public function insert($postData){
    	$gid = intval($postData['gid']);
    	$str = trim($postData['str']);
    	$sql = '';
    	$strArr = explode(',', $str);
    	foreach ($strArr as $value){
    		if($value){
    			$sql .= "($gid, $value), ";
    		}
    	}
    	if($sql){
    		$this->_db->delete($this->_table, "`gid` = $gid", 0);
    		$sql = "insert into `".$this->_table."` values ".substr(trim($sql), 0, -1);
    		$result = $this->_db->query($sql);
    		if($result){
    			return true;
    		}
    	}
    	
    	return false;
    }
    
    function getGroupModuleChecked($gid){
    	$selectArr = array();
    	$checkedStr = '';
    	$sql = "select * from `".$this->_table."` where `gid` = {$gid} order by mid";
    	$selectArr = $this->_db->fetchAll($sql);
    	foreach ($selectArr as $selectItem){
    		$checkedStr .= $selectItem['mid'].',';
    	}
    	return $checkedStr?substr($checkedStr, 0, -1):'';
    }

    function getUserGroupArray($user_id){
    	$gid = $this->_db->fetchOne("select `gid` from `oto_admin` where `id` = {$user_id}");
    	return explode(',', $this->getGroupModuleChecked($gid));
    }
    
    function setPermissions(& $user){
    	$userGroupArray = array();
    	$userModuleArray = array();
    	$this->getModuleAll();
    	$userGroupArray = $this->getUserGroupArray($user['id']);
    	if($user['role_id'] != 1){
	    	foreach ($this->moduleAll as $key => $module){
	    		if(!empty($module['children'])){
	    			foreach ($module['children'] as $skey => $children) {
	    				if(!in_array($children['mid'], $userGroupArray)){
	    					unset($this->moduleAll[$key]['children'][$skey]);
	    				}
	    			}
	    		}
	    	}
    	}
    	foreach ($this->moduleAll as $key => $module){
    		if(empty($module['children'])){
    			unset($this->moduleAll[$key]);
    		}
    	}
    	    	
    	foreach ($this->moduleAll as $key => $module){
    		$userModuleArray[$module['mark']] = array('text' => $module['m_name'], 'subtext' => $module['m_name']);
    		$flag = false;
    		foreach ($module['children'] as $skey => $children) {
    			if(!$flag){
    				$userModuleArray[$module['mark']]['default'] = $children['mark'];
    				$flag = true;
    			}
    			$userModuleArray[$module['mark']]['children'][$children['mark']] = array('text' => $children['m_name'], 'url' => '/'.$GLOBALS['GLOBAL_CONF']['Default_Manager_Module_Path'].'/'.$children['m_path']);
    		}
    	}
    	$savePath = ROOT_PATH.'var'.DIRECTORY_SEPARATOR.'manager'.DIRECTORY_SEPARATOR;
    	if(!is_dir($savePath)){
    		mkdir($savePath, true);
    		chmod($savePath, 0777);
    	}
    	file_put_contents($savePath.$user['userid'].'.php', "<?php\r\n return ". var_export($userModuleArray, true).";", LOCK_EX);
    }
}