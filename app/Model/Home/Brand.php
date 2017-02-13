<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-7-1
 * Time: 下午1:58
 */
class Model_Home_Brand extends Base {

    private static $_instance;

    public static function getInstance()
    {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getBrandDetail($brand_id, $city){
    	$key = 'get_brand_detail_' . $brand_id . '_' . $city;
    	$data = $this->getData($key);
    	if (empty($data)) {
    		$sql ="select * from oto_brand where brand_id = '{$brand_id}' and city = '{$city}'";
    		$data = $this->_db->fetchRow($sql);
    		$this->setData($key, $data);
    	}
    	$data['brand_logo'] = $data['brand_logo'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $data['brand_logo'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/blank.png';
    	$data['brand_icon'] = $data['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $data['brand_icon'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/wap/default_brand_icon.png';
    	$data['brand_head'] = $data['brand_head'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $data['brand_head'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/wap/img_brand_default.png';
    	$data['brand_figure'] = $data['brand_figure'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $data['brand_figure'] : $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/images/blank.png';
        return $data;
    }

    public function hadFavoriteBrand($user_id,$brand_id){
        $sql = "select 1 from oto_brand_favorite where brand_id = '{$brand_id}' and user_id = '{$user_id}'";
        return $this->_db->fetchOne($sql) == 1;
    }

    //查询出关注该品牌的数量
    public function getUserNumByBrandId($brand_id){
         return $this->_db->fetchOne("select count(favorite_id) from `oto_brand_favorite` where `brand_id` = '{$brand_id}'");
    }

    //根据品牌id查询出相关店铺
    public function getShopsByBrandId($brand_id, $limit = 3){
        $sql ="select shop_id , shop_name, shop_address from `oto_shop` where brand_id = '{$brand_id}'and shop_pid = '0' and shop_status <> '-1' order by sequence asc limit {$limit}";
        return $this->_db->fetchAll($sql);
    }

    //根据品牌id查询出该品牌下面的商量数量
    public function  getGoodNumByBrandId($brand_id){
        $sql = "select count(*) from `oto_good` where  brand_id = '{$brand_id}' ";
        return $this->_db->fetchOne($sql);
    }
    
    // 根据品牌ID查询出改品牌有那些店铺
    public function getShopInfo($bid, $city) {
    	$key = "wap_brand_shop_info_" . $bid . '_' . $city;
    	$data = $this->getData($key);
    	if (empty($data)) {
    		$sql = "SELECT shop_id, shop_name,shop_address FROM oto_shop WHERE shop_status <> -1 and shop_pid = 0 and brand_id = '{$bid}' and city = '{$city}'";
    		$data = $this->_db->fetchAll($sql);
    		$this->setData($key, $data);
    	}
    	return $data; 
    }


    public function getAjaxGoodList($brand_id, $page, $order, $pagesize = 20) {
        //缓存键值
        $cacheKey = 'get_ajax_brand_good_list_' . " {$brand_id}_{$page}_{$order}";
        $data = $this->getData($cacheKey);

        if (empty($data)) {
            $snapArray = $snapData = array();

            $where = "A.`brand_id` = '{$brand_id}' and A.`good_status` <> '-1' and A.`is_auth` <> '-1' and A.`is_del` = '0'";
            $orderby = '';

            if($order == 1) {
                $orderby = "order by `created` desc";
            } elseif ($order == 2) {
                $orderby = "order by `clicks` desc, `created` desc";
            }

            $sqlC = "select count(A.good_id) from `oto_good` A where {$where}";
            $totalNum = $this->_db->fetchOne($sqlC);

            $sql = "select
					`good_id`, `good_name`, `brand_id`,`shop_id`, `shop_name`, `dis_price`, `favorite_number`, `concerned_number`,
					(select `img_url` from `oto_good_img` where `good_id` = A.good_id order by is_first desc, good_img_id asc limit 1) as `img_url`
					from `oto_good` A
					where {$where} {$orderby}";
            $snapArray = $this->_db->limitQuery($sql, ($page - 1) * $pagesize, $pagesize);
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
            $snapData['totalPage'] = ceil($totalNum / $pagesize);
            $data = $snapData;
            unset($snapArray, $snapData);
            $this->setData($cacheKey, $data);
        }
        return $data;
    }
    
    public function getAllBrand ($city) {
    	$key = 'web_get_brand_all_' . $city;
    	$data = $this->getData($key);
    	if (empty($data)) {
    		$data = array();
    		// 1. 获取品牌分类名
    		$store = $this->getStore(0, true, false, $city);
    		// 2. 获取品牌分类下的所有品牌
    		foreach ($store as $store_id => $store_name) {
    			$all_brand = $this->_db->fetchAll("select `brand_id`, `brand_name_zh`, `brand_name_en` from `oto_brand` where `store_id` = '{$store_id}' and is_enable = 1 and city = '{$city}' order by `sequence` asc");
    			// 遍历品牌名
    			foreach ($all_brand as &$r) {
    				if($r['brand_name_zh'] && !$r['brand_name_en']) {
    					$r['brand_name'] = $r['brand_name_zh'];
    				} elseif(!$r['brand_name_zh'] && $r['brand_name_en']) {
    					$r['brand_name'] = $r['brand_name_en'];
    				} else {
    					$r['brand_name'] = $r['brand_name_zh'];
    				}
    			}
    			
    			// return 数据
    			$data[] = array(
    					'store_id' => $store_id,
    					'store_name' => $store_name,
    					'all_brand' => $all_brand
    					);
    		}
    		$this->setData($key, $data);
    	}
    	return $data;
    }
    
    public function getBrand($city) {
    	$key = 'web_get_brand_by_storeid_' . $city;
    	$data = $this->getData($key);
    	if (empty($data)) {
    		$data = array();
    		// 1. 获取品牌分类名
    		$store = $this->getStore(0, true, false, $city);
    		// 2. 获取品牌分类下的所有品牌
    		foreach ($store as $store_id => $store_name) {
    			$store_brand = $this->_db->fetchAll("select `brand_id`, `brand_icon`, `brand_name_zh`, `brand_name_en`, `brand_logo` from `oto_brand` where `store_id` = '{$store_id}' and city = '{$city}' and is_enable = 1 order by `sequence` asc limit 10");
    			// 遍历品牌名
    			foreach ($store_brand as &$r) {
    				if($r['brand_name_zh'] && !$r['brand_name_en']) {
    					$r['brand_name'] = $r['brand_name_zh'];
    				} elseif(!$r['brand_name_zh'] && $r['brand_name_en']) {
    					$r['brand_name'] = $r['brand_name_en'];
    				} else {
    					$r['brand_name'] = $r['brand_name_zh'];
    				}
    				$r['brand_logo'] = $r['brand_logo'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $r['brand_logo'] : '/images/blank.png';
    				$r['brand_icon'] = $r['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $r['brand_icon'] : '/images/blank.png';
    				// 该品牌下是否有优惠券
    				$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
    				$sql = "select count(0) from oto_ticket
		    				where brand_id = '{$r['brand_id']}' and city = '{$city}' and ticket_type = '{$ticket_type}'
		    				and ticket_status = '1' and is_auth = '1' and end_time > '" . REQUEST_TIME . "' and start_time < '" . REQUEST_TIME . "'
		    				order by created desc";
    				$r['is_ticket'] = $this->_db->fetchOne($sql);
    				
    			}
    			
    			// 取出分类下 左侧一张推荐品牌（有券）
    			$sql_r = "SELECT ob.brand_id, ob.brand_icon, om.img_url 
			    				FROM oto_recommend om 
			    				LEFT JOIN oto_brand ob 
			    				ON om.come_from_id = ob.brand_id
			    				WHERE ob.store_id = '{$store_id}' AND om.come_from_type = 3 AND 
			    				ob.city = '{$city}' AND om.city = '{$city}' 
    							ORDER BY om.sequence asc, om.created desc limit 1";
    			
    			$store_recomm_brand = $this->_db->fetchRow($sql_r);
    			$store_recomm_brand['brand_icon'] = $store_recomm_brand['brand_icon'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/brand/' . $store_recomm_brand['brand_icon'] : '/images/blank.png';
    			$store_recomm_brand['img_url'] = $store_recomm_brand['img_url'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $store_recomm_brand['img_url'] : '/images/blank.png';
    			if (!empty($store_recomm_brand['brand_id'])) {
    				$store_recomm_brand['ticket'] = $this->getCouponByBrand($store_recomm_brand['brand_id'], $city);
    			}
  
    			// return 数据 
    			$data[] = array(
    					'store_id' => $store_id,
    					'store_name' => $store_name,
    					'store_brand' => $store_brand,
    					'store_recomm_brand' => $store_recomm_brand
    			);
    			
    		}
    		$this->setData($key, $data);
    	}
    	return $data;
    }
    
    public function getCouponByBrand($bid, $city) {
    	$key = 'web_get_ticket_by_brand_' . $bid . '_' . $city;
    	$data = $this->getData($key);
    	if (empty($data)) {
    		$ticket_type = Model_Home_Ticket::getInstance()->getTicketTypeID('voucher');
    		// 获取该品牌最新的一个优惠券
    		$sql = "select ticket_id, ticket_uuid, selling_price, par_value, ticket_title, ticket_summary, valid_stime, valid_etime, cover_img, shop_id, shop_name
    			    from oto_ticket
		    		where brand_id = '{$bid}' and city = '{$city}'
		    		and ticket_type = '{$ticket_type}' and `ticket_status` = '1' and `is_auth` = '1' and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'
		    		order by created desc limit 1 ";
   
    		$data = $this->_db->fetchRow($sql);
    		
    		// 根据券ID获取适用店铺
    		$shop_id_array = array($data['shop_id']);
    		$shopIds = $this->_db->fetchCol("select shop_id from oto_ticket_shop where ticket_id = '{$data['ticket_id']}'");
    		if ($shopIds) {
    			$shop_id_array = array_merge($shop_id_array, $shopIds);
    		}
    		    		
    		$s_sql = "select shop_id, shop_name from oto_shop where " . $this->db_create_in($shop_id_array, 'shop_id') . " and shop_status <> -1 and shop_pid = 0 and city = '{$city}'  limit 3";
    		$data['used_shop'] = $this->_db->fetchAll($s_sql);
    		
    		$data['selling_price'] = floor($data['selling_price']);
    		$data['par_value'] = floor($data['par_value']);
    		$data['cover_img'] = $data['cover_img'] ? $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/cover/' . $data['cover_img'] : '/images/blank.png';
    		$data['ticket_title'] = Custom_String::cutString($data['ticket_title'], 18);
    		$this->setData($key, $data);
    	}
    	return $data;
    }
}