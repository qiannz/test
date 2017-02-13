<?php
class Controller_Admin_Shop extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Shop::getInstance();	
	}
	
	public function listAction() {
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
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
		$this->_tpl->display('admin/shop_list.php');		
	}
	/**
	 * 新增店铺
	 */
	public function addAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$insert_shop_id = $this->_model->postShop($getData);
			if($insert_shop_id) {
				Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'add', 'shop', $insert_shop_id);
				Custom_Common::showMsg(
					'店铺新增成功',
					'back',
					array(
						'add' => '继续新增',
						'list' => '返回店铺列表'
					)
				);				
			}
		}
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$packArray = $this->getPack(0, true, '', $this->_ad_city);
		$picSize = $this->_model->getTheRecommendedPosition('shop', 'shop_head_img', true, $this->_ad_city);
		$this->_tpl->assign('shopheadsize', $picSize);
		
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('packArray', $packArray);
		
		$this->_tpl->display('admin/shop_modi.php');
	}
	/**
	 * 编辑店铺
	 */
	public function editAction() {
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$postEditResult = $this->_model->postShop($getData);
			if($postEditResult) {
				Custom_Log::log($this->_userInfo['id'], serialize($getData), $this->pmodule, $this->cmodule, 'edit', 'shop', $getData['sid']);
				Custom_Common::showMsg(
					'店铺编辑成功',
					'back',
					array(
						'edit/sid:'.$getData['sid'] => '继续编辑',
						'list' => '返回店铺列表'
					)
				);
			}
		}
		$sid = $this->_http->get('sid');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		$row = $this->_model->getShopRow($sid);
		$storeArray = $this->getStore(0, true, false, $this->_ad_city);
		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$packArray = $this->getPack(0, true, '', $this->_ad_city);
		$circleArray = $this->getCircleByRegionId($row['region_id'], false, true, $this->_ad_city);
		$marketArray = $this->getMarketByRidAndCid($row['region_id'], $row['circle_id'], $this->_ad_city);
		$picSize = $this->_model->getTheRecommendedPosition('shop', 'shop_head_img', true, $this->_ad_city);
		$this->_tpl->assign('shopheadsize',$picSize);
		
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('circleArray', $circleArray);
		$this->_tpl->assign('packArray', $packArray);
		$this->_tpl->assign('marketArray', $marketArray);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('page', $page);
		
		$this->_tpl->display('admin/shop_modi.php');
	}
	
	public function staffManagementAction() {
		$sid = $this->_http->get('sid');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$row = $this->_model->getShopRow($sid);
		$data = $this->_model->getShopStaffManagementList($sid, $this->_ad_city);
		
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('page', $page);
		
		$this->_tpl->display('admin/shop_manage_list.php');
	}
	
	public function addStaffAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			
			$result = $this->_model->modiShopCommodity($getData, $this->_ad_city);
			if($result) {
				Custom_Common::showMsg(
					'店员新增成功',
					'',
					array(
						'add-staff/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '继续新增店员',
						'staff-management/sid:' . $getData['sid'] . '/page:' . $getData['page'] => '返回店员管理'
					)
				);
			} else {
				Custom_Common::showMsg('<span style="color:red">店员新增失败</span>');
			}
		}
		
		$sid = intval($this->_http->get('sid'));
		$uid = intval($this->_http->get('uid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		if($sid && $uid) {
			$row = $this->_model->getShopStaffManagementRow($sid, $uid, $this->_ad_city);
			$row['user_info'] = $row['mobile'] ? $row['mobile'] : $row['user_name'];
			$this->_tpl->assign('row', $row);
		}
		
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('page', $page);
		$this->_tpl->display('admin/shop_manage_modi.php');
	}
	/**
	 * 设置店长
	 */
	public function setShopManagerAction() {
		$sid = intval($this->_http->get('sid'));
		$uid = intval($this->_http->get('uid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		
		if($sid && $uid) {
			$this->_db->update('oto_user_shop_commodity', array('user_type' => 1), array('shop_id' => $sid), false);
			
			$this->_db->update('oto_user_shop_commodity', array('user_type' => 2), array('shop_id' => $sid, 'user_id' => $uid));
			Custom_Common::showMsg(
				'店长设置成功',
				'back',
				array(
					'staff-management/sid:' . $sid . '/page:' . $page => '返回店员管理'
				)
			);			
			
		}
		
	}
	/**
	 * 删除店长
	 */
	public function delShopManagerAction() {
		$sid = intval($this->_http->get('sid'));
		$uid = intval($this->_http->get('uid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
	
		if($sid && $uid) {
			$this->_db->delete('oto_user_shop_commodity', array('shop_id' => $sid, 'user_id' => $uid));
			Custom_Common::showMsg(
				'店员删除成功',
				'back',
				array(
					'staff-management/sid:' . $sid . '/page:' . $page => '返回店员管理'
				)
			);
				
		}
	
	}
	/**
	 * 检查本店重复或者他店重复
	 */
	public function checkStaffNameAction() {
		$shop_id = intval($this->_http->get('sid'));
		$uid = intval($this->_http->get('uid'));
		$user_info = $this->_http->get('user_info');
		
		$user_id = 0;
		//手机号码
		if(preg_match('/^1[2-9][0-9]{9}$/', $user_info)) {
			$mianUserInfoRow = Custom_AuthLogin::get_user_by_mobile($user_info);
			if($mianUserInfoRow['GetUserInfosResult'] == 1) {
				$mobileRow = $this->getWebUserId($mianUserInfoRow['userInfo']['UserId']);
				$user_id = $mobileRow['user_id'];
			}
		}
		//用户名
		else {
			$userRow = $this->getUserInfoByUserName($user_info);
			$mobileRow = $this->getWebUserId($userRow['uuid']);
			$user_id = $mobileRow['user_id'];
		}
				
		if(!$user_id) {
			_exit('fail', 200);
		}
		
		//判断是否已经是本店成员
		$sql = "select 1 from `oto_user_shop_commodity` where `shop_id` = '{$shop_id}' and `user_id` = '{$user_id}' limit 1";
		if($this->_db->fetchOne($sql) == 1) {
			_exit('fail', 400);
		}
		//判断是否已经是他店成员				
		$sql = "select 1 from `oto_user_shop_commodity` where `shop_id` <> '{$shop_id}' and `user_id` = '{$user_id}' limit 1";
		if($this->_db->fetchOne($sql) == 1) {
			_exit('fail', 300);
		}
		
		$this->_tpl->assign('mobileRow', $mobileRow);
		$html = $this->_tpl->fetch('admin/ajax/ajax_user.php');
		_exit('sucess', 100, $html);
	}
	/**
	 * 联想获取品牌列表
	 */
	public function getBrandAction() {
		$q = $this->_http->get('q');
		$sql = "select * from `oto_brand` where (`brand_name_zh` like '{$q}%' or `brand_name_en` like '{$q}%') and `city` = '{$this->_ad_city}' order by brand_id asc";
		$qArray = $this->_db->fetchAll($sql);
		$qStr = '';
		foreach ($qArray as $item) {
			if(!empty($item['brand_name_zh']) && !empty($item['brand_name_en'])) {
				$qStr .= $item['brand_name_zh'] . '[' . $item['brand_name_en'] .']' . '|'. "\r\n";
			}elseif(!empty($item['brand_name_zh']) && empty($item['brand_name_en'])) {
				$qStr .= $item['brand_name_zh'] . '|'. "\r\n";
			}elseif(empty($item['brand_name_zh']) && !empty($item['brand_name_en'])) {
				$qStr .= $item['brand_name_en'] . '|'. "\r\n";
			}
		}
		exit($qStr);
	}
	/**
	 * 检查品牌是否存在
	 */
	public function checkBrandNameAction() {
		$brand_name = $this->_http->get('brand_name');
		if ($this->_model->uniqueBrandName($brand_name))
		{
			exit(json_encode(true));
		}
		else
		{
			exit(json_encode(false));
		}
	}
	/**
	 * 检查用户名是否存在
	 */
	public function checkUserNameAction() {
		$user_name = $this->_http->get('user_name');
		if ($this->_model->uniqueUserName($user_name))
		{
			exit(json_encode(true));
		}
		else
		{
			exit(json_encode(false));
		}
	}
	/**
	 * 单删店铺
	 */
	public function delAction() {
		$id = $this->_http->get('id');
		$shop_name = $this->_http->get('sname');	
		$page = $this->_http->get('page');
		$result = $this->_model->del($id);
		if($result) {
			$content = "店铺名称：{$shop_name} 　店铺ID：{$id}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'shop', $id);
			Custom_Common::showMsg('店铺删除成功', '/admin/shop/list/page:' . $page);
		}		
	}
	/**
	 * 批量删除店铺
	 */
	public function delAllAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$idArray = explode(',', $ids);
		foreach($idArray as $id) {
			$result = $this->_model->del($id);
		}
		if($result) {
			$content = "店铺ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'delAll', 'shop', $ids);
			Custom_Common::showMsg('店铺删除成功', '', array('list/page:' . $page => '返回店铺列表'));
		}
	}
	/**
	 * 批量恢复店铺
	 */
	public function unDelAction() {
		$ids = $this->_http->get('id');
		$page = $this->_http->get('page');
		$idArray = explode(',', $ids);
		foreach($idArray as $id) {
			$result = $this->_model->unDel($id);
		}
		if($result) {
			$content = "店铺ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'unDel', 'shop', $ids);
			Custom_Common::showMsg('店铺批量恢复删除成功', '', array('list' => '返回店铺列表'));
		}
	}
	
	/**
	 * 审核店铺
	 */
	public function auditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				$shop_name = $this->getShopName($getData['sid']);
				$content = "店铺名称：{$shop_name} 　店铺ID：.{$getData['sid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit', 'shop', $getData['sid']);
				Custom_Common::showMsg($getData['audit_type'] == 1?'店铺审核通过':'店铺审核不通过', '', array('list/page:' . $getData['page'] => '返回店铺列表'));
			}
		}
		$sid = $this->_http->get('sid');
		$sname = $this->getShopName($sid);
		$page = $this->_http->get('page');
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('sname', $sname);
		$this->_tpl->display('admin/shop_audit.php');
	}
	/**
	 * 合并店铺
	 */
	public function mergeAction() {		
		$data = $this->select('', 'oto_shop_session', '*', 'is_main desc, shop_id desc');		
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('admin/shop_merge.php');
	}
	
	public function fixMergeAction() {
		$getData = $this->_http->getParams();
		$result = $this->_model->merge($getData);
		if($result) {
			$content = "店铺：{$getData['cNameString']} ID：{$getData['cId']} 合并到 店铺：{$getData['mNameString']} ID：{$getData['mId']}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'merge', 'shop', $getData['cId']);
			$this->_db->query("truncate table `oto_shop_session`");
			Custom_Common::showMsg('店铺合并成功', '', array('list' => '返回店铺列表'));
		} else {
			Custom_Common::showMsg('<span style="color:red">店铺合并失败</span>', '', array('list' => '返回店铺列表'));
		}		
	}
	
	public function searchAction() {
		$sname = $this->_http->get('sname');
		$searchArray = $this->_model->getSearch($sname);
		echo json_encode($searchArray);
	}
	/**
	 * 加入合并序列
	 */
	public function setShopSeqAction() {
		$sids = $this->_http->get('sid');
		$sidsArray = explode(',', $sids);
		foreach($sidsArray as $sid) {
			if(!$this->_model->replace($sid)) {
				continue;
			}
		}
		die('ok');
	}
	/**
	 * 清空合并序列
	 */
	public function usetShopSeqAction() {
		$this->_db->query("truncate table `oto_shop_session`");
		die('ok');
	}
	
	public function delShopSessionAction() {
		$sid = $this->_http->get('sid');
		$result = $this->_model->delShopSession($sid);
		if($result) {
			die('ok');
		}
	}
	
	public function checkShopNameAction() {
		$shop_name = $this->_http->get('shop_name');
		$shop_name = Custom_String::HtmlReplace($shop_name, -1);
		$shop_id = intval($this->_http->get('sid'));
		if(Model_Home_Shop::getInstance()->repeatShop($shop_name, $shop_id, $this->_ad_city)) {
			exit(json_encode(false));
		} else {
			exit(json_encode(true));
		}
	}
	
	public function checkShopAddressAction() {
		$shop_address = $this->_http->get('shop_address');
		$shop_address = Custom_String::HtmlReplace($shop_address, -1);
		if(!$this->getLatitudeAndLongitudeFormamap($shop_address, $this->_ad_city)) {
			exit(json_encode(false));
		} else {
			exit(json_encode(true));
		}
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
			$img_url = Custom_Upload::singleImgUpload($_FILES[$postData['type']],'shop');
			if(!$img_url){
				echo json_encode(array('msg'=>103));exit;
			}else{
				echo json_encode(array('msg'=>100 ,'img_url'=>$img_url , 'url' =>$GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/shop/'.$img_url ));exit;
			}
		}
	}
	/**
	 * 店铺是否有主
	 */
	public function isOwnerAction() {
		$shop_id = intval($this->_http->get('sid'));
		$ownerArray = $this->_model->isHasOwner($shop_id);
		if($ownerArray) {
			_exit('sucess', 100, $ownerArray);	
		}
		_exit('fail', 300);
	}
	
	public function getMarketAction() {
		$region_id = $this->_http->get('region_id');
		$circle_id = $this->_http->get('circle_id');
		$marketArray = $this->getMarketByRidAndCid($region_id, $circle_id, $this->_ad_city);
		$marketArray = !$marketArray ? array() : $marketArray;
		exit(json_encode($marketArray));
	}	
}
