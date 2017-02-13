<?php
/**
 * Enter description here ...
 *
 * @author ellison
 * @copyright 2011-2012 mplife.com
 * @file: file_name 2015 2012-7-31 ����03:05:43 ellison
 * @history 2012-7-31  ::  ellison  ::  Create File
 * @version
 */

class Custom_FtpUpload {

     private static $conn_id;
          
     public static function upload2($newName, $tempImageName, $newDir) {
     	if(!isset(self::$conn_id) && empty(self::$conn_id)){
     		self::$conn_id = ftp_connect($GLOBALS['GLOBAL_CONF']['FTP_Host']) or die ("Ftp Connect Error!");
     	}    	
     	ftp_login(self::$conn_id, $GLOBALS['GLOBAL_CONF']['FTP_UserName'], $GLOBALS['GLOBAL_CONF']['FTP_PassWord']);
     	ftp_pasv(self::$conn_id, true); // 穿越防火墙，开启被动模式
     
     	if(!self::mk_dir($newDir)){
     		return -2;//创建目录失败
     	}
     	$upload = ftp_put(self::$conn_id, $newName, $tempImageName, FTP_BINARY, 0); // 上传
     	if ($upload) {
     		return 1; //上传成功
     	} else {
     		return -1;//上传失败
     	}
     }
      
     public static function mk_dir($path)
     {
     	$dir = explode("/", $path);
     	$path="";
     	$ret = true;
     	 
     	for ($i=0;$i<count($dir);$i++)
     	{
	     	$path.="/".$dir[$i];
	     	if(!@ftp_chdir(self::$conn_id,$path)){
		     	@ftp_chdir(self::$conn_id,"/");
		     	if(!@ftp_mkdir(self::$conn_id,$path)){
			     	$ret=false;
			     	break;
		     	}
	     	}
     	}
     	return $ret;
     } 

     public static function delete($path){
     	if(!isset(self::$conn_id) && empty(self::$conn_id)){
     		self::$conn_id = ftp_connect($GLOBALS['GLOBAL_CONF']['FTP_Host']) or die ("Ftp Connect Error!");
     	}
     	ftp_login(self::$conn_id, $GLOBALS['GLOBAL_CONF']['FTP_UserName'], $GLOBALS['GLOBAL_CONF']['FTP_PassWord']);
     	ftp_pasv(self::$conn_id, true); // 穿越防火墙，开启被动模式
		if(is_array($path)){
			foreach($path as $pathItem){
				ftp_delete(self::$conn_id, $pathItem);
			}
		}else{
			ftp_delete(self::$conn_id, $path);
		}
     }
     
     public static function upload($sid) {
     	if(!isset(self::$conn_id) && empty(self::$conn_id)){
     		self::$conn_id = ftp_connect($GLOBALS['GLOBAL_CONF']['FTP_Host']) or die ("Ftp Connect Error!");
     	}
     	ftp_login(self::$conn_id, $GLOBALS['GLOBAL_CONF']['FTP_UserName'], $GLOBALS['GLOBAL_CONF']['FTP_PassWord']);
     	ftp_pasv(self::$conn_id, true); // 穿越防火墙，开启被动模式
     	 
     	$dirname = ROOT_PATH . 'web/data/'.$sid;
     	$to = '/'.$sid;   	
     	ftp_mkdir(self::$conn_id, $to);     	
     	$uploadResult = self::listDir($dirname, $to);
     	
     	if ($uploadResult) {
     		return true; //上传成功
     	} else {
     		return false;//上传失败
     	}
     }
     
     private function listDir($dirname, $to) {
     	set_time_limit(300);
     	
     	$dir = opendir ( $dirname );
     	while ( ($file = readdir ( $dir )) != false ) {
     		if ($file == "." || $file == "..") {
     			continue;
     		}
     		if (is_dir ( $dirname . "/" . $file )) {
     			//array_push ( $this->dirs, $dirname . "/" . $file );   			
     			//ftp_chdir ( self::$conn_id, "{$to}/{$file}" );   			
     			ftp_mkdir ( self::$conn_id, "{$to}/{$file}" );
     			self::listDir ( $dirname . "/" . $file,  "{$to}/{$file}");
     		} else {
     			//array_push ( $this->files, $dirname . "/" . $file );
     			ftp_chdir ( self::$conn_id, "$to" );
     			if (self::endsWith ( $file, ".jpg" ) || self::endsWith ( $file, ".png" ) || self::endsWith ( $file, ".gif" ) || self::endsWith ( $file, ".exe" ) || self::endsWith ( $file, ".zip" ) || self::endsWith ( $file, ".swf" ) || self::endsWith ( $file, ".db" ) || self::endsWith ( $file, ".dll" ))
     			{
     				$upload = ftp_put ( self::$conn_id, $file, $dirname . "/" . $file, FTP_BINARY );
     				if(!$upload){
     					return false;
     				}
     			}
     			else
     			{
     				$upload = ftp_put ( self::$conn_id, $file, $dirname . "/" . $file, FTP_ASCII );
     				if(!$upload){
     					return false;
     				}
     			}
     		}
     	}
     	return true;
     }
     		 
     private function endsWith($haystack, $needle) {
     	$length = strlen ( $needle );
     	if ($length == 0) {
     		return true;
     	}
     	return (substr ( $haystack, - $length ) === $needle);
     }
}
