<?php

/**
 * 错误处理
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return void
 */
function errorHandler($errno, $errstr, $errfile, $errline){
    $types = array(E_ERROR=>'Error', E_WARNING=>'Warning', E_PARSE=>'Parsing Error', E_NOTICE=>'Notice', E_CORE_ERROR=>'Core Error', E_CORE_WARNING=>'Core Warning', E_COMPILE_ERROR=>'Compile Error', E_COMPILE_WARNING=>'Compile Warning', E_USER_ERROR=>'User Error', E_USER_WARNING=>'User Warning', E_USER_NOTICE=>'User Notice', E_STRICT=>'Runtime Notice');
    $type = isset($types[$errno]) ? $types[$errno] : 'Unknow';
    $str = "[" . date('Y-m-d H:i:s') . "] $type:[$errno] $errstr in line $errline of file $errfile " . (isset($_SERVER['REQUEST_URI']) ? 'referer by ' . $_SERVER['REQUEST_URI'] : '') . "\n\n";
    if(isDebug() && $errno != E_NOTICE){ #debug
        echo $str;
    }
    if($errno != E_NOTICE){ #debug
        @file_put_contents(LOG_PATH . 'php_error.log', $str, FILE_APPEND);
    }
    if($errno == E_USER_ERROR){
        exit('E_USER_ERROR');
    }
}

/**
 * 显示调试信息
 */
