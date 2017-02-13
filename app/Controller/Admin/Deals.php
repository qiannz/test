<?php
class Controller_Admin_Deals extends Controller_Admin_Abstract {
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Deals::getInstance();
	}
	
	public function listAction() {
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$this->_model->setWhere($getData); //设置WEHRE
		$this->_model->setOrder($getData); //设置order
		$page_info = $this->_get_page($page);
		$data = $this->_model->getList($page);
		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/deals_list.php');		
	}
	
	public function addEditAction() {
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$insertUpdateResult = $this->_model->addEdit($getData);
			if ($insertUpdateResult) {
				
				if(!$getData['deals_id']) {
					Custom_Common::showMsg(
						'特卖添加成功',
						'back',
						array(
							'add-edit' => '继续添加特卖',
							'list' => '返回特卖列表'
						)
					);
				} else {
					Custom_Common::showMsg(
						'特卖添加成功',
						'back',
						array(
							'add-edit/id:' . $getData['deals_id'] . '/page:' . $getData['page'] => '重新编辑',
							'list' => '返回特卖列表'
						)
					);					
				}
			}
		}
		
		$id = $this->_http->get('id');
		$page = !$this->_http->has('page') ? 1 : intval($this->_http->get('page'));
		if($id) {
			$row = $this->_model->getDealsRow($id);
			$this->_tpl->assign('row', $row);
			$this->_tpl->assign('deals_id', $id);
		}
		
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/deals_modi.php');
	}
	
	public function uploadAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(!$_FILES['uploadImg']){
				echo json_encode(array('msg'=>101)); exit;
			}
			$size = getimagesize($_FILES['uploadImg']['tmp_name']);
			$imgWidth  = $size[0];
			$imgHeight = $size[1];
	
			if($imgWidth != 640 || $imgHeight != 300){
				echo json_encode(array('msg'=>102)); exit;
			}
			$img_url = Custom_Upload::singleImgUpload($_FILES['uploadImg'],'deals');
			if(!$img_url){
				echo json_encode(array('msg'=>103));exit;
			}else{
				echo json_encode(array('msg'=>100 ,'img_url'=>$img_url , 'url' =>$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/deals/'.$img_url ));exit;
			}
		}
	}
	
	public function delAction() {
		$deals_id = $this->_http->get('id');
		$page = !$this->_http->has('page') ? 1 : intval($this->_http->get('page'));
		
		if (!$deals_id) {
			Custom_Common::showMsg("请您选择要删除的特卖 ", 'back');
		}
		$resultBack = $this->_model->del($deals_id);
		if ($resultBack) {
			Custom_Common::showMsg("删除特卖成功。 ", '', array('list/page:' . $page => '返回特卖列表'));
		}
	}
	
	// ajax编辑排序
	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			exit(json_encode(true));
		} else {
			exit(json_encode(false));
		}
	}	
}