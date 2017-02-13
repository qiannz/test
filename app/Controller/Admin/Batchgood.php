<?php
class Controller_Admin_Batchgood extends Controller_Admin_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Batchgood::getInstance();
	}

	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		
		$page_str = '';
		$getData = $this->_http->getParams();
		$page_str = $this->getPageStr($getData);
			
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
		$this->_tpl->display('admin/warehousing/batchgood_list.php');
	}
	
	public function addEditAction(){
		$ticket_id = $this->_http->has('tid') ? intval($this->_http->get('tid')) : 0;
		$page = $this->_http->get('page') ? $this->_http->get('page') : 1;
		if( $this->_http->isPost() ) {
			$getData = $this->_http->getPost();
			$wap_img_url = array();
			if($_FILES['img_url']['error'][0] == 0) {
				foreach($_FILES['img_url']['tmp_name'] as $_key => $_value) {
					$img_url = Custom_Upload::singleImgUpload(
							array(
									'name' => $_FILES['img_url']['name'][$_key],
									'type' => $_FILES['img_url']['type'][$_key],
									'tmp_name' => $_value,
									'error' => $_FILES['img_url']['error'][$_key],
									'size' => $_FILES['img_url']['size'][$_key]
							), 'ticketwap');
						
					if(!$img_url) {
						Custom_Common::showMsg(
						'<span style="color:red">请检查图片格式，大小是否正确</span>',
						'back'
						);
					}
					$wap_img_url[] = $img_url;
				}
			}
			$flag = $this->_model->updateGoodInfo($getData);
			if( $flag === false ){
				Custom_Common::showMsg('商品编辑失败');
			}else{
				Custom_Log::log($this->_userInfo['id'], "编辑了商品： <b>{$getData['ticket_title']}</b> 内容：".serialize($getData), $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
				//WAP图片上传
				$user_id = $this->getUserIdByUserName($getData['user_name']);
				Model_Admin_Ticket::getInstance()->wapUploadImg($wap_img_url, $getData['tid'], $getData['sid'], $user_id);
				if( $getData['type'] == 2 ){
					Custom_Common::showMsg(
						'卖场商品编辑成功',
						'',
						array(
							'../marketgood/list/page:' . $getData['page'] => '返回卖场商品',
							'add-edit/tid:' . $getData['tid'] . '/type:2/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '我要重新编辑'
						)
					);
				}else{
					Custom_Common::showMsg(
						'后仓商品编辑成功',
						'',
						array(
							'list/page:' . $getData['page'] => '返回后仓商品',
							'add-edit/tid:' . $getData['tid'] . '/uname:' . $getData['user_name'] . '/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '我要重新编辑'
						)
					);
				}
			}	
		}
		if( $ticket_id ){
			$row = Model_Home_Ticket::getInstance()->getTicketRow($ticket_id);
			$batchRow = $this->_model->getGooodBatchRow($ticket_id);
			$row['batch'] = $batchRow;
			$colorSizeInfo = Model_Admin_Batch::getInstance()->getFormatColorSize($row['shop_id']);
			$skuArr = $this->_model->getGoodSku($ticket_id);
			$skuInfo = array();
			foreach ($skuArr as $skuRow){
				$skuRow['color'] = $colorSizeInfo[1][$skuRow['good_color']];
				$skuRow['size'] = $colorSizeInfo[2][$skuRow['good_size']];
				$skuInfo[$skuRow['color']][] = $skuRow; 
			}
			$row['sku'] = $skuInfo;
			//wap
			$wapImgData = Model_Admin_Ticket::getInstance()->getWapImg($ticket_id);
			$this->_tpl->assign('wapImgData', $wapImgData);
		}
		$sid = $this->_http->get('sid');
		$uname = $this->_http->get('uname');
		
		$shop_info = $this->getShopFieldById($sid);
		$row['shop_name'] = $shop_info['shop_name'];
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		
		$type = intval($this->_http->get('type'));//1：后仓商品  2：卖场商品
		$this->_tpl->assign('type', $type);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('uname', $uname);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->display('admin/warehousing/batchgood_edit.php');
	}
	
	public function wapImgDelAction() {
		$id = intval($this->_http->get('id'));
		$this->_db->update(
				'oto_ticket_wap_img',
				array('ticket_id' => '0'),
				array('id' => $id)
		);
		exit(json_encode(array('status' => 'ok')));
	}
	
	public function imgAjaxColAction() {
		$getData = $this->_http->getPost();
	
		Model_Admin_Buygood::getInstance()->img_ajax_edit($getData);
	}
	
	public function getGoodSkuAction(){
		$ticket_id = intval($this->_http->get('tid'));
		$shop_id = intval($this->_http->get('sid'));
		if( !$ticket_id || !$shop_id ){
			exit('');
		}
		$skuArr = $this->_model->getGoodSku($ticket_id);
		$colorSizeInfo = Model_Admin_Batch::getInstance()->getFormatColorSize($shop_id);
		if( empty($colorSizeInfo) ){
			exit('');
		}
		foreach ($skuArr as &$skuRow){
			$skuRow['color'] = $colorSizeInfo[1][$skuRow['good_color']];
			$skuRow['size'] = $colorSizeInfo[2][$skuRow['good_size']];
		}
		$this->_tpl->assign('ticket_id',$ticket_id);
		$this->_tpl->assign('shop_id',$shop_id);
		$this->_tpl->assign('sku',$skuArr);
		$html = $this->_tpl->fetch('admin/warehousing/batchgood_sku.php');
		exit($html);
	}
	
	public function getGoodSkuDetailAction(){
		$getData = $this->_http->getPost();
		$ticket_id = intval($getData['tid']);
		$tiketRow = Model_Home_Ticket::getInstance()->getTicketRow($ticket_id);
		$good_color = $getData['color'];
		$good_size = $getData['size'];
		$opert = intval($getData['opert']);
		$skuArr = $this->_model->getGoodSku( $ticket_id, $good_color, $good_size );
		$skuRow = $skuArr[0];
		$colorSizeInfo = Model_Admin_Batch::getInstance()->getFormatColorSize($tiketRow['shop_id']);
		$skuRow['color'] = $colorSizeInfo[1][$skuRow['good_color']];
		$skuRow['size'] = $colorSizeInfo[2][$skuRow['good_size']];
		$this->_tpl->assign('opert',$opert);
		$this->_tpl->assign('row',$tiketRow);
		$this->_tpl->assign('sku',$skuRow);
		$html = $this->_tpl->fetch('admin/warehousing/batchgood_skuopert.php');
		exit($html);
	}
	
	public function intoMarketAction(){
		$getData = $this->_http->getPost();
		$ticket_id = intval( $getData['tid'] );
		$color = $getData['color'];
		$size = $getData['size'];
		$num = intval($getData['num']);
		if( !$ticket_id || !$color || !$size || !$num ){
			_exit('参数有误', 101);
		}
		if( $this->_model->updateMarketNum($ticket_id,$color,$size,$num) ){
			Custom_Log::log($this->_userInfo['id'], "商品入场：ID <b>{$ticket_id}</b> 颜色：<b>{$color}</b> 尺寸：<b>{$size}</b> 数量：<b>{$num}</b>", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
			$skuInfo = $this->_model->getGoodSku($ticket_id, $color, $size);
			_exit('操作成功', 100, $skuInfo[0] );
		}else{
			_exit('操作失败', 102);
		}
	}
	
	public function rollbackToFactoryAction(){
		$getData = $this->_http->getPost();
		$ticket_id = intval( $getData['tid'] );
		$color = $getData['color'];
		$size = $getData['size'];
		$num = intval($getData['num']);
		if( !$ticket_id || !$color || !$size || !$num ){
			_exit('参数有误', 101);
		}
		if( $this->_model->updateRollbackNum($ticket_id,$color,$size,$num) ){
			Custom_Log::log($this->_userInfo['id'], "商品回退厂家：ID <b>{$ticket_id}</b> 颜色：<b>{$color}</b> 尺寸：<b>{$size}</b> 数量：<b>{$num}</b>", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
			$skuInfo = $this->_model->getGoodSku($ticket_id, $color, $size);
			_exit('操作成功', 100, $skuInfo[0] );
		}else{
			_exit('操作失败', 102);
		}
	}
	
	public function rollbackToWarehouseAction(){
		$getData = $this->_http->getPost();
		$ticket_id = intval( $getData['tid'] );
		$color = $getData['color'];
		$size = $getData['size'];
		$num = intval($getData['num']);
		if( !$ticket_id || !$color || !$size || !$num ){
			_exit('参数有误', 101);
		}
		if( $this->_model->updateWarehouseNum($ticket_id,$color,$size,$num) ){
			Custom_Log::log($this->_userInfo['id'], "商品回退后仓：ID <b>{$ticket_id}</b> 颜色：<b>{$color}</b> 尺寸：<b>{$size}</b> 数量：<b>{$num}</b>", $this->pmodule, $this->cmodule, 'edit', 'ticket', $getData['tid']);
			$skuInfo = $this->_model->getGoodSku($ticket_id, $color, $size);
			_exit('操作成功', 100, $skuInfo[0] );
		}else{
			_exit('操作失败', 102);
		}
	}
}