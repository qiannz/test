<?php
$referer=empty($_SERVER['HTTP_REFERER']) ? array() : array($_SERVER['HTTP_REFERER']);

$getfilter="'|\b(alert|confirm|prompt)\b|<[^>]*?>|^\\+\/v(8|9)|\\b(and|or)\\b.+?(>|<|=|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$postfilter="^\\+\/v(8|9)|\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$cookiefilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq) {
	$StrFiltValue=arr_foreach($StrFiltValue);
	if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue) == 1 ) {
		$log_content = "<br><br>操作IP: ".Custom_Client::getUserIp() . $_SERVER['REQUEST_URI'] . "<br>操作时间: ".strftime("%Y-%m-%d %H:%M:%S")."<br>操作页面:".$_SERVER["PHP_SELF"]."<br>提交方式: ".$_SERVER["REQUEST_METHOD"]."<br>提交参数: ".$StrFiltKey."<br>提交数据: ".$StrFiltValue;
		logLog('value.log', $log_content);
		Custom_Common::showMsg('你的输入非法', 'back');
	}
	if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey) == 1) {
		$log_content = "<br><br>操作IP: ".Custom_Client::getUserIp() . $_SERVER['REQUEST_URI'] . "<br>操作时间: ".strftime("%Y-%m-%d %H:%M:%S")."<br>操作页面:".$_SERVER["PHP_SELF"]."<br>提交方式: ".$_SERVER["REQUEST_METHOD"]."<br>提交参数: ".$StrFiltKey."<br>提交数据: ".$StrFiltValue;
		logLog('key.log', $log_content);
		Custom_Common::showMsg('你的输入非法', 'back');
	}  
}
if(!empty($_GET)) {
	foreach($_GET as $key=>$value) { 
		StopAttack($key,$value,$getfilter);
	}
}
if(!empty($_POST)) {
	foreach($_POST as $key=>$value) { 
		StopAttack($key,$value,$postfilter);
	}
}
if(!empty($_COOKIE)) {
	foreach($_COOKIE as $key=>$value) { 
		StopAttack($key,$value,$cookiefilter);
	}
}
if(!empty($referer)) {
	foreach($referer as $key=>$value) { 
		StopAttack($key,$value,$getfilter);
	}
}

function arr_foreach($arr) {
	static $str;
	if (!is_array($arr)) {
		return $arr;
	}
	foreach ($arr as $key => $val ) {
		if (is_array($val)) {
			arr_foreach($val);
		} else {
		  $str[] = $val;
		}
	}
	return implode($str);
}