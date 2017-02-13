<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-5-29
 * Time: 上午9:21
 */
class Model_Admin_Keyword extends Base
{
    private static $_instance;
    private $_where;
    private $_order;

    public static function getInstance()
    {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();
        $this->_where = '';
        $this->_order = '';
    }

    public function setWhere($getData) {
        $where = " and `city` = '{$this->_ad_city}'";
        if($getData['keyword']){
            $keyword = trim($getData['keyword']);
            $where .= "  and keyword_name like '%{$keyword}%'";
        }
        if($getData['is_hot']){
            $where .= "  and is_hot = '{$getData['is_hot']}'";
        }
        $this->_where = $where;
    }

    public function setOrder($getData) {
        if($getData['is_hot'] == 1) {
            $order = ' order by `sequence` asc,  `keyword_id` asc';
        } else {
            $order = ' order by `keyword_id` asc';
        }
        $this->_order = $order;
    }

    public function getCount() {
        return $this->_db->fetchOne("select count(keyword_id) from oto_search_keyword where 1=1".$this->_where);
    }

    public function getKeyWordList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        $sql = "select * from oto_search_keyword where 1=1 ".$this->_where.$this->_order;
        $data = $this->_db->limitQuery($sql, $start, $pagesize);
        return $data ? $data : array();
    }

    public function ajax_module_edit($getData){
        $column = $getData['column'];
        $id = $getData['id'];
        $value = $getData['value'];

        $result = $this->_db->update('oto_search_keyword',array($column => $value), "`keyword_id` = $id");
        if($result){
            return true;
        }
        return false;
    }

    public function recommend($id) {
        $sql = "update oto_search_keyword set is_hot = (case is_hot when 1 then 0 else 1 end) where `keyword_id` = '{$id}' ";
        return $this->_db->query($sql);
    }

    public function getKeyWordById($id){
        return $this->_db->fetchRow("select * from oto_search_keyword where `keyword_id` = '{$id}' limit 1");
    }

    public function upDateKeyWord($postData){
        $arr  = array(
                'keyword_name'     => trim($postData['keyword']),
                'keyword_searches' => trim($postData['keyword_searches']),
                'keyword_type'     => trim($postData['keyword_type'])
        );
        if(!$postData['keyword_id']){
        	$arr = array_merge($arr, array('city' => $this->_ad_city));
            $insert_id = $this->_db->replace('oto_search_keyword' , $arr);
            return $insert_id?$insert_id:false;
        }else{
            $where = array('keyword_id' =>$postData['keyword_id']);
            $affected_rows = $this->_db->update('oto_search_keyword' , $arr , $where);
            return $affected_rows?$affected_rows:false;
        }

    }

    public function  del($id){
        $delResult = $this->_db->delete('oto_search_keyword', "`keyword_id` = $id");
        return $delResult;
    }
}