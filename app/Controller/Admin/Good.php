<?php
class Controller_Admin_Good extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Good::getInstance();	
	}
	
	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		$days = $this->_http->get('days');
		$page_str = '';
		$getData = $this->_http->getParams();
		if ($days) {
			$getData['days'] = $days;
			$this->_tpl->assign('batch', true);
			$this->_tpl->assign('auditDay', $days);
		}
		
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
		$this->_tpl->assign('7days', $this->get7daysAction(7));
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->display('admin/good_list.php');	
	}
	
	public function addAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$insert_good_id = $this->_model->postGood($getData);
			if($insert_good_id) {
				Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'add', 'good', $insert_good_id);
				Custom_Common::showMsg(
					'商品新增成功',
					'back',
					array(
						'add' => '继续新增',
						'list' => '返回商品列表'
					)
				);
			}
		}
		
		$regionArray = $this->getRegion(0, true, $this->_ad_city);		
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->display('admin/good_modi.php');
	}
	
	public function editAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$postEditResult = $this->_model->postGood($getData);
			if($postEditResult) {
				Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'edit', 'good', $getData['gid']);
				Custom_Common::showMsg(
				'商品编辑成功',
				'back',
				array(
				'edit/gid:'.$getData['gid'] => '继续编辑',
				'list' => '返回商品列表'
						)
				);
			}
		}
		$good_id = $this->_http->get('gid');
		
		$row = $this->_model->getGoodRow($good_id);
		$imgList = $this->_model->getImgList($good_id);
		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$circleArray = $this->getCircleByRegionId($row['region_id'], false, true, $this->_ad_city);
		$shopArray = $this->getShop($row['region_id'], $row['circle_id'], $this->_ad_city);
		
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('circleArray', $circleArray);
		$this->_tpl->assign('shopArray', $shopArray);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('imgList', $imgList);
		
		$this->_tpl->display('admin/good_modi.php');
	}
	/**
	 * 单个删除
	 */
	public function delAction() {
		$id = $this->_http->get('id');
		$good_name = $this->_http->get('gname');
		$page = $this->_http->get('page');
		$result = $this->_model->del($id);
		if($result) {
			$content = "商品名称：{$good_name} 　商品ID：{$id}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'good', $id);
			Custom_Common::showMsg('商品删除成功', '/admin/good/list/page:' . $page);
		}
	}
	/**
	 * 批量删除
	 */
	public function delAllAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$idArray = explode(',', $ids);
		foreach($idArray as $id) {
			$result = $this->_model->del($id);
		}
		if($result) {
			$content = "商品ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'delAll');
			Custom_Common::showMsg('商品删除成功', '', array('list/page:' . $page => '返回商品列表'));
		}
	}
	/**
	 * 批量恢复删除
	 */
	public function unDelAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$idArray = explode(',', $ids);
		foreach($idArray as $id) {
			$result = $this->_model->unDel($id);
		}
		if($result) {
			$content = "商品ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'unDel');
			Custom_Common::showMsg('商品批量恢复成功', '', array('list' => '返回商品列表'));
		}
	}
	/**
	 * 审核商品
	 */
	public function auditAction() {		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->newAudit($getData);
			$goodRow = $this->_model->getGoodRow($getData['gid']);
			if($result) {
				$content = "商品名称：{$goodRow['good_name']}　商品ID：{$getData['gid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				Custom_Common::showMsg($getData['audit_type'] == 1?'商品审核通过':'商品审核不通过', '', array('list/days:'.$getData['audit_day'].'/page:' . $getData['page'] => '返回商品列表'));
			}
		}
		$gid = $this->_http->get('gid');
		$goodRow = $this->_model->getGoodRow($gid);
		$page = $this->_http->get('page');
		$auditDay = $this->_http->get('audit_day'); // 商品审核日

		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('gid', $gid);
		$this->_tpl->assign('gname', $goodRow['good_name']);
		$this->_tpl->assign('audit_day', $auditDay);
		$this->_tpl->display('admin/good_audit.php');
	}
	
	/**
	 * 判断在活动结束时间前 是否有活动没有审核
	 */
	public function checkAuditAction() {
		$auditDay = $this->_http->get('audit_day');
		$taskDay = $GLOBALS['GLOBAL_CONF']['TASK_START_TIME']; // 天天向上活动开始日		
		$checkAudit = $this->_model->checkAuditDay($auditDay, $taskDay);
		if ($checkAudit > 0) {
			echo 'audit';
			exit();
		}
	}
	
	/**
	 * 推荐商品
	 */
	public function recommendAction() {
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$checkRepeat = $this->_model->checkRecommend($getData['id'], $getData['pos_id']);
			$goodRow = $this->_model->getGoodRow($getData['id']);
			if($checkRepeat){
				Custom_Common::showMsg(
					'当前商品在此推荐位重复，请重新选择推荐位',
					'/admin/good/recommend/id:' . $getData['id'] . '/page:' . $getData['page']
				);
			}		
			$img_url = Custom_Upload::recommendImgUpload($_FILES, $getData);
			if ($img_url == 'img_error') {
				Custom_Common::showMsg(
					'图片尺寸不符合，请重新选择',
					'back',
					array(
						'recommend/id:' . $getData['id'] . '/page:' . $getData['page'] => '重新选择'
					)
				);
			}
			$getData['img_url'] = $img_url;
			$getData['title'] = $getData['summary'] = $goodRow['good_name'];
			$result = $this->_model->recommend($getData);			
			if($result){
				Custom_Log::log($this->_userInfo['id'], "新增了商品名为  <b>{$goodRow['good_name']}</b> 的推荐", $this->pmodule, $this->cmodule, 'recommend');
				Custom_Common::showMsg(
					'推荐成功',
					'',
					array(
						'list/page:' . $getData['page'] => '返回商品列表'
					)
				);
			}
		}
		
		$id = $this->_http->get('id');
		$goodRow = $this->_model->getGoodRow($id);
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('index','recommend_goods','good_show')", "`identifier` not in ('index_img_large','index_img_small','index_top_shop')");
		$this->_tpl->assign('position', $position);
		$this->_tpl->assign('rjson', json_encode($position));
		$this->_tpl->assign('id', $id);
		$this->_tpl->assign('goodRow', $goodRow);
		$this->_tpl->assign('page', $page);
		
		$this->_tpl->display('admin/recommend_good_position.php');
	}
	
	// 上传图片
	public function uploadAction() {
		if($this->_http->isPost()){
			$user_name = $this->_http->has('user_name') ? $this->_http->get('user_name') : DEFINED_USER_NAME;
			$folder = $this->_http->has('folder') ? $this->_http->get('folder') : 'good';
			$primary_id = $this->_http->has('primary_id') ? intval($this->_http->get('primary_id')) : 0;
			$user_id = $this->getUserIdByUserName($user_name);
			$picStr = Custom_Upload::imageUpload($_FILES, $user_id, $folder, $primary_id);
			if($picStr){
				list($aid, $img_url) = explode('|', $picStr);
				$picArr = array(
						'error' => 0,
						'data' => array(
								array(
									'aid' => $aid,
									'img_url' => $GLOBALS['GLOBAL_CONF']['IMG_URL'].$img_url,
									'gid' => $primary_id
								)
						)
				);
				exit(json_encode($picArr));
			}
			exit(json_encode(array('error' => 1)));
		}
	}
	
	private function imageUpload($_FILES, $user_id = 0, $folder = 'good', $primary_id = 0) {
		$db = Core_DB::get('superbuy', null, true);
		$image = Third_Image::getInstance();
		$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
		$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
		//上传原始图
		$filePath = $image->upload_image($_FILES['uploadFile'], $imgPath . 'original/');
		//图片日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'upload/original/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, $image->error_msg(), $logPath);
		
		//文件名
		$baseName = basename($filePath);
	
		//拷贝到buy文件夹一份
		$dir = $image->make_dir($imgCopyPath  . 'original/');
		self::imageCopy($dir, $baseName, $filePath);
	
		$configArray = @include VAR_PATH . 'config/config.php';
		$imgSizeArray = $configArray['IMAGE_SIEZ'];
		foreach($imgSizeArray as $key => $item) {
			if($item['width'] == 220) {
				$image->thumb($filePath, $item['width'], $item['height'], $imgPath . $item['width'] . '/');
			} else {
				$image->make_thumb($filePath, $item['width'], $item['height'], $imgPath . $item['width'] . '/');
			}
			$tmpFileName = str_replace('original', $item['width'], $filePath);
			//加水印
			if($item['water'] == 1) {
				$image->add_watermark($tmpFileName, '', ROOT_PATH.'web/data/water_mark.png', 5);
			}
			
			//图片日志
			$fileName = date('Ymd'). '.log';
			$logPath = LOG_PATH . 'upload/' . $item['width'] . '/' . date('Y') . '/' .date('m') . '/';
			logLog($fileName, $image->error_msg(), $logPath);
			
			//拷贝到buy文件夹一份
			$dir = $image->make_dir($imgCopyPath . $item['width'] . '/');
			$this->imageCopy($dir, $baseName, $tmpFileName);
		}
		 
		//160 的缩略图， 如果原始图太小则是拷贝操作
		$thumb_small = $image->thumb($filePath, 160, 160, $imgPath . 'small/');
		//图片日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'upload/small/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, $image->error_msg(), $logPath);
		
		//拷贝到buy文件夹一份
		$dir = $image->make_dir($imgCopyPath  . 'small/');
		$tmpFileName = str_replace('original', 'small', $filePath);
		$this->imageCopy($dir, $baseName, $tmpFileName);
		 
		 
		//原始图加水印 同时保存到一个新的文件夹中  如果 图片和宽高都小于 300 则不加水印
		$thumb = $image->add_watermark($filePath, ROOT_PATH.'web/data/'.$folder.'/thumb/', ROOT_PATH.'web/data/water_mark.png', 5);
		//图片日志
		$fileName = date('Ymd'). '.log';
		$logPath = LOG_PATH . 'upload/thumb/' . date('Y') . '/' .date('m') . '/';
		logLog($fileName, $image->error_msg(), $logPath);
		
		//拷贝到buy文件夹一份
		$dir = $image->make_dir($imgCopyPath  . 'thumb/');
		$tmpFileName = str_replace('original', 'thumb', $filePath);
		$this->imageCopy($dir, $baseName, $tmpFileName);
		
		if($filePath) {
			$file_name = basename($filePath);
			$ftp_path = str_replace($imgPath . 'original/', '', substr($filePath, 0, - (strlen($file_name) + 1)));
			if($folder == 'good') {
				$arr = array(
						'good_id'   => $primary_id,
						'user_id'  => $user_id,
						'img_url'  => str_replace($imgPath . 'original/', '', $filePath),
						'created' => REQUEST_TIME
				);
				$aid = $db->insert('oto_good_img', $arr);
			} elseif($folder == 'ticket') {
				$arr = array(
						'ticket_id'   => $primary_id,
						'user_id'  => $user_id,
						'img_url'  => str_replace($imgPath . 'original/', '', $filePath),
						'created' => REQUEST_TIME
				);
				$aid = $db->insert('oto_ticket_img', $arr);
			}
			if($aid){
				return $aid.'|'."/buy/{$folder}/small/{$ftp_path}/{$file_name}";
			}
		}
		return false;
	}	
	
	private function imageCopy($dir, $baseName, $tmpFileName) {
		if(create_folders($dir)) {
			copy($tmpFileName, $dir.$baseName);
		}
	}
	/**
	 * 批量审核
	 */
	public function batchAuditAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$days = $this->_http->get('days');
		$idArray = explode(',', $ids);
		// 审核通过所选商品
		$sql1 = "update oto_good set good_status = 1 where " . $this->db_create_in($idArray, 'good_id') . " and good_status = 0";
		$rs = $this->_db->query($sql1);
		if ($rs !== true) {
			$sql2 = "select * from oto_good where " . $this->db_create_in($ids, 'good_id') ." group by user_id order by created desc";
			$goodinfo = $this->_db->fetchAll($sql2);
			/**
			 * 名品街任务活动的时间范围内的商品 进行任务触发
			 */
			$taskStartDay = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']); // 名品街任务活动的开始时间
			$taskEndDay = strtotime($GLOBALS['GLOBAL_CONF']['TASK_END_TIME']); // 名品街任务活动的结束时间
			foreach ($goodinfo as &$row) {
				if ($row && $taskStartDay < $row['created'] && $row['created'] < $taskEndDay) {
					//$this->_model->taskEveryDay($row);
					$this->_model->taskExecution($row);
					Custom_Log::log($this->_userInfo['id'], "商品 <b>{$row['good_name']}</b>-ID:{$row['good_id']} 审核成功", $this->pmodule, $this->cmodule, 'audit');
				}
			}
			
			Custom_Common::showMsg('商品审核成功', '', array('list/days:' . $days .'/page:' . $page => '返回商品列表'));
		} else {
			Custom_Common::showMsg('商品审核失败', '', array('list/days:' . $days .'/page:' . $page => '返回商品列表'));
		}
	}

    /**
     * 批量上传审核 8/28 营业员说上传每件商品获取奖励
     */
    public function batchUploadAction(){
        $ids = $this->_http->get('id');
        $page = $this->_http->get('page');
        $days = $this->_http->get('days');
        $idArray = explode(',', $ids);
        // 审核通过所选商品
        $sql1 = "update oto_good set good_status = 1 where " . $this->db_create_in($idArray, 'good_id') . " and good_status = 0";
        $rs = $this->_db->query($sql1);
        if ($rs !== true) {
            $sql2 = "select * from oto_good where " . $this->db_create_in($ids, 'good_id') ." group by user_id order by created desc";
            $goodinfo = $this->_db->fetchAll($sql2);
            foreach ($goodinfo as &$row) {
                $userInfo = $this->_db->fetchRow("select * from oto_user  where user_id = '{$row['user_id']}'");
                if($userInfo['user_type'] == 3){
                    $this->_model->AwardForClerk($row, 0.5, date('Y-m-d', REQUEST_TIME), 8);
                }
                Custom_Log::log($this->_userInfo['id'], "商品 <b>{$row['good_name']}</b>-ID:{$row['good_id']} 审核成功", $this->pmodule, $this->cmodule, 'audit');
            }
            Custom_Common::showMsg('商品审核成功', '', array('list/days:' . $days .'/page:' . $page => '返回商品列表'));
        } else {
            Custom_Common::showMsg('商品审核失败', '', array('list/days:' . $days .'/page:' . $page => '返回商品列表'));
        }
    }

	/**
	 * 批量审核拒绝
	 */
	public function notBatchAuditAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$days = $this->_http->get('days');
		$idArray = explode(',', $ids);
		// 审核通过所选商品
		$sql = "update oto_good set good_status = '-1' where " . $this->db_create_in($idArray, 'good_id') . " and good_status = 0";
		$this->_db->query($sql);
		Custom_Common::showMsg('商品审核拒绝成功', '', array('list/days:' . $days .'/page:' . $page => '返回商品列表'));		
	}
		
	public function delImgAction() {
		$aid = $this->_http->get('aid');
		if(Custom_Upload::imageDelete($aid)) {
			echo json_encode(array('status' => 'ok'));
		}
	}
	
	public function setFirstAction() {
		$imgId = $this->_http->get('imgId');
		$gid = $this->_http->get('gid');
		if($this->_model->setFirst($imgId, $gid)) {
			echo json_encode(array('status' => 'ok'));
		}
	}
	
	public function getCircleAction() {
		$region_id = $this->_http->get('id');
		if($region_id) {
			echo json_encode($this->getCircleByRegionId($region_id, false, true, $this->_ad_city));
			exit();
		}
		echo json_encode(array());
		exit();
	}
	
	public function getMarketAction() {
		$region_id = $this->_http->get('id');
		$marketArray = $this->getMarket($region_id, true, $this->_ad_city);
		$marketArray = !$marketArray ? array() : $marketArray;
		echo json_encode($marketArray);
	}
	
	public function getShopAction() {
		$master = false;
		$region_id = $this->_http->get('region_id');
		$circle_id = $this->_http->get('circle_id');
		$user_type = $this->_http->get('user_type');
		if($user_type != 3) {
			$master = $this->_http->get('master');
		}
		echo json_encode($this->getShop($region_id, $circle_id, $this->_ad_city));
	}
	
	public function get7daysAction($num) {
		$data = array();
		for ($i=1; $i <= $num; $i++ ) {
			$p = 1-$i;
			$data[$i] = date("Y-m-d",strtotime("$p day"));
		}
		return $data;
	}
}