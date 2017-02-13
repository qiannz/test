<?php

/**
 * 获取在线视频信息
 *
 */

class Custom_GetVideo {
    private static $link_url;
    private static $todou_url = "tudou.com";
    private static $youku_url = 'youku.com';
    private static $ku6_url = 'ku6.com';
    private static $url_56 = '56.com';
    private static $ouou_url = 'ouou.com';
    private static $sohu_url = 'sohu.com';
    private static $sina_url = 'sina.com.cn';
    
    public function getInfo($link){
        self::$link_url = $link;
        return self::_getInfo();
    }
    
    private function _getInfo(){
        if(strpos(self::$link_url, self::$todou_url) !== false){
            $videoInfo = self::_getVideoInfoByToDou();
        }else if(strpos(self::$link_url, self::$youku_url)){
            $videoInfo = self::_getVideoInfoByYouKu();
        }else if(strpos(self::$link_url, self::$ku6_url)){
            $videoInfo = self::_getVideoInfoByKu6();
        }else if(strpos(self::$link_url, self::$url_56)){
            $videoInfo = self::_getVideoInfoBy56();
        }else if(strpos(self::$link_url, self::$ouou_url)){
            $videoInfo = self::_getVideoInfoByOuou();
        }else if(strpos(self::$link_url, self::$sohu_url)){
            $videoInfo = self::_getVideoInfoBySohu();
        }else if(strpos(self::$link_url, self::$sina_url)){
            $videoInfo = self::_getVideoInfoBySina();
        }else{
            return "";
        }
        return $videoInfo;
    }
    
