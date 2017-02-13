<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-17
 * Time: 下午4:20
 */

class Controller_Admin_Loans extends Controller_Admin_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Loans::getInstance();
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
        $loansList =  $this->_model->getLoansList($page);

        foreach($loansList as  &$row){
            $row['admin_name'] = $this->_model->getAdminUser($row['admin_id']);
        }

        $statistics = $this->_model->getStatistics();


        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }
        $this->_format_page($page_info);

        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('request', stripslashes_deep($_REQUEST));
        $this->_tpl->assign('loanslist', $loansList);
        $this->_tpl->assign('statistics', $statistics);

        $this->_tpl->display('admin/loans_list.php');
    }

}

