<?php 
class Model_Home_User extends Base
{
	private static $_instance;
	private $_table = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getMyGoodList($user_id, $page, $pagesize) {
		$snapArray = $data = array();
		
		$sqlC = "select count(good_id) from `oto_good` where `user_id` = '{$user_id}' and `is_del` = '0'";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
		
		$sql = "select `good_id`, `good_name`, `user_name`, `shop_id`, `shop_name`, `dis_price`, `created`, `good_status`, `favorite_number`, `concerned_number`
				from `oto_good`
				where `user_id` = '{$user_id}' and `is_del` = '0'
				order by `created` desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		foreach ($data as $key => $item) {
			$data[$key]['www_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/good/show/gid/' . $item['good_id'];
			$data[$key]['edit_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/user/good-edit/gid/' . $item['good_id'] . '/sid/' . $item['shop_id'];
			$data[$key]['del_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/user/good-del/gid/' . $item['good_id'];
			$data[$key]['shop_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/shop/show/sid/' . $item['shop_id'];
			$data[$key]['created'] = date('y.m.d H:i', $item['created']);
		}
		$snapArray['data'] = $data;
		return $snapArray;	
	}
	
	public function deGood($user_id, $good_id) {
		return $this->_db->update('oto_good', array('is_del' => 1), array('good_id =' => $good_id));
	}
	
	public function getMyFavList($user_id, $page, $pagesize) {
		$snapArray = $data = array();
		$sqlC = "select count(favorite_id) from `oto_good_favorite` where `user_id` = '{$user_id}'";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
		
		$sql = "select B.good_id, B.good_name, B.user_name, B.shop_id, B.shop_name, B.dis_price, B.created, B.is_auth, B.favorite_number, B.concerned_number
				from `oto_good_favorite` as A
				left join `oto_good` as B on A.good_id = B.good_id
				where A.user_id = '{$user_id}' and B.good_id is not null
				order by B.created desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		foreach ($data as $key => $item) {
			$data[$key]['www_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/good/show/gid/' . $item['good_id'];
			$data[$key]['del_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/user/good-fav-del/gid/' . $item['good_id'];
			$data[$key]['shop_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/shop/show/sid/' . $item['shop_id'];
			$data[$key]['created'] = date('y.m.d H:i', $item['created']);
		}
		$snapArray['data'] = $data;
		return $snapArray;
	}
	
	public function deGoodFav($user_id, $good_id) {
		return $this->_db->delete('oto_good_favorite', array('user_id' => $user_id, 'good_id' => $good_id));
	}
	
	public function getMyLikeList($user_id, $page, $pagesize) {
		$snapArray = $data = array();
		$sqlC = "select count(concerned_id) from `oto_good_concerned` where `user_id` = '{$user_id}'";
		$totalNum = $this->_db->fetchOne($sqlC);
		//翻页最大边界值判断
		$maxPage = ceil($totalNum / $pagesize);
		if($page > $maxPage) $page = $maxPage;
		$snapArray['totalNum'] = $totalNum;
		
		$sql = "select B.good_id, B.good_name, B.user_name, B.shop_id, B.shop_name, B.dis_price, B.created, B.is_auth, B.favorite_number, B.concerned_number
				from `oto_good_concerned` as A
				left join `oto_good` as B on A.good_id = B.good_id
				where A.user_id = '{$user_id}' and B.good_id is not null
				order by B.created desc";
		$data = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		foreach ($data as $key => $item) {
			$data[$key]['www_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/good/show/gid/' . $item['good_id'];
			$data[$key]['del_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/user/good-like-del/gid/' . $item['good_id'];
			$data[$key]['shop_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/shop/show/sid/' . $item['shop_id'];
			$data[$key]['created'] = date('y.m.d H:i', $item['created']);
		}
		$snapArray['data'] = $data;
		return $snapArray;		
	}
	
	public function deGoodLike($user_id, $good_id) {
		return $this->_db->delete('oto_good_concerned', array('user_id' => $user_id, 'good_id' => $good_id));
	}
	
	public function getMyCircle($user_id) {
		$data = array();
		$data['allCircle'] = $this->getAllCircle($user_id);
		$data['myCid'] = $this->getOwnerCircle($user_id);
		return $data;
	}
	
	public function getAllCircle($user_id) {
		$snapArray = array();
		$circleArray = $this->getCircleByRegionId();
		$regionArray = $this->getRegion();
		$myCircleArray = $this->getMyCircleCol($user_id);
		foreach ($regionArray as $rid => $rname) {			
			foreach($circleArray as $key => $circle) {
				if($rid == $key) {
					$circleSnapArray = array();
					foreach ($circle as $ckey => $circleItem) {
						$circleSnapArray[$ckey]['circle_id'] = $circleItem['circle_id'];
						$circleSnapArray[$ckey]['circle_name'] = $circleItem['circle_name'];
						if(in_array($circleItem['circle_id'], $myCircleArray)) {
							$circleSnapArray[$ckey]['is_checked'] = 1;
						} else {
							$circleSnapArray[$ckey]['is_checked'] = 0;
						}
					}
					$snapArray[] = array(
								'rid' => $rid,
								'rname' => $rname,
								'circle' => $circleSnapArray
							);
				}
			}
		}
		unset($circleArray, $regionArray);
		return $snapArray;
	}
	
	public function getMyCircleCol($user_id) {
		return $this->_db->fetchCol("select `circle_id` from `oto_user_circle` where `user_id` = '{$user_id}'");
	}
	
	public function getOwnerCircle($user_id) {
		$snapArray = array();
		$myCircleArray = $this->getMyCircleCol($user_id);
		foreach($myCircleArray as $cirle_id) {
			$snapArray[] = array(
						'circle_id' => $cirle_id,
						'circle_name' => $this->getCircleNameByCircleId($cirle_id)
					);
		}
		unset($myCircleArray);
		return $snapArray;
	}
	
	public function getCircleNameByCircleId($circle_id) {
		$snapArray = array();
		$circleArray = $this->getCircleByRegionId();
		foreach($circleArray as $circleList) {
			foreach($circleList as $key => $circle) {
				$snapArray[$circle['circle_id']] = $circle['circle_name'];
			}
		}
		unset($circleArray);
		return $snapArray[$circle_id];
	}
	
	public function setMyCirle($user_id, $cids) {
		$this->_db->beginTransaction();
		$delResult = $this->_db->delete('oto_user_circle', array('user_id' => $user_id), 0);
		$cidsArray = explode(',', $cids);
		$fieldArray = array();
		foreach ($cidsArray as $key => $circle_id) {
			$fieldArray[$key] = array('user_id' => $user_id, 'circle_id' => $circle_id);
		}
		$insertResult = $this->_db->insertBatch('oto_user_circle', $fieldArray);
		if($delResult && $insertResult) {
			$this->_db->commit();
			return true;
		} else {
			$this->_db->rollBack();
			return false;
		}
	}
	

	public function getUserInfoByUserName($user_name_encrypt) {
		Third_Des::$key = 'IN0xMmwV';
		$user_name = Third_Des::decrypt($user_name_encrypt);
		return $this->getWebUserId($user_name);
	}
	/**
	 * 我的奖励
	 * @param unknown_type $user_id
	 */
	public function getMyBonus($user_id) {
		$logBonus = $this->_db->fetchOne("select sum(award) from `oto_task_log` where `user_id` = '{$user_id}'");
		$appBonus = $this->_db->fetchOne("select sum(amount) from `oto_task_money` where `user_id` = '{$user_id}' and `operat_status` = '1'");
		$myBonus = $logBonus - $appBonus;
		return $myBonus > 0 ? $myBonus : 0;
	}
	/**
	 * 我今日上传的商品数（已审核）
	 * @param unknown_type $user_id
	 */
	public function myTodayUploads($user_id) {
		$today_start_time = strtotime(date('Y-m-d'));
		$today_end_time = strtotime(date('Y-m-d 23:59:59'));
		return $this->_db->fetchOne("select count(good_id)
							  from `oto_good` where `user_id` = '{$user_id}'
							  and `good_status` = '1'
							  and `is_del` = '0'
							  and `created` >= '{$today_start_time}' and `created` <= '{$today_end_time}' limit 1");
	}
	/**
	 * 我总共上传的商品数（已审核）
	 * @param unknown_type $user_id
	 */
	public function getMyTotalUploads($user_id) {
		//任务开始时间
		$task_start_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']);
		return $this->_db->fetchOne("select count(good_id)
				from `oto_good` where `user_id` = '{$user_id}'
				and `good_status` = '1'
				and `is_del` = '0'
				and `created` > '{$task_start_time}' limit 1");
	}
	/**
	 * 完成任务最多的用户信息和数量
	 * @param unknown_type $field
	 * @return unknown
	 */
	public function getMaxUploads($field = 'num') {
		//任务开始时间
		$task_start_time = strtotime($GLOBALS['GLOBAL_CONF']['TASK_START_TIME']);
		$maxRow = $this->_db->fetchRow("select count(good_id) as num, user_id, user_name 
										from `oto_good`
										where `good_status` = '1' and `is_del` = '0' and `created` > '{$task_start_time}'
										group by user_id
										order by num desc
										limit 1");
		return $maxRow[$field] + $GLOBALS['GLOBAL_CONF']['TASK_SUM_GOOD'];
	}
	/**
	 * 我今天上传的商品数量（已审核）
	 * @param unknown_type $user_id
	 */
	public function getMyTodayUploads($user_id) {
		//今日日期 YYYY-MM-DD
		$today = date('Y-m-d');
		//判断是否已经完成今日任务
		$todayEveryDayRow = $this->_db->fetchRow("select effective_upload from `oto_task_every_day` where `user_id` = '{$user_id}' and `day_date` = '{$today}' limit 1");
		if($todayEveryDayRow && $todayEveryDayRow['effective_upload'] >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
			return $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER'];
		} else {
			return $this->myTodayUploads($user_id);
		}		
	}
	/**
	 * 十全大补-我当前完成度
	 * @param unknown_type $user_id
	 */
	public function getMyTenDays($user_id, $myTodayUploads) {		
		$tenDayRow = $this->_db->fetchRow("select cdays from `oto_task_ten_day_log` where `user_id` = '{$user_id}' limit 1");
		if($myTodayUploads == 0 || $myTodayUploads >= $GLOBALS['GLOBAL_CONF']['TASK_WINNING_UPPER_TRIGGER']) {
			$cdays = $tenDayRow['cdays'];
		} else {
			$cdays = $tenDayRow['cdays'] - 1;
		}
		return $cdays > 0 ? $cdays : 0;
	}
	/**
	 * 街友最划算-刮奖次数
	 * @param unknown_type $user_id
	 */
	public function getMyClientEffectiveNum($user_id) {
		$myClientEffectiveNum = $this->_db->fetchOne("select count(client_log_id) from `oto_task_client_log` where `user_id` = '{$user_id}' and `used` = '0'");
		return $myClientEffectiveNum > 0 ? $myClientEffectiveNum : 0;
	}
	/**
	 * 店员最划算-刮奖次数
	 * @param unknown_type $user_id
	 */
	public function getMyClerkEffectiveNum($user_id) {
		$myClerkEffectiveNum = $this->_db->fetchOne("select count(clerk_log_id) from `oto_task_clerk_log` where `user_id` = '{$user_id}' and `used` = '0'");
		return $myClerkEffectiveNum > 0 ? $myClerkEffectiveNum : 0;
	}
	/**
	 * 街友开始刮奖
	 * @param unknown_type $user_id
	 */
	public function clientScratchStart(& $userInfo) {
		
		$time = time();
		$clientResultMsg = array();
		$scratchRow = Custom_Scratch::client();
		$over = $this->getMyClientEffectiveNum($userInfo['user_id']);
		if($scratchRow['award'] && $over) {
			$today_start_time = strtotime(date('Y-m-d'));
			//事务开始
			$this->_db->beginTransaction();
			$scratchNum = $this->_db->fetchOne("select count(client_id) from `oto_task_client` where `award` = '{$scratchRow['award']}' and `created` > '{$today_start_time}' ");
			if($scratchNum < $scratchRow['amount']) {
				$clientLogResult = $this->taskLogReduce($userInfo['user_id'], 'client');
				$clientResult = $this->_db->insert('oto_task_client', array(
							'user_id' => $userInfo['user_id'],
							'user_name' => $userInfo['user_name'],
							'scratch_time' => $time,
							'award' => $scratchRow['award'],
							'remaining' => $over - 1,
							'created' => REQUEST_TIME
						));
				$logArr = array(
						'user_id' => $userInfo['user_id'],
						'award'   => $scratchRow['award'],
						'task_type'    => 3,
						'day_date' => date('Y-m-d'),
						'created' =>REQUEST_TIME
				);
				$logResult = $this->_db->insert('oto_task_log' , $logArr );
				if($clientLogResult && $clientResult && $logResult) {
					//事务确认
					$this->_db->commit();
					//刮奖成功
					$clientResultMsg = array(
								'msg' => 'success',
								'res' => 100,
								'over' => $this->getMyClientEffectiveNum($userInfo['user_id']),
								'award' => $scratchRow['award']
							);
				} else {
					//事务回滚
					$this->_db->rollBack();
					//刮奖失败
					$clientResultMsg = array(
								'msg' => 'failure',
								'res' => 101
							);
				}
			} else {
				$clientLogResult = $this->taskLogReduce($userInfo['user_id'], 'client');
				//事务确认
				$this->_db->commit();
				//今日奖励已发完
				$clientResultMsg = array(
						'msg' => 'exceed',
						'res' => 102,
						'over' => $this->getMyClientEffectiveNum($userInfo['user_id'])
				);
			}
		} else {
			$clientLogResult = $this->taskLogReduce($userInfo['user_id'], 'client');
			//谢谢参与
			$clientResultMsg = array(
					'msg' => 'emptyPrize',
					'res' => 103,
					'over' => $this->getMyClientEffectiveNum($userInfo['user_id'])
			);
		}
		return $clientResultMsg;
	}
	/**
	 * 店员开始刮奖
	 * @param unknown_type $user_id
	 */
	public function clerkScratchStart($userInfo) {
		$time = time();
		$clerkResultMsg = array();
		$scratchRow = Custom_Scratch::clerk();
		$over = $this->getMyClerkEffectiveNum($userInfo['user_id']);
		if($scratchRow['award'] && $over) {
			$today_start_time = strtotime(date('Y-m-d'));
			//事务开始
			$this->_db->beginTransaction();
			$scratchNum = $this->_db->fetchOne("select count(clerk_id) from `oto_task_clerk` where `award` = '{$scratchRow['award']}' and `created` > '{$today_start_time}' ");
			if($scratchNum < $scratchRow['amount']) {
				$clientLogResult = $this->taskLogReduce($userInfo['user_id'], 'clerk');
				$clientResult = $this->_db->insert('oto_task_clerk', array(
						'user_id' => $userInfo['user_id'],
						'user_name' => $userInfo['user_name'],
						'scratch_time' => $time,
						'award' => $scratchRow['award'],
						'remaining' => $over - 1,
						'created' => REQUEST_TIME
				));
				$logArr = array(
						'user_id' => $userInfo['user_id'],
						'award'   => $scratchRow['award'],
						'task_type'    => 4,
						'day_date' => date('Y-m-d'),
						'created' =>REQUEST_TIME
				);
				$logResult = $this->_db->insert('oto_task_log' , $logArr );
				
				if($clientLogResult && $clientResult && $logResult) {
					//事务确认
					$this->_db->commit();
					//刮奖成功
					$clerkResultMsg = array(
							'msg' => 'success',
							'res' => 100,
							'over' => $this->getMyClerkEffectiveNum($userInfo['user_id']),
							'award' => $scratchRow['award']
					);					
				} else {
					//事务回滚
					$this->_db->rollBack();
					//刮奖失败
					$clerkResultMsg = array(
							'msg' => 'failure',
							'res' => 101
					);
				}
			} else {
				$clientLogResult = $this->taskLogReduce($userInfo['user_id'], 'clerk');
				//事务确认
				$this->_db->commit();
				//今日奖励已发完
				$clerkResultMsg = array(
						'msg' => 'exceed',
						'res' => 102,
						'over' => $this->getMyClerkEffectiveNum($userInfo['user_id'])
				);
			}
		} else {
			$clientLogResult = $this->taskLogReduce($userInfo['user_id'], 'clerk');
			//谢谢参与
			$clerkResultMsg = array(
					'msg' => 'emptyPrize',
					'res' => 103,
					'over' => $this->getMyClerkEffectiveNum($userInfo['user_id'])
			);
		}
		return $clerkResultMsg;
	}
	/**
	 * 减少一次中奖机会
	 */
	private function taskLogReduce($user_id, $task_type) {
		if($task_type == 'client') {
			return $this->_db->update('oto_task_client_log', array('used' => 1), array('user_id' => $user_id, 'used' => 0));
		} elseif($task_type == 'clerk') {

			return $this->_db->update('oto_task_clerk_log', array('used' => 1), array('user_id' => $user_id, 'used' => 0));
		}
	}
	
	public function getTaskInfo(& $userInfo, $page, $pagesize = PAGESIZE) {
		$data = array();
		$totalNum = $this->_db->fetchOne("select count(log_id) from `oto_task_log` where `user_id` = '{$userInfo['user_id']}' and `task_type` <> '5'");
		$pageTotal = ceil($totalNum / $pagesize);
		if($page > $pageTotal) {$page = $pageTotal;}
		$sql = "select award, task_type, created from  `oto_task_log` where `user_id` = '{$userInfo['user_id']}' and `task_type` <> '5' order by `created` desc, `log_id` desc";
		$taskData = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		$data['data'] = $taskData;
		$data['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, '/home/user/my-task-info/');
		return $data;
	}
	
	public function getTaskMoney(& $userInfo, $page, $pagesize = PAGESIZE) {
		$data = array();
		$totalNum = $this->_db->fetchOne("select count(money_id) from `oto_task_money` where `user_id` = '{$userInfo['user_id']}'");
		$pageTotal = ceil($totalNum / $pagesize);
		if($page > $pageTotal) {$page = $pageTotal;}
		$sql = "select amount, operat_status, operat_result, reason_of_failure, app_time from  `oto_task_money` where `user_id` = '{$userInfo['user_id']}' order by `app_time` desc";
		$taskMoneyData = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
		$data['data'] = $taskMoneyData;
		$data['pagestr'] = Custom_Page::get($totalNum, $pagesize, $page, '/home/user/my-task-extract/');
		return $data;
	}
	/**
	 * 提交提取申请
	 * @param unknown_type $getData
	 * @param unknown_type $userInfo
	 */
	public function addTaskMoney($getData, & $userInfo) {
        if($getData['type'] =='alipay'){
            $param = array(
                        'user_id' => $userInfo['user_id'],
                        'user_name' => $userInfo['user_name'],
                        'user_type' => $userInfo['user_type'],
                        'amount' => $getData['money'],
                        'paypal_name' => Custom_String::HtmlReplace($getData['realName']),
                        'paypal_account' => Custom_String::HtmlReplace($getData['paypal']),
                        'app_time' => REQUEST_TIME
                    );
        }elseif($getData['type'] =='bank'){
            $param = array(
                'user_id' => $userInfo['user_id'],
                'user_name' => $userInfo['user_name'],
                'user_type' => $userInfo['user_type'],
                'amount' => $getData['cardMoney'],
                'paypal_name' => Custom_String::HtmlReplace($getData['cardRealName']),
                'bank_name' => Custom_String::HtmlReplace($getData['bankName']),
                'bank_number' => Custom_String::HtmlReplace($getData['cardNum']),
                'app_time' => REQUEST_TIME
            );
        }
		return $this->_db->insert('oto_task_money', $param);
	}
}

