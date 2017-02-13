<?php
class Controller_Home_Wap extends Controller_Home_Abstract {
	private $_model;
	
	public function __construct() {
		parent::__construct();
		$this->_model = Model_Home_Wap::getInstance();
	}
	/**
	 * 调查问卷
	 */
	public function inquireAction() {
		//记录用户名
		if($this->_http->has('uname')) {
			cookie('INQUIRE_UNAME', urldecode(trim($this->_http->get('uname'))));
		}
		//获取所有的调查问卷标题和内容
		$inquireArray = $this->_model->getCurrentInquireByType('app');
		//格式化调查步骤
		$arrFirst = array('home');
		$arrContent = array_keys($inquireArray);
		$arrEnd = array('submit', 'success');		
		$stepArray = array_merge($arrFirst, $arrContent, $arrEnd);
		//获取当前用户的调查进度
		$step = $this->_http->has('step') ? $this->_http->get('step') : 1;
		//STEP超出的话不存在的话，重新开始
		if(!array_key_exists($step - 1, $stepArray)) {
			$step = 1;
		}
		if($step == 1 && count($_COOKIE['INQUIRE']) > 0) {
			foreach($_COOKIE['INQUIRE'] as $wordId => $value){
				cookie("INQUIRE[$wordId]", "", 1);
			}
			header301($GLOBALS['GLOBAL_CONF']['SITE_URL'] .'/home/wap/inquire');
		}
		//调查必须按顺序进行
		if($step > 2 && $step <= count($stepArray) - 2) {
			if(count($_COOKIE['INQUIRE']) != $step - 2) {
				foreach($_COOKIE['INQUIRE'] as $wordId => $value){
					cookie("INQUIRE[$wordId]", "", 1);
				}
				header301($GLOBALS['GLOBAL_CONF']['SITE_URL'] .'/home/wap/inquire');
			}
		}
		//确保调查全部完成
		if($step == count($stepArray) - 1) {
			if(count($_COOKIE['INQUIRE']) != count($stepArray) - 3) {
				foreach($_COOKIE['INQUIRE'] as $wordId => $value){
					cookie("INQUIRE[$wordId]", "", 1);
				}
				header301($GLOBALS['GLOBAL_CONF']['SITE_URL'] .'/home/wap/inquire');
			}
		}
		
		//不是开头和结尾的话，传送调查问卷标题和内容
		if(is_numeric($stepArray[$step - 1])) {
			$this->_tpl->assign('stepNum', $step - 1);
			$this->_tpl->assign('inquireContent', $inquireArray[$step - 1]);
		}
		
		$this->_tpl->assign('step', $stepArray[$step - 1]);
		$this->_tpl->assign('next', $step + 1);
		$this->_tpl->display('wap/inquire.php');
	}

	public function inquireCookieAction() {
		$itype = intval($this->_http->get('itype'));
		$survey_id = intval($this->_http->get('survey_id'));
		$answer = Custom_String::HtmlReplace($this->_http->get('answer'), 1);
		cookie("INQUIRE[$survey_id]", $itype . '|' . $answer);
		_exit('success', 100);
	}
	
	public function inquireCookieResultAction() {
		if(!empty($_COOKIE['INQUIRE'])) {
			ksort($_COOKIE['INQUIRE']);
			$resultArray = array();
			$inquireArray = $this->_model->getCurrentInquireByType('app');
			foreach($_COOKIE['INQUIRE'] as $key => $item) {
				$type = substr($item, 0, 1);
				switch ($type) {
					case 0:
						$resultKey = $inquireArray[$key]['title'];
						foreach($inquireArray[$key]['child'] as $childItem) {
							if($childItem['survey_detail_id'] == substr($item, 2)) {
								$resultValue = $childItem['survey_detail'];
								break;
							}
						}
						$resultArray[$resultKey] = $resultValue;
						break;
					case 1:
						$resultKey = $inquireArray[$key]['title'];
						$resultValue = '';
						foreach($inquireArray[$key]['child'] as $childItem) {
							if(in_array($childItem['survey_detail_id'], explode(',', substr($item, 2)))) {
								$resultValue .= $childItem['survey_detail'] . '|';
							}
						}
						$resultArray[$resultKey] = substr($resultValue, 0, -1);
						break;
					case 2:
						$resultKey = $inquireArray[$key]['title'];
						$resultValue = substr($item, 2);
						$resultArray[$resultKey] = $resultValue;
						break;
				}
			}
			$resultInsertArray = array();
			foreach($resultArray as $key => $value) {
				$resultInsertArray[] = array(
							'key' => $key,
							'value' => $value
						);
			}
			if(!empty($_COOKIE['INQUIRE_UNAME'])) {
				$resultInsertArray = array_merge($resultInsertArray, array(array('key' =>'用户名', 'value' => $_COOKIE['INQUIRE_UNAME'])));
			}
			if($this->_model->inquireInsert($resultInsertArray)) {
				foreach($_COOKIE['INQUIRE'] as $wordId => $value){
					cookie("INQUIRE[$wordId]", "", 1);
				}
			}
		}
		_exit('success', 100);
	}
	
	public function inquireResultAction() {
		$itype = intval($this->_http->get('itype'));
		$survey_id = intval($this->_http->get('survey_id'));
		$answer = Custom_String::HtmlReplace($this->_http->get('answer'), 1);
		
		switch ($itype) {
			case 0:
				$this->_model->inquireReplace($survey_id, $answer, $this->_user_id);
				break;
			case 1:
				$answerArray = explode(',', $answer);
				if(!empty($answerArray)) {
					foreach($answerArray as $survey_detail_id) {
						$this->_model->inquireReplace($survey_id, $survey_detail_id, $this->_user_id);
					}
				}
				break;
			case 2:
				$this->_model->inquireReplace($survey_id, 0, $this->_user_id, $answer);
				break;
		}
		_exit('success', 100);
	}
}