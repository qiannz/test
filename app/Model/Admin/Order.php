<?php
class Model_Admin_Order extends Base
{
	private static $_instance;
	protected $_table = '';	
	protected $_where;
	protected $_order;
		
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getList($getData) {
		$snapArray = $data = array();
		$pagesize = $getData['pagesize'];
		$page = $getData['page'];
		
		$data = array();
				
		$orderData = array(
					//'IsTuan' => 1,
					'TypeLabel' => 'Commodity',
				);
		//判断刷选时间
		if($getData['startDate'] && $getData['overDate'] && $getData['overDate'] >= $getData['startDate']) {
			$orderData = array_merge(array(
						'OrderStartTime' => $getData['startDate'] . ' 00:00:00',
						'OrderEndTime' => $getData['overDate'] . ' 23:59:59'
					), $orderData);
			
		}
		//订单状态
		if($getData['orderStatus']) {
			switch ($getData['orderStatus']) {
				case 1://已取消
					$orderData = array_merge(array('OrderStatus' => 0), $orderData);
					break;
				case 2://等待付款
					$orderData = array_merge(array('OrderStatus' => 1), $orderData);
					break;
				case 3://完成支付（已付款）
					$orderData = array_merge(array('OrderStatus' => 2), $orderData);
					break;
				case 4://申请退款
					$orderData = array_merge(array('OrderStatus' => 4), $orderData);
					break;
			}
		}
		//提货方式
		if($getData['receiveType']) {
			switch ($getData['receiveType']) {
				case 1://快递送货
					$orderData = array_merge(array('ReceiptStatus' => 1), $orderData);
					break;
				case 2://到店自提
					$orderData = array_merge(array('ReceiptStatus' => 0), $orderData);
					break;
			}
			
		}
		//订单号
		if($getData['orderNum']) {
			$orderData = array_merge(array('OrderNo' => $getData['orderNum']), $orderData);
		}
		//商品名称
		if($getData['productName']) {
			$orderData = array_merge(array('ProductName' => $getData['productName']), $orderData);
		}
		//手机号码
		if($getData['mobile']) {
			$orderData = array_merge(array('Mobile' => $getData['mobile']), $orderData);
		}		
		//店铺名称
		if($getData['shopName']) {
			$shop_name = Custom_String::HtmlReplace($getData['shopName'], 1);
			$sql = "select shop_id from `oto_shop` 
					where `shop_name` = '{$shop_name}' 
					and `city` = '{$this->_ad_city}' 
					and `shop_pid` = '0' 
					and `shop_status` <> '-1' 
					limit 1";
			$shop_id = $this->_db->fetchOne($sql);
			if($shop_id) {
				$orderData = array_merge(array('MerchantCommonId' => $shop_id), $orderData);
			}
		}
		
		$dataResult = Custom_AuthSku::getOrderList($orderData, $page, $pagesize);
		
		if($dataResult['code'] == 1) {
			$data = $dataResult['message'];
			$snapArray['totalNum'] = $data['Paging']['RecCount'];
			
			foreach($data['Result'] as & $orderItem) {
				$orderItem['Order']['OrderTime'] = datex(strtotime($orderItem['Order']['OrderTime']), 'Y-m-d H:i:s');
				$orderItem['Order']['DeliverInfo'] = json_decode($orderItem['Order']['DeliverInfo'], true);
			}
			
			$snapArray['data'] = $data['Result'];
			return $snapArray;	
		}	
	}
}