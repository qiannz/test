<?php
class Model_Home_Search extends Base {

	private static $_instance;
	private $_table = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	

	public function getAjaxGoodList($key, $page, $limit = 20) {
		//缓存键值
		$key = Custom_String::FilterSearch($key);
		$cacheKey = 'get_search_ajax_good_list_' . " {$page}_{$key}";
		$data = $this->getData($cacheKey);
		$snapArray = $snapData = array();
		if (empty($data)) {
			if($key) {
				$orderby = 'order by `created` desc';
				$where = "A.`good_status` <> '-1' and A.`is_auth` <> '-1' and A.`is_del` = '0' and `good_name` like '%".$key."%'" ;
				$sqlC = "select count(A.good_id) from `oto_good` A where {$where}";
				$totalNum = $this->_db->fetchOne($sqlC);
		
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
				$snapData['data'] = $snapArray;
			} else {
				$snapData['totalNum'] = 0;
				$snapData['data'] = '';
			}
			$data = $snapData;
			unset($snapArray, $snapData);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}
}