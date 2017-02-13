<?php
class Controller_Admin_Circle extends Controller_Admin_Abstract {
    
    private $_model;
	
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Circle::getInstance();	
	}
	
	public function list_backAction() {
	    $region_id = $_GET['id'];
	    $region = $this->getRegion(0, true, $this->_ad_city);
	    $circle = $this->_model->getList($region_id);
	    
	    $this->_tpl->assign('region', $region);
	    $this->_tpl->assign('circle', $circle);
	    $this->_tpl->assign('region_id', $region_id);
	    $this->_tpl->display('admin/circle_list.php');
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
		
		$region = $this->getRegion(0, true, $this->_ad_city);
		$this->_model->setWhere($getData);
		$circle = $this->_model->getList($page, $this->_ad_city);
		foreach ($circle as &$row) {
			$row['r_name'] = $this->getRegion($row['region_id'], true, $this->_ad_city); 
		}
		
		$page_info = $this->_get_page($page);
		$page_info['item_count'] = $this->_model->getCount($this->_ad_city);
		if($page_str){
			$page_info['page_str'] = $page_str;
		}
		$this->_format_page($page_info);

		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('region', $region);
		$this->_tpl->assign('circle', $circle);
		$this->_tpl->display('admin/circle_list.php');
	}
	
	public function addAction() {
		$region = $this->getRegion(0, true, $this->_ad_city);
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$insert_result = $this->_model->modi($postData, $this->_ad_city);
			if ($insert_result) {
				$this->getCircleByRegionId(0, false, false, $this->_ad_city);
				$this->getCircleByCircleId(0, false, $this->_ad_city);
 				Custom_Log::log($this->_userInfo['id'], "新增商圈  <b>{$postData['circle_name']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
				Custom_Common::showMsg(
						'商圈添加成功',
						'back',
						array('add' => '继续添加商圈','list' => '返回商圈管理')
				);
			} else {
				Custom_Common::showMsg(
				'商圈添加失败',
				'back'
				);
			}
		}
		
		$this->_tpl->assign('region', $region);
		$this->_tpl->display('admin/circle_modi.php');
	}
	
	public function editAction() {
		if ($this->_http->isPost()) {
			$postData = $this->_http->getPost();
			$update_result = $this->_model->modi($postData, $this->_ad_city);
			if ($update_result) {
				$this->getCircleByRegionId(0, false, false, $this->_ad_city);
				$this->getCircleByCircleId(0, false, $this->_ad_city);
				Custom_Log::log($this->_userInfo['id'], "编辑商圈  <b>{$postData['circle_name']}</b> 成功", $this->pmodule, $this->cmodule, 'edit');
				Custom_Common::showMsg(
				'商圈编辑成功',
				'back',
				array('list' => '返回商圈管理','edit/id:'.$postData['circle_id'] => '重新编辑该商圈 ')
				);
			} else {
				Custom_Common::showMsg(
				'商圈编辑失败',
				'back'
				);
			}
		}
		$circle_id = $this->_http->get('id');
		$page = $this->_http->get('page');
		$circle = $this->select("`circle_id` = {$circle_id}", "oto_circle", '*', '', true);
		$region = $this->getRegion(0, true, $this->_ad_city);

		$this->_tpl->assign('circle', $circle);
		$this->_tpl->assign('region', $region);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/circle_modi.php');
	}
	
	public function delAction() {
		$circle_id = $this->_http->get('id');
		$circle_name = $this->_http->get('name');
		if (!$circle_id) {
			Custom_Common::showMsg("请您选择要删除的商圈 ", 'back');
		}
		$result = $this->_model->del($circle_id);
		if($result) {
			$this->getCircleByRegionId(0, false, false, $this->_ad_city);
			$this->getCircleByCircleId(0, false, $this->_ad_city);
			Custom_Log::log($this->_userInfo['id'], "删除商圈  <b>{$circle_name}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			Custom_Common::showMsg("删除商圈成功。 ", 'back',array('list' => '返回商圈列表'));
		}
	}
	
	// ajax编辑排序
	public function ajaxColAction(){
		$resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		if($resultBact) {
			$this->getCircleByRegionId(0, false, false, $this->_ad_city);
			$this->getCircleByCircleId(0, false, $this->_ad_city);
			exit(json_encode(true));
		} else {
			exit(json_encode(false));
		}
	}
    
    public function add_backAction() {
        $region_id = $this->_http->get('region_id');
        $cname = $this->_http->get('cname');
        $isHot = $this->_http->get('is_hot');
        $result = $this->_model->add($region_id, $cname, $isHot);
        if ($result) {
            $this->getCircleByRegionId(0, false, false, $this->_ad_city);
            $this->getCircleByCircleId(0, false, $this->_ad_city);
            Custom_Log::log($this->_userInfo['id'], "新增商圈  <b>{$cname}</b> 成功", $this->pmodule, $this->cmodule, 'add');
            _exit('添加成功', 1);
        }
    }
    
    public function edit_backAction() {
    	$id = $this->_http->get('id');
    	$region_id = $this->_http->get('region_id');
    	$cname = $this->_http->get('cname');
    	$isHot = $this->_http->get('is_hot');
    	$result = $this->_model->edit($id, $region_id, $cname, $isHot);
    	if ($result) {
    		$this->getCircleByRegionId(0, false, false, $this->_ad_city);
            $this->getCircleByCircleId(0, false, $this->_ad_city);
    		Custom_Log::log($this->_userInfo['id'], "编辑商圈  <b>{$cname}</b> 成功", $this->pmodule, $this->cmodule, 'edit');
    		_exit('编辑成功', 1);
    	}
    }
    
    public function del_backAction() {
		$id = $this->_http->get('id');
		$cname = $this->_http->get('cname');
		$result = $this->_model->del($id);
		if($result) {
			$this->getCircleByRegionId(0, false, false, $this->_ad_city);
            $this->getCircleByCircleId(0, false, $this->_ad_city);
			Custom_Log::log($this->_userInfo['id'], "删除商圈  <b>{$cname}</b> 成功", $this->pmodule, $this->cmodule, 'del');
			_exit('删除成功', 1);
		}
    }
    
    public function recommendAction() {
    	$id = $this->_http->get('id');
    	$page = !$this->_http->get('page') ? 1 : $this->_http->get('page');
    	$circleRow = $this->select("`circle_id` = {$id}", "oto_circle", '*', '', true);
    	 
    	if ($this->_http->isPost()) {
    		$getData = $this->_http->getPost();
    		$checkRepeat = $this->_model->checkRecommend($getData['id'], $getData['pos_id']);
    		if($checkRepeat){
    			Custom_Common::showMsg(
	    			'<span style="color:red">当前商圈在此推荐位重复，请重新选择推荐位</span>',
	    			'/admin/brand/recommend/id:' . $getData['id'] . '/page:' . $getData['page']
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
    
    
    		$getData['title'] = $circleRow['circle_name'];
    		$getData['summary'] = $circleRow['circle_name'];
    		$result = $this->_model->recommend($getData);
    		if($result){
    			Custom_Log::log($this->_userInfo['id'], "新增了商圈名为  <b>{$circleRow['circle_name']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend');
    			Custom_Common::showMsg(
	    			'推荐成功',
	    			'',
	    			array(
	    				'list/page:' . $getData['page'] => '返回商圈管理'
	    			)
    			);
    		}
    	}
    
    	$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('discount')", "`identifier` in ('discount_circle_recommend')");
    	$this->_tpl->assign('position', $position);
    	$this->_tpl->assign('rjson', json_encode($position));
    	$this->_tpl->assign('id', $id);
    	$this->_tpl->assign('circleRow', $circleRow);
    	$this->_tpl->assign('page', $page);
    	$this->_tpl->display('admin/circle_recommend.php');
    	 
    }    
}