function showRuntime(){
    // 如果是火狐则把调试信息打印在 FirePHP 控制台，否则直接 echo 在页面底部
    $firefox = (strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') !== false);
    
    $content = '<div style="text-align:left;color:#f60; background:#ffffe7; border:#ffcf87 1px solid; padding:8px 15px;width:1000px;margin:20px auto;overflow-x:auto;font-size:12px;">';
    $content .= '<pre>';
    
    if(isset($GLOBALS['__runmsg']) && !empty($GLOBALS['__runmsg'])){
        foreach($GLOBALS['__runmsg'] as $v){
            $content .= ($v['slow'] ? '<font color="blue">' : '') . "[" . sprintf('%.4f', $v['time']) . "] " . str_pad($v['host'], 15) . str_pad($v['dbname'], 22) . $v['sql'] . ($v['slow'] ? '</font>' : '') . '<br />';
        }
        
        if($firefox){
            $table = array();
            $table[] = array('Time', 'Host', 'Database', 'SQL');
            foreach($GLOBALS['__runmsg'] as $v){
                $table[] = array($v['time'], $v['host'], $v['dbname'], $v['sql']);
            }
            FB::table('SQL Info', $table);
        }
    }
    
    $table = array();
    $table[] = array('Debug Info');
    $content .= 'Router: ' . Core_Router::getModule() . '/' . Core_Router::getController() . '/' . Core_Router::getAction() . '<br />';
    $content .= 'Total Time: ' . round(microtime(true) - $GLOBALS['__starttime'], 3) . '<br />';
    $content .= 'Total Memory: ' . Custom_String::sizecount(round(memory_get_usage() - $GLOBALS['__memoryuse'], 3)) . '<br />';
    $content .= 'Server IP: ' . $_SERVER['SERVER_ADDR'] . ' &nbsp;|&nbsp; ' . $_SERVER['HTTP_HOST'] . '<br />';
    $table[] = array('Router: ' . Core_Router::getModule() . '/' . Core_Router::getController() . '/' . Core_Router::getAction());
    $table[] = array('Total Time: ' . round(microtime(true) - $GLOBALS['__starttime'], 3));
    $table[] = array('Total Memory: ' . Custom_String::sizecount(round(memory_get_usage() - $GLOBALS['__memoryuse'], 3)));
    $table[] = array('Server IP: ' . $_SERVER['SERVER_ADDR'] . ' | ' . $_SERVER['HTTP_HOST']);
    if(isset($GLOBALS['LOADED_MODELS_LOCAL']) && $GLOBALS['LOADED_MODELS_LOCAL']){
        $local = $local1 = 'Loaded Model (Local): ';
        foreach($GLOBALS['LOADED_MODELS_LOCAL'] as $class=>$times){
            $local .= $class . '(' . $times . ')';
            $local1 .= $class . '(' . $times . '), ';
        }
        $content .= $local1 . '<br />';
        $table[] = array($local);
    }
    if(isset($GLOBALS['LOADED_MODELS_REMOTE']) && $GLOBALS['LOADED_MODELS_REMOTE']){
        $remote = $remote1 = 'Loaded Model (Remote): ';
        foreach($GLOBALS['LOADED_MODELS_REMOTE'] as $class=>$times){
            $remote .= $class . '(' . $times . ')';
            $remote1 .= $class . '(' . $times . '), ';
        }
        $content .= $remote1 . '<br />';
        $table[] = array($remote);
    }
    $content .= '</pre>';
    $content .= '</div>';
    
    if($firefox){
        FB::table('Debug Info', $table);
    }else{
        echo $content;
    }
}

/**
 * 记录调试信息
 *
 * @param string $msg
 * @param int $limit
 * @return void
 */
function setRuntime($msg = '', $limit = -1){
    $runtime = round(microtime(true) - $GLOBALS['__runtime'], 3);
    if($runtime > $limit){
        $GLOBALS['__runmsg'][] = '[' . $runtime . '] ' . $msg . '<br>';
    }
    $GLOBALS['__runtime'] = microtime(true);
}

function vd($s, $exit = 1){
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $exit && exit();
}

function pr($s, $exit = 1){
    echo '<pre>';
    print_r($s);
    echo '</pre>';
    $exit && exit();
}

function showDebugDetail(){
    // 如果是火狐则把调试信息打印在 FirePHP 控制台，否则直接 echo 在页面底部
    $firefox = (strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') !== false);
    
    $content = <<<EOF
    <style>
    .tclass, .tclass2 {
    text-align:left;width:100%;border:0;border-collapse:collapse;margin-bottom:5px;table-layout: fixed; word-wrap: break-word;background:#FFF; padding:8px 15px;}
    .tclass table, .tclass2 table {width:100%;border:0;table-layout: fixed; word-wrap: break-word;}
    .tclass table td, .tclass2 table td {border-bottom:0;border-right:0;border-color: #ADADAD;}
    .tclass th, .tclass2 th {border:1px solid #000;background:#CCC;padding: 2px;font-family: Courier New, Arial;font-size: 11px;}
    .tclass td, .tclass2 td {border:1px solid #000;background:#FFFCCC;padding: 2px;font-family: Courier New, Arial;font-size: 11px;}
    .tclass2 th {background:#D5EAEA;}
    .tclass2 td {background:#FFFFFF;}
    .firsttr td {border-top:0;}
    .firsttd {border-left:none !important;}
    .bold {font-weight:bold;}
    </style>
    <div id="silver-debug" style="width:1032px;margin:20px auto;">
EOF;
    $class = 'tclass2';
    if($GLOBALS['__queries']){
        foreach($GLOBALS['__queries'] as $dkey=>$debug){
            $class = $class == 'tclass' ? 'tclass2' : 'tclass';
            $content .= '<table cellspacing="0" class="' . $class . '"><tr><th rowspan="2" width="20">' . ($dkey + 1) . '</th><td width="60">' . $debug['time'] . ' ms</td><td class="bold">' . htmlspecialchars($debug['sql']) . '</td></tr>';
            if(!empty($debug['explain'])){
                $content .= '<tr><td>' . DB_DRIVER . '</td><td><table cellspacing="0"><tr class="firsttr"><td width="5%" class="firsttd">id</td><td width="10%">select_type</td><td width="12%">table</td><td width="5%">type</td><td width="20%">possible_keys</td><td width="10%">key</td><td width="8%">key_len</td><td width="5%">ref</td><td width="5%">rows</td><td width="20%">Extra</td></tr><tr>';
                foreach($debug['explain'] as $ekey=>$explain){
                    ($ekey == 'id') ? $tdclass = ' class="firsttd"' : $tdclass = '';
                    if(empty($explain)) $explain = '-';
                    $content .= '<td' . $tdclass . '>' . $explain . '</td>';
                }
                $content .= '</tr></table></td></tr>';
            }
            $content .= '</table>';
        }
    }
    if($firefox){
        FB::group('Trace Info', array('Collapsed'=>0));
    }
    if($files = get_included_files()){
        $class = $class == 'tclass' ? 'tclass2' : 'tclass';
        $content .= '<table class="' . $class . '">';
        $table = array();
        $table[] = array('Line', 'File');
        foreach($files as $fkey=>$file){
            $content .= '<tr><th width="20">' . ($fkey + 1) . '</th><td>' . $file . '</td></tr>';
            $table[] = array($fkey + 1, $file);
        }
        $content .= '</table>';
        if($firefox){
            FB::table('Call Trace', $table);
        }
    }
    $content .= '</div>';
    if($firefox){
        //FB::trace('Trace Info');
        FB::groupEnd();
    }
    if(!$firefox){
        echo $content;
    }
    /*
    if ($firefox) {
        // PHP Predefined Variables
        FB::group('Predefined Variables', array('Collapsed' => true));
        //FB::info($GLOBALS, 'GLOBALS');
        FB::info($_COOKIE, '_COOKIE');
        FB::info($_ENV, '_ENV');
        FB::info($_FILES, '_FILES');
        FB::info($_GET, '_GET');
        FB::info($_POST, '_POST');
        FB::info($_REQUEST, '_REQUEST');
        FB::info($_SERVER, '_SERVER');
        if (isset($_SESSION)) {
            FB::info($_SESSION, '_SESSION');
        }
        FB::groupEnd();
    }
    */
}