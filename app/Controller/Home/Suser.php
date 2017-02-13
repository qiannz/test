<?php
class Controller_Home_Suser extends Controller_Home_Abstract {
	private $_model;
	private $_userSync;
	private $_page;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Suser::getInstance();
		if(!$this->_user_id) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		if($this->_userInfo['user_type'] == 1) {
			Custom_Common::showMsg('当前模块，只征对 认证商户/营业员开放');
		}	
		$this->_page = 10;
	}
	
	private function getOneShopIdByUserId($sid, $user_id) {
		$myShopArray = array();
		
		$shopList = $this->_model->getPermissionShopByUserId($user_id, $this->_city);
		$shopListFirstRow = current($shopList);
				
		$myShopArray = array_keys($shopList);
		
		if($sid) {
			if(in_array($sid, $myShopArray)) {
				$shop_id = $sid;
			} else {
				header301('/home/suser/my-good/sid/' . $shopListFirstRow['shop_id']);
			}
		} else {
			$shop_id = $shopListFirstRow['shop_id'];
		}
		
		if(empty($shopList) || !$shop_id) $this->ifNotShops();
		
				
		if($this->_userInfo['user_type'] == 3) {
				$competenceArray = explode(',', $shopList[$shop_id]['competence']);
				
				
				$allowPermissions = array('my-good', 'add', 'shop-edit', 'coupon-list', 'valid', 'goods-auth', 'good-edit', 'good-del');
				if(!in_array($this->_action, $allowPermissions)) {
					$this->ifNotPermissions();
				}
				
				
				//控制权限
				switch ($this->_action) {
					case 'goods-auth':
						if(!in_array(1, $competenceArray)) {
							$this->ifNotPermissions();
						}
						break;
					case 'good-edit':
						if(!in_array(2, $competenceArray)) {
							$this->ifNotPermissions();
						}
						break;
					case 'good-del':
						if(!in_array(3, $competenceArray)) {
							$this->ifNotPermissions();
						}
						break;
					case 'valid':
						if(!in_array(4, $competenceArray)) {
							$this->ifNotPermissions();
						}
						break;
					case 'shop-edit':
						if(!in_array(5, $competenceArray)) {
							$this->ifNotPermissions();
						}
						break;
				}
				
				$this->_tpl->assign('userPermission', $competenceArray);
		}
		
		//拆分店铺权限toArray
		foreach ($shopList as & $shopItem) {
			$shopItem['competence'] = explode(',', $shopItem['competence']);
		}
		
		//如果不是当前店铺不具备旗舰店权限则跳转至店铺商品管理列表
		foreach ($shopList as & $shopItemRow) {			
			if($shopItemRow['shop_id'] == $shop_id) {
				if($shopItemRow['is_flag'] == 0 && ($this->_action == 'shop-decoration' || $this->_action == 'shop-decoration-add')) {
					header301('/home/suser/my-good/sid/' . $shop_id);
				}
				$this->_tpl->assign('shopRow', $shopItemRow);
				break;
			}
		}
		
		$this->_tpl->assign('shopList', $shopList);	
		
		return $shop_id;
	}
	/**
	 * 当认证用户没有店铺时
	 */
	private function ifNotShops() {
		Custom_Common::showMsg('抱歉，你当前还没有店铺！');
	}

	/**
	 * 当前用户没有权限时
	 */
	private function ifNotPermissions() {
		Custom_Common::showMsg('抱歉，你的权限不够！');
	}
	
	public function myGoodAction() {
		$getData = $this->_http->getParams();		
		$sid = intval($this->_http->get('sid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$getData['shop_id'] = $shop_id;
		$this->_model->setWhere($getData);
		$myGoodList = $this->_model->getMyGoodList($this->_userInfo['user_id'], $shop_id, $page, $this->_page);
		
		$this->_tpl->assign('sid', $shop_id);	
		$this->_tpl->assign('myGoodList', $myGoodList);
		$this->_tpl->assign('request', stripslashes_deep($getData));
		$this->_tpl->display('center/suser/my_good.php');
	}
	/**
	 * 验证记录
	 */
	public function validRecordAction() {
		$getData = $this->_http->getParams();
		$sid = intval($getData['sid']);
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$getData['shop_id'] = $shop_id;
				
		$myValidRecordList = $this->_model->getValidRecordList($getData, $shop_id, $page, $this->_page);
		//获取验证店铺
		$validShopArray = $this->_model->getValidShopList($shop_id);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('myValidRecordList', $myValidRecordList);
		$this->_tpl->assign('validShopArray', $validShopArray);
		$this->_tpl->assign('request', stripslashes_deep($getData));
		$this->_tpl->display('center/suser/valid_record.php');		
	}
	
	public function goodEditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
			if (empty($getData['sid'])) {
				$errMsg .= '店铺不能为空'."\r\n";
			}
			
			if (empty($getData['gid'])) {
				$errMsg .= '商品不能为空'."\r\n";
			}
				
			if(empty($getData['good_name'])) {
				$errMsg .= '商品标题不能为空'."\r\n";
			}
			
			if(!empty($getData['good_name']) && mb_strlen($getData['good_name'], 'utf8') > 30) {
				$errMsg .= '商品标题最多30个字符，汉字算一个字符'."\r\n";
			}
			
			if(empty($getData['dis_price'])) {
				$errMsg .= '商品现价不能为空'."\r\n";
			}
				
			if(!empty($getData['dis_price']) && !is_numeric($getData['dis_price'])) {
				$errMsg .= '商品现价要为整数'."\r\n";
			}
							
			if($errMsg == '') {
				$editResult = $this->_model->editGood($getData);
				if ($editResult) {
					Custom_Common::showMsg(
						'恭喜，你的商品编辑成功！',
						'',
						array(
						'good-edit/sid/'. $getData['sid'] .'/gid/' . $getData['gid'] => '重新编辑',
						'my-good/sid/' . $getData['sid'] => '返回店铺商品管理页'
						)
					);
				}				
			} else {
				Custom_Common::showMsg(
					'系统繁忙，请稍后再试！',
					'',
					array(
					'good-edit/sid/'. $getData['sid'] .'/gid/' . $getData['gid'] => '重新编辑',
					'my-good/sid/' . $getData['sid'] => '返回店铺商品管理页'
					)
				);
			}
		}
		$gid = $this->_http->get('gid');
		$sid = $this->_http->get('sid');
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$goodInfo = $this->select("`good_id` = '{$gid}'", 'oto_good', '*', '', true);
		$goodInfo['dis_price'] = floatval($goodInfo['dis_price']);
		$goodInfo['org_price'] = floatval($goodInfo['org_price']);
		$shopArray = $this->getShop($goodInfo['region_id'], $goodInfo['circle_id']);
		$imgList = Model_Admin_Good::getInstance()->getImgList($gid);
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('gid', $gid);
		$this->_tpl->assign('shopArray', $shopArray);
		$this->_tpl->assign('goodInfo', $goodInfo);
		$this->_tpl->assign('imgList', $imgList);
		$this->_tpl->display('center/suser/good_edit.php');
	}
	
	public function addAction() {
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
			if (empty($getData['shop_id'])) {
				$errMsg .= '店铺不能为空'."\r\n";
			}
			
			if(empty($getData['good_name'])) {
				$errMsg .= '商品标题不能为空'."\r\n";
			}
				
			if(!empty($getData['good_name']) && mb_strlen($getData['good_name'], 'utf8') > 30) {
				$errMsg .= '商品标题最多30个字符，汉字算一个字符'."\r\n";
			}
				
			if(empty($getData['dis_price'])) {
				$errMsg .= '商品现价不能为空'."\r\n";
			}
			
			if(!empty($getData['dis_price']) && !is_numeric($getData['dis_price'])) {
				$errMsg .= '商品现价要为整数'."\r\n";
			}
			
			if(empty($getData['img'])) {
				$errMsg .= '商品照片不能为空'."\r\n";
			}
			
			if($errMsg == '') {
				$this->_model->merchantGoodsUploadLimit($getData['shop_id'], $this->_city);//套餐上线控制
				$shopInfo = $this->select("`shop_id` = '{$getData['shop_id']}'", 'oto_shop', '*', '', true);
				$getData['user_name'] = $this->_userInfo['user_name'];
				$getData['user_id'] = $this->_userInfo['user_id'];
				$getData['shop_name'] = $shopInfo['shop_name'];
				$getData['region_id'] = $shopInfo['region_id'];
				$getData['circle_id'] = $shopInfo['circle_id'];
				$getData['address'] = $shopInfo['shop_address'];
				$getData['is_auth'] = 1;
				if($this->_http->submitCheckRefresh()) {
					$insert_good_id = Model_Home_Good::getInstance()->submitGood($getData, $this->_city); 
					if($insert_good_id) {
						$this->updateUser(CLIENT_IP, $this->_userInfo['user_id']);
						$this->updateQuantityTotalGoodByUserId($this->_userInfo['user_id']);
						Custom_Common::showMsg(
							'恭喜，你的新商品添加成功！',
							'',
							array(
								'add/sid/' . $getData['shop_id'] => '继续新增',
								'my-good/sid/' . $getData['shop_id'] => '返回店铺商品管理页'
							)
						);
					}
				} else {
					Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/suser/my-good/sid/' . intval($getData['shop_id']));
				}
			} else {
				Custom_Common::showMsg(
					'系统繁忙，请稍后再试！',
					'',
					array(
						'add/sid/' . $getData['shop_id'] => '重新上传商品',
						'my-good/sid/' . $getData['shop_id'] => '返回店铺商品管理页'
					)
				);
			}
		}
				
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$this->_model->merchantGoodsUploadLimit($shop_id, $this->_city);//套餐上线控制
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('formhash', $this->_http->formHashRefresh());
		$this->_tpl->display('center/suser/my_good_add.php');
	}
	
	public function delImgAction() {
		$aid = $this->_http->get('aid');
		if(Custom_Upload::imageDelete($aid)) {
			echo json_encode(array('status' => 'ok'));
		}
	}
	
	public function addShopAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
			if(empty($getData['sname'])) {
				$errMsg .= '店铺名称不能为空'."\r\n";
			}
			
			if(!empty($getData['sname']) && mb_strlen($getData['sname'], 'utf8') > 30) {
				$errMsg .= '店铺名称最多30个字符，汉字算一个字符'."\r\n";
			}
			
			if(empty($getData['rid'])) {
				$errMsg .= '请选择店铺所在的区'."\r\n";
			}
			
			if(empty($getData['cid'])) {
				$errMsg .= '请选择店铺所在的商圈'."\r\n";
			}
			
			if(empty($getData['ad'])) {
				$errMsg .= '请输入店铺所在的详细地址'."\r\n";
			}
			
			if(empty($getData['bname'])) {
				$errMsg .= '请输入品牌名称'."\r\n";
			}
			
			if(!empty($getData['bname']) && !$this->_model->uniqueBrandName($getData['bname'])) {
				$errMsg .= '请输入正确的品牌名称'."\r\n";
			}
			
			if(empty($getData['stid'])) {
				$errMsg .= '请选择店铺所属分类'."\r\n";
			}
			
			if(!empty($getData['not']) && mb_strlen($getData['not'], 'utf8') > 100 ) {
				$errMsg .= '店铺公告100个字以内（汉字算一个字符）'."\r\n";
			}
			
			if($errMsg == '') {
				$insert_id = $this->_model->addShop($getData, $this->_userInfo);
				if($insert_id) {
					_exit('店铺新增成功', 100, array('sid' => $insert_id));
				}
			} else {
				_exit($errMsg, 300);
			}
			exit();
		}
		$storeArray = $this->getStore();				
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->display('center/suser/add_shop.php');
	}
	
	/**
	 * 店铺编辑
	 */
	public function shopEditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
			if(!empty($getData['not']) && mb_strlen($getData['not'], 'utf8') > 100 ) {
				$errMsg .= '店铺公告100个字以内（汉字算一个字符）'."\r\n";
			}			
			
			if($errMsg == '') {
				$update_id = $this->_model->editShopNotice($getData, $this->_userInfo);
				if($update_id) {
					_exit('店铺编辑成功', 100, array('sid' => $getData['shop_id']));
				} else {
					_exit('店铺编辑失败', 300);
				}
			} else {
				_exit($errMsg, 300);
			}
		}
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$shopInfo = $this->getShopFieldById($shop_id);
		
		$storeArray = $this->getStore(0, true, false, $this->_city);
		$regionArray = $this->getRegion(0, true, $this->_city);
		$circleArray = $this->getCircleByRegionId($shopInfo['region_id'], false, true, $this->_city);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('regionArray', $regionArray);
		$this->_tpl->assign('circleArray', $circleArray);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('shopInfo', $shopInfo);
		$this->_tpl->display('center/suser/shop_edit.php');
	}
	
	/**
	 * 优惠卷管理
	 */
	public function couponListAction() {
		$sid = $this->_http->get('sid');
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$myCouponList = $this->_model->getMyCouponList($this->_userInfo['user_id'], $shop_id, $page, $this->_page);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('myCouponList', $myCouponList);
		$this->_tpl->display('center/suser/coupon_list.php');
	}
	/**
	 * 发券
	 */
	public function addCouponAction() {
		$coupon_type = $this->_http->has('ctype') ?  (in_array($this->_http->get('ctype'), array('coupon', 'voucher')) ? $this->_http->get('ctype') : 'coupon') : 'voucher';
		
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
			
			if($coupon_type == 'voucher') { //活动名称
				if(empty($getData['activity_name'])) {
					$errMsg .= '请输入活动名称'."\r\n";
				}
			}
			
			if(empty($getData['t_title'])) {
				$errMsg .= '请输入券标题'."\r\n";
			}
			
			if(!empty($getData['t_title']) && mb_strlen($getData['t_title'], 'utf8') > 30) {
				$errMsg .= '券标题最多30个字符，汉字算一个字符'."\r\n";
			}
			
			if(empty($getData['p_value'])) {
				if($coupon_type == 'coupon') {
					$errMsg .= '请输入券面值'."\r\n";
				} elseif($coupon_type == 'voucher') {
					$errMsg .= '请输入券原价'."\r\n";
				}
			}
			
/* 			if(!empty($getData['p_value']) && !preg_match('/[1-9][0-9]*$/', $getData['p_value'])) {
				if($coupon_type == 'coupon') {
					$errMsg .= '券面值为正整数'."\r\n";
				} elseif($coupon_type == 'voucher') {
					$errMsg .= '券原价为正整数'."\r\n";
				}
			} */

			if($coupon_type == 'voucher') { //券售价
				if(empty($getData['s_value'])) {
					$errMsg .= '请输入券售价'."\r\n";
				}
			
/* 				if(!empty($getData['s_value']) && !preg_match('/[1-9][0-9]*$/', $getData['s_value'])) {
					$errMsg .= '券售价为正整数'."\r\n";
				} */
			}
						
			if(empty($getData['total'])) {
				$errMsg .= '请输入券数量'."\r\n";
			}			
				
			if(!empty($getData['total']) && !preg_match('/[1-9][0-9]*$/', $getData['total'])) {
				$errMsg .= '券数量为正整数'."\r\n";
			}

			if($coupon_type == 'voucher') { //限购
				if(empty($getData['climit'])) {
					$errMsg .= '请输入限购数量'."\r\n";
				}
					
				if(!empty($getData['climit']) && !preg_match('/[1-9][0-9]*$/', $getData['climit'])) {
					$errMsg .= '限购数量为正整数'."\r\n";
				}
			}
						
			if(empty($getData['sdate'])) {
				if($coupon_type == 'coupon') {
					$errMsg .= '请输入券领取有效期开始时间'."\r\n";
				} elseif($coupon_type == 'voucher') {
					$errMsg .= '请输入券销售有效期开始时间'."\r\n";
				} 
			}
			
			if(empty($getData['edate'])) {
				if($coupon_type == 'coupon') {
					$errMsg .= '请输入券领取有效期结束时间'."\r\n";
				} elseif($coupon_type == 'voucher') {
					$errMsg .= '请输入券销售有效期结束时间'."\r\n";
				} 
			}
			
			if(empty($getData['stime'])) {
				$errMsg .= '请输入使用有效期开始时间'."\r\n";
			}
				
			if(empty($getData['etime'])) {
				$errMsg .= '请输入使用有效期结束时间'."\r\n";
			}
			
			if(empty($getData['summary'])) {
				$errMsg .= '请输入券简介'."\r\n";
			}
				
			if(!empty($getData['summary']) && mb_strlen($getData['summary'], 'utf8') > 70) {
				$errMsg .= '券简介最多70个字符，汉字算一个字符'."\r\n";
			}
			
			if(empty($_FILES['file_img'])) {
				$errMsg .= '请上传封面图片'."\r\n";
			} else {
				$imgInfo = getimagesize($_FILES['file_img']['tmp_name']);
				if ($imgInfo['0'] != 640 || $imgInfo['1'] != 300) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 640 × 300 的图片'."\r\n";
				} else {
					$cover_img = Custom_Upload::singleImgUpload($_FILES['file_img'], 'cover');
					$getData['cover_img'] = $cover_img;	
				}			
			}
			
			if(empty($getData['content'])) {
				$errMsg .= '请输入券使用说明'."\r\n";
			}
			
			if($errMsg == '') {
				if($this->_http->submitCheckRefresh()) {
					$this->_model->merchantCouponsUploadLimit($getData['sid'], $this->_city); //套餐上线控制
					$addResult = $this->_model->addTicket($getData, $this->_userInfo, $this->_city);
					if($addResult) {
						Custom_Common::showMsg(
							'恭喜，你的新券添加成功！',
							'',
							array(
								'add-coupon/ctype/' . $coupon_type . '/sid/'. $getData['sid']   => '继续新增券',
								'/home/suser/coupon-list/sid/' . $getData['sid'] => '返回券管理'
							)
						);
					} else {
						Custom_Common::showMsg('系统忙，请稍后再试');
					}
				} else {
					Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/suser/coupon-list/sid/' . $getData['sid']);
				}
			} else {
				Custom_Common::showMsg(nl2br($errMsg), 'back');
			}
		}
				
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$this->_model->merchantCouponsUploadLimit($shop_id, $this->_city); //套餐上线控制
		$activityArray = $this->_model->getActivityList($this->_user_id);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('sortArray', $this->getTicketSortById(0, 'ticketsort'));
		$this->_tpl->assign('coupon_type', $coupon_type);
		$this->_tpl->assign('activityArray', $activityArray);
		$this->_tpl->assign('formhash', $this->_http->formHashRefresh());
				
		$this->_tpl->display('center/suser/add_coupon.php');
	}
	/**
	 * 券编辑
	 */
	public function couponEditAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
			if(empty($getData['total'])) {
				$errMsg .= '请输入券数量'."\r\n";
			}
			
			if(!empty($getData['total']) && !preg_match('/[1-9][0-9]*$/', $getData['total'])) {
				$errMsg .= '券数量为正整数'."\r\n";
			}
			
			if(!empty($_FILES['file_img'])) {
				$imgInfo = getimagesize($_FILES['file_img']['tmp_name']);
				if ($imgInfo['0'] != 640 || $imgInfo['1'] != 300) {
					$errMsg .= '封面图片尺寸错误，请上传宽高为 640 × 300 的图片'."\r\n";
				} else {
					$cover_img = Custom_Upload::singleImgUpload($_FILES['file_img'], 'cover');
					$getData['cover_img'] = $cover_img;
				}
			}
			

			if($errMsg == '') {
				$editResult = $this->_model->editTicket($getData, $this->_userInfo);
				if($editResult) {
					Custom_Common::showMsg(
						'恭喜，你的券编辑成功！',
						'',
						array(
							'coupon-edit/tid/' . $getData['tid'] . '/sid/' . $getData['sid']   => '再次编辑',
							'coupon-list/sid/' . $getData['sid'] => '返回券管理'
						)
					);
				} else {
					Custom_Common::showMsg(
						'系统忙，请稍后再试',
						'',
						array(
							'coupon-edit/tid/' . $getData['tid'] . '/sid/' . $getData['sid']   => '再次编辑',
							'coupon-list/sid/' . $getData['sid'] => '返回券管理'
						)
					);
				}
			} else {
				Custom_Common::showMsg(nl2br($errMsg), 'back');
			}
		}
		
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$tid = intval($this->_http->get('tid'));		
		
		$ticketRow = $this->_model->getTicketRow($tid, $sid);
		
		if($ticketRow['end_time'] < REQUEST_TIME) {
			Custom_Common::showMsg(
				'抱歉，当前卷已经过期！',
				'',
				array(
					'coupon-list/sid/' . $sid => '返回券管理',
					'add-coupon/sid/' . $sid => '创建新券'
				)
			);	
		}
				
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('tid', $tid);
		$this->_tpl->assign('ticketRow', $ticketRow);
		$this->_tpl->display('center/suser/coupon_edit.php');
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
		if ($this->_model->uniqueBrandName($bname))
		{
			exit(json_encode(true));
		}
		else
		{
			exit(json_encode(false));
		}
	}
	
	public function goodDelAction() {
		$gid = $this->_http->get('gid');
		$gid = intval($gid);
		$result = $this->_model->goodDel($gid);
		if($result) {
			_exit('删除成功', '100');
		}
	}
	
	public function goodAuthAction() {
		$gid = $this->_http->get('gid');
		$gid = intval($gid);
		$result = $this->_model->goodAuth($gid);
		if($result) {
			_exit('认证成功', '100');
		}
	}

	public function goodAuthNoAction() {
		$gid = $this->_http->get('gid');
		$gid = intval($gid);
		$result = $this->_model->goodAuthNo($gid);
		if($result) {
			_exit('认证不通过', '300');
		}
	}
		
	public function goodsAuthAction() {
		$successArray = array();
		$ids = $this->_http->get('ids');
		$idsArray = explode(',', $ids);
		foreach($idsArray as $id) {
			if($this->_model->goodAuth($id)) {
				$successArray[] = $id;
			}
		}
		_exit('批量认证成功', '100', $successArray);
	}
	
	public function goodsAuthNoAction() {
		$failureArray = array();
		$ids = $this->_http->get('ids');
		$idsArray = explode(',', $ids);
		foreach($idsArray as $id) {
			if($this->_model->goodAuthNo($id)) {
				$failureArray[] = $id;
			}
		}
		_exit('批量认证不通过', '300', $failureArray);
	}
	
	public function ticketOffAction() {
		$tid = intval($this->_http->get('tid'));
		$result = $this->_model->ticketOff($tid);
		if($result) {
			_exit('下架成功', '100');
		}
	}
	
	public function ticketOffsAction() {
		$successArray = array();
		$ids = $this->_http->get('ids');
		$idsArray = explode(',', $ids);
		foreach($idsArray as $id) {
			if($this->_model->ticketOff($id)) {
				$successArray[] = $id;
			}
		}
		_exit('批量下架成功', '100', $successArray);
	}
	
	public function getGoodAction() {
		$sid = intval($this->_http->get('sid'));
		$goodsArray = $this->select("`shop_id` = '{$sid}' and `good_status` <> '-1' and `is_auth` <> '-1' and `is_del` = '0'", 'oto_good', 'good_id,good_name,dis_price', 'created desc');
		echo json_encode($goodsArray);
	}
	
	public function validAction() {
		$coupon_type = $this->_http->has('ctype') ?  (in_array($this->_http->get('ctype'), array('coupon', 'voucher')) ? $this->_http->get('ctype') : 'coupon') : 'coupon';
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('sortArray', $this->getTicketSortById(0, 'ticketsort'));
		$this->_tpl->assign('coupon_type', $coupon_type);
		$this->_tpl->display('center/suser/valid.php');
	}
	/**
	 * 券验证  - 查询可用的优惠券
	 */
	public function searchTicketAction() {
		$sid = intval($this->_http->get('sid'));
		$phone = $this->_http->get('phone');
		//判断手机号码是否存在， 券ID 是否合法
		if(!preg_match('/^1[3|4|5|6|7|8|9][0-9]{9}$/', $phone) || !$sid) exit('Hacker attacks');
		//开始查询
		$resultArray = $this->_model->searchTicketByPhone($sid, $phone);
		//查询结果
		_exit($resultArray['msg'], $resultArray['res'], $resultArray['extra']);
	}
	/**
	 * 券验证 - 使用可用的优惠券
	 */
	public function useTicketAction() {
		$sid = intval($this->_http->get('sid'));
		$phone = $this->_http->get('phone');
		$detailIdString = $this->_http->get('items');
		$resultArray = $this->_model->useTicket($sid, $phone, $detailIdString, $this->_userInfo);
		//券验证结果
		_exit($resultArray['msg'], $resultArray['res'], $resultArray['extra']);
	}
	/**
	 * 现金券验证  - 验证可用的现金券
	 */
	public function vaildVoucherTicketAction() {
		$sid = intval($this->_http->get('sid'));
		$detailIdString = $this->_http->get('items');
		$sidString = $this->_http->get('sidStr');
		$tidString = $this->_http->get('tidStr');
		$captcha = $this->_http->get('captcha');
		
		$resultArray = $this->_model->vaildVoucherTicket($sid, $captcha, $detailIdString, $sidString, $tidString, $this->_userInfo);
		//现金券验证结果
		_exit($resultArray['msg'], $resultArray['res'], $resultArray['extra']);
	}
	/**
	 * 券查询 - 查询已经购买的现金券
	 */
	public function inquireTicketAction() {
		$sid = intval($this->_http->get('sid'));
		$captcha = $this->_http->get('captcha');
		//判断验证码是否存在， 券ID 是否合法
		if(!$captcha || !$sid) exit('Hacker attacks');
		//开始查询
		$resultArray = $this->_model->inquireTicketByCaptcha($sid, $captcha);
		//现金券查询结果
		_exit($resultArray['msg'], $resultArray['res'], $resultArray['extra']);
	}
	/**
	 * 账户记录
	 */
	public function myAccountAction() {
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$stime = $this->_http->has('stime') ? urldecode($this->_http->get('stime')) : 0;
		$etime = $this->_http->has('etime') ? urldecode($this->_http->get('etime')) : 0;

		$data = $this->_model->getAccountListByShopId($shop_id, $stime, $etime, $page);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('page', $page);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->assign('data', $data);
		$this->_tpl->display('center/suser/account.php');
	}
	/**
	 * 店铺推荐
	 */
	public function shopDecorationAction() {
		$getData = $this->_http->getParams();
		$sid = intval($this->_http->get('sid'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$getData['shop_id'] = $shop_id;
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('shop_recommend')");
		$position = current($position);
		$myShopDecorationList = $this->_model->getShopDecorationList($this->_userInfo['user_id'], $getData, $page, $this->_page, $position['child']);

		$this->_tpl->assign('position', $position['child']);
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->assign('myShopDecorationList', $myShopDecorationList);
		$this->_tpl->display('center/suser/shop_decoration.php');
	}
	/**
	 * 店铺推荐编辑
	 */
	public function shopDecorationAddAction() {
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			if(!empty($_FILES['img'])) {
				$imgCheck = Custom_Upload::checkImg($getData['pos_id'], getimagesize($_FILES['img']['tmp_name']));
				if($imgCheck == '0') {
					echo 300;
					exit();	
				}
			}
			$uploadPath = Custom_Upload::singleImgUpload($_FILES['img'], 'shop');
			
			$result = $this->_model->shopDecorationEdit($getData, $uploadPath, $this->_userInfo);
			if($result) {
				echo 100;
				exit();
			}
		}
		$sid = intval($this->_http->get('sid'));
		$did = intval($this->_http->get('did'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$position = $this->getPosition("`pos_pid` = '0' and `identifier` in ('shop_recommend')");
		$position = current($position);
		if($did) {
			$row = $this->_db->fetchRow("select * from `oto_shop_decoration` where `shop_details_id` = '{$did}'");
			$this->_tpl->assign('did', $did);
			$this->_tpl->assign('row', $row);
		}
		
		$this->_tpl->assign('position', $position['child']);	
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->display('center/suser/shop_decoration_add.php');
	}
	/**
	 * 店铺推荐删除
	 */
	public function shopDecorationDelAction() {
		$sid = intval($this->_http->get('sid'));
		$did = intval($this->_http->get('did'));
		if($sid && $did) {
			$delResult = $this->_model->shopDecorationDel($did, $sid, $this->_user_id);
			if($delResult) {
				_exit('success', 100);
			}
		}
	}
	/**
	 * 店铺推荐排序
	 */
	public function shopAjaxColAction() {
		if($this->_model->shopModuleEdit($this->_http->getPost())) {
			echo json_encode(true);
			exit;
		}
	}
	/**
	 * 团购管理
	 */
	public function buyGoodAction() {
		$getData = $this->_http->getParams();
		$sid = intval($this->_http->get('sid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$getData['shop_id'] = $shop_id;
		$getData['page'] = $page;
		$getData['pagesize'] = $this->_page;
		$getData['user_id'] = $this->_userInfo['user_id'];
		
		$buyGoodList = $this->_model->getBuyGoodList($getData);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('buyGoodList', $buyGoodList);
		$this->_tpl->assign('request', stripslashes_deep($getData));
		$this->_tpl->display('center/suser/buy_good.php');		
	}
	/**
	 * 售出订单
	 */
	public function soldOrdersAction() {
		$getData = $this->_http->getParams();
		$sid = intval($this->_http->get('sid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		$getData['shop_id'] = $shop_id;
		$getData['page'] = $page;
		$getData['pagesize'] = PAGESIZE;
		$soldOrderList = $this->_model->getSoldOrderList($getData);
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('request', stripslashes_deep($_REQUEST));
		$this->_tpl->assign('soldOrderList', $soldOrderList);		
		$this->_tpl->display('center/suser/sold_orders.php');
	}
	/**
	 * 发起团购
	 */
	public function buyReleaseAction() {
		$recommendArray = $this->getTheRecommendedPosition('buygood', null, true, $this->_city);
		if($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
				
			if(empty($getData['activity_name'])) {
				$errMsg .= '请输入活动名称'."\r\n";
			}
			
			if(empty($getData['ticket_class'])) {
				$errMsg .= '请选择商品分类'."\r\n";
			}
			
			if(empty($getData['ticket_title'])) {
				$errMsg .= '请输入商品名称'."\r\n";
			}
				
			if(!empty($getData['ticket_title']) && mb_strlen($getData['ticket_title'], 'utf8') > 30) {
				$errMsg .= '商品名称最多30个字符，汉字算一个字符'."\r\n";
			}
				
			if(empty($getData['p_value'])) {
				$errMsg .= '请输入商品面值'."\r\n";
			}
						
			if(empty($getData['s_value'])) {
				$errMsg .= '请输入商品售价'."\r\n";
			}							
						
			if(!preg_match('/[1-9][0-9]*$/', $getData['total'])) {
				$errMsg .= '券数量为正整数'."\r\n";
			}
							
			if(!preg_match('/[1-9][0-9]*$/', $getData['climit'])) {
				$errMsg .= '限购数量为正整数'."\r\n";
			}
			
			if(empty($getData['sdate'])) {
				$errMsg .= '请输入商品销售开始时间'."\r\n";
			}
				
			if(empty($getData['edate'])) {
				$errMsg .= '请输入商品销售结束时间'."\r\n";
			}
				
			if(empty($getData['stime'])) {
				$errMsg .= '请输入商品使用开始时间'."\r\n";
			}
			
			if(empty($getData['etime'])) {
				$errMsg .= '请输入商品使用结束时间'."\r\n";
			}
				
			if(empty($getData['ticket_summary'])) {
				$errMsg .= '请输入商品简介'."\r\n";
			}
			
			if(!empty($getData['ticket_summary']) && mb_strlen($getData['ticket_summary'], 'utf8') > 70) {
				$errMsg .= '商品简介最多70个字符，汉字算一个字符'."\r\n";
			}
				
			if(empty($_FILES['file_img_large'])) {
				$errMsg .= '请上传商品大图'."\r\n";
			} else {
				$imgInfo = getimagesize($_FILES['file_img_large']['tmp_name']);
				if ($imgInfo['0'] != $recommendArray['buygood_img_large']['width'] || $imgInfo['1'] != $recommendArray['buygood_img_large']['height']) {
					$errMsg .= '商品大图尺寸错误，请上传宽高为 640 × 400 的图片'."\r\n";
				} else {
					$file_img_large = Custom_Upload::singleImgUpload($_FILES['file_img_large'], 'ticket');
					$getData['file_img_large'] = $file_img_large;
				}
			}
			
			if(empty($_FILES['file_img_small'])) {
				$errMsg .= '请上传商品小图'."\r\n";
			} else {
				$imgInfo = getimagesize($_FILES['file_img_small']['tmp_name']);
				if ($imgInfo['0'] != $recommendArray['buygood_img_small']['width'] || $imgInfo['1'] != $recommendArray['buygood_img_small']['height']) {
					$errMsg .= '商品小图尺寸错误，请上传宽高为 180 × 180 的图片'."\r\n";
				} else {
					$file_img_small = Custom_Upload::singleImgUpload($_FILES['file_img_small'], 'ticket');
					$getData['file_img_small'] = $file_img_small;
				}
			}
				
			if(empty($getData['content'])) {
				$errMsg .= '请输入商品使用说明'."\r\n";
			}
				
			if($errMsg == '') {
				if($this->_http->submitCheckRefresh()) {
					$addResultArr = Model_Admin_Ticket::getInstance()->addTuanTicket($getData, $this->_userInfo, $this->_city);
					if($addResultArr['status'] == 100) {
						Custom_Common::showMsg(
						'恭喜，你的商品添加成功！',
						'',
						array(
						'buy-release/sid/'. $getData['sid']   => '继续新增商品',
						'buy-good/sid/' . $getData['sid'] => '返回商品管理'
						)
						);
					} else {
						Custom_Common::showMsg('系统忙，请稍后再试');
					}
				} else {
					Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/suser/buy-good/sid/' . $getData['sid']);
				}
			} else {
				Custom_Common::showMsg(nl2br($errMsg), '');
			}			
		}
		$sid = intval($this->_http->get('sid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
		
		$storeArray = $this->getStore(0, true, false, $this->_city);
		$skuArray = $this->_model->getSkuCategoryList();
		$activityArray = $this->_model->getActivityList($this->_user_id);		
		
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->assign('storeArray', $storeArray);
		$this->_tpl->assign('skuArray', $skuArray);
		$this->_tpl->assign('activityArray', $activityArray);
		$this->_tpl->assign('recommendArray', $recommendArray);
		$this->_tpl->assign('formhash', $this->_http->formHashRefresh());
		$this->_tpl->display('center/suser/buy_release.php');
	}
	/**
	 * 获取SKU阵列
	 */
	public function getSkuListAction() {
		$cid = intval($this->_http->get('cid'));
		$skuListArray = $this->_model->getSkuPropList($cid);
		exit(json_encode($skuListArray));
	}
	/**
	 * 团购验证
	 */
	public function veriyAction() {
		$getData = $this->_http->getParams();
		$sid = intval($this->_http->get('sid'));
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$shop_id = $this->getOneShopIdByUserId($sid, $this->_user_id);
			
		$this->_tpl->assign('sid', $shop_id);
		$this->_tpl->display('center/suser/veriy.php');
	}
	
	public function getSelListAction() {
		$stype = $this->_http->get('stype');
		$region_id = intval($this->_http->get('region_id'));
		if($stype && $region_id) {
			echo json_encode(Model_Admin_User::getInstance()->getSelList($stype, $region_id));
			exit();
		}
	}

	public function getShopListAction() {
		$stype = $this->_http->get('stype');
		$region_id = intval($this->_http->get('region_id'));
		$related_id = intval($this->_http->get('related_id'));
		$sname = $this->_http->get('sname');
		echo json_encode(Model_Admin_User::getInstance()->getShopList($stype, $region_id, $related_id, $sname));
		exit();
	}
	
	public function changeOrderAction() {
		$getData = $this->_http->getParams();
		$excResultArr = Custom_AuthTicket::changeOrder($getData['method'], $getData['orderNo'], $this->_userInfo['user_name'], $getData['expressCompany'], $getData['expressNumber']);
		if($excResultArr['code'] == 1) {
			_exit('sucess', 100);
		} else {
			_exit('sucess', 300, $excResultArr['message']);
		}
	}	
}