<?php
class Model_Admin_Wechatbusiness extends Base {
	
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'st':
							if($value == 1) {
								$where .= " and `user_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `user_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `user_status` = '-1'";
							}
							break;
						case 'realname':
							if($value) {
								$where .= " and `realname` like '%{$value}%'";
							}
							break;
						case 'mobile':
							if($value) {
								$where .= " and `mobile` like '%".trim($value)."%'";
							}
							break;
						case 'ut':
							if($value) {
								$where .= " and `user_type` = '".trim($value)."'";
							}
							break;
					}
				}
			}
		}
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by `created` desc";
		$this->_order = $order;
	}
	
	//会员列表
	public function getMemberList( $page, $pagesize = PAGESIZE ){
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `wb_user` where `user_type`>-1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	//获取会员数量
	public function getMemberCount(){
		return $this->_db->fetchOne("select count(*) from `wb_user` where 1 ".$this->_where);
	}
	
	//根据会员id获取会员信息
	public function getMemberInfoByUid( $uid ){
		return $this->_db->fetchRow("select * from `wb_user` where `user_id` = '{$uid}'");
	}
	
	//根据会员手机号获取会员信息
	public function getMemberInfoByMobile( $mobile ){
		return $this->_db->fetchRow("select * from `wb_user` where `mobile` ='{$mobile}'");
	}
	
	//检查手机号码是否已经存在
	public function checkMobileIsExist( $mobile , $uid ){
		$user = $this->_db->fetchRow("select * from `wb_user` where `mobile` = '{$mobile}'");
		if( !empty($user) && $user["user_id"] != $uid ){//电话号码已存在
			return 0;
		}
		return 1;
	}
	
	//添加会员
	public function postUser( $postData ){
		$param = array();
		$param["realname"] = Custom_String::HtmlReplace( $postData["realname"], -1 );
		$param["mobile"] = $postData["mobile"];
		$param["user_type"] = $postData["user_type"];
		$param["apply_reason"] = $postData["apply_reason"];
		$uid = intval( $postData["uid"] );
		if( $uid ){//修改会员
			$insert_id = $uid;
			$param = array_merge($param, array('user_status'=>0,'updated' => REQUEST_TIME,'city'=>$this->_ad_city));
			$this->_db->update('wb_user', $param, array('user_id' => $uid));
		}else{//添加会员
			$param = array_merge($param, array(
					'user_status' => 1,
					'updated'     => REQUEST_TIME,
					'created'     => REQUEST_TIME
			));
			$insert_id = $this->_db->insert('wb_user', $param);
		}
		return $insert_id;
	}
	
	//修改审核会员信息
	public function doAudit( $postData ){
		$uid = intval($postData["uid"]);
		$param = array();
		$param["user_type"] = intval($postData["user_type"]);
		$param["user_status"] = $postData["user_status"];
		$res = $this->_db->update('wb_user', $param, array('user_id'=>$uid));
		if( is_numeric($res) ){
			return true;
		}
		return false;
	}
	
	//折扣列表
	public function getDiscountList( $page , $pagesize = PAGESIZE ){
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `wb_discount` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	//获取折扣数量
	public function getDiscountCount(){
		return $this->_db->fetchOne("select count(*) from `wb_discount` where 1 ".$this->_where);
	}
	
	//设定折扣
	public function postDiscount( $postData ){
		$param = array();
		$param["min_price"] = $postData["min_price"];
		$param["max_price"] = $postData["max_price"];
		$param["discount"] = $postData["discount"];
		$id = intval( $postData["id"] );
		$sql = "SELECT * 
				FROM `wb_discount` 
				WHERE `min_price`<='{$param["min_price"]}' AND `max_price`>'{$param["min_price"]}'
				UNION
				SELECT * 
				FROM `wb_discount` 
				WHERE `min_price`<'{$param["max_price"]}' AND `max_price`>='{$param["max_price"]}'";
		$data = $this->_db->fetchRow($sql);
		if( !empty($data) ){//价格区间已存在
			return false;
		}
		$param = array_merge($param, array(
					'created' => REQUEST_TIME,
					'city'=>$this->_ad_city
		));
		
		$insert_id = $this->_db->insert('wb_discount', $param);
		return $insert_id;
	}
	
	//根据折扣id获取折扣信息
	public function getDiscountById( $id ){
		return $this->_db->fetchRow("select * from `wb_discount` where `id` = '{$id}'");
	}
	
	//删除折扣信息
	public function doDel( $ids , $is_del ){
		$sql = "DELETE FROM `wb_discount` WHERE ".$this->db_create_in($ids,'id');
		$flag = $this->_db->query($sql);
		if( is_numeric($flag) ){
			return true;
		}else{
			return false;
		}
	}
	
	//订单列表
	public function getOrderList( $page , $pagesize = PAGESIZE ){
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `wb_order` where `is_del`='0' ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	//获取折扣数量
	public function getOrderCount(){
		return $this->_db->fetchOne("select count(*) from `wb_order` where `is_del`='0' ".$this->_where);
	}
	
	//获取合适的折扣
	public function getSuitableDiscount( $total_price ){
		$sql = "select * from `wb_discount` where `min_price`<={$total_price} AND `max_price`>{$total_price} LIMIT 1";
		return $this->_db->fetchRow($sql);
	}
	
	//保存订单
	public function postOrder( $postData ){
		$mobile = $postData["mobile"];
		$memberInfo = $this->getMemberInfoByMobile($mobile);
		if( empty($memberInfo) ){//该手机号不存在的情况下创建
			$insertData = array();
			$insertData["mobile"] = $mobile;
			$insertData["realname"] = Custom_String::HtmlReplace( $postData["realname"], -1 );
			$insertData["user_type"] = $postData["user_type"];
			$insertData["apply_reason"] = $postData["apply_reason"];
			$insertData["user_status"] = 1;
			$insertData["city"] = $this->_ad_city;
			$insertData["created"] = $insertData["updated"] = REQUEST_TIME;
			$insert_id = $this->_db->insert('wb_user', $insertData);
		}else{
			$insert_id = $memberInfo["user_id"];
		}
		$param = array();
		$param["user_id"] = $insert_id;
		$param["mobile"] = $postData["mobile"];
		$param["realname"] = $postData["realname"];
		$param["user_type"] = $postData["user_type"];
		$param["total_price"] = $postData["total_price"];
		$param["discount"] = $postData["discount"];
		$param["pay_price"] = $postData["pay_price"];
		$param["city"] = $this->_ad_city;
		$order_id = intval($postData["order_id"]);
		if( $order_id ){
			$insert_id = $order_id;
			$this->_db->update('wb_order', $param, array('order_id'=>$order_id));
		}else{
			$param["created"] = REQUEST_TIME;
			$insert_id = $this->_db->insert('wb_order', $param);
		}
		return $insert_id;
	}
	
	//获取订单信息
	public function getOrderInfo( $order_id ){
		$sql = "SELECT * FROM `wb_order` WHERE `order_id`='{$order_id}'";
		return $this->_db->fetchRow($sql);
	}
	
	//删除订单信息
	public function doDelOrder( $ids ){
		$sql = "UPDATE `wb_order` SET `is_del`='1' WHERE ".$this->db_create_in($ids,'order_id');
		$flag = $this->_db->query($sql);
		if( is_numeric($flag) ){
			return true;
		}else{
			return false;
		}
	}
}
?>