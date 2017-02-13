<?php
class Controller_Admin_Module extends Controller_Admin_Abstract{
	private $_model;
	//初始化
	public function __construct()
	{	
		parent::__construct();
		is_login($this->_db);
		$this->_model = Model_Admin_Module::getInstance();
	}
	//模块列表
	public function listAction(){
				
		$acategories = $this->_model->getModuleList();
		$row = array(0 => 0);
        $map = array();
        foreach ($acategories as $key => $acategory)
        {
            $row[$acategory['mid']] = $key + 1;
            $map[] = $row[$acategory['pid']];
        }
        $this->_tpl->assign('map', json_encode($map));
		$this->_tpl->assign('acategories', $acategories);
		$this->_tpl->assign('max_layer', 2);
		$this->_tpl->display('admin/module_list.php');
	}
	//模块添加
	public function addAction(){

		if($this->_http->isPost()){
		    $postData = $this->_http->getPost();
			$insert_result = $this->_model->module_insert($this->_http->getPost());
			if($insert_result == 'repeat'){
		        Custom_Common::showMsg(
					'当前分类下模块名称重复',
					'back',
					array('add/pid:' . $postData['pid'] => '继续添加模块', 'list' => '返回模块列表')	        
		        );					
			}
			
			if($insert_result){
		        Custom_Common::showMsg(
					'模块添加成功',
					'back',
					array('add/pid:' . $postData['pid'] => '继续添加模块', 'list' => '返回模块列表')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'模块添加失败',
					'back'
		        );				
			}
		}
		//获取一级模块列表
		$pid = $this->_http->get('pid');
		$this->_tpl->assign('pid', $pid);
		$moduleArr = $this->_model->getModuleSelect();
		$this->_tpl->assign('moduleArr', $moduleArr);
		$this->_tpl->display('admin/module_add.php');
	}

	public function editAction(){
		if($this->_http->isPost()){
		    $postData = $this->_http->getPost();
			$insert_result = $this->_model->module_insert($postData);
			if($insert_result){
		        Custom_Common::showMsg(
					'模块编辑成功',
					'back',
					array('list' => '返回模块列表','edit/id:'.$this->_http->getPost('mid') => '重新编辑该模块')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'模块编辑失败',
					'back'
		        );				
			}
		}
		//获取一级模块列表
		$id = $this->_http->get('id');
		$moduleRow = $this->_model->get_info($id);
		
		$this->_tpl->assign('pid', $moduleRow['pid']);
		$this->_tpl->assign('moduleRow', $moduleRow);
		$moduleArr = $this->_model->getModuleSelect();
		$this->_tpl->assign('moduleArr', $moduleArr);
		$this->_tpl->display('admin/module_add.php');
	}
	
	public function AjaxColAction(){
		echo $resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		exit;
	}

	public function CheckModAction(){
		$getData = $this->_http->getPost();
		$m_name = empty($getData['m_name'])?'':trim($getData['m_name']);
        $pid = empty($getData['pid']) ? 0 : intval($getData['pid']);
        $mid = empty($getData['mid']) ? 0 : intval($getData['mid']);
        if (!$m_name)
        {
            echo json_encode(true);
            exit;
        }
        if ($this->_model->unique($m_name, $pid, $mid))
        {
            echo json_encode(true);
        }
        else
        {
            echo json_encode(false);
        }
        exit;      		
	}
	
	// 删除模块
    public function DropAction(){
        $id = $this->_http->get('id');
    	if (!$id)
        {
            Custom_Common::showMsg("请您选择要删除的分类 ", 'back');
        }
        $ids = explode(',', $id);
        $sql = "select `m_name` from `module` where `mid` ". $this->_model->db_create_in(implode(',', $ids));
        $m_name = $this->_db->fetchCol($sql);
        $m_name = implode(', ', $m_name);
        
        foreach ($ids as $key=>$id){
            if($id){
            	$resultBack = $this->_model->module_drop($id);
            }
        }        
        $resultBack = $this->_model->module_drop($id);
        if($resultBack){
        	Custom_Common::showMsg("删除分类成功。 ", 'back',array('list' => '返回模块列表'));
        }   	
    }
}