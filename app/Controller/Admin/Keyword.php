<?php
/**
 * Created by PhpStorm.
 * User: fedde
 * Date: 14-5-29
 * Time: 上午9:18
 */

class Controller_Admin_Keyword extends Controller_Admin_Abstract {

    private $_model;

    public function __construct() {
        parent::__construct();
        $this->_model = Model_Admin_Keyword::getInstance();
    }

    public function listAction() {
        $page = $this->_http->get('page');
        $page = !$page ? 1 : intval($page);
        $page_str = '';
        $getData = $this->_http->getParams();

        foreach($getData as $key => $value) {
            if(!in_array($key, array('m', 'c', 'act', 'con', 'page'))) {
                $page_str .= "{$key}:{$value}/";
            }
        }
        $this->_model->setWhere($getData);
        $this->_model->setOrder($getData);
        $page_info = $this->_get_page($page);

        $data = $this->_model->getKeyWordList($page);
        $page_info['item_count'] = $this->_model->getCount();
        if($page_str){
            $page_info['page_str'] = $page_str;
        }

        $this->_format_page($page_info);
        $this->_tpl->assign('page_info', $page_info);
        $this->_tpl->assign('page', $page);
        $this->_tpl->assign('data', $data);
        $this->_tpl->assign('request', stripslashes_deep($_REQUEST));
        $this->_tpl->display('admin/keyword_list.php');
    }

    public function addAction(){
        if($this->_http->isPost()){
            $postData = $this->_http->getPost();
            $result = $this->_model->upDateKeyWord($postData);
            if($result){
                Custom_Log::log($this->_userInfo['id'], "添加关键词  <b>{$postData['keyword']}</b> 成功", $this->pmodule, $this->cmodule, 'add');
                Custom_Common::showMsg(
                    '关键词添加成功',
                    'back',
                    array('add' => '继续添加关键词', 'list' => '返回关键词列表')
                );
            } else {
                Custom_Common::showMsg(
                    '关键词添加失败',
                    'back'
                );
            }
        }
        $this->_tpl->display('admin/keyword_add.php');
    }

    public function editAction(){
        $keyword_id  = $this->_http->get('id');
        $KeyWordRows = $this->_model->getKeyWordById($keyword_id);
        if($this->_http->isPost()){
            $postData = $this->_http->getPost();
            $result = $this->_model->upDateKeyWord($postData);
            if($result){
                Custom_Log::log($this->_userInfo['id'], "编辑关键词  <b>{$postData['keyword']}</b> 成功", $this->pmodule, $this->cmodule, 'mod');
                Custom_Common::showMsg(
                    '关键词编辑成功',
                    'back',
                    array('list' => '返回关键词列表','edit/id:'.$postData['keyword_id'] => '重新编辑该套餐')
                );
            } else {
                Custom_Common::showMsg(
                    '关键词编辑失败',
                    'back'
                );
            }
        }
        $this->_tpl->assign('keyword',$KeyWordRows);
        $this->_tpl->display('admin/keyword_modi.php');
    }

    public function delAction(){
        $keyword_id = $this->_http->get('id');
        if(!$keyword_id){
            Custom_Common::showMsg("请您选择要删除的关键词 ", 'back');
        }
        $keyword_name = $this->_db->fetchOne("select keyword_name from oto_search_keyword where keyword_id = '{$keyword_id}'");
        $resultDel = $this->_model->del($keyword_id);
        if($resultDel){
            Custom_Log::log($this->_userInfo['id'], "删除  <b>{$keyword_name}</b> 成功", $this->pmodule, $this->cmodule, 'del');
            Custom_Common::showMsg("删除关键词成功。 ", 'back',array('list' => '返回关键词列表'));
        }
    }

    public function ajaxColAction(){
        $resultBack = $this->_model->ajax_module_edit($this->_http->getPost());
        if($resultBack) {
            exit(json_encode(true));
        }
        exit(json_encode(false));
    }

    public function recommendAction() {
        $id = intval($this->_http->get('id'));
        $page = intval($this->_http->get('page'));
        $recommendResult = $this->_model->recommend($id);
        if($recommendResult) {
            Custom_Common::showMsg(
                '设置成功',
                'back',
                array('list/page:' . $page => '返回搜索管理列表')
            );
        }
    }
}