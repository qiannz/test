<?php
	class Controller_Api_Discount extends Controller_Api_Abstract {
		private $_model;
		private $_pagesize = 20;
		public function __construct() {
			parent::__construct();
			$this->_model = Model_Api_Discount::getInstance();
		}
		
		//搜索首页
		public function mainFilterAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$data = array(
					"category" =>$this->_model->getSearchFilter( 
							array("type"=>"category"),
							$this->_city ),
					"brand" => $this->_model->getSearchFilter( 
							array("pmark"=>"discount",
								  "cmark"=>"discount_brand_recommend",
								  "type"=>"brand"),
							$this->_city ),
					"circle" => $this->_model->getSearchFilter(
							array("pmark"=>"discount",
								  "cmark"=>"discount_circle_recommend",
								  "type"=>"circle"),
							$this->_city
							),
					"market" => $this->_model->getSearchFilter(
							array("pmark"=>"discount",
								  "cmark"=>"discount_market_recommend",
								  "type"=>"market"),
								   $this->_city),
					"discount_strength"=>$this->_model->getSearchFilter(
							array("type"=>"discount_strength"),
							$this->_city
							)
					);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//折扣分类
		public function getCategoryAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			//折扣分类
			$getData["type"] = "category";
			$data  = $this->_model->getSearchFilter( $getData,$this->_city );
			exit(json_encode($this->returnArr(1, $data)));	
		}
		
		//品牌
		public function getBrandAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$type = $getData["type"];
			if( $type == 'recommend' ){
				$getData["pmark"] = "discount";
				$getData["cmark"] = "discount_brand_recommend";
			}
			$getData['type'] = 'brand';
			$data  = $this->_model->getSearchFilter( $getData,$this->_city );
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//商圈
		public function getCircleAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$type = $getData["type"];
			if( $type == 'recommend' ){
				$getData["pmark"] = "discount";
				$getData["cmark"] = "discount_circle_recommend";
			}
			$getData["type"] = 'circle';
			$data = $this->_model->getSearchFilter($getData,$this->_city);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//商场
		public function getMarketAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$type = $getData["type"];
			if( $type == 'recommend' ){
				$getData["pmark"] = "discount";
				$getData["cmark"] = "discount_market_recommend";
			}
			$getData["type"] = 'market';
			$data = $this->_model->getSearchFilter($getData,$this->_city);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//折扣力度
		public function getDiscountStrengthAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$getData["type"] = 'discount_strength';
			$data = $this->_model->getSearchFilter($getData,$this->_city);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//首页
		public function mainAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$data = $this->_model->getMain($getData , $this->_city , 10 , true);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//更多附近关注
		public function nearbyMoreAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$data = $this->_model->getNearbyMore($getData , $this->_city , 10 , true);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//折扣列表（搜索，我的关注）
		public function discountListAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( $getData['uuid'] && $getData['uname']) {
				$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if(!$user_id) {
					exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
				}
				$getData["uid"] = $user_id;
			}
			$data = $this->_model->getDiscountList( $getData , $this->_city , 10);
			exit(json_encode($this->returnArr(1 , $data)));
		}
		
		//图片上传
		public function imgUploadAction() {
			$getData = $this->_http->getParams();
			 
			//传输加密验证
			$this->authAll($getData);
			
			if(!$getData['img']) {
				exit(json_encode($this->returnArr(0, array(), 300, '图片不能为空')));
			}
			if( !$getData['uuid'] || !$getData['uname'] ) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
		
			$param = array(
					'img' => $getData['img'],
					'user_id' => $getData['uid'],
					'folder' => 'discount',
			);
		
			$insert_id = Custom_Upload::discountImgageUpload($param);
		
			exit(json_encode($this->returnArr(1, array('id' => $insert_id))));
		}
		
		//图片删除
		public function imgDelAction() {
			$getData = $this->_http->getParams();
		
			//传输加密验证
			$this->authAll($getData);
		
			if(!$getData['id']) {
				exit(json_encode($this->returnArr(0, array(), 300, '图片ID不能为空')));
			}
			if( !$getData['uuid'] || !$getData['uname'] ) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			 
			//断开商品图片关联
			$result = $this->_db->update(
					'discount_wap_img',
					array('discount_id' => '0'),
					array(
							'id' => intval($getData['id']),
							'user_id' => intval($getData['uid'])
					)
			);
		
			if($result) {
				exit(json_encode($this->returnArr(1, array())));
			} else {
				exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
			}
		}
		
		//折扣新增
		public function discountAddAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( !$getData['uuid'] || !$getData['uname'] ) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			$getData["uid"] = $user_id;
			 
			if(!$getData['title']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣标题不能为空')));
			}else if( mb_strlen($getData['title'], 'utf8') > 50 ){
				exit(json_encode($this->returnArr(0, array(), 300,'折扣标题最多50个字符，汉字算一个字符')));
			}
			
			if(!$getData['stime']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣开始时间不能为空')));
			}
			 
			if(!$getData['etime']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣结束时间不能为空')));
			}
			if( strtotime($getData['stime']) > strtotime($getData['etime']) ){
				exit(json_encode($this->returnArr(0, array(), 300, '折扣开始时间必须小于折扣结束时间')));
			}
			
			if(!$getData['address']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣地址不能为空')));
			}
			
			$last_insert_id = $this->_model->discountAdd($getData , $this->_city);
			if($last_insert_id>0) {
				exit(json_encode($this->returnArr(1, array("id"=>$last_insert_id))));
			} else {
				exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
			}
		}
		
		//折扣查看
		public function viewDiscountContentAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['did']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣ID不能为空')));
			}
			if( $getData['uuid'] && $getData['uname']) {
				$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if($user_id) {
					$getData["uid"] = $user_id;
				}
			}
			$data = $this->_model->getDiscountContent( $getData , $this->_city , true );
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//点击共同关注页面
		public function commonConcernAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['did']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣ID不能为空')));
			}
			$discount = $brand = array();
			list($discount,$brand) = $this->_model->getDiscountAndFirstBrand($getData['did']);
			exit(json_encode($this->returnArr(1, array("discount"=>$discount,"brand"=>$brand))));
		}
		
		//折扣收藏
		public function favAddAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			 
			$discount_id = intval($getData['did']);
			 
			if(!$discount_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣ID不能为空')));
			}
			 
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			 
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			
			$isFav = Model_Api_App::getInstance()->isFavorite('discount_favorite', $discount_id, $user_id);
						
			if ($isFav) {
				exit(json_encode($this->returnArr(0, array(), 300, '您已经收藏了这个折扣')));
			}
			//新增折扣收藏
			$result = Model_Api_App::getInstance()->addFavorite('discount_favorite', $discount_id, $user_id);
			//改变折扣收藏数量
			$this->_model->updtDiscountFavQuantity($discount_id);
			if( $result ){
				//同步到oto_user_dynamic
				$title = $this->_db->fetchOne("SELECT `title` FROM `discount_content` WHERE `discount_id`='{$discount_id}'");
				$this->syncFavoriteDynamic(array('user_id' => $user_id, 'from_id' => $discount_id, 'summary' => $title,'type'=>5, 'favorite_id'=>$result,'created' => REQUEST_TIME));
			}
			Model_Api_App::getInstance()->updateQuantityFavByUserId('discount_favorite', $user_id);
			exit(json_encode($this->returnArr(1, array())));
		}
		
		//折扣取消收藏
		public function favDelAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
		
			$discount_id = intval($getData['did']);
		
			if(!$discount_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣ID不能为空')));
			}
		
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
		
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
				
			$isFav = Model_Api_App::getInstance()->isFavorite('discount_favorite', $discount_id, $user_id);
				
			if (!$isFav) {
				exit(json_encode($this->returnArr(0, array(), 300, '您还没有收藏这个折扣')));
			}
			//新增折扣收藏
			$result = Model_Api_App::getInstance()->delFavorite('discount_favorite', $discount_id, $user_id);
			//改变折扣收藏数量
			$this->_model->updtDiscountFavQuantity($discount_id);
			if( $result ){
				//删除oto_user_dynamic中相应的记录
				$this->removeFavoriteDynamic($user_id,$discount_id,5);
			}
			
			Model_Api_App::getInstance()->updateQuantityFavByUserId('discount_favorite', $user_id);
			exit(json_encode($this->returnArr(1, array())));
		}
		
		//点击查看专题
		public function viewSpecialContentAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] && !$getData['uname']) {
				$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				$getData["user_id"] = $user_id;
			}
			if( !$getData["sid"] ){
				exit(json_encode($this->returnArr(0, array(), 300, '专题ID为空')));
			}
			$data = $this->_model->getSpecialContent($getData, $this->_city);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//专题商品更多
		public function specialGoodMoreAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( !$getData["sid"] ){
				exit(json_encode($this->returnArr(0, array(), 300, '专题ID为空')));
			}
			$data = $this->_model->specialGoodMore($getData, $this->_city , 100);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//聊天更多（折扣，专题）
		public function chatMoreAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if( $getData['uuid'] && $getData['uname']) {
				$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if($user_id) {
					$getData["uid"] = $user_id;
				}
			}
			$data = $this->_model->chatMore($getData, $this->_city , 10);
			exit(json_encode($this->returnArr(1, $data)));
		}
		
		//添加聊天（折扣，专题）
		public function chatAddAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			
			$userInfo = $this->getWebUserId($getData['uuid']);
			if( empty($userInfo) ){
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			if($getData['uname'] != $userInfo['user_name'] ) {
				exit(json_encode($this->returnArr(0, '', 300, '你的用户名已修改，请重新登录！')));
			}
			
			if(!$getData['type']) {
				exit(json_encode($this->returnArr(0, array(), 300, '类型不能为空')));
			}
			 
			if(!$getData['id']) {
				exit(json_encode($this->returnArr(0, array(), 300, ($getData['type']=='special' ? '专题':'折扣').'ID不能为空')));
			}
			 
			if(!trim($getData['content'])) {
				exit(json_encode($this->returnArr(0, array(), 300, '聊天内容不能为空')));
			}
			$getData["city"] = $this->_city;
			$res = $this->_model->chatAdd($getData,$userInfo);
			if($res) {
				exit(json_encode($this->returnArr(1, array("last_id"=>$res))));
			} else {
				exit(json_encode($this->returnArr(0, array(), 300, '未知错误')));
			}
		}
		
		
		
		//聊天点赞
		public function chatPraiseAddAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$chat_id = intval($getData['cid']);
			
			if(!$chat_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '聊天记录ID不能为空')));
			}
			
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			
			$isPraise = Model_Api_App::getInstance()->isFavorite('oto_group_chat_praise', $chat_id, $user_id);
			
			if ($isPraise) {
				exit(json_encode($this->returnArr(0, array(), 300, '您已对这个折扣点赞')));
			}
			//新增折扣收藏
			$result = Model_Api_App::getInstance()->addFavorite('oto_group_chat_praise', $chat_id, $user_id);
			//改变折扣收藏数量
			$this->_model->updtChatPraiseQuantity($chat_id);
			exit(json_encode($this->returnArr(1, array())));
		}
		
		//聊天取消点赞
		public function chatPraiseDelAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$chat_id = intval($getData['cid']);
				
			if(!$chat_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '聊天记录ID不能为空')));
			}
				
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}

			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
				
			$isPraise = Model_Api_App::getInstance()->isFavorite('oto_group_chat_praise', $chat_id, $user_id);
				
			if (!$isPraise) {
				exit(json_encode($this->returnArr(0, array(), 300, '您还没有对这个折扣点赞')));
			}
			//新增折扣收藏
			$result = Model_Api_App::getInstance()->delFavorite('oto_group_chat_praise', $chat_id, $user_id);
			//改变折扣收藏数量
			$this->_model->updtChatPraiseQuantity($chat_id);
			exit(json_encode($this->returnArr(1, array())));
		}
		
		//相关折扣
		public function relateDiscountAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$data = $this->_model->relateDiscount($getData , $this->_city);
			exit(json_encode($this->returnArr(1, $data )));
		}
		
		//我们都关注
		public function discountViewUserAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['did']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣ID不能为空')));
			}
			if( $getData['uuid'] && $getData['uname']) {
				$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
				if($user_id) {
					$getData["uid"] = $user_id;
				}
			}
			$data = $this->_model->discountViewUser($getData , $this->_city , $this->_pagesize);
			exit(json_encode($this->returnArr(1, $data )));
		}
	
		//用户关注折扣列表
		public function userViewDiscountAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['vuid']) {
				exit(json_encode($this->returnArr(0, array(), 300, '查看用户ID不能为空')));
			}
			$data = $this->_model->userViewDiscount($getData , $this->_city , $this->_pagesize);
			exit(json_encode($this->returnArr(1, $data )));
		}
		
		//用户发布的折扣列表
		public function userPublishDiscountAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['vuid']) {
				exit(json_encode($this->returnArr(0, array(), 300, '查看用户ID不能为空')));
			}
			$data = $this->_model->userPublishDiscount($getData, $this->_pagesize, $this->_city);
			exit(json_encode($this->returnArr(1, $data )));
		}
	
		//约逛,咨询列表
		public function messageShowAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			$getData["uid"] = $user_id;
			if(!$getData['frid']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣frid不能为空')));
			}
			if(!$getData['type']) {
				exit(json_encode($this->returnArr(0, array(), 300, '消息类型不能为空')));
			}
			$typeStr = ("shopping"==$getData["type"]?"约逛":"咨询");
			if(!$getData['to_uid']) {
				exit(json_encode($this->returnArr(0, array(), 300, $typeStr.'用户ID不能为空')));
			}
			if( $user_id == $getData['to_uid'] ){
				exit(json_encode($this->returnArr(0, array(), 300, '用户不能对自己进行'.$typeStr)));
			}
			$data = $this->_model->getMessageShow($getData , $this->_city , $this->_pagesize);
			exit(json_encode($this->returnArr(1, $data )));
		}
		
		//约逛，咨询添加
		public function messageAddAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$typeStr = ("shopping"==$getData["type"]?"约逛":"咨询");
			if(!$getData['frid']) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣frid不能为空')));
			}
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			$getData["uid"] = $user_id;
			if(!$getData['to_uid']) {
				exit(json_encode($this->returnArr(0, array(), 300, $typeStr.'用户ID不能为空')));
			}
			if(!trim($getData['content'])){
				exit(json_encode($this->returnArr(0, array(), 300, $typeStr.'内容不能为空')));
			}
			if( $getData['uid'] == $getData['to_uid'] ){
				exit(json_encode($this->returnArr(0, array(), 300, '用户不能对自己进行'.$typeStr)));
			}
			$res = $this->_model->messageAdd($getData, $this->_city);
			if( $res ){
				exit(json_encode($this->returnArr(1, array())));
			}else{
				exit(json_encode($this->returnArr(1, array(),300,'添加失败')));
			}
		}
		
		//热门品牌
		public function hotBrandAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			$getData["uid"] = $user_id;
			$getData["pmark"] = "discount";
			$getData["cmark"] = "discount_brand_recommend";
			$data = $this->_model->getHotList($getData, $this->_city);
			exit(json_encode($this->returnArr(1, $data )));
		}
		
		//品牌收藏（多个品牌）
		public function brandsFavAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			if(!$getData["bids"]) {
				exit(json_encode($this->returnArr(0, array(), 300, '品牌ID不能为空')));
			}
			$bids = trim($getData["bids"],",");
			$bidArr = explode(",", $bids);
			foreach($bidArr as $bid){
				$isFavorite = Model_Api_App::getInstance()->isFavorite('oto_brand_favorite', $bid, $user_id);
				if( $isFavorite ){continue;}
				Model_Api_Goods::getInstance()->addBrandFav($bid, $user_id, CLIENT_IP);	
			}
			Model_Api_App::getInstance()->updateQuantityFavByUserId('oto_brand_favorite', $user_id);
			exit(json_encode($this->returnArr(1, array())));
		}
		
		//取消品牌收藏（单个）
		public function delBrandFavAction() {
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			$bid = $getData['bids'];
			$isFav = Model_Api_Goods::getInstance()->isBrandFav($user_id, $bid, 'oto_brand_favorite');
			if (empty($bid)) {
				exit(json_encode($this->returnArr(0, array(), 300, '请选择要取消收藏的品牌')));
			}
			if (!$isFav) {
				exit(json_encode($this->returnArr(0, array(), 201, '您还没有收藏这个品牌')));
			}
			$result = Model_Api_Goods::getInstance()->delFavBrand($bid, $user_id, CLIENT_IP);
			Model_Api_App::getInstance()->updateQuantityFavByUserId('oto_brand_favorite', $user_id);
			exit(json_encode($result));
		}
		
		//热门商场
		public function hotMarketAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			$getData["uid"] = $user_id;
			$getData["pmark"] = "discount";
			$getData["cmark"] = "discount_market_recommend";
			$data = $this->_model->getHotList($getData, $this->_city);
			exit(json_encode($this->returnArr(1, $data )));
		}
		
		//商场收藏（多个品牌）
		public function marketsFavAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			if(!$getData["mids"]) {
				exit(json_encode($this->returnArr(0, array(), 300, '商场ID不能为空')));
			}
			$mids = trim($getData["mids"],",");
			$midArr = explode(",", $mids);
			foreach($midArr as $mid){
				$isFavorite = Model_Api_App::getInstance()->isFavorite('oto_market_favorite', $mid, $user_id);
				if( $isFavorite ){continue;}
				Model_Api_Goods::getInstance()->addMarketFav($mid, $user_id, CLIENT_IP);
			}
			Model_Api_App::getInstance()->updateQuantityFavByUserId('oto_market_favorite', $user_id);
			exit(json_encode($this->returnArr(1, array())));
		}
		
		//取消商场收藏
		public function delMarketFavAction() {
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			$market_id = $getData['mids'];
			if (empty($market_id)) {
				exit(json_encode($this->returnArr(0, array(), 101, '请选择要取消收藏的商场')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			$isFav = Model_Api_Goods::getInstance()->isMarketFav($user_id, $market_id, 'oto_market_favorite');
			if (!$isFav) {
				exit(json_encode($this->returnArr(0, array(), 201, '您还没有收藏这个商场')));
			}
			$result = Model_Api_Goods::getInstance()->delFavMarket($market_id, $user_id, CLIENT_IP);
			Model_Api_App::getInstance()->updateQuantityFavByUserId('oto_market_favorite', $user_id);
			exit(json_encode($result));
		}
		
		
		//热门用户
		public function hotUserAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			$getData["uid"] = $user_id;
			$data = $this->_model->getHotList($getData, $this->_city);
			exit(json_encode($this->returnArr(1, $data )));
		}
		
		//用户关注
		public function usersConcernAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			if(!$getData["uids"]) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户ID不能为空')));
			}
			$this->_model->addUsersConcern($getData["uids"],$user_id);
			exit(json_encode($this->returnArr(1, array())));
		}
	
		//提醒我
		public function noticeMeAction(){
			$getData = $this->_http->getParams();
			//传输加密验证
			$this->authAll($getData);
			if(!$getData['uuid'] || !$getData['uname']) {
				exit(json_encode($this->returnArr(0, array(), 300, '请先登录')));
			}
			$user_id = $this->checkUid($getData['uuid'], $getData['uname']);
			if(!$user_id) {
				exit(json_encode($this->returnArr(0, array(), 300, '用户不存在')));
			}
			if(!$getData["did"]) {
				exit(json_encode($this->returnArr(0, array(), 300, '折扣ID不能为空')));
			}
			$res = $this->_model->addNoticeMe($getData , $user_id);
			if( $res )
				exit(json_encode($this->returnArr(1, array() )));
			else
				exit(json_encode($this->returnArr(1, array() , 300 ,'未知错误')));
		}
	}
?>
