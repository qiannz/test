<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-15
 * Time: 下午3:39
 */

class Controller_Admin_Dubaitour extends Controller_Admin_Abstract {
    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Dubaitour::getInstance();
    }

    public function listAction() {
        $Dubaitour_List = $this->_model->getdubaitourList();

        $this->_tpl->assign('dubaitour_list', $Dubaitour_List);
        $this->_tpl->display('admin/dubaitour_list.php');
    }
}

