<?php
class Controller_Admin_Active extends Controller_Admin_Abstract {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Active::getInstance();
	}
	
	// 活动列表
	public function listAction(){
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		if(array_key_exists('act_name', $getData)){
			if($getData['act_name']){
				$page_str .= "act_name:{$getData['act_name']}/";
			}
		}
			
		$this->_model->setWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$activeList = $this->_model->getActiveList($page);
		foreach ($activeList as &$row) {
			$row['attend_num'] = $this->_db->fetchOne("select count(*) from act_mobile where act_id = '{$row['act_id']}'");
			$row['shareNum'] = $this->_db->fetchOne("select count(*) from act_share where act_id = '{$row['act_id']}'");
		}
		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('activeList', $activeList);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/act_list.php');
	}
	
	// 新增活动
	public function addAction() {
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$insert_result = $this->_model->actModi($postData);
			if ($insert_result) {
				Custom_Log::log($this->_userInfo['id'], "新增活动  <b>{$postData['act_name']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
				'活动添加成功',
				'back',
				array('add' => '继续添加活动', 'list' => '返回活动列表')
				);
			} else {
				Custom_Common::showMsg(
				'活动添加失败',
				'back'
				);
			}
		}
		$this->_tpl->display('admin/act_modi.php');
	}
	
	// 编辑活动
	public function editAction() {
		$act_id = $this->_http->get('act_id');
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$update_result = $this->_model->actModi($postData);
			if ($update_result) {
				Custom_Log::log($this->_userInfo['id'], "编辑活动  <b>{$postData['act_name']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
				Custom_Common::showMsg(
				'活动编辑成功',
				'back',
				array('list' => '返回活动列表','edit/act_id:'.$postData['act_id'] => '重新编辑该活动')
				);
			} else {
				Custom_Common::showMsg(
				'活动编辑失败',
				'back'
				);
			}
		}
		
		$actRows = $this->select("`act_id` = '{$act_id}'", 'act_active', '*', '', true);
		$this->_tpl->assign('actRow', $actRows);
		$this->_tpl->display('admin/act_modi.php');
	}
	
	// 删除活动
	public function delAction() {
		$act_id = $this->_http->get('act_id');
		if (!$act_id) {
			Custom_Common::showMsg("请您选择要删除的活动 ", 'back');
		}
		$act_name = $this->_db->fetchOne("select act_name from act_active where act_id = '{$act_id}'");
		$resultBack = $this->_model->del($act_id);
		if ($resultBack) {
			Custom_Log::log($this->_userInfo['id'], "删除  <b>{$act_name}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			Custom_Common::showMsg("删除活动成功。 ", 'back',array('list' => '返回活动列表'));
		}
	}
	
	// 参与者列表
	public function attendListAction() {
		$act_id = $this->_http->getParam('act_id');
		if (empty($act_id)) {
			$act_id = $this->_http->getPost('act_id');
		}
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$page_str .= 'act_id:'.$act_id.'/';
		$getData = $this->_http->getParams();
		if(array_key_exists('mobile', $getData)){
			if($getData['mobile']){
				$page_str .= "mobile:{$getData['mobile']}/";
			}
		}
			
		$this->_model->setAttendWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$attendList = $this->_model->getAttendList($page, $act_id);
		foreach ($attendList as &$row) {
			$shareNum = $this->_db->fetchOne("select count(*) from act_share where act_id = '{$row['act_id']}' and mobile = '{$row['phone']}'");
			$row['shareNum'] = $shareNum;
		}
		$page_info['item_count'] = $this->_model->getAttendCount($act_id);
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('attendList', $attendList);
		$this->_tpl->assign('act_id', $act_id);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/act_attend_list.php');
	}
	
	// 分享者名单
	public function shareListAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		
		$act_id = $this->_http->get('act_id');
		$mobile = $this->_http->get('mobile');
		
		$page_str = '';
		$getData = $this->_http->getParams();
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
				
		$this->_model->setShareWhere($getData); //设置WEHRE
		$page_info = $this->_get_page($page);
		$shareList = $this->_model->getShareList($page, $act_id, $mobile);
		$page_info['item_count'] = $this->_model->getShareCount($act_id, $mobile);
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('shareList', $shareList);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('act_id', $act_id);
		$this->_tpl->assign('mobile', $mobile);
		$this->_tpl->display('admin/act_share_list.php');
	}
	
	// 验证活动标记是否重复
	public function martCheckAction() {
		$getData = $this->_http->getPost();
		$act_mart = empty($getData['act_mart'])?'':trim($getData['act_mart']);
		$act_id = empty($getData['act_id']) ? 0 : intval($getData['act_id']);
		$res = $this->_model->check_mart($act_mart, $act_id);
		if($res == 0){
			echo json_encode(true);
			exit;
		}
		exit;
	}
	
	// 导出50元中奖人名单
	public function exportAction() {
		// 中奖人名单
		$act_id = $this->_http->get('act_id');
 		$share_num = $this->_http->get('share_num');
 		$title = $this->_http->get('act_mart');		
		$sql = "select count(id) as num, mobile, nick_name, act_id from act_share where act_id = '{$act_id}' and customer_phone = '' GROUP BY mobile HAVING num >= '{$share_num}'";
		$mobileAll = $this->_db->fetchAll($sql);
		$phoneArray = array();
		foreach ($mobileAll as &$mobileItem) {
			$sql = "select count(*) from (
				select * from act_share where mobile = '{$mobileItem['mobile']}' GROUP BY nick_name
				) A";
			$countNum = $this->_db->fetchOne($sql);
			if($countNum >= $share_num) {
				$mobileItem['act_content'] = $this->_db->fetchOne("select act_content from act_active where act_id = '{$mobileItem['act_id']}'");
				$phoneArray[] = array ($mobileItem['mobile'], '' , $mobileItem['act_content'], '');
			}
		}
		Custom_Export::export($phoneArray, $title);
	}
	
	// 导出10元中奖人名单
	public function exportSecondAction() {
		// 中奖人名单
		$act_id = $this->_http->get('act_id');
		$act_content = $this->_db->fetchOne("select act_content from act_active where act_id = '{$act_id}'");
		$share_num = $this->_http->get('share_num');
		$title = $this->_http->get('act_mart');
		$sql = "select count(id) as num, customer_phone, nick_name, act_id from act_share where act_id = '{$act_id}' and customer_phone <> '' GROUP BY customer_phone order by created";
		$mobileAll = $this->_db->fetchAll($sql);
		$phoneArray = array();
		foreach ($mobileAll as &$mobileItem) {		
			$phoneArray[] = array ($mobileItem['customer_phone'], '' , $act_content, '');
		}
		Custom_Export::export($phoneArray, $title);
	}
	
	public function verifyAction() {
		$act_id = $this->_http->get('act_id');
		$act_name = $this->_http->get('act_name');
		$winning = $this->_http->get('winning') ? $this->_http->get('winning') : 1;
		
		$this->_tpl->assign('act_id', $act_id);
		$this->_tpl->assign('act_name', $act_name);
		$this->_tpl->assign('winning', $winning);
		$this->_tpl->display('admin/act_verify.php');
	}
	
	/**
	 * 查询获奖名单
	 */
	public function queryAction() {
		$act_id = $this->_http->get('act_id');
		$winning = $this->_http->get('winning');
		$mobile = $this->_http->get('mobile');
		if($winning == 1) { //50元
			$winningArray = $this->_model->getFiftyWinning($act_id);
			if(array_search($mobile, $winningArray) !== false) {
				$sql = "select * from `act_mobile` where `phone` = '{$mobile}' limit 1";
				$phoneRow = $this->_db->fetchRow($sql);
				if($phoneRow['had_received'] == 1) {
					_exit('你已经兑换过奖励', 300);
				} else {
					_exit('可以兑奖', 100, $mobile);
				}
			} else {
				_exit('你输入的手机号码不在中奖名单中', 300);
			}
		}
		elseif($winning == 2) //10元
		{
			$sql = "select 1 from `act_share` where `act_id` = '{$act_id}' and  `customer_phone` = '{$mobile}' limit 1";
			if(! $this->_db->fetchOne($sql) == 1) {
				_exit('你输入的手机号码不在中奖名单中', 300);
			}
			
			$winningNum = $this->_model->getTenWinning($act_id, $mobile);
			if($winningNum) {
				_exit('可以兑奖', 100, $mobile);
			} else {
				_exit('你输入的手机号码已经兑奖', 300);
			}
		}
	
	}
	
	public function convertAction() {
		$act_id = $this->_http->get('act_id');
		$winning = $this->_http->get('winning');
		$mobile = $this->_http->get('mobile');
		
		if($winning == 1) {
			$sql = "update act_mobile set `had_received` = '1' where `had_received` = '0' and `phone` = '{$mobile}' limit 1";
			if($this->_db->query($sql)) {
				exit('ok');
			}
		}
		elseif($winning == 2) {
			$sql = "update act_share set `had_received` = '1' where `customer_phone` = '{$mobile}'";
			if($this->_db->query($sql)) {
				exit('ok');
			}			
		}
		
		
	}
}