<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 下午4:11
 */

class Controller_Admin_Withdrawal extends Controller_Admin_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Withdrawal::getInstance();
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
        $withdrawalList = $this->_model->getWithdrawalList($page);
        $statistics = $this->_model->getStatistics(); //统计本周人数，金额，次数、 或筛选

        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }
        $this->_format_page($page_info);

        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('withdrawalList', $withdrawalList);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('request', stripslashes_deep($_REQUEST));
        $this->_tpl->assign('statistics', $statistics);

        $this->_tpl->display('admin/withdrawal_list.php');
    }


    public function changeStatusAction(){
        $getData = $this->_http->getPost();
        $getData['admin_id'] = $this->_userInfo['id'];
        $result = $this->_model->ChangeStatus($getData);
        if($result){
            _exit('操作成功', 1);
        }else
            _exit('操作失败', 2);
    }
}

