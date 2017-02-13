<?php
class Model_Admin_Shop extends Base {
	private static $_instance;
	protected $_table = 'oto_shop';
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
		$this->_where = "`shop_pid` = '0'";
		$this->_order = '';
	}
	
	public function getCount() {
		return $this->_db->fetchOne("select count(shop_id) from `".$this->_table."` where ".$this->_where);
	}
	
	public function setWhere($getData) {
		$where .= " and `city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'st':
							if($value == 1) {
								$where .= " and `shop_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `shop_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `shop_status` = '-1'";
							}
							break;
						case 'tt':
							if($value == 1) {
								$where .= " and (unix_timestamp() - `created` < ". 3600 * 24 * 7 . ")"; //1周
							} elseif($value == 2) {
								$where .= " and (unix_timestamp() - `created` < ". 3600 * 24 * 30 . ")"; //1个月
							} elseif($value == 3) {
								$where .= " and (unix_timestamp() - `created` < ". 3600 * 24 * 90 . ")"; //3个月
							} elseif($value == 4) {
								$where .= " and (unix_timestamp() - `created` < ". 3600 * 24 * 365 . ")"; //1年
							}
							break;
						case 'uname':
							if($value) {
								$where .= " and `user_name` = '{$value}'";
							}
							break;
						case 'title':
							if($value) {
								$where .= " and `shop_name` like '".trim($value)."%'";
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
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($data as $key => $item) {
			$data[$key]['through'] = $this->_db->fetchOne("select count(good_id) from `oto_good` where `shop_id` = '{$item['shop_id']}' and `good_status` = '1'");
			$data[$key]['total'] = $this->_db->fetchOne("select count(good_id) from `oto_good` where `shop_id` = '{$item['shop_id']}'");
		}
		return $data ? $data : array();
	}

	public function postShop($postData) {
		$store_id = intval($postData['store_id']);
		$pack_id = intval($postData['pack_id']);
		$pack_stime = strtotime($postData['pack_stime']);
		$pack_etime = strtotime($postData['pack_etime']);
		$shop_name = Custom_String::HtmlReplace(trim($postData['shop_name']), -1);
		$region_id = intval($postData['region_id']);
		$circle_id = intval($postData['circle_id']);
		$market_id = intval($postData['market_id']);
		$shop_address = trim($postData['shop_address']);
		$business_hour = trim( $postData['business_hour'] );
		$brand_name = $postData['brand_name'] ? trim($postData['brand_name']) : '';
		$is_commodity = intval($postData['is_commodity']);
		$is_selfpay = intval($postData['is_selfpay']);
		$phone = $postData['phone'] ? trim($postData['phone']) : '';
		$is_flag = intval($postData['is_flag']);
		$shop_img = $is_flag == 1 ? ( $postData['logoImg'] ? trim($postData['logoImg']) : '') : '';
		if(!$shop_img) {
			$is_flag =  0;
		}
		$sid = intval($postData['sid']);
		$is_divided = intval($postData['is_divided']);
		$divided_ratio = $is_divided == 1 ? intval($postData['divided_ratio']) : 0;
		$divided_user_id = $is_divided == 1 ? intval($postData['divided_user_id']) : 0;
		
		$lng = $lat = 0;
		$lngLatString = $this->getLatitudeAndLongitudeFormamap($shop_address);
		if($lngLatString) {
			list($lng, $lat) = explode(',', $lngLatString);
		}
		if(!empty($brand_name)) {
			$brand_id = $this->getBrandId($brand_name, $this->_ad_city);
		}
		
		$brand_id || $brand_id = 0;
		
		$param = array(
					'store_id' => $store_id,
					'pack_id' => $pack_id,
					'pack_stime' => $pack_stime,
					'pack_etime' => $pack_etime,
					'shop_name' => $shop_name,
					'region_id' => $region_id,
					'circle_id' => $circle_id,
					'market_id'	=> $market_id,
					'shop_address' => $shop_address,
					'business_hour' => $business_hour,
					'brand_id' => $brand_id,
					'brand_name' => $brand_name,
					'phone'      => $phone,
					'shop_img'   => $shop_img,
					'lng' => $lng,
					'lat' => $lat,
					'is_flag' => $is_flag,
					'is_divided' => $is_divided,
					'divided_ratio' => $divided_ratio,
					'divided_user_id' => $divided_user_id,
					'is_commodity' => $is_commodity,
					'is_selfpay' => $is_selfpay,
				);
		if($sid && $sid > 0) {
			$param = array_merge($param, array('shop_status' => 0, 'updated' => REQUEST_TIME));
			//事务开始
			$this->_db->beginTransaction();
			//修改店铺
			$shopResult = $this->_db->update($this->_table, $param, "`shop_id` = '{$sid}'");

			//修改店铺对应商品
			$goodResult = $this->_db->update('oto_good', array(
							'shop_name' => $shop_name, 
							'brand_id'  => $brand_id,
							'store_id'  => $store_id,
							'region_id' => $region_id,
							'circle_id' => $circle_id,
							'market_id'	=> $market_id						
						), "`shop_id` = '{$sid}'", false);

			//修改对应券表
			$tickResult = $this->_db->update('oto_ticket', array(
						'brand_id'  => $brand_id,
						'store_id'  => $store_id,
						'shop_name'	=> $shop_name,
						'region_id' => $region_id,
						'circle_id' => $circle_id,
						'market_id'	=> $market_id
					), "`shop_id` = '{$sid}'", false);	
			if($shopResult && $goodResult && $tickResult) {
				//事务确认
				$this->_db->commit();
				return true;
			} else {
				//事务回滚
				$this->_db->rollBack();
			}
		} else {
			$param = array_merge($param, array('created' => REQUEST_TIME, 'city' => $this->_ad_city));
			$insert_shop_id = $this->_db->insert($this->_table, $param);
			if($insert_shop_id) {
				return $insert_shop_id;
			}
		}
		return false;
	}
	
	public function getBrandId($brand_name, $city = 'sh') {
		$posf = strpos($brand_name, '[');
		if($posf !== false) {
			$brand_name_zh = substr($brand_name, 0, $posf);
			$brand_name_en = substr($brand_name, $posf + 1, -1);
			$sql = "select `brand_id` from `oto_brand` where `brand_name_zh` = '{$brand_name_zh}' and `brand_name_en` = '{$brand_name_en}' and `city` = '{$city}' limit 1";
		} else {
			$sql = "select `brand_id` from `oto_brand` where `brand_name_zh` = '{$brand_name}' or `brand_name_en` = '{$brand_name}' and `city` = '{$city}' limit 1";
		}
		return $this->_db->fetchOne($sql);			
	}
	
	public function uniqueBrandName($brand_name) {
		$posf = strpos($brand_name, '[');
		if($posf !== false) {
			$brand_name_zh = substr($brand_name, 0, $posf);
			$brand_name_en = substr($brand_name, $posf + 1, -1);
			$sql = "select 1 from `oto_brand` where `brand_name_zh` = '{$brand_name_zh}' and `brand_name_en` = '{$brand_name_en}' limit 1";
 		} else {
 			$sql = "select 1 from `oto_brand` where `brand_name_zh` = '{$brand_name}' or `brand_name_en` = '{$brand_name}' limit 1";
 		}
 		return $this->_db->fetchOne($sql) == 1;
	}
	
	public function uniqueUserName($user_name) {
		$sql = "select 1 from `oto_user` where `user_name` = '{$user_name}' limit 1";
		return $this->_db->fetchOne($sql) == 1;
	}

	public function getShopRow($shop_id) {
		$shopRow = $this->select("`shop_id` = '{$shop_id}'", $this->_table, '*', '', true);
		return $shopRow;
	}
	
	public function del($shop_id) {
		return $this->_db->update($this->_table, array('is_del' => 1), "`shop_id` = '{$shop_id}'");
	}
	
	public function unDel($shop_id) {
		return $this->_db->update($this->_table, array('is_del' => 0), "`shop_id` = '{$shop_id}'");
	}
	
	public function audit($data) {
		$sid = intval($data['sid']);
		$page = intval($data['page']);
		$audit_type = intval($data['audit_type']);
		$reason1 = trim($data['reason1']);
		$reason2 = trim($data['reason2']);
		 
		if($audit_type == 2) {
			switch ($reason1) {
				case 1:
					$reason = '虚假信息';
					break;
				case 2:
					$reason = '重复信息';
					break;
				case 3:
					$reason = $reason2;
					break;
			}
		} elseif($audit_type == 1) {
			$reason = '审核通过';
		}
	
		if($audit_type == 1) {
			return $this->_db->update($this->_table, array('shop_status' => '1', 'reason' => $reason), "`shop_id` = '{$sid}'");
		} elseif ($audit_type == 2) {
			return $this->_db->update($this->_table, array('shop_status' => '-1', 'reason' => $reason), "`shop_id` = '{$sid}'");
		}
		return false;
	}
	
	public function getSearch($shop_name) {
		$shop_name = trim($shop_name);
		$sql = "select `shop_id` as `id`, `shop_name` as `name` from `oto_shop` where  `is_del` = '0' and `shop_name` like '{$shop_name}%' order by `created` desc";
		return $this->_db->fetchAll($sql);
	}
	
	public function merge($data) {
		$cNameString = trim($data['cNameString'], ',');
		$cId = trim($data['cId'], ',');
		$mNameString = trim($data['mNameString'], ',');
		$mId = trim($data['mId'], ',');
		$shopNameArray = array();
		$shopNameStr = '';
		
		$shopResult = $shopAnotherResult = $goodResult = $ticketResult = $claimResult = true;
		$this->_db->beginTransaction();//事务开始
		
		//合并别名
		$shopNameArray = explode(',', $cNameString);
		$row = $this->select("`shop_id` = '{$mId}'", $this->_table, 'shop_another_name', '', true);
		$shopNameArray = array_merge($shopNameArray, explode(',', $row['shop_another_name']));		
		$shopNameArray = array_unique($shopNameArray);
		$shopNameStr = implode(',', $shopNameArray);
		$shopAnotherNum = count($shopNameArray);
		
		//修改被合并店铺
		$sql = "update `oto_shop` set `shop_pid` = '{$mId}', `shop_another_name` = '', `another_num` = '0' where `shop_id` in ({$cId})";
		$shopResult = $this->_db->query($sql);
		//修改主店铺对应参数信息
		$sql = "update `oto_shop` set `shop_another_name` = '{$shopNameStr}',`is_main` = '1', `another_num` = '{$shopAnotherNum}' where `shop_id` = '{$mId}'";
		$shopAnotherResult = $this->_db->query($sql);
		//修改店铺对应商品表
		$sql = "update `oto_good` set `shop_id` = '{$mId}', `shop_name` = '{$mNameString}' where `shop_id` in ({$cId})";
		$goodResult = $this->_db->query($sql);
		//修改店铺对应优惠券表
		$sql = "update `oto_ticket` set `shop_id` = '{$mId}', `shop_name` = '{$mNameString}' where `shop_id` in ({$cId})";
		$ticketResult = $this->_db->query($sql);
		//修改认领店铺对应表
		$shopClaimArray = $this->select("`shop_id` in ({$cId})", "oto_merchant_audit");
		foreach ($shopClaimArray as $shopClaimItem) {
			$explan = '<b>店铺合并</b>操作导致店铺从 <b style="color:red">'.$shopClaimItem['shop_name'].'</b> 被合并为 <b style="color:red">'.$mNameString.'</b>';
			$sql = "update `oto_merchant_audit` set `shop_id` = '{$mId}', `shop_name` = '{$mNameString}', `explan` = '{$explan}' where `shop_id` = '{$shopClaimItem['shop_id']}'";
			$claimResult = $this->_db->query($sql);
		}
		
		if($shopResult && $shopAnotherResult && $goodResult && $ticketResult && $claimResult) {
			$this->_db->commit();//事务结束
			return true;
		} else {
			$this->_db->rollBack();//事务回滚
			return false;
		}
	}
	
	public function replace($shop_id) {
		$row = $arr = array();
		$row = $this->getShopRow($shop_id);
		if($row) {
			if($row['is_main'] == 1) {
				$sql = "select 1 from `oto_shop_session` where `is_main` = '1' limit 1";
				$num = $this->_db->fetchOne($sql);
				if($num == 1) {
					return false;
				}
			}			
			$arr = array(
						'shop_id' => $shop_id,
						'shop_name' => $row['shop_name'],
						'shop_address' => $row['shop_address'],
						'is_main' => $row['is_main']
					);		
			$this->_db->replace('oto_shop_session', $arr);
			return true;
		}
		return false;
	}
	
	public function delShopSession($shop_id) {
		return $this->_db->delete('oto_shop_session', "`shop_id` = '{$shop_id}'");
	}
	
	public function isHasOwner($shop_id) {
		$sql = "select user_id from `oto_user_shop_competence` where `shop_id` = '{$shop_id}' and `user_type` = '2' group by user_id";
		$userList = $this->_db->fetchAll($sql);
		foreach($userList as & $user) {
			$userRow = $this->getUserByUserId($user['user_id']);
			$user['user_name'] = $userRow['user_name'];
		}
		return $userList;
	}
	/**
	 * 新增编辑店铺店员
	 * @param unknown_type $getData
	 * @param unknown_type $city
	 */
	public function modiShopCommodity($getData, $city) {
		$shop_id = $getData['sid'];
		$user_id = $getData['uid'];
		$userRow = $this->getUserByUserId($user_id);
		$real_name = $getData['real_name'];
		$mobile = $getData['mobile']; 
		$user_type = $getData['user_type'];
		$param = array(
					'shop_id' => $shop_id,
					'user_id' => $user_id,
					'user_name' => $userRow['user_name'],
					'real_name' => $real_name,
					'mobile' => $mobile,
					'user_type' => $user_type,
					'city'	=> $city,
					'created' => REQUEST_TIME
				);
		
		$syncResult = Custom_AuthLogin::sync_user($userRow['uuid'], $mobile, $real_name);
		if($syncResult['UpdateUserForMpshopResult'] == 1) {
			return $this->_db->insert('oto_user_shop_commodity', $param);
		}
		
		return false;
	}
	/**
	 * 获取店铺店员
	 * @param unknown_type $shop_id
	 */
	public function getShopStaffManagementList($shop_id, $city) {
		$sql = "select A.shop_id, A.user_id, A.real_name, A.user_type, A.mobile, A.created, B.user_name
				from `oto_user_shop_commodity` A
				left join `oto_user` B on A.user_id = B.user_id
				where A.`shop_id` = '{$shop_id}' and A.`city` = '{$city}'
				order by A.created asc";
		$data = $this->_db->fetchAll($sql);
		return $data;
	}
	/**
	 * 获取单个店铺店员
	 * @param unknown_type $shop_id
	 */
	public function getShopStaffManagementRow($shop_id, $user_id, $city) {
		$sql = "select A.shop_id, A.user_id, A.mobile, A.user_type, A.created, B.user_name
		from `oto_user_shop_commodity` A
		left join `oto_user` B on A.user_id = B.user_id
		where A.`shop_id` = '{$shop_id}' and A.`user_id` = '{$user_id}' and A.`city` = '{$city}'
		limit 1";
		return $this->_db->fetchRow($sql);
	}
	/**
	 * 根据店铺名称搜索相关店铺（模糊搜索）
	 * @param unknown_type $shop_name
	 * @param unknown_type $city
	 */
	public function searchShopListByName($shop_name, $city) {
		$data = array();
		if($shop_name) {
			$shop_name = Custom_String::HtmlReplace($shop_name);
			$sql = "select shop_id, shop_name from `oto_shop` 
					where `city` = '{$city}' 
					and `shop_name` like '%{$shop_name}%' 
					and `shop_pid` = '0'";
			$data = $this->_db->fetchAll($sql);
		}
		return $data;
	}
} 