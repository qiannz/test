<?php
class Controller_Home_Ajax extends Controller_Home_Abstract {
	private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Home_Ajax::getInstance();
    }
	
	//获取特卖券列表
	public function tmVouncherAction(){
		$getData = $this->_http->getParams();
		if( !trim($getData['vids']) ){
			_sexit('fail', 300);
		}
		$data = $this->_model->getTmVoucherList($getData,$this->_city);
		$content = "";
		if( count($data)>0 ){
			$this->_tpl->assign("voucher",$data);
			$content = $this->_tpl->fetch("voucher/1.php");
		}
		_sexit('sucess', 100, $content);
	}
	
	//获取店铺下面的商品列表
	public function shopCommodityAction(){
		$getData = $this->_http->getParams();
		if( !intval($getData['shop_id']) ){
			_sexit('fail', 300);
		}
		$getData['city'] = $this->getShopFieldById($getData['shop_id'],'city');
		$data = Model_Api_App::getInstance()->getNewCommodityMore($getData);
		$content = "";
		if( $data["current_total"] > 0 ){
			$this->_tpl->assign("commodity",$data["data"]);
			$content = $this->_tpl->fetch("commodity/{$data["current_total"]}.php");
		}
		_sexit('sucess', 100, $content);
	}
}
