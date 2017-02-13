<?php
/*
 * 微商订单控制器类
 */
class Controller_Admin_Wborder extends Controller_Admin_Abstract{
	private $_model;
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Wechatbusiness::getInstance();
	}
	
	//商品订单列表
	public function listAction(){
		$getData = $this->_http->getParams();
		$page = intval($getData["page"])<1?1:intval($getData["page"]);
		//翻页传递参数拼接
		$page_str = $this->getPageStr($getData);
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getOrderList($page);
		$page_info['item_count'] = $this->_model->getOrderCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/wborder_list.php');
	}
	
	//商品订单添加
	public function addAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$order_id = intval($this->_http->get('order_id'));
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$postResult = $this->_model->postOrder($getData);
			if( intval($getData["order_id"]) ){
				Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'edit', 'wborder', $getData["order_id"]);
				Custom_Common::showMsg(
					'订单编辑成功',
					'back',
					array(
						'edit/order_id:' . $getData['order_id'] . '/page:' . $getData['page'] => '继续编辑',
						'../wborder/list/' . '/page:' . $getData['page'] => '返回订单管理'
					)
				);
			}else{
				Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'add', 'wborder', $postResult['insert_id']);
				Custom_Common::showMsg(
					'订单新增成功',
					'back',
					array(
						'add' => '继续新增',
						'list' => '返回订单管理'
					)
				);
			}
		}
		$order_id = intval($this->_http->get('order_id'));
		if( $order_id ){
			$order_info = $this->_model->getOrderInfo($order_id);
			$this->_tpl->assign("row",$order_info);
		}
		$this->_tpl->display('admin/wborder_add.php');
	}
	
	//获取会员信息
	public function getMemberAction(){
		$getData = $this->_http->getPost();
		$mobile = $getData["mobile"];
		$memberInfo = $this->_model->getMemberInfoByMobile($mobile);
		if( empty($memberInfo) ){
			exit('');
		}else{
			exit(json_encode($memberInfo));
		}
	}
	
	//获取合适的价格区间
	public function getSuitDiscountAction(){
		$getData = $this->_http->getPost();
		$total_price = $getData["total_price"];
		$discountInfo = $this->_model->getSuitableDiscount($total_price);
		exit(json_encode($discountInfo));
	}
	
	//单个订单记录删除
	public function delAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$ids = $this->_http->get('id');
		$result = $this->_model->doDelOrder($ids,1);
		if($result) {
			Custom_Log::log($this->_userInfo['id'], "订单ID:{$ids}删除成功", $this->pmodule, $this->cmodule, 'del', 'wborder', $ids);
			Custom_Common::showMsg(
			'删除成功',
			'',
			array(
			'/admin/wborder/list/page:' . $page => '返回订单管理'
			)
			);
		}
	}
	//批量删除
	public function delAllAction(){
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$ids = $this->_http->get('id');
		$result = $this->_model->doDelOrder($ids,1);
		if($result) {
			Custom_Log::log($this->_userInfo['id'], "订单IDs:{$ids}删除成功", $this->pmodule, $this->cmodule, 'delAll', 'wborder', $ids);
			Custom_Common::showMsg(
			'删除成功',
			'',
			array(
			'/admin/wborder/list/page:' . $page => '返回订单管理'
			)
			);
		}
	}
}