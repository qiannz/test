<?php
class Model_Api_Tianshan extends Base
{
	private $_key;
	private static $_instance;

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_key = Core_Router::getModule() . '_' . Core_Router::getController(). '_' . Core_Router::getAction() . '_';
	}
	
	//天山店首页
	public function getHome( $city , $pagesize = PAGESIZE, $is_cache = FALSE ){
		$key = $this->_key.$city;
		$data = $this->getData($key);
		if( empty($data) || !$is_cache ){
			$data = array();
			$data['tianshan_banner'] = Model_Api_App::getInstance()->getListByMark($city, 'tianshan', 'tianshan_banner', 3 );
			$data['tianshan_icon']   = Model_Api_App::getInstance()->getListByMark($city, 'tianshan', 'tianshan_icon', FALSE);
			$data['tianshan_shop']   = $this->getShopMore(array('page'=>1,'pagesize'=>$pagesize), $city);
		}
		return $data;
	}
	
	
	//推荐店铺列表	
	public function getShopMore( $getData, $city, $is_cache = FALSE ) {
		$page = intval($getData['page']);
		if( $page < 1 ){
			$page = 1;
		}
		$pagesize = intval($getData['pagesize'])?intval($getData['pagesize']):PAGESIZE;
		
		$key = $this->_key.$city.'_'.$page;
		$data = $this->getData($key);
		if( empty($data) || !$is_cache ){
			$data = array();
			$start = ($page-1)*$pagesize;
			$pmark = 'tianshan';
			$cmark = 'tianshan_shop';
			$pos_id = Model_Api_App::getInstance()->getPosIdByMark($city, $pmark, $cmark);
			
			$sql = "SELECT *
					FROM `oto_recommend`
					WHERE `city`='{$city}' AND `pos_id`='{$pos_id}'
					ORDER BY `sequence` asc, `created` desc
					LIMIT {$start},{$pagesize}";
			$data = $this->_db->fetchAll($sql);
			foreach($data as & $row) {
				if($row['img_url']) {
					$img_tmp = $row['img_url'];
						$row['img_url'] =  $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $img_tmp;
						list($row['width'], $row['height']) = getimagesize(ROOT_PATH . 'web/data/recommend/' . $img_tmp);
				} else {
					$row['img_url'] = '';
					$row['width'] = $row['height'] = 0;
				}
			}
		}
		return $data ? $data : array();
	}
}