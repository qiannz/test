<?php
class Model_Admin_Buygood extends Base
{
	private static $_instance;
	private $_table = 'oto_ticket';
	
	public static function getInstance()
	{
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
						case 'act_name':
							if($value) {
								$act_id = $this->_db->fetchCol("select activity_id from oto_activity where activity_name like '%{$value}%' order by created desc");
								$where .= " and ".$this->db_create_in($act_id, 'activity_id');
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
		foreach ($data as $key => $item) {
			$data[$key]['activity_name'] = $this->_db->fetchOne("select activity_name from oto_activity where activity_id = '{$item['activity_id']}' limit 1");
			if($item['start_time'] > REQUEST_TIME) {
				$data[$key]['apply_status'] = '0';
			} elseif ($item['start_time'] < REQUEST_TIME && $item['end_time'] > REQUEST_TIME) {
				$data[$key]['apply_status'] = '1';
			} elseif ($item['end_time'] < REQUEST_TIME) {
				$data[$key]['apply_status'] = '-1';
			}
		}
		return $data ? $data : array();
	}
	
	public function getTuanRow($ticket_id) {
		$sql = "select `oto_ticket`.*, `oto_ticket_info`.* from `oto_ticket` 
				left join `oto_ticket_info` on `oto_ticket`.`ticket_id` = `oto_ticket_info`.`ticket_id`
				where `oto_ticket`.`ticket_id` = '{$ticket_id}'";
		
		$ticketRow = $this->_db->fetchRow($sql);
		$skuArray = unserialize($ticketRow['sku_info']);
		$ticketRow['UserNameLimit'] =  $ticketRow['user_name_limit'] ? $ticketRow['user_name_limit'] : 0;
		$ticketRow['MobileLimit'] =  $ticketRow['mobile_limit'] ? $ticketRow['mobile_limit'] : 0;
		$ticketRow['CanWeb'] =  $ticketRow['can_web'] ? $ticketRow['can_web'] : 0;
		$ticketRow['CanWap'] =  $ticketRow['can_wap'] ? $ticketRow['can_wap'] : 0;
		$ticketRow['CanApp'] =  $ticketRow['can_app'] ? $ticketRow['can_app'] : 0;
		$ticketRow['SkuStr'] =  $skuArray['PropStrStr'] ? $skuArray['PropStrStr'] : '[]';
		$ticketRow['par_value'] = $ticketRow['par_value'] > 0 ? intval($ticketRow['par_value']) : $ticketRow['par_value'];
		$ticketRow['selling_price'] = $ticketRow['selling_price'] > 1 ? intval($ticketRow['selling_price']) : $ticketRow['selling_price'];
		$ticketRow['app_price'] = $ticketRow['app_price'] > 1 ? intval($ticketRow['app_price']) : ($ticketRow['app_price'] == 0 ? '' : $ticketRow['app_price']);
		return $ticketRow;
	}
	
	public function audit($data) {
		$tid = intval($data['tid']);
		$audit_type = intval($data['audit_type']);
		$reason1 = trim($data['reason1']);
		$reason2 = trim($data['reason2']);
		$rebates = intval($data['rebates']);
			
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
			$resultArr = Model_Admin_Ticket::getInstance()->syncAudit('buygood', $tid, $this->_ad_city);
			//新建
			if($resultArr['status'] == 100) {
				$param = array('ticket_status' => '1', 'ticket_uuid' => $resultArr['data']['ticket_uuid'], 'reason' => $reason);
				if($rebates) {
					$param = array_merge($param, array('rebates' => $rebates));
				}
				return $this->_db->update($this->_table, $param, "`ticket_id` = '{$tid}'");
			}
			//编辑
			elseif($resultArr['status'] == 200) {
				$param = array('ticket_status' => '1', 'reason' => $reason);
				if($rebates) {
					$param = array_merge($param, array('rebates' => $rebates));
				}
				return $this->_db->update($this->_table, $param, "`ticket_id` = '{$tid}'");
			}
		} elseif ($audit_type == 2) {
			return $this->_db->update($this->_table, array('ticket_status' => '-1', 'reason' => $reason), "`ticket_id` = '{$tid}'");
		}
		return false;
	}
	
	public function recommend($getData) {
		$arr = array(
				'come_from_id' => $getData['id'],
				'come_from_type' => 2,
				'title' => saddslashes($getData['title']),
				'summary' => saddslashes($getData['summary']),
				'pos_id' => $getData['pos_id'],
				'www_url' => '/home/ticket/show/tid/' . $getData['id'],
				'img_url' => $getData['img_url'],
				'created' => REQUEST_TIME,
				'updated' => REQUEST_TIME,
				'pmark' => 'buy',
				'cmark' => 'nine_buy_view',
				'city' => $this->_ad_city
		);
		return $this->_db->insert('oto_recommend', $arr);
	}
	
	public function img_ajax_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
		$result = $this->_db->update('oto_ticket_wap_img',array($column => $value), "`id` = $id");
		if($result){
			exit(json_encode(true));
		}
	}
}