<?php
class Model_Admin_User extends Base {
	private static $_instance;
	protected $_table = 'oto_user';
	protected $_where;
    protected $_order;
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_where = '';
		$this->_order = '';
	}
	
	public function getCount(){
		return $this->_db->fetchOne("select count(user_id) from `".$this->_table."` where 1".$this->_where);
	}
	/**
	 * 根据用户ID 统计用户 上传并且审核通过的商品
	 * @param unknown_type $user_id
	 */
	public function getQuantityThroughGoodByUserId($user_id) {
		return $this->_db->fetchOne("select count(good_id) from `oto_good` where `user_id` = '{$user_id}' and `good_status` = '1' and `is_del` = '0'");
	}
	/**
	 * 根据用户ID 统计用户 上传的所有商品
	 * @param unknown_type $user_id
	 */
	public function getQuantityTotalGoodByUserId($user_id) {
		return $this->_db->fetchOne("select count(good_id) from `oto_good` where `user_id` = '{$user_id}'");
	}
	/**
	 * 根据用户ID 统计用户 收藏的所有商品
	 * @param unknown_type $user_id
	 */
	public function getQuantityFavGoodByUserId($user_id) {
		return $this->_db->fetchOne("select count(favorite_id) from `oto_good_favorite` where `user_id` = '{$user_id}'");
	}
	/**
	 * 根据用户ID 统计用户 喜欢的所有商品
	 * @param unknown_type $user_id
	 */
	public function getQuantityLoveGoodByUserId($user_id) {
		return $this->_db->fetchOne("select count(concerned_id) from `oto_good_concerned` where `user_id` = '{$user_id}'");
	}
	// 用户列表
	public function getUserList($page, $pagesize = PAGESIZE)
	{
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
		$users = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($users as & $user)
		{
			/*
			$user['through'] = $this->updateQuantityThroughGoodByUserId($user['user_id']);
			$user['total'] = $this->updateQuantityTotalGoodByUserId($user['user_id']);
			$user['favorite_number'] = $this->updateQuantityFavGoodByUserId($user['user_id']);
			$user['concerned_number'] = $this->updateQuantityLoveGoodByUserId($user['user_id']);
			*/
			//判断用户是否是：营业员，店长，收银员
			$user['user_type_shop'] = 0;
			$affectRows = $this->select_one("`user_id` = '{$user['user_id']}'", oto_user_shop_commodity);
			if(!empty($affectRows)) {
				$user['user_type_shop'] = $affectRows['user_type'];
			}
			//用户是否禁用
			if($user['gag_time'] && $user['gag_time'] >= time())
			{
				$user['gag_status'] = 1;
				$user['user_status'] = 2;
				$interval = $user['gag_time'] - time();
				$user['gag_time_str'] = Custom_Time::getDayMinuteSecond($interval);
			}
		}
		 
		return $users?$users:array();
	}
	/**
	 * 个人通知列表
	 */
	public function getUserNoticeList($getData, $pageInfo) {		
		$db = Core_DB::get('message', null, true);
		$table = Model_Api_Message::getInstance()->getNoticeTableNameByUserId($getData['uid']);
		
		$where = " and `user_id` = '{$getData['uid']}'";
		
		$sqlC = "select count(id) from `{$table}` where  1 {$where}";
		$totalNum = $db->fetchOne($sqlC);
		
		$sqlQ = "select * from `{$table}` where 1 {$where} order by created desc";
		$data['result'] = $db->limitQuery($sqlQ, ($pageInfo['curr_page'] - 1) * $pageInfo['pageper'], $pageInfo['pageper']);
		$data['totalNum'] = $totalNum;
		
		return $data?$data:array();
	}
	
	// 用户查询条件
	public function setWhere($getData){
		$where = '';
		if(!empty($getData)){
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'user_type':
							if($value) {
								$where .= " and `user_type` = '{$value}'";
							}
							break;
						case 'user_status':
							if($value == 1) {
								$where .= " and `user_status` = '0' and `gag_time` < '". REQUEST_TIME ."'";
							} elseif($value == 2) {
								$where .= " and `user_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `gag_time` > '". REQUEST_TIME ."'";
							}
							break;
						case 'user_name':
							if($value) {
								$where .= " and `user_name` = '".trim($value)."'";
							}
							break;
					}
				}
			}
		}
	
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by user_id desc";
		if(!empty($getData)){
			if(array_key_exists('sort', $getData)){
				$order = " order by {$getData['sort']} {$getData['order']}";
			}
		}
		$this->_order = $order;
	}

	
	public function edit($data, $row) {
		$utype = intval($data['utype']);
		$user_type = intval($data['user_type']);
		$shop_id = intval($data['shop_id']);
		$user_status = intval($data['user_status']);
		$competence = $data['competence'];
		$gag = intval($data['gag']);
		
		//设置为 认证商户
		if($shop_id && $user_type == 2) {
			//店铺绑定
			$this->_db->update('oto_shop', 
								array(
										'user_id' => $row['user_id'], 
										'user_name' => $row['user_name']
									), 
								"`shop_id` = '{$shop_id}'"
								);
			//更改用户身份
			$this->_db->update($this->_table, array('user_type' => 2), "`user_id` = '{$row['user_id']}'");
			//删除可能的营业员权限
			$this->_db->delete('oto_user_shop_competence', array('user_id' => $row['user_id']));
			//同步用户身份类型
			$syncUserTypeResult = Custom_AuthLogin::changeUserType($row['uuid'], 'Manager');
			logLog('syncUser.log', var_export($syncUserTypeResult, true));
			
			return true;
		}
		
		//设置为营业员
		if($shop_id && $user_type == 3 && $utype != 2) {
			if(empty($competence)) {
				Custom_Common::showMsg('权限不能为空', 'back');
			}
			//更改用户身份
			$this->_db->update($this->_table, array('user_type' => 3), "`user_id` = '{$row['user_id']}'");
			//新增营业员权限
			$this->_db->replace('oto_user_shop_competence', array('user_id' => $row['user_id'], 'shop_id' => $shop_id, 'competence' => implode(',', $competence)));
			//同步用户身份类型
			$syncUserTypeResult = Custom_AuthLogin::changeUserType($row['uuid'], 'Clerk');
			logLog('syncUser.log', var_export($syncUserTypeResult, true));
			
			return true;
		}
		
		//设置为普通用户
		if(!$shop_id) {
			//不做任何修改
			if($utype == $user_type) {
				return true;
			}
			//认证商户 => 普通用户
			if($utype == 2 && $user_type == 1) {
				//更改用户身份
				$this->_db->update($this->_table, array('user_type' => 1), "`user_id` = '{$row['user_id']}'");
				//释放绑定店铺
				$this->unsetShop($row['user_id']);
				//同步用户身份类型
				$syncUserTypeResult = Custom_AuthLogin::changeUserType($row['uuid'], 'Null');
				logLog('syncUser.log', var_export($syncUserTypeResult, true));
				
				return true;
			} 
			//营业员 => 普通用户
			if ($utype == 3 && $user_type == 1) {
				//更改用户身份
				$this->_db->update($this->_table, array('user_type' => 1), "`user_id` = '{$row['user_id']}'");
				//释放绑定店铺
				$this->unsetShop($row['user_id']);
				//删除可能的营业员权限
				$this->_db->delete('oto_user_shop_competence', array('user_id' => $row['user_id']));
				//同步用户身份类型
				$syncUserTypeResult = Custom_AuthLogin::changeUserType($row['uuid'], 'Null');
				logLog('syncUser.log', var_export($syncUserTypeResult, true));
				
				return true;
			}
		}
		return true;
	}
	/**
	 * 用户店铺关系保存
	 * @param unknown_type $uid
	 * @param unknown_type $sidArray
	 * @param unknown_type $utype
	 */
	public function userShopSave($uid, $sidArray, $utype) {
		$userRow = $this->getRowById($uid);
		//更改用户身份
		$this->_db->update($this->_table, array('user_type' => $utype), "`user_id` = '{$userRow['user_id']}'");
		//同步用户身份		
		if($utype == 1) {
			Custom_AuthLogin::changeUserType($userRow['uuid'], 'Null');
		} elseif($utype == 2) {
			Custom_AuthLogin::changeUserType($userRow['uuid'], 'Manager');
		} elseif($utype == 3) {
			Custom_AuthLogin::changeUserType($userRow['uuid'], 'Clerk');
		}
		
		//删除原店铺关联
		$this->_db->delete('oto_user_shop_competence', array('user_id' => $userRow['user_id'], 'city' => $this->_ad_city), 0);
		//建立新的用户关联
		$sqlStr = '';
		$sidArray = array_unique($sidArray);
		foreach ($sidArray as $shop_id) {
			if($shop_id) {
				$sqlStr .= "('{$uid}', '{$shop_id}', '{$utype}', '{$this->_ad_city}'),";
			}
		}
		
		if($sqlStr) {
			$sql = "insert into `oto_user_shop_competence` (`user_id`, `shop_id`, `user_type`, `city`) values " . substr($sqlStr, 0, -1);
			$this->_db->query($sql);
		}
		return true;
	}
	
	public function userPurviewSave($uid, $sid, $purviewStr) {
		$this->_db->update('oto_user_shop_competence', array('competence' => $purviewStr), array('user_id' => $uid, 'shop_id' => $sid));
		return true;
	}
	/**
	 * 用户权限设置
	 * @param unknown_type $getData
	 */
	public function userRightsEdit($getData) {
		$param = array(
					'user_id' => $getData['uid'],
					'AllowOffline' => intval($getData['AllowOffline']),
					'AllowVerify' => intval($getData['AllowVerify']),
					'AllowView' => intval($getData['AllowView']),
					'AllowPrint' => intval($getData['AllowPrint']),
					'AllowMerchantVerify' => intval($getData['AllowMerchantVerify']),
					'AllowMerchantView' => intval($getData['AllowMerchantView']),
					'AllowMerchantManage' => intval($getData['AllowMerchantManage']),
					'AllowRefundApply' => intval($getData['AllowRefundApply']),
					'AllowBindCoupon' => intval($getData['AllowBindCoupon'])
				);
		
		return $this->_db->replace('oto_user_right', $param);
	}
	
	public function array_to_file($data, $filename) {
		$arr = array();
		$path = VAR_PATH . 'config/';
		if(!empty($data))
		{
			foreach($data as $item)
			{
				if($item['type'] == 1){
					$arr['ip'][] = $item['ip'];
				}
				elseif($item['type'] == 2)
				{
					$arr['username'][] = $item['user_name'];
					$arr['user_id'][] = $item['user_id'];
				}
			}
			$arr['ip'] = array_unique($arr['ip']);
			$arr['username'] = array_unique($arr['username']);
			$arr['user_id'] = array_unique($arr['user_id']);
			if(!is_dir($path))
			{
				make_dir($path);
			}
			file_put_contents($path.$filename.'.php', "<?php\r\n return ". var_export($arr, true).';');
		} else {
			unlink($path.$filename.'.php');
		}
	}

	public function getRowById($user_id) {
		return $this->select("`user_id` = '{$user_id}'", $this->_table, '*', '', true);
	}
	
	public function getShopByUserId($user_id) {
		$sql = "select `shop_id` , `shop_name` from `oto_shop` where `user_id` = '{$user_id}' and `shop_pid` = '0' order by sequence asc, shop_id asc";
		return $this->_db->fetchAll($sql);		
	}
	/**
	 * 获取用户关联店铺名称
	 * @param unknown_type $user_id
	 */
	public function getRelationShopByUserId($user_id) {
		$sql = "select SH.shop_id, SH.shop_name, CO.competence from
				oto_user_shop_competence CO
				left join oto_shop SH on CO.shop_id = SH.shop_id
				where CO.user_id = '{$user_id}' and CO.city = '{$this->_ad_city}'
				order by SH.sequence asc, SH.shop_id asc";
		$shopArray = $this->_db->fetchAll($sql);
		return $shopArray ? $shopArray : array();
	}
	
	public function audit($data, $row) {
		$user_id = intval($data['uid']);
		$page = intval($data['page']);
		$audit_type = intval($data['audit_type']);
		$reason1 = trim($data['reason1']);
		$reason2 = trim($data['reason2']);
			
		switch ($reason1) {
			case 1:
				$reason = '虚假信息';
				break;
			case 2:
				$reason = '重复认证';
				break;
			case 3:
				$reason = $reason2;
				break;
			default:
				$reason = '审核通过';
				break;
		}
	
		if($audit_type == 1) {
			return $this->_db->update($this->_table, array('user_type' => '2', 'reason' => $reason), "`user_id` = '{$user_id}'");		
		} elseif ($audit_type == 2) {
			return $this->_db->update($this->_table, array('user_type' => '1', 'reason' => $reason), "`user_id` = '{$user_id}'");
		}
		return false;
	}
	
	public function delShop($user_id, $shop_id) {
		$result =  $this->_db->update('oto_shop', array('user_id' => 0, 'user_name' => ''), "`shop_id` = '{$shop_id}' and `user_id` = '{$user_id}'");
		if($result) {
			return array('status' => 'ok');
		}
	}
	
	public function unsetShop($user_id) {
		$this->_db->update('oto_shop', array('user_id' => 0, 'user_name' => ''), "`user_id` = '{$user_id}'", 0);
	}
	
	public function getShopByRegionId($region_id, $master = false) {
		$where = " and `shop_pid` = '0'";
		if($master) {
			$where .= " and `user_id` = '0'";
		}
		$sql = "select `shop_id` as `id`, `shop_name` as `name` from `oto_shop` where `region_id` = '{$region_id}' and `shop_status` <> '-1' {$where} order by sequence asc, shop_id asc";
		$data = $this->_db->fetchAll($sql);
		return $data;
	}
	
	public function getSelList($stype, $region_id) {
		$where = " and `shop_pid` = '0' and `city` = '{$this->_ad_city}'";
		if($stype == 1) {
			$circleArray = $this->getCircleByRegionId($region_id, false, true, $this->_ad_city);
			return $circleArray ? $circleArray : array();
		} elseif($stype == 2) {
			$marketArray = $this->getMarket($region_id, true, $this->_ad_city);
			return $marketArray ? $marketArray : array();
		} elseif ($stype == 3) {
			$sql = "select `brand_id` as `id`, `brand_name` as `name` from `oto_shop` where `region_id` = '{$region_id}' and `brand_id` > 0 and `shop_status` <> '-1' {$where} group by brand_id order by sequence asc, shop_id asc";
			$data = $this->_db->fetchAll($sql);
			return $data ? $data : array();			
		}
	}
	
	public function getShopList($stype, $region_id, $related_id, $sname) {
		$where = "`shop_status` <> '-1' and `shop_pid` = '0' and `city` = '{$this->_ad_city}'";
		if($region_id) {
			$where .= " and `region_id` = '{$region_id}'";
		}
		
		if($sname) {
			$where .= " and `shop_name` like '%{$sname}%'";
		}
		
		if($stype == 1 && $related_id) {
			$where .= " and `circle_id` = '{$related_id}'";
		} elseif($stype == 2 && $related_id) {
			$where .= " and `market_id` = '{$related_id}'";
		} elseif ($stype == 3 && $related_id) {
			$where .= " and `brand_id` = '{$related_id}'";
		}
		
		$sql = "select `shop_id` as `id`, `shop_name` as `name` from `oto_shop` where {$where} order by sequence asc, shop_id asc";
		$data = $this->_db->fetchAll($sql);
		
		return $data ? $data : array();
	}

	public function starEdit($getData) {
		$number = $getData['number'];	
		$user_name = trim($getData['user_name']);
		$user_name = str_replace(array("\r","\r\n"), "\n", $user_name);
		$userArray = explode("\n", $user_name);

		foreach($userArray as $user_name) {
			if($user_name) {
				$userRow = $this->select("`user_name` = '{$user_name}'", 'oto_user', 'user_id, uuid, user_name, user_type, user_status, star', '', true);
				//用户名错误，跳过
				if(empty($user_name)) {
					continue;
				}
				
				//如果是减幸运星，判断用户剩余的幸运星是否足够，不够的话全部减完
				if($number < 0) {
					//已经没有幸运星了，则跳过
					if(!$userRow['star']) {
						continue;
					}
					if($userRow['star'] < abs($number)) {
						$number = '-' . $userRow['star'];
					}
					$award_name = $number . '幸运星';
				} else {
					$award_name = '+'. $number . '幸运星';
				}
				//日志
				$this->_db->insert(
						'oto_app_wheel_star_log', 
						array(
								'type' => 'back',
								'user_id' => $userRow['user_id'],
								'number' => $number,
								'award_name' => $award_name,
								'ip' => CLIENT_IP,
								'created' => REQUEST_TIME
							)
						);
				//更新用户幸运星
				Model_Active_Wheel::getInstance()->statisticsLucky($userRow['user_id']);
			}
		}
		return true;
	}
}