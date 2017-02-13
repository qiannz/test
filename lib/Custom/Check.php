<?php

/**
 * 检测常用格式
 *
 */

class Custom_Check {
    /**
     * 是否为Email
     *
     * @param    string    $string
     * @return   bool
     */
    public static function isEmail($string){
        return preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/', $string);
    }
    
    /**
     * 是否为手机号码
     *
     * @param    string    $string
     * @return   bool
     */
    public static function isMobile($string){
        return preg_match('/^(((1[3|4|5|8]{1}[0-9]{1}))[0-9]{8})$/', $string);
    }
    
    /**
     * 是否为电话号码
     *
     * @param    string    $string
     * @return   bool
     */
    public static function isTel($string){
        return preg_match('/^((0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/', $string);
    }
    
    /**
     * 是否为QQ号码
     *
     * @param    string    $string
     * @return   bool
     */
    public static function isQq($string){
        return preg_match('/^\d{4,}$/', $string);
    }
    
    /**
     * 是否为邮政编码号码
     *
     * @param    string    $string
     * @return   bool
     */
    public static function isZip($string){
        return preg_match('/^\d{6}$/', $string);
    }
    
    /**
     * 是否为传真号码
     *
     * @param    string    $string
     * @return   bool
     */
    public static function isFax($string){
        return preg_match('/^((0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/', $string);
    }
    
    /**
     * 是否十进制数
     *
     * @param    float    $num
     * @return   bool
     */
    public static function isDecimal($num){
        return preg_match('/^[+-]?\d+(\.\d+)?$/', $num);
    }
    
    /**
     * 是否机器人请求
     *
     * @return bool
     */
    public static function isRobot(){
        $botchar = '/(bot|crawl|spider|slurp|yahoo|sohu-search|lycos|robozilla)/i';
        return preg_match($botchar, strtolower($_SERVER['HTTP_USER_AGENT']));
    }
    
    /**
     * 验证日期是否正确
     *
     * @param string $date
     * @return bool
     */
    public static function isDate($date){
        if(empty($date)){
            return false;
        }
        $tmp = explode('-', $date);
        return checkdate($tmp[1], $tmp[2], $tmp[0]);
    }
}