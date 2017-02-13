<?php
/**
 * 控制器基类
 */
 class Base {
    protected $_tpl;
    protected $_http;
    protected $_db;
    protected $_sess;
    protected $_file;
    protected $_mem;
    protected $_ad_city;
    protected $_city;
    protected $_platform;
    /**
     * 构造函数
     */
    public function __construct() {
    	$this->_http = Core_Request::getInstance();
    	$this->_db = Core_DB::get('superbuy', null, true);
    	$this->_tpl = Third_Template::getInstance()->get();
    	$this->_file = Core_Cache_File::getInstance();
    	$this->_mem = Core_Cache_Memcache::getInstance();
    	$this->_ad_city = !$_COOKIE['_ad_city'] ? 'sh' : $_COOKIE['_ad_city'];
    	$this->_city = !$_COOKIE['_city'] ? 'sh' : $_COOKIE['_city'];
    	//来源
    	$this->_platform = !$_COOKIE['_platform'] ? 'wap' : $_COOKIE['_platform'];;
    }
    
	/**
	 * 条件获取结果集
	 * @param unknown_type $where 查询条件
	 * @param unknown_type $table 查询表
	 * @param unknown_type $order order条件
	 * @param unknown_type $limit 获取一条记录
	 */
    public function select($where = '', $table = null, $field = '*', $order = '', $limit = false) {
    	$orderby = !empty($order)?" order by {$order}":"";
    	$limitStr = $limit?"limit ".intval($limit):"";
    	$where = $where ? 'where '. $where: '';
    	if($field != '*') {
	    	$fieldArray = explode(',', $field);
	    	array_walk($fieldArray, array($this, 'add_special_char'));
	    	$field = implode(',', $fieldArray);
    	}
    	if(is_null($table)){
    		$sql = "select {$field} from `{$this->_table}` {$where} {$orderby} {$limitStr}";
    	}else{
    		$sql = "select {$field} from `{$table}` {$where} {$orderby} {$limitStr}";
    	}
    	if(intval($limit) == 1) {
    		return $this->_db->fetchRow($sql);
    	} else {
    		return $this->_db->fetchAll($sql);
    	}
    }
    /**
     * 获取唯一一条记录
     * @param unknown_type $where
     * @param unknown_type $table
     */
    public function select_one($where, $table, $field = '*') {
    	return $this->select($where, $table, $field, '', true);
    }
    /**
     * 数组生成文件
     * @param unknown_type $data
     * @param unknown_type $filename
     * @param unknown_type $path
     */
    public function array_to_file($data, $filename, $path = 'config') {    	 
    	if(!empty($data)) {   		
    		$path = VAR_PATH.$path.'/';
    		$filename = $path.$filename.'.php';
    		if(!is_dir($path))
    		{
    			make_dir($path);
    		}
    		file_put_contents($filename, "<?php\r\n return ". var_export($data, true).';');
    	}
    }
    /**
     * 创建像这样的查询: "IN('a','b')";
     *
     * @access   public
     * @param    mix      $item_list      列表数组或字符串,如果为字符串时,字符串只接受数字串
     * @param    string   $field_name     字段名称
     * @author   qiannz
     *
     * @return   void
     */
    public function db_create_in($item_list, $field_name = '') {
    	if (empty($item_list))
    	{
    		return $field_name . " IN ('') ";
    	}
    	else
    	{
    		if (!is_array($item_list))
    		{
    			$item_list = explode(',', $item_list);
    			foreach ($item_list as $k=>$v)
    			{
    				$item_list[$k] = intval($v);
    			}
    		}
    
    		$item_list = array_unique($item_list);
    		$item_list_tmp = '';
    		foreach ($item_list AS $item)
    		{
    			if ($item !== '')
    			{
    				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
    			}
    		}
    		if (empty($item_list_tmp))
    		{
    			return $field_name . " IN ('') ";
    		}
    		else
    		{
    			return $field_name . ' IN (' . $item_list_tmp . ') ';
    		}
    	}
    }
    /**
     * 获取缓存
     * @param unknown_type $key 缓存KEY
     * @return multitype:
     */
    public function getData($key) {
    	$data = array();
    	/*
    	if($GLOBALS['GLOBAL_CONF']['File_Cache_Enabled']){
    		$data = $this->_file->get($key);
    	}*/
    	
    	if($GLOBALS['GLOBAL_CONF']['Mem_Cache_Enabled']){
    		$data = $this->_mem->get($key);
    	}
    	return $data;
    }
    /**
     * 设置缓存
     * @param unknown_type $key 缓存KEY
     * @param unknown_type $data 缓存数据
     * @param unknown_type $lifetime 缓存时间
     */
    public function setData($key, $data, $lifetime = 0) {
    	if(empty($lifetime)){
    		$lifetime = $GLOBALS['GLOBAL_CONF']['Cache_Life_Time'];
    	}
    	/*
    	if($GLOBALS['GLOBAL_CONF']['File_Cache_Enabled']){
    		$this->_file->set($key, $data, $lifetime);
    	}
    	*/
    	if($GLOBALS['GLOBAL_CONF']['Mem_Cache_Enabled']){
    		$this->_mem->set($key, $data, $lifetime);
    	}
    }

    public function _get_page($page, $page_size = PAGESIZE) {
    	$start = ($page -1) * $page_size;
    	return array('limit' => "{$start},{$page_size}", 'curr_page' => $page, 'pageper' => $page_size);
    }
    
    function _format_page(&$page_info, $num = 7) {
    	$page_info['page_count'] = ceil($page_info['item_count'] / $page_info['pageper']);
    	$mid = ceil($num / 2) - 1;
    	if ($page_info['page_count'] <= $num)
    	{
    		$from = 1;
    		$to   = $page_info['page_count'];
    	}
    	else
    	{
    		$from = $page_info['curr_page'] <= $mid ? 1 : $page_info['curr_page'] - $mid + 1;
    		$to   = $from + $num - 1;
    		$to > $page_info['page_count'] && $to = $page_info['page_count'];
    	}
    
    	$url_format =  $GLOBALS['GLOBAL_CONF']['FORM_ACTION'];
    	if($page_info['page_str']){
    		$url = $url_format.'/'.$page_info['page_str'].'page:';
    	}else{
    		$url = $url_format.'/page:';
    	}
    	
    	$page_info['page_links'] = array();
    	$page_info['first_link'] = ''; // 首页链接
    	$page_info['first_suspen'] = ''; // 首页省略号
    	$page_info['last_link'] = ''; // 尾页链接
    	$page_info['last_suspen'] = ''; // 尾页省略号
    
    
    	for ($i = $from; $i <= $to; $i++)
    	{
    	$page_info['page_links'][$i] = $url.$i;
    	}
    	if (($page_info['curr_page'] - $from) < ($page_info['curr_page'] -1) && $page_info['page_count'] > $num)
    	{
    		$page_info['first_link'] = $url.'1';
    		if (($page_info['curr_page'] -1) - ($page_info['curr_page'] - $from) != 1)
    		{
    				$page_info['first_suspen'] = '..';
    		}
    	}
    	if (($to - $page_info['curr_page']) < ($page_info['page_count'] - $page_info['curr_page']) && $page_info['page_count'] > $num)
    	{
    		$page_info['last_link'] = $url . $page_info['page_count'];
    		if (($page_info['page_count'] - $page_info['curr_page']) - ($to - $page_info['curr_page']) != 1)
    		{
    			$page_info['last_suspen'] = '..';
    		}
    	}
    
    	$page_info['prev_link'] = $page_info['curr_page'] > $from ? $url . ($page_info['curr_page'] - 1) : "";
        $page_info['next_link'] = $page_info['curr_page'] < $to ? $url . ($page_info['curr_page'] + 1) : "";
    }
    
    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param $value 数组值
     */
    public function add_special_char(&$value) {
    	if('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos ( $value, '`')) {
    		//不处理包含* 或者 使用了sql方法。
    	} else {
    		$value = '`'.trim($value).'`';
    	}
    	return $value;
    }
	/**
	 * 获取品牌
	 * @param unknown_type $brand_id
	 * @param unknown_type $is_all
	 * @param unknown_type $is_show
	 * @param unknown_type $is_cache
	 */
    public function getBrand($brand_id = 0, $is_show = false, $is_cache = true, $city = 'sh') {
    	if($is_show) {
    		$data = @include VAR_PATH . 'config/brand.php';
    	} else {
    		$data = @include VAR_PATH . 'config/brandAll.php';
    	}
    	
    	if(empty($data) || !$is_cache) {
    		$brandArray = $data = array();
    		if($is_show) {
    			$sql = "select `brand_id` as `id`, `brand_name_zh` as `name_zh`, `brand_name_en` as `name_en`, `city`  from `oto_brand` where `is_show` = '1' order by `sequence` asc";
    		} else {
    			$sql = "select `brand_id` as `id`, `brand_name_zh` as `name_zh`, `brand_name_en` as `name_en`, `city`  from `oto_brand`  order by `sequence` asc";
    		}
    		$brandArray = $this->_db->fetchAll($sql);
    		
    		foreach ($brandArray as $brandItem) {
    			if($brandItem['name_zh']) {
    				$data[$brandItem['city']][$brandItem['id']] = $brandItem['name_zh'];
    			} else {
    				$data[$brandItem['city']][$brandItem['id']] = $brandItem['name_en'];
    			}
    		}
			
    		if($is_show) {
    			$this->array_to_file($data, 'brand');
    		} else {
    			$this->array_to_file($data, 'brandAll');
    		}
    	}    	
    	
    	if($brand_id) {
    		return $data[$city][$brand_id];
    	}
    	    	
    	return $data[$city];
    }
    
    /**
     * 获取店铺分类
     * @return unknown
     */
    public function getStore($store_id = 0, $is_cache = true, $is_app = false, $city='sh') {
    	if($is_app) {
    		$data = @include VAR_PATH . 'config/storeApp.php';
    	} else {
    		$data = @include VAR_PATH . 'config/store.php';
    	}
    	
    	if(empty($data) || !$is_cache) {
    		$data = $storeArray = array();
    		if($is_app) {
    			$sql = "select `store_id`, `store_name`, `city` from `oto_store` where `is_app` = '1' order by `sequence` asc, `store_id` asc";
    		} else {
    			$sql = "select `store_id`, `store_name`, `city` from `oto_store` order by `sequence` asc, `store_id` asc";
    		}
	    	$storeArray = $this->_db->fetchAll($sql);
	    	foreach ($storeArray as $store) {
	    		$data[$store['city']][$store['store_id']] = $store['store_name'];
	    	}
	    	
	    	if($is_app) {
    			$this->array_to_file($data, 'storeApp');
	    	} else {
	    		$this->array_to_file($data, 'store');
	    	}
    	}
    	return !$store_id ? $data[$city] : $data[$city][$store_id];
    }
    /**
     * 获取行政区 
     */
    public function getRegion($region_id = 0, $is_cache = true, $city='sh') {
    	$data = @include VAR_PATH . 'config/region.php';
    	if(empty($data) || !$is_cache) {
    		$data = $regionArray = array();
	    	$sql = "select `region_id`, `region_name`, `city` from `oto_region` order by `sequence` asc, `region_id` asc";
	    	$regionArray = $this->_db->fetchAll($sql);
	    	foreach($regionArray as $region) {
	    		$data[$region['city']][$region['region_id']] =  $region['region_name'];
	    	}
	    	
	    	$this->array_to_file($data, 'region');
    	}
    	return !$region_id ? $data[$city] : $data[$city][$region_id];
    }
    /**
     * 根据行政区ID 获取商圈
     * @param unknown_type $region_id
     */   
    public function getCircleByRegionId($region_id = 0, $is_hot = false, $is_cache = true, $city='sh') {
    	$data = @include VAR_PATH . 'config/circleRegion.php';
    	if(empty($data) || empty($is_cache)) {
    		$arr = $data = array();
    		$arr = $this->_db->fetchAll("select * from oto_circle order by sequence asc, created desc");
    		foreach ($arr as $key => $value) {
    			$data[$value['city']][$value['region_id']][]=$value;
    		}
    		$this->array_to_file($data, 'circleRegion');
    		unset($arr);
    	}
		
    	if($region_id) {
    		$snapArray = array();
    		foreach ($data[$city][$region_id] as  $key =>$item) {
    			$snapArray[$key]['id'] = $item['circle_id'];
    			$snapArray[$key]['name'] = $item['circle_name'];
    		}
    		
    		foreach($snapArray as $item) {
    			$sortAux[] = $item['sequence'];
    		}
    		array_multisort($sortAux, SORT_ASC, $snapArray);
    		
    		return $snapArray;
    	} elseif ($region_id == 0 && $is_hot) {
    		$snapArray = array();
    		$i = 0;
    		foreach ($data[$city] as $key => $itemList) {
    			foreach($itemList as $skey => $sitem) {
	    			if($sitem['is_hot'] == 1) {
		    			$snapArray[$i]['id'] = $sitem['circle_id'];
		    			$snapArray[$i]['name'] = $sitem['circle_name'];
		    			$i++;
	    			}
    			}
    		}    		
    		return $snapArray;
    	}
    	
    	return $data[$city];   	
    }
    /**
     * 不根据行政区ID 获取商圈
     * @param unknown_type $circle_id
     * @param unknown_type $is_cache
     * @param unknown_type $city
     */
    public function getCircleByCircleId($circle_id = 0, $is_cache = true, $city='sh') {
    	$data = @include VAR_PATH . 'config/circle.php';
    	if(empty($data) || empty($is_cache)) {
    		$arr = $data = array();
    		$arr = $this->_db->fetchAll("select * from oto_circle order by sequence asc, created desc");
    		foreach ($arr as $key => $value) {
    			$data[$value['city']][$value['circle_id']] = $value['circle_name'];
    		}
    		$this->array_to_file($data, 'circle');
    		unset($arr);
    	}
    	
    	if($circle_id) {    	
    		return $data[$city][$circle_id];
    	}
    	 
    	return $data[$city];   	
    }
    /**
     * 根据行政区ID 和 商圈ID 获取 店铺列表
     * @param unknown_type $region_id 行政区ID
     * @param unknown_type $circle_id 商圈ID
     * @param unknown_type $city 城市
     * @return unknown
     */
    public function getShop($region_id, $circle_id, $city = 'sh') {
    	$key = "get_shop_rid{$region_id}_cid{$circle_id}_{$city}";
    	$data = $this->getData($key);
    	if(empty($data)) {
	    	$where = " and `shop_pid` = '0'";
	    	
	    	$sql = "select `shop_id` as `id`, `shop_name` as `name` 
	    			from `oto_shop` 
	    			where `city` = '{$city}' and `region_id` = '{$region_id}' and `circle_id` = '{$circle_id}' and `shop_status` <> '-1' {$where} 
	    			order by sequence asc, shop_id asc";
	    	$data = $this->_db->fetchAll($sql);
	    	$this->setData($key, $data);
    	}
    	return $data;
    }
    /**
     * 根据行政区ID 和 商圈ID 获取 商场列表
     * @param unknown_type $region_id
     * @param unknown_type $circle_id
     * @param unknown_type $city
     */
    public function getMarketByRidAndCid($region_id, $circle_id, $city = 'sh') {
    	$key = "get_market_rid{$region_id}_cid{$circle_id}_{$city}";
    	$data = $this->getData($key);
    	if(empty($data)) {    	
    		$sql = "select `market_id` as `id`, `market_name` as `name`
		    		from `oto_market`
		    		where `city` = '{$city}' and `region_id` = '{$region_id}' and `circle_id` = '{$circle_id}'
		    		order by sequence asc, market_id asc";
    		$data = $this->_db->fetchAll($sql);
    		$this->setData($key, $data);
    	}
    	return $data;
    }
    /**
     * 根据商铺ID获取商铺名称
     * @param unknown_type $shop_id 商品ID
     */
    public function getShopName($shop_id) {
    	return $this->_db->fetchOne("select `shop_name` from `oto_shop` where `shop_id` = '{$shop_id}' and `shop_pid` = '0' limit 1");	
    }
    /**
     * 根据套餐ID获取套餐
     * @param unknown_type $pick_id
     * @param unknown_type $is_cache
     * @param unknown_type $field
     * @return unknown
     */
    public function getPack($pack_id = 0, $is_cache = true, $field = '', $city = 'sh') {
    	$data = @include VAR_PATH . 'config/pack.php';
    	if(empty($data) || empty($is_cache)) {
    		$data = $packArray = array();
    		$sql = "select * from oto_pack order by sequence asc, pack_id asc";
    		$packArray = $this->_db->fetchAll($sql);
    		foreach($packArray as $pack) {
    			$data[$pack['city']][$pack['pack_id']] = $pack;
    		}
    		$this->array_to_file($data, 'pack');
    	}
    	
    	if($pack_id && isset($data[$city][$pack_id])) {
    		if(!$field) {
    			return $data[$city][$pack_id];
    		} else {
    			return $data[$city][$pack_id][$field];
    		}
    	}
    	
    	return $data[$city];
    }
    /**
     * 根据区域名称获取商场
     * @param unknown_type $region
     * @return unknown
     */
    public function getMarket($region_id = 0, $is_cache = true, $city='sh') {
    	$data = @include VAR_PATH . 'config/market.php';
    	if(empty($data) || empty($is_cache)) {
    		$market = array();
    		$sql = "select `market_id` as `id`, `market_name` as `name`, `region_id`, `circle_id`, `city`, `is_show` from `oto_market` order by market_id asc";
    		$marketArray = $this->_db->fetchAll($sql);
    		foreach($marketArray as $marketItem) {
    			$market[$marketItem['city']][$marketItem['region_id']][] = $marketItem;
    		}
    		$data = $market;
    		unset($market, $marketArray);
    		$this->array_to_file($data, 'market');
    	}
    	
    	if(!empty($region_id)) {
    		return $data[$city][$region_id];
    	}
    	
    	return $data[$city];
    }
    /**
     * 根据店铺ID获取某个字段值
     * @param unknown_type $shop_id
     * @param unknown_type $field
     * @return unknown
     */
    public function getShopFieldById($shop_id, $field = '*') {
    	$row = $this->select("`shop_id` = '{$shop_id}'", 'oto_shop', $field, '', true);
    	if($field != '*' && strpos($field, ',') === false) {
    		return $row[$field];
    	}
    	return $row;
    }
    
    /**
     * 根据经纬度 查询店铺名
     * @param unknown_type $post_lng
     * @param unknown_type $post_lat
     * @param unknown_type $distance
     */
    
    public function getShopNameByLngLat($post_lng, $post_lat, $distance = 0.5, $city = 'sh') {
    	$key = "get_shop_name_by_{$post_lng}_{$post_lat}_{$city}";
		$data = $this->getData($key); 
    	if (empty($data)) {
    		$squares = returnSquarePoint($post_lng, $post_lat, $distance);
    		$info_sql = "select shop_id, shop_name, region_id, circle_id, shop_address from `oto_shop`
				    		where `lat` <> 0 and `lat` > {$squares['right-bottom']['lat']}
				    		and `lat` < {$squares['left-top']['lat']}
				    		and `lng` > {$squares['left-top']['lng']}
				    		and `lng` < {$squares['right-bottom']['lng']}
				    		and `shop_status` <> '-1' and `shop_pid` = '0'
				    		and `city` = '{$city}'
				    		order by `created` desc";
    		$data = $this->_db->fetchAll($info_sql);
    		$this->setData($key, $data);
    	}
    	return $data;
    }
    
    /**
     * 根据用户名获取用户Id
     * @param unknown_type $user_name 用户名
     */
    public function getUserIdByUserName($user_name) {
    	return $this->_db->fetchOne("select `user_id` from `oto_user` where `user_name` = '{$user_name}' limit 1");
    }
    
    /**
     * 根据用户名获取用户明细
     * @param unknown_type $user_name 用户名
     */
    public function getUserInfoByUserName($user_name) {
    	return $this->select_one("`user_name` = '{$user_name}'", "oto_user");
    }
    
    /**
     * 根据UUID获取用户明细
     * @param unknown_type $user_name 用户名
     */
    public function getUserByUuid($uuid, $field = 'user_id, uuid, user_name, user_type, code') {    	
    	return $this->select("`uuid` = '{$uuid}'", 'oto_user', $field, '', true);
    }
    
    /**
     * 根据用户ID获取用户明细
     * @param unknown_type $user_name 用户名
     */
    public function getUserByUserId($user_id, $field = 'user_id, uuid, user_name, user_type') {
    	return $this->select("`user_id` = '{$user_id}'", 'oto_user', $field, '', true);
    }
    
    /**
     * 获取推荐位
     */
 	public function getPosition($where = '', $cwhere = '') {
		$data = array();
		
		if(empty($where)) {
			$where = "`pos_pid` = '0' and `city` = '{$this->_ad_city}'";
		} else {
			$where .= " and `city` = '{$this->_ad_city}'";
		}
		
		if(!empty($cwhere)) {
			$cwhere = ' and '.$cwhere;
		}		
		$rows = $this->select($where, 'oto_position', '*', 'sequence asc, pos_id asc');
		foreach ($rows as &$row){
			$data[$row['pos_id']] = $row;
			$data[$row['pos_id']]['child'] = $this->select("`pos_pid` = '{$row['pos_id']}' {$cwhere}", 'oto_position', '*', 'sequence asc, pos_id asc');
		}
		return $data;
	}
    /**
     * 获取全部/某组/某个 推荐位
     */
	public function getTheRecommendedPosition($firstLayer = null, $secondLayer = null,  $is_cache = true, $city = 'sh') {
		$data = @include VAR_PATH . 'config/position.php';
		if(empty($data) || !$is_cache) {
			$data = $positionArray = $parentArray = array();
			$sql = "select * from `oto_position` order by sequence asc, pos_id asc";
			$positionArray = $this->_db->fetchAll($sql);
			
			foreach ($positionArray as $position) {
				if($position['pos_pid'] == 0) {
					$data[$position['city']][$position['identifier']] = $position;
					$parentArray[$position['pos_id']] = $position['identifier'];
				}
			}
			
			foreach ($positionArray as $position) {
				if($position['pos_pid'] == 0) {
					continue;
				} else {
					$data[$position['city']][$parentArray[$position['pos_pid']]]['child'][$position['identifier']] = $position;
				}				
			}
						
			$this->array_to_file($data, 'position');
		}
		
		if(is_array($firstLayer) && is_null($secondLayer)) {
			$temArray = array();
			foreach ($firstLayer as $layer) {
				foreach ($data[$this->_ad_city] as $posKey => $posItem) {
					
					if($posKey == $layer) {
						$temArray[] = $posItem;
					}
					
					foreach ($posItem['child'] as $cposKey => $cposItem) {
						if($cposKey == $layer) {
							$temArray[] = $cposItem;
						}
					}
				}	
			}
			return $temArray;
		}
		
		if(!is_null($firstLayer) && !is_null($secondLayer)) {
			return $data[$city][$firstLayer]['child'][$secondLayer];
		}
		
		if(!is_null($firstLayer) && is_null($secondLayer)) {
			return $data[$city][$firstLayer]['child'];
		}
		
		return $data[$city];
	}
	/**
	 * 获取店铺经纬度列表 / 单店铺经纬度
	 * @param unknown_type $shop_id
	 */
	public function getShopLngLatAll($shop_id = 0, $is_cache = true, $city = 'sh') {
		$data = @include VAR_PATH . 'config/shopLngLatList.php';
		if(empty($data) || !$is_cache) {
			$data = $snapArray = array();
			$sql = "select shop_id, lng, lat, city from `oto_shop` where lng > 0 and lat > 0 order by shop_id asc";
			$snapArray = $this->_db->fetchAll($sql);
			
			foreach($snapArray as $snapItem) {
				$data[$snapItem['city']][$snapItem['shop_id']] = array(
							'lng' => $snapItem['lng'],
							'lat' => $snapItem['lat'],
						);
			}
			unset($snapArray);
			$this->array_to_file($data, 'shopLngLatList');
		}
		
		if($shop_id) {
			return $data[$city][$shop_id];
		}
		
		return $data[$city];
	}
    /**
     *  推荐位ID获取
     */
    public function getPosId($identifier, $city = '') {
    	if(empty($city)) {
    		$city = $this->_city;
    	}
    	return $this->_db->fetchOne("select pos_id from `oto_position` where identifier = '{$identifier}' and `city` = '{$city}' limit 1");
    }
    
    /**
     * 更新用户最新登录时间和登录IP
     */
    public function updateUser($ip, $user_id) {
    	$arr = array(
    			'lasted_login_time' => REQUEST_TIME,
    			'lasted_login_ip'   => $ip
    			);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    }
    
    /**
     * 根据用户ID 统计用户 上传并且审核通过的商品
     * @param unknown_type $user_id
     */
    public function updateQuantityThroughGoodByUserId($user_id) {
    	$through = $this->_db->fetchOne("select count(good_id) from `oto_good` where `user_id` = '{$user_id}' and `good_status` = '1' and `is_del` = '0'");
    	$arr = array(
    			'through' => $through
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $through;
    }
    /**
     * 根据用户ID 统计用户 上传的所有商品
     * @param unknown_type $user_id
     */
    public function updateQuantityTotalGoodByUserId($user_id) {
    	$good_number = $this->_db->fetchOne("select count(good_id) from `oto_good` where `user_id` = '{$user_id}' and `is_del` = '0'");
    	$arr = array(
    			'good_number' => $good_number
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $good_number;
    }
    /**
     * 根据用户ID 统计用户 收藏的所有商品
     * @param unknown_type $user_id
     */
    public function updateQuantityFavGoodByUserId($user_id) {
    	$favorite_number = $this->_db->fetchOne("select count(favorite_id) from `oto_good_favorite` where `user_id` = '{$user_id}'");
    	$arr = array(
    			'favorite_number' => $favorite_number
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $favorite_number;
    }
    /**
     * 根据用户ID 统计用户 喜欢的所有商品
     * @param unknown_type $user_id
     */
    public function updateQuantityLoveGoodByUserId($user_id) {
    	$concerned_number = $this->_db->fetchOne("select count(concerned_id) from `oto_good_concerned` where `user_id` = '{$user_id}'");
    	$arr = array(
    			'concerned_number' => $concerned_number
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $concerned_number;
    }    
    
    /**
     * 根据用户ID 统计用户收藏的 店铺数
     * @param unknown_type $user_id
     */
    public function updateQuantityFavShopByUserId($user_id) {
    	$shop_favorite_number = $this->_db->fetchOne("select count(favorite_id) from `oto_shop_favorite` where `user_id` = '{$user_id}'");
    	$arr = array(
    			'shop_number' => $shop_favorite_number
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $shop_favorite_number;
    }
    
    /**
     * 根据用户ID 统计用户收藏的 品牌数
     * @param unknown_type $user_id
     */
    public function updateQuantityFavBrandByUserId($user_id) {
    	$brand_favorite_number = $this->_db->fetchOne("select count(favorite_id) from `oto_brand_favorite` where `user_id` = '{$user_id}'");
    	$arr = array(
    			'brand_number' => $brand_favorite_number
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $brand_favorite_number;
    }   
    
    /**
     * 根据用户ID 统计用户收藏的 商场数
     * @param unknown_type $user_id
     */
    public function updateQuantityFavMarketByUserId($user_id) {
    	$market_favorite_number = $this->_db->fetchOne("select count(favorite_id) from `oto_market_favorite` where `user_id` = '{$user_id}'");
    	$arr = array(
    			'market_number' => $market_favorite_number
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    	return $market_favorite_number;
    }
    
    /**
     * 根据店铺ID 统计店铺拥有的商品数量
     * @param unknown_type $user_id
     */
    public function updateQuantityCommodityNumByShopId($shop_id) {
    	$commodity_number = $this->_db->fetchOne("select count(ticket_id) from `oto_ticket` where `shop_id` = '{$shop_id}'" . Model_Api_App::getInstance()->commodityWhereSql());
    	$arr = array(
    			'commodity_number' => $commodity_number
    	);
    	$this->_db->update('oto_shop', $arr, "shop_id = '{$shop_id}'");
    	return $commodity_number;
    }
    /**
     * 根据品牌ID 统计品牌拥有的商品数量
     * @param unknown_type $user_id
     */
    public function updateQuantityCommodityNumByBrandId($brand_id) {
    	$commodity_number = Model_Api_App::getInstance()->getCommodityNumByBrandId($brand_id);
    	$arr = array(
    			'commodity_number' => $commodity_number
    	);
    	$this->_db->update('oto_brand', $arr, "brand_id = '{$brand_id}'");
    	return $commodity_number;
    }
    
    /**
     * 更新用户领取优惠券数量
     * @param unknown_type $user_id
     */
    public function updateUserTicket($user_id) {
    	$arr = array(
    			'ticket_number' => $this->_db->fetchOne("select count(detail_id) from `oto_ticket_detail` where `user_id` = '{$user_id}'")
    	);
    	$this->_db->update('oto_user', $arr, "user_id = '{$user_id}'");
    }
    /**
     * 获取用户头像
     */
    public function getUserAvatar($user_name, $avatar = 'Avatar50') {
    	$user_name = urldecode($user_name);
    	$userInfo = Custom_AuthLogin::get_user_info($user_name);
    	//用户头像  (Avatar180|Avatar50|Avatar30)
    	if($userInfo['GetUserInfoResult'] == 1) {
    		return $userInfo['userInfo']['userField']['Avatar50'];
    	} 
    	return '';
    }
    
    /**
     * 获取用户头像
     */
    public function getUserAvatarByUuid($uuid) {
    	$userInfo = Custom_AuthLogin::get_user_by_uuid($uuid);
    	//用户头像  (Avatar180|Avatar50|Avatar30)
    	return $userInfo['userInfo']['userField'];
    }
    
   /**
    * 返回格式
    *  num : 数组总条数
    *  data : 返回数组
    */
    public function returnArr($num = 0 , $data = '', $code = 100, $message = 'success') {
    	$message = trim($message, '|');
    	$arr = array(
    			'returncode'     => $code,
    			'returnmessage'	 => $message,
    			'total'  => $num,
    			'result' => !$data ? array() : $data
    	);
    
    	return $arr;
    }

    /**
     * 百度api    
     * 通过经纬度取得实际地址  
     */   
    public function getRealyAddress($wei,$jing)  {
    	$place_url = $GLOBALS['GLOBAL_CONF']['Get_Latitude_And_Longitude'].'?output=json&location='.$wei.','.$jing.'&key=ccea36ece20a7a6eb0666bc726957e85';
    	$json_place = file_get_contents($place_url);   
    	$place_arr = json_decode($json_place,true);   
    	$address = $place_arr['result']['formatted_address'];   
    	return $address;
    }
    /**
     * 百度地图根据地址获取经纬度
     */
    public function getLatitudeAndLongitudeFromBaiDu($address, $key = 'ccea36ece20a7a6eb0666bc726957e85')  {
    	$place_url = $GLOBALS['GLOBAL_CONF']['Get_Latitude_And_Longitude'].'?output=json&address='.$address.'&key=ccea36ece20a7a6eb0666bc726957e85';
    	$json_place = file_get_contents($place_url);
    	$place_arr = json_decode($json_place,true);
    	$lngLatArray = $place_arr['result']['location']; 
    	return $lngLatArray;
    }    
    /**
     * 高德地图根据经纬度获取地址
     * @param unknown_type $lng
     * @param unknown_type $lat
     */
    public function getAddressBylnglatFormamap($lng, $lat, $key = '430494ef87722fe88a14c54f2ddbf4e4') {	
    	$weburl = $GLOBALS['GLOBAL_CONF']['Get_Address_Bylnglat_Formamap'] . '?location=' .$lng . ',' . $lat . '&key=' . $key;    	
    	$contentObject = json_decode(file_get_contents($weburl ));
    	if($contentObject->status == 1) {
    		return $contentObject->regeocode->formatted_address;
    	}
    	return false;
    }
    /**
     * 高德地图根据地址获取经纬度
     * @param unknown_type $address
     */
    public function getLatitudeAndLongitudeFormamap($address, $city = 'sh', $key = '430494ef87722fe88a14c54f2ddbf4e4') {
    	switch ($city) {
    		case 'sh':
    			$city_name = '上海市'; 
    			break;
    		case 'nj':
    			$city_name = '南京市';
    			break;
    		case 'nb':
    			$city_name = '宁波市';
    			break;
    	}
    	
    	$pargm = array(
    			'address' => $address,
    			'key' => $key,
    			'city' => $city_name ? $city_name : '上海市',
    	);    	
    	$weburl = $GLOBALS['GLOBAL_CONF']['Get_Latitude_And_Longitude_Formamap'] . '?';    	
    	$contentURL = $weburl . http_build_query($pargm);
    	$contentObject = json_decode(file_get_contents($contentURL));
    	if($contentObject->status == 1) {
    		return $contentObject->geocodes[0]->location;
    	}
    	return false;
    }
    /**
     * 验证UID是否存在  如果没有 则新增
     */ 
    public function checkUid($uuid, $user_name) {
    	$userRow = $this->getWebUserId($uuid);
    	$user_name = urldecode($user_name);
    	
    	if($userRow) {
    		if($userRow['user_name'] != $user_name) {
    			//exit(json_encode($this->returnArr(0, '', 300, '你的用户名已修改，请重新登录！')));
    		}
    	}
    	
    	return $userRow['user_id'];
    }
    
    /**
     * 获取商品图片以及图片总数
     * 
     */
    public function getGoodImg($data, $width = '640') {
    	foreach ($data as &$row) {
    		$row['good_name'] = specialHtmlConversion($row['good_name']);
	    	$img_url = $this->_db->fetchOne("select img_url from oto_good_img where good_id = '{$row['good_id']}' order by is_first desc, good_img_id asc limit 1");
	    	if (!empty($img_url)) {
	    		$row['first_img'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/good/'.$width.'/' . $img_url;
	    		$size = getimagesize(ROOT_PATH.'web/data/good/'.$width.'/' . $img_url);
	    		$row['width'] = $size['0'];
	    		$row['height'] = $size['1'];
	    	} else {
	    		$row['first_img'] = '/data/default_good.jpg';
	    		$size = getimagesize(ROOT_PATH.'web/data/default_good.jpg');
	    		$row['width'] = $size['0'];
	    		$row['height'] = $size['1'];
	    	}
	    	$row['img_num'] = $this->_db->fetchOne("select count(good_img_id) from oto_good_img where good_id = '{$row['good_id']}'");
    	}
    	return $data;
    }
    
    /**
     * 获取详情页大图
     */
    public function getDetailImage($gid) {
    	$imgInfo = $this->_db->fetchAll("select good_img_id, img_url from oto_good_img where good_id = '{$gid}' order by is_first desc, good_img_id asc");
		foreach ($imgInfo as &$row) {
			$row['img_detail_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'].'/buy/good/640/' . $row['img_url'];
			$size = getimagesize(ROOT_PATH.'web/data/good/640/' . $row['img_url']);
			$row['width'] = $size['0'];
			$row['height'] = $size['1'];
			unset($row['img_url']);
		}
		return $imgInfo;
    }
    
    /**
     * 判断手机用户是否登录
     */
    public function isLogin($uuid, $uname) {
    	if (empty($uuid) || empty($uname)) {
    		echo json_encode($this->returnArr(0, array(), 300, '请先登录！'));
    		exit();
    	}
    }
    /**
     * 判断网页端是否登录
     * @return Ambigous <boolean, string>|boolean
     */
    public function isWebLogin() {
    	Third_Des::$key = 'A06D40B7';
    	$parseUrl = parse_url($GLOBALS['GLOBAL_CONF']['MPSHOP_SITE_URL']);   	
    	if($_SERVER['HTTP_HOST'] == $parseUrl['host']) {
    		if($_COOKIE['MPSHOPUUID']) {
    			$uuid = Third_Des::decrypt($_COOKIE['MPSHOPUUID']);
    			if($uuid) {
    				return $this->getWebUserId($uuid);
    			}
    		}
    	} else {
	    	if($_COOKIE['MPUUID']) {
	    		$uuid = Third_Des::decrypt($_COOKIE['MPUUID']);
	    		if($uuid) {
	    			return $this->getWebUserId($uuid);
	    		}
	    	}
    	}
    	
    	return false;
    }
    /**
     * 同步检测用户名，同步 UUID，同步用户，返回 用户基本信息
     * @param unknown_type $uuid
     * @param unknown_type $is_cache
     * @return Ambigous <number, unknown>|Ambigous <multitype:, multitype:>|boolean
     */
    public function getWebUserId($uuid, $is_cache = FALSE) {
    	$key = 'get_website_user_info_by_id_' . $uuid;
    	$data = $this->getData($key);
    	$userRow = $this->select_one(
    			"`uuid` = '{$uuid}'", 
    			'oto_user', 
    			'user_id, uuid, user_name, user_type, user_status, star, phone_number'
    	);
    	if(empty($data) || empty($userRow) || !$is_cache) {
	    	$authUserRow = Custom_AuthLogin::get_user_by_uuid($uuid);
	    	if($authUserRow['GetUserInfosResult'] == 1) {
	    		$userRow['GroupTitle'] = $authUserRow['userInfo']['GroupTitle'];
	    		$userRow['UserSex'] = $authUserRow['userInfo']['UserSex'];
	    		$userRow['Mobile'] = $authUserRow['userInfo']['Mobile'];
	    		$userRow['RealName'] = $authUserRow['userInfo']['RealName'];
	    		$userRow['CityTitle'] = $authUserRow['userInfo']['CityTitle'];
	    		$userRow['MP'] = $authUserRow['userInfo']['MP'];
	    		$userRow['Avatar50'] = $authUserRow['userInfo']['userField']['Avatar50'];
	    		$userRow['Avatar30'] = $authUserRow['userInfo']['userField']['Avatar30'];
	    		$user_name = $authUserRow['userInfo']['UserName'];
	    		if($userRow['user_id']) {
	    			if($user_name != $userRow['user_name']) {
	    				$this->_db->update('oto_user', array('user_name' => $user_name), "`uuid` = '{$uuid}'");
	    				$userRow['user_name'] = $user_name;
	    			}
	    		} else {
	    			$sql = "insert ignore into `oto_user` (`uuid`, `user_name`, `lasted_login_time`, `lasted_login_ip`, `created`) values ('{$uuid}', '{$user_name}', '".REQUEST_TIME."', '".CLIENT_IP."', '".REQUEST_TIME."')";
	    			$this->_db->query($sql);
	    			$insert_id = $this->_db->lastInsertId();
	    			if($insert_id) {
	    				$userRow['uuid'] = $uuid;
	    				$userRow['user_id'] = $insert_id;
	    				$userRow['user_name'] = $user_name;
	    				$userRow['user_type'] = 1;
	    				$userRow['user_status'] = 0;
	    			}
	    		}
	    		$this->setData($key, $userRow, REQUEST_TIME + 3600);
	    		return $userRow;
	    	}
    	}
    	if(!empty($data)) {
    		return $data;
    	}
    	
    	return false;
    }
    /**
     * 获取导航菜单
     * @return Ambigous <multitype:, unknown>
     */
    public function getNavList() {
    	$navArray = @include VAR_PATH . 'config/nav.php';
    	return $navArray ? $navArray[$this->_city] : array();
    }
    /**
     * 添加商品关注/喜欢
     * @param unknown_type $user_name
     * @param unknown_type $good_id
     */
  	public function addConcern($user_name, $good_id, $ip = null) {
  		$ip = is_null($ip) ? CLIENT_IP : $ip;
  		$user_name = urldecode($user_name);
  		$user_id = $this->getUserIdByUserName($user_name);
  		$result = $this->_db->replace('oto_good_concerned', array('user_id' => $user_id, 'good_id' => $good_id, 'created' => REQUEST_TIME));
  		if($result) {
  			$num = $this->_db->fetchOne("select count(concerned_id) from `oto_good_concerned` where `good_id` = '{$good_id}'");
  			$this->_db->update('oto_good', array('concerned_number' => $num), "good_id = '{$good_id}'");
  			$this->updateUser($ip, $user_id);
  			return $num;
  		}
  		return false;
  	}
  	/**
  	 * 添加商品收藏
  	 * @param unknown_type $user_name
  	 * @param unknown_type $good_id
  	 */
  	public function addFavorite($user_name, $good_id, $ip = null) {
  		$ip = is_null($ip) ? CLIENT_IP : $ip;
  		$user_name = urldecode($user_name);
  		$user_id = $this->getUserIdByUserName($user_name);
  		$result = $this->_db->replace('oto_good_favorite', array('user_id' => $user_id, 'good_id' => $good_id, 'created' => REQUEST_TIME));
  		if($result) {
  			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_good_favorite` where `good_id` = '{$good_id}'");
  			$this->_db->update('oto_good', array('favorite_number' => $num), "good_id = '{$good_id}'");
  			$this->updateUser($ip, $user_id);
  			return $num;
  		}
  		return false;
  	}	
  	/**
  	 * 添加店铺收藏
  	 * @param unknown_type $user_name
  	 * @param unknown_type $good_id
  	 */
  	public function addShopFavorite($user_id, $shop_id, $ip = null) {
  		$ip = is_null($ip) ? CLIENT_IP : $ip;
  		$result = $this->_db->replace('oto_shop_favorite', array('user_id' => $user_id, 'shop_id' => $shop_id, 'created' => REQUEST_TIME));
  		if($result) {
  			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_shop_favorite` where `shop_id` = '{$shop_id}'");
  			$this->updateUser($ip, $user_id);
  			return $num;
  		}
  		return false;
  	}
  	
  	/**
  	 * 添加品牌收藏
  	 * @param unknown_type $user_name
  	 * @param unknown_type $good_id
  	 */
  	public function addBrandFavorite($user_id, $brand_id, $ip = null) {
  		$ip = is_null($ip) ? CLIENT_IP : $ip;
  		$result = $this->_db->replace('oto_brand_favorite', array('user_id' => $user_id, 'brand_id' => $brand_id, 'created' => REQUEST_TIME));
  		if($result) {
  			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_brand_favorite` where `brand_id` = '{$brand_id}'");
  			$this->updateUser($ip, $user_id);
  			return $num;
  		}
  		return false;
  	} 	
  	
  	/**
  	 * 添加商场关注/收藏
  	 * @param unknown_type $user_name
  	 * @param unknown_type $good_id
  	 */
  	public function addMarketFavorite($user_id, $market_id, $ip = null) {
  		$ip = is_null($ip) ? CLIENT_IP : $ip;
  		$result = $this->_db->replace('oto_market_favorite', array('user_id' => $user_id, 'market_id' => $market_id, 'created' => REQUEST_TIME));
  		if($result) {
  			$num = $this->_db->fetchOne("select count(favorite_id) from `oto_market_favorite` where `market_id` = '{$market_id}'");
  			$this->updateUser($ip, $user_id);
  			return $num;
  		}
  		return false;
  	}
  	
  	
  	/**
  	 * 商品点击数
  	 * @param unknown_type $good_id
  	 */
  	public function addclick($good_id) {
  		$sql = "update oto_good set clicks = clicks + 1 where good_id = '{$good_id}'";
  		$this->_db->query($sql);
  	}
  	/**
  	 * 根据分类ID和类别标记获取券分类名称 / 设置券分类缓存
  	 * @param unknown_type $detail_id
  	 * @param unknown_type $sort_unique
  	 * @return unknown
  	 */
  	public function getTicketSortById($detail_id = 0, $sort_unique = '', $sort_field = '') {
  		$data = @include VAR_PATH . 'config/sort.php';
  		if( empty($data) || ( empty($detail_id) && empty($sort_unique) ) ) {
  			$sortArray = $sortList = array();
  			$sortList = $this->select('', 'oto_sort');
  			foreach ($sortList as $sortItem) {
  				$sortArray[$sortItem['sort_unique']] = $sortItem;
  				$sortArray[$sortItem['sort_unique']]['child'] = $this->_db->fetchAssoc("select * from oto_sort_detail where `sort_id` = '{$sortItem['sort_id']}' order by sequence asc, created desc");
  			}
  			$data = $sortArray;unset($sortArray, $sortList);
  			$this->array_to_file($data, 'sort');
  		}		
  		if(!empty($detail_id) && !empty($sort_unique)) {
  			if(!empty($sort_field)) {
  				return $data[$sort_unique]['child'][$detail_id][$sort_field];
  			} else {
  				return $data[$sort_unique]['child'][$detail_id]['sort_detail_name'];
  			}
  		} elseif ($detail_id == 0 && !empty($sort_unique)) {
  			if(!empty($sort_field)) {
  				foreach($data[$sort_unique]['child'] as $sort_detail_id => $sortItem) {
  					if($sortItem['sort_detail_mark'] == $sort_field) {
  						return $sort_detail_id;
  					}
  				} 				
  			}
  			return $data[$sort_unique]['child'];
  		}
  	}
	/**
	 * 禁言
	 * @param unknown_type $client_ip
	 * @param unknown_type $info
	 */
  	public function whether_to_allow($client_ip, $info){
  		if($info['user_status'] == 1){
  			Custom_Common::showMsg(
  				'抱歉，你被禁言了，暂时不能上传商品，若有疑问请联系网站工作人员'
  			);
  		}
  		 
  		$blacklistArray = @include VAR_PATH.'config/backlist.php';
  		if($info['user_name'] && $blacklistArray['username']){
  			if(in_array($info['user_name'], $blacklistArray['username'])) {
  				Custom_Common::showMsg(
  					'抱歉，你的用户名被黑名单了，暂时不能上传商品，若有疑问请联系网站工作人员'
  				);
  			}
  		}
  		 
  		if($client_ip && $blacklistArray['ip']){
  			if(in_array($client_ip, $blacklistArray['ip'])) {
  				Custom_Common::showMsg(
  					'抱歉，你所在的IP被黑名单了，暂时不能上传商品，若有疑问请联系网站工作人员'
  				);
  			}
  		}
  		 
  	}
  	/**
  	 * 联想获取品牌
  	 * @param unknown_type $q
  	 */
  	public function assocGetBrand($q) {
  		$cacheKey = "get_brand_" . $q;
  		$dataStr = $this->getData($cacheKey);
  		if (empty($dataStr)) {
  			$sql = "select `brand_name_zh`, `brand_name_en` from `oto_brand` where `brand_name_zh` like '{$q}%' or `brand_name_en` like '{$q}%' order by brand_id asc";
  			$qArray = $this->_db->fetchAll($sql);
  			$qStr = '';
  			foreach ($qArray as $item) {
  				if(!empty($item['brand_name_zh']) && !empty($item['brand_name_en'])) {
  					$qStr .= $item['brand_name_zh'] . '[' . $item['brand_name_en'] .']' . '|'. "\r\n";
  				}elseif(!empty($item['brand_name_zh']) && empty($item['brand_name_en'])) {
  					$qStr .= $item['brand_name_zh'] . '|'. "\r\n";
  				}elseif(empty($item['brand_name_zh']) && !empty($item['brand_name_en'])) {
  					$qStr .= $item['brand_name_en'] . '|'. "\r\n";
  				}
  			}
  			$dataStr = $qStr;unset($qStr);
  			$this->setData($cacheKey, $dataStr);
  		}
  		return $dataStr;
  	}

     /**
      * 高德地图经纬度转百度地图经纬度
      * @param unknown_type $lng
      * @param unknown_type $lat
      * @return multitype:number
      */
     public function germanToBaidu($lng, $lat) {
     	define('X_PI',3.14159265358979324 * 3000.0 / 180.0);
     	$x = $lng;
     	$y = $lat;
     	$z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * X_PI);
     	$theta = atan2($y, $x) + 0.000003 * cos($x * X_PI);
     	$bd_lon = $z * cos($theta) + 0.0065;
     	$bd_lat = $z * sin($theta) + 0.006;
     	return array('lng' => $bd_lon, 'lat' => $bd_lat);
     }
	/**
	 * 百度地图经纬度转高德地图经纬度
	 * @param unknown_type $lng
	 * @param unknown_type $lat
	 * @return multitype:number
	 */
     public function baiduToGerman($lng, $lat) {
     	define('X_PI',3.14159265358979324 * 3000.0 / 180.0);
     	$x = $lng - 0.0065;
     	$y = $lat - 0.006;
     	$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * X_PI);
     	$theta = atan2($y, $x) - 0.000003 * cos($x * X_PI);
     	$gg_lon = $z * cos($theta);
     	$gg_lat = $z * sin($theta);
     	return array('lng' => $gg_lon, 'lat' => $gg_lat);
     }
     
     public function insertSql($table, $setArr){
     	$insertkeysql = $insertvaluesql = $comma = '';
     	foreach($setArr as $key=>$value){
     		$insertkeysql .= $comma . '`' . $key . '`';
     		$insertvaluesql .= $comma . '\'' . $value . '\'';
     		$comma = ', ';
     	}
     	$sql = 'insert ignore into `' . $table . '`' . '(' . $insertkeysql . ') ' . 'VALUES (' . $insertvaluesql . ')';
     	return $sql;
     }
	 
     public function Err404($module, $message, $redirect = null, $links = null) {
     	$this->_tpl->assign('message', $message);
     	if($redirect){
     		$this->_tpl->assign('redirect', $redirect);
     	}
     	if(!empty($links) && is_array($links)){
     		$this->_tpl->assign('links', $links);
     	}
     	
     	if($module == $GLOBALS['GLOBAL_CONF']['Default_Manager_Module_Path']){
     		$this->_tpl->display('_common/admin_msg.php');
     	} else {
     		$this->_tpl->display('_common/front_msg.php');
     	}
     	exit();
     }
     
     /**
      * 获取指定类型的宣传标语
      * @param unknown_type $type 类型
      */
     public function getSlogans( $type=0 , $is_cache=false){
     	$data = @include VAR_PATH . 'config/slogan.php';
     	if(empty($data) || !$is_cache) {
     		$sql = "SELECT * FROM `oto_slogan` WHERE `is_del`= 0 ORDER BY `category` ASC";
     		$res = $this->_db->fetchAll($sql);
     		$data = array();
     		foreach( $res as $row ){
     			$data[$row["category"]][$row["slogan_id"]] = $row["name"];
     		}
     	}
     	if( $type ){
     		return $data[$type];
     	}
     	return $data;
     }
     
     /**
      * 同步收藏数据
      * @param unknown_type $data
      */
     public function syncFavoriteDynamic( $data ){
     	$data["ip"] = CLIENT_IP;
     	$slogans = $this->getSlogans($data["type"]);
     	$indx = array_rand($slogans);
     	$slogan = $slogans[$indx];
     	$data["summary"] = str_replace('{name}', '{'.$data["summary"].'}' , $slogan);
     	$data["summary"] = mysql_escape_string($data["summary"]);
     	$this->_db->replace('oto_user_dynamic',$data);
     }
     
     /**
      * 同步取消收藏的数据
      * @param unknown_type $user_id 用户id
      * @param unknown_type $from_id 来源id
      * @param unknown_type $type 类型 1：商品，2：店铺，3：商场，4：品牌，5：收藏折扣，6：发布折扣，7：浏览折扣
      */
     public function removeFavoriteDynamic( $user_id , $from_id , $type ){
     	$sql = "SELECT `id` FROM `oto_user_dynamic` 
     			WHERE `user_id`='{$user_id}' AND `from_id`='{$from_id}' AND `type`='{$type}'";
     	$dynamicId = $this->_db->fetchOne($sql);
     	if( $dynamicId && $this->_db->query("DELETE FROM `oto_user_dynamic` WHERE `id`='{$dynamicId}'") ){
     		$this->_db->query("DELETE FROM `oto_user_dynamic_like` WHERE `dynamic_id`='{$dynamicId}'");
     	}
     }
     
     /**
     * 析构函数
     */
    public function __destruct(){
        // 打印调试信息
        if(isDebug()){
            if(!$this->_http->isXmlHttpRequest() && !$this->_http->isFlashRequest()){
                showRuntime();
                showDebugDetail();
            }
        }
        // 销毁数据库链接
        Core_DB::disconnect();
    }
}