<?php
class Controller_Api_Tianshan extends Controller_Api_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Api_Tianshan::getInstance();
	}
	
	//天山店首页
	public function homeAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		$pagesize = intval($getData['pagesize'])?$getData['pagesize']:PAGESIZE;
		$data = $this->_model->getHome( $this->_city , $pagesize);
		exit(json_encode($this->returnArr(1, $data)));
	}
	
	//天山店首页店铺更多
	public function shopMoreAction(){
		$getData = $this->_http->getParams();
		//传输加密验证
		$this->authAll($getData);
		$data = $this->_model->getShopMore($getData, $this->_city);
		exit(json_encode($this->returnArr(1,$data)));
	}
	
	//店铺(品牌)下面的商品列表
	public function shopGoodsAction(){
		
	}
	
	//商品详情
	public function goodDetailAction(){
		
	}
	
	//图片上传
	public function imgUploadAction(){
		
	}
	
}
?>