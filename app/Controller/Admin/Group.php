<?php 
class Controller_Admin_Group extends Controller_Admin_Abstract{
	private $_model;
	//初始化
	public function __construct()
	{	
		parent::__construct();
		is_login($this->_db);
		$this->_model = Model_Admin_Group::getInstance();
	}
	
	public function listAction(){
        $groups = $this->_model->getGroupList();
        $this->_tpl->assign('groups', $groups);
		$this->_tpl->display('admin/group_list.php');		
	}
	
	// 添加组
	public function addAction(){
		if($this->_http->isPost()){			
			$postData = $this->_http->getPost();
			if(empty($postData['g_name'])){
		        Custom_Common::showMsg(
					'组名称不能为空',
					'back',
					array('add' => '继续添加组', 'list' => '返回组列表')	        
		        );				
			}
			$insert_result = $this->_model->insert($postData);
			if($insert_result == 'repeat'){
		        Custom_Common::showMsg(
					'组名称重复',
					'back',
					array('add' => '继续添加组', 'list' => '返回组列表')	        
		        );					
			}
			
			if($insert_result){
		        Custom_Common::showMsg(
					'组添加成功',
					'back',
					array('add' => '继续添加组', 'list' => '返回组列表')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'组添加失败',
					'back'
		        );				
			}
		}
		$this->_tpl->display('admin/group_add.php');		
	}

	// 编辑组
	public function editAction(){

		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(empty($postData['g_name'])){
		        Custom_Common::showMsg(
					'组名称不能为空',
					'back',
					array('add' => '继续添加组', 'list' => '返回组列表')	        
		        );				
			}
			$insert_result = $this->_model->insert($postData);
			if($insert_result){
		        Custom_Common::showMsg(
					'组编辑成功',
					'back',
					array('list' => '返回组列表','edit/'.$this->_http->getPost('gid') => '重新编辑该组')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'组编辑失败',
					'back'
		        );				
			}
		}
		//获取一级模块列表
		
		$id = $this->_http->get('id');
		$group = $this->select("`gid` = {$id}", 'group', '*', '', true);
		$this->_tpl->assign('group', $group);
		$this->_tpl->display('admin/group_add.php');
	}
	
	// 验证组名
	public function checkGroupAction(){
		$getData = $this->_http->getPost();
		$g_name = empty($getData['g_name'])?'':trim($getData['g_name']);
        $id = empty($getData['id']) ? 0 : intval($getData['id']);
        if (!$g_name)
        {
            echo json_encode(true);
            exit;
        }
        if ($this->_model->unique($g_name, $id))
        {
            echo json_encode(true);
        }
        else
        {
            echo json_encode(false);
        }
        exit; 		
	}
	
	// 删除组
    public function dropAction(){
        $id = $this->_http->get('id');
    	if (!$id)
        {
            Custom_Common::showMsg("请您选择要删除的组 ", 'back');
        }
        $ids = explode(',', $id);
        $sql = "select `g_name` from `nj_group` where `gid` ". $this->_model->db_create_in(implode(',', $ids));
        $g_name = $this->_db->fetchCol($sql);
        $g_name = implode(', ', $g_name);
        foreach ($ids as $key=>$id){
            if($id){
            	$resultBack = $this->_model->group_drop($id);
            }
        }        
        $resultBack = $this->_model->group_drop($id);
        if($resultBack){
            Custom_Log::admin_log($_SESSION['admin_id'], 'group_list', '删除管理组'.$g_name, '3');
        	Custom_Common::showMsg("删除组成功。 ", 'back',array('list' => '返回组列表'));
        }   	
    }	
}