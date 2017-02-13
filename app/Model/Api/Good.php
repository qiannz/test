<?php
class Model_Api_Good extends Base
{
	private static $_instance;

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 图片缩略
	 * @param unknown_type $img_url
	 * @param unknown_type $folder
	 * @param unknown_type $w
	 * @param unknown_type $h
	 */
	public function imgThumb($img_url, $folder, $w, $h, $is_show = true) {
		$real_img_url = ROOT_PATH . 'web/data/buy/' . $folder . '/' . $img_url;
		$file_name = basename($real_img_url);
		$ext_name = getExtensionName($file_name);
		$thumb_img_url = rtrim($real_img_url, '.' . $ext_name) . '_' . $w . $h . '.' . $ext_name;
	
		$www_thumb_img_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/' . $folder . '/' . rtrim($img_url, '.' . $ext_name) . '_' . $w . $h . '.' . $ext_name;

		if (file_exists($thumb_img_url)) {
			if($is_show) {
				header('Location: ' . $www_thumb_img_url);
				exit();
			} else {
				return $thumb_img_url;
			}
		}
	
		$image = Third_Image::getInstance();
		if($image->commodityImgThumb($real_img_url, $w, $h, $thumb_img_url)) {
			if($is_show) {
				header('Location: ' . $www_thumb_img_url);
				exit();
			} else {
				return $thumb_img_url;
			}
		}
	}
	
	/**
	 * 图片缩略2
	 * @param unknown_type $img_url
	 * @param unknown_type $folder
	 * @param unknown_type $w
	 * @param unknown_type $h
	 */
	public function specialImgThumb($img_url, $folder, $w, $h, $is_show = true) {
		$real_img_url = ROOT_PATH . 'web/data/buy/' . $folder . '/' . $img_url;
		$file_name = basename($real_img_url);
		$ext_name = getExtensionName($file_name);
		$thumb_img_url = rtrim($real_img_url, '.' . $ext_name) . '_' . $w . $h . '.' . $ext_name;
	
		$www_thumb_img_url = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/' . $folder . '/' . rtrim($img_url, '.' . $ext_name) . '_' . $w . $h . '.' . $ext_name;
		if (file_exists($thumb_img_url)) {
			if($is_show) {
				header('Location: ' . $www_thumb_img_url);
				exit();
			} else {
				return $thumb_img_url;
			}
		}
	
		$image = Third_Image::getInstance();
		if($image->specialImgThumb($real_img_url, $w, $h, $thumb_img_url)) {
			if($is_show) {
				header('Location: ' . $www_thumb_img_url);
				exit();
			} else {
				return $thumb_img_url;
			}
		}
	}
}