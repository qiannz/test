<?php
class Controller_Admin_Privateletter extends Controller_Admin_Abstract {
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Privateletter::getInstance();
		//通知类型 user:个人交互|voucher:现金券|buygood:团购商品|system:系统通知|commodity:商城商品|Specialsale:特卖|discount:折扣|brand:品牌|shop:店铺|market:商场
		
		$this->_message_type = array(
				'0' => '全部',
				'voucher' => '现金券',
				'commodity' => '商城商品',
				'discount' => '折扣',
				'system' => '系统通知',
				'buygood' => '团购商品',
				'Specialsale' => '特卖',
				'user' => '个人交互',
				'brand' => '品牌',
				'shop'=> '店铺',
				'market'=> '市场'
				);
	}
	
	public function listAction(){
		
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		//翻页传递参数拼接
		$this->_model->setWhere($getData);
		$page_str = $this->getPageStr($getData);
		$page_info = $this->_get_page($page);
		$messages = $this->_model->getLetterList($page);
		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('message_type', $this->_message_type);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('messages', $messages);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/privateletter_list.php');
	}
	
	// 发送信息
	public function AddAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if($postData['send_type'] == 1){ // 点对点发送消息
				$message = '指定会员消息发送成功';
				if (!isset($postData['user_name']) || empty($postData['user_name']))
				{
					Custom_Common::showMsg(
					'请填写会员名',
					'back'
					);
				}
			}elseif($postData['send_type'] == 2){ // 发送给全部会员
				$message = '全部会员消息发送成功';
			}
			$result = $this->_model->letterAdd($postData);
			if($result){
				if($postData['send_type'] == 1) {
					$con = '发送消息给指定会员'.$postData['user_name'].'，';
					$con .= '内容：'.$postData['content'];
				}else{
					$con = '发送消息给全部会员，';
					$con .= '内容：'.$postData['content'];
				}
				Custom_Common::showMsg(
				$message,
				'back'
				);
	
			}else{
				Custom_Common::showMsg(
				'消息通知发送失败'
				);
			}
		}
		$moduleLocationList = include VAR_PATH . 'config/appLink.php';
		$this->_tpl->assign('send_type', array(
				1 => '指定会员',
				2 => '全部会员',
		));
		
		$this->_tpl->assign('moduleLocationList',$moduleLocationList);	
		$this->_tpl->assign('moduleLocationListJson', json_encode($moduleLocationList));
		$this->_tpl->display('admin/privateletter_add.php');
	}
	
	public function systemAction(){
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		$page_str = $this->getPageStr($getData);
		$page_info = $this->_get_page($page);
		$systems = $this->_model->getSystemList($getData, $page);
		$page_info['item_count'] = $this->_model->getSystemCount($getData);
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('systems', $systems);
		
		$this->_tpl->display('admin/privateletter_system.php');
	}
	
	// ajax编辑信息内容
	public function AjaxColAction(){
		$sysValue = $this->_db->fetchOne("select message from oto_pre_notice where message_id = '{$this->_http->getPost('id')}'");
		$cont = '编辑前系统信息是:'.$sysValue.'<br>';
		$cont .= '编辑后系统信息是:'.$this->_http->getPost('value');
		echo $resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		exit;
	}
}