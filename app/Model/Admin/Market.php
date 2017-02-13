<?php
class Model_Admin_Market extends Base
{
	private static $_instance;
	private $_table = 'oto_market';
	private $_where;
	private $_order;

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
	
	public function setWhere(& $getData) {
		$where = " and `city` = '{$this->_ad_city}'";
		if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'market':
							if($value) {
								$where .= " and `market_name` like '%".trim($value)."%'";
							}
							break;
						case 'ist':
							if($value) {
								$where .= " and `is_show` = '{$value}'";
							}
							break;
					}
				}
			}
		}
		$this->_where .= $where;		
	}
	
	public function setOrder(& $getData) {
		if($getData['ist'] == 1) {
			$order = ' order by `sequence` asc,  `market_id` desc';
		} else {
			$order = ' order by `market_id` desc';
		}
		$this->_order = $order;
	}

	public function getCount() {
		return $this->_db->fetchOne("select count(market_id) from `".$this->_table."` where 1 = 1".$this->_where);
	}
	
	public function getMarketList($page, $pagesize = PAGESIZE) {
		$start = ($page - 1) * $pagesize;
		$sql = "select * from `".$this->_table."` where 1 ".$this->_where.$this->_order;
		$data = $this->_db->limitQuery($sql, $start, $pagesize);
		foreach ($data as & $row) {
			$row['region_name'] = $this->getRegion($row['region_id'], true, $this->_ad_city);
		}
		return $data ? $data : array();		
	}
	
	public function marketSync() {
		$marketList = Custom_AuthTicket::getMarketPlaceList(300);
		if($marketList['code'] == 1) {
			foreach ($marketList['message'] as $marketItem) {
				if($marketItem['Status'] == 1) {
					$num = $this->_db->fetchOne("select 1 from `oto_market` where `market_uid` = '{$marketItem['ID']}'");
					if($num == 1) {
						$this->_db->update('oto_market', array(
									'market_name' => saddslashes($marketItem['Name']),
									'market_address' => saddslashes($marketItem['Address']),
									'lng' => $marketItem['Longitude'],
									'lat' => $marketItem['Latitude'],
									'intro' => saddslashes($marketItem['Intro']),
									'trafficInfo' => saddslashes($marketItem['TrafficInfo']),
									'area' => $marketItem['CityArea'],
									'tel' => $marketItem['Phone']
								),
								"`market_uid` = '{$marketItem['ID']}'"
								);
					} else {
						$this->_db->insert('oto_market', array(
								'market_uid' => $marketItem['ID'],
								'market_name' => saddslashes($marketItem['Name']),
								'market_address' => saddslashes($marketItem['Address']),
								'lng' => $marketItem['Longitude'],
								'lat' => $marketItem['Latitude'],
								'intro' => saddslashes($marketItem['Intro']),
								'trafficInfo' => saddslashes($marketItem['TrafficInfo']),
								'area' => $marketItem['CityArea']
						));				
					}
				}
			}
			return true;
		}
		return false;
	}
	
	public function marketEdit($postData) {
		$market_id = intval($postData['market_id']);
		$market_name   = trim($postData['market_name']);
		$market_address = trim($postData['market_address']);
		
		$region_id = intval($postData['region_id']);
		$circle_id = intval($postData['circle_id']);
		
		$tel = trim($postData['tel']);
		$head_img = $postData['m_headImg'];
		$logo_img = $postData['logoImg'];
		$trafficInfo = $postData['trafficInfo'] ? $postData['trafficInfo'] : '';
		$is_show = intval($postData['is_show']);
		
		$arr = array(
				'market_name'       => $market_name,
				'region_id'         => $region_id,
				'circle_id'         => $circle_id,
				'market_address'    => $market_address,
				'trafficInfo'       => $trafficInfo,
				'tel'               => $tel,
				'head_img'          => $head_img,
				'logo_img'          => $logo_img,
				'is_show'			=> $is_show
				);
		
		if($market_address) {
			$cityArray = $this->_city_options;
			$city = $cityArray[$this->_ad_city];
			$area = $this->getRegion($region_id);
			$lngLatArray = $this->getLatitudeAndLongitudeFromBaiDu($city.$area.$market_address);
			if($lngLatArray['lng'] && $lngLatArray['lat']) {
				$arr = array_merge($arr, array('lng' => $lngLatArray['lng'], 'lat' => $lngLatArray['lat']));
			}
		}
	
		if($market_id) {
			return $this->_db->update('oto_market', $arr,"`market_id` = $market_id");			
		} else {
			$arr['city'] = $this->_ad_city;
			$insert_id = $this->_db->insert('oto_market', $arr);
			return $insert_id;
		}
		
	}
	
	public function getPositionForPicture() {
		$position = $positionArray = $data = array();
		$params = array('market_logo', 'market_head');
		$position = $this->getTheRecommendedPosition($params, null, true, $this->_ad_city);
		foreach($position as $positionItem) {
			$positionArray[$positionItem['identifier']] = $positionItem;
		}
		$data['logo']  = $positionArray['market_logo'];
		$data['head']   = $positionArray['market_head'];

		return $data;		
	}
	
	public function ajax_module_edit($getData){
		$column = $getData['column'];
		$id = $getData['id'];
		$value = $getData['value'];
	
		return $this->_db->update('oto_market',array($column => $value), "`market_id` = $id");
	}
	
	public function recommend_back($id) {
		$sql = "update oto_market set is_show = (case is_show when 1 then 0 else 1 end) where `market_id` = '{$id}' ";
		return $this->_db->query($sql);
	}
	
	public function recommend($getData) {		
		if (in_array($getData['identifier'], array('market_recom', 'discount_market_recommend')) ) {
			$www_url = $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/market/wap/mid/' . $getData['id'];
		} else {
			$www_url = '/home/market/show/mid/' . $getData['id'];
		}
		
		$arr = array(
				'come_from_id' => $getData['id'],
				'come_from_type' => 4,
				'title' => saddslashes($getData['title']),
				'summary' => saddslashes($getData['summary']),
				'pos_id' => $getData['pos_id'],
				'www_url' => $www_url,
				'img_url' => $getData['img_url'],
				'created' => REQUEST_TIME,
				'updated' => REQUEST_TIME,
				'pmark' => 'market',
				'cmark' => 'market_view',
				'city'	=> $this->_ad_city
		);
		return $this->_db->insert('oto_recommend', $arr);
	}
	
	
	public function del($id) {
		return $this->_db->delete('oto_market', "`market_id` = $id");		
	}
	
	public function getMarketRow($id, $city) {
		$row = $this->select("`market_id` = '{$id}' and city = '{$city}'", 'oto_market', '*', '', true);
		return $row;
	}
	
	public function checkRecommend($come_from_id, $pos_id) {
		return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '4' and `pos_id` = '{$pos_id}' limit 1") == 1;
	}
}