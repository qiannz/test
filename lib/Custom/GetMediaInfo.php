<?php

/**
 * 获取媒体信息
 *
 */

class Custom_GetMediaInfo {
    public function getVideoInfo($link){
        return Custom_GetVideo::getInfo($link);
    }
    
    public function getAudioInfo($link){
        return Custom_GetAudio::getInfo($link);
    }
}
?>