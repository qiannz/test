<?php

/**
 * GOOGLE MAP 相关信息
 *
 */

class Custom_Map extends Base {
    /**
     * map key
     *
     * @var string
     */
    private static $key = 'dc27b1d958ee7725aa1b6899af7b50816258da9bf3ffa0f736db1bf3ca24877b7a25104e7f587e1c';
    
    /**
     * 反回结果类型，JSON,XML
     *
     * @var string
     */
    private static $_output = 'json';
    
    /**
     * URL
     *
     * @var string
     */
    private static $_url = 'http://maps.google.com/maps/geo?';
    
    /**
     * 址址转成坐标。
     *
     * @param string $address 中文地址（不转码）
     * @return array or false
     */
    public static function getCoordinate($address){
        $url = self::getUrl($address);
        $data = json_decode(file_get_contents($url), true);
        if(empty($data) || $data['Status']['code'] != 200){
            return false;
        }else{
            return $data['Placemark'][0]['Point']['coordinates'];
        }
    }
    
    /**
     * 拼接 URL
     *
     * @param string $address
     * @return string
     */
    public static function getUrl($address){
        return self::$_url . 'q=' . rawurlencode($address) . '&output=' . self::$_output . '&sensor=true&region=CHN&language=zh_cn&key=' . self::$key;
    }
}