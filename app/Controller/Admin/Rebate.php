<?php
class Controller_Admin_Rebate extends Controller_Admin_Abstract {
    private $_model;

	//初始化
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Rebate::getInstance();
	}

	public function listAction() {
        $page = $this->_http->getParam('page');
        $page = !$page ? 1 : intval($page);
        $page_str = '';
        $getData = $this->_http->getParams();
        foreach($getData as $key => $value) {
            if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
                $page_str .= "{$key}:{$value}/";
            }
        }
        $this->_model->setWhere($getData);
        $page_info = $this->_get_page($page);
        $RebateList = $this->_model->getRebateList($page);
        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }

        $this->_format_page($page_info);
        foreach($RebateList as &$val){
              $val['shop_name'] = $this->_db->fetchOne("select shop_name from oto_shop where shop_id = {$val['shop_id']}");
        }

        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('rebateList', $RebateList);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('request', stripslashes_deep($_REQUEST));

		$this->_tpl->display('admin/rebate_list.php');
	}

    public function detailAction() {
        $page = $this->_http->getParam('page');
        $page = !$page ? 1 : intval($page);
        $page_str = '';
        $getData = $this->_http->getParams();

        foreach($getData as $key => $value) {
            if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
                $page_str .= "{$key}:{$value}/";
            }
            if($key =='user_id' || $key == 'id'){
                $parameterKey = $key;
                $parameterVal = $value;
            }
        }

        $this->_model->setWhere($getData);
        $detail = $this->_model->getDetailList($parameterKey,$parameterVal,$page);
        $page_info['item_count'] = $this->_model->getDetailCount($parameterKey,$parameterVal);
        if($page_str){
            $page_info['page_str'] = $page_str;
        }
        $this->_format_page($page_info);

        foreach($detail as &$v){
            $v['ticket_title'] = $this->_db->fetchOne("select ticket_title from oto_ticket where ticket_id = '{$v['ticket_id']}'");
        }

        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('detail',$detail);

        $this->_tpl->display('admin/detail.php');
    }
}