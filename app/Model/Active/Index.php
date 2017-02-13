<?php
class Model_Active_Index extends Base {
	
	private static $_instance;
	
	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}