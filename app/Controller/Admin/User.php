<?php
class Controller_Admin_User extends Controller_Admin_Abstract {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_User::getInstance();
		$this->_config = array (
								   array (
								      'name' => '商品认证',
								      'value' => 1,
								    ),
								    array (
								      'name' => '商品编辑',
								      'value' => 2,
								    ),
								    array (
								      'name' => '商品删除',
								      'value' => 3,
								    ),
								    array (
								      'name' => '券验证',
								      'value' => 4,
								    ),
								    array (
								      'name' => '编辑店铺',
								      'value' => 5,
								    ),
								  );
	}

	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);

		$page_str = '';
		$getData = $this->_http->getParams();

		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$users = $this->_model->getUserList($page);
		$page_info['item_count'] = $this->_model->getCount();

		if($page_str){
			$page_info['page_str'] = $page_str;
		}

		$this->_format_page($page_info);

		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('users', $users);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->display('admin/user_list.php');
	}
	
	public function userNoticeAction() {
		$page_str = '';
		$getData = $this->_http->getParams();
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}

		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getUserNoticeList($getData, $page_info);
		$page_info['item_count'] = $data['totalNum'];
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('data', $data['result']);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->display('admin/user_notice.php');
	}
	
	public function editAction() {		
		$uid = $this->_http->get('uid');
		$page = $this->_http->get('page');
		$row = $this->_model->getRowById($uid);
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->edit($getData, $row);
			if($result) {
				$content = "用户名：{$row['user_name']} 编辑成功，内容：" . serialize($getData);
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'edit');
				Custom_Common::showMsg(
					'用户编辑成功', 
					'', 
					array(
						'edit/uid:'. $getData['uid'] . '/page:' . $getData['page'] => '继续编辑用户',
						'list/page:' . $getData['page'] => '返回用户列表'
					)	
				);				
			}

		}
		
		$competenceArray = $this->_config;
		
		$this->_tpl->assign('competenceArray', $competenceArray);
		$this->_tpl->assign('competenceJSON', json_encode($competenceArray));
		
		//当用户是营业员时，获取用户的权限
		if($row['user_type'] == 3) {
			$competenceRow = $this->select("`user_id` = '{$row['user_id']}'", 'oto_user_shop_competence', '*', '', true);
			$shop_name = $this->getShopFieldById($competenceRow['shop_id'], 'shop_name');
			$this->_tpl->assign('shop_name', $shop_name);
			$this->_tpl->assign('userJSON', $competenceRow['competence']);
		}
		
		$regionArray = $this->getRegion();
		$userShopArray = $this->_model->getShopByUserId($uid);//根据用户名获取对应的店铺
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('uid', $uid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('userShopArray', $userShopArray);
		
		$this->_tpl->display('admin/user_edit.php');
	}
	/**
	 * 关联店铺
	 */
	public function userShopAction() {
		$uid = $this->_http->get('uid');
		$page = !$this->_http->get('page') ? 1 : $this->_http->get('page');
		$step = !$this->_http->get('step') ? 1 : $this->_http->get('step');
		$utype = !$this->_http->get('utype') ? 1 : $this->_http->get('utype');
		
		if($step > 1) {
			//根据用户名获取对应的店铺
			$userRelationShopArray = $this->_model->getRelationShopByUserId($uid);
			$this->_tpl->assign('userRelationShopArray', $userRelationShopArray);
		}
		
		$row = $this->_model->getRowById($uid);

		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('step', $step);
		$this->_tpl->assign('utype', $utype);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->display('admin/user_shop.php');
	}
	/**
	 * 保存关联店铺
	 */
	public function userShopSaveAction() {
		$uid = $this->_http->get('uid');
		$sid = $this->_http->get('sid');
		$utype = !$this->_http->get('utype') ? 1 : $this->_http->get('utype'); 
		$page = $this->_http->get('page');
		
		$sidArray = explode(',', $sid);
		
		$result = $this->_model->userShopSave($uid, $sidArray, $utype);
		
		if($result) {			
			//日志
			$fileName ='saveShop.log';
			$logPath = LOG_PATH . 'user/shop/';
			logLog($fileName, var_export($this->_userInfo, true) . var_export($_REQUEST, true), $logPath);
						
			exit('ok');
		}
	}
	/**
	 * 营业员权限管理
	 */
	public function userPurviewAction() {
		$uid = $this->_http->get('uid');
		$uname = $this->_http->get('uname');
		$page = $this->_http->get('page');

		//默认权限
		$competenceNewArray = array();	
		$competenceArray = $this->_config;
		
		foreach ($competenceArray as & $competence) {
			$competenceNewArray[$competence['value']] = $competence['name'];
		}
		
		$userRelationShopArray = $this->_model->getRelationShopByUserId($uid);
		foreach ($userRelationShopArray as & $userRelationRow) {
			$relationString = '';
			$relationStringArray = explode(',', $userRelationRow['competence']);
			foreach ($relationStringArray as $competence_id) {
				if($competence_id) {
					$relationString .= $competenceNewArray[$competence_id] . '，';
				}
			}
			$userRelationRow['purview'] = $relationString ? rtrim($relationString, '，') : '';
		}
		
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('uid', $uid);
		$this->_tpl->assign('uname', $uname);
		$this->_tpl->assign('userRelationShopArray', $userRelationShopArray);
		$this->_tpl->assign('competenceJSON', json_encode($competenceArray));
		$this->_tpl->display('admin/user_shop_purview.php');
	}
	/**
	 * 用户权限【营业员，店长，收银员】
	 */
	public function userRightsAction() {
		$getData = $this->_http->getParams();
		
		if($this->_http->isPost()) {
			$result = $this->_model->userRightsEdit($getData);
			if($result) {
				Custom_Common::showMsg(
					'用户权限编辑成功',
					'',
					array(
						"user-rights/uid:{$getData['uid']}/utype:{$getData['utype']}/uname:{$getData['uname']}/page:{$getData['page']}" => "继续编辑权限",
						'list/page:' . $getData['page'] => '返回用户列表'
					)
				);
			}
		}
		
		$row = $this->select_one("`user_id` = '{$getData['uid']}'", "oto_user_right");
		$this->_tpl->assign('request', $getData);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/user_shop_right.php');
	}
	/**
	 * 营业员店铺权限
	 */
	public function userPurviewSaveAction() {
		$uid = $this->_http->get('uid');
		$sid = $this->_http->get('sid');
		$purviewStr = $this->_http->get('purviewStr');
		
		if($uid && $sid && $purviewStr) {
			$updateResult = $this->_model->userPurviewSave($uid, $sid, $purviewStr);
			if($updateResult) {
				exit('ok');
			}
		}
	}
	
	//设置禁言
	public function setGagAction()
	{
		$user_id = $this->_http->get('user_id');
		if($user_id)
		{
			$time = time();
			$gag = $this->_http->getPost('gag');
			switch ($gag)
			{
				case '1':
					$gag_time = strtotime('+ 3day');
					break;
				case '2':
					$gag_time = strtotime('+ 1week');
					break;
				case '3':
					$gag_time = strtotime('+ 1month');
					break;
				case '4':
					$gag_time = strtotime('+ 6month');
					break;
			}
			$userAll = explode(',', $user_id);
				
			foreach($userAll as $user_id)
			{
				$afectedRows = $this->_db->update('oto_user', array('gag_time' => $gag_time), "`user_id` = {$user_id}");
				if($afectedRows)
				{
					$personalRow = $this->select("`user_id` = {$user_id}", "oto_user");
					$personalRow = array_shift($personalRow);
					$operat_info = "用户名：{$personalRow['user_name']} 用户ID：{$user_id} 被禁言 <br> 解禁时间：<span style='color:red'>".date('Y-m-d H:i:s', $gag_time)."</span>";
					Custom_Log::log($this->_userInfo['id'], $operat_info, $this->pmodule, $this->cmodule, 'gag', 'user', $user_id);
				}
			}
			echo 'ok';
			exit();
		}
	}
	//取消禁言
	public function setGagNoAction()
	{
		$user_id = $this->_http->get('user_id');
		if($user_id)
		{
			$userAll = explode(',', $user_id);
	
			foreach($userAll as $user_id)
			{
				$afectedRows = $this->_db->update('oto_user', array('gag_time' => 0), "`user_id` = {$user_id} and `gag_time` > '".REQUEST_TIME."'");
				if($afectedRows)
				{
					$personalRow = $this->select("`user_id` = {$user_id}", "oto_user");
					$personalRow = array_shift($personalRow);
					$operat_info = "用户名：{$personalRow['user_name']} 用户ID：{$user_id} 被取消禁言" ;
					Custom_Log::log($this->_userInfo['id'], $operat_info, $this->pmodule, $this->cmodule, 'gag', 'user', $user_id);
				}
			}
			echo 'ok';
			exit();
		}
	}
	//加黑名单
	public function plusBlacklistAction()
	{
		$user_id = $this->_http->get('user_id');
		if($user_id)
		{
			$userAll = explode(',', $user_id);
	
			foreach($userAll as $user_id)
			{
				$afectedRows = $this->_db->update('oto_user', array('user_status' => 1), "`user_id` = {$user_id} and `user_status` <> '1'");
				if($afectedRows)
				{
					$personalRow = $this->select("`user_id` = {$user_id}", "oto_user");
					$personalRow = array_shift($personalRow);
					$operat_info = "用户名：{$personalRow['user_name']} 用户ID：{$user_id} 被加入黑名单" ;
					Custom_Log::log($this->_userInfo['id'], $operat_info, $this->pmodule, $this->cmodule, 'black', 'user', $user_id);
					$this->_db->insert('oto_backlist',
							array(
									'user_id' => $user_id,
									'user_name' => $personalRow['user_name'],
									'type' => 2,
									'created' => REQUEST_TIME
							)
					);
				}
			}
			$data = $this->_db->fetchAll('select * from `oto_backlist` order by id desc');
			$this->_model->array_to_file($data, 'backlist');
			echo 'ok';
			exit();
		}
	}
	//加入白名单
	public function plusWhitelistAction()
	{
		$user_id = $this->_http->get('user_id');
		if($user_id)
		{
			$userAll = explode(',', $user_id);
	
			foreach($userAll as $user_id)
			{
				$afectedRows = $this->_db->update('oto_user', array('user_status' => '0'), "`user_id` = {$user_id} and `user_status` = '1'");
				if($afectedRows)
				{
					$personalRow = $this->select("`user_id` = {$user_id}", "oto_user");
					$personalRow = array_shift($personalRow);
					$operat_info = "用户名：{$personalRow['user_name']} 用户ID：{$user_id} 被取消黑名单" ;
					Custom_Log::log($this->_userInfo['id'], $operat_info, $this->pmodule, $this->cmodule, 'black', 'user', $user_id);
					$this->_db->delete('oto_backlist', "`user_id` = {$user_id} and `type` = '2'");
				}
			}
			$data = $this->_db->fetchAll('select * from `oto_backlist` order by id desc');
			$this->_model->array_to_file($data, 'backlist');
			echo 'ok';
			exit();
		}
	}
	
	// 封ip
	public function setIpAction() {
		$user_id = $this->_http->get('user_id');
		if($user_id) {
			$userAll = explode(',', $user_id);
			foreach ($userAll as $user_id) {
				$is_bandIp = $this->_db->fetchOne("select count(id) from `oto_backlist` where user_id = '{$user_id}' and type = '1'");
				if (!$is_bandIp) {
					$personalRow = $this->select("`user_id` = '{$user_id}'", "oto_user");
					$personalRow = array_shift($personalRow);
					$resultBack = $this->_db->insert('oto_backlist',
							array(
									'ip'        => $personalRow['lasted_login_ip'],
									'user_id'   => $user_id,
									'user_name' => $personalRow['user_name'],
									'type' => 1,
									'created' => REQUEST_TIME
							)
					);
					if ($resultBack) {
						$operat_info = "用户名：{$personalRow['user_name']} 用户ID：{$user_id} 被封了IP";
						Custom_Log::log($this->_userInfo['id'], $operat_info, $this->pmodule, $this->cmodule, 'ip', 'user', $user_id);
						$data = $this->_db->fetchAll('select * from `oto_backlist` order by id desc');
						$this->_model->array_to_file($data, 'backlist');
					}
				}
			}
			if ($resultBack) {
				echo 1;
			} else {
				echo 2;
			}
		}
	
	}
	
	// 解封IP
	public function unsetIpAction() {
		$user_id = $this->_http->get('user_id');
		if($user_id) {
			$userAll = explode(',', $user_id);
			$time = time();
			foreach ($userAll as $user_id){
				$is_black = $this->_db->fetchOne("select count(*) from `oto_backlist` where user_id = '{$user_id}' and type = '1'");
				if ($is_black) {
					$personalRow = $this->select("`user_id` = '{$user_id}'", "oto_user");
					$personalRow = array_shift($personalRow);
					$resultBack = $this->_db->delete('oto_backlist', "`user_id` = '{$user_id}' and `type` = '1'");
					if ($resultBack) {
						$operat_info = "用户名：{$personalRow['user_name']} 用户ID：{$user_id} 被解封IP" ;
						Custom_Log::log($this->_userInfo['id'], $operat_info, $this->pmodule, $this->cmodule, 'ip', 'user', $user_id);

					}
				}
			}
			
			$data = $this->_db->fetchAll('select * from `oto_backlist` order by id desc');
			$this->_model->array_to_file($data, 'backlist');
				
			if ($resultBack) {
				echo 1;
			} else {
				echo 2;
			}
		}
	}
	
	/**
	 * 用户审核
	 */
	public function auditAction() {
		$uid = $this->_http->get('uid');
		$page = $this->_http->get('page');
		$row = $this->_model->getRowById($uid);
	
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData, $row);
			if($result) {
				$content = "用户名：{$row['user_name']} 商户认证 " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				Custom_Common::showMsg($getData['audit_type'] == 1?'商户认证审核通过':'商户认证审核不通过', '', array('list/page:' . $getData['page'] => '返回用户列表'));
			}
		}
	
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('uid', $uid);
		$this->_tpl->assign('row', $row);
		$this->_tpl->display('admin/user_audit.php');
	}
	
	public function delShopAction() {
		$uid = $this->_http->get('uid');
		$sid = $this->_http->get('sid');
		
		$resultArray = $this->_model->delShop($uid, $sid);
		echo json_encode($resultArray);
	}
	
	public function getShopAction() {
		$uid = $this->_http->get('uid');
		$userShopArray = $this->_model->getRelationShopByUserId($uid);
		if(!empty($userShopArray) && is_array($userShopArray)) {
			echo json_encode($userShopArray);
		}
	}
	
	public function getRegionShopAction() {
		$region_id = intval($this->_http->get('id'));
		if($region_id) {
			echo json_encode($this->_model->getShopByRegionId($region_id));
		}
	}
	/**
	 * 根据店铺类型，分类列出  不同区域 不同分类的店铺
	 */
	public function getSelListAction() {
		$stype = $this->_http->get('stype');
		$region_id = intval($this->_http->get('region_id'));
		if($stype && $region_id) {
			echo json_encode($this->_model->getSelList($stype, $region_id));
			exit();
		}
	}
	
	public function getShopListAction() {
		$stype = $this->_http->get('stype');
		$region_id = intval($this->_http->get('region_id'));
		$related_id = intval($this->_http->get('related_id'));
		$sname = $this->_http->get('sname');
		echo json_encode($this->_model->getShopList($stype, $region_id, $related_id, $sname));
		exit();
	}
	/**
	 * 幸运星后台加减
	 */
	public function starEditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			
			$result = $this->_model->starEdit($getData);
			Custom_Common::showMsg(
				'幸运星编辑成功',
				'',
				array(
					'star-edit' => '继续调整幸运星',
					'list' => '返回用户列表'
				)
			);			
		}
		
		$this->_tpl->display('admin/user_star_edit.php');
	}
}