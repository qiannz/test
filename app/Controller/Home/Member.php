<?php
/**
 * 商户入驻
 * @author qiannz
 *
 */
class Controller_Home_Member extends Controller_Home_Abstract {
	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Member::getInstance();
		
		//获取分类导航
		$navList = $this->getNavList();
		$this->_tpl->assign('navList', $navList);
		
		
	}
	
	public function joinAction() {
		if($this->_http->isPost() && $this->_user_id) {
			$getData = $this->_http->getPost();
			//商户申请
			if(intval($getData['step']) == 1) {
				$this->merchantApplication($getData, $_FILES);
				header301('/home/member/join/sid/' . $getData['sid']);
			} 
			//资料补全
			elseif(intval($getData['step']) == 5) {
				$this->merchantDataCompletion($getData, $_FILES);
				header301('/home/member/join/sid/' . $getData['sid']);
			}	
		}
		
		if(!$this->_user_id) {
			$step = 0; //未登录状态
		} else {			
			if($this->_http->has('sid')) {
				$sid = intval($this->_http->get('sid'));
				$shopRow = $this->select("`shop_id` = '{$sid}'", 'oto_shop', 'shop_name,shop_address', '', true);
				$this->_tpl->assign('shopRow', $shopRow);
			} else {
				$merchantNum = $this->_model->getMerchantNumByUserId($this->_user_id);
				if($merchantNum > 1) {
					$merchantArray = $this->select("`user_id` = '".$this->_user_id."'", 'oto_merchant_app', 'shop_id, shop_name', 'shop_id asc');
					$this->_tpl->assign('merchantNum', $merchantNum);
					$this->_tpl->assign('merchantArray', $merchantArray);
				}
			}
			$memberRow = $this->_model->getMerchantAppByUserId($this->_user_id, $sid);
			if(empty($memberRow) || $memberRow['auth_status'] == 0) {
				$step = 1; //待申请状态
			} else {
				if($this->_http->getQuery('step')) {
					if($this->_http->getQuery('step') == 1) {
						$step = 1; //重新提交审核
					} elseif ($this->_http->getQuery('step') == 5) {
						if($memberRow['auth_status'] == 2) {
							$step = 5; //补全资料第一步
						} elseif($memberRow['auth_status'] == 3) {
							$step = 6; //补全资料第二步，等待支付
						} elseif($memberRow['auth_status'] == 4) {
							$step = 7; //已支付，入驻成功
						}
					} elseif ($this->_http->getQuery('step') == 6) {
						$step = 5; //补全资料第一步
					}	
				} else {
					if($memberRow['auth_status'] == 1) {
						$step = 2; //待审核状态
					} elseif($memberRow['auth_status'] == -1) {
						$step = 3; //审核不通过
					} elseif($memberRow['auth_status'] == 2) {
						$step = 4; //审核通过
					}elseif($memberRow['auth_status'] == 3) {
						$step = 6; ////补全资料第二步，等待支付
					}elseif($memberRow['auth_status'] == 4) {
						$step = 7; //已支付，入驻成功
					}					
				}
			}
		}
		$storeArray = $this->getStore();
		$packArray = $this->getPack();
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('packArray', $packArray);
		$this->_tpl->assign('step', $step);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('memberRow', $memberRow);
		$this->_tpl->display('member/join.php');
	}
	/**
	 * 商户申请
	 * @param unknown_type $getData
	 */
	private function merchantApplication($getData, & $_file) {
		$errMsg = '';
		if(empty($getData['real_name'])) {
			$errMsg .= '请输入真实姓名'."\r\n";
		}
			
		if(empty($getData['mobile'])) {
			$errMsg .= '请输入手机号码'."\r\n";
		}
		
		if(!empty($getData['mobile']) && !preg_match('/^1[0-9]{10}$/', $getData['mobile'])) {
			$errMsg .= '请输入正确的手机号码'."\r\n";
		}

		if(empty($getData['shop_name'])) {
			$errMsg .= '请输入店铺名称'."\r\n";
		}
			
		if(empty($getData['shop_address'])) {
			$errMsg .= '请输入店铺地址'."\r\n";
		}
		if($errMsg == '') {
			//判断店铺是否重复
			if(!$getData['sid'] && Model_Home_Shop::getInstance()->repeatShop(Custom_String::HtmlReplace(trim($getData['shop_name']), -1))) {
				Custom_Common::showMsg('很抱歉，你录入的店铺名称重复，请换一个再试', 'back');
			}
			$this->_model->replaceMerchantApp($getData, $this->_userInfo, $_file);
		} else {
			_exit($errMsg, 300);
		}	
	}
	/**
	 * 上海资料补全
	 * @param unknown_type $getData
	 * @param unknown_type $_file
	 */
	private function merchantDataCompletion($getData, & $_file) {
		$errMsg = '';
		if(empty($getData['store_id'])) {
			$errMsg .= '请选择所属分类'."\r\n";
		}
			
		if(empty($getData['bname'])) {
			$errMsg .= '请输入主营品牌'."\r\n";
		}

		if(empty($getData['pack_id'])) {
			$errMsg .= '请选择套餐'."\r\n";
		}
		
		if(empty($getData['alipay_acount'])) {
			$errMsg .= '请输入支付宝账户名'."\r\n";
		}
			
		if(empty($getData['alipay_name'])) {
			$errMsg .= '请输入账户户主姓名'."\r\n";
		}
		if($errMsg == '') {
			$this->_model->updateMerchantDataCompletion($getData, $this->_userInfo, $_file);
		} else {
			_exit($errMsg, 300);
		}		
	}
	
	/**
	 * 联想获取品牌列表
	 */
	public function getBrandAction() {
		$q = Custom_String::HtmlReplace($this->_http->get('q'), 2);
		exit($this->assocGetBrand($q));
	}
	
	/**
	 * 检查品牌是否存在
	 */
	public function checkBrandNameAction() {
		$bname = Custom_String::HtmlReplace($this->_http->get('bname'), 2);
		$bname = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $bname);
		if (Model_Home_Suser::getInstance()->uniqueBrandName($bname))
		{
			exit(json_encode(true));
		}
		else
		{
			exit(json_encode(false));
		}
	}
}