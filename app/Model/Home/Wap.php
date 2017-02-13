<?php 
class Model_Home_Wap extends Base
{
	private static $_instance;
	private static $_inquireArray;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getCurrentInquireByType($tag) {	
		if(empty(self::$_inquireArray)) {
			$inquireArray = array();
			$sql = "select * from `oto_survey_title` where `tag` = '{$tag}' order by survey_id asc";
			$inquireArray = $this->_db->fetchAssoc($sql);
			foreach($inquireArray as $key => $inquireItem) {
				$inquireArray[$key]['child'] = $this->select("`survey_id` = '{$key}'", 'oto_survey_details', '*', 'survey_detail_id asc');
			}
			
			self::$_inquireArray = $inquireArray;
			
		}
		return self::$_inquireArray;
	}
	
	public function inquireReplace($survey_id, $survey_detail_id, $user_id, $opinion = '') {
		$this->_db->replace('oto_survey_results', array(
				'survey_id' => $survey_id,
				'survey_detail_id' => $survey_detail_id,
				'opinion' => $opinion,
				'user_id' => $user_id,			
				'ip' => CLIENT_IP,
				'created' => REQUEST_TIME
		));
		return true;
	}
	
	public function inquireInsert($resultInsertArray) {
		$this->_db->insert('oto_survey_items', array(
					'data' => serialize($resultInsertArray),
					'ip' => CLIENT_IP,
					'created' => REQUEST_TIME
				));
		return true;
	}
}