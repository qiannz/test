<?php

/**
 * 字符串处理函数
 *
 */

class Custom_String {
	/**
	 * 格式化富文本
	 * @param unknown_type $content
	 * @return Ambigous <mixed, string>
	 */
	public static function cleanHtml($content) {
		$content = stripslashes($content);
		include_once ROOT_PATH.'lib/Third/htmlpurifier/library/HTMLPurifier.auto.php';
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', 'utf-8');
		$config->set('HTML.Doctype', 'XHTML 1.0 Strict');
		//$config->set('HTML.TargetBlank', TRUE);
		$def = $config->getHTMLDefinition(true);
		$def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');
		$purifier = new HTMLPurifier($config);
		$clean_html = $purifier->purify(stripcslashes($content));
		return saddslashes($clean_html);
	}	
    /**
     * 截取字符串
     *
     * @param string $string
     * @param int $length
     * @param string $dot
     * @param string $charset
     * @return string
     */
    public static function cutstr($string, $length, $dot = ' ...', $charset = 'UTF-8'){
        if(strlen($string) <= $length){
            return $string;
        }
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
        $strcut = '';
        if(strtolower($charset) == 'utf-8'){
            $n = $tn = $noc = 0;
            while($n < strlen($string)){
                $t = ord($string[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)){
                    $tn = 1;
                    $n++;
                    $noc++;
                }elseif(194 <= $t && $t <= 223){
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                }elseif(224 <= $t && $t <= 239){
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                }elseif(240 <= $t && $t <= 247){
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                }elseif(248 <= $t && $t <= 251){
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                }elseif($t == 252 || $t == 253){
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                }else{
                    $n++;
                }
                if($noc >= $length){
                    break;
                }
            }
            if($noc > $length){
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
        }else{
            for($i = 0; $i < $length; $i++){
                $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            }
        }
        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
        return $strcut . $dot;
    }
    
    /**
     * 遍历处理数组
     *
     * @param mixed $data
     * @param string $function
     * @return mixed
     */
    public static function deepFilterData($data, $function){
        if(!$data || !$function){
            return false;
        }
        if(is_array($data) || is_object($data)){
            foreach($data as $key=>$value){
                $data[$key] = self::deepFilterData($value, $function);
            }
        }else{
            $data = $function($data);
        }
        return $data;
    }
    
    /**
     * 遍历处理数组（可同时MAP多个函数）
     *
     * @param mixed $data
     * @param array $functions array('trim', 'strip_tags')
     * @return mixed
     */
    public static function deepFilterDatas($data, $functions){
        if(!$data || !$functions || !is_array($functions)){
            return false;
        }
        foreach($functions as $function){
            $data = self::deepFilterData($data, $function);
        }
        return $data;
    }
    
    /**
     * 遍历转义处理字符串
     *
     * @param mixed $data
     * @return mixed
     */
    public static function deepFilterDatasInput($data){
        $data = self::deepFilterDatas($data, array('trim', 'strip_tags'));
        $data = self::shtmlspecialchars($data);
        return $data;
    }
    
    /**
     * 取消HTML代码
     *
     * @param string $string
     * @return string
     */
    public static function shtmlspecialchars($string){
        if(is_array($string)){
            foreach($string as $key=>$val){
                $string[$key] = self::shtmlspecialchars($val);
            }
        }else{
            $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
        }
        return $string;
    }
    
    /**
     * 判断字符串是否超出范围
     * 1个中文字按一个字符算。
     *
     * @param string $str   字符串
     * @param int $length   长度
     * @return boolean
     */
    public static function checkStringOverlength($str, $length = 200){
        $tmpLength = self::getStrLen($str, 1);
        if($tmpLength > $length){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * 格式化文件大小
     *
     * @param int $size
     * @return string
     */
    public static function sizecount($size){
        if($size >= 1073741824){
            $size = round($size / 1073741824 * 100) / 100 . ' GB';
        }elseif($size >= 1048576){
            $size = round($size / 1048576 * 100) / 100 . ' MB';
        }elseif($size >= 1024){
            $size = round($size / 1024 * 100) / 100 . ' KB';
        }else{
            $size = $size . ' Bytes';
        }
        return $size;
    }
    
    /**
     * 计算字符串个数
     *
     * @param string $str
     * @return string
     */
    public static function cnstrlen($str){
        return (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
    }
    
    /**
     * 转换为utf-8编码
     *
     * @param string $string
     * @return string
     */
    public static function strToUtf8($string){
        $encode = mb_detect_encoding($string, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
        if($encode != "UTF-8" && !empty($string)){
            $string = iconv($encode, "UTF-8//TRANSLIT//IGNORE", $string);
        }
        return $string;
    }
    
    /**
     * 计算字符串个数
     *
     * @param string $str
     * @param int $type     1=>英文1个字符，中文1个字符
     * 2=>英文1个字符，中文2个字符
     * 3=>英文0.5个字符(出现小数四舍五入)，中文1个字符
     * 4=>英文0.5个字符(出现小数, 去除小数)，中文1个字符
     * @param bool $isRound
     * 
     * @return int
     */
    public static function getStrLen($str, $type = 1, $isRound = true, $len = 0){
        $enNum = 1;
        $cnNum = 1;
        if($type == 2){
            $enNum = 1;
            $cnNum = 2;
        }elseif($type == 3){
            $enNum = 0.5;
            $cnNum = 1;
        }elseif($type == 4){
            $enNum = 0.5;
            $cnNum = 1;
        }
        
        $strLen = 0;
        for($i = 0; $i < strlen($str); $i++){
            if(intval(bin2hex($str[$i]), 16) < 0x80){
                $strLen += $enNum;
            }else{
                $strLen += $cnNum;
                $i += 2;
            }
            if($len && $strLen > $len){
                return true;
            }
        }
        if($isRound){
            if($type == 3){
                $strLen = round($strLen);
            }elseif($type == 4){
                $strLen = floor($strLen);
            }
        
        }
        return $strLen;
    }
    
    /**
     * 截取字符串
     *
     * @param string $str
     * @param int $len
     * @param string $dot
     * @param int $type     1=>英文1个字符，中文1个字符
     * 2=>英文1个字符，中文2个字符
     * 3=>英文0.5个字符(出现小数四舍五入)，中文1个字符
     * 4=>英文0.5个字符(出现小数去除小数)，中文1个字符
     * 
     * @return string
     */
    public static function cutString($str, $len, $dot = '...', $type = 1){
        $enNum = 1;
        $cnNum = 1;
        if($type == 2){
            $enNum = 1;
            $cnNum = 2;
        }elseif($type == 3){
            $enNum = 0.5;
            $cnNum = 1;
        }elseif($type == 4){
            $enNum = 0.5;
            $cnNum = 1;
        }
        
        $isMoreChar = self::getStrLen($str, $type, true, $len);
        if($isMoreChar === true){
            if($dot){
                $len -= self::getStrLen($dot, $type, false);
            }
        }else{
            return $str;
        }
        
        $strLen = 0;
        $cutWord = '';
        for($i = 0; $i < strlen($str); $i++){
            if(intval(bin2hex($str[$i]), 16) < 0x80){
                $cutWord .= $str[$i];
                $strLen += $enNum;
            }else{
                $cutWord .= $str[$i] . $str[$i + 1] . $str[$i + 2];
                $strLen += $cnNum;
                $i += 2;
            }
            
            if($strLen >= $len){
                if($strLen > $len && $type == 4 && intval(bin2hex($str[$i]), 16) >= 0x80){
                    $cutWord = substr($cutWord, 0, strlen($cutWord) - 3);
                }
                $cutWord .= $dot;
                break;
            }
        }
        return $cutWord;
    }
    
    /**
     * 截取字符串（支持html）
     *
     * @param string $string
     * @param int $count
     * @param string $dot
     * @param int $start
     * @param string $tags 以|分开多个html标签
     * @param float $zhfw 用来修正中英字宽参数
     * @param string $charset
     * @return string
     */
    function cutStringForHtml($string, $count = 0, $dot = "...", $start = 0, $tags = "span", $zhfw = 0.9, $charset = "utf-8"){
        //author: lael
        //blog: http://hi.baidu.com/lael80
        

        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        
        $zhre['utf-8'] = "/[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $zhre['gb2312'] = "/[\xb0-\xf7][\xa0-\xfe]/";
        $zhre['gbk'] = "/[\x81-\xfe][\x40-\xfe]/";
        $zhre['big5'] = "/[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        
        //下面代码还可以应用到关键字加亮、加链接等，可以避免截断HTML标签发生
        //得到标签位置
        $tpos = array();
        preg_match_all("/<(" . $tags . ")([\s\S]*?)>|<\/(" . $tags . ")>/ism", $string, $match);
        $mpos = 0;
        for($j = 0; $j < count($match[0]); $j++){
            $mpos = strpos($string, $match[0][$j], $mpos);
            $tpos[$mpos] = $match[0][$j];
            $mpos += strlen($match[0][$j]);
        }
        ksort($tpos);
        
        //根据标签位置解析整个字符
        $sarr = array();
        $bpos = 0;
        $epos = 0;
        foreach($tpos as $k=>$v){
            $temp = substr($string, $bpos, $k - $epos);
            if(!empty($temp)){
                array_push($sarr, $temp);
            }
            array_push($sarr, $v);
            $bpos = ($k + strlen($v));
            $epos = $k + strlen($v);
        }
        $temp = substr($string, $bpos);
        if(!empty($temp)){
            array_push($sarr, $temp);
        }
        
        //忽略标签截取字符串
        $bpos = $start;
        $epos = $count;
        for($i = 0; $i < count($sarr); $i++){
            if(preg_match("/^<([\s\S]*?)>$/i", $sarr[$i])) continue; //忽略标签
            

            preg_match_all($re[$charset], $sarr[$i], $match);
            
            for($j = $bpos; $j < min($epos, count($match[0])); $j++){
                if(preg_match($zhre[$charset], $match[0][$j])) $epos -= $zhfw; //计算中文字符
            }
            
            $sarr[$i] = "";
            for($j = $bpos; $j < min($epos, count($match[0])); $j++){ //截取字符
                $sarr[$i] .= $match[0][$j];
            }
            $bpos -= count($match[0]);
            $bpos = max(0, $bpos);
            $epos -= count($match[0]);
            $epos = round($epos);
        }
        
        //返回结果
        $slice = join("", $sarr); //自己可以加个清除空html标签的东东
        if($slice != $string){
            return $slice . $dot;
        }
        return $slice;
    }
    
    /**
     * 对 MYSQL LIKE 的内容进行转义
     *
     * @param string string
     * @return string
     */
    public function mysqlLikeQuote($str){
        return strtr($str, array("\\\\"=>"\\\\\\\\", '_'=>'\_', '%'=>'\%', "\'"=>"\\\\\'"));
    }
	/**
	 * 
	 * 格式化URL地址
	 * @param $url
	 * @param $replace
	 */
    public static function urlAddressFormat($url, $replace = '%'){
    	if($replace == '%'){
    		return str_replace('%', '_', rawurlencode($url));
    	}elseif($replace == '_'){
    		return rawurldecode(str_replace('_', '%', $url));
    	}
    }
    /**
     * 驼峰形式字符串反转 中划线/下划线形式
     * @param unknown_type $str
     * @param unknown_type $symbol
     */
    public static function uncwords($str, $symbol = '-'){
    	if($str){
    		$strArr = array();
    		$len = strlen($str);
    		for($i=0; $i<$len; $i++){
    			if(preg_match('/[A-Z]/', $str[$i])){
    				$strArr[] = $symbol;
    				$strArr[] = $str[$i];
    			}else{
    				$strArr[] = $str[$i];
    			}
    		}
    		return strtolower(trim(implode('', $strArr), $symbol));
    	}
    	return '';
    }
    
    /**
     * 表情图片标签真实地址
     * @param string $content
     * @return string
     */
    public static function faceToImg($content, $reverse = FALSE){
		$face = array(
			'[:default01:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0001.gif" />',
			'[:default02:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0002.gif" />',
			'[:default03:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0003.gif" />',
			'[:default04:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0004.gif" />',
			'[:default05:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0005.gif" />',
			'[:default06:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0006.gif" />',
			'[:default07:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0007.gif" />',
			'[:default08:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0008.gif" />',
			'[:default09:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0009.gif" />',
			'[:default10:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0010.gif" />',
			'[:default11:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0011.gif" />',
			'[:default12:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0012.gif" />',
			'[:default13:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0013.gif" />',
			'[:default14:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0014.gif" />',
			'[:default15:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0015.gif" />',
			'[:default16:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0016.gif" />',
			'[:default17:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0017.gif" />',
			'[:default18:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0018.gif" />',
			'[:default19:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0019.gif" />',
			'[:default20:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0020.gif" />',
			'[:default21:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0021.gif" />',
			'[:default22:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0022.gif" />',
			'[:default23:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0023.gif" />',
			'[:default24:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0024.gif" />',
			'[:default25:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0025.gif" />',
			'[:default26:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0026.gif" />',
			'[:default27:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0027.gif" />',
			'[:default28:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0028.gif" />',
			'[:default29:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0029.gif" />',
			'[:default30:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0030.gif" />',
			'[:default31:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0031.gif" />',
			'[:default32:]' => '<img src="/js/kindeditor/plugins/emoticons/images/default/m_0032.gif" />',
		
		
			'[:kebai01:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0001.gif" />',
			'[:kebai02:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0002.gif" />',
			'[:kebai03:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0003.gif" />',
			'[:kebai04:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0004.gif" />',
			'[:kebai05:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0005.gif" />',
			'[:kebai06:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0006.gif" />',
			'[:kebai07:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0007.gif" />',
			'[:kebai08:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0008.gif" />',
			'[:kebai09:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0009.gif" />',
			'[:kebai10:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0010.gif" />',
			'[:kebai11:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0011.gif" />',
			'[:kebai12:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0012.gif" />',
			'[:kebai13:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0013.gif" />',
			'[:kebai14:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0014.gif" />',
			'[:kebai15:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0015.gif" />',
			'[:kebai16:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0016.gif" />',
			'[:kebai17:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0017.gif" />',
			'[:kebai18:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0018.gif" />',
			'[:kebai19:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0019.gif" />',
			'[:kebai20:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0020.gif" />',
			'[:kebai21:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0021.gif" />',
			'[:kebai22:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0022.gif" />',
			'[:kebai23:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0023.gif" />',
			'[:kebai24:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0024.gif" />',
			'[:kebai25:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0025.gif" />',
			'[:kebai26:]' => '<img src="/js/kindeditor/plugins/emoticons/images/kebai/QQ_0026.gif" />',
		
			'[:ali01:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0001.gif" />',
			'[:ali02:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0002.gif" />',
			'[:ali03:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0003.gif" />',
			'[:ali04:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0004.gif" />',
			'[:ali05:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0005.gif" />',
			'[:ali06:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0006.gif" />',
			'[:ali07:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0007.gif" />',
			'[:ali08:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0008.gif" />',
			'[:ali09:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0009.gif" />',
			'[:ali10:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0010.gif" />',
			'[:ali11:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0011.gif" />',
			'[:ali12:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0012.gif" />',
			'[:ali13:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0013.gif" />',
			'[:ali14:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0014.gif" />',
			'[:ali15:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0015.gif" />',
			'[:ali16:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0016.gif" />',
			'[:ali17:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0017.gif" />',
			'[:ali18:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0018.gif" />',
			'[:ali19:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0019.gif" />',
			'[:ali20:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0020.gif" />',
			'[:ali21:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0021.gif" />',
			'[:ali22:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0022.gif" />',
			'[:ali23:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0023.gif" />',
			'[:ali24:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0024.gif" />',
			'[:ali25:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0025.gif" />',
			'[:ali26:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0026.gif" />',
			'[:ali27:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0027.gif" />',
			'[:ali28:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0028.gif" />',
			'[:ali29:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0029.gif" />',
			'[:ali30:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0030.gif" />',
			'[:ali31:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0031.gif" />',
			'[:ali32:]' => '<img src="/js/kindeditor/plugins/emoticons/images/ali/al_0032.gif" />',
		
			'[:mogutou01:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0001.gif" />',
			'[:mogutou02:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0002.gif" />',
			'[:mogutou03:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0003.gif" />',
			'[:mogutou04:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0004.gif" />',
			'[:mogutou05:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0005.gif" />',
			'[:mogutou06:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0006.gif" />',
			'[:mogutou07:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0007.gif" />',
			'[:mogutou08:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0008.gif" />',
			'[:mogutou09:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0009.gif" />',
			'[:mogutou10:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0010.gif" />',
			'[:mogutou11:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0011.gif" />',
			'[:mogutou12:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0012.gif" />',
			'[:mogutou13:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0013.gif" />',
			'[:mogutou14:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0014.gif" />',
			'[:mogutou15:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0015.gif" />',
			'[:mogutou16:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0016.gif" />',
			'[:mogutou17:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0017.gif" />',
			'[:mogutou18:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0018.gif" />',
			'[:mogutou19:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0019.gif" />',
			'[:mogutou20:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0020.gif" />',
			'[:mogutou21:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0021.gif" />',
			'[:mogutou22:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0022.gif" />',
			'[:mogutou23:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0023.gif" />',
			'[:mogutou24:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0024.gif" />',
			'[:mogutou25:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0025.gif" />',
			'[:mogutou26:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0026.gif" />',
			'[:mogutou27:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0027.gif" />',
			'[:mogutou28:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0028.gif" />',
			'[:mogutou29:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0029.gif" />',
			'[:mogutou30:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0030.gif" />',
			'[:mogutou31:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0031.gif" />',
			'[:mogutou32:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mogu/mg_0032.gif" />',
		
			'[:mp01:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0001.jpg" />',
			'[:mp02:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0002.jpg" />',
			'[:mp03:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0003.jpg" />',
			'[:mp04:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0004.jpg" />',
			'[:mp05:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0005.jpg" />',
			'[:mp06:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0006.jpg" />',
			'[:mp07:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0007.jpg" />',
			'[:mp08:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0008.jpg" />',
			'[:mp09:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0009.jpg" />',
			'[:mp10:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0010.jpg" />',
			'[:mp11:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0011.jpg" />',
			'[:mp12:]' => '<img src="/js/kindeditor/plugins/emoticons/images/mpzhuanshu/mp_0012.jpg" />'
		);
		$face_key = array_keys($face);
		$face_value = array_values($face);
		if($reverse){
			return str_replace($face_value, $face_key, $content);
		}else{
			return str_replace($face_key, $face_value, $content);  
		}	
    }
    /**
     * 表情图片真实地址转为标签
     * @param string $content
     * @return string
     */
    function imgToFace($content){
    	$face = array(
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0001.gif\"(.*)? \/>/iU' => '[:default01:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0002.gif\"(.*)? \/>/iU' => '[:default02:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0003.gif\"(.*)? \/>/iU' => '[:default03:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0004.gif\"(.*)? \/>/iU' => '[:default04:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0005.gif\"(.*)? \/>/iU' => '[:default05:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0006.gif\"(.*)? \/>/iU' => '[:default06:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0007.gif\"(.*)? \/>/iU' => '[:default07:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0008.gif\"(.*)? \/>/iU' => '[:default08:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0009.gif\"(.*)? \/>/iU' => '[:default09:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0010.gif\"(.*)? \/>/iU' => '[:default10:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0011.gif\"(.*)? \/>/iU' => '[:default11:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0012.gif\"(.*)? \/>/iU' => '[:default12:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0013.gif\"(.*)? \/>/iU' => '[:default13:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0014.gif\"(.*)? \/>/iU' => '[:default14:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0015.gif\"(.*)? \/>/iU' => '[:default15:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0016.gif\"(.*)? \/>/iU' => '[:default16:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0017.gif\"(.*)? \/>/iU' => '[:default17:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0018.gif\"(.*)? \/>/iU' => '[:default18:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0019.gif\"(.*)? \/>/iU' => '[:default19:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0020.gif\"(.*)? \/>/iU' => '[:default20:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0021.gif\"(.*)? \/>/iU' => '[:default21:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0022.gif\"(.*)? \/>/iU' => '[:default22:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0023.gif\"(.*)? \/>/iU' => '[:default23:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0024.gif\"(.*)? \/>/iU' => '[:default24:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0025.gif\"(.*)? \/>/iU' => '[:default25:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0026.gif\"(.*)? \/>/iU' => '[:default26:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0027.gif\"(.*)? \/>/iU' => '[:default27:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0028.gif\"(.*)? \/>/iU' => '[:default28:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0029.gif\"(.*)? \/>/iU' => '[:default29:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0030.gif\"(.*)? \/>/iU' => '[:default30:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0031.gif\"(.*)? \/>/iU' => '[:default31:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/default\/m_0032.gif\"(.*)? \/>/iU' => '[:default32:]',
    	
    	
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0001.gif\"(.*)? \/>/iU' => '[:kebai01:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0002.gif\"(.*)? \/>/iU' => '[:kebai02:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0003.gif\"(.*)? \/>/iU' => '[:kebai03:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0004.gif\"(.*)? \/>/iU' => '[:kebai04:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0005.gif\"(.*)? \/>/iU' => '[:kebai05:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0006.gif\"(.*)? \/>/iU' => '[:kebai06:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0007.gif\"(.*)? \/>/iU' => '[:kebai07:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0008.gif\"(.*)? \/>/iU' => '[:kebai08:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0009.gif\"(.*)? \/>/iU' => '[:kebai09:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0010.gif\"(.*)? \/>/iU' => '[:kebai10:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0011.gif\"(.*)? \/>/iU' => '[:kebai11:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0012.gif\"(.*)? \/>/iU' => '[:kebai12:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0013.gif\"(.*)? \/>/iU' => '[:kebai13:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0014.gif\"(.*)? \/>/iU' => '[:kebai14:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0015.gif\"(.*)? \/>/iU' => '[:kebai15:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0016.gif\"(.*)? \/>/iU' => '[:kebai16:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0017.gif\"(.*)? \/>/iU' => '[:kebai17:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0018.gif\"(.*)? \/>/iU' => '[:kebai18:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0019.gif\"(.*)? \/>/iU' => '[:kebai19:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0020.gif\"(.*)? \/>/iU' => '[:kebai20:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0021.gif\"(.*)? \/>/iU' => '[:kebai21:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0022.gif\"(.*)? \/>/iU' => '[:kebai22:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0023.gif\"(.*)? \/>/iU' => '[:kebai23:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0024.gif\"(.*)? \/>/iU' => '[:kebai24:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0025.gif\"(.*)? \/>/iU' => '[:kebai25:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/kebai\/QQ_0026.gif\"(.*)? \/>/iU' => '[:kebai26:]',
    	
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0001.gif\"(.*)? \/>/iU' => '[:ali01:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0002.gif\"(.*)? \/>/iU' => '[:ali02:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0003.gif\"(.*)? \/>/iU' => '[:ali03:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0004.gif\"(.*)? \/>/iU' => '[:ali04:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0005.gif\"(.*)? \/>/iU' => '[:ali05:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0006.gif\"(.*)? \/>/iU' => '[:ali06:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0007.gif\"(.*)? \/>/iU' => '[:ali07:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0008.gif\"(.*)? \/>/iU' => '[:ali08:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0009.gif\"(.*)? \/>/iU' => '[:ali09:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0010.gif\"(.*)? \/>/iU' => '[:ali10:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0011.gif\"(.*)? \/>/iU' => '[:ali11:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0012.gif\"(.*)? \/>/iU' => '[:ali12:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0013.gif\"(.*)? \/>/iU' => '[:ali13:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0014.gif\"(.*)? \/>/iU' => '[:ali14:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0015.gif\"(.*)? \/>/iU' => '[:ali15:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0016.gif\"(.*)? \/>/iU' => '[:ali16:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0017.gif\"(.*)? \/>/iU' => '[:ali17:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0018.gif\"(.*)? \/>/iU' => '[:ali18:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0019.gif\"(.*)? \/>/iU' => '[:ali19:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0020.gif\"(.*)? \/>/iU' => '[:ali20:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0021.gif\"(.*)? \/>/iU' => '[:ali21:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0022.gif\"(.*)? \/>/iU' => '[:ali22:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0023.gif\"(.*)? \/>/iU' => '[:ali23:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0024.gif\"(.*)? \/>/iU' => '[:ali24:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0025.gif\"(.*)? \/>/iU' => '[:ali25:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0026.gif\"(.*)? \/>/iU' => '[:ali26:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0027.gif\"(.*)? \/>/iU' => '[:ali27:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0028.gif\"(.*)? \/>/iU' => '[:ali28:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0029.gif\"(.*)? \/>/iU' => '[:ali29:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0030.gif\"(.*)? \/>/iU' => '[:ali30:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0031.gif\"(.*)? \/>/iU' => '[:ali31:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/ali\/al_0032.gif\"(.*)? \/>/iU' => '[:ali32:]',
    	
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0001.gif\"(.*)? \/>/iU' => '[:mogutou01:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0002.gif\"(.*)? \/>/iU' => '[:mogutou02:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0003.gif\"(.*)? \/>/iU' => '[:mogutou03:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0004.gif\"(.*)? \/>/iU' => '[:mogutou04:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0005.gif\"(.*)? \/>/iU' => '[:mogutou05:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0006.gif\"(.*)? \/>/iU' => '[:mogutou06:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0007.gif\"(.*)? \/>/iU' => '[:mogutou07:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0008.gif\"(.*)? \/>/iU' => '[:mogutou08:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0009.gif\"(.*)? \/>/iU' => '[:mogutou09:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0010.gif\"(.*)? \/>/iU' => '[:mogutou10:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0011.gif\"(.*)? \/>/iU' => '[:mogutou11:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0012.gif\"(.*)? \/>/iU' => '[:mogutou12:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0013.gif\"(.*)? \/>/iU' => '[:mogutou13:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0014.gif\"(.*)? \/>/iU' => '[:mogutou14:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0015.gif\"(.*)? \/>/iU' => '[:mogutou15:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0016.gif\"(.*)? \/>/iU' => '[:mogutou16:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0017.gif\"(.*)? \/>/iU' => '[:mogutou17:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0018.gif\"(.*)? \/>/iU' => '[:mogutou18:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0019.gif\"(.*)? \/>/iU' => '[:mogutou19:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0020.gif\"(.*)? \/>/iU' => '[:mogutou20:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0021.gif\"(.*)? \/>/iU' => '[:mogutou21:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0022.gif\"(.*)? \/>/iU' => '[:mogutou22:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0023.gif\"(.*)? \/>/iU' => '[:mogutou23:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0024.gif\"(.*)? \/>/iU' => '[:mogutou24:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0025.gif\"(.*)? \/>/iU' => '[:mogutou25:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0026.gif\"(.*)? \/>/iU' => '[:mogutou26:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0027.gif\"(.*)? \/>/iU' => '[:mogutou27:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0028.gif\"(.*)? \/>/iU' => '[:mogutou28:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0029.gif\"(.*)? \/>/iU' => '[:mogutou29:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0030.gif\"(.*)? \/>/iU' => '[:mogutou30:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0031.gif\"(.*)? \/>/iU' => '[:mogutou31:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mogu\/mg_0032.gif\"(.*)? \/>/iU' => '[:mogutou32:]',
    	
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0001.jpg\"(.*)? \/>/iU' => '[:mp01:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0002.jpg\"(.*)? \/>/iU' => '[:mp02:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0003.jpg\"(.*)? \/>/iU' => '[:mp03:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0004.jpg\"(.*)? \/>/iU' => '[:mp04:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0005.jpg\"(.*)? \/>/iU' => '[:mp05:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0006.jpg\"(.*)? \/>/iU' => '[:mp06:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0007.jpg\"(.*)? \/>/iU' => '[:mp07:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0008.jpg\"(.*)? \/>/iU' => '[:mp08:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0009.jpg\"(.*)? \/>/iU' => '[:mp09:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0010.jpg\"(.*)? \/>/iU' => '[:mp10:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0011.jpg\"(.*)? \/>/iU' => '[:mp11:]',
    			'/<img (.*)?src=\"(http:\/\/([a-z]+)\.([a-z]+)\.([a-z]+))?\/js\/kindeditor\/plugins\/emoticons\/images\/mpzhuanshu\/mp_0012.jpg\"(.*)? \/>/iU' => '[:mp12:]'
    	);
    	$face_key = array_keys($face);
    	$face_value = array_values($face);
    
    	return preg_replace($face_key, $face_value, $content);
    }
    
    /**
     *  去除html中不规则内容字符
    
     *
     * @access    public
     * @param     string  $str  需要处理的字符串
     * @param     string  $rptype  返回类型
     *            $rptype = 0 表示仅替换 html标记
     *            $rptype = 1 表示替换 html标记同时去除连续空白字符
     *            $rptype = 2 表示替换 html标记同时去除所有空白字符
     *            $rptype = 3 表示仅替换 html危险的标记的同时保留换行效果
     *            $rptype = -1 表示仅替换 html危险的标记
     * @return    string
     */
    
    public static function HtmlReplace($str,$rptype=0)
    {
    	$str = stripslashes($str);
    	$str = preg_replace("/<[\/]{0,1}style([^>]*)>(.*)<\/style>/i", '', $str);
    	if($rptype==0)
    	{
    		$str = htmlspecialchars($str);
    	}
    	else if($rptype==1)
    	{
    		$str = htmlspecialchars($str);
    		$str = str_replace("　", ' ', $str);
    		$str = preg_replace("/[\r\n\t ]{1,}/", ' ', $str);
    	}
    	else if($rptype==2)
    	{
    		$str = htmlspecialchars($str);
    		$str = str_replace("　", '', $str);
    		$str = preg_replace("/[\r\n\t ]/", '', $str);
    	} 
    	else if($rptype==3) {
    		$str = preg_replace("/[\t ]{1,}/", ' ', $str);
    		$str = preg_replace('/script/i', 'ｓｃｒｉｐｔ', $str);
    		$str = preg_replace("/<[\/]{0,1}(link|meta|ifr|fra)[^>]*>/i", '', $str);
    	}
    	else
    	{
    		$str = preg_replace("/[\r\n\t ]{1,}/", ' ', $str);
    		$str = preg_replace('/script/i', 'ｓｃｒｉｐｔ', $str);
    		$str = preg_replace("/<[\/]{0,1}(link|meta|ifr|fra)[^>]*>/i", '', $str);
    	}
    	return addslashes($str);
    }
    
    /**
     *  修复浏览器XSS hack的函数
     *
     * @param     string   $val  需要处理的内容
     * @return    string
     */
    
    public static function RemoveXSS($val) {
    	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    	$search = 'abcdefghijklmnopqrstuvwxyz';
    	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$search .= '1234567890!@#$%^&*()';
    	$search .= '~`";:?+/={}[]-_|\'\\';
    	for ($i = 0; $i < strlen($search); $i++) {
    		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
    		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    	}
    
    	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    	$ra = array_merge($ra1, $ra2);
    
    	$found = true;
    	while ($found == true) {
    		$val_before = $val;
    		for ($i = 0; $i < sizeof($ra); $i++) {
    			$pattern = '/';
    			for ($j = 0; $j < strlen($ra[$i]); $j++) {
    				if ($j > 0) {
    					$pattern .= '(';
    					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
    					$pattern .= '|';
    					$pattern .= '|(&#0{0,8}([9|10|13]);)';
    					$pattern .= ')*';
    				}
    				$pattern .= $ra[$i][$j];
    			}
    			$pattern .= '/i';
    			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
    			$val = preg_replace($pattern, $replacement, $val);
    			if ($val_before == $val) {
    				$found = false;
    			}
    		}
    	}
    	return $val;
    }
    
    
    /**
     *  处理禁用HTML但允许换行的内容
     *
     * @access    public
     * @param     string  $msg  需要过滤的内容
     * @return    string
     */
    
    public static function TrimMsg($msg)
    {
    	$msg = trim(stripslashes($msg));
    	$msg = nl2br(htmlspecialchars($msg));
    	$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
    	return addslashes($msg);
    }
    
    
    /**
     *  过滤用于搜索的字符串
     *
     * @param     string  $keyword  关键词
     * @return    string
     */
    public static function FilterSearch($keyword, $lang = 'utf-8')
    {
    	if($lang=='utf-8')
    	{
    		$keyword = preg_replace("/[\"\r\n\t\$\\><']/", '', $keyword);
    		if($keyword != stripslashes($keyword))
    		{
    			return '';
    		}
    		else
    		{
    			return $keyword;
    		}
    	}
    	else
    	{
    		$restr = '';
    		for($i=0;isset($keyword[$i]);$i++)
    		{
    			if(ord($keyword[$i]) > 0x80)
    			{
    				if(isset($keyword[$i+1]) && ord($keyword[$i+1]) > 0x40)
    				{
    					$restr .= $keyword[$i].$keyword[$i+1];
    					$i++;
    				}
    				else
    				{
    					$restr .= ' ';
    				}
    			}
    			else
    			{
    				if(preg_match("/[^0-9a-z@#\.]/",$keyword[$i]))
    				{
    					$restr .= ' ';
    				}
    				else
    				{
    					$restr .= $keyword[$i];
    				}
    			}
    		}
    	}
    	return $restr;
    }    
}