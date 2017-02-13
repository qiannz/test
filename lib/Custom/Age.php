<?php

class Custom_Age {
    public static $constellationArr = array('水瓶座', '双鱼座', '白羊座', '金牛座', '双子座', '巨蟹座', '狮子座', '处女座', '天秤座', '天蝎座', '射手座', '魔羯座');
    public static $constellationEdgeDay = array(20, 19, 21, 21, 21, 22, 23, 23, 23, 23, 22, 22);
    
    /**
     * 根据日期获取所在星座
     *
     * @param int $month
     * @param int $day
     * @param bool $isGetName
     * @return bool
     */
    public static function date2Constellation($month, $day, $isGetName = true){
        $month = intval($month);
        $day = intval($day);
        
        $cont = ($month - 1) < 0 ? 0 : ($month - 1);
        if($day < self::$constellationEdgeDay[$cont]){
            $cont = $cont - 1;
        }
        
        if($cont >= 0){
            return $isGetName ? self::$constellationArr[$cont] : $cont;
        }
        
        // 默认魔羯座
        return $isGetName ? self::$constellationArr[11] : 11;
    }
    
    /**
     * 根据出生年月获取当前年龄
     *
     * @param int $year 出生年份
     * @return int 年龄
     */
    public static function getAge($year){
        $yearDiff = intval(date('Y', time())) - intval($year);
        return min(100, max(0, $yearDiff));
    }
}