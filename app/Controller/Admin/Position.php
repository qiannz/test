<?php
class Controller_Admin_Position extends Controller_Admin_Abstract {
    private $_model;

	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Position::getInstance();
	}

	public function listAction() {
		$data = $this->_model->getPositionList();
		$row = array(0 => 0);
		$map = array();
		foreach ($data as $key => $pcategory)
		{
			$row[$pcategory['pos_id']] = $key + 1;
			$map[] = $row[$pcategory['pos_pid']];
		}
	
		$this->_tpl->assign('map', json_encode($map));
		$this->_tpl->assign('max_layer', 2);
	
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/position_list.php');
	}
	
	// 新增推荐位
	public function addAction() {
		if($this->_http->isPost()){
			$getData = $this->_http->getPost();
			$result = $this->_model->add($getData);
			if($result) {
				Custom_Log::log($this->_userInfo['id'], "新增推荐位  <b>{$getData['pos_name']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				$this->getTheRecommendedPosition(null, null, false);
				Custom_Common::showMsg(
					'推荐位添加成功',
					'back',
					array('add/pid:'. $getData['pid'] => '继续添加推荐位', 'list' => '返回推荐位列表')
				);
			}
		}
	
		$pid = $this->_http->get('pid');
		$this->_tpl->assign('pid', $pid);
		$this->_tpl->display('admin/position_modi.php');
	}
	
	// 编辑推荐位
	public function editAction() {
		if($this->_http->isPost()){
			$getData = $this->_http->getPost();
			$result = $this->_model->add($getData);
			if($result) {
				Custom_Log::log($this->_userInfo['id'], "编辑推荐位  <b>{$getData['pos_name']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
				$this->getTheRecommendedPosition(null, null, false);
				Custom_Common::showMsg(
					'推荐位编辑成功',
					'back',
					array('list' => '返回推荐位列表','edit/pid:'.$getData['pos_pid'].'/id:'.$getData['id'] => '重新编辑该推荐位')
				);
			}
		}
		$id = $this->_http->get('id');
		$pid = $this->_http->get('pid');
		$row = $this->_model->getPositionRow($id);
		// 获取一级分类
		$parentSortList = $this->_model->getParentSortList();
		$this->_tpl->assign('parentSortList', $parentSortList);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('pid', $pid);
		$this->_tpl->display('admin/position_modi.php');
	}
	
	// 删除推荐位
	public function delAction() {
		$id = $this->_http->get('id');
		if (!$id)
		{
			Custom_Common::showMsg("该选项不允许删除，请重新选择", 'back');
		}
		$ids = explode(',', $id);
		if(is_array($ids)) {
			foreach ($ids as $cid){
				if($cid){
					$this->_model->del($cid);
				}
			}
		} else {
			$this->_model->del($id);
		}
		$this->getTheRecommendedPosition(null, null, false);
		Custom_Common::showMsg("删除推荐位成功。 ", 'back',array('list' => '返回推荐位列表'));		
	}
	
	// 验证推荐位
	public function checkPlateAction() {
		$getData = $this->_http->getPost();
		$id = empty($getData['id']) ? 0 : intval($getData['id']);
		$identifier = empty($getData['identifier'])?'':trim($getData['identifier']);
		if (!$identifier)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->unique($identifier, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}
	
	// 验证推荐位名称
	public function checkNameAction() {
		$getData = $this->_http->getPost();
		$id = empty($getData['id']) ? 0 : intval($getData['id']);
		$name = empty($getData['pos_name'])?'':trim($getData['pos_name']);
		if (!$name)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->checkName($name,$id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}	

	
	// ajax编辑排序
	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			$this->getTheRecommendedPosition(null, null, false);
			exit(json_encode(true));
		} else {
			exit(json_encode(false));
		}
	}
}