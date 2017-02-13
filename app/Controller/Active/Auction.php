<?php
/**
 * 拍卖会
 */
class Controller_Active_Auction extends Base {
	
	public function indexAction() {
		$output = array();
		$msg = $this->_http->get('msg');
		Third_Des::$key = '34npzntC';
		$http_build_query_string = Third_Des::decrypt($msg);
		parse_str($http_build_query_string, $output);
		var_dump($output);
	}
}