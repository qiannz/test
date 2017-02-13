<?php
class Controller_Admin_Recommend extends Controller_Admin_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Recommend::getInstance();

		$this->moduleLocationList = include VAR_PATH . 'config/appLink.php';
	}
	
	public function listAction() {
		
		$page = $this->_http->getParam('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();
		
		if(array_key_exists('title', $getData)){
			if($getData['title']){
				$page_str .= "title:{$getData['title']}/";
			}
		}
			
		if(array_key_exists('pos_id', $getData)){
			if($getData['pos_id']){
				$page_str .= "pos_id:{$getData['pos_id']}/";
			}
		}
			
		//获取推荐位
		$position = $this->getTheRecommendedPosition(null, null, true, $this->_ad_city);
		
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
		$recommend = $this->_model->getList($page);

		$page_info['item_count'] = $this->_model->getCount();
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('recommend', $recommend);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('position', $position);
		
		$this->_tpl->display('admin/recommend_list.php');
	}
	
	public function addAction() {
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$positionRow = $this->select('pos_id = ' .  $getData['pos_id'], 'oto_position', '*', '', true);
			if($_FILES['uploadFile']['error'] == 0) {
				$imageInfo = getimagesize($_FILES['uploadFile']['tmp_name']);
				if ($positionRow['width'] && $positionRow['width'] != $imageInfo[0] || ($positionRow['height'] && $positionRow['height'] != $imageInfo[1]) ) {
					Custom_Common::showMsg(
						'<span style="color:red">图片尺寸不符合，请重新选择</span>',
						'back',
						array(
							'add' => '重新选择'
						)
					);
				}
				$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
			} else {
				if($positionRow['width']) {
					Custom_Common::showMsg(
						'<span style="color:red">请上传图片</span>',
						'back',
						array(
							'add' => '重新选择'
						)
					);
				}
			}
			$getData['img_url'] = $img_url ? $img_url : '';		
			$insert_result = $this->_model->recommendEdit($getData);
			if($insert_result){
				Custom_Log::log($this->_userInfo['id'], "新增了商品名为  <b>{$getData['title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
					'新增成功',
					'',
					array('add/pos_id:' . $getData['pos_id'] => '继续添加', 'list/pos_id:' . $getData['pos_id'] => '返回推荐列表')
				);
			}else{
				Custom_Common::showMsg(
					'新增失败',
					'back'
				);
			}
		}
		
		$position = $this->getTheRecommendedPosition(null, null, true, $this->_ad_city);
		
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('moduleLocationList', $this->moduleLocationList);
		$this->_tpl->assign('moduleLocationListJson', json_encode($this->moduleLocationList));
		$this->_tpl->display('admin/recommend_edit.php');
	}
	
	public function editAction() {
		$id = $this->_http->get('id');	
		$recommend = $this->select("recommend_id = '{$id}'", 'oto_recommend', '*', '', true);
		
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			if($_FILES['uploadFile']['error'] == 0) {
				$positionRow = $this->select('pos_id = ' .  $getData['pos_id'], 'oto_position', '*', '', true);
				$imageInfo = getimagesize($_FILES['uploadFile']['tmp_name']);
				if ($positionRow['width'] && $positionRow['width'] != $imageInfo[0] || ($positionRow['height'] && $positionRow['height'] != $imageInfo[1]) ) {
					Custom_Common::showMsg(
						'<span style="color:red">图片尺寸不符合，请重新选择</span>',
						'back',
						array(
							'edit/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
				$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
			} else {
				if($positionRow['width']) {
					Custom_Common::showMsg(
						'<span style="color:red">请上传图片</span>',
						'back',
						array(
							'edit/id:' . $getData['id'] . '/pos_id:' . $getData['pos_id'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			}
			
			$getData['img_url'] = $img_url ? $img_url : $recommend['img_url'];
			$update_result = $this->_model->recommendEdit($getData);
			if ($update_result) {
				Custom_Log::log($this->_userInfo['id'], "编辑了商品名为  <b>{$getData['title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'edit');
				Custom_Common::showMsg(
					'编辑成功',
					'back',
					array(
						'list/pos_id:' . $getData['pos_id'] . '/page:' . $getData['page'] => '返回推荐管理列表', 'edit/id:'.$getData['id'] . '/page:' . $getData['page'] => '重新编辑'
					)
				);
			}else{
				Custom_Common::showMsg(
					'编辑失败',
					'back'
				);
			}			
			
		}
		
		$position = $this->getTheRecommendedPosition(null, null, true, $this->_ad_city);
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('recommend', $recommend);
		$this->_tpl->assign('moduleLocationList', $this->moduleLocationList);
		$this->_tpl->assign('moduleLocationListJson', json_encode($this->moduleLocationList));
		$this->_tpl->display('admin/recommend_edit.php');
	}
	
	
	public function delAction() {
		$id = $this->_http->get('id');
		$recommend = $this->select("recommend_id = '{$id}'", 'oto_recommend', '*', '', true);
		$result = $this->_model->del($id);
		if ($result){
			unlink(ROOT_PATH.'web/data/recommend/'.$recommend['img_url']);
			$postionRow = $this->getPosition("`pos_id` = '{$recommend['pos_id']}'");
			$postionRow = array_shift($postionRow);
			Custom_Log::log($this->_userInfo['id'], "删除商品名为  <b>{$recommend['title']}</b>【{$postionRow['pos_name']}】 的推荐", $this->pmodule, $this->cmodule, 'del');
			Custom_Common::showMsg("删除推荐内容成功。 ", 'back',array('list' => '返回推荐管理列表'));
		}
	}
	
	// ajax编辑排序
	public function ajaxColAction(){
		if($this->_model->ajax_module_edit($this->_http->getPost())) {
			echo json_encode(true);
			exit;
		}
	}
}