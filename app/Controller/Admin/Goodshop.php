<?php
class Controller_Admin_Goodshop extends Controller_Admin_Abstract {
	
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Good::getInstance();
	}
	
	public function listAction() {
		$page = $this->_http->get('page');
		$page = !$page ? 1 : intval($page);
		
		$page_str = '';
		$getData = $this->_http->getParams();
		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'page', 'isd'))) {
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
		
		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$circleArray = $this->getCircleByRegionId($getData['region_id'], false, true, $this->_ad_city);
		$shopArray = $this->getShop($getData['region_id'], $getData['circle_id'], $this->_ad_city);
		
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('data', $data);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('circleArray', $circleArray);
		$this->_tpl->assign('shopArray', $shopArray);
		$this->_tpl->assign('request', $_REQUEST);
		$this->_tpl->assign('page_str', trim($page_str, '/'));
		$this->_tpl->display('admin/good_shop_list.php');		
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
						$getData['page_str'] ? 'edit/'. $getData['page_str'] .'/gid:'.$getData['gid'] : 'edit/gid:'.$getData['gid'] => '继续编辑',
						$getData['page_str'] ? 'list/'.$getData['page_str'] : 'list' => '返回商品列表'
					)
				);
			}
		}
		
		$getData = $this->_http->getParams();		
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'gid'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		$good_id = $this->_http->get('gid');	
		$row = $this->_model->getGoodRow($good_id);
		$imgList = $this->_model->getImgList($good_id);
		$regionArray = $this->getRegion(0, true, $this->_ad_city);
		$circleArray = $this->getCircleByRegionId($row['region_id'], false, true, $this->_ad_city);
		$shopArray = $this->getShop($row['region_id'], $row['circle_id'], $this->_ad_city);
	
		$this->_tpl->assign('page_str', substr($page_str, 0, -1));
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('circleArray', $circleArray);
		$this->_tpl->assign('shopArray', $shopArray);
		$this->_tpl->assign('row', $row);
		$this->_tpl->assign('imgList', $imgList);
	
		$this->_tpl->display('admin/good_edit.php');
	}	
	/**
	 * 单个删除
	 */
	public function delAction() {
		$id = $this->_http->get('id');
		$good_name = $this->_http->get('gname');
		$page = $this->_http->get('page');
		
		$getData = $this->_http->getParams();
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'gname', 'id'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		$result = $this->_model->del($id);
		if($result) {
			$content = "商品名称：{$good_name} 　商品ID：{$id}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'del', 'good', $id);
			Custom_Common::showMsg('商品删除成功', '/admin/goodshop/list/' . substr($page_str, 0, -1));
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
		
		$getData = $this->_http->getParams();
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'id'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		if($result) {
			$content = "商品ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'delAll');
			Custom_Common::showMsg('商品删除成功', '', array('list/' . substr($page_str, 0, -1) => '返回商品店铺列表'));
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
		
		$getData = $this->_http->getParams();
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'id'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		if($result) {
			$content = "商品ID：{$ids}";
			Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'unDel');
			Custom_Common::showMsg('商品批量恢复成功', '', array('list/' . substr($page_str, 0, -1) => '返回商品店铺列表'));
		}
	}
	/**
	 * 审核商品
	 */
	public function auditAction() {		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$result = $this->_model->audit($getData);
			if($result) {
				$content = "商品名称：{$getData['gname']}　商品ID：{$getData['gid']} " . ($getData['audit_type'] == 1?'<span style="color:green">审核通过</span>':'<span style="color:red">审核不通过</span>');
				Custom_Log::log($this->_userInfo['id'], $content, $this->pmodule, $this->cmodule, 'audit');
				Custom_Common::showMsg($getData['audit_type'] == 1?'商品审核通过':'商品审核不通过', '', array('list/'. $getData['page_str'] => '返回商品列表'));
			}
		}
		
		$getData = $this->_http->getParams();
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'gid', 'gname'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		$gid = $this->_http->get('gid');
		$gname = $this->_http->get('gname');
		$this->_tpl->assign('gid', $gid);
		$this->_tpl->assign('gname', $gname);	
		$this->_tpl->assign('page_str', substr($page_str, 0, -1));
		
		$this->_tpl->display('admin/good_shop_audit.php');
	}
	
	public function topAction() {
		$id = $this->_http->get('id');
		$gname = $this->_http->get('gname');
		$page = $this->_http->get('page');
		$result = $this->_model->top($id);
		
		$getData = $this->_http->getParams();
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'gid', 'gname'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		if($result) {
			Custom_Log::log($this->_userInfo['id'], '置顶成功', $this->pmodule, $this->cmodule, 'top', 'good', $id);
			Custom_Common::showMsg('商品置顶成功', '', array('list/' . substr($page_str, 0, -1) => '返回商品店铺列表'));
		}
	}
	
	public function unTopAction() {
		$id = $this->_http->get('id');
		$gname = $this->_http->get('gname');
		$page = $this->_http->get('page');
		$result = $this->_model->unTop($id);
		
		$getData = $this->_http->getParams();
		foreach($getData as $key => $value) {
			if(!in_array($key, array('m', 'c', 'act', 'con', 'gid', 'gname'))) {
				$page_str .= "{$key}:{$value}/";
			}
		}
		
		if($result) {
			Custom_Log::log($this->_userInfo['id'], '取消置顶成功', $this->pmodule, $this->cmodule, 'top', 'good', $id);
			Custom_Common::showMsg('商品取消置顶成功', '', array('list/' . substr($page_str, 0, -1) => '返回商品店铺列表'));
		}
	}
}