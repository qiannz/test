<?php
class Controller_Admin_Nav extends Controller_Admin_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Nav::getInstance();
	}
	
	public function listAction() {
		$where = '';
		$getData = $this->_http->getParams();

		$data = $this->_model->getPositionList($getData);
		$row = array(0 => 0);
		$map = array();

		foreach ($data as $key => $pcategory)
		{
			$row[$pcategory['nav_id']] = $key + 1;
			$map[] = $row[$pcategory['nav_pid']];
			$positionRow = $this->_model->getPosition($pcategory['pos_id'], 'pos_name, identifier');
			$data[$key]['pos_name'] = $positionRow['pos_name'];
			$data[$key]['identifier'] = $positionRow['identifier'];
		}
		
		$this->_tpl->assign('map', json_encode($map));
		$this->_tpl->assign('max_layer', 2);

		//导航分类
		$posSortList = $this->getTheRecommendedPosition('nav', null, true, $this->_ad_city);
		$this->_tpl->assign('posSortList', $posSortList);
		
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->display('admin/nav_list.php');
	}
	
	// 新增推荐位
	public function addAction() {
		if($this->_http->isPost()){
			$getData = $this->_http->getPost();
			$result = $this->_model->add($getData);
			if($result) {
				$this->_model->getNavCache();
				Custom_Log::log($this->_userInfo['id'], "导航  <b>{$getData['nav_name']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
				'导航添加成功',
				'back',
				array('add/pid:' . $getData['pid'].'/psid:' . $getData['psid'] => '继续添加导航', 'list' => '返回导航列表')
				);
			}
		}
	
		$pid = $this->_http->get('pid');		
		$psid = $this->_http->get('psid');
		
		//导航分类
		$posSortList = $this->getTheRecommendedPosition('nav', null, true, $this->_ad_city);
		$this->_tpl->assign('posSortList', $posSortList);
		
		$this->_tpl->assign('pid', $pid);
		$this->_tpl->assign('psid', $psid);
		$this->_tpl->display('admin/nav_modi.php');
	}
	
	public function editAction() {
		if($this->_http->isPost()){
			$getData = $this->_http->getPost();
			$result = $this->_model->add($getData);
			if($result) {
				$this->_model->getNavCache();
				Custom_Log::log($this->_userInfo['id'], "编辑导航  <b>{$getData['nav_name']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
				Custom_Common::showMsg(
				'导航编辑成功',
				'back',
				array('list' => '返回导航列表','edit/pid:' . $getData['pid'] . '/psid:' . $getData['psid'] . '/id:' . $getData['id'] => '重新编辑该导航')
				);
			}
		}
		$id = $this->_http->get('id');
		$pid = $this->_http->get('pid');
		$psid = $this->_http->get('psid');
		
		$row = $this->_model->getNavRow($id);

		//导航分类
		$posSortList = $this->getTheRecommendedPosition('nav', null, true, $this->_ad_city);
		$this->_tpl->assign('posSortList', $posSortList);
		
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('pid', $pid);
		$this->_tpl->assign('psid', $psid);
		$this->_tpl->display('admin/nav_modi.php');
	}

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
		$this->_model->getNavCache();
		Custom_Common::showMsg("删除导航成功。 ", 'back',array('list' => '返回导航列表'));
	}
	
	public function checkNameAction() {
		$getData = $this->_http->getPost();
		$id = empty($getData['id']) ? 0 : intval($getData['id']);
		$pos_id = empty($getData['pos_id']) ? 0 : intval($getData['pos_id']);
		$name = empty($getData['nav_name'])?'':trim($getData['nav_name']);
		if (!$name || !$pos_id)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->checkName($name, $pos_id, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}

	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			exit(json_encode(true));
		} else {
			exit(json_encode(false));
		}
	}
}