<?php
/**
 *大转盘 
 * @author qiannz
 *
 */

class Controller_Active_Wheel extends Controller_Home_Abstract {
	
	private $_model;
	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Active_Wheel::getInstance();
		
		if($_COOKIE['MP_APP_USER_ID']) {
			$this->_userInfo = $this->select("`user_id` = '".intval($_COOKIE['MP_APP_USER_ID'])."'", 'oto_user', 'user_id, uuid, user_name, user_type, user_status, star, phone_number,code', '', true);
			$this->_user_id = $this->_userInfo['user_id'];
		}
	}
	/**
	 * 验证
	 * @param unknown_type $getData
	 */
	public function auth($getData) {
		Third_Des::$key = '34npzntC';
		$encryptString = 'uname='.$getData['uname'].'&time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];				
		$sid = Third_Des::encrypt($encryptString);
		$ssid = $getData['ssid'];
		
		$sid = urldecode($sid);
		$ssid = urldecode($ssid);
	
		if (empty($ssid) || $ssid != $sid) {
			echo json_encode($this->returnArr(0, '', 300, '验证失败'));
			exit();
		}
	}
	/**
	 * 首页
	 */	
	public function indexAction() {
		//是否登录
		$is_login = 0;
		//是否绑定
		$is_bind = 0;
		//获取参数
		$getData = $this->_http->getParams();
		//验证
		$this->auth($getData);
		
		//获取登录信息
		$uname = urldecode($getData['uname']);
				
		$userInfoResult = Custom_AuthLogin::get_user_info($uname);
		if($userInfoResult['GetUserInfoResult'] == 1) {
			//获取/创建用户信息
			$this->_userInfo = $this->getWebUserId($uname);
			
			if($this->_userInfo) {
				$is_login = 1;
				$this->_user_id = $this->_userInfo['user_id'];
				cookie('MP_APP_USER_ID', $this->_user_id, 0, $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_path'], $GLOBALS['GLOBAL_CONF']['COOKIE']['cookie_domain'], false, false);
			}
			//判断是否绑定手机
			if($userInfoResult['userInfo']['IsMobilePass'] == 1) {
				$phone_number = $this->_db->fetchOne("select phone_number from `oto_user` where `user_id` = '{$this->_user_id}' limit 1");
				if(!$phone_number) {
					$this->_model->updateUserMobile($this->_user_id, $userInfoResult['userInfo']['Mobile']);
				}
				$is_bind = 1;
			}
			
			$this->_tpl->assign('user', $this->_userInfo);
		}
		
		$this->_tpl->assign('is_login', $is_login);
		$this->_tpl->assign('is_bind', $is_bind);
		$this->_tpl->display('active/wheel/index.php');
	}
	
	public function isBindMobileAction() {
		$uname = $this->_http->get('uname');
		$uname = Custom_String::HtmlReplace($uname);
		
		if($uname) {
			$userInfoResult = Custom_AuthLogin::get_user_info($uname);
			if($userInfoResult['GetUserInfoResult'] == 1) {
				if($userInfoResult['userInfo']['IsMobilePass'] == 1) {
					_exit('sucess', 100, $this->_userInfo['star']);
				}
			}
		}
		
		_exit('fail', 300);
	}
	
	public function isCanScratchAction() {
		//判断是否已登录
		if(!$this->_user_id) {
			_exit('fail', 101);
		}
		//幸运星不够
		if($this->_model->statisticsLucky($this->_user_id) < 1) {
			_exit('fail', 102);
		}
		
		_exit('fail', 100);
	}
	
	public function startScratchAction() {
		//奖励记录ID
		$insertLogId = 0;
		//轮盘奖励阵列		
		$winArr = array(
					'star' => array(1 => array(2, 8), 5 => array(4, 12)),
					'virtual' => array(1, 7),
					'real' => array(10),
					'call' => array(5, 11),
					'thanks' => array(3, 6, 9)
				);
		//判断是否登录
		if(!$this->_user_id) {
			_exit('fail', 101);
		}
		
		$appConfigArray = $this->_model->appConfigAll(); //中奖几率配置
		$winRow = Custom_Scratch::appStart($appConfigArray);
		
		switch($winRow['type']) {
			//幸运星
			case 'star':
				if($winRow['number'] == 1) {
					$key = array_rand($winArr['star'][1]);
					$winId = $winArr['star'][1][$key];
					//扣除一幸运星
					$this->_model->spendOneStar($this->_user_id, $winRow);
					//判断是否超出今日限制
					$todayNumber = $this->_model->getTodayWinningNumber('star');
					if($todayNumber + 1 > $winRow['dayLimit']) {
						break;
					}
					//判断是否超出共计限制
					$totalNumber = $this->_model->getTotalWinningNumber('star');
					if($totalNumber + 1 > $winRow['totalLimit']) {
						break;
					}
					//奖励入库表
					$insertLogId = $this->_model->luckyRecords($winRow, $this->_userInfo);
					//奖励提示内容
					$awardMsg = '恭喜您！ 抽到 ' . $winRow['name'];
				} elseif($winRow['number'] == 5) {
					$key = array_rand($winArr['star'][5]);
					$winId = $winArr['star'][5][$key];
					//扣除一幸运星
					$this->_model->spendOneStar($this->_user_id, $winRow);
					//判断是否超出今日限制
					$todayNumber = $this->_model->getTodayWinningNumber('star');
					if($todayNumber + 5 > $winRow['dayLimit']) {
						break;
					}
					//判断是否超出共计限制
					$totalNumber = $this->_model->getTotalWinningNumber('star');
					if($totalNumber + 5 > $winRow['totalLimit']) {
						break;
					}
					//奖励入库表
					$insertLogId = $this->_model->luckyRecords($winRow, $this->_userInfo);
					//奖励提示内容
					$awardMsg = '恭喜您！ 抽到 ' . $winRow['name'];
				}
				break;
			//券，实物，话费
			case 'virtual':
			case 'real':
			case 'call':
				$key = array_rand($winArr[$winRow['type']]);
				$winId = $winArr[$winRow['type']][$key];
				//扣除一幸运星
				$this->_model->spendOneStar($this->_user_id, $winRow);
				//判断是否超出今日限制
				$todayNumber = $this->_model->getTodayWinningNumber($winRow['type']);
				if($todayNumber + 1 > $winRow['dayLimit']) {
					break;
				}
				//判断是否超出共计限制
				$totalNumber = $this->_model->getTotalWinningNumber($winRow['type']);
				if($totalNumber + 1 > $winRow['totalLimit']) {
					break;
				}
				//奖励入库表
				$insertLogId = $this->_model->luckyRecords($winRow, $this->_userInfo);
				//奖励提示内容
				$awardMsg = '恭喜您！ 抽到 ' . $winRow['name'];
				break;
			case 'thanks':
				$key = array_rand($winArr['thanks']);
				$winId = $winArr['thanks'][$key];
				//扣除一幸运星
				$this->_model->spendOneStar($this->_user_id, $winRow);
				//奖励提示内容
				$awardMsg = '什么也没有抽到，感谢参与';
				break;	
		}
		
		//当超出限制的时候
		if(!$winId) {
			//扣除一幸运星
			$this->_model->spendOneStar($this->_user_id);
			
			$key = array_rand($winArr['thanks']);
			$winId = $winArr['thanks'][$key];
			//奖励提示内容
			$awardMsg = '什么也没有抽到，感谢参与';
		}
		//统计还有多少幸运星
		$surplus = intval($this->_model->statisticsLucky($this->_user_id));
		
		_exit('sucess', 100, array('win' => $winId, 'star' => $surplus, 'msg' => $awardMsg, 'id' => $insertLogId));
	}
	
	//发送验证码
	public function sendCodeAction(){
		$mobile = $this->_http->getPost('mobile');
	
		$code = Custom_Common::random(4);
		$content = "您的验证码为：" .$code;
		$resultMes = Custom_Send::sendMobileMessage($mobile,$content);
		if($resultMes['SendSmsResult'] == 1){
			$result = $this->_model->updateUserCode($this->_user_id, $code);
			if($result){
				_exit('success', 100);
			}
		}else {
			_exit('fail', 300);
		}
	}

	//验证验证码
	public  function verifyMobileCodeAction() {
		$mobile = trim($this->_http->getPost('mobile'));
		$code   = trim($this->_http->getPost('code'));
	
		if(!$mobile || !preg_match( '/^1[2-9][0-9]{9}$/' , $mobile )){
			_exit('fail', 101); //手机错误
		}
	
		if(!$code){
			_exit('fail', 102); //验证码不存在
		}
		
		if(!empty($this->_userInfo)) {
			if($this->_userInfo['code'] == $code) {
				$bindResult = Custom_AuthLogin::bindMobile($this->_userInfo['uuid'], $mobile);
				if($bindResult['MS_BindUserMobileResult'] == 1) {
					$this->_model->updateUserMobile($this->_user_id, $mobile);
					_exit('sucess', 100);
				} else {
					_exit($bindResult['errMsg'], 105); //绑定失败
				}
			} else {
				_exit('fail', 104); //验证码错误
			}
		} else {
			_exit('fail', 103); //不存在
		}
	}

	public function myWheelAction() {
		$this->_tpl->display('active/wheel/my_wheel.php');
	}
	
	public function myAjaxWheelAction() {
		$page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
		$data = $this->_model->getMyWheel($this->_user_id, $page);
		echo json_encode($data);
		exit();
	}
}