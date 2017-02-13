<?php
	class Controller_Api_User extends Controller_Api_Abstract {
		private $_model;
		private $_pagesize = 20;
		public function __construct() {
			parent::__construct();
			$this->_model = Model_Api_User::getInstance();
		}

		//动态列表
		public function dynamicAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( $getData['uuid'] && $getData['uname'] ) {
				$login_user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if($login_user_id) {
					$getData["login_user_id"] = $login_user_id;
					if( !intval($getData["uid"]) )
						$getData["uid"] = $login_user_id;
				}
			}
			if( !$getData["uid"] ){
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$data = $this->_model->getFriendsDynamicList( $getData , 10);
			exit(json_encode($this->returnArr(1 , $data)));
		}
		
		//动态按赞
		public function dynamicLikeAddAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$dynamic_id = intval($getData['dynamic_id']);
			
			if(!$dynamic_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '动态ID不能为空')));
			}
			
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			
			$isLike = Model_Api_App::getInstance()->isFavorite('oto_user_dynamic_like', $dynamic_id, $user_id);
			
			if ($isLike) {
				exit(json_encode($this->returnArr(0, array(), 300, '您已对这个动态点赞')));
			}
			$result = Model_Api_App::getInstance()->addFavorite('oto_user_dynamic_like', $dynamic_id, $user_id);
			$this->_model->updtDynamicPraiseQuantity($dynamic_id);
			exit(json_encode($this->returnArr(1, array(),100,'点赞成功')));
		}
		
		//取消动态按赞
		public function dynamicLikeDelAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$dynamic_id = intval($getData['dynamic_id']);
				
			if(!$dynamic_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '动态ID不能为空')));
			}
				
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}

			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
				
			$isLike = Model_Api_App::getInstance()->isFavorite('oto_user_dynamic_like', $dynamic_id, $user_id);
				
			if (!$isLike) {
				exit(json_encode($this->returnArr(0, array(), 300, '您还没有对这个动态点赞')));
			}
			$result = Model_Api_App::getInstance()->delFavorite('oto_user_dynamic_like', $dynamic_id, $user_id);
			$this->_model->updtDynamicPraiseQuantity($dynamic_id);
			exit(json_encode($this->returnArr(1, array(),100,'取消点赞成功')));
		}
		
		//粉丝列表
		public function fansAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( $getData['uuid'] && $getData['uname'] ) {
				$login_user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if($login_user_id) {
					$getData["login_user_id"] = $login_user_id;
					if( !intval($getData["uid"]) )
						$getData["uid"] = $login_user_id;
				}
			}
			if( !$getData["uid"] ){
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$data = $this->_model->getFansList( $getData , 10);
			exit(json_encode($this->returnArr(1 , $data)));
		}
		
		//关注用户列表
		public function concernUsersAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( $getData['uuid'] && $getData['uname'] ) {
				$login_user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if($login_user_id) {
					$getData["login_user_id"] = $login_user_id;
					if( !intval($getData["uid"]) )
						$getData["uid"] = $login_user_id;
				}
			}
			if( !$getData["uid"] ){
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$data = $this->_model->getConernedList( $getData , 10);
			exit(json_encode($this->returnArr(1 , $data)));
		}
		/**
		 * 用户权限
		 */
		public function userRightAction() {
			$getData = $this->_http->getParams();
			//加密验证
			$this->authZeroNet($getData);
			
			$data = $this->_model->getUserRight($getData);
			_sexit('成功', 100, $data);
		}
	}
	