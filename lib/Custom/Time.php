<?php

/**
 * 处理显示时间
 *
 */

class Custom_Time {
    
    /**
     * 获取时间2
     * 全站时间显示规则
     * 1，	在1小时内的时间，现实多少分钟前（9分钟前）
     * 2，	超过一小时，当天内显示今天的具体时间（今天 05:12）
     * 3，	超过一天，当年内的显示月日时间（9月7日 13:46）
     * 4，      超过一年，现实具体年月日时间 （2011-12-30 20：02）
     *
     * @param int $time
     * @return string 处理后的时间
     */
    public static function getTime($time){
        $nowTime = time();
        $todayBeginTime = strtotime('today');
        $theYearBegin = strtotime(date('Y',$todayBeginTime)."0101");
        $beginToNow = $nowTime - $todayBeginTime;
        $val = max($nowTime - $time, 1);
        if($val < 60){
            return $val . '秒前';
        } else if($val >= 60 && $val < (60 * 60)){
            return intval($val / 60) . '分钟前';
        } else if($val >= (60 * 60) && $val < (60 * 60 * 24) && $beginToNow > $val){
            return date('今天 H:i', $time);
        } else {
            return date('m月d日 H:i', $time);
        } 
//            else {
//            return date('Y年m月d日 H:i', $time);
//        }
    }
    
    
    /**
     * 根据时间戳反正显示时间
     * i.	1分钟内：XX秒前
     * ii.	1-60分钟内：XX分钟前
     * iii.	1小时-24小时内：XX小时前
     * iv.	24小时以上：XX天之前
     *
     * @param string $time
     * @return string 处理后的时间
     */
    public static function getTime2($time){
        $nowTime = time();
//      $todayBeginTime = strtotime(date('Y-m-d', mktime(0, 0, 0, date("m", $nowTime), date("d", $nowTime), date("Y", $nowTime))));
//      $beginToNow = $nowTime - $todayBeginTime;
        $val = $nowTime - $time;
        if( $val >= 0 && $val < 60){
        	return intval($val) . '秒前';
        }else if($val >= 60 && $val < (60 * 60)){
            return intval($val / 60) . '分钟前';
        }else if($val >= (60 * 60) && $val < (60 * 60 * 24)){//&& $beginToNow > $val
            return ceil($val / (60 * 60)) . '小时前';
        }else if($val >= (60 * 60 * 24)){
            return intval($val / (60 * 60 * 24)) . '天之前';
        }
    }
    
    
    /**
     * 今天17：40
     * 其它12月09日
     *
     * @param int $time
     * @return string
     */
    public static function getTime3($time){
        if($time > strtotime('today')){
            return date('H:i', $time);
        }else{
            return date('m月d日', $time);
        }
    }
    
    /**
     * 今天17：40
     * 昨天17：50
     * 前天17：50、
     * 08月10日 17：50
     * @param unknown_type $time
     * @return string
     */
    public static function getTime4( $time ){
    	$nowTime = time();
    	$todayBeginTime = strtotime(date('Y-m-d', $nowTime));
    	if( $time > $todayBeginTime ){//今天
    		return date('今天 H:i', $time);
    	}else if( $time < $todayBeginTime && $time > $todayBeginTime-24*60*60 ){//昨天
    		return date('昨天 H:i', $time);
    	}else if( $time < $todayBeginTime && $time > $todayBeginTime-24*60*60*2 ){//前天
    		return date('前天 H:i', $time);
    	}else{
    		return date('m月d日 H:i', $time);
    	}
    }
    
    public static function ExecTime()
    {
        $time = explode(" ", microtime());
        $usec = (double)$time[0];
        $sec = (double)$time[1];
        return $sec + $usec;
    }
    
    public static function getDayMinuteSecond($time)
    {
    	$day = floor($time / (3600 * 24));
    	$hour = floor(($time - $day * 3600 * 24) / 3600);
    	$minutes =  floor(($time - $day * 3600 * 24 - $hour * 3600) / 60);
    	$seconds = $time - $day * 3600 * 24 - $hour * 3600 - $minutes * 60;
    	return $day . '天' .$hour . '时' .$minutes . '分' .$seconds . '秒';
    }
}