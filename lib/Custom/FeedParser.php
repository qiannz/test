<?php
/**
 * 动态内容文字解析器
 * 解析@谁，解析短域名，解析话题
 *
 */
class Custom_FeedParser {
    private static $gid = 0;
    
    const USER_PREG_STR = "/@([\x{4e00}-\x{9fa5}A-Za-z0-9_•()（）]*)[^\s|^\#|^\/\/|^\[]?/u";
    const TOPIC_PREG_STR = "/\#([^\#|.]+)\#/u";
    const URL_PREG_STR = '/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/'; //"/((http|https|ftp|telnet|news):\/\/)?([a-z0-9_\-\/\.]+\.[][a-zA-Z0-9:;&#@=_~%\?\/\.\,\+\-]+)/";
    

    public static function topicParserCallback($matches){
        if(self::$gid){
            $strString = " <a class=\"org\" href=\"" . gtopicurl(self::$gid, strip_tags($matches[1])) . "\" target=\"_blank\">#{$matches[1]}#</a> ";
        }else{
            $strString = " <a class=\"org\" href=\"" . topicurl(strip_tags($matches[1])) . "\" target=\"_blank\">#{$matches[1]}#</a> ";
        }
        return $strString;
    }
    
    public static function parseSourceApp($source_app, $feed){
        if($source_app['render'] == 'Group'){
            $gid = $source_app['id'];
            $gName = Model_Group_Api::getInstance()->getName($gid);
            if(empty($gName)) $gName = "解散掉的圈子";
            return '<a href="' . groupurl($gid) . '" title="' . $gName . '" class="cor-999"><img src="' . Model_Group_Api::getInstance()->getPicById($gid, 16, 16) . '" width="16" height="16" align="absmiddle" alt="">' . Model_Feed_Logic::utf8Substr($gName, 5, true) . '</a>';
        }
        return '<a href="' . spaceurl($feed->user) . '" class="org">享乐圈</a>';
    }
    
    public static function parseHost($url){
        $pattern1 = '/^(http:\\/\\/)?([^\\/]+)/i';
        preg_match($pattern1, $url, $matches);
        return (isset($matches[2])) ? $matches[2] : "";
    }
    /**
     * weibo parse
     *
     * @author Elaine
     * @param string
     * @return string
     */
    public static function parseWeibo($t, $gid = 0, $hidden_user = 0){
        self::$gid = $gid;
        
        $t = stripslashes($t);
        // link URLs
        /* $t = " ".preg_replace( "/(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]*)".
          "([[:alnum:]#?\/&=])/i", "<a href=\"\\1\\3\\4\" target=\"_blank\">".
          "\\1\\3\\4</a>", $t); */
        $t = preg_replace_callback(self::URL_PREG_STR, 'urlParserCallback', $t);
        //link sina users
        

        if($hidden_user){
            $t = preg_replace_callback(self::USER_PREG_STR, 'userHiddenParserCallback', $t);
        
        }else{
            $t = preg_replace_callback(self::USER_PREG_STR, 'userParserCallback', $t);
        }
        
        //link sina hot topics
        $t = preg_replace_callback(self::TOPIC_PREG_STR, array('self', 'topicParserCallback'), $t);
        
        // truncates long urls that can cause display problems (optional)
        /*  $t = preg_replace("/>(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]".
          "{30,40})([^[:space:]]*)([^[:space:]]{10,20})([[:alnum:]#?\/&=])".
          "</", ">\\3...\\5\\6<", $t); */
        return trim($t);
    }
    
    public static function shortenWeibo($t){
        $t = htmlspecialchars_decode($t);
        
        $t = preg_replace_callback(self::URL_PREG_STR, 'shortenUrlParserCallback', $t);
        return trim($t);
    }
    
    public static function parseAt($t){
        $t = preg_match_all(self::USER_PREG_STR, $t, $matches);
        if(isset($matches[1])) return $matches[1];
        return array();
    }
    
    public static function parseTopic($t){
        $t = preg_match_all(self::TOPIC_PREG_STR, $t, $matches);
        if(isset($matches[1])) return $matches[1];
        return array();
    }
    
    public static function parseUrl($t){
        $t = preg_match_all(self::URL_PREG_STR, $t, $matches);
        if(isset($matches[1])) return $matches[1];
        return array();
    }
    
    public static function createLink($_userName, $uid = 0){
        if($uid > 0){
            return "<a class='namecard' rel=\"" . namecardurl($uid) . "\" href=\"" . spaceurl($uid) . "\" target=\"_blank\">@{$_userName}</a>: ";
        }
        $userName = urlencode($_userName);
        return "<a class='namecard' rel=\"" . namecardurl($uid) . "\" href='" . X_WEB_DOMAIN . "/index/user/?k=" . urlencode($userName) . "' target=\"_blank\">@{$_userName}</a>: ";
    }
    
    public static function insertShortUrl($url){
        $urls = self::createShortUrl($url);
        $mongo = new Mongo($GLOBALS['GLOBAL_CONF']['MongoDB']);
        $db = $mongo->sns_feed;
        $collection = $db->xl_short_url;
        
        $key = "";
        foreach($urls as $_key){
            $cursor = $collection->findOne(array('key'=>$_key));
            
            if($cursor){
                if($cursor['v'] == $url) return $_key;
                else continue;
            }else{
                $key = $_key;
                break;
            }
        }
        
        $setArr = array('key'=>$key, 'v'=>$url, // 'uid' => $uid,
'create_time'=>time());
        
        $collection->insert($setArr);
        $mongo->close();
        return $key;
    }
    
    public static function createShortUrl($url){
        $key = "xiangle";
        $input = "{$key}{$url}";
        $base32 = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5');
        
        $hex = md5($input);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;
        $output = array();
        
        for($i = 0; $i < $subHexLen; $i++){
            $subHex = substr($hex, $i * 8, 8);
            $int = 0x3FFFFFFF & (1 * ('0x' . $subHex));
            $out = '';
            
            for($j = 0; $j < 6; $j++){
                $val = 0x0000001F & $int;
                $out .= $base32[$val];
                $int = $int >> 5;
            }
            
            $output[] = $out;
        }
        
        return $output;
    }
}

function userParserCallback($matches){
    $userName = urlencode($matches[1]);
    return " <a class='org namecard' rel='/namecard/index/get-name-card-user/?k=" . urlencode($userName) . "' href=\"" . X_WEB_DOMAIN . "/index/user/?k=" . urlencode($userName) . "\" target=\"_blank\">@{$matches[1]}</a> ";
}

function userHiddenParserCallback($matches){
    $userName = urlencode($matches[1]);
    return "";
}

function urlParserCallback($matches){
    $url = $matches[1];
    return " <a class=\"org\" href=\"{$url}\" target=\"_blank\">{$url}</a> ";
}

function shortenUrlParserCallback($matches){
    $url = $matches[1];
    $key = Custom_FeedParser::insertShortUrl($url);
    if(strstr($url, X_SHORT_DOMAIN)){
        return X_SHORT_DOMAIN . "/q/?k={$key}";
    }
    return X_SHORT_DOMAIN . "/s/?k={$key}";
}