<?php
/*
 * 微商折扣控制器类
 */
class Controller_Admin_Wbdiscount extends Controller_Admin_Abstract{
	private $_model;
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Wechatbusiness::getInstance();
	}
	
	//折扣列表
	public function listAction(){
		$getData = $this->_http->getParams();
		$page = intval($getData["page"])<1?1:intval($getData["page"]);
		//翻页传递参数拼接
		$page_str = $this->getPageStr($getData);
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getDiscountList($page);
		$page_info['item_count'] = $this->_model->getDiscountCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/wbdiscount_list.php');
	}
	
	//设定折扣
	public function addAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$id = intval($this->_http->get('id'));
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			if( $getData['min_price'] >= $getData['max_price'] ){//起始价格不能大于结束价格
				Custom_Common::showMsg(
				'折扣设定失败，价格区间：'.$getData['min_price'].','.$getData['max_price'].'；起始价格不能大于结束价格',
				'back',
				array()
				);
			}
			$postResult = $this->_model->postDiscount($getData);
			
			Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'add', 'wbdiscount', $postResult['insert_id']);
			if( is_bool($postResult) ){
				Custom_Common::showMsg(
					'折扣设定失败，价格区间：'.$getData['min_price'].','.$getData['max_price'].'与已有的价格区间重叠',
					'back',
					array()
				);
			}else{
				Custom_Common::showMsg(
					'折扣设定成功',
					'back',
					array(
						'add' => '继续新增',
						'list' => '返回折扣管理'
					)
				);
			}
		}
		if( $id > 0 ){
			$data = $this->_model->getDiscountById($id);
			$this->_tpl->assign("data",$data);
		}
		$this->_tpl->display('admin/wbdiscount_add.php');
	}
	
	//单个折扣记录删除
	public function delAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$ids = $this->_http->get('id');
		$result = $this->_model->doDel($ids,1);
		if($result) {
			Custom_Log::log($this->_userInfo['id'], "折扣ID：{$ids}删除成功", $this->pmodule, $this->cmodule, 'del', 'wbdiscount', $ids);
			Custom_Common::showMsg(
			'删除成功',
			'',
			array(
			'/admin/wbdiscount/list/page:' . $page => '返回折扣管理'
			)
			);
		}
	}
	//批量删除
	public function delAllAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$ids = $this->_http->get('id');
		$result = $this->_model->doDel($ids,1);
		if($result) {
			Custom_Log::log($this->_userInfo['id'], "折扣IDs：{$ids}删除成功", $this->pmodule, $this->cmodule, 'delAll', 'wbdiscount', $ids);
			Custom_Common::showMsg(
			'删除成功',
			'',
			array(
			'/admin/wbdiscount/list/page:' . $page => '返回折扣管理'
			)
			);
		}
	}
}