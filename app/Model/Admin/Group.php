<?php 
class Model_Admin_Group extends Base {

    private static $_instance = NULL;
	protected $_table = 'group';
	
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
    }
    
    // 获取组链接
    public function getGroupList(){
    	$sql = "select * from `".$this->_table."` order by `gid`";
    	$groups = $this->_db->fetchAll($sql);
    	return $groups?$groups:array();
    }
    
    // 新增组
    public function insert($postData){
     	$gid = isset($postData['gid'])?intval($postData['gid']):0;
     	$g_name = isset($postData['g_name'])?trim($postData['g_name']):'';
		
		$time = time();
		$arr = array(
				'g_name' => $g_name,
			);		
		if($gid == 0){
			if(!$this->unique($g_name, $gid)){
				return 'repeat';
			}			
			$insert_id = $this->_db->insert($this->_table, $arr);
			return $insert_id?$insert_id:false; 
		}else{			
			$affected_rows = $this->_db->update($this->_table, $arr,"`gid` = $gid");
			return $affected_rows?$affected_rows:false;   					
		}   		   	
    }
    
    // 验证组
	public function unique($g_name, $gid = 0)
    {
        $conditions = "`g_name` = '{$g_name}'";
        $gid && $conditions .= " AND `gid` <> $gid";
        
        $sql = "select count(*) from `".$this->_table."` where $conditions";
        return $this->_db->fetchOne($sql) == 0;
    }
    
    // 删除组
	public function group_drop($id){
		$delResult = $this->_db->delete($this->_table, "`gid` = $id");				
		return $delResult;
	}
}