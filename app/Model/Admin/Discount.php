<?php
class Model_Admin_Discount extends base{
private static $_instance;
	protected $_table = 'discount_content';
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
		return $this->_db->fetchOne("select count(discount_id) from `".$this->_table."` where 1".$this->_where);
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
								$where .= " and `discount_status` = '0'";
							} elseif($value == 2) {
								$where .= " and `discount_status` = '1'";
							} elseif($value == 3) {
								$where .= " and `discount_status` = '-1'";
							}
							break;
						case 'uname':
							if($value) {
								$where .= " and `user_name` = '{$value}'";
							}
							break;
						case 'title':
							if($value) {
								$where .= " and `title` like '%".trim($value)."%'";
							}
							break;
						case 'isd':
							if($value) {
								$isDel = true;
								$where .= " and `is_del` = '1'";
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
    
    public function getConsultationList($getData, $page, $pagesize = PAGESIZE) {
    	$data = array();
    	$where = '';
    	$isDel = false;
    	if(!empty($getData)) {
			foreach($getData as $key => $value) {
				if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
					switch ($key) {
						case 'question':
							if($value) {
								$where .= " and `question` like '%{$value}%'";
							}
							break;
						case 'author':
							if($value) {
								$user_id = $this->getUserIdByUserName($value);
								$where .= " and `user_id` = '{$user_id}'";
							}
							break;
						case 'isd':
							if($value) {
								$isDel = true;
								$where .= " and `is_del` = '1'";
							}
							break;
					}
				}
			}
		}
		if (!$isDel) {
			$where .= " and `is_del` = '0'";
		}
		//折扣ID
		$from_id = intval($getData['did']);
		$sql = "select count(tid) from `discount_message_thread` where `from_id` = '{$from_id}' {$where}";
		$data['totalNum'] = $this->_db->fetchOne($sql);
		//折扣列表
		$start = ( $page - 1 ) * $pagesize;
		$sql = "select * from `discount_message_thread` where `from_id` = '{$from_id}' {$where} order by `updated` desc, `reply_time` desc, `created` desc"; 
		$data['data'] = $this->_db->limitQuery($sql, $start, $pagesize);
		return $data ? $data : array();
    }
    
    public function postDiscount( $postData ){
    	$param = array();
    	$param["title"] = Custom_String::HtmlReplace( $postData["title"], -1 );
    	$param["stime"] = $postData['stime'] ? strtotime($postData['stime']) : 0;
    	$param["etime"] = $postData['etime'] ? strtotime($postData['etime']) : 0;
    	$param["address"] = trim( $postData["discount_address"] );
    	$param["type_id"] = intval( $postData["type_id"] );
    	$param["category_id"] = intval( $postData["category_id"] );
    	$param["discount_start"] = intval( $postData["discount_start"] );
    	$param["discount_end"] = intval( $postData["discount_end"] );
    	$param["promotion"] = Custom_String::HtmlReplace( $postData["promotion"], -1);
    	$param["region_id"] = intval( $postData["region_id"] );
    	$param["circle_id"] = intval( $postData["circle_id"] );
    	$param["market_id"] = intval( $postData["market_id"] );
    	$param["content"] = Custom_String::cleanHtml( $postData["content"] );
    	$param["wap_content"] = Custom_String::HtmlReplace( $postData["wap_content"] , 3);
    	
    	$param["star"] = intval( $postData["star"] );
    	$param["linker"] = trim( $postData["linker"] );
    	$param["telephone"] = trim( $postData["telephone"] );
    	
    	$city = !$postData['city'] ? $this->_ad_city : $postData['city'];
    	$param['city'] = $city;
    	
    	$bids = trim( $postData["bids"] , "," );
    	
    	$param["ip"] = CLIENT_IP;
    	$lng = $lat = 0;
    	$lngLatString = $this->getLatitudeAndLongitudeFormamap($param["address"], $city);
    	if($lngLatString) {
    		list($lng, $lat) = explode(',', $lngLatString);
    	}
    	$param["lng"] = $lng;
    	$param["lat"] = $lat;
    	
    	$did = intval( $postData["did"] );
    	$user_id = $this->getUserIdByUserName(DEFINED_USER_NAME);
    	//新增
    	if(!$did) {
    		$param = array_merge($param, array(
	    				'user_id' => $user_id,
	    				'user_name' => DEFINED_USER_NAME,
    					'updated' => REQUEST_TIME,
	    				'created' => REQUEST_TIME
    				));
    		$insert_id = $this->_db->insert($this->_table, $param);
    		if( $insert_id > 0 ){
    			$param = array(
    					'charter_user_id'=>$user_id,
    					'charter_member'=>DEFINED_USER_NAME,
    					'charter_member_avator'=>$this->getUserAvatar(DEFINED_USER_NAME)
    			);
    			
    			//内容图片处理
    			$this->contentPictureAddEdit($param["content"], $insert_id, 'add');
    			
    			Model_Api_Message::getInstance()->addPreNotice('discount', 'discount_view', $insert_id , $param);
    		}
    	}
    	//编辑
    	else {
    		$insert_id = $did;
    		$param = array_merge($param, array('updated' => REQUEST_TIME));
    		if ( $this->_db->update($this->_table, $param, array('discount_id' => $did)) ){
    			$this->updateVisit(array(
    					"stime"=>$param["stime"],
    					"etime"=>$param["etime"],
    					"type_id"=>$param["type_id"],
    					"category_id"=>$param["category_id"],
    					"discount_start"=>$param["discount_start"],
    					"discount_end"=>$param["discount_end"],
    					"region_id"=>$param["region_id"],
    					"circle_id"=>$param["circle_id"],
    					"market_id"=>$param["market_id"]
    			), $did);
    		}
    		
    		//内容图片处理
    		$this->contentPictureAddEdit($param["content"], $insert_id, 'edit');
    	}
    	if( $insert_id ){
    		//同步动态
    		$this->syncFavoriteDynamic( array('user_id' => $user_id, 'from_id' => $insert_id, 'summary' => $param["title"],'type'=>6, 'favorite_id'=>$insert_id,'created' => REQUEST_TIME) );
    	}
		
    	if($bids) {
    		$sql = '';
    		$bidArr = explode(',', $bids);
    		foreach($bidArr as $brand_id) {
    			$sql .= "('{$insert_id}', '{$brand_id}'),";
    		}
    		 
    		if($sql) {
    			$sql = 'replace into discount_brand (`discount_id`, `brand_id`) values ' . substr($sql, 0, -1);
    			$this->_db->query($sql);
    		}
    	}
    	
    	return array('insert_id' => $insert_id);
    	
    }
    
    //修改wap图片的序号
    public function img_ajax_edit($getData){
    	$column = $getData['column'];
    	$id = $getData['id'];
    	$value = $getData['value'];
    	$result = $this->_db->update('discount_wap_img',array($column => $value), "`id` = $id");
    	if($result){
    		exit(json_encode(true));
    	}
    }
	
    //根据行政区id获取商圈列表
    public function getCirclesByRid($region_id) {
    	$where = " and `shop_pid` = '0' and `city` = '{$this->_ad_city}'";
    	$circleArray = $this->getCircleByRegionId($region_id, false, true, $this->_ad_city);
    	return $circleArray ? $circleArray : array();
    }
    
    //获取品牌列表
    public function getBrandList( $filter , $city ){
    	$where = " WHERE `city` = '{$city}'";
    	if( $filter ){
    		$where .= " AND ( `brand_name_zh` LIKE '%{$filter}%' OR `brand_name_en` LIKE '%{$filter}%' )";
    	}
    	$sql = " SELECT `brand_id`,`brand_name_zh`,`brand_name_en` FROM `oto_brand` {$where} ";
    	$data = $this->_db->fetchAll($sql);
    	
    	return $data ? $data : array();
    }
    
    //更新wap图片到数据库
    public function wapImgUpload($wap_img_url, $discount_id){
    	$sql = '';
    	//默认上传者
    	$user_id = $this->getUserIdByUserName(DEFINED_USER_NAME);
    	if( !empty($wap_img_url) ){
    		foreach ( $wap_img_url as $img_url ){
    			$sql .= "('{$discount_id}','{$user_id}','{$img_url}','".REQUEST_TIME."'),";
    		}
    		$sql = substr($sql, 0 , -1);
    		if( $sql ){
    			$sql = "INSERT INTO `discount_wap_img`(`discount_id`,`user_id`,`img_url`,`created`) VALUES  ".$sql;
    			$this->_db->query( $sql );
    		}
    	}
    	
    	return 0;
    }
    
    //根据discount_id获取折扣信息
    public function getDiscountRow( $discount_id ){
    	$discountRow = $this->select("`discount_id` = '{$discount_id}'", $this->_table, '*', '', true);
    	return $discountRow ? $discountRow : array();
    }
    
    //根据discount_id获取wap图片列表
    public function getWapImgList( $discount_id ){
    	$wapImgList = $this->select("`discount_id` = '{$discount_id}'", 'discount_wap_img', '*', 'sequence asc, created asc');
    	return $wapImgList;
    }
    
    //根据discount_id获取品牌列表
    public function getBrandListById( $discount_id ){
    	$sql = 'SELECT o.brand_id , o.brand_name_zh , o.brand_name_en FROM discount_brand AS b LEFT JOIN oto_brand AS o ON o.brand_id = b.brand_id WHERE b.discount_id = '.$discount_id;
    	return $this->_db->fetchAll($sql);
    }
    
    //删除折扣
    public function del($dids) {
    	$sql = "update `{$this->_table}` set `is_del` = '1' where `discount_id` in ({$dids})";
    	$this->_db->query($sql);
    	$this->updateVisit(array('is_del'=>1), $dids);
    	$this->updateUserConcernedStatus(0, $dids);
    	return true;
    }
    
    //修改visit表中discount 的信息
    public function updateVisit( $setArr , $dids ){
    	$setSql = $comma = '';
    	foreach($setArr as $key=>$value){
    		$setSql .= $comma . '`' . $key . '`' . '=\'' . $value . '\'';
    		$comma = ', ';
    	}
    	$sql = 'UPDATE `discount_visit`' . ' SET ' . $setSql . ' WHERE `discount_id` IN ('.$dids.')';
    	$this->_db->query($sql);
    }
    
    //修改用户关注折扣表的状态(折扣删除，审核状态发生改变时，折扣关注表状态改变，用户关注数量改变)
    public function updateUserConcernedStatus( $status , $dids ){
    	$sql = "UPDATE `discount_concerned` SET `status`='{$status}' WHERE `discount_id` IN ('$dids')";
    	$this->_db->query($sql);
    	
    	$sql = "SELECT `user_id` FROM `discount_visit` WHERE `discount_id` IN ({$dids}) GROUP BY `user_id`";
    	$uidArr = $this->_db->fetchCol($sql);
    	foreach( $uidArr as $user_id ){
    		Model_Api_Discount::getInstance()->updateUserConcernNumber($user_id);
    	}
    }
    
    //删除折扣咨询
    public function delConsultation($tids, $discount_id) {
    	$sql = "update `discount_message_thread` set `is_del` = '1' where `tid` in ({$tids}) and `from_id` = '{$discount_id}'";
    	return $this->_db->query($sql);
    }
    
    //删除折扣咨询
    public function unDelConsultation($tids, $discount_id) {
    	$sql = "update `discount_message_thread` set `is_del` = '0' where `tid` in ({$tids}) and `from_id` = '{$discount_id}'";
    	return $this->_db->query($sql);
    }
    //取消删除折扣
    public function unDel($dids) {
		$sql = "update `{$this->_table}` set `is_del` = '0' where `discount_id` in ({$dids})";
		$flag = $this->_db->query($sql);
		if( $flag ){
			$this->updateVisit(array('is_del'=>0), $dids);
			$this->updateUserConcernedStatus(1, $dids);
		}
		return $flag;    
    }
    
    //折扣审核
    public function audit($data) {
    	$did = $data['did'];
    	$audit_type = intval($data['audit_type']);
    	$reason1 = trim($data['reason1']);
    	$reason2 = trim($data['reason2']);
    	if($audit_type == 2) {
    		$discount_status = -1;
    		switch ($reason1) {
    			case 1:
    				$reason = '虚假信息';
    				break;
    			case 2:
    				$raeson = '恶意广告';
    				break;
    			case 3:
    				$reason = '敏感内容';
    				break;
    			case 4:
    				$reason = $reason2;
    				break;
    		}
    	} elseif($audit_type == 1) {
    		$discount_status = 1;
    		$reason = '审核通过';
    	}
    	$affected_rows = $this->_db->update($this->_table, array('discount_status' => $discount_status, 'reason' => $reason), "`discount_id` = '{$did}'");
    	if(is_numeric($affected_rows)) {
    		$this->updateVisit(array('discount_status'=>$discount_status), $did);
    		$status = ($discount_status==1?1:0);
    		$this->updateUserConcernedStatus($status, $did);
    		return true;
    	}
    	return false;
    }
    
    public function checkRecommend($come_from_id, $pos_id) {
    	return $this->_db->fetchOne("select 1 from `oto_recommend` where `come_from_id` = '{$come_from_id}' and `come_from_type` = '5' and `pos_id` = '{$pos_id}' limit 1") == 1;
    }  

    public function recommend($getData) {
    	$arr = array(
    			'come_from_id' => $getData['did'],
    			'come_from_type' => 5,
    			'title' => $getData['title'],
    			'summary' => $getData['summary'],
    			'pos_id' => $getData['pos_id'],
    			'www_url' => $GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/home/discount/wap-show/did/' . $getData['did'],
    			'img_url' => $getData['img_url'],
    			'created' => REQUEST_TIME,
    			'pmark' => 'discount',
    			'cmark' => 'discount_view',
    			'city' => $getData['city'] ? $getData['city'] : $this->_ad_city
    	);
    	return $this->_db->insert('oto_recommend', $arr);
    }
    
    public function getConsultationPost($tid) {
    	$sql = "select * from `discount_message_post` where `tid` = '{$tid}' and `is_del` = '0' order by pid asc";
    	$data = $this->_db->fetchAll($sql);
    	return $data ? $data : array();
    }
    
    public function delConsultationPost($pid) {
    	$sql = "update `discount_message_post` set `is_del` = '1' where `pid` = '{$pid}' limit 1";
    	return $this->_db->query($sql);
    }

    public function getGroupChat($discount_id, $page, $type = "discount", $pagesize = PAGESIZE) {
    	$sql = "select * from `oto_group_chat` where `type` = '{$type}' and `did` = '{$discount_id}' and `is_del`='0'  order by `created` desc";
    	$data = $this->_db->fetchAll($sql);
    	return $data ? $data : array();
    }
    
    public function delGroupChatPost( $gc_id ){
    	$sql = "update `oto_group_chat` set `is_del` = '1' where `id` = '{$gc_id}' limit 1";
    	return $this->_db->query($sql);
    }
    
    public function unDelGroupChatPost( $gc_id ){
    	$sql = "update `oto_group_chat` set `is_del` = '0' where `id` = '{$gc_id}' limit 1";
    	return $this->_db->query($sql);
    }
    /**
     * 内容图片处理
     * @param unknown_type $content
     * @param unknown_type $discount_id
     * @param unknown_type $action
     */
    public function contentPictureAddEdit($content, $discount_id, $action = 'add') {
    	$matches = array();
    	preg_match_all( "/<img.*?src=[\\\'| \\\"](http:\/\/.*\/api\/good\/get\-special\-img\-thumb\/iid\/[1-9][0-9]*\/type\/discount\/w\/740)[\\\'|\\\"].*?[\/]?>/i", stripslashes($content), $matches);
    	$img_attachment = array();
    	if(!empty($matches[1]))
    	{
    		foreach ($matches[1] as $img_url){
    			$img_attachment[] = str_replace(
    					array(
    							$GLOBALS['GLOBAL_CONF']['SITE_URL'] . '/api/good/get-special-img-thumb/iid/',
    							'/type/discount/w/740'
    					), '', $img_url);
    		}
    		$img_attachment_ids = '';
    		$img_attachment = array_unique($img_attachment);
    		$img_attachment_ids = implode(',', $img_attachment);
    		if(!empty($img_attachment_ids)) {
    			//编辑先断开图片和券的关联
    			if($action == 'edit') {
    				$this->_db->update('discount_img', array('discount_id' => 0), array('discount_id' => $discount_id), 0);
    			}
    			//接着把图片和券关联上
    			$sql = "select * from `discount_img` where `id` in ({$img_attachment_ids})";
    			$imgArr = $this->_db->fetchAll($sql);
    			foreach($imgArr as & $imgRow) {
    				if($imgRow['discount_id'] == 0) {
    					$this->_db->update('discount_img', array('discount_id' => $discount_id), array('id' => $imgRow['id']));
    				} elseif ($imgRow['discount_id'] == $discount_id) {
    						
    				} else {
    					if(!$this->checkTicketImg($discount_id, $imgRow['user_id'], $imgRow['img_url'])) {
    						$param = array(
    								'discount_id'  => $discount_id,
    								'user_id'  	 => $imgRow['user_id'],
    								'img_url'  	 => $imgRow['img_url'],
    								'created' 	 => REQUEST_TIME
    						);
    						$sql = $this->insertSql('discount_img', $param);
    						$this->_db->query($sql);
    					}
    				}
    			}
    		}
    	}
    }

    /**
     * 检查图片唯一性
     * @param unknown_type $discount_id
     * @param unknown_type $user_id
     * @param unknown_type $img_url
     */
    public function checkTicketImg($discount_id, $user_id, $img_url) {
    	$sql = "select 1 from `discount_img`
		    	where `discount_id` = '{$discount_id}'
		    	and `user_id` = '{$user_id}'
		    	and `img_url` = '{$img_url}'
		    	limit 1";
    	return $this->_db->fetchOne($sql) == 1;
    }
}