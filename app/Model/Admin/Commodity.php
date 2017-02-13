<?php
class Model_Admin_Commodity extends Base {
	private static $_instance;
	protected $_table = 'oto_ticket';
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
	
	public function getCount($ticketType) {
		return $this->_db->fetchOne("select count(ticket_id) from `".$this->_table."` where ticket_type = '{$ticketType}'".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'app':
							if($value == 1) {
								$where .= " and `start_time` > '". REQUEST_TIME ."'";
							} elseif($value == 2) {
								$where .= " and `start_time` < '". REQUEST_TIME ."' and `end_time` > '". REQUEST_TIME ."'";
							} elseif($value == 3) {
								$where .= " and `end_time` < '". REQUEST_TIME ."'";
							}
							break;
						case 'st':
							if($value == 1) {
								$where .= " and `ticket_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `ticket_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `ticket_status` = '-1'";
							}
							break;
						case 'isa':
							if($value == 1) {
								$where .= " and `is_auth` = '1'";
							} elseif($value == 2) {
								$where .= " and `is_auth` = '0'";
							}
							break;
						case 'iss':
							if($value == 1) {
								$where .= " and `is_show` = '1'";
							} elseif($value == 2) {
								$where .= " and `is_show` = '0'";
							}
							break;
						case 'title':
							if($value) {
								$where .= " and `ticket_title` like '%".trim($value)."%'";
							}
							break;
						case 'uname':
							if($value) {
								$user_id = $this->getUserIdByUserName($value);
								$where .= " and `user_id` = '{$user_id}'";
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
	
	public function getList($page, $ticketType, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where ticket_type = '{$ticketType}' ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
	}
	
	public function getShopListByUser($user_name = null, $city = 'sh') {
		$data = array();
		
		if($user_name) {
			$user_id = $this->getUserIdByUserName($user_name);
		
			$sql = "select SH.shop_id, SH.shop_name from
					`oto_user_shop_commodity` CO
					left join `oto_shop` SH on CO.shop_id = SH.shop_id
					where CO.user_id = '{$user_id}' and CO.city = '{$city}'";
				
			$data = $this->_db->fetchAll($sql);
		}
		
		return $data;
	}
	
	public function check_user_shop($user_name = null) {
		$user_id = $this->getUserIdByUserName($user_name);
		if ($user_name) {
			/*
			$sql = "select 1 from
					`oto_user_shop_commodity` CO
					left join `oto_shop` SH on CO.shop_id = SH.shop_id
					where CO.user_id = '{$user_id}' and SH.shop_status <> '-1' limit 1";
			*/
			$sql = "select 1 from `oto_user_shop_commodity` where user_id = '{$user_id}' limit 1";
			return $this->_db->fetchOne($sql) == 1;
		}
		
		return false;
	}
	
	public function getCommodityRow($ticket_id) {
		$sql = "select `oto_ticket`.*, `oto_ticket_info`.`category_id`, `oto_ticket_info`.`category_id`
				, `oto_ticket_info`.`category_name`, `oto_ticket_info`.`sku_info`, `oto_ticket_info`.`user_name_limit`
				, `oto_ticket_info`.`mobile_limit`, `oto_ticket_info`.`can_web`, `oto_ticket_info`.`can_wap`
				, `oto_ticket_info`.`can_app`
				from `oto_ticket`
				left join `oto_ticket_info` on `oto_ticket`.`ticket_id` = `oto_ticket_info`.`ticket_id`
				where `oto_ticket`.`ticket_id` = '{$ticket_id}'";
	
		$ticketRow = $this->_db->fetchRow($sql);
		$skuArray = unserialize($ticketRow['sku_info']);
		$ticketRow['UserNameLimit'] =  $ticketRow['user_name_limit'] ? $ticketRow['user_name_limit'] : 0;
		$ticketRow['MobileLimit'] =  $ticketRow['mobile_limit'] ? $ticketRow['mobile_limit'] : 0;
		$ticketRow['CanWeb'] =  $ticketRow['can_web'] ? $ticketRow['can_web'] : 0;
		$ticketRow['CanWap'] =  $ticketRow['can_wap'] ? $ticketRow['can_wap'] : 0;
		$ticketRow['CanApp'] =  $ticketRow['can_app'] ? $ticketRow['can_app'] : 0;
		$ticketRow['SkuStr'] =  $skuArray['PropStrStr'] ? $skuArray['PropStrStr'] : '';
		$ticketRow['selling_price'] = $ticketRow['selling_price'] > 1 ? intval($ticketRow['selling_price']) : $ticketRow['selling_price'];
		$ticketRow['par_value'] = $ticketRow['par_value'] > 0 ? intval($ticketRow['par_value']) : '';
		$ticketRow['app_price'] = $ticketRow['app_price'] > 0 ? intval($ticketRow['app_price']) : '';
		return $ticketRow;
	}
	/**
	 * 商城商品审核
	 * @param unknown_type $data
	 */
	public function audit($data) {
		$tid = intval($data['tid']);
		$audit_type = intval($data['audit_type']);
		$ticket_sort = intval($data['ticket_sort']);
		$reason1 = trim($data['reason1']);
		$reason2 = trim($data['reason2']);
		$rebates = intval($data['rebates']);
		$user_id = intval($data['user_id']);
		$city = $data['city'] ? $data['city'] : $this->_ad_city;
			
		if($audit_type == 2) {
			switch ($reason1) {
				case 1:
					$reason = '虚假信息';
					break;
				case 2:
					$reason = '恶意广告';
					break;
				case 3:
					$reason = '敏感内容';
					break;
				case 4:
					$reason = $reason2;
					break;
			}
		} elseif($audit_type == 1) {
			$reason = '审核通过';
		}
	
		if($audit_type == 1) {
			$resultArr = Model_Admin_Ticket::getInstance()->syncAudit('commodity', $tid, $city);
			
			//新建
			if($resultArr['status'] == 100) {
				$param = array(
						'ticket_status' => '1', 
						'ticket_uuid' => $resultArr['data']['ticket_uuid'], 
						'reason' => $reason, 
						'audit_person' => $user_id,
						'audit_time' => REQUEST_TIME
					);
				//商品返利设置
				if($rebates) {
					$param = array_merge($param, array('rebates' => $rebates));
				}
				//商品分类设置
				if($ticket_sort) {
					$param = array_merge($param, array('ticket_sort' => $ticket_sort));
				}
				
				$this->_db->update($this->_table, $param, "`ticket_id` = '{$tid}'");
			}
			//编辑
			elseif($resultArr['status'] == 200) {
				$param = array(
						'ticket_status' => '1', 
						'reason' => $reason, 
						'audit_person' => $user_id,
						'audit_time' => REQUEST_TIME
					);
				if($rebates) {
					$param = array_merge($param, array('rebates' => $rebates));
				}
				$this->_db->update($this->_table, $param, "`ticket_id` = '{$tid}'");
			}
			
			if($resultArr['status'] == 100 || $resultArr['status'] == 200) {
				//统计店铺在线商品数量
				$this->updateQuantityCommodityNumByShopId($resultArr['data']['shop_id']);
				
				//统计品牌在线商品数量
				$this->updateQuantityCommodityNumByBrandId($resultArr['data']['brand_id']);
				
				return true;
			}
		} elseif ($audit_type == 2) {
			$param = array(
					'ticket_status' => '-1', 
					'reason' => $reason,
					'audit_person' => $user_id,
					'audit_time' => REQUEST_TIME
				);
			//商品分类设置
			if($ticket_sort) {
				$param = array_merge($param, array('ticket_sort' => $ticket_sort));
			}
			return $this->_db->update($this->_table, $param, "`ticket_id` = '{$tid}'");
		}
		
		return false;
	}
	
	public function recommend($getData) {
		$arr = array(
				'come_from_id' => $getData['id'],
				'come_from_type' => 2,
				'title' => $getData['title'],
				'summary' => $getData['summary'],
				'pos_id' => $getData['pos_id'],
				'www_url' => '/home/ticket/show/tid/' . $getData['id'],
				'img_url' => $getData['img_url'],
				'created' => REQUEST_TIME,
				'pmark' => 'commodity',
				'cmark' => 'commodity_view',
				'city' => $getData['city'] ? $getData['city'] : $this->_ad_city
		);
		return $this->_db->insert('oto_recommend', $arr);
	}	
}