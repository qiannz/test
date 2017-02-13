<?php

/**
 * 验证码生成、检测
 *
 */

class Custom_Captcha {
    /**
     * 默认图片宽度
     */
    private $_width = 87;
    
    /**
     * 默认图片高度
     */
    private $_height = 23;
    
    /**
     * 默认命名空间
     */
    private $_space = 0;
    
    /**
     * 图片
     */
    public $image = null;
    
    /**
     * 对象初始化
     *
     * @param    int    $width
     * @param    int    $height
     * @return   void
     */
    public function __construct($space = NULL, $width = 0, $height = 0){
        if($width > 0){
            $this->_width = $width;
        }
        if($height > 0){
            $this->_height = $height;
        }
    }
    
    /**
     * 验证字符串是否正确
     *
     * @param   string  $word
     * @return  bool
     */
    public function checkCode($word){
        $word = strtoupper($word);
        $recorded = $this->getStoreCode($this->_space);
        if(!$recorded){
            return false;
        }
        $code = '';
        for($i = 0; $i < strlen($word); $i++){
            if(strstr('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $word[$i])){
                $code .= $word[$i];
            }
        }
        
        $given = md5(base64_encode($this->_space . '_' . $code));
        
        // 清空
        $result = (bool) ($given === $recorded);
        
        /*

        //不管是否验证正确，COOKIES中的验证码都不清除,因为当验证码通过（检测在前），其它信息未通过，页面未刷新则会出现由于无法找到COOKIES中的验证码而无法匹配------ by LuJun
        if ($result) {
            $this->deleteStoreCode($this->_space);
        }*/
        
        return $result;
    }
    
    /**
     * 创建并输出图片
     *
     * @param    void
     * @return   void
     */
    public function createCode(){
        $word = $this->randomCode();
        
        // 记录字符串
        $this->setStoreCode($this->_space, $word);
        $this->image = imagecreate($this->_width, $this->_height);
        imagecolorallocate($this->image, 220, 220, 220);
        
        // 在图片上添加扰乱元素
        $this->disturbPixel();
        
        // 在图片上添加字符串
        $this->drawCode($word);
        
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-type: image/png');
        
        imagepng($this->image);
        imagedestroy($this->image);
    }
    
    /**
     * 创建扰乱元素
     *
     * @param    void
     * @return   void
     */
    private function disturbPixel(){
        for($i = 1; $i <= 100; $i++){
            $disturbColor = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($this->image, rand(2, 128), rand(2, 38), $disturbColor);
        }
        for($i = 0; $i < 5; $i++){
            imageline($this->image, rand(0, 20), rand(0, 25), rand(90, 100), rand(20, 60), $disturbColor);
        }
    }
    
    /**
     * 在图片上添加字符串
     *
     * @param    string    $word
     * @return   void
     */
    private function drawCode($word){
        $fontPath = RESOURCE_PATH . './captcha/svenings.ttf';
        for($i = 0; $i < strlen($word); $i++){
            $color = imagecolorallocate($this->image, rand(0, 255), rand(0, 128), rand(0, 255));
            $x = floor($this->_width / strlen($word)) * $i;
            $y = rand(0, $this->_height - 15);
            // imageChar($this->image, rand(3,6), $x, $y, $word[$i], $color);
            imagettftext($this->image, 14, 0, $x, $y + 15, $color, $fontPath, $word[$i]);
        }
    }
    
    /**
     * 创建字符串
     *
     * @param    int    $length
     * @return   string
     */
    private function randomCode($length = 5){
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        for($i = 0, $count = strlen($chars); $i < $count; $i++){
            $arr[$i] = $chars[$i];
        }
        mt_srand((double) microtime() * 1000000);
        shuffle($arr);
        return substr(implode('', $arr), 5, $length);
    }
    
    /**
     * 将生成的验证码存到临时存储区中，可以为 $_SESSION，这里用 $_COOKIE
     *
     * @param string $space
     * @param string $code
     */
    private function setStoreCode($space, $code){
        $code = md5(base64_encode($space . '_' . $code));
        setcookie('__ccode_' . $space, $code, time() + 86400, '/');
        $_COOKIE['__ccode_' . $space] = $code;
    }
    
    /**
     * 从临时存储区获取生成的验证码
     *
     * @param string $space
     * @return string
     */
    private function getStoreCode($space){
        return isset($_COOKIE['__ccode_' . $space]) ? $_COOKIE['__ccode_' . $space] : '';
    }
    
    /**
     * 清空临时存储区
     *
     * @param string $space
     */
    private function deleteStoreCode($space){
        setcookie('__ccode_' . $space, '', time() - 86400, '/');
        unset($_COOKIE['__ccode_' . $space]);
    }

}