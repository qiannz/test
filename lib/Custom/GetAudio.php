<?php

/**
 * 获取音频信息
 *
 */

class Custom_GetAudio {
    private static $link_url;
    private static $xiami_url = "xiami.com";
    
    public function getInfo($link){
        self::$link_url = $link;
        return self::_getInfo();
    }
    
    private function _getInfo(){
        $audioInfo = array();
        if(strpos(self::$link_url, self::$xiami_url) !== false){
            $audioInfo = self::_getAudioInfoByXiami();
        }else{
            $strIndex = strpos(self::$link_url, ".mp3");
            if($strIndex !== false && $strIndex == (strlen(self::$link_url) - 4)){
                $audioInfo['title'] = "";
                $audioInfo['flv'] = self::$link_url;
            }
        }
        return $audioInfo;
    }
    
    // 取得虾米音频地址
    private function _getAudioInfoByXiami(){
        return '';
    }
}
?>