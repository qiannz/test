<?php
class Custom_Upload {

	private static function imageCopy($dir, $baseName, $tmpFileName) {
		if(create_folders($dir)) {
			copy($tmpFileName, $dir.$baseName);
		}
	}
	
    public static function imageUpload($_FILES, $user_id = 0, $folder = 'good', $primary_id = 0) {
    	$db = Core_DB::get('superbuy', null, true);
    	$image = Third_Image::getInstance();
    	$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    	$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
    	//上传原始图
    	$filePath = $image->upload_image($_FILES['uploadFile'], $imgPath . 'original/');
    	//文件名
		$baseName = basename($filePath);
		
		//拷贝到buy文件夹一份
		$dir = $image->make_dir($imgCopyPath  . 'original/');
		self::imageCopy($dir, $baseName, $filePath);
		
    	$configArray = @include VAR_PATH . 'config/config.php';
    	$imgSizeArray = $configArray['IMAGE_SIEZ'];
    	foreach($imgSizeArray as $key => $item) {
    		if($item['width'] == 220) {
    			$image->thumb($filePath, $item['width'], $item['height'], $imgPath . $item['width'] . '/');	
    		} else {
    			$image->make_thumb($filePath, $item['width'], $item['height'], $imgPath . $item['width'] . '/');
    		}
			$tmpFileName = str_replace('original', $item['width'], $filePath);
			//加水印
    		if($item['water'] == 1) {
    			$image->add_watermark($tmpFileName, '', ROOT_PATH.'web/data/water_mark.png', 5);
    		}
    		//拷贝到buy文件夹一份
    		$dir = $image->make_dir($imgCopyPath . $item['width'] . '/');
    		self::imageCopy($dir, $baseName, $tmpFileName);
    	}
    	
    	//160 的缩略图， 如果原始图太小则是拷贝操作
    	$thumb_small = $image->thumb($filePath, 160, 160, $imgPath . 'small/');
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath  . 'small/');
    	$tmpFileName = str_replace('original', 'small', $filePath);
    	self::imageCopy($dir, $baseName, $tmpFileName);
    	
    	
    	//原始图加水印 同时保存到一个新的文件夹中  如果 图片和宽高都小于 300 则不加水印
    	$thumb = $image->add_watermark($filePath, ROOT_PATH.'web/data/'.$folder.'/thumb/', ROOT_PATH.'web/data/water_mark.png', 5);
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath  . 'thumb/');
    	$tmpFileName = str_replace('original', 'thumb', $filePath);
    	self::imageCopy($dir, $baseName, $tmpFileName);
    	
    	
    	if($filePath) {
    		$file_name = basename($filePath);
    		$ftp_path = str_replace($imgPath . 'original/', '', substr($filePath, 0, - (strlen($file_name) + 1)));
    		if($folder == 'good') {
	    		$arr = array(
	    				'good_id'   => $primary_id,
	    				'user_id'  => $user_id,
	    				'img_url'  => str_replace($imgPath . 'original/', '', $filePath),
	    				'created' => REQUEST_TIME
	    		);
	    		$aid = $db->insert('oto_good_img', $arr);
    		} elseif($folder == 'ticket') {
    			$arr = array(
    					'ticket_id'   => $primary_id,
    					'user_id'  => $user_id,
    					'img_url'  => str_replace($imgPath . 'original/', '', $filePath),
    					'created' => REQUEST_TIME
    			);
    			$aid = $db->insert('oto_ticket_img', $arr);
    		}
    		if($aid){
    			return $aid.'|'."/buy/{$folder}/small/{$ftp_path}/{$file_name}";
    		}
    	}
    	return false;
    }
    
    // 手机上传商品图片
    public static function goodsImgageUpload($img, $user_id = 0, $good_id = 0, $folder = 'good'){
    	$db = Core_DB::get('superbuy', null, true);
    	$image = Third_Image::getInstance();
    	// 原始图路径
    	$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    	// 拷贝图路径
    	$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
    	// 原始图生成文件夹
    	$dir_img = $image->make_dir($imgPath  . 'original/');
    	// 随机生成图片名字
    	$temp_img = time().Custom_Common::random(6).'.jpg';
    	
    	$filePath = $dir_img.$temp_img;
    	
    	// 生成转化过来的图片
    	file_put_contents($filePath, base64_decode(urldecode($img)));
    	
    	$baseName = basename($filePath);
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath  . 'original/');
    	self::imageCopy($dir, $baseName, $filePath);
    	
    	$configArray = @include VAR_PATH . 'config/config.php';
    	$imgSizeArray = $configArray['IMAGE_SIEZ'];
    	foreach($imgSizeArray as $key => $item) {
    		if($item['width'] == 220) {
    			$image->thumb($filePath, $item['width'], $item['height'], $imgPath . $item['width'] . '/');
    		} else {
    			$image->make_thumb($filePath, $item['width'], $item['height'], $imgPath . $item['width'] . '/');
    		}
    		$tmpFileName = str_replace('original', $item['width'], $filePath);
    		//加水印
    		if($item['water'] == 1) {
    			$image->add_watermark($tmpFileName, '', ROOT_PATH.'web/data/water_mark.png', 5);
    		}
    		//拷贝到buy文件夹一份
    		$dir = $image->make_dir($imgCopyPath . $item['width'] . '/');
    		self::imageCopy($dir, $baseName, $tmpFileName);
    	}
    	 
    	//160 的缩略图， 如果原始图太小则是拷贝操作
    	$thumb_small = $image->make_thumb($filePath, 160, 160, $imgPath . 'small/');
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath  . 'small/');
    	$tmpFileName = str_replace('original', 'small', $filePath);
    	self::imageCopy($dir, $baseName, $tmpFileName);
    	 
    	 
    	//原始图加水印 同时保存到一个新的文件夹中  如果 图片和宽高都小于 300 则不加水印
    	$thumb = $image->add_watermark($filePath, ROOT_PATH.'web/data/'.$folder.'/thumb/', ROOT_PATH.'web/data/water_mark.png', 5);
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath  . 'thumb/');
    	$tmpFileName = str_replace('original', 'thumb', $filePath);
    	self::imageCopy($dir, $baseName, $tmpFileName);
    	 
    	 
    	if($filePath) {
    		$file_name = basename($filePath);
    		$arr = array(
    				'good_id'   => $good_id,
    				'user_id'  => $user_id,
    				'img_url'  => str_replace($imgPath . 'original/', '', $filePath),
    				'created' => REQUEST_TIME
    		);
    		$aid = $db->insert('oto_good_img', $arr);
    		if($aid){
    			return $aid;
    		}
    	}
    	return false;
    }
    
    // 手机上传商城商品图片
    public static function commodityImgageUpload($data) {    	
    	$img = $data['img'];
    	$user_id = intval($data['user_id']);
    	$shop_id = intval($data['shop_id']);
    	$folder = $data['folder'];
    	
    	$image = Third_Image::getInstance();
    	// 原始图路径
    	$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    	// 拷贝图路径
    	$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
    	// 原始图生成文件夹
    	$dir_img = $image->make_dir($imgPath);
    	// 随机生成图片名字
    	$temp_img = time().Custom_Common::random(9).'.jpg';
    	 
    	$filePath = $dir_img.$temp_img;
    	 
    	// 生成转化过来的图片
    	file_put_contents($filePath, base64_decode(urldecode($img)));
    	 
    	$baseName = basename($filePath);
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath);
    	self::imageCopy($dir, $baseName, $filePath);

    	if($filePath) {
    		$file_name = basename($filePath);
    		$img_url = str_replace($imgPath, '', $filePath);
    		return Model_Admin_Ticket::getInstance()->wapUploadImg($img_url, 0, $shop_id, $user_id);
    	}
    	
    	return false;
    }
    
    // 折扣图片上传
    public static function discountImgageUpload($data) {
    	$img = $data['img'];
    	$user_id = intval($data['user_id']);
    	$folder = $data['folder'];
    	 
    	$image = Third_Image::getInstance();
    	// 原始图路径
    	$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    	// 拷贝图路径
    	$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
    	// 原始图生成文件夹
    	$dir_img = $image->make_dir($imgPath);
    	// 随机生成图片名字
    	$temp_img = time().Custom_Common::random(9).'.jpg';
    
    	$filePath = $dir_img.$temp_img;
    
    	// 生成转化过来的图片
    	file_put_contents($filePath, base64_decode(urldecode($img)));
    
    	$baseName = basename($filePath);
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath);
    	self::imageCopy($dir, $baseName, $filePath);
    
    	if($filePath) {
    		$file_name = basename($filePath);
    		$img_url = str_replace($imgPath, '', $filePath);
    		return Model_Api_Discount::getInstance()->wapUploadImg($img_url, 0, $user_id);
    	}
    	 
    	return false;
    }
    
    public static function imageDelete($aid, $folder = 'good') {
    	$db = Core_DB::get('superbuy', null, true);
    	if($folder == 'good') {
    		$img_url = $db->fetchOne("select `img_url` from `oto_good_img` where `good_img_id` = '{$aid}' limit 1");
    		$result = $db->delete('oto_good_img', '`good_img_id` = ' . $aid);
    	} elseif ($folder == 'ticket') {
    		$img_url = $db->fetchOne("select `img_url` from `oto_ticket_img` where `id` = '{$aid}' limit 1");
    		$result = $db->delete('oto_ticket_img', '`id` = ' . $aid);
    	}
    	
    	if($result) {
    		$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    		$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
    		
    		$configArray = @include VAR_PATH . 'config/config.php';
    		$imgSizeArray = $configArray['IMAGE_SIEZ'];
    		foreach($imgSizeArray as $key => $item) {
    			//unlink($imgPath . $item['width'] . '/' . $img_url);
    			//unlink($imgCopyPath . $item['width'] . '/' . $img_url);
    		}   		    		
    		//unlink($imgPath . 'original/'. $img_url);	unlink($imgCopyPath . 'original/'. $img_url);
    		//unlink($imgPath . 'small/'. $img_url);		unlink($imgCopyPath . 'small/'. $img_url);
    		//unlink($imgPath . 'thumb/'. $img_url);		unlink($imgCopyPath . 'thumb/'. $img_url);
    		return true;
    	}
    	return false;
    }
    
    public static function recommendImgUpload($_FILES, $postData, $folder = 'recommend') {
    	// 上传图片
    	$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    	$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';
    	
    	$image = Third_Image::getInstance();
    	
    	$size = getimagesize($_FILES['uploadFile']['tmp_name']);
    	if (self::checkImg($postData['pos_id'], $size) == '1'){  		
    		$filePath = $image->upload_image($_FILES['uploadFile'], $imgPath);
			$baseName = basename($filePath);
			
			//拷贝到buy文件夹一份
			$dir = $image->make_dir($imgCopyPath);
			self::imageCopy($dir, $baseName, $filePath);
			
	    	if($filePath){
	    		return str_replace(ROOT_PATH.'web/data/'.$folder.'/', '', $filePath);
	    	}
    	} else {
    		return 'img_error';
    	}
	
    	return false;
    }
    
    public static function checkImg($pos_id, $size = array()) {
    	$db = Core_DB::get('superbuy', null, true);
    	$sizeInfo = $db->fetchRow("select * from oto_position where pos_id = '{$pos_id}'");
    	$width = $sizeInfo['width'];
    	$height = $sizeInfo['height'];
    		
    	$width_new = $size['0'];
    	$height_new = $size['1'];
    		
    	if($width && $height && $width_new == $width && $height_new == $height){
    		return '1';
    	}elseif ($width && empty($height) && $width_new == $width){
    		return '1';
    	}else{
    		return '0';
    	}
    }

    public static function singleImgUpload($uploadFile, $folder) {
    	// 上传图片
    	$imgPath = ROOT_PATH.'web/data/'.$folder.'/';
    	$imgCopyPath = ROOT_PATH.'web/data/buy/'.$folder.'/';

    	$image = Third_Image::getInstance();

    	$filePath = $image->upload_image($uploadFile, $imgPath);
    	$baseName = basename($filePath);
    			
    	//拷贝到buy文件夹一份
    	$dir = $image->make_dir($imgCopyPath);
    	self::imageCopy($dir, $baseName, $filePath);
    			
    	if($filePath){
    		return str_replace(ROOT_PATH.'web/data/'.$folder.'/', '', $filePath);
    	}
    	return false;
    }
}
