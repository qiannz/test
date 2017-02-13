<?php
class Model_Admin_Appset extends Base
{
	private static $_instance;
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function addEdit($getData) {		
		$id = intval($getData['id']);
		$type = $getData['type'];
		$awardName = Custom_String::HtmlReplace($getData['awardName']);
		$awardNum = intval($getData['awardNum']);
		$pro = intval($getData['pro']);
		$dayList = intval($getData['dayList']);
		$totalList = intval($getData['totalList']);
		$msg = Custom_String::HtmlReplace($getData['msg']);
		
		$param = array(
					'type' => !in_array($type,array('star','virtual','real', 'call')) ? 'star' : $type,
					'award_name' => $awardName,
					'award_number' => $awardNum,
					'pro' => $pro,
					'every_day_limit' => $dayList,
					'total_limit' => $totalList,
					'msg' => $msg
				);
		
		if($id) {
			return $this->_db->update('oto_app_wheel_set', $param, array('id' => $id));
		} else {
			return $this->_db->insert('oto_app_wheel_set', $param);
		}
	}
	
	public function getList() {
		$data = $this->select('', 'oto_app_wheel_set');
		return $data;
	}
	
	public function del($id) {
		return $this->_db->delete('oto_app_wheel_set', '`id` = ' . $id);
	}	
}