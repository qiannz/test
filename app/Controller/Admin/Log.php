<?php
class Controller_Admin_Log extends Controller_Admin_Abstract {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Log::getInstance();
	}
	
	public function listAction() {
		$type = $this->_http->get('type');
		$id = $this->_http->get('id');
		
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		
		$activity = array(
					''    		=> '全部',
    				'add' 		=> '添加',
    				'del' 		=> '删除',
    				'delAll' 	=> '批量删除',
    				'unDel' 	=> '取消删除',
    				'edit' 		=> '编辑',
    				'mod' 		=> '修改',
    				'audit' 	=> '审核',
    				'gag' 		=> '禁言',
    				'black' 	=> '黑名单',
    				'ip' 		=> '封IP',
    				'recommend' => '推荐',
    				'top' 		=> '置顶',
    				'merge' 	=> '店铺合并',
					'export' 	=> '券导出',
                    'loans' 	=> '放款',
                    'awards'	=> '发奖',
        );
		$this->_tpl->assign('activity_fields', $activity);
		
		$getData = $this->_http->getParams();
		
		if ($type && $id) {
			if ($type == 'shop') {
				$sname = $this->_db->fetchOne("select shop_name from oto_shop where shop_id = '{$id}'");
 				$page_str .= "field_value:{$sname}/";
				$this->_tpl->assign('name', $sname);
			} else if ($type == 'user') {
				$uname = $this->_db->fetchOne("select user_name from oto_user where user_id = '{$id}'");
 				$page_str .= "field_value:{$uname}/";
				$this->_tpl->assign('name', $uname);
			} else if ($type == 'ticket') {
				$tname = $this->_db->fetchOne("select ticket_title from oto_ticket where ticket_id = '{$id}'");
 				$page_str .= "field_value:{$tname}/";
				$this->_tpl->assign('name', $tname);
			} else if($type == 'discount'){
				$dname = $this->_db->fetchOne("select title from discount_content where discount_id ='{$id}'");
				$page_str .= "field_value:{$tname}/";
				$this->_tpl->assign('name', $dname);
			} else if($type == 'special'){
				$sname = $this->_db->fetchOne("select title from special_content where special_id='{$id}'");
				$page_str .= "field_value:{$tname}/";
				$this->_tpl->assign('name',$tname);
			}
			$logInfo = $this->_model->getLogInfo($type, $id);
			if( !empty($logInfo) ){
				$getData['pmodule'] = $_REQUEST['pmodule'] = $logInfo['pmodule'];
				$getData['cmodule'] = $_REQUEST['cmodule'] = $logInfo['cmodule'];
			}
		}
		
		if(array_key_exists('pmodule', $getData)){
			if($getData['pmodule']){
				$page_str .= "pmodule:{$getData['pmodule']}/";
			}
		}
			
		if(array_key_exists('cmodule', $getData)){
			if($getData['cmodule']){
				$page_str .= "cmodule:{$getData['cmodule']}/";
			}
		}
		
		if(array_key_exists('activity', $getData)){
			if($getData['activity']){
				$page_str .= "activity:{$getData['activity']}/";
			}
		}

  		if(array_key_exists('field_value', $getData) && $getData['field_value'] ){
 			$page_str .= "field_value:{$getData['field_value']}/";
 		} 

		$this->_model->setWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$logs = $this->_model->getLogList($page, PAGESIZE, $type, $id);
		foreach ($logs as &$row) {
			$row['activity_name'] = $activity[$row['activity']];
		}
		$page_info['item_count'] = $this->_model->getCount($type, $id);
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$pmodule = $this->_model->getPModel();
		$cmodule = $this->_model->getCModel($getData['pmodule']);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('logs', $logs);
		$this->_tpl->assign('pmodule', $pmodule);
		$this->_tpl->assign('cmodule', $cmodule);
		$this->_tpl->assign('type', $type);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/log_list.php');
	}
	
	public function getCmoduleAction() {
		$pmodule = $this->_http->get('id');
		echo json_encode($this->_model->getCModel($pmodule));
	}
}