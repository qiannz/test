<?php
/*
 * 宣传标语控制器类
 */
class Controller_Admin_Slogan extends Controller_Admin_Abstract{
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Slogan::getInstance();
	}
	
	public function listAction(){
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		//翻页传递参数拼接
		$page_str = $this->getPageStr($getData);
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getList($page);
		$page_info['item_count'] = $this->_model->getCount();
	
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
	
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('slogans', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/slogan_list.php');
	}
	
	public function addAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();	
			$slogan_id = $this->_model->postSlogan($getData);
	
			Custom_Log::log($this->_userInfo['id'], "宣传标语：{$getData['name']}, 宣传标语ID：{$slogan_id}", $this->pmodule, $this->cmodule, 'add', 'slogan', $slogan_id);
				
			Custom_Common::showMsg(
			'宣传标语新增成功',
			'back',
			array(
			'add' => '继续新增',
			'list' => '返回宣传标语管理'
			)
			);
		}
		$this->_tpl->display('admin/slogan_add.php');
	}
	
	public function editAction() {
		$sid = $this->_http->get('id');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$slogan_id = $this->_model->postSlogan($getData);
	
			Custom_Log::log($this->_userInfo['id'], "宣传标语：{$getData['name']}, 宣传标语ID：{$slogan_id}", $this->pmodule, $this->cmodule, 'edit', 'slogan', $slogan_id);

			Custom_Common::showMsg(
			'宣传标语编辑成功',
			'back',
			array(
			'edit/id:' . $slogan_id . '/page:' . $getData['page'] => '继续编辑',
			'list/' . '/page:' . $getData['page'] => '返回宣传标语管理'
			)
			);
		}
		$slogan_row = $this->_model->getSloganById($sid);
		$this->_tpl->assign("row",$slogan_row);
		$this->_tpl->assign("page",$page);
		$this->_tpl->display('admin/slogan_add.php');
	}
	
	public function delAction(){
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$result = $this->_model->changeSloganDelStat($ids, 1);
		if($result) {
			$content = "宣传标语IDS：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'slogan', $ids);
			Custom_Common::showMsg(
			'删除成功',
			'back'
			);
		}
	}
	
	public function unDelAction(){
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$result = $this->_model->changeSloganDelStat($ids,0);
		if($result) {
			$content = "宣传标语IDS：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'unDel', 'slogan', $ids);
			Custom_Common::showMsg('批量恢复成功', 'back', array('list' => '返回宣传标语管理'));
		}
	}
}