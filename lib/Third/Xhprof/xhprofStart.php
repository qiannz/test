<?php

/**
 * Description of xhprofStart
 *
 */
$XHPROF_DEBUG = FALSE;

if(function_exists('xhprof_enable')){
    $XHPROF_DEBUG = TRUE;
}

if($XHPROF_DEBUG){
    include_once "/home/wwwroot/xhprof/xhprof_lib/utils/xhprof_lib.php";
    include_once "/home/wwwroot/xhprof/xhprof_lib/utils/xhprof_runs.php";
    xhprof_enable(); //start profiling 
}

    