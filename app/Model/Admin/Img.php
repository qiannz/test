<?php
class Model_Admin_Img extends Base {
	private static $_instance;
	
	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function add($name, $width, $height, $water) {
	    $config_value_arr = array(
	        'name'   => $name,
	        'width'  => $width,
	        'height' => $height,
	        'water'  => $water
	    );
	    $config_value = serialize($config_value_arr);
	    $inArr = array (
	        'config_key' => 'IMAGE_SIEZ',
	        'config_value' => $config_value,
	        'config_ex' => '图片尺寸设置',
	        'created'   => REQUEST_TIME,
	        'updated'   => REQUEST_TIME
	    );
		return $this->_db->insert('oto_config', $inArr);
	}
	
	public function getList() {
		return $this->_db->fetchAll("select * from oto_config where config_key = 'IMAGE_SIEZ' order by created desc");
	}
	
	public function del($id) {
		return $this->_db->delete('oto_config', '`config_id` = ' . $id);
	}
}