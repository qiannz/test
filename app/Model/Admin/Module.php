<?php 
class Model_Admin_Module extends Base {

    private static $_instance = NULL;
	protected $_table = 'module';
		
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
    
    // 获取模板 树状结构
    public function getModuleList(){
    	include ROOT_PATH.'lib/Third/Tree/tree.lib.php';
    	$tree = new Tree();
    	$moduleAll = array();
    	$sql = "select * from `".$this->_table."` order by sequence asc, mid asc";
    	$moduleAll = $this->_db->fetchAssoc($sql);
    	$tree->setTree($moduleAll, 'mid', 'pid', 'm_name');
     	$sorted_acategories = array();
        $cate_ids = $tree->getChilds();
               
        foreach ($cate_ids as $id)
        {
           $sorted_acategories[] = array_merge($moduleAll[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>1));
        }
		return $sorted_acategories;
    }
    
    // 获取模板
    public function getModuleSelect($pid = 0){
    	$moduleParent = array();
 		$sql = "select * from `".$this->_table."` where `pid` = {$pid} order by sequence asc, mid asc";
		$moduleParent = $this->_db->fetchAll($sql);
		return $moduleParent; 	
    }
    
    // 新增模板
    public function module_insert($postData){
    	$mid = !$postData['mid']?0:$postData['mid'];
 		$m_name = trim($postData['m_name']);
		$pid = !$postData['pid']?0:$postData['pid'];
		$m_path = $postData['m_path'];
		$mark = trim($postData['mark']);
		$sequence = intval($postData['sequence']);
		if($mid == 0){
			if(!$this->unique($m_name, $pid)){
				return 'repeat';
			}
			$insert_id = $this->_db->insert($this->_table, array(
				'm_name' => $m_name,
				'pid' => $pid,
				'm_path' => $m_path,
				'mark' => $mark,
				'sequence' => $sequence,
			));
			return $insert_id?$insert_id:false; 
		}else{
			$affected_rows = $this->_db->update($this->_table, array(
				'm_name' => $m_name,
				'pid' => $pid,
				'm_path' => $m_path,
				'mark' => $mark,
				'sequence' => $sequence,
			),
			"`mid` = $mid"
			);
			return $affected_rows?$affected_rows:false;   					
		}
    }
    
    // ajax编辑模板
    public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
		if($column == 'm_name'){
			$acategory = $this->get_info($id);
	        if(!$this->unique($value, $acategory['pid'], $id))
	        {
		        return json_encode(false);
	        }			
		}
		$result = $this->_db->update($this->_table,array($column => $value), "`mid` = $id");
		if($result){
			echo json_encode(true);
		}   	
    }
    
    // 获取信息函数
    public function get_info($mid){
    	$sql = "select * from `".$this->_table."` where `mid` = '{$mid}'";
    	return $this->_db->fetchRow($sql);
    }
    
    // 验证函数
	public function unique($m_name, $pid, $mid = 0)
    {
        $conditions = "pid = '$pid' AND m_name = '$m_name'";
        $mid && $conditions .= " AND mid <> '" . $mid . "'";
        
        $sql = "select count(*) from `".$this->_table."` where $conditions";
        return $this->_db->fetchOne($sql) == 0;
    }
    
    // 删除模板
	public function module_drop($id){
		$rowAll = array();
		$sql = "select * from `".$this->_table."` where `pid` = $id";
		$rowAll = $this->_db->fetchAll($sql);
		$delResult = $this->_db->delete($this->_table, "`mid` = $id");
		if(!empty($rowAll) && is_array($rowAll)){
			foreach ($rowAll as $rowItem){
				$this->module_drop($rowItem['mid']);
			}
		}else{
			$delResult = $this->_db->delete($this->_table, "`mid` = $id");
		}
		
		return $delResult;
	}
}
