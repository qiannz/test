<?php
class Model_Admin_Merchant extends Base {
	private static $_instance;
	protected $_table = 'oto_merchant_app';
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getCount() {
		return $this->_db->fetchOne("select count(*) from `{$this->_table}` where 1 {$this->_where}");
	}
	
	public function getList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `{$this->_table}` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data?$data:array();
	}
	
	public function setWhere($getData) {
		$where = '';
		$isDel = false;
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'st':
							if($value == 1) {
								$where .= " and `auth_status` = '1'";
							} elseif($value == 2) {
								$where .= " and `auth_status` = '2'";
							} elseif($value == 3) {
								$where .= " and `auth_status` = '3'";
							}elseif($value == 4) {
								$where .= " and `auth_status` = '4'";
							} elseif($value == 5) {
								$where .= " and `auth_status` = '-1'";
							} 
							break;
						case 'uname':
							if($value) {
								$where .= " and `user_name` = '{$value}'";
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
	
	public function audit($data) {
		$uid = intval($data['uid']);
		$shop_id = intval($data['shop_id']);
		$shop_name = $data['shop_name'];
		$shop_address = $data['shop_address']; 
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
					$reason = '重复认证';
					break;
				case 3:
					$reason = $reason2;
					break;
			}
		} elseif ($audit_type == 1) {
			$reason = '审核通过';
		}
		
		if($audit_type == 1) {
			// 如果shop_id = 0 创建商户
			if (!$shop_id) {
				$lng = $lat = 0;
				$lngLatString = $this->getLatitudeAndLongitudeFormamap($shop_address);
				if($lngLatString) {
					list($lng, $lat) = explode(',', $lngLatString);
				}
				$arr = array(
						'shop_name' => $shop_name,
						'shop_address' => $shop_address,
						'lng' => $lng,
						'lat' => $lat, 
						'created' => REQUEST_TIME
						);
				$insertId =	$this->_db->insert('oto_shop', $arr);
				if ($insertId) {
					return $this->_db->update($this->_table, array('auth_status' => '2', 'reason' => $reason, 'shop_id' => $insertId), "`user_id` = '{$uid}' and `shop_id` = '{$shop_id}'");
				}
			} else {
				return $this->_db->update($this->_table, array('auth_status' => '2', 'reason' => $reason, 'updated' => REQUEST_TIME), "`user_id` = '{$uid}' and `shop_id` = '{$shop_id}'");
			}
		} elseif ($audit_type == 2) {
			return $this->_db->update($this->_table, array('auth_status' => '-1', 'reason' => $reason, 'updated' => REQUEST_TIME), "`user_id` = '{$uid}' and `shop_id` = '{$shop_id}'");
		}
		return false;
	}
	
	public function pay_audit($data) {
		$uid = intval($data['uid']);
		$shop_id = intval($data['shop_id']);
		$uname = trim($data['uname']);
		
		$merchantShopRow = $this->select("`shop_id` = '{$shop_id}'", 'oto_merchant_app', 'store_id, brand_id, brand_name, pack_id', '', true);
		$arr = array (
				'user_id' => $uid,
				'user_name' => $uname,
				'pack_id'    => $merchantShopRow['pack_id'],
				'store_id'  => $merchantShopRow['store_id'],
				'brand_id'  => $merchantShopRow['brand_id'],
				'brand_name' => $merchantShopRow['brand_name'],
				'pack_stime' => REQUEST_TIME,
				'pack_etime' => strtotime("+1 year", REQUEST_TIME),
				'updated' => REQUEST_TIME
				);
		$rs3 = $this->_db->update('oto_shop', $arr, "`shop_id` = '{$shop_id}'");
		$rs1 = $this->_db->update($this->_table, array('auth_status' => '4', 'updated' => REQUEST_TIME), "`user_id` = '{$uid}' and shop_id = '{$shop_id}'");
		$rs2 = $this->_db->update('oto_user', array('user_type' => '2'), "`user_id` = '{$uid}'");
		if ($rs1 && $rs2 && $rs3) {
			return true;
		}
		return false;
	}
}