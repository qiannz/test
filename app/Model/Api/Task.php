<?php
class Model_Api_Task extends Base
{
	private static $_instance;

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 删除无效的折扣查看记录
	 */
	public function handleDiscountVisit(){
		//处理删除，未审核，已过期的折扣查看记录
		$sql = "SELECT * FROM `discount_visit` WHERE `is_del` = 1
				UNION
				SELECT * FROM `discount_visit` WHERE `discount_status` = -1
				UNION
				SELECT * FROM `discount_visit` WHERE `etime`<'". REQUEST_TIME ."'
				LIMIT 100";
		$list = $this->_db->fetchAll($sql);
		$num = count($list);
		if( !$num ){
			return 0;
		}
		
		$insert_backup_sql = "";
		$ids = array();
		$insert_concerned_sql = "";
		foreach( $list as $row ){
			$insert_backup_sql .=" ('".implode("','", array_values($row) )."'),";
			$insert_concerned_sql .= " ('{$row["user_id"]}','{$row["discount_id"]}','{$row["created"]}','{$row["created"]}'),";
			$ids[] = $row["id"];
		}
	
		$insert_concerned_sql = trim($insert_concerned_sql, ",");
		$insert_concerned_sql = "INSERT INTO `discount_concerned`(`user_id`,`discount_id`,`created`,`updated`)
								VALUES ".$insert_concerned_sql."
								ON DUPLICATE KEY UPDATE `updated`=VALUES(`updated`)";
		if( $this->_db->query($insert_concerned_sql) ){
			$ids = array_unique($ids);
			$del_sql = "DELETE FROM `discount_visit` WHERE `id` IN ('".implode("','", $ids)."')";
			if( $this->_db->query($del_sql) ){
				$insert_backup_sql = trim($insert_backup_sql,",");
				$insert_backup_sql = "INSERT INTO `discount_visit_backup`(`".implode("`,`", array_keys($row))."`) VALUES ".$insert_backup_sql;
				$this->_db->query($insert_backup_sql);
			}
		}
		$this->_db->query("alter table `discount_visit` ENGINE='InnoDB';");
		$this->_db->query("analyze table `discount_visit`;");
		return $num;
	}
	
	/**
	 * 更新店铺的游惠状态
	 */
	public function handleShopSelfpayStatus(){
		$sql = "SELECT * FROM `oto_shop`";
		$shopRes = $this->_db->fetchAll($sql);
		$hasSelfpayShopIds = array();//有游惠的店铺id
		$notHasSelfpayShopIds = array();//没有游惠的店铺id
		$hasSelfpayMarketIds = array();//有游惠的商场id
		$hasSelfpayBrandIds = array();//有游惠的品牌id
		foreach( $shopRes as $shopRow ){
			$selfpay = Model_Api_App::getInstance()->getShopSelfPay($shopRow['shop_id'],$shopRow['city']);
			if( empty($selfpay) ){
				$notHasSelfpayShopIds[] = $shopRow['shop_id'];
			}else{
				$hasSelfpayShopIds[] = $shopRow['shop_id'];
				if( intval($shopRow['market_id']) ){
					$hasSelfpayMarketIds[] = $shopRow['market_id'];
				}
				if( intval($shopRow['brand_id']) ){
					$hasSelfpayBrandIds[] = $shopRow['brand_id'];
				}
			}
		}
		if( !empty($notHasSelfpayShopIds) ){
			$updateSql = "UPDATE `oto_shop` SET `has_selfpay` = 0 WHERE `shop_id` IN(".implode(",", $notHasSelfpayShopIds).")";
			$this->_db->query( $updateSql );
		}
		if( !empty($hasSelfpayShopIds) ){
			$updateSql = "UPDATE `oto_shop` SET `has_selfpay` = 1 WHERE `shop_id` IN(".implode(",", $hasSelfpayShopIds).")";
			$this->_db->query( $updateSql );
		}
		$hasSelfpayMarketIds = array_unique($hasSelfpayMarketIds);
		//默认商场中的游惠状态都置为0
		$updateSql = "UPDATE `oto_market` SET `has_selfpay` = 0";
		$this->_db->query( $updateSql );
		//将有游惠的商场has_selpay字段设为1
		if( !empty($hasSelfpayMarketIds) ){
			$updateSql = "UPDATE `oto_market` SET `has_selfpay` = 1 WHERE `market_id` IN(".implode(",", $hasSelfpayMarketIds).")";
			$this->_db->query( $updateSql );
		}
		//默认品牌中的是否有游惠状态都置为0
		$updateSql = "UPDATE `oto_brand` SET `has_selfpay` = 0";
		$this->_db->query( $updateSql );
		//将有游惠的品牌has_selpay字段设为1
		if( !empty($hasSelfpayBrandIds) ){
			$updateSql = "UPDATE `oto_brand` SET `has_selfpay` = 1 WHERE `brand_id` IN(".implode(",", $hasSelfpayBrandIds).")";
			$this->_db->query( $updateSql );
		}
	}
}