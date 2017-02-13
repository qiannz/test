<?php
class Model_Admin_Brand extends Base
{
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getCount() {
	    return $this->_db->fetchOne("select count(*) from oto_brand where 1=1". $this->_where.$this->_order);
	}
	
	public function getBrandList($page, $pagesize = PAGESIZE) {
	    $start = ($page - 1) * $pagesize;
	    $sql = "select * from oto_brand where 1=1" . $this->_where . $this->_order;
        $brands = $this->_db->limitQuery($sql, $start, $pagesize);
        foreach ($brands as &$row) {
        	$row['store_name'] = $row['store_id'] ? $this->getStore($row['store_id'], true, false, $this->_ad_city) : '';
        }
        return $brands?$brands:array();  
	}
	
    public function setWhere($getData) {
         $where = " and `city` = '{$this->_ad_city}'";
 		
         if($getData['store_id']){
         	$where .= "  and store_id = '{$getData['store_id']}'";
         }
         
         if($getData['brand_name']){
 			$where .= "  and (brand_name_zh like '%{$getData['brand_name']}%' or brand_name_en like '%{$getData['brand_name']}%')";
 		 }
 		 
 		 if($getData['is_show']){
 		 	$where .= "  and is_show = '{$getData['is_show']}'";
 		 }

        if($getData['is_enable']){
            $where .= "  and is_enable = '{$getData['is_enable']}'";
        }

         $this->_where = $where;
    }
    
    public function setOrder($getData) {
    	$order = '';
    		   
    	if($getData['store_id'] || $getData['is_show']){
    		$order .= " order by sequence asc, created desc ";
    	}else{
    		$order .= " order by created desc ";
    	}
    
    	$this->_order = $order;
    }
	
    public function brandEdit($postData) {
        $brand_id = isset($postData['brand_id'])?intval($postData['brand_id']):0;
        $brand_name_zh = isset($postData['brand_name_zh'])?trim($postData['brand_name_zh']):'';
        $brand_name_en = isset($postData['brand_name_en'])?trim($postData['brand_name_en']):'';
        $firs_word = $postData['firs_word'];
        $brand_profile = Custom_String::HtmlReplace($postData['brand_profile']);
        $logoPicture   = trim($postData['logoImg']);
        $figurePicture = trim($postData['mapImg']);
        $headPicture = trim($postData['m_headImg']);
        $iconPicture = trim($postData['iconImg']);
        $is_show = $postData['is_show'];
        $is_enable = $postData['is_enable'];
        $store_id = intval($postData['store_id']);
		
        $arr = array(
        	'store_id'	     => $store_id,
            'brand_name_zh'  => $brand_name_zh,
            'brand_name_en'  => $brand_name_en,
            'brand_profile'  => $brand_profile,
            'brand_logo'     => $logoPicture,
            'brand_figure'   => $figurePicture,
        	'brand_head'     => $headPicture,
        	'brand_icon'     => $iconPicture,
        	'is_show'        => $is_show,
            'is_enable'      => $is_enable,
            'firs_word'      => $firs_word,
        );
        if ($brand_id == 0) { // 新增
            $arr['created'] = REQUEST_TIME;
            $arr['city'] = $this->_ad_city;
			$insert_id = $this->_db->insert('oto_brand', $arr);
			return $insert_id?$insert_id:false; 
        } else { // 编辑
            $arr['updated'] = REQUEST_TIME; 
			$affected_rows = $this->_db->update('oto_brand', $arr,"`brand_id` = $brand_id");
			return $affected_rows?$affected_rows:false;   
        }
    }
    
    public function check_brand_name($name, $brand_id, $type) {
        if ($type == 'zh') {
            $conditions = "`brand_name_zh` = '{$name}' and `city` = '{$this->_ad_city}'";
            $brand_id && $conditions .= " AND `brand_id` <> $brand_id";
            return $this->_db->fetchOne("select count(*) from oto_brand where $conditions");
        } else {
            $conditions = "`brand_name_en` = '{$name}' and `city` = '{$this->_ad_city}'";
            $brand_id && $conditions .= " AND `brand_id` <> $brand_id";
            return $this->_db->fetchOne("select count(*) from oto_brand where $conditions");
        }
    }
    
    public function del($brand_id) {
		$delResult = $this->_db->delete('oto_brand', "`brand_id` = $brand_id");				
		return $delResult;     
    }
    
    public function ajax_module_edit($getData){
    	$column = $getData['column'];
    	$id = $getData['id'];
    	$value = $getData['value'];
    
    	return $this->_db->update('oto_brand',array($column => $value), "`brand_id` = $id");
    }


    public function getPositionForPicture(){
        $position = $positionArray = $data = array();        
        $params = array('brand_logo', 'brand_figure', 'brand_head', 'brand_icon');
        $position = $this->getTheRecommendedPosition($params, null, true, $this->_ad_city);
        
        foreach($position as $positionItem) {
        	$positionArray[$positionItem['identifier']] = $positionItem;
        }
        $data['logo']  = $positionArray['brand_logo'];
        $data['map']   = $positionArray['brand_figure'];
        $data['head']  = $positionArray['brand_head'];
        $data['icon']  = $positionArray['brand_icon'];
        
        return $data;
    }

    public function recommend($getData) {
    	$arr = array(
    			'come_from_id' => $getData['id'],
    			'come_from_type' => 3,
    			'title' => saddslashes($getData['title']),
    			'summary' => saddslashes($getData['summary']),
    			'pos_id' => $getData['pos_id'],
    			'www_url' => '/home/brand/show/bid/' . $getData['id'],
    			'img_url' => $getData['img_url'],
    			'created' => REQUEST_TIME,
    			'updated' => REQUEST_TIME,
    			'pmark' => 'brand',
    			'cmark' => 'brand_view',
    			'city'	=> $this->_ad_city
    	);
    	return $this->_db->insert('oto_recommend', $arr);
    }
    
    public function getBrandRow($id, $city) {
    	$row = $this->select("`brand_id` = '{$id}' and `city` = '{$city}'", 'oto_brand', '*', '', true);
    	if($row['brand_name_zh'] && !$row['brand_name_en']) {
    		$row['brand_name'] = $row['brand_name_zh'];
    	} elseif(!$row['brand_name_zh'] && $row['brand_name_en']) {
    		$row['brand_name'] = $row['brand_name_en'];
    	} else {
    		$row['brand_name'] = $row['brand_name_zh'];
    	}
    	
    	return $row;
    }

    public function checkRecommend($come_from_id, $pos_id) {
    	return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '3' and `pos_id` = '{$pos_id}' limit 1") == 1;
    }
    
    public function getColorSizeListByBrandId($brand_id) {
    	return $this->select("`brand_id` = '{$brand_id}'", "oto_brand_color_size");
    }
    
    public function addEditColorSize($getData) {
    	$param = array(
    				'brand_id' => intval($getData['bid']),
    				'type' => intval($getData['type']),
    				'name' => Custom_String::HtmlReplace($getData['name']),
    				'number' => Custom_String::HtmlReplace($getData['number']),
    				'created' => REQUEST_TIME
    			);
    	
    	return $this->_db->replace('oto_brand_color_size', $param);
    }
    
    public function getBrandRowByName($name, $city) {
    	$sql = "select * from oto_brand where ( brand_name_zh = '{$name}' or brand_name_en = '{$name}' ) and city = '{$city}'";
    	$row = $this->_db->fetchRow($sql);
    	return $row ? $row : false;
    }
}