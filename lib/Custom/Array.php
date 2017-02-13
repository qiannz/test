<?php

/**
 * 字符串处理函数
 *
 */

class Custom_Array {
    
    public static function arrayFilter($data){
        return Custom_String::deepFilterData($data, 'array_filter');
    }
    
    /**
     * 去除数组中的空键/去除两边空格 是否去除为int or string = 0的
     *
     * @param array $arr
     * @param boolen $zero
     * @return array
     */
    public static function arrayRemoveEmpty($arr, $zero = false){
        foreach($arr as $key=>$value){
            if(is_array($value)){
                $arr[$key] = self::arrayRemoveEmpty($value);
            }else{
                $value = trim($value);
                if($value == ''){
                    unset($arr[$key]);
                }else{
                    if(true == $zero && 0 == intval($value)){
                        unset($arr[$key]);
                    }
                }
            }
        }
        return $arr;
    }
    
    /**
     * 在多维数组中搜索值是否存在
     *
     * @param mixed $find
     * @param array $multiArray
     *
     * @return bool
     */
    public static function inMultiArray($find, $multiArray){
        $isFound = false;
        if(is_array($multiArray)){
            foreach($multiArray as $key=>$val){
                if(is_array($val)){
                    $isFound = self::inMultiArray($find, $val);
                }else{
                    if($find == $val){
                        $isFound = true;
                    }
                }
                if($isFound){
                    break;
                }
            }
            return $isFound;
        }
        return false;
    }
    
    /**
     * 取出数组某列
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function arrayFetchCol($array, $key){
        $result = array();
        foreach($array as $value){
            $result[] = $value[$key];
        }
        return $result;
    }
    
    /**
     * 随机数组中的元素
     *
     * @param array $array
     * @param int $rndNum 取几个
     * @return mixed
     */
    public static function arrayRand($array, $rndNum = 1){
        if(!$array || !is_array($array)){
            return false;
        }
        if(count($array) <= $rndNum){
            return $array;
        }
        if($rndNum == 1){ // 随机一个
            $randKey = array_rand($array);
            return $array[$randKey];
        }else{ // 随机多个
            $result = array();
            $randKeys = array_rand($array, $rndNum);
            foreach($randKeys as $randKey){
                $result[] = $array[$randKey];
            }
            return $result;
        }
    }
    
    public static function toJsObject($array = array()){
        $jsObject = '{';
        foreach($array as $key=>$var){
            if(is_array($var)){
                $jsObject .= self::toJsObject($var);
            }
            $jsObject .= "{$key}:'{$var}', ";
        }
        $jsObject = trim($jsObject, ", ");
        $jsObject .= '}';
        return $jsObject;
    }

}