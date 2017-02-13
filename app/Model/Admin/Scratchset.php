<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-4-16
 * Time: 下午2:25
 */

class Model_Admin_Scratchset extends Base {
    private static $_instance;

    public static function getInstance() {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function add($pro, $award, $amount, $ex) {
        $config_value_arr = array(
            'pro'    => $pro,
            'award'  => $award,
            'amount' => $amount,
            'ex'     => $ex
        );
        $config_value = serialize($config_value_arr);
        $inArr = array (
            'config_key'   => 'CLIENT_EFFECTIVE',
            'config_value' => $config_value,
            'config_ex'    => '刮奖设置-街友',
            'created'      => REQUEST_TIME,
            'updated'      => REQUEST_TIME
        );
        return $this->_db->insert('oto_config', $inArr);
    }

    public function getList() {
        return $this->_db->fetchAll("select * from oto_config where config_key = 'CLIENT_EFFECTIVE' order by created desc");
    }

    public function del($id) {
        return $this->_db->delete('oto_config', '`config_id` = ' . $id);
    }
}