    // 取得土豆网的视频地址和预览图片.
    private function _getVideoInfoByToDou(){
        $videoInfo = array();
        
        if(preg_match("/http:\/\/(www.)?tudou.com\/programs\/view\/([^\/]+)/i", self::$link_url, $matches)){
            $flv = 'http://www.tudou.com/v/' . $matches[2];
            $str = file_get_contents(self::$link_url);
            if(!empty($str) && preg_match("/<span class=\"s_pic\">(.+?)<\/span>/i", $str, $image)){
                $img = trim($image[1]);
            }
            
            //标题
            preg_match("/<title>(.*?)_(.*)_(.*)<\/title>/", $str, $title);
            if(!empty($title)){
                preg_match("/charset=(\w+(-*\w*)*)/i", $str, $charset);
                $title = iconv($charset[0][2], "utf-8", $title[0][2]);
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    // 取得优酷网的视频地址和预览图片.
    private function _getVideoInfoByYouKu(){
        $videoInfo = array();
        
        if(preg_match("/http:\/\/v.youku.com\/v_show\/id_([^\/]+)(.html|)/i", self::$link_url, $matches)){
            //flv地址
            $flv = 'http://player.youku.com/player.php/sid/' . $matches[1] . '/v.swf';
            $api = 'http://v.youku.com/player/getPlayList/VideoIDS/' . $matches[1];
            $str = stripslashes(file_get_contents($api));
            if(!empty($str) && preg_match("/\"logo\":\"(.+?)\"/i", $str, $image)){
                //获取图片
                $url = substr($image[1], 0, strrpos($image[1], '/') + 1);
                $filename = substr($image[1], strrpos($image[1], '/') + 2);
                $img = $url . '0' . $filename;
                //获取视频标题
                preg_match("/<title>(.*?) - (.*)<\/title>/", $str, $title);
                if(!empty($title)){
                    preg_match("/charset=(\w+(-*\w*)*)/i", $title, $charset);
                    $title = iconv($charset[1], "utf-8", $title[1]); // $title[1];
                }
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    // 取得酷6网视频地址和预览图片.
    private function _getVideoInfoByKu6(){
        $videoInfo = array();
        if(strpos(self::$link_url, 'v.ku6.com/show/') !== FALSE){
            if(preg_match("/http:\/\/v.ku6.com\/show\/([^\/]+).html/i", self::$link_url, $matches)){
                $flv = 'http://player.ku6.com/refer/' . $matches[1] . '/v.swf';
                $api = 'http://vo.ku6.com/fetchVideo4Player/1/' . $matches[1] . '.html';
                $str = file_get_contents($api);
                if(!empty($str) && preg_match("/\"picpath\":\"(.+?)\"/i", $str, $image)){
                    $img = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
                }
            }
        }elseif(strpos(self::$link_url, 'v.ku6.com/special/show_') !== FALSE){
            if(preg_match("/http:\/\/v.ku6.com\/special\/show_\d+\/([^\/]+).html/i", self::$link_url, $matches)){
                $flv = 'http://player.ku6.com/refer/' . $matches[1] . '/v.swf';
                $api = 'http://vo.ku6.com/fetchVideo4Player/1/' . $matches[1] . '.html';
                $str = file_get_contents($api);
                if(!empty($str) && preg_match("/\"picpath\":\"(.+?)\"/i", $str, $image)){
                    $img = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
                }
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    // 取得56网视频地址和预览图片
    private function _getVideoInfoBy56(){
        $videoInfo = array();
        if(preg_match("/http:\/\/www.56.com\/\S+\/play_album-aid-(\d+)_vid-(.+?).html/i", self::$link_url, $matches)){
            $flv = 'http://player.56.com/v_' . $matches[2] . '.swf';
            $matches[1] = $matches[2];
        }elseif(preg_match("/http:\/\/www.56.com\/\S+\/([^\/]+).html/i", self::$link_url, $matches)){
            $flv = 'http://player.56.com/' . $matches[1] . '.swf';
        }
        if(!empty($matches[1])){
            $api = 'http://vxml.56.com/json/' . str_replace('v_', '', $matches[1]) . '/?src=out';
            $str = file_get_contents($api);
            if(!empty($str) && preg_match("/\"img\":\"(.+?)\"/i", $str, $image)){
                $img = trim($image[1]);
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    // 取得偶偶网视频地址和预览图片
    private function _getVideoInfoByOuou(){
        $videoInfo = array();
        $str = file_get_contents(self::$link_url);
        if(!empty($str) && preg_match("/var\sflv\s=\s'(.+?)';/i", $str, $matches)){
            $flv = $_G['style']['imgdir'] . '/flvplayer.swf?&autostart=true&file=' . urlencode($matches[1]);
            if(preg_match("/var\simga=\s'(.+?)';/i", $str, $image)){
                $img = trim($image[1]);
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    // 取得搜狐网视频地址和预览图片
    private function _getVideoInfoBySohu(){
        $videoInfo = array();
        if(preg_match("/http:\/\/v.blog.sohu.com\/u\/[^\/]+\/(\d+)/i", self::$link_url, $matches)){
            $flv = 'http://v.blog.sohu.com/fo/v4/' . $matches[1];
            $api = 'http://v.blog.sohu.com/videinfo.jhtml?m=view&id=' . $matches[1] . '&outType=3';
            $str = file_get_contents($api);
            if(!empty($str) && preg_match("/\"cutCoverURL\":\"(.+?)\"/i", $str, $image)){
                $img = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    // 取得新浪网视频地址和预览图片
    private function _getVideoInfoBySina(){
        $videoInfo = array();
        if(preg_match("/http:\/\/video.sina.com.cn\/v\/b\/(\d+)-(\d+).html/i", self::$link_url, $matches)){
            $flv = 'http://vhead.blog.sina.com.cn/player/outer_player.swf?vid=' . $matches[1];
            $api = 'http://interface.video.sina.com.cn/interface/common/getVideoImage.php?vid=' . $matches[1];
            $str = file_get_contents($api);
            if(!empty($str)){
                $img = str_replace('imgurl=', '', trim($str));
            }
        }
        $videoInfo['flv'] = isset($flv) ? $flv : '';
        $videoInfo['img'] = isset($img) ? str_replace("\\", "", $img) : '';
        $videoInfo['title'] = isset($title) ? $title : '';
        return $videoInfo;
    }
    
    public function parseflv($url, $width = 0, $height = 0){
        $lowerurl = strtolower($url);
        $flv = '';
        $imgurl = '';
        if($lowerurl != str_replace(array('player.youku.com/player.php/sid/', 'tudou.com/v/', 'player.ku6.com/refer/'), '', $lowerurl)){
            $flv = $url;
        }elseif(strpos($lowerurl, 'www.youtube.com/watch?') !== FALSE){
            if(preg_match("/http:\/\/www.youtube.com\/watch\?v=([^\/&]+)&?/i", $url, $matches)){
                $flv = 'http://www.youtube.com/v/' . $matches[1] . '&hl=zh_CN&fs=1';
                if(!$width && !$height){
                    $str = file_get_contents($url);
                    if(!empty($str) && preg_match("/'VIDEO_HQ_THUMB':\s'(.+?)'/i", $str, $image)){
                        $url = substr($image[1], 0, strrpos($image[1], '/') + 1);
                        $filename = substr($image[1], strrpos($image[1], '/') + 3);
                        $imgurl = $url . $filename;
                    }
                }
            }
        }elseif(strpos($lowerurl, 'tv.mofile.com/') !== FALSE){
            if(preg_match("/http:\/\/tv.mofile.com\/([^\/]+)/i", $url, $matches)){
                $flv = 'http://tv.mofile.com/cn/xplayer.swf?v=' . $matches[1];
                if(!$width && !$height){
                    $str = file_get_contents($url);
                    if(!empty($str) && preg_match("/thumbpath=\"(.+?)\";/i", $str, $image)){
                        $imgurl = trim($image[1]);
                    }
                }
            }
        }elseif(strpos($lowerurl, 'v.mofile.com/show/') !== FALSE){
            if(preg_match("/http:\/\/v.mofile.com\/show\/([^\/]+).shtml/i", $url, $matches)){
                $flv = 'http://tv.mofile.com/cn/xplayer.swf?v=' . $matches[1];
                if(!$width && !$height){
                    $str = file_get_contents($url);
                    if(!empty($str) && preg_match("/thumbpath=\"(.+?)\";/i", $str, $image)){
                        $imgurl = trim($image[1]);
                    }
                }
            }
            /*} elseif(strpos($lowerurl, 'v.youku.com/v_show/') !== FALSE) {
            if(preg_match("/http:\/\/v.youku.com\/v_show\/id_([^\/]+)(.html|)/i", $url, $matches)) {
                $flv = 'http://player.youku.com/player.php/sid/'.$matches[1].'/v.swf';
                if(!$width && !$height) {
                    $api = 'http://v.youku.com/player/getPlayList/VideoIDS/'.$matches[1];
                    $str = stripslashes(file_get_contents($api));
                    if(!empty($str) && preg_match("/\"logo\":\"(.+?)\"/i", $str, $image)) {
                        $url = substr($image[1], 0, strrpos($image[1], '/')+1);
                        $filename = substr($image[1], strrpos($image[1], '/')+2);
                        $imgurl = $url.'0'.$filename;
                    }
                }
            }
        } elseif(strpos($lowerurl, 'tudou.com/programs/view/') !== FALSE) {
            if(preg_match("/http:\/\/(www.)?tudou.com\/programs\/view\/([^\/]+)/i", $url, $matches)) {
                $flv = 'http://www.tudou.com/v/'.$matches[2];
                if(!$width && !$height) {
                    $str = file_get_contents($url);
                    if(!empty($str) && preg_match("/<span class=\"s_pic\">(.+?)<\/span>/i", $str, $image)) {
                        $imgurl = trim($image[1]);
                    }
                }
            }
        } elseif(strpos($lowerurl, 'v.ku6.com/show/') !== FALSE) {
            if(preg_match("/http:\/\/v.ku6.com\/show\/([^\/]+).html/i", $url, $matches)) {
                $flv = 'http://player.ku6.com/refer/'.$matches[1].'/v.swf';
                if(!$width && !$height) {
                    $api = 'http://vo.ku6.com/fetchVideo4Player/1/'.$matches[1].'.html';
                    $str = file_get_contents($api);
                    if(!empty($str) && preg_match("/\"picpath\":\"(.+?)\"/i", $str, $image)) {
                        $imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
                    }
                }
            }
        } elseif(strpos($lowerurl, 'v.ku6.com/special/show_') !== FALSE) {
            if(preg_match("/http:\/\/v.ku6.com\/special\/show_\d+\/([^\/]+).html/i", $url, $matches)) {
                $flv = 'http://player.ku6.com/refer/'.$matches[1].'/v.swf';
                if(!$width && !$height) {
                    $api = 'http://vo.ku6.com/fetchVideo4Player/1/'.$matches[1].'.html';
                    $str = file_get_contents($api);
                    if(!empty($str) && preg_match("/\"picpath\":\"(.+?)\"/i", $str, $image)) {
                        $imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
                    }
                }
            }
        } elseif(strpos($lowerurl, 'you.video.sina.com.cn/b/') !== FALSE) {
            if(preg_match("/http:\/\/you.video.sina.com.cn\/b\/(\d+)-(\d+).html/i", $url, $matches)) {
                $flv = 'http://vhead.blog.sina.com.cn/player/outer_player.swf?vid='.$matches[1];
                if(!$width && !$height) {
                    $api = 'http://interface.video.sina.com.cn/interface/common/getVideoImage.php?vid='.$matches[1];
                    $str = file_get_contents($api);
                    if(!empty($str)) {
                        $imgurl = str_replace('imgurl=', '', trim($str));
                    }
                }
            }
        } elseif(strpos($lowerurl, 'http://v.blog.sohu.com/u/') !== FALSE) {
            if(preg_match("/http:\/\/v.blog.sohu.com\/u\/[^\/]+\/(\d+)/i", $url, $matches)) {
                $flv = 'http://v.blog.sohu.com/fo/v4/'.$matches[1];
                if(!$width && !$height) {
                    $api = 'http://v.blog.sohu.com/videinfo.jhtml?m=view&id='.$matches[1].'&outType=3';
                    $str = file_get_contents($api);
                    if(!empty($str) && preg_match("/\"cutCoverURL\":\"(.+?)\"/i", $str, $image)) {
                        $imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
                    }
                }
            }
        } elseif(strpos($lowerurl, 'http://www.ouou.com/fun_funview') !== FALSE) {
            $str = file_get_contents($url);
            if(!empty($str) && preg_match("/var\sflv\s=\s'(.+?)';/i", $str, $matches)) {
                $flv = $_G['style']['imgdir'].'/flvplayer.swf?&autostart=true&file='.urlencode($matches[1]);
                if(!$width && !$height && preg_match("/var\simga=\s'(.+?)';/i", $str, $image)) {
                    $imgurl = trim($image[1]);
                }
            }
        } elseif(strpos($lowerurl, 'http://www.56.com') !== FALSE) {
    
            if(preg_match("/http:\/\/www.56.com\/\S+\/play_album-aid-(\d+)_vid-(.+?).html/i", $url, $matches)) {
                $flv = 'http://player.56.com/v_'.$matches[2].'.swf';
                $matches[1] = $matches[2];
            } elseif(preg_match("/http:\/\/www.56.com\/\S+\/([^\/]+).html/i", $url, $matches)) {
                $flv = 'http://player.56.com/'.$matches[1].'.swf';
            }
            if(!$width && !$height && !empty($matches[1])) {
                $api = 'http://vxml.56.com/json/'.str_replace('v_', '', $matches[1]).'/?src=out';
                $str = file_get_contents($api);
                if(!empty($str) && preg_match("/\"img\":\"(.+?)\"/i", $str, $image)) {
                    $imgurl = trim($image[1]);
                }
            }*/
        }
        if($flv){
            if(!$width && !$height){
                return array('flv'=>$flv, 'imgurl'=>$imgurl);
            }else{
                $width = addslashes($width);
                $height = addslashes($height);
                $flv = addslashes($flv);
                $randomid = 'flv_' . random(3);
                return '<span id="' . $randomid . '"></span><script type="text/javascript" reload="1">$(\'' . $randomid . '\').innerHTML=AC_FL_RunContent(\'width\', \'' . $width . '\', \'height\', \'' . $height . '\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \'' . $flv . '\', \'quality\', \'high\', \'bgcolor\', \'#ffffff\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
            }
        }else{
            return FALSE;
        }
    }
}
?>