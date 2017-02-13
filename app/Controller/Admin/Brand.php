<?php
class Controller_Admin_Brand extends Controller_Admin_Abstract {
    private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Brand::getInstance();	
	}
	
	public function listAction() {
	    $page = $this->_http->getParam('page');
        $page = !$page ? 1 : intval($page);
        $page_str = '';  
        $getData = $this->_http->getParams();
        
		if(array_key_exists('brand_name', $getData)){
 		 	if($getData['brand_name']){
 				$page_str .= "brand_name:{$getData['brand_name']}/";
 	  		}
 		}
 		
 		if(array_key_exists('is_show', $getData)){
 			if($getData['is_show']){
 				$page_str .= "is_show:{$getData['is_show']}/";
 			}
 		}
 		
 		if(array_key_exists('store_id', $getData)){
 			if($getData['store_id']){
 				$page_str .= "store_id:{$getData['store_id']}/";
 			}
 		}
 		
        $this->_model->setWhere($getData); //设置WEHRE
        $this->_model->setOrder($getData); //设置order
        $page_info = $this->_get_page($page); 
        $brands = $this->_model->getBrandList($page);
	    $page_info['item_count'] = $this->_model->getCount();
		if($page_str){
        	$page_info['page_str'] = $page_str;        	
        }
        $this->_format_page($page_info);
        $storeArray = $this->getStore(0, true, false, $this->_ad_city);
                
        $this->_tpl->assign('storeArray', $storeArray);
	    $this->_tpl->assign('page_info', $page_info); 
	    $this->_tpl->assign('brands', $brands);
	    $this->_tpl->assign('page', $page);
	    $this->_tpl->display('admin/brand_list.php');
	}
    
    public function addAction() {
        if ($this->_http->isPost()) {
            $postData = $this->_http->getPost();
            $insert_result = $this->_model->brandEdit($postData);
            if ($insert_result) {
            	$this->getBrand(0, true, false);
            	$this->getBrand(0, false, false);
                $name = $postData['brand_name_zh'].$postData['brand_name_en'];
                Custom_Log::log($this->_userInfo['id'], "新增品牌  <b>{$name}</b> 成功", $this->pmodule, $this->cmodule, 'add');
	            Custom_Common::showMsg(
					'品牌添加成功',
					'back',
					array(
						'add' => '继续添加品牌',
						'list' => '返回品牌管理'
					)
		        );
            } else {
		        Custom_Common::showMsg(
					'品牌添加失败',
					'back'
		        );				
	        }
        }
        $picSize = $this->_model->getPositionForPicture();               
        $storeArray = $this->getStore(0, true, false, $this->_ad_city);
		
        $this->_tpl->assign('storeArray', $storeArray);
        $this->_tpl->assign('logosize',$picSize['logo']);
        $this->_tpl->assign('mapsize',$picSize['map']);
        $this->_tpl->assign('headsize',$picSize['head']);
        $this->_tpl->assign('iconsize',$picSize['icon']);

        $this->_tpl->display('admin/brand_add.php');
    }
    
    public function editAction() {
        if ($this->_http->isPost()) {
            $postData = $this->_http->getPost();
            $update_result = $this->_model->brandEdit($postData);
            if ($update_result) {
            	$this->getBrand(0, true, false);
            	$this->getBrand(0, false, false);
                $name = $postData['brand_name_zh'].' '.$postData['brand_name_en'];
                Custom_Log::log($this->_userInfo['id'], "编辑品牌  <b>{$name}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
		        Custom_Common::showMsg(
					'品牌编辑成功',
					'back',
					array('list' => '返回品牌管理','edit/id:'.$postData['brand_id'] => '重新编辑该品牌')	        
		        );
            } else {
		        Custom_Common::showMsg(
					'品牌编辑失败',
					'back'
		        );				
			}
        }
        $picSize = $this->_model->getPositionForPicture();
        $storeArray = $this->getStore(0, true, false, $this->_ad_city);
        
        $this->_tpl->assign('storeArray', $storeArray);
        $this->_tpl->assign('logosize',$picSize['logo']);
        $this->_tpl->assign('mapsize',$picSize['map']);
        $this->_tpl->assign('headsize',$picSize['head']);
        $this->_tpl->assign('iconsize',$picSize['icon']);


        $brand_id = $this->_http->get('id');
        $page = $this->_http->get('page');
        $brands = $this->select("`brand_id` = {$brand_id}", "oto_brand", '*', '', true);
        $this->_tpl->assign('brands', $brands);
        $this->_tpl->assign('page', $page);
        $this->_tpl->display('admin/brand_add.php');
    }
    
    public function delAction() {
        $brand_id = $this->_http->get('id');
	    if (!$brand_id) {
            Custom_Common::showMsg("请您选择要删除的品牌 ", 'back');
        }
        $row = $this->_db->fetchRow("select brand_name_zh, brand_name_en from oto_brand where brand_id = '{$brand_id}' limit 1");
        $resultBack = $this->_model->del($brand_id);
        $name = $row['brand_name_zh'].' '.$row['brand_name_en'];
        if ($resultBack) {
        	$this->getBrand(0, true, false);
        	$this->getBrand(0, false, false);
            Custom_Log::log($this->_userInfo['id'], "删除  <b>{$name}</b> 成功", $this->pmodule, $this->cmodule, 'del');
            Custom_Common::showMsg("删除品牌成功。 ", 'back',array('list' => '返回品牌列表'));
        }
    }
    
    public function checkZhAction() {
	    $getData = $this->_http->getPost();
	    $brand_name_zh = empty($getData['brand_name_zh'])?'':trim($getData['brand_name_zh']);
	    $brand_id = empty($getData['b_id']) ? 0 : intval($getData['b_id']);
	    $res = $this->_model->check_brand_name($brand_name_zh, $brand_id, 'zh');
	    if($res == 0){
            echo json_encode(true);
            exit;
	    }
	    exit;
    }
    
    public function checkEnAction() {
	    $getData = $this->_http->getPost();
	    $brand_name_en = empty($getData['brand_name_en'])?'':trim($getData['brand_name_en']);
	    $brand_id = empty($getData['b_id']) ? 0 : intval($getData['b_id']);
	    $res = $this->_model->check_brand_name($brand_name_en, $brand_id, 'en');
	    if($res == 0){
            echo json_encode(true);
            exit;
	    }
	    exit;
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
            $img_url = Custom_Upload::singleImgUpload($_FILES[$postData['type']],'brand');
            if(!$img_url){
                echo json_encode(array('msg'=>103));exit;
            }else{
               echo json_encode(array('msg'=>100 ,'img_url'=>$img_url , 'url' =>$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/brand/'.$img_url ));exit;
            }
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
    
    
    // 导入数据
    public function inputAction() {
        $zh = file_get_contents(VAR_PATH .'zh.txt');
        $zh = explode("\n", $zh);
        
        $en = file_get_contents(VAR_PATH .'en.txt');
        $en = explode("\n", $en);
        $tmp = array();
        foreach($zh as $k => $v) {
                    $field_zh = trim($v);
                    $field_en = trim($en[$k]);
                    //echo $field_zh.'--';
                    
                    if (!empty($field_zh)) {
                        $firs_word = Custom_Pinyin::getFirstWord($field_zh);
                        $firs_word = strtolower(substr($firs_word, 0, 1));
                    } else if (empty($field_zh) && !empty($field_en)) {
                        $firs_word = strtolower(substr($field_en, 0, 1));
                    }
                    
                    $tmp[] = array(
        							'brand_name_zh' => mysql_escape_string($field_zh),
        							'brand_name_en' => mysql_escape_string($field_en),
        					        'firs_word'     => $firs_word,
        					        'created'  => time(),
        							'updated' => time(),
        						);
                }
        $this->_db->insertBatch('oto_brand', $tmp);
    }
    
    /**
     * 推荐品牌
     */
    public function recommendAction() {
    	if ($this->_http->isPost()) {
    		$getData = $this->_http->getPost();
    		$checkRepeat = $this->_model->checkRecommend($getData['id'], $getData['pos_id']);
    		$brandRow = $this->_model->getBrandRow($getData['id'], $this->_city);
    		if($checkRepeat){
    			Custom_Common::showMsg(
    			'<span style="color:red">当前品牌在此推荐位重复，请重新选择推荐位</span>',
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
    		
    		
    		$getData['title'] = $brandRow['brand_name'];
    		$getData['summary'] = $brandRow['brand_profile'];
    		$result = $this->_model->recommend($getData);
    		if($result){
    			Custom_Log::log($this->_userInfo['id'], "新增了品牌名为  <b>{$brandRow['brand_name']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend');
    			Custom_Common::showMsg(
    			'推荐成功',
    			'',
    			array(
    			'list/page:' . $getData['page'] => '返回品牌列表'
    			)
    			);
    		}
    	}
    
    	$id = $this->_http->get('id');
    	$page = !$this->_http->get('page') ? 1 : $this->_http->get('page');
    	$brandRow = $this->_model->getBrandRow($id, $this->_city);
    	
    	$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('index', 'app_home_version_four', 'app_brand_version_four', 'app_brand_sort','discount','app_home_version_six')", "`identifier` not in ('index_latest_event', 'index_img_large', 'index_img_small', 'index_top_shop', 'index_value_pick', 'index_market','index_market_logo', 'app_home_recommended_coupons', 'app_home_recommended_for_you', 'app_brand_ticket', 'app_brand_banner','discount_banner', 'discount_market_recommend', 'discount_recommend', 'discount_circle_recommend')");
    	$this->_tpl->assign('position', $position);
    	$this->_tpl->assign('rjson', json_encode($position));
    	$this->_tpl->assign('id', $id);
    	$this->_tpl->assign('brandRow', $brandRow);
    	$this->_tpl->assign('page', $page);
    	$this->_tpl->display('admin/brand_recom.php');
    	
    }
    
    public function colorSizeAction() {
    	$page = $this->_http->has('page') ? intval($this->_http->get('page')) : 1;
    	$brand_id = $this->_http->get('bid');
    	if(!$brand_id) {
    		Custom_Common::showMsg('入口错误','back');
    	}
    	
    	if($this->_http->isPost()) {
    		$getData = $this->_http->getParams();
    		$this->_model->addEditColorSize($getData);
    		if($getData['mid']) {
    			_exit('编辑成功', 200);
    		} else {
    			_exit('新增成功', 100);
    		}
    	}
    	
    	$data = $this->_model->getColorSizeListByBrandId($brand_id);
    	
    	$this->_tpl->assign('page', $page);
    	$this->_tpl->assign('brand_id', $brand_id);
    	$this->_tpl->assign('data', $data);
    	$this->_tpl->display('admin/brand_color_size.php');
    }
    
    public function ajaxColorSizeAction() {
    	$this->_tpl->display('admin/ajax/ajax_color_size.php');
    }
    
    public function ajaxColorSizeDelAction() {
        $mid = $this->_http->get('mid');
    	if($mid) {
    		$this->_db->delete(oto_brand_color_size, array('id' => intval($mid)));
    		_exit('编辑成功', 100);
    	}
    }
}