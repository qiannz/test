<?php
class Model_Admin_Crowdfunding extends Base {
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
		foreach($data as & $row) {
			if($row['start_time'] > REQUEST_TIME) {
				$row['ticketStatus'] = '未开始';
			} elseif($row['start_time'] < REQUEST_TIME && $row['end_time'] > REQUEST_TIME) {
				$row['ticketStatus'] = '进行中';
			} elseif($row['end_time'] < REQUEST_TIME) {
				$row['ticketStatus'] = '已结束';
			}
			$row['ticketInfo'] = $this->select("`ticket_id` = '{$row['ticket_id']}'", 'oto_ticket_info', '*', '', true);
		}
		return $data ? $data : array();
	}
	
	public function getCrowdfundingRow($ticket_id) {
		$sql = "select `oto_ticket`.*, `oto_ticket_info`.*
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
		$ticketRow['selling_price'] = $ticketRow['selling_price'] >= 1 ? intval($ticketRow['selling_price']) : $ticketRow['selling_price'];
		$ticketRow['par_value'] = $ticketRow['par_value'] > 0 ? intval($ticketRow['par_value']) : '';
		$ticketRow['app_price'] = $ticketRow['app_price'] > 0 ? intval($ticketRow['app_price']) : '';
		if($ticketRow['lottery_uuid']) {
			$userRow = $this->getWebUserId($ticketRow['lottery_uuid']);
			$ticketRow['userInfo'] = $userRow;
		}
		return $ticketRow;
	}	
	/**
	 * 一元众筹审核
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
			$resultArr = Model_Admin_Ticket::getInstance()->syncAudit('crowdfunding', $tid, $city);
				
			//新建
			if($resultArr['status'] == 100) {
				$param = array(
						'ticket_status' => '1', 
						'ticket_uuid' => $resultArr['data']['ticket_uuid'], 
						'reason' => $reason, 
						'audit_person' => $user_id,
						'audit_time' => REQUEST_TIME
					);
				//返利设置
				if($rebates) {
					$param = array_merge($param, array('rebates' => $rebates));
				}
				//分类设置
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
				return true;
			}
		} elseif ($audit_type == 2) {
			$param = array(
					'ticket_status' => '-1', 
					'reason' => $reason,
					'audit_person' => $user_id,
					'audit_time' => REQUEST_TIME
				);
			//分类设置
			if($ticket_sort) {
				$param = array_merge($param, array('ticket_sort' => $ticket_sort));
			}
			return $this->_db->update($this->_table, $param, "`ticket_id` = '{$tid}'");
		}
	
		return false;
	}
	
	public function updateLotteryStatus($data, $ticket_id) {
		$params = array(
					'lottery_uuid' => $data['UserId'],
					'lottery_mobile' => $data['Mobile'],
					'lottery_code' => $data['Code'],
					'lottery_order_no' => $data['OrderNo'],
					'lottery_action' => 1
				);
		return $this->_db->update('oto_ticket_info', $params, array('ticket_id' => $ticket_id));
		
	}
	
	public function recommend($getData) {
		$arr = array(
				'come_from_id' => $getData['id'],
				'come_from_type' => 2,
				'title' => $getData['title'],
				'summary' => $getData['summary'],
				'pos_id' => $getData['pos_id'],
				'www_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'].'/home/ticket/wap/tid/' . $getData['id'],
				'img_url' => $getData['img_url'],
				'created' => REQUEST_TIME,
				'pmark' => 'wap',
				'cmark' => 'wap_index',
				'city' => $this->_ad_city
		);
		return $this->_db->insert('oto_recommend', $arr);
	}
	
	public function checkRecommend($come_from_id, $pos_id) {
		return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '2' and `pos_id` = '{$pos_id}' limit 1") == 1;
	}
}