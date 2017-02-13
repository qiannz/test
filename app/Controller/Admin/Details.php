<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 上午10:14
 */

class Controller_Admin_Details extends Controller_Admin_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Details::getInstance();
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

        $page_info = $this->_get_page($page);
        $this->_model->setWhere($getData);
        $detailsList = $this->_model->getDetailsList($page);

        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }
        $this->_format_page($page_info);

        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('detailsList', $detailsList);
        $this->_tpl->assign('page', $page);

        $this->_tpl->display('admin/details.php');
    }
}

