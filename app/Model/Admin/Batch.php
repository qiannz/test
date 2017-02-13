<?php
class Model_Admin_Batch extends Base {
	
	private static $_instance;
	private $_where;
	private $_order;
	private $_table = 'oto_good_batch';
	
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
	
	public function getCount() {
		return $this->_db->fetchOne("select count(batch_id) from `".$this->_table."` where ".$this->_where);
	}
	
	public function setWhere($getData) {
		$where = "`city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'batch':
							if($value) {
								$where .= " and `good_batch` = '".trim($value)."'";
							}
							break;
						case 'bname':
							if($value) {
								$brandRow = Model_Admin_Brand::getInstance()->getBrandRowByName($value, $this->_ad_city);
								if($brandRow) {
									$where .= " and `brand_id` = '{$brandRow['brand_id']}'";
								}
							}
							break;
						case 'stime':
							if(strtotime($value . ':00:00') !==  false) {
								$where .= " and `created` >= '". strtotime($value . ':00:00') ."'";
							}
							break;
						case 'etime':
							if(strtotime($value . ':00:00') !==  false) {
								$where .= " and `created` <= '". strtotime($value . ':00:00') ."'";
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
	
	public function getList($page, $city, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where " . $this->_where . $this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($data as & $row) {
			$shopRow = Model_Admin_Shop::getInstance()->getShopRow($row['shop_id']);
			$row['shop_name'] = $shopRow['shop_name'];
			$brandRow = Model_Admin_Brand::getInstance()->getBrandRow($row['brand_id'], $city);
			$row['brand_name'] = $brandRow['brand_name'];
			$adminRow = Model_Admin_Manager::getInstance()->getMangerByUserId($row['creator']);
			$row['creator_user_name'] = $adminRow['userid'];
		}
		return $data ? $data : array();
	}
	/**
	 * 获取最新批次
	 */
	public function getLatestBatchNumber() {
		$year_month_day = datex(REQUEST_TIME, 'Y-m-d');
		$sql = "select good_batch from `{$this->_table}` where `year_month_day` = '{$year_month_day}' order by batch_id desc limit 1";
		$good_batch = $this->_db->fetchOne($sql);
		if($good_batch) {
			return $good_batch + 1;
		} else {
			return datex(REQUEST_TIME, 'Ymd0001');
		}
	}
	/**
	 * 根据店铺ID获取对应品牌，然后获取品牌对应颜色、尺寸 ，格式化数据
	 * @param unknown_type $shop_id
	 */
	public function getFormatColorSize($shop_id) {
		$brandColorSizeSnap = array();
		$shopRow = Model_Admin_Shop::getInstance()->getShopRow($shop_id);
		if($shopRow['brand_id']) {
			$brandColorSizeArr = Model_Admin_Brand::getInstance()->getColorSizeListByBrandId($shopRow['brand_id']);
			if($brandColorSizeArr) {
				foreach($brandColorSizeArr as $ColorSize) {
					$brandColorSizeSnap[$ColorSize['type']][$ColorSize['number']] = $ColorSize['name'];
				}
			}
		}
		return $brandColorSizeSnap;	
	}
	
	public function dataProcessingSnap($getData, & $file, $city) {
		$data = $snapArr = array();
		$data['detail']['sid'] = $getData['sid'];
		$data['detail']['sname'] = $getData['sname'];
		$data['detail']['good_batch'] = $getData['good_batch'];
		$data['detail']['stime'] = $getData['stime'];
		$data['detail']['etime'] = $getData['etime'];
		//根据店铺ID获取对应品牌，然后获取品牌对应颜色、尺寸 ，格式化数据
		$brandColorSizeSnap = $this->getFormatColorSize($getData['sid']);
		if($file['uploadFile']['error'] == 0) {
			require_once ROOT_PATH . 'lib/PHPExcel.php';
			$objPHPExcel = new PHPExcel();
			$inputFileName = $file['uploadFile']['tmp_name'];
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			$tbHeadStr = '<table width="100%" cellspacing="0" class="dataTable">';
			$tbTitleStr = '<tr class="tatr1">
				<td>品类</td>
				<td>品名</td>
				<td>货号</td>
				<td>条形码</td>
				<td>质地</td>
				<td>适合人群</td>
				<td>年份</td>
				<td>季节</td>
				<td>颜色</td>
				<td>尺码</td>
				<td>数量</td>
				<td>原价</td>
				<td>现价</td>
				<td>卖点</td>
				<td>简介</td>
				<td>是否包邮</td>
				<td>详情</td></tr>';
			
			$tbBodyStr = '';
			$can_sub = 0;
			foreach($sheetData as $key => $item) {
				$tbBodyStr .= '<tr class="tatr2">';
				$tbBodyTdStr = '';
				foreach ($item as $skey => $value) {
					$value = trim($value);
					switch($skey) {
						//品类
						case 0:
							if(empty($value)) {
								$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
								$can_sub = 1;
							} else {
								$storeArr = $this->getStore(0, true, false, $city);
								if(!in_array($value, $storeArr)) {
									$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
									$can_sub = 1;
								} else {
									$snapArr[$key][$skey] = array('mark' => 0, 'value' => $value);
								}
							}
							break;
						//品名
						case 1:
						//货号
						case 2:
						//条形码
						case 3:
						//质地
						case 4:
						//适合人群
						case 5:
						//年份
						case 6:
						//季节
						case 7:
							if(empty($value)) {
								$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
								$can_sub = 1;
							} else {
								$snapArr[$key][$skey] = array('mark' => 0, 'value' => $value);
							}
							break;
						//颜色
						case 8:	
							if(empty($value)) {
								$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
								$can_sub = 1;
							} else {
								if($brandColorSizeSnap[1][$value]) {
									$snapArr[$key][$skey] = array('mark' => 0, 'value' => $value);
								} else {
									$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
									$can_sub = 1;
								}
							}					
							break;
						//尺码
						case 9:
							if(empty($value)) {
								$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
								$can_sub = 1;
							} else {
								if($brandColorSizeSnap[2][$value]) {
									$snapArr[$key][$skey] = array('mark' => 0, 'value' => $value);
								} else {
									$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
									$can_sub = 1;
								}
							}
							break;
						//数量
						case 10:
						//原价
						case 11:
						//现价
						case 12:
						//卖点
						case 13:
						//简介
						case 14:
						//是否包邮
						case 15:
						//详情
						case 16:
							if(empty($value)) {
								$snapArr[$key][$skey] = array('mark' => 1, 'value' => $value);
								$can_sub = 1;
							} else {
								$snapArr[$key][$skey] = array('mark' => 0, 'value' => $value);
							}
							break;
					}
					$tbBodyTdStr .= '<td style="background-color: #FFB6C1">' . $value . '</td>';
				}
				$tbBodyStr .=  $tbBodyTdStr . '</tr>';
			}
			
			$tbFooterStr = '</table>';
						
			$data['data'] = $snapArr;
			$data['can_sub'] = $can_sub;
			return $data;
		}
	}
	/**
	 * 批次入库
	 * @param unknown_type $data
	 * @param unknown_type $userInfo
	 * @param unknown_type $city
	 */
	public function addBatchResult($data, $userInfo, $city) {
		$snapArr = array();
		$quantity = 0;
		//匹配赋值
		foreach($data['data'] as $key => $item) {
			foreach ($item as $skey => $sitem) {
				switch($skey) {
					//品类
					case 0:
						$storeArr = $this->getStore(0, true, false, $city);
						foreach ($storeArr as $store_id => $store_value) {
							if($sitem['value'] == $store_value) {
								$snapArr[$key]['ticket_sort'] = $store_id;
								break;
							}
						}
						break;
					//品名
					case 1:
						$snapArr[$key]['ticket_title'] = $sitem['value'];
						break;
					//货号
					case 2:
						$snapArr[$key]['good_number'] = $sitem['value'];
						break;
					//条形码
					case 3:
						$snapArr[$key]['good_barcode'] = $sitem['value'];
						break;
					//质地
					case 4:
						$snapArr[$key]['good_texture'] = $sitem['value'];
						break;
					//适合人群
					case 5:
						$snapArr[$key]['good_match_crowd'] = $sitem['value'];
						break;
					//年份
					case 6:
						$snapArr[$key]['good_years'] = $sitem['value'];
						break;
					//季节
					case 7:
						$snapArr[$key]['good_season'] = $sitem['value'];
						break;
					//颜色
					case 8:
						$snapArr[$key]['good_color'] = $sitem['value'];
						break;
					//尺码
					case 9:
						$snapArr[$key]['good_size'] = $sitem['value'];
						break;
					//数量
					case 10:
						//总数量
						$quantity += $sitem['value'];
						$snapArr[$key]['good_quantity'] = $sitem['value'];
						break;
					//原价
					case 11:
						$snapArr[$key]['par_value'] = $sitem['value'];
						break;
					//现价
					case 12:
						$snapArr[$key]['selling_price'] = $sitem['value'];
						break;
					//卖点
					case 13:
						$snapArr[$key]['selling_points'] = $sitem['value'];
						break;
					//简介
					case 14:
						$snapArr[$key]['ticket_summary'] = $sitem['value'];
						break;
					//是否包邮
					case 15:
						if($sitem['value'] == '是') {
							$snapArr[$key]['free_shipping'] = 1;
						} elseif($sitem['value'] == '否') {
							$snapArr[$key]['free_shipping'] = 0;
						}
						break;
					//详情
					case 16:
						$snapArr[$key]['wap_content'] = $sitem['value'];
						break;
				}
			}
		}
		//合并去重(货号为唯一标识)，组合SKU
		$snapTampArr = array();
		foreach($snapArr as $key => $row) {
			$snapTampArr[$row['good_number']]['ticket_sort'] = $row['ticket_sort'];
			$snapTampArr[$row['good_number']]['ticket_title'] = $row['ticket_title'];
			$snapTampArr[$row['good_number']]['good_barcode'] = $row['good_barcode'];
			$snapTampArr[$row['good_number']]['good_texture'] = $row['good_texture'];
			$snapTampArr[$row['good_number']]['good_match_crowd'] = $row['good_match_crowd'];
			$snapTampArr[$row['good_number']]['good_years'] = $row['good_years'];
			$snapTampArr[$row['good_number']]['good_season'] = $row['good_season'];
			
			$color_size = $row['good_color'] . '_' . $row['good_size'];
			if(isset($snapTampArr[$row['good_number']]['sku'][$color_size])) {
				$snapTampArr[$row['good_number']]['sku'][$color_size]['good_quantity'] += $row['good_quantity'];
			} else {
				$snapTampArr[$row['good_number']]['sku'][$color_size] = array(
						'good_color' => $row['good_color'],
						'good_size'  => $row['good_size'],
						'good_quantity' => $row['good_quantity'],
				);
			} 
			
			$snapTampArr[$row['good_number']]['par_value'] = $row['par_value'];
			$snapTampArr[$row['good_number']]['selling_price'] = $row['selling_price'];
			$snapTampArr[$row['good_number']]['selling_points'] = $row['selling_points'];
			$snapTampArr[$row['good_number']]['ticket_summary'] = $row['ticket_summary'];
			$snapTampArr[$row['good_number']]['free_shipping'] = $row['free_shipping'];
			$snapTampArr[$row['good_number']]['wap_content'] = $row['wap_content'];
		}
	
		//事务开始
		$this->_db->beginTransaction();
		
		//入库批次数据
		$shopRow = Model_Admin_Shop::getInstance()->getShopRow($data['detail']['sid']);
		$batchParam = array(
					'good_batch' 	=>	$data['detail']['good_batch'],
					'shop_id'	 	=>	$data['detail']['sid'],
					'brand_id'		=>	$shopRow['brand_id'],
					'stime'			=>	strtotime($data['detail']['stime']),
					'etime'			=>	strtotime($data['detail']['etime']),
					'year_month_day'=>	datex(REQUEST_TIME, 'Y-m-d'),
					'creator'		=>	$userInfo['id'],
					'quantity'		=>	$quantity,
					'city'			=>	$city,
					'created'		=> 	REQUEST_TIME,
				);
		//入库批次 insert_id
		$batch_id = $this->_db->insert('oto_good_batch', $batchParam);
		$insertStatus = true;
		//开始插入数据
		foreach($snapTampArr as $good_number => $good_item) {			
			//货号存在
			if($this->isTicketBatchExist($good_number)) {
				$batchGoodRow = $this->getTicketBatchRowByGoodNumber($good_number);
				$ticket_id = $batchGoodRow['ticket_id'];
				//改变商品数量
				$this->updateQuantityBatchGood($batch_id, $ticket_id, $good_item);
			} else {
				//商品记录入库
				$ticket_id = $this->addTicketBatchGood($batch_id, $good_number, $good_item, $data['detail'], $shopRow, $city);
				if(!$ticket_id) {
					$insertStatus = false;
					break;
				}
			}
			//商品SKU操作
			$this->updateQuantityBatchGoodSku($ticket_id, $good_item);
		}
		
		if($insertStatus) {
			//事务确认
			$this->_db->commit();
			return true;
		} else {
			//事务回滚
			$this->_db->rollBack();
			return false;
		}		
	}
	/**
	 * 商品记录入库
	 * @param unknown_type $batch_id
	 * @param unknown_type $good_number
	 * @param unknown_type $good_item
	 * @param unknown_type $good_detail
	 * @param unknown_type $shopRow
	 * @param unknown_type $city
	 */
	public function addTicketBatchGood($batch_id, $good_number, $good_item, $good_detail, $shopRow, $city) {
		//获取商品分类ID
		$ticket_type = $this->getTicketSortById(0, 'ticketsort', 'warehousing');
		$user_id = $this->getUserIdByUserName(DEFINED_USER_NAME);
		
		$good_quantity = 0;
		foreach($good_item['sku'] as & $row) {
			$good_quantity += $row['good_quantity'];
		}
		
		$paramTicket = array(
				'ticket_title' 		=> 		$good_item['ticket_title'],
				'ticket_type' 		=> 		$ticket_type,
				'ticket_sort'		=> 		$good_item['ticket_sort'],
				'ticket_summary' 	=> 		$good_item['ticket_summary'],
				'user_id' 			=> 		$user_id,
				'user_name' 		=> 		DEFINED_USER_NAME,
				'brand_id' 			=> 		$shopRow['brand_id'],
				'store_id' 			=> 		$shopRow['store_id'],
				'region_id' 		=> 		$shopRow['region_id'],
				'circle_id' 		=> 		$shopRow['circle_id'],
				'shop_id' 			=> 		$good_detail['sid'],
				'shop_name' 		=> 		$good_detail['sname'],
				'par_value' 		=> 		$good_item['par_value'],
				'selling_price' 	=> 		$good_item['selling_price'],
				'app_price' 		=> 		$good_item['selling_price'],
				'start_time' 		=> 		strtotime($good_detail['stime']),
				'end_time' 			=> 		strtotime($good_detail['etime']),
				'wap_content' 		=> 		$good_item['wap_content'],
				'total' 			=> 		$good_quantity,
				'is_show'			=>		1,
				'city' 				=> 		$city,
				'created' 			=> 		REQUEST_TIME,
				'updated'			=>		REQUEST_TIME
		);

		$ticket_id = $this->_db->insert('oto_ticket', $paramTicket);

		$paramTicketBatch = array(
					'ticket_id'			=>		$ticket_id,
					'good_batch'		=>		$good_detail['good_batch'],
					'good_number'		=>		$good_number,
					'good_barcode'		=>		$good_item['good_barcode'],
					'good_texture'		=>		$good_item['good_texture'],
					'good_match_crowd'	=>		$good_item['good_match_crowd'],
					'good_years'		=>		$good_item['good_years'],
					'good_season'		=>		$good_item['good_season'],
				);
		if($this->_db->insert('oto_ticket_batch', $paramTicketBatch)) {
			return $ticket_id;
		} else {
			return false;
		}			
	}
	/**
	 * 改变商品SKU
	 * @param unknown_type $ticket_id
	 * @param unknown_type $good_item
	 */
	public function updateQuantityBatchGoodSku($ticket_id, $good_item) {
		foreach($good_item['sku'] as & $row) {
			$sql = "insert into `oto_ticket_sku` 
					(`ticket_id`, `good_color`, `good_size`, `good_warehouse_num`) 
					values 
					('{$ticket_id}', '{$row['good_color']}', '{$row['good_size']}', '{$row['good_quantity']}') 
					ON DUPLICATE KEY UPDATE `good_warehouse_num` = `good_warehouse_num` + {$row['good_quantity']}";	
			$this->_db->query($sql);
		}
	}
	/**
	 * 改变商品数量
	 * @param unknown_type $batch_id
	 * @param unknown_type $ticket_id
	 * @param unknown_type $good_item
	 */
	public function updateQuantityBatchGood($batch_id, $ticket_id, $good_item) {
		$good_quantity = 0;
		foreach($good_item['sku'] as & $row) {
			$good_quantity += $row['good_quantity'];
		}
		$updated = REQUEST_TIME;
		$sql = "update `oto_ticket` set total = total + {$good_quantity}, updated = '{$updated}' where `ticket_id` = '{$ticket_id}'";
		if($this->_db->query($sql)) {
			//批次入库数量日志
			$this->insertBatchGoodQuantityLog($batch_id, $ticket_id, $good_quantity, 2);
		}				
	}
	/**
	 * 批次入库数量日志
	 * @param unknown_type $batch_id
	 * @param unknown_type $ticket_id
	 * @param unknown_type $good_quantity
	 * @param unknown_type $type
	 */
	public function insertBatchGoodQuantityLog($batch_id, $ticket_id, $good_quantity, $type) {
		return $this->_db->insert('oto_ticket_batch_log', array(
					'batch_id' => $batch_id,
					'ticket_id'=> $ticket_id,
					'good_quantity' => $good_quantity,
					'type' => $type,
					'created' => REQUEST_TIME
				));
	}
	/**
	 * 检测商品是否已存在
	 * @param unknown_type $good_number
	 */
	public function isTicketBatchExist($good_number) {
		return $this->_db->fetchOne("select 1 from `oto_ticket_batch` where `good_number` = '{$good_number}' limit 1") == 1;
	}
	/**
	 * 根据货号获取基本信息
	 * @param unknown_type $good_number
	 */
	public function getTicketBatchRowByGoodNumber($good_number) {
		return $this->select_one("`good_number` = '{$good_number}'", 'oto_ticket_batch');
	}
}