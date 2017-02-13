<?php
class Model_Admin_Good extends Base {
	private static $_instance;
	protected $_table = 'oto_good';
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
	
	public function getCount() {
		return $this->_db->fetchOne("select count(good_id) from `".$this->_table."` where 1".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		$isDel = false;
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'st':
							if($value == 1) {
								$where .= " and `good_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `good_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `good_status` = '-1'";
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
								$where .= " and `good_name` like '".trim($value)."%'";
							}
							break;
						case 'region_id':
							if($value == 1) {
								$where .= " and `region_id` = '{$value}'";
							}
							break;
						case 'circle_id':
							if($value == 1) {
								$where .= " and `circle_id` = '{$value}'";
							}
							break;
						case 'shop_id':
							if($value) {
								$where .= " and `shop_id` = '{$value}'";
							}
							break;
						case 'isd':
							if($value) {
								$isDel = true;
								$where .= " and `is_del` = '{$value}'";
							}
							break;
						case 'days':
							if($value) {
								$start_time = strtotime($value." 00:00:00");
								$end_time = strtotime($value." 23:59:59");
								if($getData['state'] && $getData['state'] == 1) {
									$where .= " AND created between '{$start_time}' AND '{$end_time}'";
								} else {
									$where .= " AND created between '{$start_time}' AND '{$end_time}' AND good_status = 0";
								}
							}
							break;
					}
				}
			}
		}
		if (!$isDel) {
			$where .= " and `is_del` = '0'";
		}
		$this->_where .= $where;
	}
	
	public function setOrder($getData) {
		$order = " order by `created` desc";
		$this->_order = $order;
	}	
		
	public function getList($page, $pagesize = PAGESIZE) {
    	$start = ($page - 1) * $pagesize;
    	$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
    	$data = $this->_db->limitQuery($sql, $start, $pagesize);  	   	
    	return $data ? $data : array();
    }
    
    public function postGood($postData) {
    	$img = trim($postData['img'], ',');
    	$good_name = trim($postData['good_name']);
    	$org_price = trim($postData['org_price']);
    	$dis_price = trim($postData['dis_price']);
    	$region_id = intval($postData['region_id']);
    	$circle_id = intval($postData['circle_id']);
    	$shop_id = intval($postData['shop_id']);
    	$gid = intval($postData['gid']);
    	
    	$shopRow = $this->getShopFieldById($shop_id);
    	$param = array(
    				'good_name' => $good_name,
    				'org_price' => $org_price,
    				'dis_price' => $dis_price,
    				'brand_id'	=> $shopRow['brand_id'],
    				'store_id'  => $shopRow['store_id'],
    				'region_id' => $region_id,
    				'circle_id' => $circle_id,
    				'shop_id' => $shop_id,
    				'shop_name' => saddslashes($shopRow['shop_name']),
    				'city' => $this->_ad_city
    			);
    	
    	if($gid && $gid > 0) {
    		$param = array_merge($param, array('updated' => REQUEST_TIME));
    		$result = $this->_db->update($this->_table, $param, "`good_id` = '{$gid}'");
    		if($result) {
    			return true;
    		}
    	} else {
    		$param = array_merge(
    					$param, 
    					array(
    						'user_id' => $this->getUserIdByUserName(DEFINED_USER_NAME),
    						'user_name' => DEFINED_USER_NAME,
    						'created' => REQUEST_TIME
    					)
    				);
	    	$insert_good_id = $this->_db->insert($this->_table, $param);
	    	if($insert_good_id && $insert_good_id > 0) {
	    		$imgArray = explode(',', $img);
	    		foreach ($imgArray as $value) {
	    			if($value) {
		    			$img_url = str_replace($GLOBALS['GLOBAL_CONF']['IMG_URL']. '/buy/good/small/', '', $value);
		    			$this->_db->update('oto_good_img', array('good_id' => $insert_good_id), "`img_url` = '{$img_url}'");
	    			}
	    		}
	    		return $insert_good_id;
	    	}
    	}
    	return false;
    }
    
    public function audit($data) {
    	$gid = intval($data['gid']);
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
    		$goodInfo = $this->select("`good_id` = '{$gid}'", 'oto_good', '*', '', true);
    		$taskStartDay = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']); // 名品街任务活动的开始时间
    		$taskEndDay = strtotime($GLOBALS['GLOBAL_CONF']['TASK_END_TIME']); // 名品街任务活动的结束时间
    		// 商品审核通过
    		$rs = $this->_db->update($this->_table, array('good_status' => '1', 'reason' => $reason), "`good_id` = '{$gid}'");
    		// 上传商品时间在活动结束时间内 才会触发该任务
    		if ($rs) {
    			if ($taskStartDay < $goodInfo['created'] && $goodInfo['created'] < $taskEndDay) {
    				//$this->taskEveryDay($goodInfo);
    				$this->taskExecution($goodInfo);
    				return true;
    			}
    		}
    	} elseif ($audit_type == 2) {
    		return $this->_db->update($this->_table, array('good_status' => '-1', 'reason' => $reason), "`good_id` = '{$gid}'");
    	}   	
    	return false;
    }

    /**
     * 商品审核 营业员上传返利 8/28
     * @param $data
     * @return bool
     */
    public function newAudit($data){
        $gid        = intval($data['gid']);
        $page       = intval($data['page']);
        $audit_type = intval($data['audit_type']);
        $reason1    = trim($data['reason1']);
        $reason2    = trim($data['reason2']);
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
           $goodRow = $this->getGoodRow($gid);
            // 商品审核通过
            $rs = $this->_db->update($this->_table, array('good_status' => '1', 'reason' => $reason), "`good_id` = '{$gid}'");
            if ($rs) {
                //判断该商品上传者否是营业员
                $userType = $this->_db->fetchOne("select user_type from oto_user where user_id = '{$goodRow['user_id']}'");
                if($userType == 3){
                    $result = $this->AwardForClerk($goodRow, 0.5, date('Y-m-d', REQUEST_TIME), 8);
                    if($result){
                        return true;
                    }else{
                        return false;
                    }
                }
                return true;
            }
            return false;
        }elseif ($audit_type == 2) {
            return $this->_db->update($this->_table, array('good_status' => '-1', 'reason' => $reason), "`good_id` = '{$gid}'");
        }
    }

    // 天天向上 合格者入库
    public function taskEveryDay($goodInfo) {
    	$userId = $goodInfo['user_id'];
    	$userName = $goodInfo['user_name'];
    	$stime = strtotime(date('Y-m-d', $goodInfo['created'])."00:00:00");
    	$etime = strtotime(date('Y-m-d', $goodInfo['created'])."23:59:59");
    	$day_date = $goodInfo['created'];
    	$num = $this->_db->fetchOne("select count(*) as num from oto_good where user_id = '$userId' and is_del = 0 and good_status = 1 and created between '{$stime}' AND '{$etime}'");
    	$taskLogInfo = $this->_db->fetchRow("select * from oto_task_ten_day_log where user_id = '{$userId}'");
    	
    	/**
    	 * 隔了1天 或者 1天以上 来操作审核   首先验证是否有满足十全大补的用户
    	 * @param $day_tye
    	 */
    	if (!empty($taskLogInfo)){
	    	$day_type = ceil(($day_date - $taskLogInfo['etime']) / 86400);
	    	if ($day_type == 2 || $day_type > 2) {
	    		if ($taskLogInfo['cdays'] == $GLOBALS['GLOBAL_CONF']['TASK_WINNING_DAYS']) {
	    			$this->task_ten_day($taskLogInfo, $userId, $userName, $day_date, $num);
					if ($num >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
						//  重新启动task_every_day任务
						$this->task_every_day($userId, $userName, $day_date, $num, 1);
					}
	    		}
	    	}
    	} 
    	
    	/**
    	 * 当天通过审核 >=20 个商品
    	 */ 
    	if ($num >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
    		// 第一次触发
    		if (empty($taskLogInfo)) {
    			$this->task_ten_day_log($userId, $day_date, 1, 'insert');
    			$this->task_every_day($userId, $userName, $day_date, $num, 1);
    		} else {
    			// 根据上传日期来判断是否隔天
    			$etime = date('Y-m-d', $taskLogInfo['etime']);
    			$type = ceil(($day_date - strtotime($etime)) / 86400);
    			// 当天内的操作
    			if ($type == 1) {
    				// 满足条件  不停跟新etime
    				$this->_db->update('oto_task_ten_day_log', array('etime' => $day_date), "`user_id` = '{$userId}'");
    				// 天天向上逻辑 
    				$this->task_every_day($userId, $userName, $day_date, $num, $taskLogInfo['cdays']);
    			} elseif ($type == 2) { // 隔一天操作
    				// 验证连续天数是否到达上限 （10）
    				if ($taskLogInfo['cdays'] == $GLOBALS['GLOBAL_CONF']['TASK_WINNING_DAYS']) {
    					$this->task_ten_day($taskLogInfo, $userId, $userName, $day_date, $num);
    					// 2. 重新启动task_every_day任务
    					$this->task_every_day($userId, $userName, $day_date, $num, 1);
    				} else {
    					$cdays = $taskLogInfo['cdays'] + 1;
    					$this->_db->update('oto_task_ten_day_log', array('cdays' => $cdays, 'etime' => $day_date), "`user_id` = '{$userId}'");
    					$this->task_every_day($userId, $userName, $day_date, $num, $cdays);
    				}
    			} elseif ($type > 2) { // 隔一天以上操作
    				// 十全大补条件失败  初始化oto_task_ten_day_log表
    				$this->_db->update('oto_task_ten_day_log', array('cdays' => 1, 'stime' => $goodInfo['created'], 'etime' => $goodInfo['created']), "`user_id` = '{$userId}'");
    				// 天天向上逻辑
    				$this->task_every_day($userId, $userName, $day_date, $num, 1);
    			}
    		}
    	}
    }
    
    /**
     * ten_day_log 逻辑
     */
    public function task_ten_day_log($userId, $day_date, $cdays, $type) {
    	if ($type == 'insert') {
    		$this->_db->insert('oto_task_ten_day_log', array('user_id' => $userId, 'cdays' => $cdays, 'stime'=> $day_date, 'etime' => $day_date));
    	} else {
    		$this->_db->update('oto_task_ten_day_log', array('cdays' => $cdays, 'stime' => $day_date, 'etime' => $day_date), "`user_id` = '{$userId}'");
    	}
    }
    
    /**
     * 满足天天向上活动的条件后  触发的十全大补逻辑
     */ 
    public function task_ten_day ($taskLogInfo, $userId, $userName, $day_date, $num) {
    	$allNum = $this->_db->fetchOne("select sum(effective_upload) from oto_task_every_day where created between '{$taskLogInfo['stime']}' and '{$taskLogInfo['etime']}'"); 
    	
    	// 1. 初始化arr_ten_day_log表
    	$this->task_ten_day_log($userId, $day_date, 1, 'update');
    	
    	// 3. 记录 十全大补  
    	$arr_ten_day = array(
    			'user_id'    => $userId,
    			'user_name'  => $userName,
    			'stime'      => $taskLogInfo['stime'],
    			'etime'      => $taskLogInfo['etime'],
    			'effective_upload' => $allNum, 
    			'award'      => 50,
    			'created'    => REQUEST_TIME
    	);
    	$this->_db->insert('oto_task_ten_day', $arr_ten_day);
    	
    	// 4. 新增oto_task_log记录
    	$arr_task_log= array(
    			'user_id'    => $userId,
    			'award'      => 5,
    			'task_type'  => 2,
    			'created'    => $taskLogInfo['etime']
    	);
    	$this->_db->insert('oto_task_log', $arr_task_log);
    }
      
    /**
     * 天天向上任务表操作
     * 如果满足当天审核通过20个上传宝贝 进入oto_task_every_day
     * 没数据新增 有数据则跟新宝贝上传数
     */ 
    public function task_every_day($userId, $userName, $day_date, $num, $tcdays) {
    	$day_date_ymd = date('Y-m-d', $day_date);
    	$insertArr = array(
    			'user_id'    => $userId,
    			'user_name'  => $userName,
    			'day_date'   => $day_date_ymd,
    			'effective_upload'	=> $num,
    			'award'             => 5,
    			'consecutive_day'   => $tcdays,
    			'created'   => $day_date
    	);
    	$arr_task_log= array(
    			'user_id'    => $userId,
    			'award'      => 5,
    			'task_type'  => 1,
    			'created'    => $day_date
    	);
    	$everyDayRow = $this->_db->fetchOne("select count(every_day_id) from oto_task_every_day where user_id = '{$userId}' and day_date = '{$day_date_ymd}'");
    	if (!$everyDayRow) {
    		$this->_db->insert('oto_task_every_day', $insertArr);
    		$this->_db->insert('oto_task_log', $arr_task_log);
    	} else {
    		$updateArr = array(
    					'effective_upload' => $num,
    					'updated' => REQUEST_TIME,
    					'consecutive_day' => $tcdays
    				);
    		$this->_db->update('oto_task_every_day', $updateArr, "`user_id` = '{$userId}' and `day_date` = '{$day_date_ymd}'");
    	}
    }
    
    public function del($good_id) {
    	return $this->_db->update($this->_table, array('is_del' => 1), "`good_id` = '{$good_id}'");
    }
    
    public function unDel($good_id) {
    	return $this->_db->update($this->_table, array('is_del' => 0), "`good_id` = '{$good_id}'");
    }
    
    public function getGoodRow($good_id) {
    	$goodRow = $this->select("`good_id` = '{$good_id}'", $this->_table, '*', '', true);
    	$goodRow['org_price'] = $goodRow['org_price'];
    	$goodRow['dis_price'] = $goodRow['dis_price'];
    	return $goodRow;
    }
    
    public function getImgList($good_id) {
    	$imgList = $this->select("`good_id` = '{$good_id}'", 'oto_good_img');
    	return $imgList;
    }
    
    public function setFirst($good_img_id, $good_id) {
    	$res1 = $this->_db->update('oto_good_img', array('is_first' => 0), "`good_id` = '{$good_id}'", 0);
    	$res2 = $this->_db->update('oto_good_img', array('is_first' => 1), "`good_img_id` = '{$good_img_id}'");
    	if($res1 && $res2) {
    		return true;
    	}
    	return false;
    }
    
    public function recommend($getData) {
    	$arr = array(
    				'come_from_id' => $getData['id'],
    				'come_from_type' => 1,
    				'title' => saddslashes($getData['title']),
    				'summary' => saddslashes($getData['summary']),
    				'pos_id' => $getData['pos_id'],
    				'www_url' => '/home/good/show/gid/' . $getData['id'],
    				'img_url' => $getData['img_url'],
    				'created' => REQUEST_TIME,
    				'updated' => REQUEST_TIME,
    				'city' => $this->_ad_city
    			);
    	return $this->_db->insert('oto_recommend', $arr);
    }
    
    public function checkRecommend($come_from_id, $pos_id) {
    	return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '1' and `pos_id` = '{$pos_id}' limit 1") == 1;
    }
    
    public function top($good_id) {
    	return $this->_db->update($this->_table, array('is_top' => 1), "`good_id` = '{$good_id}' and `is_top` = '0'");
    }
    
    public function unTop($good_id) {
    	return $this->_db->update($this->_table, array('is_top' => 0), "`good_id` = '{$good_id}' and `is_top` = '1'");
    }
    
    /**
     * 验证是否有遗漏的商品审核
     * 
     */ 
    public function checkAuditDay($auditDay, $taskDay) {
    	$auditDay = strtotime($auditDay);
    	$taskDay = strtotime($taskDay);
    	return $this->_db->fetchOne("select count(good_id) from oto_good where created >= '{$taskDay}' and created < '{$auditDay}' and good_status = 0");
    }
    /**
     * 任务开始执行
     * @param unknown_type $goodInfo
     */
    public function taskExecution(& $goodInfo) {
    	$ymd = date('Y-m-d', $goodInfo['created']);
    	$stime = strtotime($ymd . ' 00:00:00');
    	$etime = strtotime($ymd . ' 23:59:59');
    	//判断任务计数是否存在
    	$isTenLogExist = $this->_db->fetchOne("select 1 from `oto_task_ten_day_log` where `user_id` = '{$goodInfo['user_id']}'");
    	//如果任务计数不存在，开始任务计数
    	if(!$isTenLogExist) {
    		$this->taskTenDayLog($goodInfo);
    	}
    	$taskTenDayLogRow = $this->select("`user_id` = '{$goodInfo['user_id']}'", 'oto_task_ten_day_log', '*', '', true); 
		//统计用户上传商品的那一天 已经通过审核的商品数
		$hadAuditGoodNums = $this->_db->fetchOne("select count(good_id) 
													from `oto_good` 
													where `user_id` = '{$goodInfo['user_id']}'
													and `created` between {$stime} and {$etime}
													and `good_status` = '1'
													and `is_del` = '0'
												"); 	
		//计算间隔天数
		$numberOfDays = ceil( ($goodInfo['created'] - strtotime(date('Y-m-d',$taskTenDayLogRow['etime']))) / 86400);
		//同一天
		if($numberOfDays == 1) {
			//触发修改任务计数结束时间
			$this->taskTenDayLog($goodInfo, $taskTenDayLogRow['cdays'], true);
			//当满足天天向上时
			if($hadAuditGoodNums >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
				//触发天天向上
				$this->taskUpEveryDay($goodInfo, $ymd, $hadAuditGoodNums, $taskTenDayLogRow['cdays']);
				//触发十全大补
				if($taskTenDayLogRow['cdays'] == $GLOBALS['GLOBAL_CONF']['TASK_WINNING_DAYS']) {
					$this->taskUpTenDay($goodInfo, $taskTenDayLogRow);
				}
			}
		} 
		//隔天
		elseif ($numberOfDays == 2) {
			$cdays = $taskTenDayLogRow['cdays'] + 1;
			//重置用户任务计数
			if($taskTenDayLogRow['cdays'] == $GLOBALS['GLOBAL_CONF']['TASK_WINNING_DAYS']) {
				$this->taskTenDayLog($goodInfo);
				$cdays = 1;
			}
			
			//当满足天天向上时
			if($hadAuditGoodNums >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
				//触发天天向上
				$this->taskUpEveryDay($goodInfo, $ymd, $hadAuditGoodNums, $cdays);
				//触发修改任务计数结束时间、任务联系天数
				$this->taskTenDayLog($goodInfo, $cdays, true);
				//触发十全大补
				if($cdays == $GLOBALS['GLOBAL_CONF']['TASK_WINNING_DAYS']) {
					$this->taskUpTenDay($goodInfo, $taskTenDayLogRow);
				}
			} else {
				//触发修改任务计数结束时间 
				$this->taskTenDayLog($goodInfo, $cdays, true);
			}
		} 
		//隔一天以上 / 反日期审核
		else {
			//重置用户任务计数
			$this->taskTenDayLog($goodInfo);
			 
			//当满足天天向上时
			if($hadAuditGoodNums >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
				//触发天天向上
				$this->taskUpEveryDay($goodInfo, $ymd, $hadAuditGoodNums, 1);
			}  			
		}
    	
    }
    /**
     * 触发十全大补
     * @param unknown_type $goodInfo 产品信息
     * @param unknown_type $taskTenDayLogRow 用户任务计数信息
     */
    private function taskUpTenDay(& $goodInfo, & $taskTenDayLogRow) {
    	$day_date_start = date('Y-m-d', $taskTenDayLogRow['stime']);
    	$day_date_end = date('Y-m-d', $goodInfo['created']);
    	//统计十全大补总计上传的有效商品数
    	$uploads = $this->_db->fetchOne("select sum(effective_upload) 
    										from `oto_task_every_day` 
    										where `user_id` = '{$goodInfo['user_id']}'
    										and `day_date` between '{$day_date_start}' and '{$day_date_end}'
    									"); 
    	//触发奖励
    	$this->_db->replace('oto_task_ten_day', array(
    				'user_id' => $goodInfo['user_id'],
    				'user_name' => $goodInfo['user_name'],
    				'stime' => $day_date_start,
    				'etime' => $day_date_end,
    				'effective_upload' => $uploads,
    				'award' => 50, //十全大补的奖励金额
    				'created' => REQUEST_TIME
    			));
    	//触发十全大补奖励日志
    	$this->taskAwardLog($goodInfo, 50, $day_date_end, 2);
    }
    /**
     * 触发天天向上
     * @param unknown_type $goodInfo 产品信息
     * @param unknown_type $ymd	产品上传日期(Y-m-d)
     * @param unknown_type $hadAuditGoodNums 当日上传的有效商品数量
     * @param unknown_type $cdays 满足天天向上的连续天数
     */
    private function taskUpEveryDay(& $goodInfo, $ymd, $hadAuditGoodNums, $cdays) {
    	$isExist = $this->_db->fetchOne("select 1 from `oto_task_every_day` where `user_id` = '{$goodInfo['user_id']}' and `day_date` = '{$ymd}'");
    	if($isExist == 1) {
    		$this->_db->update('oto_task_every_day', 
    							array('effective_upload' => $hadAuditGoodNums, 'updated' => REQUEST_TIME), 
    							array('user_id' => $goodInfo['user_id'], 'day_date' => $ymd)
    					);
    	} else {
    		$this->_db->insert('oto_task_every_day', array(
    					'user_id' => $goodInfo['user_id'],
    					'user_name' => $goodInfo['user_name'],
    					'day_date' => $ymd,
    					'effective_upload' => $hadAuditGoodNums,
    					'consecutive_day' => $cdays,
    					'award' => 5, //天天向上奖励金额
    					'created' => REQUEST_TIME,
    					'updated' => REQUEST_TIME
    				));
    		//触发天天向上奖励日志
    		$this->taskAwardLog($goodInfo, 5, $ymd, 1);
    	}
    }
    /**
     * 触发奖励日志
     * @param unknown_type $goodInfo 产品信息
     * @param unknown_type $award 任务奖励
     * @param unknown_type $ymd 任务中奖标记日期（Y-m-d）
     * @param unknown_type $task_type 任务类型
     */
    public function taskAwardLog(& $goodInfo, $award, $ymd, $task_type) {
    	$isExist = $this->_db->fetchOne("select 1 from `oto_task_log` where `user_id` = '{$goodInfo['user_id']}' and `day_date` = '{$ymd}' and `task_type` = '{$task_type}'");   	
    	if(!$isExist) { 
	    	$insert_id = $this->_db->insert('oto_task_log', array(
	    				'user_id' => $goodInfo['user_id'],
	    				'award' => $award,
	    				'task_type' => $task_type,
	    				'day_date' => $ymd,
	    				'created' => REQUEST_TIME
	    			));
    	}   	
    }
    /**
     * 十全大补计数
     * @param unknown_type $goodInfo 产品信息
     * @param unknown_type $cdays 连续天天向上天数
     * @param unknown_type $update 计数新增还是修改
     */
    private function taskTenDayLog(& $goodInfo, $cdays = 1, $update = false) {
    	if($update) {
 			$this->_db->update('oto_task_ten_day_log', 
 								array('cdays' => $cdays, 'etime' => $goodInfo['created']), 
 								array('user_id' => $goodInfo['user_id'])
 							);   		
    	} else {
	    	$param = array(
	    				'user_id' => $goodInfo['user_id'],
	    				'cdays' => $cdays,
	    				'stime' => $goodInfo['created'],
	    				'etime' => $goodInfo['created']
	    			);
	    	$this->_db->replace('oto_task_ten_day_log', $param);
    	}
    }

   /**
    * 营业员上传商品审核获得0.5元奖励
    */
   private function AwardForClerk(& $goodInfo, $award, $ymd, $task_type){
       	// 验证营业员对于某上传商品是否已领取过奖励
    	$isAward = $this->_db->fetchOne("select 1 from `oto_task_upload_good` where `user_id` = '{$goodInfo['user_id']}' and `good_id` = '{$goodInfo['good_id']}'");
    	if(!$isAward) { 
    		$insert_id = $this->_db->insert('oto_task_upload_good', array(
	    				'user_id' => $goodInfo['user_id'],
	    				'good_id' => $goodInfo['good_id'],
	    				'award'   => $award,
	    				'created' => REQUEST_TIME
	    		));
	    	if ($insert_id) {
    			$this->_db->insert('oto_task_log', array(
	    				'user_id' => $goodInfo['user_id'],
	    				'award' => $award,
	    				'task_type' => $task_type,
	    				'day_date' => $ymd,
	    				'created' => REQUEST_TIME
    			));
	    	}
	    	return true;
    	}
    	return false;
   }
}