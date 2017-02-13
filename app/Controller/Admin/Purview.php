<?php 
class Controller_Admin_Purview extends Controller_Admin_Abstract{
	private $_model;
	//初始化
	public function __construct()
	{	
		parent::__construct();
		is_login($this->_db);
		$this->_model = Model_Admin_Purview::getInstance();
	}
	
	public function ListAction(){
		$group_model = Model_Admin_Group::getInstance();
        $groups = $group_model->getGroupList();
        $this->_tpl->assign('groups', $groups);
		$this->_tpl->display('admin/purview_list.php');
	}
	
	public function AllotAction(){
		if($this->_http->isPost()){
		    $postData = $this->_http->getPost();
			$insert_result = $this->_model->insert($this->_http->getPost());
			if($insert_result){
		        Custom_Common::showMsg(
					'组权限编辑成功',
					'back',
					array('allot/gid:'.$this->_http->getPost('gid') => '重新编辑', 'list' => '返回组列表')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'组权限编辑失败',
					'back'
		        );				
			}			
		}
		$gid = $this->_http->get('gid');
		$moduleAll = $this->_model->getModuleAll();
		$this->_tpl->assign('moduleAll', $moduleAll);
		$this->_tpl->assign('gid', $gid);
		$checkedStr = $this->_model->getGroupModuleChecked($gid);
		$this->_tpl->assign('checkedStr', $checkedStr);
		$this->_tpl->display('admin/purview_allot.php');
	}
}