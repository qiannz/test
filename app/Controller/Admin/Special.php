<?php
/*
 * 折扣控制器类（社交）
 */
class Controller_Admin_Special extends Controller_Admin_Abstract{
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Special::getInstance();
	}
	
	//专题列表
	public function listAction(){
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
			
		$data = $this->_model->getList($page);
		$page_info['item_count'] = $this->_model->getCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/special_list.php');
	}
	
	public function addEditAction() {
		$special_id = $this->_http->get('sid');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$user_id = $this->getUserIdByUserName(DEFINED_USER_NAME);
			if($_FILES['cover_img']['error'] == 0) {
				$imgInfo = getimagesize($_FILES['cover_img']['tmp_name']);
				if ($imgInfo['0'] != 640 || $imgInfo['1'] != 240) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 640 × 240 的图片'."\r\n";
			
					Custom_Common::showMsg(
					'<span style="color:red">'.$errMsg.'</span>',
					'back',
					array(
					'list/page:' . $getData['page'] => '返回专题列表'
					)
					);
				} else {
					$cover_img = Custom_Upload::singleImgUpload($_FILES['cover_img'], 'cover');
					$getData['cover_img'] = $cover_img;
				}
			}
			$wap_img_url = array();
			if(!empty($_FILES['img_url'])) {
				foreach($_FILES['img_url']['tmp_name'] as $_key => $_value) {
					$wap_img_url[] = Custom_Upload::singleImgUpload(
							array(
									'name' => $_FILES['img_url']['name'][$_key],
									'type' => $_FILES['img_url']['type'][$_key],
									'tmp_name' => $_value,
									'error' => $_FILES['img_url']['error'][$_key],
									'size' => $_FILES['img_url']['size'][$_key]
							), 'special');
						
				}
			}
			
			$postResult = $this->_model->postSpecial($getData);
			if($getData['sid']) {
				//WAP图片上传
				$this->_model->wapUploadImg($wap_img_url, $getData['sid'],  $user_id);
				
				Custom_Log::log($this->_userInfo['id'], "专题：{$getData['title']}, 专题ID：{$postResult['insert_id']}", $this->pmodule, $this->cmodule, 'edit', 'special', $postResult['insert_id']);
				Custom_Common::showMsg(
					'专题编辑成功',
					'back',
					array(
						'add-edit/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '继续编辑',
						'list/page:' . $getData['page'] => '返回专题管理'
					)
				);
			} else {
				//WAP图片上传
				$this->_model->wapUploadImg($wap_img_url, $postResult['insert_id'],  $user_id);
				
				Custom_Log::log($this->_userInfo['id'], "专题：{$getData['title']}, 专题ID：{$postResult['insert_id']}", $this->pmodule, $this->cmodule, 'add', 'special', $postResult['insert_id']);
				Custom_Common::showMsg(
					'专题新增成功',
					'back',
					array(
						'add' => '继续新增',
						'list' => '返回专题管理'
					)
				);
			}
		}

		if($special_id) {
			$row = $this->_model->getSpecialRow($special_id);
			$this->_tpl->assign("row",$row);
			//wap图片
			//$wapImgList = $this->_model->getWapImgList($special_id);
			//$this->_tpl->assign("wapImgList",$wapImgList);
			
			$goodArray = $this->_model->getGoodListById($special_id);
			$this->_tpl->assign("goodArray",$goodArray);
		}
		
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/special_add.php');
	}
	
	// 上传图片
	public function uploadAction() {
		if($this->_http->isPost()){
			$user_name = $this->_http->has('user_name') ? $this->_http->get('user_name') : DEFINED_USER_NAME;
			$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'good';
			$primary_id = $this->_http->has('primary_id') ? intval($this->_http->get('primary_id')) : 0;
			$user_id = $this->getUserIdByUserName($user_name);
			$filePath = Custom_Upload::singleImgUpload($_FILES['uploadFile'], $folder);
			if($filePath){
				$arr = array(
						'special_id'   => $primary_id,
						'user_id'  => $user_id,
						'img_url'  => $filePath,
						'created' => REQUEST_TIME
				);
				$aid = $this->_db->insert('special_img', $arr);
				$picArr = array(
						'error' => 0,
						'data' => array(
								array(
										'aid' => $aid,
										'img_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL']."/api/good/get-special-img-thumb/iid/{$aid}/type/special",
										'gid' => $primary_id
								)
						)
				);
				exit(json_encode($picArr));
			}
			exit(json_encode(array('error' => 1)));
		}
	}
	
	//删除图片
	public function delImgAction() {
		$aid = intval($this->_http->get('aid'));
		$count = $this->_db->fetchOne("SELECT COUNT(*) FROM `special_img` WHERE `special_id` != 0 AND `id`='{$aid}'");
		if( $count > 0 ){
			$this->_db->query("UPDATE `special_img` SET `special_id` = 0 WHERE `id`='{$aid}'");
		}
		echo json_encode(array('status' => 'ok'));
	}
	
	//修改图片的序号
	public function imgAjaxColAction(){
		$getData = $this->_http->getPost();
		$this->_model->img_ajax_edit($getData);
	}
	
	//wap图片删除
	public function wapImgDelAction(){
		$id = intval($this->_http->get("id"));
		if( $id ){
			$wapImgRow = $this->select("`id` = '{$id}'", 'special_wap_img', '*', '', true);
			if( !empty($wapImgRow) && $this->_db->delete('special_wap_img', '`id` = ' . $id) ){
				unlink(ROOT_PATH . 'web/data/special/' . $wapImgRow['img_url']);
				unlink(ROOT_PATH . 'web/data/buy/special/' . $wapImgRow['img_url']);
			}else{
				exit(json_encode(array('status' => 'fail')));
			}
		}
		exit(json_encode(array('status' => 'ok')));
	}

	
	/**
	 * 商品搜索
	 */
	public function getGoodListAction() {
		$filter = Custom_String::HtmlReplace( $this->_http->get("filter"), 1 );
		$data = $this->_model->getGoodList($filter, $this->_ad_city);
		exit(json_encode($data));
	}
	
	//删除
	public function delAction(){
		$id = $this->_http->get('id');
		$page = $this->_http->get('page');
		$result = $this->_model->del($id);
		$row = $this->_model->getSpecialRow($id);
		if($result) {
			$content = "专题标题：{$row['title']} 　专题ID：{$id}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'special', $id);
			Custom_Common::showMsg('删除成功', '/admin/special/list/page:' . $page);
		}
	}
	
	//批量删除
	public function delAllAction(){
		$ids = $this->_http->get('id');
		$result = $this->_model->del($ids);
		if($result) {
			$content = "专题IDS：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'special', $ids);
			Custom_Common::showMsg('批量删除成功', 'back');
		}
	}
	
	//专题恢复删除
	public function unDelAction() {
		$ids = $this->_http->get('id');
		$result = $this->_model->unDel($ids);
		if($result) {
			$content = "专题ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'unDel', 'special', $ids);
			Custom_Common::showMsg('专题批量恢复成功', '', array('list' => '返回专题列表'));
		}
	}
	
	//删除关联商品
	public function goodDelAction(){
		$special_id = intval($this->_http->get("sid"));
		$good_id = intval($this->_http->get("gid"));
		$this->_db->delete('special_good', array('special_id' => $special_id,"good_id"=>$good_id));
		exit(json_encode(array('status' => 'ok')));
	}
	/**
	 * 推荐专题
	 */
	public function recommendAction() {
		$sid = $this->_http->get('sid');
		$specialRow = $this->_model->getSpecialRow($sid);
	
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = $this->_model->checkRecommend($getData['sid'], $getData['pos_id']);
			if($checkRepeat){
				Custom_Common::showMsg(
					'<span style="color:red">当前专题在此推荐位重复，请重新选择推荐位</span>',
					'recommend/sid:' . $getData['sid'] . '/page:' . $getData['page']
				);
			}
			$positionRow = $this->select('pos_id = ' .  $getData['pos_id'], 'oto_position', '*', '', true);
			if($_FILES['uploadFile']['error'] == 0) {
				$imageInfo = getimagesize($_FILES['uploadFile']['tmp_name']);
				if ($positionRow['width'] && $positionRow['width'] != $imageInfo[0] || ($positionRow['height'] && $positionRow['height'] != $imageInfo[1]) ) {
					Custom_Common::showMsg(
						'<span style="color:red">图片尺寸不符合，请重新选择</span>',
						'back',
						array(
							'recommend/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			} else {
				if($positionRow['width']) {
					Custom_Common::showMsg(
						'<span style="color:red">请上传图片</span>',
						'back',
						array(
							'recommend/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '重新选择'
						)
					);
				}
			}
	
			$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
	
			$getData['img_url'] = $img_url ? $img_url : '';
	
			$backUrl = 'list/page:' . $getData['page'];
	
			$getData['title']  = $specialRow['title'];
			$getData['summary'] = $specialRow['wap_content'];
			if( $positionRow["identifier"]=="app_home_six_daren" ){
				$getData['www_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL']."/home/daren/daren-show/sid/{$getData['sid']}";
				$getData['pmark'] = 'wap';
				$getData['cmark'] = 'wap_index';
			}else{
				$getData['www_url'] = $specialRow['www_url'];
			}
			$result = $this->_model->recommend($getData);
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了专题名为  <b>{$specialRow['title']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend','special',$getData['sid']);
				Custom_Common::showMsg(
					'推荐成功',
					'',
					array(
						$backUrl => '返回专题管理'
					)
				);
			}
		}
	
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('discount','app_home_version_six')", "`identifier` in ('discount_banner','app_home_six_daren')");
	
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('specialRow', $specialRow);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/special_recommend.php');
	}
}
	