<?php 
class Model_Admin_Manager extends Base {

    private static $_instance;
	private $_table = 'oto_admin';
	private $_where = '';
	private $_order = '';
	
    public static function getInstance() {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    // sql拼接
    public function setWhere($getData) {
 		$where = '';
 		if(!empty($getData)){
 			if(array_key_exists('field_name', $getData) && array_key_exists('field_value', $getData)){
 				$where .= "  and `{$getData['field_name']}` like '%{$getData['field_value']}%'";
 			}
 		}
 		$this->_where = $where; 	
    }
    
    // 排序设置
    public function setOrder($getData) {
 		$order = " order by id asc";
 		if(!empty($getData)){
 			if(array_key_exists('sort', $getData)){
 				$order = " order by {$getData['sort']} {$getData['order']}";
 			}
 		}
 		$this->_order = $order;
    }
    
    // 获取整数
    public function getCount() {
    	return $this->_db->fetchOne("select count(*) from `".$this->_table."` where 1=1".$this->_where);
    }
    
    // 管理员列表
 	public function getManagerList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
 		$sql = "select * from `".$this->_table."` where 1=1".$this->_where.$this->_order;
 		$users = $this->_db->limitQuery($sql, $start, $pagesize);
 		return $users?$users:array();		
 	}
   
 	// 新增管理员
    public function manager_insert($postData) {
    	$user_id = isset($postData['user_id'])?intval($postData['user_id']):0;
     	$user_name = isset($postData['user_name'])?trim($postData['user_name']):'';
		$password = $postData['password'];
		$re_password = $postData['re_password'];
		$gid = isset($postData['gid'])?intval($postData['gid']):'';
		$group_admin = intval($postData['group_admin']);
		
		$arr = array(
				'userid' => $user_name,
				'pwd' => md5($password),
				'gid' => $gid,
				'group_admin' => $group_admin,
				'role_id' => 2,
				'logintime' => REQUEST_TIME,
				'loginip' => CLIENT_IP
			);		
		if($user_id == 0){
			if(!$this->unique($user_name, $user_id)){
				return 'repeat';
			}
			$insert_id = $this->_db->insert($this->_table, $arr);
			return $insert_id?$insert_id:false; 
		}else{
			$arr = array(
				'pwd' => md5($password),
				'gid' => $gid,
				'group_admin' => $group_admin
			);			
			if(!$password) unset($arr['pwd']);
			$affected_rows = $this->_db->update($this->_table, $arr,"`id` = $user_id");
			return $affected_rows?$affected_rows:false;   					
		}   		
    }
    
    // 验证用户名是否存在
	public function unique($user_name, $user_id = 0) {
        $conditions = "`userid` = '{$user_name}'";
        $user_id && $conditions .= " AND `id` <> $user_id";
        
        $sql = "select count(*) from `".$this->_table."` where $conditions";
        return $this->_db->fetchOne($sql) == 0;
    }

    // 验证邮箱
	public function unique_email($email, $user_id = 0) {
        $conditions = "`email` = '{$email}'";
        $user_id && $conditions .= " AND `id` <> $user_id";
        
        $sql = "select count(*) from `".$this->_table."` where $conditions";
        return $this->_db->fetchOne($sql) == 0;
    }

    // 删除管理员
	public function manager_drop($id) {
		$delResult = $this->_db->delete($this->_table, "`id` = '{$id}' and `role_id` <> 1");				
		return $delResult;
	}
	
	public function manager_disabled($id) {
		$sql = "update `{$this->_table}` set is_disabled =  case when is_disabled = 0 then 1 else 0 end where id = '{$id}'";
		return $this->_db->query($sql);
	}
	
	public function getMangerByUserId($ad_id) {
		return $this->select_one("`id` = '{$ad_id}'", 'oto_admin');
	}
}