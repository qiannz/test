<?php
class Controller_Api_Task extends Controller_Api_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Task::getInstance();
	}
	
	public function indexAction(){
		set_time_limit(0);//执行时间设为0
		$getData = $this->_http->getParams();
		if(!$this->_http->has("key") || $this->_http->get("key") != "teNCGmkxZph6yvbLrW5Bo3T7Kgs8MRDH") {
			_sexit('fail', 500, 'Linux验证失败');
		}
		//通知调用日志
		logLog(date('Ymd'). '.log', var_export($getData, true),  LOG_PATH . 'task/' . date('Y') . '/' .date('m') . '/');
		
		$hour = datex(REQUEST_TIME,"H");
		//处理折扣关注记录
		if( $hour == "23" ){//23点处理折扣关注用户记录
			$count = $this->_model->handleDiscountVisit();
			logLog(date('Ymd'). '.log', "handleDiscountVisit:处理了{$count}条记录" ,  LOG_PATH . 'task/action/' . date('Y') . '/' .date('m') . '/');
		}
		
		//更新店铺的游惠状态  整点0到1分执行
		$date = datex(REQUEST_TIME,"Y-m-d H");
		$startTime = strtotime( $date.":00:00" );
		$endTime = strtotime( $date.":01:00" );
		if( REQUEST_TIME > $startTime && REQUEST_TIME <= $endTime ){
			$this->_model->handleShopSelfpayStatus();
			logLog(date('Ymd'). '.log', "handleShopSelfpayStatus:处理完了" ,  LOG_PATH . 'task/action/' . date('Y') . '/' .date('m') . '/');
		}
		
		_sexit('success', 100, '处理结束');
	}
}