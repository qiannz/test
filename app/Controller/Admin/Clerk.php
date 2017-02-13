<?php
class Controller_Admin_Clerk extends Controller_Admin_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Clerk::getInstance();
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
        $clerkList = $this->_model->getClerkList($page);
        $statistics = $this->_model->getStatistics(); //统计本周人数，金额，次数、 或筛选

        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }
        $this->_format_page($page_info);

        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('clerkList', $clerkList);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('request', stripslashes_deep($_REQUEST));
        $this->_tpl->assign('statistics', $statistics);

        $this->_tpl->display('admin/clerk_list.php');
    }
}

