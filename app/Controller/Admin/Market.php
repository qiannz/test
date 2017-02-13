<?php
class Controller_Admin_Market extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Market::getInstance();
	}
	
	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$page_str = '';
		$getData = $this->_http->getParams();

		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page')) && !empty($value)) {
				$page_str .= "{$key}:{$value}/";
			}
		}
			
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($page);
			
		$data = $this->_model->getMarketList($page);
		$page_info['item_count'] = $this->_model->getCount();
		
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		
		$this->_format_page($page_info);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/market_list.php');
	}
	
	public function addEditAction() {
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$update_result = $this->_model->marketEdit($getData);
			if ($update_result) {
				if($getData['market_id']) {
					Custom_Log::log($this->_userInfo['id'], "编辑商场  <b>{$getData['market_name']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
					$this->getMarket(0, false);
					Custom_Common::showMsg(
						'商场编辑成功',
						'back',
						array(
							'list/page:' . $getData['page'] => '返回商场列表',
							'add-edit/id:'.$getData['market_id'] => '重新编辑该商场'
						)
					);
				} else {
					Custom_Log::log($this->_userInfo['id'], "新增商场  <b>{$getData['market_name']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
					$this->getMarket(0, false);
					Custom_Common::showMsg(
						'新增商场成功',
						'back',
						array(
							'list' => '返回商场列表',
							'add-edit/id:'.$update_result => '重新编辑该商场'
						)
					);					
				}
			} else {
				Custom_Common::showMsg('操作失败');
			}
		}
		
		$picSize = $this->_model->getPositionForPicture();
		$this->_tpl->assign('logosize',$picSize['logo']);
		$this->_tpl->assign('headsize',$picSize['head']);
		
		$market_id = intval($this->_http->get('id'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		if($market_id) {
			$makets = $this->select("`market_id` = {$market_id}", "oto_market", '*', '', true);
			$this->_tpl->assign('makets', $makets);
			if ($makets['region_id']){
				$circleArray = $this->getCircleByRegionId($makets['region_id'], false, true, $this->_ad_city);
				$this->_tpl->assign('circleArray', $circleArray);
			}
		}
					
		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/market_edit.php');
	}
	
	public function marketSyncAction() {
		set_time_limit(300);
		if($this->_model->marketSync()) {
			_exit('success', 100);
		} else {
			_exit('failure', 300);
		}
	}
	
	public function ajaxColAction(){
		$resultBack = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBack) {
			exit(json_encode(true));
		}
		exit(json_encode(false));
	}
	
    /**
     * 推荐商场
     */
    public function recommendAction() {
    	$id = $this->_http->get('id');
    	$page = !$this->_http->get('page') ? 1 : $this->_http->get('page');
    	$marketRow = $this->select("`market_id` = {$id}", "oto_market", '*', '', true);
    	if ($this->_http->isPost()) {
    		$getData = $this->_http->getPost();
    		$checkRepeat = $this->_model->checkRecommend($getData['id'], $getData['pos_id']);
    		if($checkRepeat){
    			Custom_Common::showMsg(
	    			'<span style="color:red">当前商场在此推荐位重复，请重新选择推荐位</span>',
	    			'recommend/id:' . $getData['id'] . '/page:' . $getData['page']
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
	    					'recommend/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
	    				)
    				);
    			}
    		} else {
    			if($positionRow['width']) {
    				Custom_Common::showMsg(
	    				'<span style="color:red">请上传图片</span>',
	    				'back',
	    				array(
	    					'recommend/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
	    				)
    				);
    			}
    		}
    	
    		$img_url = Custom_Upload::singleImgUpload($_FILES['uploadFile'], 'recommend');
    		$getData['img_url'] = $img_url ? $img_url : '';
    	
    	
    		$getData['title'] = $marketRow['market_name'];
    		$getData['summary'] = $marketRow['intro'];
    		$getData['identifier'] = $positionRow['identifier'];
    		$result = $this->_model->recommend($getData);
    		if($result){
    			Custom_Log::log($this->_userInfo['id'], "新增了商场名为  <b>{$marketRow['market_name']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend');
    			Custom_Common::showMsg(
	    			'推荐成功',
	    			'',
	    			array(
	    				'list/page:' . $getData['page'] => '返回商场管理'
	    			)
    			);
    		}
    	}
    	

    	 
    	$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('index', 'app_home_version_four', 'app_brand_version_four', 'app_brand_sort','discount')", "`identifier` not in ('index_latest_event', 'index_img_large', 'index_img_small', 'index_top_shop', 'index_value_pick', 'index_market','index_market_logo', 'app_home_recommended_coupons', 'app_home_recommended_for_you', 'app_brand_ticket', 'app_brand_banner','discount_banner', 'discount_brand_recommend', 'discount_recommend', 'discount_circle_recommend')");
    	$this->_tpl->assign('position', $position);
    	$this->_tpl->assign('rjson', json_encode($position));
    	$this->_tpl->assign('id', $id);
    	$this->_tpl->assign('marketRow', $marketRow);
    	$this->_tpl->assign('page', $page);
    	$this->_tpl->display('admin/market_recommend.php');
    	
    }
	
	public function uploadAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(!$_FILES[$postData['type']]){
				echo json_encode(array('msg'=>101)); exit;
			}
			$size = getimagesize($_FILES[$postData['type']]['tmp_name']);
			$imgWidth  = $size[0];
			$imgHeight = $size[1];
	
			if($imgWidth > $postData['width'] || $imgHeight > $postData['height'] || $imgWidth < $postData['width'] || $imgHeight < $postData['height']){
				echo json_encode(array('msg'=>102)); exit;
			}
			$img_url = Custom_Upload::singleImgUpload($_FILES[$postData['type']],'market');
			if(!$img_url){
				echo json_encode(array('msg'=>103));exit;
			}else{
				echo json_encode(array('msg'=>100 ,'img_url'=>$img_url , 'url' =>$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/market/'.$img_url ));exit;
			}
		}
	}
	
	public function deleteAction() {
		$id = intval($this->_http->get('id'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		$result = $this->_model->del($id);
		if ($result) {
			$this->getMarket(0, false);
			Custom_Common::showMsg(
				'商场删除成功',
				'back',
				array('list/page:' . $page => '返回商场列表')
			);
		} else {
			Custom_Common::showMsg('商场删除失败','back');
		}
	}
}