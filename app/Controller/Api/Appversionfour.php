<?php
class Controller_Api_Appversionfour extends Base {

	private $_model;

	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_App::getInstance();
		$this->_city = !$this->_http->get('city') ? $this->_city : strval($this->_http->get('city'));
	}
	
	/**
	 * 加密验证
	 * @param unknown_type $getData
	 */
	private function auth($getData) {
		$encryptString = 'time=' . $getData['time'] . '&key=' . $GLOBALS['GLOBAL_CONF']['My_User_Auth_Key'];
		Third_Des::$key = '34npzntC';
		if (!$getData['ssid'] || $getData['ssid'] != urldecode(Third_Des::encrypt($encryptString))) {
			exit(json_encode($this->returnArr(0, array(), 300, '验证失败！')));
		}
	}
	
	/**
	 * 首页
	 */
	public function homeAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		
		$data = $this->_model->getHomeList($getData, $this->_city);
		exit(json_encode($this->returnArr(1, $data)));
	}
	/**
	 * 为您推荐
	 */
	public function getRecommendMoreAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$data = $this->_model->getRecommendMore('app_home_recommended_for_you', $page, $this->_city);
		
		exit(json_encode($this->returnArr(1, $data)));
	}
	/**
	 * 获取99邮推荐列表
	 */
	public function getTuanRecommendAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$data = $this->_model->getTuanRecommend('buygood_hot', $page, $this->_city);
		exit(json_encode($this->returnArr(1, $data)));
	}
	/**
	 * 获取99邮分类统计
	 */
	public function getTuanSortAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		
		$data = $this->_model->getStoreNum($this->_city);
		exit(json_encode($this->returnArr(1, $data)));
	}
	/**
	 * 获取99邮分类列表
	 */
	public function getTuanListAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		
		$page = !$getData['page'] ? 1 : intval($getData['page']);
		$storeid = !$getData['storeid'] ? 0 : intval($getData['storeid']);
		
		$data = $this->_model->getTuanByStoreId($storeid, $page, $this->_city);
                foreach ($data['data'] as & $row) {
                    $ticketSurplus = Custom_AuthTicket::get_ticket_details_by_guid($row['ticket_uuid']);
                    if(is_object($ticketSurplus)) {
                            $row['surplus'] = $ticketSurplus->data->Avtivities[0]->ProductStock; // 剩余
                            $row['total'] = $ticketSurplus->data->Avtivities[0]->ProductNum; // 总数
                            $row['has_led'] = $ticketSurplus->data->Avtivities[0]->ProductDisplaySale; // 售出
                    } else {
                            $row['surplus'] = $row['total'] = $row['has_led'] = 0;
                    }
                    if($row['start_time'] > REQUEST_TIME) {
                            $row['good_status'] = '0'; //未开始
                    } else {
                            if($row['surplus'] > 0) {
                                    $row['good_status'] = '1'; //进行中
                            } else {
                                    $row['good_status'] = '-1'; //已卖完
                            }
                    }
                }
                
		exit(json_encode($this->returnArr(1, $data)));	
	}
	/**
	 * 获取99邮新品推荐
	 */
	public function getTuanNewAction() {
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->auth($getData);
		
		$data = $this->_model->getTuanByStoreId(0, 1, $this->_city, true, 100);
		exit(json_encode($this->returnArr(1, $data)));		
	}
	
	/**
	 * 99邮情页
	 */
	public function getTuanShowAction() {
		$getData = $this->_http->getParams();
		//验证
		$this->auth($getData);
		if (!$getData['tid']) {
			exit(json_encode($this->returnArr(0, array(), 300, '请选择99邮商品')));
		}
	
		$tuanShow = array();
		//团购明细
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($getData['tid']);
		$tuanShow['detail'] = Model_Home_Ticket::getInstance()->getTicktRow($ticketRow['ticket_id']);
		$ticketSurplus = Custom_AuthTicket::get_ticket_details_by_guid($tuanShow['detail']['ticket_uuid']);
		if(is_object($ticketSurplus)) {
			$tuanShow['detail']['surplus'] = $ticketSurplus->data->Avtivities[0]->ProductStock; // 剩余
			$tuanShow['detail']['total'] = $ticketSurplus->data->Avtivities[0]->ProductNum; // 总数
			$tuanShow['detail']['has_led'] = $ticketSurplus->data->Avtivities[0]->ProductDisplaySale; // 售出
		} else {
			$tuanShow['detail']['surplus'] = $tuanShow['detail']['total'] = $tuanShow['detail']['has_led'] = 0;
		}
		if($tuanShow['detail']['app_price'] < 0) {
			$tuanShow['detail']['selling_price'] = 0;
		} elseif($tuanShow['detail']['app_price'] > 0) {
			$tuanShow['detail']['selling_price'] = $tuanShow['detail']['app_price'];
		}
		//折扣
		$tuanShow['detail']['discount'] = round(($tuanShow['detail']['selling_price'] / $tuanShow['detail']['par_value']) * 10, 1); 
		//商品状态
		if($tuanShow['detail']['start_time'] > REQUEST_TIME) {
			$tuanShow['detail']['good_status'] = '0'; //未开始
		} else {
			if($tuanShow['detail']['surplus'] > 0) {
				$tuanShow['detail']['good_status'] = '1'; //进行中
			} else {
				$tuanShow['detail']['good_status'] = '-1'; //已卖完
			}
		}
		//团购消费提示
		$configArray = @include VAR_PATH . 'config/config.php';
		$tuanShow['detail']['tips'] = $configArray['CONSUMER_TIPS'] ? $configArray['CONSUMER_TIPS'] : '';
		//推荐
		$tuanShow['recommend'] = Model_Home_Ticket::getInstance()->getTuanRecommend('buygood_hot', $this->_city, 20);
		//团购关联店铺
		$tuanShow['associated_shops'] = Model_Home_Ticket::getInstance()->getAssociatedShops($getData['tid'], Model_Home_Good::getInstance()->getShop($tuanShow['detail']['shop_id']));
	
		exit(json_encode($this->returnArr(1, $tuanShow)));
	}	
	/**
	 * 获取券状态
	 */
	public function getCouponStatusAction() {
		$getData = $this->_http->getParams();
		//验证
		$this->auth($getData);
		
		$ticketRow = Model_Home_Ticket::getInstance()->getTicketRowByTicketUuid($getData['tid']);
		
		$ticketSurplus = Custom_AuthTicket::get_ticket_details_by_guid($getData['tid']);
		if(is_object($ticketSurplus)) {
			$surplus = $ticketSurplus->data->Avtivities[0]->ProductStock; // 剩余
			$total = $ticketSurplus->data->Avtivities[0]->ProductNum; // 总数
			$has_led = $ticketSurplus->data->Avtivities[0]->ProductDisplaySale; // 售出
		} else {
			$surplus = $total = $has_led = 0;
		}
		
		//商品状态
		if($ticketRow['start_time'] > REQUEST_TIME) {
			$good_status = '0'; //未开始
		} else {
			if($surplus > 0) {
				$good_status = '1'; //进行中
			} else {
				$good_status = '-1'; //已卖完
			}
		}
		
		exit(json_encode($this->returnArr(1, array('good_status' => $good_status))));
	}
}