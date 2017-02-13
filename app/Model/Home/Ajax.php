<?php 
class Model_Home_Ajax extends Base
{
	private static $_instance;

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	//获取特卖现金券
	public function getTmVoucherList( $getData , $city){
		$vids = trim($getData['vids']);
		$sql = "select ticket_id, ticket_uuid, ticket_title, ticket_summary,par_value,selling_price,valid_stime, valid_etime, cover_img,app_price,content 
				from `oto_ticket` 
				where `city` = '{$city}' and ". $this->db_create_in($vids, 'ticket_id') . Model_Api_App::getInstance()->couponWhereSql() . " 
				order by sequence asc, created desc";
		$res = $this->_db->fetchAll($sql);
		if( !empty($res) ){
			$res = Model_Api_App::getInstance()->formatVouncherResult($res);
		}
		return $res;
	}
}