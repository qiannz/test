<?php

/**
 * Description of xhprofEnd
 *
 */
if(TRUE == $XHPROF_DEBUG){
    //stop profiler
    $xhprof_data = xhprof_disable();
    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, 'xhprof_foo');
    echo "—————\n" . "<a href='http://10.66.201.28/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof_foo'>xhprof</a>\n" . "—————\n";
}
echo '<!– xhprof –>';