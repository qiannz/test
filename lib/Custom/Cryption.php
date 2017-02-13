<?php

/**
 * 公用加密、解密类
 *
 */

class Custom_Cryption {
    /**
     * 私钥
     */
    protected static $_authkey = 'a6e7allw265eb72c92a549dd5a3301tf';
    /**
     * 字符表
     */
    public static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    
    /**
     * 加密、解密函数 (UCenter 1.5.2)
     *
     * @param string    $string     连接后的字符串
     * @param string    $operation  ENCODE|DECODE 加密或解密操作
     * @param string    $key        密钥
     * @param int       $expiry     过期时间
     * @return string
     */
    public static function authCode($string, $operation = 'DECODE', $key = '', $expiry = 0){
        $ckey_length = 4; // 随机密钥长度 取值 0-32;
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥
        

        $key = md5($key ? $key : self::$_authkey);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        
        $result = '';
        $box = range(0, 255);
        
        $rndkey = array();
        for($i = 0; $i <= 255; $i++){
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        
        for($j = $i = 0; $i < 256; $i++){
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        
        for($a = $j = $i = 0; $i < $string_length; $i++){
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        if($operation == 'DECODE'){
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)){
                return substr($result, 26);
            }else{
                return '';
            }
        }else{
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
       
    public static function short($url) {
    	$key = "alexis";
    	$urlhash = md5($key . $url);
    	$len = strlen($urlhash);
    
    	#将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
    	for ($i = 0; $i < 4; $i++) {
    		$urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);
    		#将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
    		$hex = hexdec($urlhash_piece) & 0x3fffffff; #此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
    
    		$short_url = "http://u.mplife.com/";
    		#生成6位短连接
    		for ($j = 0; $j < 6; $j++) {
    			#将得到的值与0x0000003d,3d为61，即charset的坐标最大值
    			$short_url .= self::$charset[$hex & 0x0000003d];
    			#循环完以后将hex右移5位
    			$hex = $hex >> 5;
    		}
    
    		$short_url_list[] = $short_url;
    	}
    
    	return $short_url_list;
    }    
}