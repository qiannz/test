<?php
class Controller_Admin_Sort extends Controller_Admin_Abstract {
	
	private $_model;
	private $_page;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Admin_Sort::getInstance();
		$this->_page = !$this->_http->get('page') ? 1 : intval($this->_http->get('page'));
	}
	
	public function listAction(){
		$getData = $this->_http->getParams();
		$this->_model->setWhere($getData);
		$this->_model->setOrder($getData);
		$page_info = $this->_get_page($this->_page);
		$sorts = $this->_model->getSortList($this->_page);
		$page_info['item_count'] = $this->_model->getCount();
		$this->_format_page($page_info);
	
		$categories = $this->_model->getCategory();
		$this->_tpl->assign('sorts', $sorts);
		$this->_tpl->assign('page_info', $page_info);
		$this->_tpl->assign('categories', $categories);
		$this->_tpl->assign('tid', $getData['tid']);
		$this->_tpl->assign('page', $this->_page);
		$this->_tpl->display('admin/sort_list.php');
	}
	
	// 添加分类
	public function sortAddAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(empty($postData['sort_id']) || empty($postData['sort_detail_name'])){
				Custom_Common::showMsg(
				'类别名称或者分类名称不能为空',
				'back',
				array('sort-add/sortid:'.$postData['sort_id'] => '继续添加分类', 'list' => '返回分类管理')
				);
			}
			$insert_result = $this->_model->sortOperate($postData);
			if($insert_result == 'repeat'){
				Custom_Common::showMsg(
				'分类名称重复',
				'back',
				array('sort-add/sortid:'.$postData['sort_id'] => '继续添加分类', 'list' => '返回分类管理')
				);
			}
			if($insert_result){
				$this->getTicketSortById();
				Custom_Common::showMsg(
				'分类添加成功',
				'back',
				array('sort-add/sortid:'.$postData['sort_id'] => '继续添加分类', 'list' => '返回分类管理')
				);
			}else{
				Custom_Common::showMsg(
				'分类添加失败',
				'back'
				);
			}
		}
		if($this->_http->get('sortid')){
			$this->_tpl->assign('sort', array('sort_id' => $this->_http->get('sortid')));
		}
		$categories = $this->_model->getCategory();
		$this->_tpl->assign('categories', $categories);
		$this->_tpl->display('admin/sort_add.php');
	}
	
	// 编辑分类
	public function sortEditAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(empty($postData['sort_id']) || empty($postData['sort_detail_name'])){
				Custom_Common::showMsg(
				'类别名称或者分类名称不能为空',
				'back',
				array("sort-edit/id:{$postData['id']}/page:{$postData['page']}" => '继续编辑分类', "list/page:{$postData['page']}" => '返回分类管理')
				);
			}
			$insert_result = $this->_model->sortOperate($postData);
			if($insert_result == 'repeat'){
				Custom_Common::showMsg(
				'分类名称重复',
				'back',
				array("sort-edit/id:{$postData['id']}/page:{$postData['page']}" => '继续编辑分类', "list/page:{$postData['page']}" => '返回分类管理')
				);
			}
			if($insert_result){
				$this->getTicketSortById();
				Custom_Common::showMsg(
				'分类编辑成功',
				'back',
				array("sort-edit/id:{$postData['id']}/page:{$postData['page']}" => '继续编辑分类', "list/page:{$postData['page']}" => '返回分类管理'));
			}else{
				Custom_Common::showMsg(
				'分类编辑失败',
				'back'
				);
			}
		}
		$id = $this->_http->get('id');
		$sort = $this->select("sort_detail_id = '{$id}'", 'oto_sort_detail');
		$this->_tpl->assign('sort', array_shift($sort));
		//获取类别
		$categories = $this->_model->getCategory();
		$this->_tpl->assign('categories', $categories);
		$this->_tpl->assign('page', $this->_page);
		$this->_tpl->display('admin/sort_add.php');
	}
	
	// 删除分类
	public function sortDelAction()
	{
		$id = $this->_http->get('id');
	
		if (!$id) {
			Custom_Common::showMsg("请您选择要删除的分类 ", 'back');
		}
	
		$ids = explode(',', $id);
		foreach ($ids as $key=>$id){
			if($id){
				$resultBack = $this->_model->del_sort($id);
			}
		}
		$resultBack = $this->_model->del_sort($id);
		if($resultBack){
			$this->getTicketSortById();
			Custom_Common::showMsg("删除分类成功。 ", 'back',array('list/page:'.$this->_page => '返回分类管理'));
		}
	}
	
	// 删除类别
	public function categoryDelAction()
	{
		$id = $this->_http->get('id');
	
		if (!$id) {
			Custom_Common::showMsg("请您选择要删除的类别 ", 'back');
		}
	
		$ids = explode(',', $id);
		foreach ($ids as $key=>$id){
			if($id){
				$resultBack = $this->_model->del_category($id);
			}
		}
		$resultBack = $this->_model->del_category($id);
		if($resultBack){
			$this->getTicketSortById();
			Custom_Common::showMsg("删除类别成功。 ", 'back',array('category-list' => '返回类别管理'));
		}
	}
	
	public function checkSortAction()
	{
		$getData = $this->_http->getPost();
		$sort_detail_name = empty($getData['sort_detail_name'])?'':trim($getData['sort_detail_name']);
		$sort_id = $getData['sort_id'];
		$id = isset($getData['id']) ? intval($getData['id']):0;
		if (!$sort_detail_name)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->unique($sort_detail_name, $sort_id, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}

	public function checkMarkAction()
	{
		$getData = $this->_http->getPost();
		$sort_detail_mark = empty($getData['sort_detail_mark'])?'':trim($getData['sort_detail_mark']);
		$id = isset($getData['id']) ? intval($getData['id']):0;
		if (!$sort_detail_mark)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->unique_mark($sort_detail_mark, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}
	
	public function checkCategoryAction()
	{
		$getData = $this->_http->getPost();
		$sort_name = empty($getData['sort_name'])?'':trim($getData['sort_name']);
		$id = isset($getData['id']) ? intval($getData['id']):0;
		if (!$sort_name)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->unique_category($sort_name, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}
	
	public function checkUniqueAction()
	{
		$getData = $this->_http->getPost();
		$sort_unique = empty($getData['sort_unique'])?'':trim($getData['sort_unique']);
		$id = isset($getData['id']) ? intval($getData['id']):0;
		if (!$sort_unique)
		{
			echo json_encode(true);
			exit;
		}
		if ($this->_model->unique_unique($sort_unique, $id))
		{
			echo json_encode(true);
		}
		else
		{
			echo json_encode(false);
		}
		exit;
	}
	
	public function categoryListAction(){
		$categories = $this->_model->getCategory();
		$this->_tpl->assign('categories', $categories);
		$this->_tpl->assign('page', $this->_page);
		$this->_tpl->display('admin/category_list.php');
	}
	
	public function categoryAddAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(empty($postData['sort_name']) || empty($postData['sort_unique'])){
				Custom_Common::showMsg(
				'类别名称或者类别标记不能为空',
				'back',
				array("category-add" => '继续添加类别', 'category-list' => '返回类别管理')
				);
			}
			$insert_result = $this->_model->categoryOperate($postData);
			if($insert_result == 'repeat'){
				Custom_Common::showMsg(
				'类别名称重复',
				'back',
				array("category-add" => '继续添加类别', 'category-list' => '返回类别管理')
				);
			}
				
			if($insert_result){
				$this->getTicketSortById();
				Custom_Common::showMsg(
				'类别添加成功',
				'back',
				array("category-add" => '继续添加类别', 'category-list' => '返回类别管理')
				);
			}else{
				Custom_Common::showMsg(
				'类别添加失败',
				'back'
				);
			}
		}
		$this->_tpl->display('admin/category_add.php');
	}
	
	public function categoryEditAction(){
		if($this->_http->isPost()){
			$postData = $this->_http->getPost();
			if(empty($postData['sort_name']) || empty($postData['sort_unique'])){
				Custom_Common::showMsg(
				'类别名称或者类别标记不能为空',
				'back',
				array("category-edit/id:{$postData['id']}" => '继续编辑类别', 'category-list' => '返回类别管理')
				);
			}
			$insert_result = $this->_model->categoryOperate($postData);
			if($insert_result){
				$this->getTicketSortById();
				Custom_Common::showMsg(
				'类别编辑成功',
				'back',
				array("category-edit/id:{$postData['id']}" => '继续编辑类别', 'category-list' => '返回类别管理')
				);
			}else{
				Custom_Common::showMsg(
				'类别编辑失败',
				'back'
				);
			}
		}
		$id = $this->_http->get('id');
		$category = $this->_model->getCategory($id);
		$category = array_shift($category);
		$this->_tpl->assign('category', $category);
		$this->_tpl->assign('page', $this->_page);
		$this->_tpl->display('admin/category_add.php');
	}
	
	// ajax编辑排序
	public function ajaxColAction(){
		echo $resultBact = $this->_model->ajax_module_edit($this->_http->getPost());
		$this->getTicketSortById();
		exit;
	}
}