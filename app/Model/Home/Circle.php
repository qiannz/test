<?php
class Model_Home_Circle extends Base {

	private static $_instance;
	private $_table = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getAjaxGoodList($page, $uid, $limit = 20) {
		//缓存键值
		$cacheKey = 'get_my_circle_ajax_good_list_' . " {$page}_{$uid}";
		$data = $this->getData($cacheKey);
		$snapArray = $snapData = array();
		if (empty($data)) {
			$circle_id = $this->_db->fetchCol("select circle_id from oto_user_circle where user_id = '{$uid}'");
			if($circle_id) {
				$orderby = 'order by `created` desc';			
				$where = "A.`good_status` <> '-1' and A.`is_auth` <> '-1' and A.`is_del` = '0' and " . $this->db_create_in($circle_id, 'A.`circle_id`');
				
				$sql = "select circle_name from oto_circle where ".$this->db_create_in($circle_id, 'circle_id');
				$circle_name = $this->_db->fetchCol($sql);
				$circle_name = implode(',', $circle_name);
	
				$sqlC = "select count(A.good_id) from `oto_good` A where {$where}";
				$totalNum = $this->_db->fetchOne($sqlC);
				
				
				$sqlD = "select count(circle_id) from `oto_user_circle` where user_id = '{$uid}'";
				$totalCircleNum = $this->_db->fetchOne($sqlD);
					
				$sql = "select
				`good_id`, `good_name`, `shop_id`, `shop_name`, `dis_price`, `favorite_number`, `concerned_number`,
				(select `img_url` from `oto_good_img` where `good_id` = A.good_id order by is_first desc, good_img_id asc limit 1) as `img_url`
				from `oto_good` A
				where {$where} {$orderby}";
				$snapArray = $this->_db->limitQuery($sql, ($page - 1) * $limit, $limit);
				foreach($snapArray as $key => $snap) {
					if($snap['img_url'] && is_file(ROOT_PATH . 'web/data/good/220/' . $snap['img_url'])) {
						list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/good/220/' . $snap['img_url']);
						$snapArray[$key]['img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/good/220/' . $snap['img_url'];
					} else {
						list($width, $height, $type, $attr) = getimagesize(ROOT_PATH . 'web/data/default.jpg');
						$snapArray[$key]['img_url'] = $GLOBALS['GLOBAL_CONF']['SITE_URL']. '/data/default.jpg';
					}
					$snapArray[$key]['width'] = $width;
					$snapArray[$key]['height'] = $height;
					$snapArray[$key]['dis_price'] = floor($snap['dis_price']);
				}
				
				$snapData['totalNum'] = $totalNum;
				$snapData['totalCircleNum'] = $totalCircleNum;
				$snapData['circle_name'] = $circle_name;
				$snapData['data'] = $snapArray;				
			} else {
				$snapData['totalNum'] = 0;
				$snapData['totalCircleNum'] = 0;
				$snapData['circle_name'] = '';
				$snapData['data'] = $snapArray;				
			}
			$data = $snapData;
			unset($snapArray, $snapData);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}

}