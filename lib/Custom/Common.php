<?php

/**
 * 常用函数扩展库
 *
 */

class Custom_Common {
    /**
     * 产生随机字符
     *
     * @param int $length
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    public static function random($length, $numeric = true){
        if($numeric){
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        }else{
            $hash = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++){
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }
    
    /**
     * 用 PHP 模拟 JS 弹窗
     *
     * @param string $s       提示信息
     * @param string $url     跳转地址
     * @param string $topdiv
     */
    public static function alert($s, $url = '', $topdiv = 'topdiv'){
        $url = $url ? $url : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        $location = "window.location.href = '$url'";
        header('Content-Type: text/html;charset=UTF-8');
        exit("<script type=\"text/javascript\">if(parent.document.getElementById('{$topdiv}')!=null) { parent.document.getElementById('{$topdiv}').scrollIntoView();} alert('{$s}'); {$location} </script>");
    }
    
    /**
     * 用 PHP 模拟 JS 的 Confirm 框
     *
     * @param string $s     提示信息
     * @param string $url1  确定-跳转地址
     * @param string $url2  取消-跳转地址
     */
    public static function confirm($s, $url1 = '', $url2 = ''){
        $location1 = $url1 ? "window.location.href = '{$url1}'" : 'window.history.back();';
        $location2 = $url2 ? "window.location.href = '{$url2}'" : 'window.history.back();';
        $str = "<script type=\"text/javascript\">
            if (confirm('{$s}')) {
                $location1
            } else {
                $location2
            }";
        $str .= '</script>';
        header('Content-Type: text/html;charset=UTF-8');
        exit($str);
    }
    
    /**
     * 页面跳转
     *
     * @param string $url
     * @param int $type
     */
    public static function jumpto($url, $type = 1){
        if($type == 1){
            header('Location: ' . $url);
            exit();
        }elseif($type == 2){
            exit('<meta http-equiv="refresh" content="0;URL=' . $url . '">');
        }elseif($type == 3){
            $str = "<script type=\"text/javascript\">window.location.href = '$url';</script>";
            exit($str);
        }
    }
    
    /**
     * 错误提示页面
     *
     * @param string $message 提示信息
     * @param string $redirect 自动跳转页面
     * @param string $err_file 出错文件名
     * @param string $err_line 文件位置
     * @param string $links 手动跳转页面
     */
    public static function showMsg($message, $redirect = null, $links = null, $err_file = null, $err_line = null){
        $module = strtolower(Core_Router::getModule());
        $controller = strtolower(Core_Router::getController());   
    	if($redirect == 'back'){
    		if($module == $GLOBALS['GLOBAL_CONF']['Default_Manager_Module_Path']){
    			$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin';
    		}else{
        		$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    		}
        }
        
        if(!empty($links) && is_array($links)){
        	$i = 0;
        	$linkArr = array();
        	while(list($action, $text) = each($links)){
        		if($action{0} == '/' || strpos($action, 'http') !== false) {
        			$linkArr[$i]['href'] = $action;
        		} else {
        			$linkArr[$i]['href'] = "/{$module}/{$controller}/{$action}";
        		}
        		$linkArr[$i]['text'] = $text; 
        		$i++;
        	}
        	$links = $linkArr;
        	unset($linkArr);
        }
        //$errorObject = Controller_Error_Msg::getInstance();
        //$errorObject->showMsgAction($message, $redirect, $links, $err_file, $err_line);
        $errorObject = new Base();
        $errorObject->Err404($module, $message, $redirect, $links);
    }
    
    public static function showMessage($msg, $gourl, $jumpurl = null, $onlymsg = 0, $limittime = 0)
    {
        $htmlhead = "<html><head><title>系统提示信息</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
        $htmlhead .= "<base target='_self'/><style>div{line-height:160%;}</style></head><body leftmargin='0' topmargin='0'><center><script>";
        $htmlfoot = "</script></center></body></html>";

        if ($limittime == 0) {
            $litime = 1000;
        } else {
            $litime = $limittime;
        }

        if ($gourl == "-1") {
            if ($limittime == 0) {
                $litime = 5000;
            }
            $gourl = "javascript:history.go(-1);";
        }

        if ($gourl == '' || $onlymsg == 1) {
            $msg = "<script>alert(\"" . str_replace ( "\"", "“", $msg ) . "\");</script>";
        } 
        else 
        {
            $func = "      
                    var pgo=0;
                    function JumpUrl(){
                            if(pgo==0){ 
                                    window.location='$gourl'; pgo=1; 
                            }
                    }";

            if(!is_null($jumpurl))
            {
                    $func2 = "
                            function Jump(){
                                    parent.window.location='$jumpurl';
                            }";
                    $rmsg = $func2;
            }else{
                    $rmsg = $func;
            }
            $rmsg .= "document.write(\"<br /><center><div style='width:500px;padding:0px;border:1px solid #D1DDAA;' align='center'>";
            $rmsg .= "<div style='padding:6px;font-size:12px;border-bottom:1px solid #D1DDAA;background:none repeat scroll 0 0 #F0F7FF'><b>系统提示信息！</b></div>\");";
            $rmsg .= "document.write(\"<div style='height:130px;font-size:10pt;background:#ffffff'><br />\");";
            $rmsg .= "document.write(\"" . str_replace ( "\"", "“", $msg ) . "\");";
            $rmsg .= "document.write(\"";
            if ($onlymsg == 0) 
            {
                if ($gourl != "javascript:;" && $gourl != "") {
                    $rmsg .= "<br /><a href='{$gourl}'>如果你的浏览器没反应，请点击这里...</a>";
                }
                $rmsg .= "<br/></div>\");";
                if ($gourl != "javascript:;" && $gourl != '') 
                {
                    $rmsg .= "setTimeout('JumpUrl()',$litime);";
                }
            } 
            else 
            {
                $rmsg .= "<br/><br/></div></center>\");";
            }
                $msg = $htmlhead . $rmsg . $htmlfoot;
        }
        echo $msg;
    }    
    /**
     * 校验验证码
     *
     * @param string $vcode
     * @param string $space 命名空间
     */
    public static function checkCode($vcode, $space = NULL){
        $vcode = trim($vcode);
        if(empty($vcode)){
            return false;
        }
        $captchaObj = new Custom_Captcha($space);
        return $captchaObj->checkCode($vcode);
    }
    
    /**
     * 调取 dailog js 方法
     *
     * @param string $msg
     * @param string $title
     * @param string $callback
     * @param string $cancel
     */
    public static function dailog($msg, $title = '', $callback = '', $cancel = ''){
        exit('<script type="text/javascript">dailog(\'' . $msg . '\',\'' . $title . '\',\'' . $callback . '\',\'' . $cancel . '\');</script>');
    }
    
    /**
     * 调用 weebox alert
     *
     * @param string $msg
     * @param string $type success|error|warning
     * @param string $title
     * @param string $url
     */
    public static function walert($msg, $type, $title = '提示信息', $url = '', $width = 400){
        exit("<script type=\"text/javascript\">parent.walert('{$msg}', '{$type}', '{$title}', '{$url}','{$width}');</script>");
    }
    
    // json 输出
    public static function sendjson($code, $message) {
        exit(json_encode(array('Code' => $code, 'Message' => $message)));
    }
}