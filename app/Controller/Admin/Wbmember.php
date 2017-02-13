<?php
/*
 * 微商会员控制器类
 */
class Controller_Admin_Wbmember extends Controller_Admin_Abstract{
	private $_model;
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Wechatbusiness::getInstance();
	}
	
	//会员列表
	public function listAction(){
		$getData = $this->_http->getParams();
		$page = intval($getData["page"])<1?1:intval($getData["page"]);
		//翻页传递参数拼接
		$page_str = $this->getPageStr($getData);
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getMemberList($page);
		$page_info['item_count'] = $this->_model->getMemberCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/wbmember_list.php');
	}
	
	//添加会员
	public function addAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			if( mb_strlen($getData['apply_reason'], 'utf8') > 50 ){
				Custom_Common::showMsg(
					'会员新增失败，申请说明最多50个字符，汉字算一个字符',
					'back',
					array()
				);
			}
			$postResult = $this->_model->postUser($getData);
			Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'add', 'wbmember', $postResult['insert_id']);
			Custom_Common::showMsg(
				'会员新增成功',
				'back',
				array(
					'add' => '继续新增',
					'list' => '返回会员管理'
				)
			);
		}
		$this->_tpl->display('admin/wbmember_add.php');
	}
	
	//检查会员是否存在
	public function mobileIsExistAction(){
		$getData = $this->_http->getPost();
		$mobile = trim($getData['mobile']);
		$uid = intval($getData['uid']);
		$flag = $this->_model->checkMobileIsExist($mobile, $uid);
		echo $flag;
	}
	
	//编辑会员
	public function editAction() {
		$user_id = $this->_http->get('uid');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
	
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$postResult = $this->_model->postUser($getData);
			Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'edit', 'wbmember', $user_id);
			Custom_Common::showMsg(
			'会员编辑成功',
			'back',
			array(
			'edit/uid:' . $getData['uid'] . '/page:' . $getData['page'] => '继续编辑',
			'../wbmember/list/' . '/page:' . $getData['page'] => '返回会员管理'
			)
			);
		}
		$row = $this->_model->getMemberInfoByUid($user_id);
		$this->_tpl->assign("row",$row);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/wbmember_add.php');
	}
	
	//会员审核
	public function auditAction(){
		$getData = $this->_http->getParams();
		$flag = $this->_model->doAudit($getData);
		if( $flag ){
			Custom_Log::log($this->_userInfo['id'], "会员ID:{$getData['uid']},身份:{$getData['user_type']} 审核".($getData['user_status']==-1?"拒绝":"通过"), $this->pmodule, $this->cmodule, 'audit', 'wbmember', $getData['uid']);
		}
		echo (int)$flag;
	}
}