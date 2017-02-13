<?php 
class Controller_Admin_Manager extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct()
	{	
		parent::__construct();
		$this->_model = Model_Admin_Manager::getInstance();
	}
	
	public function listAction(){
		$getData = $this->_http->getParams();
        $page = !$getData['page'] ? 1 : intval($getData['page']);
        
        $page_str = '';      
        
        $this->_tpl->assign('query_fields', array(
            'userid' => '用户名'
        ));
        $this->_tpl->assign('sort_options', array(
            'logintime DESC' => '最后登录'
        ));
        
 		if(array_key_exists('field_name', $getData) && array_key_exists('field_value', $getData)){
 			if($getData['field_value']){
 				$page_str .= "field_name:{$getData['field_name']}/";
 				$page_str .= "field_value:{$getData['field_value']}/";
 			}
 		} 		
		if(array_key_exists('sort', $getData)){
 			$page_str .= "sort:{$getData['sort']}/";
 		}
		if(array_key_exists('order', $getData)){
 			$page_str .= "order:{$getData['order']}/";
 		}       
        $this->_model->setWhere($getData); //设置WEHRE
        $this->_model->setOrder($getData); //设置ORDER
        $page_info = $this->_get_page($page); 
        $users = $this->_model->getManagerList($page);
        $page_info['item_count'] = $this->_model->getCount();   //获取统计数据
        if($page_str){
        	$page_info['page_str'] = $page_str;        	
        }
        $this->_format_page($page_info);
        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('users', $users);
		$this->_tpl->display('admin/manager_list.php');
	}
	
	// 页面信息验证
	private function _check_post($postData, $password = true, $is_user = true){
		$msg = '';
		if($is_user && empty($postData['user_name'])){
			$msg .= "会员名称不能为空 <br>";	
		}
		
		if($password){
			if (empty($postData['password'])){
				$msg .= "密码不能为空 <br>";
			}
			
			if(preg_match("/[a-zA-Z0-9_]{6, 20}/", $postData['password'])){
				$msg .= "密码由数字字母和下划线组成，6-20个字符<br>";
			}
			
			if(!empty($postData['password']) && $postData['password'] != $postData['re_password']){
				$msg .= "两次密码不一致";
			}
		}
		
		if(isset($postData['gid']) && empty($postData['gid'])){
			$msg .= "请选择组<br>";	
		}
		
		return $msg;
		
	}
	
	// 添加管理员
	public function addAction(){
		if($this->_http->isPost()){
			
			$postData = $this->_http->getPost();
			$msg = $this->_check_post($postData);
			if(!empty($msg)){
		        Custom_Common::showMsg(
					$msg,
					'back',
					array('add' => '继续添加管理员', 'list' => '返回管理员列表')	        
		        );					
			}
			$insert_result = $this->_model->manager_insert($postData);
			if($insert_result == 'repeat'){
		        Custom_Common::showMsg(
					'用户名或者电子邮件重复',
					'back',
					array('add' => '继续添加管理员', 'list' => '返回管理员列表')	        
		        );					
			}
			
			if($insert_result){
		        Custom_Common::showMsg(
					'管理员添加成功',
					'back',
					array('add' => '继续添加管理员', 'list' => '返回管理员列表')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'管理员添加失败',
					'back'
		        );				
			}
		}
		$group_model = Model_Admin_Group::getInstance();		
		$groupArr = $group_model->getGroupList();
		$this->_tpl->assign('groupArr', $groupArr);
		$this->_tpl->display('admin/manager_add.php');
	}

	// 编辑管理员
	public function editAction(){

		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			$password = false;
			if(!empty($postData['password'])){
				$password = true;
			}
			$msg = $this->_check_post($postData, $password, false);
			if(!empty($msg)){
		        Custom_Common::showMsg(
					$msg,
					'back',
					array('add' => '继续添加管理员', 'list' => '返回管理员列表')	        
		        );					
			}			
			$insert_result = $this->_model->manager_insert($postData);
			if($insert_result){
		        Custom_Common::showMsg(
					'管理员编辑成功',
					'back',
					array('list' => '返回管理员列表','edit/id:'.$postData['user_id'] => '重新编辑该管理员')	        
		        );		
			}else{
		        Custom_Common::showMsg(
					'管理员编辑失败',
					'back'
		        );				
			}
		}
		//获取一级模块列表
		
		$id = $this->_http->get('id');
		$user = $this->select("`id` = {$id}", 'oto_admin', '*', '', true);
		$group_model = Model_Admin_Group::getInstance();		
		$groupArr = $group_model->getGroupList();
	
		$this->_tpl->assign('myUser', $user);
		$this->_tpl->assign('groupArr', $groupArr);
		$this->_tpl->display('admin/manager_add.php');
	}
	
	public function checkUserAction(){
		$getData = $this->_http->getPost();
		$user_name = empty($getData['user_name'])?'':trim($getData['user_name']);
        $id = empty($getData['id']) ? 0 : intval($getData['id']);
        if (!$user_name)
        {
            echo json_encode(true);
            exit;
        }
        if ($this->_model->unique($user_name, $id))
        {
            echo json_encode(true);
        }
        else
        {
            echo json_encode(false);
        }
        exit; 		
	}
	
	public function checkEmailAction(){
		$getData = $this->_http->getPost();
		$email = empty($getData['email'])?'':trim($getData['email']);
        $id = empty($getData['id']) ? 0 : intval($getData['id']);
        if (!$email)
        {
            echo json_encode(true);
            exit;
        }
        if ($this->_model->unique_email($email, $id))
        {
            echo json_encode(true);
        }
        else
        {
            echo json_encode(false);
        }
        exit; 		
	}
	/**
	 * 禁用操作
	 */
	public function disabledAction() {
		$id = $this->_http->get('id');
		if (!$id)
		{
			Custom_Common::showMsg("请您选择要禁用的管理员 ", 'back');
		}
		
		$result = $this->_model->manager_disabled($id);
		if($result){
			Custom_Common::showMsg("操作成功！。 ", 'back',array('list' => '返回管理员列表'));
		}
		
	}
	/**
	 * 删除管理员
	 */
    public function dropAction(){
        $id = $this->_http->get('id');
    	if (!$id)
        {
            Custom_Common::showMsg("请您选择要删除的管理员 ", 'back');
        }
        $ids = explode(',', $id);
        foreach ($ids as $key=>$id){
            if($id){
            	$resultBack = $this->_model->manager_drop($id);
            }
        }        
        $resultBack = $this->_model->manager_drop($id);
        if($resultBack){
        	Custom_Common::showMsg("删除管理员成功。 ", 'back',array('list' => '返回管理员列表'));
        }   	
    }	
}