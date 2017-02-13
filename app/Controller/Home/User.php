<?php
class Controller_Home_User extends Controller_Home_Abstract {
	private $_model;
	private $_page;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_User::getInstance();
		$userSync = Custom_AuthLogin::get_user_info($this->_userInfo['user_name']);
		if($userSync['GetUserInfoResult'] == 1) {
			$this->_userSync['GroupTitle'] = $userSync['userInfo']['GroupTitle'];
			$this->_userSync['UserSex'] = $userSync['userInfo']['UserSex'];
			$this->_userSync['CityTitle'] = $userSync['userInfo']['CityTitle'];
			$this->_userSync['MP'] = $userSync['userInfo']['MP'];
			$this->_userSync['Avatar50'] = $userSync['userInfo']['userField']['Avatar50'];
		}
		$this->_tpl->assign('userSync', $this->_userSync);
		$this->_page = 10;
	}
	/**
	 * 加密验证
	 */
	private function verify() {
		Third_Des::$key = 'IN0xMmwV';
		$ssid = $this->_http->get('sid');
		$encrypt_user_name = $this->_http->get('user_name');
		$decrypt_ssid =  Third_Des::encrypt(Third_Des::decrypt($encrypt_user_name).$GLOBALS['GLOBAL_CONF']['My_User_Auth_Key']);
		if(empty($ssid) || $ssid != $decrypt_ssid) {
			_sexit('加密错误', 900);
		} 
	}
	/**
	 * 验证用户ID
	 */
	private function validUser() {
		if(!$this->_user_id) {
			_sexit('Hacker attacks', 300);
		}
	}
	/**
	 * 我上传的商品
	 */
	public function myGoodAction() {
		$getData = $this->_http->getParams();
		$this->verify();
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$pagesize = !$getData['pagesize'] ? $this->_page : intval($getData['pagesize']);
		$userInfo = $this->_model->getUserInfoByUserName($getData['user_name']);
		if(!$userInfo) {
			_sexit('用户状态异常', 900);
		}
		$myGoodList = $this->_model->getMyGoodList($userInfo['user_id'], $page, $pagesize);
		_sexit('success', $myGoodList);		
	}
	/**
	 * 我上传的商品删除
	 */
	public function goodDelAction() {
		$this->validUser();
		$gid = $this->_http->get('gid');
		$gid = intval($gid);
		if(!$gid) _sexit('商品ID错误', 300);
		$this->verify();
		
		$delQuery = $this->_model->deGood($this->_user_id, $gid);
		if($delQuery) {
			_sexit('删除成功', 100);
		}
	}
	/**
	 * 我上传的商品批量删除
	 */
	public function goodDelsAction() {
		$this->validUser();
		$ids = $this->_http->get('ids');
		if(!$ids) _sexit('商品IDS错误', 300);
		$this->verify();
		
		$idsArray = explode(',', $ids);
		foreach($idsArray as $id) {
			if($this->_model->deGood($this->_user_id, $id)) {
				$delArray[] = $id;
			}
		}
		_sexit('批量删除成功', 100, $delArray);
		
	}
	/**
	 * 我上传的商品编辑
	 */
	public function goodEditAction() {
		$this->validUser();
		$gid = intval($this->_http->get('gid'));
		$sid = intval($this->_http->get('sid'));
		if(!$gid || !$sid) header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		if ($this->_http->isPost()) {
			$getData = $this->_http->getPost();
			$errMsg = '';
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
				$errMsg .= '商品现价要为数字'."\r\n";
			}
			
			if(!empty($getData['list_price']) && !is_numeric($getData['list_price'])) {
				$errMsg .= '商品原价要为数字'."\r\n";
			}			
				
			if($errMsg == '') {
				$editResult = Model_Home_Suser::getInstance()->editGood($getData);
				if ($editResult) {
					$this->_file->del('get_good_view_'.$getData['gid']);
					Custom_Common::showMsg(
						'恭喜，你的商品编辑成功！',
						'',
						array(
						'good-edit/gid/' . $getData['gid']. '/sid/'.$getData['sid'].'/?referer=' . $getData['referer'] => '重新编辑',
						$getData['referer']  => '返回我的商品列表' // 暂定
						)
					);
				}
			} else {
				Custom_Common::showMsg(
					'系统繁忙，请稍后再试！',
					'',
					array(
						'good-edit/gid/' . $getData['gid'].'/?referer=' . $getData['referer'] => '重新编辑',
						$getData['referer']  => '返回我的商品列表' // 暂定
					)
				);
			}
		}
		$goodInfo = Model_Home_Good::getInstance()->getGoods($gid);
		if (empty($goodInfo['good_name'])) {
			header301($GLOBALS['GLOBAL_CONF']['SITE_URL']);
		}
		$goodInfo['dis_price'] = floatval($goodInfo['dis_price']);
		$goodInfo['org_price'] = floatval($goodInfo['org_price']);
		$this->_tpl->assign('gid', $gid);
		$this->_tpl->assign('sid', $sid);
		$this->_tpl->assign('goodInfo', $goodInfo);
		$referer = $this->_http->get('referer');
		if($referer) {
			$this->_tpl->assign('referer', $referer);
		} else {
			$this->_tpl->assign('referer', HTTP_REFERER);
		}
		$this->_tpl->display('center/user/my_good_edit.php');
	}
	/**
	 * 我收藏的商品
	 */
	public function myFavAction() {
		$getData = $this->_http->getParams();
		$this->verify();
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$pagesize = !$getData['pagesize'] ? $this->_page : intval($getData['pagesize']);
		$userInfo = $this->_model->getUserInfoByUserName($getData['user_name']);
		if(!$userInfo) {
			_sexit('用户状态异常', 900);
		}
		$myFavList = $this->_model->getMyFavList($userInfo['user_id'], $page, $pagesize);
		_sexit('success', $myFavList);	
	}
	/**
	 * 我收藏的商品删除
	 */
	public function goodFavDelAction() {
		$this->validUser();
		$gid = $this->_http->get('gid');
		$gid = intval($gid);
		if(!$gid) _sexit('商品ID错误', 300);
		$this->verify();
		
		$delQuery = $this->_model->deGoodFav($this->_user_id, $gid);
		if($delQuery) {
			_sexit('删除成功', 100);
		}
	}
	/**
	 * 我收藏的商品批量删除
	 */
	public function goodFavDelsAction() {
		$this->validUser();
		$ids = $this->_http->get('ids');
		if(!$ids) _sexit('商品IDS错误', 300);
		$this->verify();
		
		$idsArray = explode(',', $ids);
		foreach($idsArray as $id) {
			if($this->_model->deGoodFav($this->_user_id, $id)) {
				$delArray[] = $id;
			}
		}
		_sexit('批量删除成功', 100, $delArray);	
	}
	
	/**
	 * 我喜欢的商品
	 */
	public function myLikeAction() {
		$getData = $this->_http->getParams();
		$this->verify();
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$pagesize = !$getData['pagesize'] ? $this->_page : intval($getData['pagesize']);
		$userInfo = $this->_model->getUserInfoByUserName($getData['user_name']);
		if(!$userInfo) {
			_sexit('用户状态异常', 900);
		}
		$myLikeList = $this->_model->getMyLikeList($userInfo['user_id'], $page, $pagesize);
		
		_sexit('success', $myLikeList);
	}
	/**
	 * 我喜欢的商品删除
	 */
	public function goodLikeDelAction() {
		$this->validUser();
		$gid = $this->_http->get('gid');
		$gid = intval($gid);
		if(!$gid) _sexit('商品ID错误', 300);
		$this->verify();
		
		$delQuery = $this->_model->deGoodLike($this->_user_id, $gid);
		if($delQuery) {
			_sexit('删除成功', 100);
		}
	}
	/**
	 * 我喜欢的商品批量删除
	 */
	public function goodLikeDelsAction() {
		$this->validUser();
		$ids = $this->_http->get('ids');
		if(!$ids) _sexit('商品IDS错误', 300);
		$this->verify();
		
		$idsArray = explode(',', $ids);
		foreach($idsArray as $id) {
			if($this->_model->deGoodLike($this->_user_id, $id)) {
				$delArray[] = $id;
			}
		}
		_sexit('批量删除成功', 100, $delArray);	
	}
	/**
	 * 获取当前商圈和我的商圈
	 */
	public function getMyCircleAction() {
		$getData = $this->_http->getParams();
		$this->verify();		
		$userInfo = $this->_model->getUserInfoByUserName($getData['user_name']);
		if(!$userInfo) {
			_sexit('用户状态异常', 900);
		}	
		$data = $this->_model->getMyCircle($userInfo['user_id']);
		_sexit('success', $data);
	}
	/**
	 * 设置我的商圈
	 */
	public function setMyCircleAction() {
		$this->validUser();
		$cids = $this->_http->get('cids');
		if(!$cids) _sexit('商圈IDS错误', 300);
		$this->verify();
		
		$setResult = $this->_model->setMyCirle($this->_user_id, $cids);
		if($setResult) {
			_sexit('success', 100);
		}
	}
	
	private function verifyIsLogin() {
		if(!$this->_user_id) {
			Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['MAIN_SITE_URL'].'/login.aspx?sourceurl=' . HTTP_URI);
		}		
	}
	/**
	 * 我的街友会
	 */
	public function myTaskAction() {
		//验证是否登录状态
		$this->verifyIsLogin();
		//任务结束时间
		$task_end_time = date('Y-m-d',strtotime('-1 day',strtotime($GLOBALS['GLOBAL_CONF']['TASK_END_TIME'])));
		$this->_tpl->assign('task_end_time', $task_end_time);
		//我的奖金
		$myBonus = $this->_model->getMyBonus($this->_user_id);
		$this->_tpl->assign('myBonus', $myBonus);
		//天天向上-今日上传商品数量（已审核）
		$myTodayUploads =  $this->_model->getMyTodayUploads($this->_user_id);
		$this->_tpl->assign('myTodayUploads', $myTodayUploads);
		//十全大补-我当前完成度
		$myTenDays = $this->_model->getMyTenDays($this->_user_id, $myTodayUploads);
		$this->_tpl->assign('myTenDays', $myTenDays);
		//畅游迪拜-我上传的商品数（已审核）
		$myTotalUploads = $this->_model->getMyTotalUploads($this->_user_id);
		$this->_tpl->assign('myTotalUploads', $myTotalUploads);
		//畅游迪拜-最高上传商品数（已审核）
		$maxUploads = $this->_model->getMaxUploads();
		$this->_tpl->assign('maxUploads', $maxUploads);
		//街友最划算-刮刮卡数量
		$myClientEffectiveNum = $this->_model->getMyClientEffectiveNum($this->_user_id);
		$this->_tpl->assign('myClientEffectiveNum', $myClientEffectiveNum);
		//店员最划算-刮刮卡数量
		/*
		if($this->_userInfo['user_type'] == 3) {
			$myClerkEffectiveNum = $this->_model->getMyClerkEffectiveNum($this->_user_id);
			$this->_tpl->assign('myClerkEffectiveNum', $myClerkEffectiveNum);
		}
		*/
		$this->_tpl->display('center/user/my_task.php');
	}
	/**
	 * 街友刮奖
	 */
	public function clientScratchAction() {
		//验证是否登录状态
		$this->validUser();
		if($this->_http->getPost('uid') != $this->_user_id) {
			_exit('伪造刮奖请求', 301);
		}
		//街友最划算-开始刮奖
		$clientResultMsgArray = $this->_model->clientScratchStart($this->_userInfo);
		$this->_tpl->assign('scratchData', $clientResultMsgArray);
		$alertHtml = $this->_tpl->fetch('center/user/popup.php');
		Custom_Log::logLog($this->_user_id, $clientResultMsgArray, 'client');
		_exit($clientResultMsgArray['msg'], $clientResultMsgArray['res'], array('over' => $clientResultMsgArray['over'], 'html' => $alertHtml));
	}
	/**
	 * 店员刮奖
	 */
	public function clerkScratchAction() {
		//验证是否登录状态
		$this->validUser();
		if($this->_http->getPost('uid') != $this->_user_id) {
			_exit('伪造刮奖请求', 301);
		}
		//店员最划算-开始刮奖
		$clerkResultMsgArray = $this->_model->clerkScratchStart($this->_userInfo);
		$this->_tpl->assign('scratchData', $clerkResultMsgArray);
		$alertHtml = $this->_tpl->fetch('center/user/popup.php');
		Custom_Log::logLog($this->_user_id, $clerkResultMsgArray, 'clerk');
		_exit($clerkResultMsgArray['msg'], $clerkResultMsgArray['res'], array('over' => $clerkResultMsgArray['over'], 'html' => $alertHtml));
	}
	/**
	 * 获奖历史
	 */
	public function myTaskInfoAction() {
		//验证是否登录状态
		$this->verifyIsLogin();
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$taskInfo = $this->_model->getTaskInfo($this->_userInfo, $page);
		$this->_tpl->assign('taskInfo', $taskInfo);
		$this->_tpl->display('center/user/my_task_info.php');
	}
	/**
	 * 申请提现
	 */
	public function appExtractAction() {
		//验证是否登录状态
		$this->verifyIsLogin();
		//我的奖金
		$myBonus = $this->_model->getMyBonus($this->_user_id);
		if($this->_http->isPost()) {
			$errMsg = '';
			$getData = $this->_http->getPost();
            if($getData['type'] == 'alipay'){
                if(!preg_match('/^-?\d+\.?\d{0,1}$/', $getData['money'])) {
                    $errMsg .= '提取金额只能保留一位小数<br>';
                }
                if($getData['money'] > $myBonus) {
                    $errMsg .= '提取金额错误<br>';
                }
                if(empty($getData['realName'])) {
                    $errMsg .= '真实姓名不能为空<br>';
                }
                if(empty($getData['paypal'])) {
                    $errMsg .= '淘宝账号不能为空<br>';
                }
                if($getData['paypal'] != $getData['repaypal']) {
                    $errMsg .= '两次淘宝账号不一致';
                }
            }elseif($getData['type'] == 'bank'){
                if(!preg_match('/^-?\d+\.?\d{0,1}$/', $getData['cardMoney'])) {
                    $errMsg .= '提取金额只能保留一位小数<br>';
                }
                if($getData['cardMoney'] > $myBonus) {
                    $errMsg .= '提取金额错误<br>';
                }
                if(empty($getData['cardRealName'])) {
                    $errMsg .= '真实姓名不能为空<br>';
                }
                if(empty($getData['cardNum'])) {
                    $errMsg .= '银行账号不能为空<br>';
                }
                if($getData['cardNum'] != $getData['repayCardNum']) {
                    $errMsg .= '两次银行账号不一致';
                }
            }
			
			if($errMsg == '') {
				if($this->_http->submitCheckRefresh()) {
					$insertResult = $this->_model->addTaskMoney($getData, $this->_userInfo);
					if($insertResult) {
						Custom_Common::showMsg(
							'提交成功，请耐心等候审核。',
							'back'
						);
					}
				} else {
					Custom_Common::jumpto($GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/user/app-extract');
				}
			} else {
				Custom_Common::showMsg($errMsg, 'back');
			}
		}
		$this->_tpl->assign('formhash', $this->_http->formHashRefresh());
		$this->_tpl->assign('myBonus', $myBonus);
		$this->_tpl->display('center/user/my_app_extract.php');
	}
	/**
	 * 提现历史
	 */
	public function myTaskExtractAction() {
		//验证是否登录状态
		$this->verifyIsLogin();
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$taskMoney = $this->_model->getTaskMoney($this->_userInfo, $page);
		$this->_tpl->assign('taskMoney', $taskMoney);
		$this->_tpl->display('center/user/my_task_extract.php');
	}
	
	public function loginAction() {
		$getData = $this->_http->getParams();		
		//日志
		logLog(date('Ymd'). '.log', var_export($getData, true), LOG_PATH . 'user/login/' . date('Y') . '/' .date('m') . '/');
		//加密验证		
		$this->auth($getData);
		$uuid = $getData['uuid'];
		$remember = $getData['remember'];
		if($uuid) {
			$userInfo = $this->getWebUserId($uuid);
			if($userInfo) {
				$life_time = 0;
				if($remember == 1) {
					$life_time = REQUEST_TIME + 3600 * 30;
				}
				Third_Des::$key = 'A06D40B7';
				cookie('MPSHOPUUID', Third_Des::encrypt($uuid), $life_time, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['myshop_cookie_domain'], false, false);
				_sexit('sucess', 100);
			}
		}
		_sexit('failure', 300);
	}
	
	public function loginOutAction() {
		logLog(date('Ymd'). '.log', Third_Des::decrypt($_COOKIE['MPSHOPUUID']), LOG_PATH . 'user/loginout/' . date('Y') . '/' .date('m') . '/');
		cookie('MPSHOPUUID', '', 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['myshop_cookie_domain'], false, false);
	}
}