<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 下午1:46
 */

class Controller_Admin_Scratchset extends Controller_Admin_Abstract {

    private $_model;
    private $scratchset;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Scratchset::getInstance();
        $this->scratchset = '街友刮奖设置';
    }

    public function listAction() {
        $data = $this->_model->getList();
        foreach ($data as $key => &$row) {
            $row['config_value'] = unserialize($row['config_value']);
            $Arr[$key] =$row['config_value']['award'];
            $sum += $row['config_value']['award']*$row['config_value']['amount'];
        }
        array_multisort($Arr, SORT_DESC, $data);

        $this->_tpl->assign('sum', $sum);
        $this->_tpl->assign('data', $data);
        $this->_tpl->display('admin/scratchset_list.php');
    }


    public function addAction() {
        $pro    = $this->_http->get('pro');
        $award  = $this->_http->get('award');
        $amount = $this->_http->get('amount');
        $ex     = $this->_http->get('ex');

        $result = $this->_model->add($pro, $award, $amount, $ex);
        if($result) {
            $appInfo = array();
            $this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
            Custom_Log::log($this->_userInfo['id'], "新增刮奖设置  <b>{$ex}</b> 成功", $this->pmodule, $this->scratchset, 'add');
            _exit('添加成功', 1);
        }
    }

    public function delAction() {
        $id = $this->_http->get('id');
        $ex = $this->_http->get('ex');
        $result = $this->_model->del($id);
        if($result) {
            $appInfo = array();
            $this->array_to_file(Model_Admin_App::getInstance()->unserializeConfig(), 'config'); //缓存
            Custom_Log::log($this->_userInfo['id'], "删除刮奖设置 <b>{$ex}</b> 成功", $this->pmodule, $this->scratchset, 'del');
            _exit('删除成功', 1);
        }
    }
}