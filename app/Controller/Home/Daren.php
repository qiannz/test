<?php
class Controller_Home_Daren extends Controller_Home_Abstract {

	/**
	 * 专题详情
	 */
	public function wapShowAction(){
		$special_id = $this->_http->has('sid')?$this->_http->get('sid'):0;
		if (!$special_id) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		$specialRow = $this->select("special_id={$special_id}","special_content","*","",1);
		if( empty($specialRow) ){
			Custom_Common::jumpto('/404/404.html');
		}
		$pos_id = Model_Api_App::getInstance()->getPosIdByMark($specialRow['city'], 'discount', 'discount_banner');
		$recRow = $this->select("`pos_id` = '{$pos_id}' AND `come_from_type`=6 AND `come_from_id`='{$special_id}'", 'oto_recommend', '*', 'sequence asc, created desc', 1);
		if( $recRow["img_url"] ){
			$share_img_url =  $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $recRow["img_url"];
		}else if( $specialRow["cover_img"] ){
			$share_img_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' .$specialRow["cover_img"];
		}else{
			$wapImgRow = $this->select( "`special_id` = '{$special_id}'", 'special_img', '*', 'sequence asc, created asc',true);
			$share_img_url = $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/id/{$wapImgRow["id"]}/w/200/type/special";
		}
		$shareInfo = array("share_title"   => $recRow["title"] . '_名品导购',
						   "share_desc"    =>  $recRow["summary"],
				           "share_img_url" =>  $share_img_url
				     );
		$this->_tpl->assign('share',$shareInfo);
		$weixinKeyArr = Model_Active_Oneyuanpurchase::getInstance()->getWeixinKey($GLOBALS['GLOBAL_CONF']['SITE_URL'] . $_SERVER['REQUEST_URI']);
		$this->_tpl->assign('weixinKeyArr', $weixinKeyArr);
		$this->_tpl->assign("daren",$specialRow);
		$this->_tpl->assign("sid",$special_id);
		$this->_tpl->display('daren/daren.php');
	}
	/**
	 * 达人说详情页面
	 */
	public function darenShowAction() {
		$special_id = $this->_http->has('sid') ? (int) $this->_http->get('sid') : 0;
		$specialRow = $this->select("special_id = '{$special_id}'", "special_content", "*", "", true);
		if( empty($specialRow) ){
			Custom_Common::jumpto('/404/404.html');
		}
		$goodList = Model_Api_Discount::getInstance()->specialGoodMore(array("sid" => $special_id), $this->_city, 16);
		$pos_id = Model_Api_App::getInstance()->getPosIdByMark($specialRow['city'], 'app_home_version_six', 'app_home_six_daren');
		$recRow = $this->select("`pos_id` = '{$pos_id}' AND `come_from_type`=6 AND `come_from_id`='{$special_id}'", 'oto_recommend', '*', 'sequence asc, created desc', 1);
		if( $recRow["img_url"] ){
			$share_img_url =  $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $recRow["img_url"];
		}else if( $specialRow["cover_img"] ){
			$share_img_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' .$specialRow["cover_img"];
		}else{
			$wapImgRow = $this->select( "`special_id` = '{$special_id}'", 'special_img', '*', 'sequence asc, created asc',true);
			$share_img_url = $GLOBALS ['GLOBAL_CONF'] ['SITE_URL'] . "/api/good/get-img-thumb/id/{$wapImgRow["id"]}/w/200/type/special";
		}
		$shareInfo = array("share_title"    => 'MP达人说：'.$recRow["title"] . '_名品导购',
						   "share_desc"    =>  $recRow["summary"] ? $recRow["summary"] : $recRow["title"],
				           "share_img_url" =>  $share_img_url
					);
		
		//手机端分享 header
		header("MplifeShareWeixinTitle : ".urlencode($shareInfo['share_title']));
		header("MplifeShareWeixinDesc : ". urlencode($shareInfo['share_desc']));
		header("MplifeShareWeixinUrl : " . $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/daren/daren-show/sid/' . $special_id);
		header("MplifeShareWeixinImageUrl : {$shareInfo['share_img_url']}");
		
		$this->_tpl->assign('share',$shareInfo);
		$weixinKeyArr = Model_Active_Oneyuanpurchase::getInstance()->getWeixinKey($GLOBALS['GLOBAL_CONF']['SITE_URL'] . $_SERVER['REQUEST_URI']);
		$this->_tpl->assign('weixinKeyArr', $weixinKeyArr);
		$this->_tpl->assign("goodList", $goodList);
		$this->_tpl->assign("specialRow", $specialRow);
		$this->_tpl->assign("sid",$special_id);
		$this->_tpl->display("daren/daren_show.php");
	}
	/**
	 * 猜你喜欢
	 */
	public function loveMoreAction() {
		$getData = $this->_http->getParams();
		$data = Model_Api_Appversionsix::getInstance()->getLoveList($getData, $this->_city);
		exit(json_encode($data));
	}	
	
	/**
	 * 达人说列表
	 */
	public function darenListAction() {
		$getData = $this->_http->getParams();
		$data = Model_Api_App::getInstance()->getListByMark($this->_city, 'app_home_version_six', 'app_home_six_daren', 5);
		exit(json_encode($data));
	}	
	/**
	 * 获取折扣相关的商品
	 */
	public function goodListAction(){
		$special_id = $this->_http->has('sid')?$this->_http->get('sid'):0;
		$page = $this->_http->has('page')?$this->_http->get('page'):1;
		$goodList = Model_Api_Discount::getInstance()->specialGoodMore(array("sid"=>$special_id,"page"=>$page),$this->_city,16);
		exit(json_encode($goodList));
	}
	
}